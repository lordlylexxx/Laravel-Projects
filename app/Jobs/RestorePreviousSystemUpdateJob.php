<?php

namespace App\Jobs;

use App\Models\UpdateLog;
use App\Services\SystemUpdateInstallerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class RestorePreviousSystemUpdateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $updateLogId,
        public string $backupPath,
        public ?int $tenantId = null,
        public ?string $targetVersion = null,
    ) {
    }

    public function handle(SystemUpdateInstallerService $installer): void
    {
        $log = UpdateLog::query()->find($this->updateLogId);

        if (! $log) {
            return;
        }

        try {
            $installer->restoreFromBackup($log, $this->backupPath, $this->tenantId);
        } catch (Throwable $exception) {
            $log->update([
                'channel_status' => 'failed',
                'status_message' => 'Restore failed. Please check install logs.',
                'install_finished_at' => now(),
                'install_error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
