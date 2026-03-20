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
