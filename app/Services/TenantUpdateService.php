<?php

namespace App\Services;

use App\Models\AppRelease;
use App\Models\Tenant;
use App\Models\TenantUpdate;
use App\Support\SemanticVersion;
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
        $currentTag = $current?->release?->tag;

        $candidates = AppRelease::query()
            ->when(
                ! config('releases.offer_prereleases_to_tenants', false),
                fn ($query) => $query->where('is_stable', true)
            )
            ->get()
            ->filter(function (AppRelease $release) use ($currentTag): bool {
                if ($currentTag === null || $currentTag === '') {
                    return true;
                }

                return version_compare(
                    SemanticVersion::normalize((string) $release->tag),
                    SemanticVersion::normalize((string) $currentTag),
                    '>'
                );
            });

        return $candidates
            ->sort(function (AppRelease $a, AppRelease $b): int {
                return -version_compare(
                    SemanticVersion::normalize((string) $a->tag),
                    SemanticVersion::normalize((string) $b->tag)
                );
            })
            ->values();
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
