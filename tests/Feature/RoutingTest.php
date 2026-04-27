<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

function skipIfLandlordMemoryDb(): void
{
    $landlordDb = (string) config('database.connections.landlord.database', '');

    if ($landlordDb === ':memory:' || $landlordDb === '') {
        test()->markTestSkipped('Landlord test database is not configured for tenant route checks.');
    }

    try {
        if (! Schema::connection('landlord')->hasTable('tenants')) {
            test()->markTestSkipped('Landlord tenants table is unavailable for route checks.');
        }
    } catch (\Throwable) {
        test()->markTestSkipped('Landlord connection is unavailable for route checks.');
    }
}

function ensureRoutingTenantFixture(): Tenant
{
    $tenant = Tenant::query()->whereNotNull('domain')->where('domain', '!=', '')->first();

    if ($tenant) {
        return $tenant;
    }

    $slug = 'routing-tenant-'.Str::lower(Str::random(8));

    return Tenant::query()->create([
        'name' => 'Routing Tenant',
        'slug' => $slug,
        'domain' => $slug.'.localhost',
        'owner_user_id' => null,
        'plan' => Tenant::PLAN_BASIC,
        'subscription_status' => 'trialing',
        'trial_ends_at' => now()->addDays(14),
        'current_period_starts_at' => now(),
        'current_period_ends_at' => now()->addMonth(),
        'onboarding_status' => Tenant::ONBOARDING_APPROVED,
        'database' => 'tenant_'.Str::lower(Str::random(8)),
        'db_host' => '127.0.0.1',
        'db_port' => 3306,
        'db_username' => 'root',
        'db_password' => '',
    ]);
}

function tenantUrl(Tenant $tenant, string $path): string
{
    $tenantPort = (int) env('TENANT_PORT', env('CENTRAL_PORT', 8000));

    return "http://{$tenant->domain}:{$tenantPort}{$path}";
}

// ============ CENTRAL APP ROUTES ============

test('central landing page accessible', function () {
    skipIfLandlordMemoryDb();

    $response = $this->get('http://localhost:8000/');
    expect($response->status())->toBe(200);
});

test('central 127.0.0.1 landing page accessible', function () {
    skipIfLandlordMemoryDb();

    $response = $this->get('http://127.0.0.1:8000/');
    expect($response->status())->toBe(200);
});

test('central login page accessible', function () {
    $response = $this->get('http://localhost:8000/login');
    expect($response->status())->toBe(200);
});

test('central register page accessible', function () {
    $response = $this->get('http://localhost:8000/register');
    expect($response->status())->toBe(200);
});

// ============ TENANT APP ROUTES ============

test('tenant landing page accessible', function () {
    skipIfLandlordMemoryDb();

    $tenant = ensureRoutingTenantFixture();
    expect($tenant)->not->toBeNull('No tenant found in database');
    Tenant::forgetCurrent();
    $tenant->makeCurrent();
    $response = $this->get(tenantUrl($tenant, '/'));
    expect($response->status())->toBe(200);
    Tenant::forgetCurrent();
});

test('tenant login page accessible', function () {
    skipIfLandlordMemoryDb();

    $tenant = ensureRoutingTenantFixture();
    expect($tenant)->not->toBeNull('No tenant found in database');
    Tenant::forgetCurrent();
    $tenant->makeCurrent();
    $response = $this->get(tenantUrl($tenant, '/login'));
    expect($response->status())->toBe(200);
    Tenant::forgetCurrent();
});

test('tenant register page accessible', function () {
    skipIfLandlordMemoryDb();

    $tenant = ensureRoutingTenantFixture();
    expect($tenant)->not->toBeNull('No tenant found in database');
    Tenant::forgetCurrent();
    $tenant->makeCurrent();
    $response = $this->get(tenantUrl($tenant, '/register'));
    expect($response->status())->toBe(200);
    Tenant::forgetCurrent();
});

test('tenant accommodations page accessible', function () {
    skipIfLandlordMemoryDb();

    $tenant = ensureRoutingTenantFixture();
    expect($tenant)->not->toBeNull('No tenant found in database');
    Tenant::forgetCurrent();
    $tenant->makeCurrent();
    $response = $this->get(tenantUrl($tenant, '/accommodations'));
    expect($response->status())->toBe(200);
    Tenant::forgetCurrent();
});

// ============ AUTHENTICATION TESTS ============

test('central admin can login', function () {
    $user = User::where('role', 'admin')->first()
        ?? User::factory()->create([
            'role' => 'admin',
            'tenant_id' => null,
        ]);

    $response = $this->post('http://localhost:8000/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect('/admin/dashboard');
});

test('tenant user can login', function () {
    $this->markTestSkipped('Tenant login behavior is covered in auth-focused suites with tenant onboarding context.');

    skipIfLandlordMemoryDb();

    $tenant = ensureRoutingTenantFixture();
    expect($tenant)->not->toBeNull('No tenant found');

    $user = User::where('tenant_id', $tenant->id)
        ->where('role', 'client')
        ->first();

    if (! $user) {
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'client',
        ]);
    }

    expect($user)->not->toBeNull('No tenant user found');

    $response = $this->post(tenantUrl($tenant, '/login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(302);
    $this->assertAuthenticatedAs($user);
});

test('authenticated user can logout', function () {
    $user = User::first() ?? User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    expect($response->status())->toBe(302)
        ->and($this->assertGuest());
});

// ============ PROTECTED ROUTES ============

test('unauthenticated user cannot access dashboard', function () {
    $response = $this->get('http://localhost:8000/dashboard');
    $response->assertRedirect('/login');
});

test('unauthenticated user cannot access messages', function () {
    skipIfLandlordMemoryDb();

    $tenant = ensureRoutingTenantFixture();
    expect($tenant)->not->toBeNull('No tenant found');
    Tenant::forgetCurrent();
    $tenant->makeCurrent();
    $response = $this->get(tenantUrl($tenant, '/messages'));
    expect($response->status())->toBeIn([302, 403]);
    Tenant::forgetCurrent();
});
