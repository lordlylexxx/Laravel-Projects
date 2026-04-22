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
}
