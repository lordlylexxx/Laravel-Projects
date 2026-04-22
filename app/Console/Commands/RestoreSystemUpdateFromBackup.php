<?php

namespace App\Console\Commands;

use App\Models\UpdateLog;
use App\Services\SystemUpdateInstallerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RestoreSystemUpdateFromBackup extends Command
{
    protected $signature = 'system-updates:restore-backup {updateLogId? : The landlord update log ID to restore from} {--backup-path= : Restore from a specific backup archive path}';

    protected $description = 'Restore the system from a previously generated update backup archive.';

    public function handle(SystemUpdateInstallerService $installer): int
    {
        $backupPath = (string) $this->option('backup-path');
        $updateLogId = $this->argument('updateLogId');

        $log = null;

        if ($updateLogId !== null && $updateLogId !== '') {
            $log = UpdateLog::query()->find((int) $updateLogId);

            if (! $log) {
                $this->error('Update log not found.');

                return self::FAILURE;
            }

            $backupPath = $backupPath !== '' ? $backupPath : (string) ($log->backup_path ?? '');
        }

        if ($backupPath === '') {
            $this->error('A backup path or update log ID is required.');

            return self::FAILURE;
        }

        if (! File::exists($backupPath)) {
            $this->error('Backup archive does not exist: '.$backupPath);

            return self::FAILURE;
        }

        $log ??= UpdateLog::query()->whereNotNull('backup_path')->latest('checked_at')->first();

        if (! $log) {
            $this->error('No update log with a usable backup was found.');

            return self::FAILURE;
        }

        $this->info('Starting manual restore from backup...');

        try {
            $installer->restoreFromBackup($log, $backupPath, $log->tenant_id ? (int) $log->tenant_id : null);
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Restore completed successfully.');

        return self::SUCCESS;
    }
}