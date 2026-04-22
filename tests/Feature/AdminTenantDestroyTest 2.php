<?php

use App\Models\Tenant;
use App\Models\TenantLifecycleLog;
use App\Models\User;
use Illuminate\Database\QueryException;

it('rejects tenant delete when confirm slug does not match', function () {
    try {
        Tenant::query()->count();
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is not available in this environment.');
    }

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $tenant = Tenant::create([
        'name' => 'Destroy Slug Test',
        'slug' => 'destroy-slug-test',
        'plan' => Tenant::PLAN_BASIC,
        'subscription_status' => 'active',
    ]);

    $response = $this
        ->actingAs($admin)
        ->from('/admin/tenants')
        ->delete(route('admin.tenants.destroy', $tenant), [
            'reason' => 'Testing validation of slug confirmation.',
            'confirm_slug' => 'wrong-slug',
        ]);

    $response->assertRedirect('/admin/tenants');
    $response->assertSessionHasErrors('confirm_slug');
    expect(Tenant::query()->whereKey($tenant->id)->exists())->toBeTrue();
});

it('deletes tenant and retains lifecycle log with null tenant_id', function () {
    try {
        Tenant::query()->count();
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is not available in this environment.');
    }

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $tenant = Tenant::create([
        'name' => 'Full Delete Test',
        'slug' => 'full-delete-test',
        'plan' => Tenant::PLAN_BASIC,
        'subscription_status' => 'active',
    ]);

    $slug = $tenant->slug;

    $response = $this
        ->actingAs($admin)
        ->from('/admin/tenants')
        ->delete(route('admin.tenants.destroy', $tenant), [
            'reason' => 'Integration test: remove tenant record.',
            'confirm_slug' => $slug,
        ]);

    $response->assertRedirect('/admin/tenants');
    $response->assertSessionHasNoErrors();

    expect(Tenant::query()->where('slug', $slug)->exists())->toBeFalse();

    $log = TenantLifecycleLog::query()
        ->where('action', 'tenant.deleted')
        ->whereNull('tenant_id')
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->before_state['slug'] ?? null)->toBe($slug);
    expect($log->reason)->toContain('Integration test');
});
