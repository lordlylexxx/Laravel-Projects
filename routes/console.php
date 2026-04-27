<?php

use App\Models\Tenant;
use App\Models\AppRelease;
use App\Services\ReleaseRegistryService;
use App\Services\TenantSelfUpdateService;
use App\Services\TenantUpdateService;
use Database\Seeders\TenantRbacSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('tenants:migrate {tenantId?}', function (?string $tenantId = null) {
    $id = ($tenantId !== null && $tenantId !== '') ? (int) $tenantId : null;

    $query = Tenant::query()->where('database_provisioned', true)->orderBy('id');

    if ($id !== null) {
        $query->whereKey($id);
    }

    $tenants = $query->get();

    if ($tenants->isEmpty()) {
        $this->error($id !== null ? 'No provisioned tenant found for that id.' : 'No provisioned tenants to migrate.');

        return 1;
    }

    $connection = config('multitenancy.tenant_database_connection_name', 'tenant');

    foreach ($tenants as $tenant) {
        if (! $tenant->database) {
            $this->warn("Skipping tenant {$tenant->id}: no database configured.");

            continue;
        }

        $this->line("Migrating tenant schema for {$tenant->id} ({$tenant->name})...");

        $tenant->makeCurrent();

        try {
            $migrateExit = Artisan::call('migrate', [
                '--database' => $connection,
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);
            $this->line(Artisan::output());

            if ($migrateExit !== 0) {
                $this->error("Tenant migrations failed for tenant {$tenant->id} (exit {$migrateExit}).");

                return 1;
            }
        } catch (\Throwable $e) {
            $this->error("Failed for tenant {$tenant->id}: ".$e->getMessage());

            return 1;
        } finally {
            Tenant::forgetCurrent();
        }
    }

    $this->info('Done.');

    return 0;
})->purpose('Run database/migrations/tenant against one or all provisioned tenant databases');

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

    $migrateExit = 1;
    $provisioningError = null;

    $tenant->makeCurrent();

    try {
        $migrateExit = Artisan::call('migrate', [
            '--database' => config('multitenancy.tenant_database_connection_name', 'tenant'),
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);

        $this->line(Artisan::output());
        if ($migrateExit === 0) {
            $this->info('Tenant schema migrated successfully.');

            try {
                $this->call(TenantRbacSeeder::class);
                $this->info('Tenant RBAC seeded.');
            } catch (\Throwable $e) {
                $this->error('Tenant RBAC seed failed: '.$e->getMessage());
                $migrateExit = 1;
                $provisioningError = 'Tenant RBAC seed failed: '.$e->getMessage();
            }
        } else {
            $this->error('Tenant migrations failed.');
            $provisioningError = 'Tenant migrate exit code: '.$migrateExit;
        }
    } finally {
        Tenant::forgetCurrent();
    }

    $tenant->refresh();

    if ($migrateExit !== 0) {
        $tenant->update([
            'database_provisioned' => false,
            'provisioning_error' => $provisioningError ?? 'Tenant migrate exit code: '.$migrateExit,
        ]);

        return 1;
    }

    $tenant->update([
        'database_provisioned' => true,
        'database_provisioned_at' => now(),
        'provisioning_error' => null,
    ]);

    $this->info('Tenant database provisioning completed.');

    return 0;
})->purpose('Create and grant a dedicated database for a tenant');

Artisan::command('tenants:sync-rbac {tenantId?}', function (?string $tenantId = null) {
    $id = ($tenantId !== null && $tenantId !== '') ? (int) $tenantId : null;

    $query = Tenant::query()->orderBy('id');

    if ($id !== null) {
        $query->whereKey($id);
    }

    $tenants = $query->get();

    if ($tenants->isEmpty()) {
        $this->error($id !== null ? 'Tenant not found.' : 'No tenants to process.');

        return 1;
    }

    foreach ($tenants as $tenant) {
        if (! $tenant->database) {
            $this->warn("Skipping tenant {$tenant->id}: no database configured.");

            continue;
        }

        $this->line("Syncing RBAC for tenant {$tenant->id} ({$tenant->name})...");

        $tenant->makeCurrent();

        try {
            $migrateExit = Artisan::call('migrate', [
                '--database' => config('multitenancy.tenant_database_connection_name', 'tenant'),
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);
            $this->line(Artisan::output());

            if ($migrateExit !== 0) {
                $this->error("Tenant migrations failed for tenant {$tenant->id} (exit {$migrateExit}).");

                return 1;
            }

            $this->call(TenantRbacSeeder::class);
        } catch (\Throwable $e) {
            $this->error("Failed for tenant {$tenant->id}: ".$e->getMessage());

            return 1;
        } finally {
            Tenant::forgetCurrent();
        }
    }

    $this->info('Done.');

    return 0;
})->purpose('Migrate tenant schema (including Spatie tables), seed roles/permissions, and sync legacy user roles');

Artisan::command('tenants:rename-domains {--dry-run}', function () {
    $baseDomain = env('TENANT_BASE_DOMAIN', env('CENTRAL_DOMAIN', parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost'));
    $dryRun = (bool) $this->option('dry-run');

    $this->info('Backfilling tenant slugs/domains using Business/App Name...');
    $this->line('Base domain: '.$baseDomain);
    $this->line($dryRun ? 'Mode: DRY RUN (no changes will be saved)' : 'Mode: APPLY');

    $updated = 0;
    $skipped = 0;

    Tenant::query()->orderBy('id')->chunkById(100, function ($tenants) use ($baseDomain, $dryRun, &$updated, &$skipped) {
        foreach ($tenants as $tenant) {
            $sourceName = trim((string) ($tenant->app_title ?: $tenant->name ?: ('tenant-'.$tenant->id)));
            $baseSlug = Str::slug($sourceName);

            if ($baseSlug === '') {
                $baseSlug = 'tenant-'.$tenant->id;
            }

            $baseSlug = substr($baseSlug, 0, 48);

            $newSlug = $baseSlug;
            $newDomain = $newSlug.'.'.$baseDomain;

            if (Tenant::query()->where('id', '!=', $tenant->id)->where('slug', $newSlug)->exists()) {
                $suffix = '-'.$tenant->id;
                $newSlug = substr($baseSlug, 0, max(1, 63 - strlen($suffix))).$suffix;
                $newDomain = $newSlug.'.'.$baseDomain;
            }

            $counter = 2;
            while (Tenant::query()->where('id', '!=', $tenant->id)
                ->where(function ($query) use ($newSlug, $newDomain) {
                    $query->where('slug', $newSlug)->orWhere('domain', $newDomain);
                })->exists()) {
                $suffix = '-'.$tenant->id.'-'.$counter;
                $newSlug = substr($baseSlug, 0, max(1, 63 - strlen($suffix))).$suffix;
                $newDomain = $newSlug.'.'.$baseDomain;
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

Artisan::command('releases:sync', function (ReleaseRegistryService $releaseRegistryService) {
    $result = $releaseRegistryService->syncFromGitHub();
    $this->info("Synced releases. New: {$result['synced']}, Updated: {$result['updated']}, Skipped: {$result['skipped']}");

    return 0;
})->purpose('Sync global release registry from GitHub releases');

Artisan::command('tenants:backfill-updates {--tenant=} {--dry-run}', function (TenantUpdateService $tenantUpdateService, ReleaseRegistryService $releaseRegistryService) {
    $tenantId = (int) ($this->option('tenant') ?? 0);
    $dryRun = (bool) $this->option('dry-run');
    $latest = $releaseRegistryService->getLatestStableRelease();

    if (! $latest) {
        $this->error('No stable release found in app_releases. Run releases:sync first.');
        return 1;
    }

    $query = Tenant::query()->orderBy('id');
    if ($tenantId > 0) {
        $query->whereKey($tenantId);
    }

    $tenants = $query->get();
    if ($tenants->isEmpty()) {
        $this->warn('No tenants found to backfill.');
        return 0;
    }

    $count = 0;
    foreach ($tenants as $tenant) {
        if ($dryRun) {
            $this->line("[DRY RUN] Would backfill tenant {$tenant->id} to {$latest->tag}");
            $count++;
            continue;
        }

        $tenantUpdateService->backfillCurrentReleaseForTenant($tenant, $latest);
        $this->line("Backfilled tenant {$tenant->id} to {$latest->tag}");
        $count++;
    }

    $this->info("Backfill complete. Processed {$count} tenant(s).");
    return 0;
})->purpose('Backfill current release adoption records for tenants');

Artisan::command('tenant:update {tenantId} {releaseId}', function (TenantSelfUpdateService $tenantSelfUpdateService) {
    $tenantId = (int) $this->argument('tenantId');
    $releaseId = (int) $this->argument('releaseId');

    if (! AppRelease::query()->whereKey($releaseId)->exists()) {
        $this->error('Release not found.');
        return 1;
    }

    $result = $tenantSelfUpdateService->applyUpdate($tenantId, $releaseId);
    if (! $result['ok']) {
        $this->error($result['message']);
        return 1;
    }

    $this->info($result['message']);
    return 0;
})->purpose('Apply a release to a tenant and record adoption state');

Schedule::command('releases:sync')->dailyAt('02:00');
