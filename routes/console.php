<?php

use App\Models\Tenant;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

Artisan::command('tenants:rename-domains {--dry-run}', function () {
    $baseDomain = env('TENANT_BASE_DOMAIN', env('CENTRAL_DOMAIN', parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost'));
    $dryRun = (bool) $this->option('dry-run');

    $this->info('Backfilling tenant slugs/domains using Business/App Name...');
    $this->line('Base domain: ' . $baseDomain);
    $this->line($dryRun ? 'Mode: DRY RUN (no changes will be saved)' : 'Mode: APPLY');

    $updated = 0;
    $skipped = 0;

    Tenant::query()->orderBy('id')->chunkById(100, function ($tenants) use ($baseDomain, $dryRun, &$updated, &$skipped) {
        foreach ($tenants as $tenant) {
            $sourceName = trim((string) ($tenant->app_title ?: $tenant->name ?: ('tenant-' . $tenant->id)));
            $baseSlug = Str::slug($sourceName);

            if ($baseSlug === '') {
                $baseSlug = 'tenant-' . $tenant->id;
            }

            $baseSlug = substr($baseSlug, 0, 48);

            $newSlug = $baseSlug;
            $newDomain = $newSlug . '.' . $baseDomain;

            if (Tenant::query()->where('id', '!=', $tenant->id)->where('slug', $newSlug)->exists()) {
                $suffix = '-' . $tenant->id;
                $newSlug = substr($baseSlug, 0, max(1, 63 - strlen($suffix))) . $suffix;
                $newDomain = $newSlug . '.' . $baseDomain;
            }

            $counter = 2;
            while (Tenant::query()->where('id', '!=', $tenant->id)
                ->where(function ($query) use ($newSlug, $newDomain) {
                    $query->where('slug', $newSlug)->orWhere('domain', $newDomain);
                })->exists()) {
                $suffix = '-' . $tenant->id . '-' . $counter;
                $newSlug = substr($baseSlug, 0, max(1, 63 - strlen($suffix))) . $suffix;
                $newDomain = $newSlug . '.' . $baseDomain;
                $counter++;
            }

            if ($tenant->slug === $newSlug && $tenant->domain === $newDomain) {
                $skipped++;
                $this->line("= Tenant {$tenant->id} unchanged ({$tenant->domain})");
                continue;
            }

            $this->line("~ Tenant {$tenant->id}: {$tenant->slug} -> {$newSlug} | {$tenant->domain} -> {$newDomain}");

            if (! $dryRun) {
                $tenant->update([
                    'slug' => $newSlug,
                    'domain' => $newDomain,
                ]);
            }

            $updated++;
        }
    });

    $this->newLine();
    $this->info("Done. Updated: {$updated}, Skipped: {$skipped}");

    return 0;
})->purpose('Rename existing tenant slugs/domains from Business/App Name');
