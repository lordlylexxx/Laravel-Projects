<?php

use App\Models\AppRelease;
use App\Models\Tenant;
use App\Models\TenantUpdate;
use App\Models\User;
use App\Services\AdminReleaseService;
use App\Services\ReleaseRegistryService;
use App\Services\TenantUpdateService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

function createUpdateTenantFixture(string $nameSuffix = 'A'): array
{
    $owner = User::factory()->create([
        'role' => User::ROLE_OWNER,
    ]);

    $slug = 'update-tenant-'.Str::lower(Str::random(8)).'-'.Str::slug($nameSuffix);

    $tenant = Tenant::query()->create([
        'name' => "Update Tenant {$nameSuffix}",
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

    $owner->update(['tenant_id' => $tenant->id]);

    return compact('owner', 'tenant');
}

it('keeps exactly one current release per tenant when marking updated', function () {
    try {
        $fixture = createUpdateTenantFixture('single-current');
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is unavailable in this environment.');
    }

    $releaseOne = AppRelease::query()->create([
        'tag' => 'v1.0.0-test',
        'title' => 'Version 1.0.0',
        'is_stable' => true,
        'published_at' => now()->subDay(),
    ]);
    $releaseTwo = AppRelease::query()->create([
        'tag' => 'v1.1.0-test',
        'title' => 'Version 1.1.0',
        'is_stable' => true,
        'published_at' => now(),
    ]);

    $service = app(TenantUpdateService::class);
    $service->markAsUpdated((int) $fixture['tenant']->id, (int) $releaseOne->id);
    $service->markAsUpdated((int) $fixture['tenant']->id, (int) $releaseTwo->id);

    $current = TenantUpdate::query()
        ->where('tenant_id', (int) $fixture['tenant']->id)
        ->where('is_current', true)
        ->get();

    expect($current)->toHaveCount(1);
    expect((int) $current->first()->app_release_id)->toBe((int) $releaseTwo->id);
});

it('applies required flag with grace period to tenants not on target release', function () {
    try {
        $fixtureA = createUpdateTenantFixture('required-A');
        $fixtureB = createUpdateTenantFixture('required-B');
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is unavailable in this environment.');
    }

    $olderRelease = AppRelease::query()->create([
        'tag' => 'v2.0.0-test',
        'title' => 'Version 2.0.0',
        'is_stable' => true,
        'published_at' => now()->subDay(),
    ]);
    $requiredRelease = AppRelease::query()->create([
        'tag' => 'v2.1.0-test',
        'title' => 'Version 2.1.0',
        'is_stable' => true,
        'published_at' => now(),
    ]);

    $tenantUpdateService = app(TenantUpdateService::class);
    $tenantUpdateService->markAsUpdated((int) $fixtureA['tenant']->id, (int) $requiredRelease->id);
    $tenantUpdateService->markAsUpdated((int) $fixtureB['tenant']->id, (int) $olderRelease->id);

    app(AdminReleaseService::class)->markAsRequired((int) $requiredRelease->id, 7);

    $requiredRecordA = TenantUpdate::query()->where('tenant_id', (int) $fixtureA['tenant']->id)->where('app_release_id', (int) $requiredRelease->id)->first();
    $requiredRecordB = TenantUpdate::query()->where('tenant_id', (int) $fixtureB['tenant']->id)->where('app_release_id', (int) $requiredRelease->id)->first();

    expect($requiredRelease->fresh()->is_required)->toBeTrue();
    expect($requiredRecordA?->required_at)->toBeNull();
    expect($requiredRecordB?->required_at)->not->toBeNull();
    expect($requiredRecordB?->grace_until)->not->toBeNull();
});

it('exposes release registry methods required by two-layer flow', function () {
    $service = app(ReleaseRegistryService::class);

    expect(method_exists($service, 'syncFromGitHub'))->toBeTrue();
    expect(method_exists($service, 'getLatestStableRelease'))->toBeTrue();
    expect(method_exists($service, 'markAsRequired'))->toBeTrue();
});

it('includes prerelease app releases in available tenant updates', function () {
    try {
        $fixture = createUpdateTenantFixture('prerelease-available');
    } catch (QueryException $exception) {
        $this->markTestSkipped('Landlord test database is unavailable in this environment.');
    }

    $currentRelease = AppRelease::query()->create([
        'tag' => 'v1.0.7-dev',
        'title' => 'v1.0.7-dev',
        'is_stable' => false,
        'published_at' => now()->subDays(3),
    ]);
    $prereleaseNewer = AppRelease::query()->create([
        'tag' => 'v1.0.10-dev',
        'title' => 'v1.0.10-dev',
        'is_stable' => false,
        'published_at' => now()->subDay(),
    ]);

    $tenantUpdateService = app(TenantUpdateService::class);
    $tenantUpdateService->markAsUpdated((int) $fixture['tenant']->id, (int) $currentRelease->id);

    $available = $tenantUpdateService->getAvailableUpdates((int) $fixture['tenant']->id);

    expect($available->pluck('id')->all())->toContain((int) $prereleaseNewer->id);
});
