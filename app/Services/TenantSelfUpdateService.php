<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class TenantSelfUpdateService
{
    public function __construct(
        private readonly TenantUpdateService $tenantUpdateService
    ) {}

    public function applyUpdate(int $tenantId, int $releaseId): array
    {
        $tenant = Tenant::query()->find($tenantId);
        if (! $tenant) {
            return ['ok' => false, 'message' => 'Tenant not found.'];
        }

        try {
            $exit = Artisan::call('tenants:migrate', ['tenantId' => (string) $tenantId]);
            if ($exit !== 0) {
                $message = 'Tenant migration failed while applying update.';
                $this->tenantUpdateService->markAsFailed($tenantId, $releaseId, $message);
                return ['ok' => false, 'message' => $message];
            }

            $this->tenantUpdateService->markAsUpdated($tenantId, $releaseId);
            return ['ok' => true, 'message' => 'Update applied successfully.'];
        } catch (\Throwable $exception) {
            Log::error('Tenant self-update failed.', [
                'tenant_id' => $tenantId,
                'release_id' => $releaseId,
                'error' => $exception->getMessage(),
            ]);

            $this->tenantUpdateService->markAsFailed($tenantId, $releaseId, $exception->getMessage());

            return ['ok' => false, 'message' => 'Update failed: '.$exception->getMessage()];
        }
    }
}
