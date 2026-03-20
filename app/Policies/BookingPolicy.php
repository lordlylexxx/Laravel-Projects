<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Allow admins to bypass other checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    /**
     * A booking can be viewed by its client or the accommodation owner.
     */
    public function view(User $user, Booking $booking): bool
    {
        return (int) $booking->client_id === (int) $user->id
            || (int) $booking->accommodation->owner_id === (int) $user->id;
    }

    /**
     * Owners can update bookings tied to their accommodations.
     */
    public function update(User $user, Booking $booking): bool
    {
        return $user->isOwner() && (int) $booking->accommodation->owner_id === (int) $user->id;
    }

    /**
     * Clients can cancel only their own bookings.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        return $user->isClient() && (int) $booking->client_id === (int) $user->id;
    }
}
