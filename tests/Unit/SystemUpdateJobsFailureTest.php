<?php

use App\Jobs\InstallSystemUpdateJob;
use App\Jobs\RestorePreviousSystemUpdateJob;
use App\Models\UpdateLog;
use App\Services\SystemUpdateInstallerService;
use Illuminate\Support\Facades\Schema;

uses(Tests\TestCase::class);

function skipIfLandlordUnavailableForUpdateJobTests(): void
{
    $landlordDb = (string) config('database.connections.landlord.database', '');

    if ($landlordDb === ':memory:' || $landlordDb === '') {
        test()->markTestSkipped('Landlord test database is not configured for update job tests.');
    }

    try {
        if (! Schema::connection('landlord')->hasTable('update_logs')) {
            test()->markTestSkipped('update_logs table is unavailable (run migrations).');
        }
    } catch (\Throwable) {
        test()->markTestSkipped('Landlord connection is unavailable.');
    }
}

test('install job marks update log as failed when installer throws', function () {
    skipIfLandlordUnavailableForUpdateJobTests();

    $log = UpdateLog::query()->create([
        'tenant_id' => null,
        'user_id' => null,
        'current_version' => '1.0.0',
        'latest_version' => '1.1.0',
        'channel_status' => 'installing',
        'status_message' => 'Queued.',
        'checked_at' => now(),
        'install_started_at' => now(),
    ]);

    $installer = Mockery::mock(SystemUpdateInstallerService::class);
    $installer->shouldReceive('installFromDownload')
        ->once()
        ->andThrow(new RuntimeException('install exploded'));

    $job = new InstallSystemUpdateJob($log->id, 'https://example.test/update.zip', null);

    expect(fn () => $job->handle($installer))->toThrow(RuntimeException::class, 'install exploded');

    $log->refresh();
    expect($log->channel_status)->toBe('failed')
        ->and((string) $log->install_error)->toContain('install exploded');
});

test('restore job marks update log as failed when installer throws', function () {
    skipIfLandlordUnavailableForUpdateJobTests();

    $log = UpdateLog::query()->create([
        'tenant_id' => null,
        'user_id' => null,
        'current_version' => '1.1.0',
        'latest_version' => '1.0.0',
        'channel_status' => 'restoring',
        'status_message' => 'Queued.',
        'checked_at' => now(),
        'install_started_at' => now(),
    ]);

    $installer = Mockery::mock(SystemUpdateInstallerService::class);
    $installer->shouldReceive('restoreFromBackup')
        ->once()
        ->andThrow(new RuntimeException('restore exploded'));

    $job = new RestorePreviousSystemUpdateJob($log->id, '/tmp/backup.zip', null, '1.0.0');

    expect(fn () => $job->handle($installer))->toThrow(RuntimeException::class, 'restore exploded');

    $log->refresh();
    expect($log->channel_status)->toBe('failed')
        ->and((string) $log->install_error)->toContain('restore exploded');
});
