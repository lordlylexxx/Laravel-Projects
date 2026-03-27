<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ReprovisionTenantDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:reprovision {tenantId : The ID of the tenant to reprovision}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually reprovision a tenant database (create database, migrate, seed)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenantId');

        /** @var Tenant|null $tenant */
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant not found with ID: {$tenantId}");
            return self::FAILURE;
        }

        if (!$tenant->database) {
            $this->error('Tenant has no database name assigned.');
            return self::FAILURE;
        }

        $this->info("Starting database provisioning for tenant: {$tenant->name} (ID: {$tenant->id})");
        $this->line("Database: {$tenant->database}");
        $this->line("Domain: {$tenant->domain}");

        try {
            // Call the existing provision-db command
            $exitCode = Artisan::call('tenants:provision-db', [
                'tenantId' => $tenant->id,
            ]);

            $output = Artisan::output();
            $this->line($output);

            if ($exitCode === 0) {
                $this->info('✓ Database provisioned successfully!');
                
                // Update tenant status
                $tenant->update([
                    'database_provisioned' => true,
                    'database_provisioned_at' => now(),
                    'provisioning_error' => null,
                ]);

                $this->info('✓ Provisioning status updated in database');
                return self::SUCCESS;
            } else {
                $this->error("✗ Provisioning failed with exit code: {$exitCode}");
                
                $tenant->update([
                    'database_provisioned' => false,
                    'provisioning_error' => "Command returned exit code: {$exitCode}",
                ]);

                return self::FAILURE;
            }
        } catch (\Throwable $exception) {
            $this->error("✗ Provisioning exception: {$exception->getMessage()}");
            $this->error($exception->getTraceAsString());

            $tenant->update([
                'database_provisioned' => false,
                'provisioning_error' => $exception->getMessage(),
            ]);

            Log::error('Manual tenant database provisioning failed.', [
                'tenant_id' => $tenant->id,
                'database' => $tenant->database,
                'error' => $exception->getMessage(),
            ]);

            return self::FAILURE;
        }
    }
}
