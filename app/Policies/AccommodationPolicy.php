<?php

namespace App\Policies;

use App\Models\Accommodation;
use App\Models\Tenant;
use App\Models\User;

class AccommodationPolicy
{
    /**
     * Platform super-admins (no tenant scope) bypass checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin() && $user->tenant_id === null) {
            return true;
        }

        return null;
    }

    /**
     * Owners and tenant admins may create listings up to the tenant plan limit (Basic 3, Standard 10, Premium unlimited).
     */
    public function create(User $user): bool
    {
        $currentTenant = Tenant::current();

        if ($user->isAdmin() && $user->tenant_id !== null && $currentTenant && (int) $user->tenant_id === (int) $currentTenant->id) {
            if (! $this->hasPermissionOrLegacy($user, User::PERM_ACCOMMODATIONS_CREATE)) {
                return false;
            }

            $currentCount = $currentTenant->accommodations()->count();

            return $currentTenant->hasActiveSubscription()
                && $currentTenant->hasFeature('bookings')
                && $currentTenant->canCreateAccommodation($currentCount);
        }

        if (! $user->isOwner()) {
            return false;
        }

        $tenant = $user->tenant ?? $user->ownedTenant;

        if (! $tenant) {
            $tenant = $user->ensureTenant();
        }

        if (! $tenant) {
            return false;
        }

        if (! $tenant->hasFeature('bookings')) {
            return false;
        }

        $currentCount = $tenant->accommodations()->count();

        return $tenant->canCreateAccommodation($currentCount)
            && $this->hasPermissionOrLegacy($user, User::PERM_ACCOMMODATIONS_CREATE);
    }

    /**
     * Owners may update their listings; tenant admins may update any listing on their tenant.
     */
    public function update(User $user, Accommodation $accommodation): bool
    {
        if ($user->isOwner() && (int) $accommodation->owner_id === (int) $user->id) {
            return $this->hasPermissionOrLegacy($user, User::PERM_ACCOMMODATIONS_UPDATE);
        }

        return $this->tenantAdminForAccommodation($user, $accommodation)
            && $this->hasPermissionOrLegacy($user, User::PERM_ACCOMMODATIONS_UPDATE);
    }

    /**
     * Owners may delete their listings; tenant admins may delete any listing on their tenant.
     */
    public function delete(User $user, Accommodation $accommodation): bool
    {
        if ($user->isOwner() && (int) $accommodation->owner_id === (int) $user->id) {
            return $this->hasPermissionOrLegacy($user, User::PERM_ACCOMMODATIONS_DELETE);
        }

        return $this->tenantAdminForAccommodation($user, $accommodation)
            && $this->hasPermissionOrLegacy($user, User::PERM_ACCOMMODATIONS_DELETE);
    }

    private function tenantAdminForAccommodation(User $user, Accommodation $accommodation): bool
    {
        if (! $user->isAdmin() || $user->tenant_id === null) {
            return false;
        }

        return (int) $user->tenant_id === (int) $accommodation->tenant_id;
    }

    private function hasPermissionOrLegacy(User $user, string $permission): bool
    {
        if ($user->hasPermission($permission)) {
            return true;
        }

        return $user->isOwner();
    }
}
