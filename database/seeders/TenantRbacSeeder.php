<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class TenantRbacSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            throw new \RuntimeException('TenantRbacSeeder requires Tenant::makeCurrent() before running.');
        }

        $previousTeam = getPermissionsTeamId();
        setPermissionsTeamId($tenant->id);

        try {
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            RbacCatalog::ensurePermissionsExist();
            RbacCatalog::ensureRolesAndGrantPermissions();

            User::query()->select(['id', 'role'])->chunkById(200, function ($users): void {
                foreach ($users as $user) {
                    $user->syncRbacFromLegacyRole();
                }
            });

            app(PermissionRegistrar::class)->forgetCachedPermissions();
        } finally {
            setPermissionsTeamId($previousTeam);
        }
    }
}
