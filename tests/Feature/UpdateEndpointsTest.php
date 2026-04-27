<?php

use App\Models\AppRelease;
use App\Models\User;
use App\Services\AdminReleaseService;
use App\Services\ReleaseRegistryService;

it('renders admin updates index endpoint', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $this->actingAs($admin);

    $response = $this->get('/admin/system-updates');

    $response->assertOk();
    $response->assertSee('Global Release Registry');
});

it('sync endpoint returns success flash', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $mock = \Mockery::mock(ReleaseRegistryService::class);
    $mock->shouldReceive('syncFromGitHub')
        ->once()
        ->andReturn(['synced' => 2, 'updated' => 1, 'skipped' => 0]);
    app()->instance(ReleaseRegistryService::class, $mock);

    $response = $this->actingAs($admin)->post('/admin/system-updates/sync');

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

it('required endpoint marks release with grace period', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $release = AppRelease::query()->create([
        'tag' => 'v9.9.0-test-endpoint',
        'title' => 'Endpoint Test Release',
        'is_stable' => true,
        'published_at' => now(),
    ]);

    $mock = \Mockery::mock(AdminReleaseService::class);
    $mock->shouldReceive('markAsRequired')
        ->once()
        ->with((int) $release->id, 7);
    app()->instance(AdminReleaseService::class, $mock);

    $response = $this->actingAs($admin)->post("/admin/system-updates/{$release->id}/required", [
        'grace_days' => 7,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

it('notify-all and force-mark-all endpoints respond successfully', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $release = AppRelease::query()->create([
        'tag' => 'v9.9.1-test-endpoint',
        'title' => 'Endpoint Test Release 2',
        'is_stable' => true,
        'published_at' => now(),
    ]);

    $mock = \Mockery::mock(AdminReleaseService::class);
    $mock->shouldReceive('notifyAllTenantsOfUpdate')
        ->once()
        ->with((int) $release->id)
        ->andReturn(4);
    $mock->shouldReceive('forceMarkAllAsUpdated')
        ->once()
        ->with((int) $release->id)
        ->andReturn(4);
    app()->instance(AdminReleaseService::class, $mock);

    $notifyResponse = $this->actingAs($admin)->post("/admin/system-updates/{$release->id}/notify-all");
    $notifyResponse->assertRedirect();
    $notifyResponse->assertSessionHas('success');

    $forceResponse = $this->actingAs($admin)->post("/admin/system-updates/{$release->id}/force-mark-all-updated");
    $forceResponse->assertRedirect();
    $forceResponse->assertSessionHas('success');
});
