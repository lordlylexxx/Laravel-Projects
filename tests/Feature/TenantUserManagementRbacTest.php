<?php

use App\Mail\TenantUserWelcomeMail;
use App\Models\Tenant;
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
            'role' => User::ROLE_CLIENT,
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
            'role' => User::ROLE_CLIENT,
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
