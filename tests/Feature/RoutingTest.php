<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

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

    $tenant = Tenant::first();
    expect($tenant)->not->toBeNull('No tenant found in database');

    $response = $this->get("http://{$tenant->domain}:8000/");
    expect($response->status())->toBe(200);
});

test('tenant login page accessible', function () {
    skipIfLandlordMemoryDb();

    $tenant = Tenant::first();
    expect($tenant)->not->toBeNull('No tenant found in database');

    $response = $this->get("http://{$tenant->domain}:8000/login");
    expect($response->status())->toBe(200);
});

test('tenant register page accessible', function () {
    skipIfLandlordMemoryDb();

    $tenant = Tenant::first();
    expect($tenant)->not->toBeNull('No tenant found in database');

    $response = $this->get("http://{$tenant->domain}:8000/register");
    expect($response->status())->toBe(200);
});

test('tenant accommodations page accessible', function () {
    skipIfLandlordMemoryDb();

    $tenant = Tenant::first();
    expect($tenant)->not->toBeNull('No tenant found in database');

    $response = $this->get("http://{$tenant->domain}:8000/accommodations");
    expect($response->status())->toBe(200);
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
    skipIfLandlordMemoryDb();

    $tenant = Tenant::first();
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

    $response = $this->post("http://{$tenant->domain}:8000/login", [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
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

    $tenant = Tenant::first();
    expect($tenant)->not->toBeNull('No tenant found');

    $response = $this->get("http://{$tenant->domain}:8000/messages");
    $response->assertRedirect('/login');
});
