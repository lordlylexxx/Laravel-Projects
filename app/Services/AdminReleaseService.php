<?php

namespace App\Services;

use App\Models\AppRelease;
use App\Models\Tenant;
use App\Models\TenantUpdate;
use App\Support\SemanticVersion;
use Illuminate\Support\Carbon;

class AdminReleaseService
{
    public function __construct(
        private readonly TenantUpdateService $tenantUpdateService
    ) {}

    public function markAsRequired(int $releaseId, int $graceDays = 7): void
    {
        $release = AppRelease::query()->findOrFail($releaseId);
        $release->update(['is_required' => true]);

        $requiredAt = Carbon::now();
        $graceUntil = $requiredAt->copy()->addDays(max(0, $graceDays));

        Tenant::query()->orderBy('id')->chunkById(100, function ($tenants) use ($releaseId, $requiredAt, $graceUntil): void {
            foreach ($tenants as $tenant) {
                $current = $this->tenantUpdateService->getCurrentRelease((int) $tenant->id);
                if ($current && (int) $current->app_release_id === $releaseId) {
                    continue;
                }

                TenantUpdate::query()->updateOrCreate(
                    [
                        'tenant_id' => (int) $tenant->id,
                        'app_release_id' => $releaseId,
                    ],
                    [
                        'status' => TenantUpdate::STATUS_UPDATE_AVAILABLE,
                        'required_at' => $requiredAt,
                        'grace_until' => $graceUntil,
                    ]
                );
            }
        });
    }

    public function notifyAllTenantsOfUpdate(int $releaseId): int
    {
        $count = 0;

        Tenant::query()->orderBy('id')->chunkById(100, function ($tenants) use ($releaseId, &$count): void {
            foreach ($tenants as $tenant) {
                $existing = TenantUpdate::query()->where([
                    'tenant_id' => (int) $tenant->id,
                    'app_release_id' => $releaseId,
                ])->first();

                if ($existing) {
                    continue;
                }

                TenantUpdate::query()->create([
                    'tenant_id' => (int) $tenant->id,
                    'app_release_id' => $releaseId,
                    'status' => TenantUpdate::STATUS_UPDATE_AVAILABLE,
                    'is_current' => false,
                ]);

                $count++;
            }
        });

        return $count;
    }

    public function forceMarkAllAsUpdated(int $releaseId): int
    {
        $count = 0;

        Tenant::query()->orderBy('id')->chunkById(100, function ($tenants) use ($releaseId, &$count): void {
            foreach ($tenants as $tenant) {
                $this->tenantUpdateService->markAsUpdated((int) $tenant->id, $releaseId);
                $count++;
            }
        });

        return $count;
    }

    public function getUpdateStatistics(): array
    {
        $totalTenants = (int) Tenant::query()->count();

        $tenantCurrent = TenantUpdate::query()
            ->select('tenant_id', 'app_release_id')
            ->where('is_current', true)
            ->get();

        $latestStable = SemanticVersion::newestRelease(
            AppRelease::query()->where('is_stable', true)->get()
        );

        $newestInRegistry = SemanticVersion::newestRelease(
            AppRelease::query()->get()
        );

        $onLatest = 0;
        if ($latestStable) {
            $onLatest = (int) $tenantCurrent->where('app_release_id', (int) $latestStable->id)->count();
        }

        $requiredOverdue = (int) TenantUpdate::query()
            ->whereNotNull('required_at')
            ->where(function ($query): void {
                $query->whereNull('grace_until')->orWhere('grace_until', '<', now());
            })
            ->where('status', '!=', TenantUpdate::STATUS_UPDATED)
            ->distinct('tenant_id')
            ->count('tenant_id');

        $failed = (int) TenantUpdate::query()
            ->where('status', TenantUpdate::STATUS_FAILED)
            ->distinct('tenant_id')
            ->count('tenant_id');

        return [
            'total_tenants' => $totalTenants,
            'latest_release_id' => $latestStable?->id,
            'latest_release_tag' => $latestStable?->tag,
            'newest_registry_id' => $newestInRegistry?->id,
            'newest_registry_tag' => $newestInRegistry?->tag,
            'tenants_on_latest' => $onLatest,
            'tenants_pending_latest' => max(0, $totalTenants - $onLatest),
            'tenants_required_overdue' => $requiredOverdue,
            'tenants_with_failed_updates' => $failed,
        ];
    }
}
