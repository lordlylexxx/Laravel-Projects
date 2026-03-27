<?php

namespace App\Policies;

use App\Models\Accommodation;
use App\Models\User;

class AccommodationPolicy
{
    /**
     * Allow admins to bypass other checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    /**
     * Owners can create accommodations if they have an active subscription
     * and haven't exceeded their plan's listing limit.
     */
    public function create(User $user): bool
    {
        if (! $user->isOwner()) {
            return false;
        }

        $tenant = $user->tenant;
        if (! $tenant) {
            return false;
        }

        // Check if they have booking feature enabled for their plan
        if (! $tenant->hasFeature('bookings')) {
            return false;
        }

        // Check if they've reached their listing limit
        $currentCount = $tenant->accommodations()->count();

        return $tenant->canCreateAccommodation($currentCount);
    }

    /**
     * Owners can update only their own accommodations.
     */
    public function update(User $user, Accommodation $accommodation): bool
    {
        return $user->isOwner() && (int) $accommodation->owner_id === (int) $user->id;
    }

    /**
     * Owners can delete only their own accommodations.
     */
    public function delete(User $user, Accommodation $accommodation): bool
    {
        return $user->isOwner() && (int) $accommodation->owner_id === (int) $user->id;
    }
}
