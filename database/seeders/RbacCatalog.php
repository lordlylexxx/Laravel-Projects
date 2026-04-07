<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

/**
 * Shared permission rows and role grants for landlord and per-tenant Spatie tables.
 * Callers must set Spatie team id (e.g. setPermissionsTeamId) when teams are enabled.
 */
final class RbacCatalog
{
    /**
     * @return list<string>
     */
    public static function permissionNames(): array
    {
        return [
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
            User::PERM_BOOKINGS_SELF,
            User::PERM_MESSAGES_USE,
            User::PERM_PROFILE_SELF,
        ];
    }

    public static function ensurePermissionsExist(): void
    {
        foreach (self::permissionNames() as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }
    }

    public static function ensureRolesAndGrantPermissions(): void
    {
        $ownerRole = Role::findOrCreate(User::ROLE_OWNER, 'web');
        $tenantAdminRole = Role::findOrCreate(User::ROLE_ADMIN, 'web');
        $clientRole = Role::findOrCreate(User::ROLE_CLIENT, 'web');

        $fullTenantManager = User::defaultOwnerSpatiePermissions();

        $ownerRole->syncPermissions($fullTenantManager);
        $tenantAdminRole->syncPermissions($fullTenantManager);

        // Clients: no staff permissions on the role; defaults are applied as direct perms when users are created / backfilled.
        $clientRole->syncPermissions([]);
    }
}
