<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Platform super-admins (no tenant scope) may access all bookings.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin() && $user->tenant_id === null) {
            return true;
        }

        return null;
    }

    /**
     * List bookings (owner / tenant admin / clients with guest booking capability).
     */
    public function viewAny(User $user): bool
    {
        if ($user->isOwner()) {
            return true;
        }

        if ($user->isAdmin() && $user->tenant_id !== null) {
            return $this->hasPermissionOrLegacy($user, User::PERM_BOOKINGS_MANAGE);
        }

        return $user->tenantClientMayManageOwnStays();
    }

    /**
     * Create a booking as a client (guest capability).
     */
    public function create(User $user): bool
    {
        return $user->tenantClientMayManageOwnStays();
    }

    /**
     * A booking can be viewed by its client, the accommodation owner, or the tenant admin for that tenant.
     */
    public function view(User $user, Booking $booking): bool
    {
        if ((int) $booking->client_id === (int) $user->id) {
            return $user->tenantClientMayManageOwnStays();
        }

        if ($user->isOwner() && (int) $booking->accommodation->owner_id === (int) $user->id) {
            return true;
        }

        return $this->isTenantAdminForBooking($user, $booking)
            && $this->hasPermissionOrLegacy($user, User::PERM_BOOKINGS_MANAGE);
    }

    /**
     * Owners and tenant admins for the booking's tenant may update (approve, decline, mark paid, etc.).
     */
    public function update(User $user, Booking $booking): bool
    {
        if ($user->isOwner() && (int) $booking->accommodation->owner_id === (int) $user->id) {
            return $this->hasPermissionOrLegacy($user, User::PERM_BOOKINGS_MANAGE);
        }

        return $this->isTenantAdminForBooking($user, $booking)
            && $this->hasPermissionOrLegacy($user, User::PERM_BOOKINGS_MANAGE);
    }

    private function isTenantAdminForBooking(User $user, Booking $booking): bool
    {
        if (! $user->isAdmin() || $user->tenant_id === null) {
            return false;
        }

        return (int) $user->tenant_id === (int) $booking->tenant_id;
    }

    /**
     * Clients can cancel only their own bookings.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        if (! $user->isClient() || (int) $booking->client_id !== (int) $user->id) {
            return false;
        }

        return $user->hasPermission(User::PERM_BOOKINGS_SELF);
    }

    private function hasPermissionOrLegacy(User $user, string $permission): bool
    {
        if ($user->hasPermission($permission)) {
            return true;
        }

        return $user->isOwner();
    }
}
