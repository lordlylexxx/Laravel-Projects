<?php

use App\Models\User;
use App\Models\Tenant;

// ============ CENTRAL APP ROUTES ============

test('central landing page accessible', function () {
    $response = $this->get('http://localhost:8000/');
    expect($response->status())->toBe(200);
});

test('central 127.0.0.1 landing page accessible', function () {
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
    $tenant = Tenant::first();
    expect($tenant)->not->toBeNull('No tenant found in database');
    
    $response = $this->get("http://{$tenant->domain}:8000/");
    expect($response->status())->toBe(200);
});

test('tenant login page accessible', function () {
    $tenant = Tenant::first();
    expect($tenant)->not->toBeNull('No tenant found in database');
    
    $response = $this->get("http://{$tenant->domain}:8000/login");
    expect($response->status())->toBe(200);
});

test('tenant register page accessible', function () {
    $tenant = Tenant::first();
    expect($tenant)->not->toBeNull('No tenant found in database');
    
    $response = $this->get("http://{$tenant->domain}:8000/register");
    expect($response->status())->toBe(200);
});

test('tenant accommodations page accessible', function () {
    $tenant = Tenant::first();
    expect($tenant)->not->toBeNull('No tenant found in database');
    
    $response = $this->get("http://{$tenant->domain}:8000/accommodations");
    expect($response->status())->toBe(200);
});

// ============ AUTHENTICATION TESTS ============

test('central admin can login', function () {
    $user = User::where('role', 'admin')->first();
    expect($user)->not->toBeNull('No admin user found');
    
    $response = $this->post('http://localhost:8000/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);
    
    expect($response->status())->toBe(302);
    expect($response)->toHaveSessionPath('landing');
});

test('tenant user can login', function () {
    $tenant = Tenant::first();
    expect($tenant)->not->toBeNull('No tenant found');
    
    $user = User::where('tenant_id', $tenant->id)
        ->where('role', '!=', 'admin')
        ->first();
    expect($user)->not->toBeNull('No tenant user found');
    
    $response = $this->post("http://{$tenant->domain}:8000/login", [
        'email' => $user->email,
        'password' => 'password',
    ]);
    
    expect($response->status())->toBe(302);
    expect($response)->toHaveSessionPath('landing');
});

test('authenticated user can logout', function () {
    $user = User::first();
    expect($user)->not->toBeNull('No user found');
    
    $response = $this->actingAs($user)->post('/logout');
    
    expect($response->status())->toBe(302)
        ->and($this->assertGuest());
});

// ============ PROTECTED ROUTES ============

test('unauthenticated user cannot access dashboard', function () {
    $response = $this->get('http://localhost:8000/dashboard');
    expect($response->status())->toBe(302)
        ->and($response)->toHaveSessionPath('login');
});

test('unauthenticated user cannot access messages', function () {
    $tenant = Tenant::first();
    expect($tenant)->not->toBeNull('No tenant found');
    
    $response = $this->get("http://{$tenant->domain}:8000/messages");
    expect($response->status())->toBe(302);
});
