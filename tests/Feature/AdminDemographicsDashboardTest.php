<?php

use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\QueryException;

it('builds demographics report for all tenants and selected tenant scopes', function () {
    try {
        Tenant::query()->count();
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is not available in this environment.');
    }

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $tenantAOwner = User::factory()->create([
        'role' => User::ROLE_OWNER,
    ]);
    $tenantA = Tenant::create([
        'name' => 'Tenant A',
        'slug' => 'tenant-a',
        'owner_id' => $tenantAOwner->id,
        'plan' => Tenant::PLAN_BASIC,
        'subscription_status' => 'active',
    ]);

    $tenantBOwner = User::factory()->create([
        'role' => User::ROLE_OWNER,
    ]);
    $tenantB = Tenant::create([
        'name' => 'Tenant B',
        'slug' => 'tenant-b',
        'owner_id' => $tenantBOwner->id,
        'plan' => Tenant::PLAN_BASIC,
        'subscription_status' => 'active',
    ]);

    $accommodationA = Accommodation::create([
        'owner_id' => $tenantAOwner->id,
        'tenant_id' => $tenantA->id,
        'name' => 'A Unit',
        'type' => 'airbnb',
        'description' => 'Desc',
        'address' => 'Address A',
        'barangay' => 'Barangay A',
        'price_per_night' => 1000,
        'bedrooms' => 1,
        'bathrooms' => 1,
        'max_guests' => 2,
        'is_available' => true,
        'is_verified' => true,
    ]);
    $accommodationB = Accommodation::create([
        'owner_id' => $tenantBOwner->id,
        'tenant_id' => $tenantB->id,
        'name' => 'B Unit',
        'type' => 'daily-rental',
        'description' => 'Desc',
        'address' => 'Address B',
        'barangay' => 'Barangay B',
        'price_per_night' => 900,
        'bedrooms' => 1,
        'bathrooms' => 1,
        'max_guests' => 2,
        'is_available' => true,
        'is_verified' => true,
    ]);

    $client = User::factory()->create([
        'role' => User::ROLE_CLIENT,
        'tenant_id' => $tenantA->id,
    ]);

    Booking::create([
        'client_id' => $client->id,
        'accommodation_id' => $accommodationA->id,
        'tenant_id' => $tenantA->id,
        'check_in_date' => now()->subDays(2)->toDateString(),
        'check_out_date' => now()->addDays(1)->toDateString(),
        'number_of_guests' => 2,
        'guest_gender' => 'male',
        'guest_age' => 27,
        'guest_is_local' => true,
        'guest_local_place' => 'Bukidnon',
        'total_price' => 2000,
        'status' => Booking::STATUS_COMPLETED,
    ]);

    Booking::create([
        'client_id' => $client->id,
        'accommodation_id' => $accommodationB->id,
        'tenant_id' => $tenantB->id,
        'check_in_date' => now()->subDays(1)->toDateString(),
        'check_out_date' => now()->addDays(2)->toDateString(),
        'number_of_guests' => 1,
        'guest_gender' => 'female',
        'guest_age' => 34,
        'guest_is_local' => false,
        'guest_country' => 'Japan',
        'total_price' => 900,
        'status' => Booking::STATUS_PAID,
    ]);

    $allResponse = $this
        ->actingAs($admin)
        ->get('/admin/reports/demographics?start_date='.now()->subMonth()->toDateString().'&end_date='.now()->addMonth()->toDateString());
    $allResponse->assertOk();
    $allDemographics = $allResponse->viewData('demographics');

    expect($allDemographics['total_bookings'])->toBe(2);
    expect($allDemographics['gender']['raw']['male'])->toBe(1);
    expect($allDemographics['gender']['raw']['female'])->toBe(1);
    expect($allDemographics['location']['raw']['local'])->toBe(1);
    expect($allDemographics['location']['raw']['foreign'])->toBe(1);

    $tenantResponse = $this
        ->actingAs($admin)
        ->get('/admin/reports/demographics?tenant_id='.$tenantA->id.'&start_date='.now()->subMonth()->toDateString().'&end_date='.now()->addMonth()->toDateString());
    $tenantResponse->assertOk();
    $tenantDemographics = $tenantResponse->viewData('demographics');

    expect($tenantDemographics['total_bookings'])->toBe(1);
    expect($tenantDemographics['scope_label'])->toContain('Tenant A');
    expect($tenantDemographics['location']['raw']['local'])->toBe(1);
    expect($tenantDemographics['location']['raw']['foreign'])->toBe(0);
});
