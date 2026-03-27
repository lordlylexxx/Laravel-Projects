<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTenantProvisioning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:check-provisioning {tenantId? : The ID of a specific tenant to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the provisioning status of tenants and their databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenantId');

        if ($tenantId) {
            $tenants = Tenant::where('id', $tenantId)->get();
            
            if ($tenants->isEmpty()) {
                $this->error("Tenant not found with ID: {$tenantId}");
                return self::FAILURE;
            }
        } else {
            $tenants = Tenant::all();
        }

        if ($tenants->isEmpty()) {
            $this->info('No tenants found.');
            return self::SUCCESS;
        }

        $this->info('');
        $this->line('╔════════════════════════════════════════════════════════════════════════════════╗');
        $this->line('║                         TENANT PROVISIONING STATUS                              ║');
        $this->line('╚════════════════════════════════════════════════════════════════════════════════╝');
        $this->info('');

        foreach ($tenants as $tenant) {
            $this->displayTenantStatus($tenant);
            $this->info('');
        }

        return self::SUCCESS;
    }

    private function displayTenantStatus(Tenant $tenant): void
    {
        $status = $tenant->database_provisioned ? '✓ PROVISIONED' : '✗ NOT PROVISIONED';
        $statusColor = $tenant->database_provisioned ? 'info' : 'error';

        $this->line("ID: {$tenant->id} | Name: {$tenant->name}");
        $this->line("Slug: {$tenant->slug}");
        $this->line("Domain: {$tenant->domain ?? 'Not set'}");
        $this->line("Database: {$tenant->database}");
        $this->line("Host: {$tenant->db_host}:{$tenant->db_port}");
        
        $this->line("<{$statusColor}>{$status}</>");

        if ($tenant->database_provisioned_at) {
            $this->line("Provisioned at: {$tenant->database_provisioned_at->format('Y-m-d H:i:s')}");
        }

        if ($tenant->provisioning_error) {
            $this->error("Error: {$tenant->provisioning_error}");
        }

        // Check if database actually exists
        $databaseExists = $this->databaseExists($tenant);
        if ($databaseExists) {
            $this->line('<info>✓ Database exists in MySQL</info>');
        } else {
            $this->error('✗ Database does not exist in MySQL!');
        }

        $this->line('─────────────────────────────────────────────────────────────────────────────────');
    }

    private function databaseExists(Tenant $tenant): bool
    {
        try {
            $database = $tenant->database;
            $result = DB::connection('landlord')->select(
                "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?",
                [$database]
            );

            return !empty($result);
        } catch (\Throwable $exception) {
            $this->error("Error checking database: {$exception->getMessage()}");
            return false;
        }
    }
}
