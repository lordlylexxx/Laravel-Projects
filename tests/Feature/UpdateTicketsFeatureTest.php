<?php

use App\Models\Tenant;
use App\Models\UpdateTicket;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

function skipIfLandlordUnavailableForUpdateTickets(): void
{
    $landlordDb = (string) config('database.connections.landlord.database', '');

    if ($landlordDb === ':memory:' || $landlordDb === '') {
        test()->markTestSkipped('Landlord test database is not configured for update ticket tests.');
    }

    try {
        if (! Schema::connection('landlord')->hasTable('tenants')) {
            test()->markTestSkipped('Landlord tenants table is unavailable.');
        }
        if (! Schema::connection('landlord')->hasTable('update_tickets')) {
            test()->markTestSkipped('update_tickets table is unavailable (run migrations).');
        }
    } catch (\Throwable) {
        test()->markTestSkipped('Landlord connection is unavailable.');
    }
}

function ensureTestTenant(): Tenant
{
    $tenant = Tenant::query()->first();

    if ($tenant) {
        return $tenant;
    }

    return Tenant::query()->create([
        'name' => 'Test Tenant',
        'slug' => 'test-tenant-'.Str::random(8),
        'plan' => Tenant::PLAN_BASIC,
        'subscription_status' => 'trialing',
        'domain_enabled' => false,
        'database_provisioned' => false,
        'feature_bookings' => true,
        'feature_messaging' => true,
        'feature_reviews' => true,
        'feature_payments' => true,
    ]);
}

test('guest is redirected from central admin update tickets index', function () {
    skipIfLandlordUnavailableForUpdateTickets();

    $response = $this->get('http://localhost:8000/admin/system-updates/tickets');

    $response->assertRedirect('/login');
});

test('central admin can view update tickets index', function () {
    skipIfLandlordUnavailableForUpdateTickets();

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $this->actingAs($admin)
        ->get('http://localhost:8000/admin/system-updates/tickets')
        ->assertOk();
});

test('central admin can resolve and reopen an update ticket', function () {
    skipIfLandlordUnavailableForUpdateTickets();

    $tenant = ensureTestTenant();

    $ticket = UpdateTicket::query()->create([
        'tenant_id' => $tenant->id,
        'reporter_landlord_user_id' => null,
        'reporter_role' => User::ROLE_CLIENT,
        'reporter_name' => 'Test Reporter',
        'reporter_email' => 'reporter-update-ticket@example.test',
        'subject' => 'Test update ticket',
        'body' => 'Something is wrong with the channel.',
        'status' => UpdateTicket::STATUS_OPEN,
    ]);

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.update-tickets.update', $ticket), [
            'action' => 'resolve',
            'resolution_notes' => 'Issue verified and fixed in central release.',
        ])
        ->assertRedirect(route('admin.update-tickets.show', $ticket));

    $ticket->refresh();
    expect($ticket->status)->toBe(UpdateTicket::STATUS_RESOLVED)
        ->and($ticket->resolution_notes)->toContain('Issue verified');

    $this->actingAs($admin)
        ->patch(route('admin.update-tickets.update', $ticket), [
            'action' => 'reopen',
            'reopen_note' => 'Tenant reported regression.',
        ])
        ->assertRedirect(route('admin.update-tickets.show', $ticket));

    $ticket->refresh();
    expect($ticket->status)->toBe(UpdateTicket::STATUS_OPEN)
        ->and($ticket->resolved_at)->toBeNull();
});

test('central admin can unresolve an update ticket without notes', function () {
    skipIfLandlordUnavailableForUpdateTickets();

    $tenant = ensureTestTenant();

    $ticket = UpdateTicket::query()->create([
        'tenant_id' => $tenant->id,
        'reporter_landlord_user_id' => null,
        'reporter_role' => User::ROLE_CLIENT,
        'reporter_name' => 'Test Reporter',
        'reporter_email' => 'reporter-update-ticket@example.test',
        'subject' => 'Resolved ticket',
        'body' => 'This ticket is resolved and should be reopened.',
        'status' => UpdateTicket::STATUS_RESOLVED,
        'resolution_notes' => 'Marked resolved previously.',
        'resolved_at' => now(),
        'resolved_by_landlord_user_id' => null,
    ]);

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.update-tickets.update', $ticket), [
            'action' => 'unresolve',
        ])
        ->assertRedirect(route('admin.update-tickets.show', $ticket));

    $ticket->refresh();
    expect($ticket->status)->toBe(UpdateTicket::STATUS_OPEN)
        ->and($ticket->resolved_at)->toBeNull()
        ->and($ticket->reopen_note)->toBeNull();
});

test('resolve action requires resolution notes', function () {
    skipIfLandlordUnavailableForUpdateTickets();

    $tenant = ensureTestTenant();

    $ticket = UpdateTicket::query()->create([
        'tenant_id' => $tenant->id,
        'reporter_landlord_user_id' => null,
        'reporter_role' => User::ROLE_OWNER,
        'reporter_name' => 'Owner',
        'reporter_email' => 'owner-update-ticket@example.test',
        'subject' => 'Needs notes',
        'body' => 'Body',
        'status' => UpdateTicket::STATUS_OPEN,
    ]);

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $this->actingAs($admin)
        ->from(route('admin.update-tickets.show', $ticket))
        ->patch(route('admin.update-tickets.update', $ticket), [
            'action' => 'resolve',
            'resolution_notes' => '',
        ])
        ->assertRedirect(route('admin.update-tickets.show', $ticket))
        ->assertSessionHasErrors('resolution_notes');
});
