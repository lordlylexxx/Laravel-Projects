<?php

use App\Mail\TenantUserWelcomeMail;
use App\Models\Tenant;
use App\Models\TenantCustomRole;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;

it('allows owner to create users within their tenant', function () {
    try {
        Tenant::query()->count();
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is not available in this environment.');
    }

    $this->seed(RolesAndPermissionsSeeder::class);

    Mail::fake();

    $owner = User::factory()->create([
        'role' => User::ROLE_OWNER,
    ]);

    $tenant = $owner->ensureTenant();

    $response = $this
        ->actingAs($owner)
        ->post('/owner/users', [
            'name' => 'Tenant Staff',
            'email' => 'tenant.staff@example.test',
            (User::tenantCustomRbacSchemaReady() ? 'role_selection' : 'role') => User::tenantCustomRbacSchemaReady() ? 'core:client' : User::ROLE_CLIENT,
        ]);

    $response->assertRedirect('/owner/users');

    $created = User::query()->where('email', 'tenant.staff@example.test')->first();

    expect($created)->not->toBeNull();
    expect((int) $created->tenant_id)->toBe((int) $tenant->id);
    expect($created->role)->toBe(User::ROLE_CLIENT);
    expect($created->hasRole(User::ROLE_CLIENT))->toBeTrue();

    Mail::assertSent(TenantUserWelcomeMail::class, function (TenantUserWelcomeMail $mail): bool {
        return $mail->hasTo('tenant.staff@example.test');
    });
});

it('blocks owner from editing users from another tenant', function () {
    try {
        Tenant::query()->count();
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is not available in this environment.');
    }

    $this->seed(RolesAndPermissionsSeeder::class);

    $ownerA = User::factory()->create([
        'role' => User::ROLE_OWNER,
    ]);
    $tenantA = $ownerA->ensureTenant();

    $ownerB = User::factory()->create([
        'role' => User::ROLE_OWNER,
    ]);
    $tenantB = $ownerB->ensureTenant();

    $foreignUser = User::factory()->create([
        'role' => User::ROLE_CLIENT,
        'tenant_id' => $tenantB->id,
    ]);

    $response = $this
        ->actingAs($ownerA)
        ->put('/owner/users/'.$foreignUser->id, [
            'name' => 'Updated Name',
            'email' => $foreignUser->email,
            (User::tenantCustomRbacSchemaReady() ? 'role_selection' : 'role') => User::tenantCustomRbacSchemaReady() ? 'core:client' : User::ROLE_CLIENT,
        ]);

    $response->assertNotFound();
});

it('maps legacy role column into spatie roles via seeder', function () {
    try {
        Tenant::query()->count();
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is not available in this environment.');
    }

    $tenant = Tenant::create([
        'name' => 'RBAC Tenant',
        'slug' => 'rbac-tenant',
        'plan' => Tenant::PLAN_PLUS,
        'subscription_status' => 'active',
    ]);

    $owner = User::factory()->create([
        'role' => User::ROLE_OWNER,
    ]);

    $tenantAdmin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => $tenant->id,
    ]);

    $this->seed(RolesAndPermissionsSeeder::class);

    $owner->refresh();
    $tenantAdmin->refresh();

    expect($owner->hasRole(User::ROLE_OWNER))->toBeTrue();
    expect($owner->hasPermission(User::PERM_USERS_ASSIGN_PERMISSIONS))->toBeTrue();
    expect($tenantAdmin->hasRole(User::ROLE_ADMIN))->toBeTrue();
    expect($tenantAdmin->hasPermission(User::PERM_USERS_ASSIGN_ROLES))->toBeTrue();
    expect($tenantAdmin->hasPermission(User::PERM_USERS_ASSIGN_PERMISSIONS))->toBeTrue();
});

it('allows owner to create tenant custom role templates with permissions', function () {
    try {
        Tenant::query()->count();
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is not available in this environment.');
    }

    $this->seed(RolesAndPermissionsSeeder::class);

    $owner = User::factory()->create([
        'role' => User::ROLE_OWNER,
    ]);
    $tenant = $owner->ensureTenant();

    $response = $this
        ->actingAs($owner)
        ->post('/owner/users/custom-roles', [
            'name' => 'Front Desk',
            'description' => 'Handles bookings and guests',
            'permissions' => [
                User::PERM_BOOKINGS_MANAGE,
                User::PERM_MESSAGES_MANAGE,
            ],
        ]);

    $response->assertRedirect('/owner/users');

    $role = TenantCustomRole::query()
        ->where('tenant_id', $tenant->id)
        ->where('name', 'Front Desk')
        ->first();

    expect($role)->not->toBeNull();
    expect($role->permissionNames())->toBe([
        User::PERM_BOOKINGS_MANAGE,
        User::PERM_MESSAGES_MANAGE,
    ]);
});

it('uses role template permissions as single source of truth', function () {
    try {
        Tenant::query()->count();
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is not available in this environment.');
    }

    $this->seed(RolesAndPermissionsSeeder::class);

    $owner = User::factory()->create([
        'role' => User::ROLE_OWNER,
    ]);
    $tenant = $owner->ensureTenant();

    $role = TenantCustomRole::create([
        'tenant_id' => $tenant->id,
        'name' => 'Operations',
        'slug' => 'operations',
        'description' => null,
    ]);
    $role->permissions()->createMany([
        ['permission_name' => User::PERM_ACCOMMODATIONS_CREATE],
        ['permission_name' => User::PERM_ACCOMMODATIONS_UPDATE],
    ]);

    $user = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => $tenant->id,
        'tenant_custom_role_id' => $role->id,
    ]);

    $user->syncTenantPermissionOverrides(
        grants: [User::PERM_REPORTS_VIEW],
        revokes: [User::PERM_ACCOMMODATIONS_UPDATE],
        tenant: $tenant
    );

    $previousTeam = getPermissionsTeamId();
    setPermissionsTeamId($tenant->id);
    try {
        $user->refresh();
        expect($user->hasPermission(User::PERM_ACCOMMODATIONS_CREATE))->toBeTrue();
        expect($user->hasPermission(User::PERM_ACCOMMODATIONS_UPDATE))->toBeTrue();
        expect($user->hasPermission(User::PERM_REPORTS_VIEW))->toBeFalse();
        expect((array) $user->tenant_permission_grants)->toBe([]);
        expect((array) $user->tenant_permission_revokes)->toBe([]);
    } finally {
        setPermissionsTeamId($previousTeam);
    }
});

it('does not fall back to core defaults when a custom role template is empty', function () {
    try {
        Tenant::query()->count();
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is not available in this environment.');
    }

    $this->seed(RolesAndPermissionsSeeder::class);

    $owner = User::factory()->create([
        'role' => User::ROLE_OWNER,
    ]);
    $tenant = $owner->ensureTenant();

    $role = TenantCustomRole::create([
        'tenant_id' => $tenant->id,
        'name' => 'Empty Staff',
        'slug' => 'empty-staff',
        'description' => null,
    ]);

    $user = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => $tenant->id,
        'tenant_custom_role_id' => $role->id,
    ]);

    $user->syncEffectiveTenantPermissions($tenant);

    $previousTeam = getPermissionsTeamId();
    setPermissionsTeamId($tenant->id);
    try {
        $user->refresh();
        expect($user->hasPermission(User::PERM_USERS_VIEW))->toBeFalse();
        expect($user->hasPermission(User::PERM_REPORTS_VIEW))->toBeFalse();
        expect($user->getAllPermissions()->pluck('name')->all())->toBe([]);
    } finally {
        setPermissionsTeamId($previousTeam);
    }
});

it('prevents assigning custom role from another tenant', function () {
    try {
        Tenant::query()->count();
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is not available in this environment.');
    }

    $this->seed(RolesAndPermissionsSeeder::class);

    $ownerA = User::factory()->create(['role' => User::ROLE_OWNER]);
    $tenantA = $ownerA->ensureTenant();

    $ownerB = User::factory()->create(['role' => User::ROLE_OWNER]);
    $tenantB = $ownerB->ensureTenant();

    $foreignRole = TenantCustomRole::create([
        'tenant_id' => $tenantB->id,
        'name' => 'Foreign Role',
        'slug' => 'foreign-role',
    ]);

    $managedUser = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => $tenantA->id,
    ]);

    $this->actingAs($ownerA)->put('/owner/users/'.$managedUser->id, [
        'name' => $managedUser->name,
        'email' => $managedUser->email,
        (User::tenantCustomRbacSchemaReady() ? 'role_selection' : 'role') => User::tenantCustomRbacSchemaReady() ? 'custom:'.$foreignRole->id : User::ROLE_ADMIN,
    ])->assertRedirect('/owner/users');

    expect($managedUser->fresh()->tenant_custom_role_id)->toBeNull();
});
