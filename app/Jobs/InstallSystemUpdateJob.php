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

class InstallSystemUpdateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Long enough for composer install + npm install + npm build + migrate.
     */
    public int $timeout = 1800;

    /**
     * Never retry: a partially-applied update must be restored manually, not re-run.
     */
    public int $tries = 1;

    public function __construct(
        public int $updateLogId,
        public string $downloadUrl,
        public ?string $checksumUrl = null,
        public ?int $tenantId = null,
    ) {
    }

    public function handle(SystemUpdateInstallerService $installer): void
    {
        $log = UpdateLog::query()->find($this->updateLogId);

        if (! $log) {
            return;
        }

        try {
            $installer->installFromDownload($log, $this->downloadUrl, $this->checksumUrl, $this->tenantId);
        } catch (Throwable $exception) {
            $log->update([
                'channel_status' => 'failed',
                'status_message' => 'Install failed. Use restore to revert if needed.',
                'install_finished_at' => now(),
                'install_error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Called by the queue worker if the job dies (timeout, fatal error, worker crash).
     * Ensures the UpdateLog row never gets stuck in the "installing" state.
     */
    public function failed(?Throwable $exception): void
    {
        $log = UpdateLog::query()->find($this->updateLogId);

        if (! $log) {
            return;
        }

        $log->update([
            'channel_status' => 'failed',
            'status_message' => 'Install job did not complete. Use restore to revert if needed.',
            'install_finished_at' => now(),
            'install_error' => $exception?->getMessage() ?? 'Queue worker terminated before the install completed.',
        ]);
    }
}
