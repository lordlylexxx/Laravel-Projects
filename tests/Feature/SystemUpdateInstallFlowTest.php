<?php

use App\Jobs\InstallSystemUpdateJob;
use App\Jobs\RestorePreviousSystemUpdateJob;
use App\Models\UpdateLog;
use App\Models\User;
use App\Services\CentralUpdateService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;

function skipIfLandlordUnavailableForSystemUpdates(): void
{
    $landlordDb = (string) config('database.connections.landlord.database', '');

    if ($landlordDb === ':memory:' || $landlordDb === '') {
        test()->markTestSkipped('Landlord test database is not configured for system update tests.');
    }

    try {
        if (! Schema::connection('landlord')->hasTable('update_logs')) {
            test()->markTestSkipped('update_logs table is unavailable (run migrations).');
        }
    } catch (\Throwable) {
        test()->markTestSkipped('Landlord connection is unavailable.');
    }
}

test('central admin can queue system update installation', function () {
    skipIfLandlordUnavailableForSystemUpdates();

    Queue::fake();

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $this->mock(CentralUpdateService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('checkForUpdates')
            ->once()
            ->andReturn([
                'download_url' => 'https://example.test/releases/latest-update.zip',
                'latest_version' => '1.2.3',
                'release_notes' => 'Test release notes',
            ]);
    });

    $this->actingAs($admin)
        ->post('http://localhost:8000/admin/system-updates/install')
        ->assertRedirect('/admin/system-updates');

    Queue::assertPushed(InstallSystemUpdateJob::class);

    $log = UpdateLog::query()->latest('id')->first();
    expect($log)->not->toBeNull()
        ->and($log->channel_status)->toBe('installing')
        ->and($log->current_step)->toBe('queued')
        ->and($log->progress_percent)->toBe(0)
        ->and($log->download_url)->toContain('latest-update.zip');
});

test('central admin cannot queue install when no newer version exists', function () {
    skipIfLandlordUnavailableForSystemUpdates();

    Queue::fake();

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $this->mock(CentralUpdateService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('checkForUpdates')
            ->once()
            ->andReturn([
                'download_url' => 'https://example.test/releases/latest-update.zip',
                'latest_version' => '1.0.0',
                'release_notes' => 'No update available',
            ]);
    });

    $this->actingAs($admin)
        ->post('http://localhost:8000/admin/system-updates/install')
        ->assertRedirect('/admin/system-updates')
        ->assertSessionHas('error');

    Queue::assertNothingPushed();
});

test('guest cannot queue central install update job', function () {
    skipIfLandlordUnavailableForSystemUpdates();

    Queue::fake();

    $this->post('http://localhost:8000/admin/system-updates/install')
        ->assertRedirect('/login');

    Queue::assertNothingPushed();
});

test('restore endpoint is blocked when backup package does not exist', function () {
    skipIfLandlordUnavailableForSystemUpdates();

    Queue::fake();

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    $this->actingAs($admin)
        ->post('http://localhost:8000/admin/system-updates/restore')
        ->assertRedirect('/admin/system-updates')
        ->assertSessionHas('error');

    Queue::assertNothingPushed();
});

test('restore endpoint queues rollback job when backup exists', function () {
    skipIfLandlordUnavailableForSystemUpdates();

    Queue::fake();

    $backupPath = storage_path('app/updates/tests/restore-backup.zip');
    File::ensureDirectoryExists(dirname($backupPath));
    File::put($backupPath, 'placeholder');

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'tenant_id' => null,
    ]);

    try {
        UpdateLog::query()->create([
            'tenant_id' => null,
            'user_id' => $admin->id,
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'channel_status' => 'installed',
            'status_message' => 'Installed previously.',
            'checked_at' => now()->subMinute(),
            'installed_at' => now()->subMinute(),
            'backup_path' => $backupPath,
            'backup_version' => '1.0.0',
        ]);
    } catch (QueryException $e) {
        if (str_contains($e->getMessage(), 'Lock wait timeout exceeded')) {
            test()->markTestSkipped('Landlord database is currently locked by another process.');
        }

        throw $e;
    }

    $this->actingAs($admin)
        ->post('http://localhost:8000/admin/system-updates/restore')
        ->assertRedirect('/admin/system-updates');

    Queue::assertPushed(RestorePreviousSystemUpdateJob::class);

    $restoreLog = UpdateLog::query()->latest('id')->first();
    expect($restoreLog)->not->toBeNull()
        ->and($restoreLog->channel_status)->toBe('restoring')
        ->and($restoreLog->restored_from_update_log_id)->not->toBeNull();
});
