<?php

use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\QueryException;

it('filters owner bookings by selected status', function () {
    try {
        Accommodation::query()->count();
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is not available in this environment.');
    }

    $owner = User::factory()->create([
        'role' => User::ROLE_OWNER,
    ]);

    $client = User::factory()->create([
        'role' => User::ROLE_CLIENT,
    ]);

    $accommodation = Accommodation::create([
        'owner_id' => $owner->id,
        'name' => 'Owner Test Unit',
        'type' => 'airbnb',
        'description' => 'Test unit for status filtering',
        'address' => 'Poblacion, Impasugong',
        'barangay' => 'Poblacion',
        'price_per_night' => 1800,
        'bedrooms' => 2,
        'bathrooms' => 1,
        'max_guests' => 4,
        'is_available' => true,
        'is_verified' => true,
    ]);

    $pendingBooking = Booking::create([
        'client_id' => $client->id,
        'accommodation_id' => $accommodation->id,
        'check_in_date' => now()->addDays(3)->toDateString(),
        'check_out_date' => now()->addDays(5)->toDateString(),
        'number_of_guests' => 2,
        'total_price' => 3600,
        'status' => Booking::STATUS_PENDING,
    ]);

    $confirmedBooking = Booking::create([
        'client_id' => $client->id,
        'accommodation_id' => $accommodation->id,
        'check_in_date' => now()->addDays(6)->toDateString(),
        'check_out_date' => now()->addDays(8)->toDateString(),
        'number_of_guests' => 2,
        'total_price' => 3600,
        'status' => Booking::STATUS_CONFIRMED,
    ]);

    $response = $this
        ->actingAs($owner)
        ->get('/owner/bookings?status=pending');

    $response->assertOk();
    $response->assertSee((string) $pendingBooking->id);
    $response->assertDontSee((string) $confirmedBooking->id);
});

it('saves profile notification preferences', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_CLIENT,
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Client Updated',
            'email' => $user->email,
            'phone' => '09171234567',
            'address' => 'Impasugong',
            'bio' => 'Updated profile for notification prefs test.',
            'notify_booking_updates' => '1',
            'notify_messages' => '1',
            // Marketing intentionally omitted to verify false fallback.
        ]);

    $response->assertRedirect('/profile');

    $user->refresh();

    expect($user->notification_preferences)->toBeArray();
    expect($user->notification_preferences['booking_updates'] ?? null)->toBeTrue();
    expect($user->notification_preferences['messages'] ?? null)->toBeTrue();
    expect($user->notification_preferences['marketing'] ?? null)->toBeFalse();
});
