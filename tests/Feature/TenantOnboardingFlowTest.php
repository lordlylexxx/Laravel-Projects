<?php

use App\Models\Tenant;
use App\Models\TenantLifecycleLog;
use App\Models\User;
use App\Services\TenantOnboardingService;

/**
 * Requires MySQL and database `laravel_testing` (see phpunit.xml).
 * Default and landlord connections use the same database so `users` (default) and
 * `tenants` (landlord) satisfy FK constraints during owner registration.
 */
it('creates tenant in awaiting_payment without provisioning on owner registration', function () {
    $response = $this->post('/register', [
        'name' => 'Onboarding Flow Owner',
        'email' => 'onboarding-flow-owner@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'owner',
    ]);

    $response->assertRedirect(route('owner.onboarding.payment'));
    $this->assertAuthenticated();

    $user = User::query()->where('email', 'onboarding-flow-owner@example.com')->first();
    expect($user)->not->toBeNull();

    $tenant = Tenant::query()->where('owner_user_id', $user->id)->first();
    expect($tenant)->not->toBeNull();
    expect($tenant->onboarding_status)->toBe(Tenant::ONBOARDING_AWAITING_PAYMENT);
    expect((bool) $tenant->domain_enabled)->toBeFalse();
    expect((bool) $tenant->database_provisioned)->toBeFalse();
});

it('submits mock payment and moves tenant to pending approval', function () {
    $owner = User::factory()->create([
        'role' => User::ROLE_OWNER,
        'email' => 'pay-submit@example.com',
    ]);

    $tenant = Tenant::create([
        'name' => 'Pay Submit Tenant',
        'slug' => 'pay-submit-tenant',
        'owner_user_id' => $owner->id,
        'plan' => Tenant::PLAN_BASIC,
        'subscription_status' => 'trialing',
        'trial_ends_at' => now()->addDays(14),
        'current_period_starts_at' => now(),
        'current_period_ends_at' => now()->addMonth(),
        'onboarding_status' => Tenant::ONBOARDING_AWAITING_PAYMENT,
        'domain_enabled' => false,
        'domain' => 'pay-submit.example.test',
        'database' => 'pay_submit_db',
        'db_host' => '127.0.0.1',
        'db_port' => 3306,
        'db_username' => 'root',
        'db_password' => '',
    ]);

    $owner->update(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($owner)->post(route('owner.onboarding.payment.submit'));

    $response->assertRedirect(route('owner.onboarding.status'));

    $tenant->refresh();
    expect($tenant->onboarding_status)->toBe(Tenant::ONBOARDING_PENDING_APPROVAL);
    expect($tenant->payment_submitted_at)->not->toBeNull();

    $log = TenantLifecycleLog::query()
        ->where('tenant_id', $tenant->id)
        ->where('action', 'tenant.payment.submitted')
        ->first();

    expect($log)->not->toBeNull();
});

it('delegates admin approval to tenant onboarding service', function () {
    $this->mock(TenantOnboardingService::class, function ($mock) {
        $mock->shouldReceive('approveRegistration')
            ->once()
            ->withArgs(function (Tenant $tenant, $actor, bool $allowFromPendingPayment): bool {
                return $tenant->onboarding_status === Tenant::ONBOARDING_PENDING_APPROVAL
                    && $actor instanceof User
                    && $allowFromPendingPayment === false;
            })
            ->andReturn(['success' => true, 'credentials_emailed' => true]);
    });

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $owner = User::factory()->create([
        'role' => User::ROLE_OWNER,
    ]);

    $tenant = Tenant::create([
        'name' => 'Approve Me Tenant',
        'slug' => 'approve-me-tenant',
        'owner_user_id' => $owner->id,
        'plan' => Tenant::PLAN_BASIC,
        'subscription_status' => 'trialing',
        'trial_ends_at' => now()->addDays(14),
        'current_period_starts_at' => now(),
        'current_period_ends_at' => now()->addMonth(),
        'onboarding_status' => Tenant::ONBOARDING_PENDING_APPROVAL,
        'domain_enabled' => false,
        'domain' => 'approve-me.example.test',
        'database' => 'approve_me_db',
        'db_host' => '127.0.0.1',
        'db_port' => 3306,
        'db_username' => 'root',
        'db_password' => '',
        'payment_reference' => 'TXN-TEST-REF',
        'payment_submitted_at' => now(),
    ]);

    $response = $this->actingAs($admin)->from(route('admin.tenants'))->post(
        route('admin.tenants.approve-onboarding', $tenant),
        ['reason' => 'Integration test approval reason.']
    );

    $response->assertRedirect(route('admin.tenants'));
    $response->assertSessionHasNoErrors();
});
