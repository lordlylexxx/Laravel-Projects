<?php

namespace App\Observers;

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class TenantObserver
{
    /**
     * Handle the Tenant "created" event.
     */
    public function created(Tenant $tenant): void
    {
        if ((string) ($tenant->onboarding_status ?? Tenant::ONBOARDING_APPROVED) !== Tenant::ONBOARDING_APPROVED) {
            return;
        }

        $this->provisionTenantDatabase($tenant);
    }

    /**
     * Provision the database for a tenant.
     */
    private function provisionTenantDatabase(Tenant $tenant): void
    {
        if (! $tenant->database) {
            Log::warning('Tenant has no database name assigned.', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
            ]);

            return;
        }

        try {
            Log::info('Starting automatic tenant database provisioning.', [
                'tenant_id' => $tenant->id,
                'database' => $tenant->database,
                'tenant_name' => $tenant->name,
            ]);

            $exitCode = Artisan::call('tenants:provision-db', [
                'tenantId' => $tenant->id,
            ]);

            if ($exitCode === 0) {
                Log::info('Tenant database provisioned successfully.', [
                    'tenant_id' => $tenant->id,
                    'database' => $tenant->database,
                    'output' => Artisan::output(),
                ]);

                // Update tenant with provisioning status
                $tenant->update([
                    'database_provisioned' => true,
                    'database_provisioned_at' => now(),
                ]);
            } else {
                Log::warning('Tenant database provisioning returned non-zero exit code.', [
                    'tenant_id' => $tenant->id,
                    'database' => $tenant->database,
                    'exit_code' => $exitCode,
                    'output' => Artisan::output(),
                ]);

                $tenant->update([
                    'database_provisioned' => false,
                    'provisioning_error' => 'Command returned exit code: '.$exitCode,
                ]);
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to provision tenant database during observer.', [
                'tenant_id' => $tenant->id,
                'database' => $tenant->database,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            $tenant->update([
                'database_provisioned' => false,
                'provisioning_error' => $exception->getMessage(),
            ]);
        }
    }
}
