<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $previousTeam = getPermissionsTeamId();
        setPermissionsTeamId(null);

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
