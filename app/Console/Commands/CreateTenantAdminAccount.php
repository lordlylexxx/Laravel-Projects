<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Multitenancy\Facades\Tenancy;

class CreateTenantAdminAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:create-admin {tenantId : The ID of the tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually create a tenant admin account';

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

        $this->info("Creating admin account for tenant: {$tenant->name}");

        try {
            // Make tenant current
            Tenancy::initialize($tenant);

            // Check if admin already exists
            $existingAdmin = User::where('role', User::ROLE_ADMIN)->first();
            if ($existingAdmin) {
                $this->warn("Admin account already exists!");
                $this->line("Email: {$existingAdmin->email}");
                Tenancy::end();
                return self::FAILURE;
            }

            // Generate unique admin email
            $adminEmail = $this->buildUniqueTenantAdminEmail($tenant);
            $plainPassword = Str::random(12);

            // Create admin user in tenant database
            $tenantAdmin = User::create([
                'name' => $tenant->name . ' Admin',
                'email' => $adminEmail,
                'password' => Hash::make($plainPassword),
                'role' => User::ROLE_ADMIN,
                'tenant_id' => $tenant->id,
                'phone' => null,
            ]);

            Tenancy::end();

            $this->info('✓ Admin account created successfully!');
            $this->line("Admin Email: {$tenantAdmin->email}");
            $this->line("Admin Password: {$plainPassword}");
            $this->line("Admin User ID: {$tenantAdmin->id}");

            Log::info('Tenant admin account created manually.', [
                'tenant_id' => $tenant->id,
                'admin_user_id' => $tenantAdmin->id,
                'admin_email' => $adminEmail,
            ]);

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            Tenancy::end();
            $this->error("Failed: {$exception->getMessage()}");
            Log::error('Failed to create tenant admin account.', [
                'tenant_id' => $tenant->id,
                'error' => $exception->getMessage(),
            ]);
            return self::FAILURE;
        }
    }

    private function buildUniqueTenantAdminEmail(Tenant $tenant): string
    {
        $base = 'admin@' . ($tenant->domain ?: ($tenant->slug . '.localhost'));

        if (!User::query()->where('email', $base)->exists()) {
            return $base;
        }

        $prefix = 'admin+' . ($tenant->slug ?: 'tenant');
        $domain = $tenant->domain ? explode('.', $tenant->domain)[0] . '.local' : 'impastay.local';
        $counter = 1;

        do {
            $candidate = $prefix . $counter . '@' . $domain;
            $counter++;
        } while (User::query()->where('email', $candidate)->exists());

        return $candidate;
    }
}
