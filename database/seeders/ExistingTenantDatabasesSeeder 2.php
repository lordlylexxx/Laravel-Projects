<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Registers landlord {@see Tenant} rows that point at MySQL schemas that already exist
 * (e.g. budget_stay_inn_tours, ranchers_ridge). Does not run tenants:provision-db.
 */
class ExistingTenantDatabasesSeeder extends Seeder
{
    /**
     * Map each existing database name to display metadata.
     * Keys must match the real MySQL database names on the server.
     *
     * @var array<string, array{name: string, slug: string}>
     */
    protected array $tenantDefinitions = [
        'budget_stay_inn_tours' => [
            'name' => 'Budget Stay Inn Tours',
            'slug' => 'budget_stay_inn_tours',
        ],
        'ranchers_ridge' => [
            'name' => 'Ranchers Ridge',
            'slug' => 'ranchers_ridge',
        ],
    ];

    public function run(): void
    {
        $baseDomain = env(
            'TENANT_BASE_DOMAIN',
            env('CENTRAL_DOMAIN', parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost')
        );

        $owners = User::query()
            ->where('role', User::ROLE_OWNER)
            ->orderBy('id')
            ->get();

        $index = 0;
        foreach ($this->tenantDefinitions as $databaseName => $meta) {
            $owner = $owners->get($index);
            $index++;

            // Avoid TenantObserver::created → tenants:provision-db (DB already exists).
            $tenant = Tenant::withoutEvents(function () use ($baseDomain, $databaseName, $meta, $owner) {
                return Tenant::updateOrCreate(
                    ['database' => $databaseName],
                    [
                        'name' => $meta['name'],
                        'slug' => $meta['slug'],
                        'owner_user_id' => $owner?->id,
                        'plan' => Tenant::PLAN_BASIC,
                        'subscription_status' => 'active',
                        'trial_ends_at' => now()->addYear(),
                        'current_period_starts_at' => now(),
                        'current_period_ends_at' => now()->addMonth(),
                        'domain' => $meta['slug'].'.'.$baseDomain,
                        'domain_enabled' => true,
                        'domain_disabled_at' => null,
                        'app_port' => null,
                        'db_host' => env('TENANT_DB_HOST', env('DB_HOST', '127.0.0.1')),
                        'db_port' => (int) env('TENANT_DB_PORT', env('DB_PORT', 3306)),
                        'db_username' => env('TENANT_DB_USERNAME', env('DB_USERNAME', 'root')),
                        'db_password' => env('TENANT_DB_PASSWORD', env('DB_PASSWORD', '')),
                        'onboarding_status' => Tenant::ONBOARDING_APPROVED,
                        'onboarding_approved_at' => now(),
                        'database_provisioned' => true,
                        'database_provisioned_at' => now(),
                        'provisioning_error' => null,
                    ]
                );
            });

            if ($owner) {
                $owner->update(['tenant_id' => $tenant->id]);
            }
        }
    }
}
