<?php

namespace App\Services;

use App\Models\AppRelease;
use App\Models\Tenant;
use App\Models\TenantUpdate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TenantUpdateService
{
    public function getCurrentRelease(int $tenantId): ?TenantUpdate
    {
        return TenantUpdate::query()
            ->with('release')
            ->where('tenant_id', $tenantId)
            ->where('is_current', true)
            ->first();
    }

    public function getAvailableUpdates(int $tenantId)
    {
        $current = $this->getCurrentRelease($tenantId);
        $currentPublishedAt = $current?->release?->published_at;

        // Include prereleases: GitHub marks typical `-dev` releases as prerelease, which we store as
        // is_stable=false. Tenants still need to see and apply those tags.
        return AppRelease::query()
            ->when($currentPublishedAt, fn ($query) => $query->where('published_at', '>', $currentPublishedAt))
            ->orderByDesc('is_stable')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();
    }

    public function markAsUpdated(int $tenantId, int $releaseId): TenantUpdate
    {
        return DB::connection('landlord')->transaction(function () use ($tenantId, $releaseId) {
            TenantUpdate::query()
                ->where('tenant_id', $tenantId)
                ->update(['is_current' => false]);

            $tenantUpdate = TenantUpdate::query()->firstOrNew([
                'tenant_id' => $tenantId,
                'app_release_id' => $releaseId,
            ]);

            $tenantUpdate->fill([
                'status' => TenantUpdate::STATUS_UPDATED,
                'is_current' => true,
                'applied_at' => now(),
                'failure_reason' => null,
            ]);
            $tenantUpdate->save();

            return $tenantUpdate;
        });
    }

    public function markAsFailed(int $tenantId, int $releaseId, string $reason): TenantUpdate
    {
        $tenantUpdate = TenantUpdate::query()->firstOrNew([
            'tenant_id' => $tenantId,
            'app_release_id' => $releaseId,
        ]);

        $tenantUpdate->fill([
            'status' => TenantUpdate::STATUS_FAILED,
            'is_current' => false,
            'failure_reason' => $reason,
        ]);
        $tenantUpdate->save();

        return $tenantUpdate;
    }

    public function isUpdateRequired(int $tenantId): bool
    {
        return TenantUpdate::query()
            ->where('tenant_id', $tenantId)
            ->whereNotNull('required_at')
            ->where(function ($query): void {
                $query->whereNull('grace_until')->orWhere('grace_until', '<', now());
            })
            ->where('status', '!=', TenantUpdate::STATUS_UPDATED)
            ->exists();
    }

    public function backfillCurrentReleaseForTenant(Tenant $tenant, AppRelease $release): TenantUpdate
    {
        return DB::connection('landlord')->transaction(function () use ($tenant, $release) {
            TenantUpdate::query()->where('tenant_id', (int) $tenant->id)->update(['is_current' => false]);

            return TenantUpdate::query()->updateOrCreate(
                [
                    'tenant_id' => (int) $tenant->id,
                    'app_release_id' => (int) $release->id,
                ],
                [
                    'status' => TenantUpdate::STATUS_UPDATED,
                    'is_current' => true,
                    'applied_at' => Carbon::now(),
                    'failure_reason' => null,
                ]
            );
        });
    }
}
