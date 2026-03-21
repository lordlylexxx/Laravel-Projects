<?php

use App\Models\Tenant;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('tenants:provision-db {tenantId}', function (int $tenantId) {
    /** @var Tenant|null $tenant */
    $tenant = Tenant::find($tenantId);

    if (! $tenant) {
        $this->error('Tenant not found.');

        return 1;
    }

    if (! $tenant->database) {
        $this->error('Tenant database name is missing.');

        return 1;
    }

    $database = preg_replace('/[^A-Za-z0-9_]/', '', $tenant->database);

    if (! $database) {
        $this->error('Invalid tenant database name.');

        return 1;
    }

    DB::connection('landlord')->statement("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $this->info("Database created or already exists: {$database}");

    if ($tenant->db_username) {
        $username = preg_replace('/[^A-Za-z0-9_]/', '', $tenant->db_username);
        $password = str_replace("'", "''", (string) $tenant->db_password);

        DB::connection('landlord')->statement("CREATE USER IF NOT EXISTS '{$username}'@'%' IDENTIFIED BY '{$password}'");
        DB::connection('landlord')->statement("GRANT ALL PRIVILEGES ON `{$database}`.* TO '{$username}'@'%'");
        DB::connection('landlord')->statement('FLUSH PRIVILEGES');

        $this->info("Database user provisioned: {$username}");
    }

    $tenant->makeCurrent();

    try {
        Artisan::call('migrate', [
            '--database' => config('multitenancy.tenant_database_connection_name', 'tenant'),
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);

        $this->line(Artisan::output());
        $this->info('Tenant schema migrated successfully.');
    } finally {
        Tenant::forgetCurrent();
    }

    $this->info('Tenant database provisioning completed.');

    return 0;
})->purpose('Create and grant a dedicated database for a tenant');
