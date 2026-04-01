<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            User::PERM_USERS_VIEW,
            User::PERM_USERS_CREATE,
            User::PERM_USERS_UPDATE,
            User::PERM_USERS_ACTIVATE,
            User::PERM_USERS_ASSIGN_ROLES,
            User::PERM_USERS_ASSIGN_PERMISSIONS,
            User::PERM_ACCOMMODATIONS_CREATE,
            User::PERM_ACCOMMODATIONS_UPDATE,
            User::PERM_ACCOMMODATIONS_DELETE,
            User::PERM_BOOKINGS_MANAGE,
            User::PERM_MESSAGES_MANAGE,
            User::PERM_REPORTS_VIEW,
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $ownerRole = Role::firstOrCreate(['name' => User::ROLE_OWNER, 'guard_name' => 'web']);
        $tenantAdminRole = Role::firstOrCreate(['name' => User::ROLE_ADMIN, 'guard_name' => 'web']);
        $clientRole = Role::firstOrCreate(['name' => User::ROLE_CLIENT, 'guard_name' => 'web']);

        $ownerRole->syncPermissions([
            User::PERM_USERS_VIEW,
            User::PERM_USERS_CREATE,
            User::PERM_USERS_UPDATE,
            User::PERM_USERS_ACTIVATE,
            User::PERM_USERS_ASSIGN_ROLES,
            User::PERM_USERS_ASSIGN_PERMISSIONS,
            User::PERM_ACCOMMODATIONS_CREATE,
            User::PERM_ACCOMMODATIONS_UPDATE,
            User::PERM_ACCOMMODATIONS_DELETE,
            User::PERM_BOOKINGS_MANAGE,
            User::PERM_MESSAGES_MANAGE,
            User::PERM_REPORTS_VIEW,
        ]);

        $tenantAdminRole->syncPermissions([
            User::PERM_USERS_VIEW,
            User::PERM_USERS_CREATE,
            User::PERM_USERS_UPDATE,
            User::PERM_USERS_ACTIVATE,
            User::PERM_USERS_ASSIGN_ROLES,
            User::PERM_ACCOMMODATIONS_CREATE,
            User::PERM_ACCOMMODATIONS_UPDATE,
            User::PERM_ACCOMMODATIONS_DELETE,
            User::PERM_BOOKINGS_MANAGE,
            User::PERM_MESSAGES_MANAGE,
            User::PERM_REPORTS_VIEW,
        ]);

        $clientRole->syncPermissions([]);

        User::query()->select(['id', 'role'])->chunkById(200, function ($users): void {
            foreach ($users as $user) {
                $user->syncRbacFromLegacyRole();
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
