<?php

namespace App\Helpers;

use App\Models\Tenant;

class FeatureHelper
{
    /**
     * Check if the current tenant has a specific feature enabled
     */
    public static function can(string $feature): bool
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            return false;
        }

        return $tenant->hasFeature($feature);
    }

    /**
     * Get all available features for the current tenant
     */
    public static function getAvailable(): array
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            return [];
        }

        return $tenant->getAvailableFeatures();
    }

    /**
     * Get the current plan details
     */
    public static function currentPlan(): ?array
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            return null;
        }

        $plans = Tenant::getPlanDetails();

        return $plans[$tenant->plan] ?? null;
    }

    /**
     * Check if current tenant has reached listing limit
     */
    public static function hasReachedListingLimit(): bool
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            return true;
        }

        $maxListings = $tenant->maxListings();
        $currentCount = $tenant->accommodations()->count();

        return ! is_null($maxListings) && $currentCount >= $maxListings;
    }

    /**
     * Get remaining listings available
     */
    public static function remainingListings(): ?int
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            return null;
        }

        $maxListings = $tenant->maxListings();

        if (is_null($maxListings)) {
            return null; // Unlimited
        }

        $currentCount = $tenant->accommodations()->count();

        return max(0, $maxListings - $currentCount);
    }

    /**
     * Get plan comparison data for all plans
     */
    public static function getAllPlans(): array
    {
        return Tenant::getPlanDetails();
    }
}
