<?php

namespace App\Services;

use App\Models\UpdateLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;
use ZipArchive;

class SystemUpdateInstallerService
{
    /**
     * Runtime-managed paths that should never be replaced by release deployment.
     *
     * @var array<int, string>
     */
    private array $excludedRuntimePaths = [
        '.env',
        '.git/',
        'node_modules/',
        'storage/',
        'bootstrap/cache/',
    ];

    public function installFromDownload(UpdateLog $log, string $downloadUrl, ?string $checksumUrl = null, ?int $tenantId = null): void
    {
        $this->runWithLock($tenantId, function () use ($log, $downloadUrl, $checksumUrl): void {
            $instanceRoot = $this->instanceWorkingPath($log->tenant_id);
            $downloadDir = $instanceRoot.'/downloads';
            $extractBaseDir = $instanceRoot.'/tmp';
            $backupDir = $instanceRoot.'/backups';
            $extractDir = null;

            File::ensureDirectoryExists($downloadDir);
            File::ensureDirectoryExists($extractBaseDir);
            File::ensureDirectoryExists($backupDir);

            $this->updateProgress($log, 'preparing', 5, 'Preparing update workspace...');

            $backupPath = $this->createBackupArchive((string) $backupDir, (string) $log->current_version);
            $log->update([
                'backup_path' => $backupPath,
                'backup_version' => (string) $log->current_version,
                'status_message' => 'Backup created. Downloading package...',
                'current_step' => 'backed_up',
                'progress_percent' => 15,
            ]);

            $this->updateProgress($log, 'downloading', 20, 'Downloading latest release package...');
            $downloadedZip = $this->downloadPackage($downloadUrl, $downloadDir, $checksumUrl, $log);
            $extractDir = $extractBaseDir.'/install_'.Str::random(8);
            $this->updateProgress($log, 'extracting', 40, 'Extracting release archive...');
            $releaseRoot = $this->extractAndResolveReleaseRoot($downloadedZip, $extractDir);
            $this->validateReleaseStructure($releaseRoot);

            $this->enableMaintenanceMode();
            $this->updateProgress($log, 'deploying', 55, 'Overwriting application files...');
            $this->deployRelease($releaseRoot);
            $this->updateProgress($log, 'dependencies', 70, 'Installing composer and frontend dependencies...');
            $this->runPostDeployCommands($log, $backupDir, true);

            $log->update([
                'channel_status' => 'installed',
                'status_message' => 'Update installed successfully.',
                'current_step' => 'completed',
                'progress_percent' => 100,
                'installed_at' => now(),
                'install_finished_at' => now(),
                'install_error' => null,
            ]);

            try {
                if ($extractDir) {
                    File::deleteDirectory($extractDir);
                }
            } finally {
                $this->disableMaintenanceMode();
            }
        });
    }

    public function restoreFromBackup(UpdateLog $log, string $backupPath, ?int $tenantId = null): void
    {
        $this->runWithLock($tenantId, function () use ($log, $backupPath): void {
            if (! File::exists($backupPath)) {
                throw new RuntimeException('Backup package is missing: '.$backupPath);
            }

            $instanceRoot = $this->instanceWorkingPath($log->tenant_id);
            $extractBaseDir = $instanceRoot.'/tmp';
            File::ensureDirectoryExists($extractBaseDir);
            $extractDir = null;

            $this->updateProgress($log, 'preparing_restore', 10, 'Preparing restore workspace...');

            $extractDir = $extractBaseDir.'/restore_'.Str::random(8);
            $releaseRoot = $this->extractAndResolveReleaseRoot($backupPath, $extractDir);
            $this->validateReleaseStructure($releaseRoot);

            $this->enableMaintenanceMode();
            $this->updateProgress($log, 'restoring', 50, 'Restoring previous release...');
            $this->deployRelease($releaseRoot);
            $this->updateProgress($log, 'restoring_dependencies', 80, 'Reinstalling dependencies after restore...');
            $this->runPostDeployCommands($log, null, false);
            $this->restoreAppKeyIfAvailable($log);

            $log->update([
                'channel_status' => 'restored',
                'status_message' => 'Restore completed successfully.',
                'current_step' => 'completed',
                'progress_percent' => 100,
                'installed_at' => now(),
                'restored_at' => now(),
                'install_finished_at' => now(),
                'install_error' => null,
            ]);

            try {
                if ($extractDir) {
                    File::deleteDirectory($extractDir);
                }
            } finally {
                $this->disableMaintenanceMode();
            }
        });
    }

    private function runWithLock(?int $tenantId, callable $callback): void
    {
        $lock = Cache::lock($this->lockKey($tenantId), 3600);

        if (! $lock->get()) {
            throw new RuntimeException('Another system update operation is already running.');
        }

        try {
            $callback();
        } finally {
            $lock->release();
        }
    }

    private function lockKey(?int $tenantId): string
    {
        return 'system_updates:operation:'.($tenantId ? 'tenant_'.$tenantId : 'central');
    }

    private function instanceWorkingPath(?int $tenantId): string
    {
        $instance = $tenantId ? 'tenant_'.$tenantId : 'central';

        return storage_path('app/updates/'.$instance);
    }

    private function createBackupArchive(string $backupDir, string $currentVersion): string
    {
        $safeVersion = preg_replace('/[^A-Za-z0-9._-]/', '-', $currentVersion) ?: 'unknown';
        $backupPath = $backupDir.'/backup_'.$safeVersion.'_'.now()->format('Ymd_His').'.zip';

        $zip = new ZipArchive;
        $result = $zip->open($backupPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($result !== true) {
            throw new RuntimeException('Failed to create backup archive.');
        }

        $sourceRoot = base_path();
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceRoot, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $path = str_replace('\\', '/', $item->getPathname());
            $relativePath = ltrim(str_replace($sourceRoot, '', $path), '/');

            if ($relativePath === '' || $this->shouldSkipPath($relativePath)) {
                continue;
            }

            if ($item->isFile()) {
                $zip->addFile($item->getPathname(), $relativePath);
            }
        }

        $zip->close();

        return $backupPath;
    }

    private function downloadPackage(string $downloadUrl, string $downloadDir, ?string $checksumUrl = null, ?UpdateLog $log = null): string
    {
        $targetPath = rtrim($downloadDir, '/').'/update_'.now()->format('Ymd_His').'_'.Str::random(6).'.zip';

        $this->assertTrustedDownloadUrl($downloadUrl);

        $response = Http::timeout(180)
            ->withOptions(['sink' => $targetPath])
            ->get($downloadUrl);

        if (! $response->ok() || ! File::exists($targetPath)) {
            throw new RuntimeException('Failed to download update package.');
        }

        if ($checksumUrl !== null && $checksumUrl !== '') {
            $this->assertTrustedDownloadUrl($checksumUrl);
            $expected = $this->downloadChecksum($checksumUrl);
            $actual = hash_file('sha256', $targetPath);

            if (! hash_equals(strtolower($expected), strtolower($actual))) {
                throw new RuntimeException('Downloaded package checksum mismatch.');
            }

            if ($log) {
                $log->update([
                    'download_checksum' => $actual,
                    'download_checksum_verified_at' => now(),
                    'status_message' => 'Checksum verified. Extracting package...',
                ]);
            }
        } elseif ((bool) config('updates.require_download_checksum', false)) {
            throw new RuntimeException('Checksum verification is required but no checksum URL was provided.');
        }

        return $targetPath;
    }

    private function downloadChecksum(string $checksumUrl): string
    {
        $response = Http::timeout(60)->get($checksumUrl);

        if (! $response->ok()) {
            throw new RuntimeException('Failed to download package checksum.');
        }

        $body = trim((string) $response->body());

        if ($body === '') {
            throw new RuntimeException('Checksum file is empty.');
        }

        if (preg_match('/\b([a-f0-9]{64})\b/i', $body, $matches) !== 1) {
            throw new RuntimeException('Checksum file does not contain a valid SHA-256 hash.');
        }

        return $matches[1];
    }

    private function extractAndResolveReleaseRoot(string $archivePath, string $extractDir): string
    {
        File::ensureDirectoryExists($extractDir);

        $zip = new ZipArchive;
        $result = $zip->open($archivePath);

        if ($result !== true) {
            throw new RuntimeException('Unable to open package archive: '.$archivePath);
        }

        $zip->extractTo($extractDir);
        $zip->close();

        if ($this->looksLikeReleaseRoot($extractDir)) {
            return $extractDir;
        }

        foreach (File::directories($extractDir) as $directory) {
            if ($this->looksLikeReleaseRoot($directory)) {
                return $directory;
            }
        }

        throw new RuntimeException('Extracted package does not contain a valid Laravel release root.');
    }

    private function looksLikeReleaseRoot(string $path): bool
    {
        return File::exists($path.'/artisan') && File::exists($path.'/composer.json');
    }

    private function validateReleaseStructure(string $releaseRoot): void
    {
        $requiredPaths = ['artisan', 'composer.json', 'package.json', 'app', 'bootstrap', 'config'];

        foreach ($requiredPaths as $relativePath) {
            $fullPath = $releaseRoot.'/'.$relativePath;

            if (! File::exists($fullPath)) {
                throw new RuntimeException('Invalid release package: missing '.$relativePath);
            }
        }
    }

    private function deployRelease(string $releaseRoot): void
    {
        $destinationRoot = base_path();
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($releaseRoot, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $path = str_replace('\\', '/', $item->getPathname());
            $relativePath = ltrim(str_replace($releaseRoot, '', $path), '/');

            if ($relativePath === '' || $this->shouldSkipPath($relativePath)) {
                continue;
            }

            $targetPath = $destinationRoot.'/'.$relativePath;

            if ($item->isDir()) {
                File::ensureDirectoryExists($targetPath);
                continue;
            }

            File::ensureDirectoryExists(dirname($targetPath));
            File::copy($item->getPathname(), $targetPath);
        }
    }

    private function shouldSkipPath(string $relativePath): bool
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

        foreach ($this->excludedRuntimePaths as $excluded) {
            $excluded = trim(str_replace('\\', '/', $excluded), '/');

            if ($relativePath === $excluded || str_starts_with($relativePath, $excluded.'/')) {
                return true;
            }
        }

        return false;
    }

    private function runPostDeployCommands(UpdateLog $log, ?string $backupDir = null, bool $rotateAppKey = true): void
    {
        $this->runCommand([
            $this->composerBinary(),
            'install',
            '--no-dev',
            '--optimize-autoloader',
            '--no-interaction',
        ], 1200);

        $this->runCommand([
            $this->npmBinary(),
            'install',
            '--no-audit',
            '--no-fund',
            '--no-progress',
        ], 1200);

        $this->runCommand([
            $this->npmBinary(),
            'run',
            'build',
        ], 1200);

        $this->runArtisanCommand(['migrate', '--force']);
        if ($rotateAppKey && $backupDir !== null) {
            $this->maybeRegenerateAppKey($log, $backupDir);
        }
        $this->runArtisanCommand(['config:clear']);
        $this->runArtisanCommand(['route:clear']);
        $this->runArtisanCommand(['view:clear']);
        $this->runArtisanCommand(['optimize']);
    }

    /**
     * @param  array<int, string>  $arguments
     */
    private function runArtisanCommand(array $arguments): void
    {
        $command = [PHP_BINARY, base_path('artisan'), ...$arguments];
        $this->runCommand($command, 900);
    }

    /**
     * @param  array<int, string>  $command
     */
    private function runCommand(array $command, int $timeoutSeconds): void
    {
        $process = new Process($command, base_path());
        $process->setTimeout($timeoutSeconds);
        $process->run();

        if (! $process->isSuccessful()) {
            $output = trim($process->getErrorOutput()."\n".$process->getOutput());
            $shortOutput = Str::limit($output === '' ? 'No process output was captured.' : $output, 4000);

            throw new RuntimeException(sprintf(
                'Command failed: %s%s%s',
                implode(' ', $command),
                PHP_EOL,
                $shortOutput
            ));
        }
    }

    private function composerBinary(): string
    {
        $configured = trim((string) env('COMPOSER_BINARY', 'composer'));

        return $configured !== '' ? $configured : 'composer';
    }

    private function npmBinary(): string
    {
        $configured = trim((string) env('NPM_BINARY', 'npm'));

        return $configured !== '' ? $configured : 'npm';
    }

    private function updateProgress(UpdateLog $log, string $step, int $progressPercent, string $message): void
    {
        $log->update([
            'current_step' => $step,
            'progress_percent' => max(0, min(100, $progressPercent)),
            'status_message' => $message,
        ]);
    }

    private function enableMaintenanceMode(): void
    {
        $this->runArtisanCommand(['down']);
    }

    private function disableMaintenanceMode(): void
    {
        $this->runArtisanCommand(['up']);
    }

    private function maybeRegenerateAppKey(UpdateLog $log, string $backupDir): void
    {
        if (! (bool) config('updates.regenerate_app_key', false)) {
            return;
        }

        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            throw new RuntimeException('Cannot regenerate APP_KEY because .env is missing.');
        }

        $contents = File::get($envPath);
        $pattern = '/^APP_KEY=.*$/m';
        $currentKey = '';

        if (preg_match($pattern, $contents, $matches) === 1) {
            $currentKey = (string) ($matches[0] ?? '');
        }

        if ($currentKey === '') {
            throw new RuntimeException('Cannot regenerate APP_KEY because the current key is missing.');
        }

        $backupPath = rtrim($backupDir, '/').'/app_key_backup_'.now()->format('Ymd_His').'.txt';
        File::put($backupPath, trim((string) preg_replace('/^APP_KEY=/', '', $currentKey)));
        File::chmod($backupPath, 0600);

        $generatedKey = 'base64:'.base64_encode(random_bytes(32));

        if (preg_match($pattern, $contents) === 1) {
            $contents = preg_replace($pattern, 'APP_KEY='.$generatedKey, $contents) ?? $contents;
        } else {
            $contents = rtrim($contents).PHP_EOL.PHP_EOL.'APP_KEY='.$generatedKey.PHP_EOL;
        }

        File::put($envPath, $contents);

        $log->update([
            'app_key_backup_path' => $backupPath,
            'app_key_rotated_at' => now(),
        ]);
    }

    private function restoreAppKeyIfAvailable(UpdateLog $log): void
    {
        $backupPath = (string) ($log->app_key_backup_path ?? '');

        if ($backupPath === '' || ! File::exists($backupPath)) {
            return;
        }

        $oldKey = trim((string) File::get($backupPath));

        if ($oldKey === '') {
            throw new RuntimeException('APP_KEY backup file is empty.');
        }

        $envPath = base_path('.env');
        if (! File::exists($envPath)) {
            throw new RuntimeException('Cannot restore APP_KEY because .env is missing.');
        }

        $contents = File::get($envPath);
        $pattern = '/^APP_KEY=.*$/m';

        if (preg_match($pattern, $contents) === 1) {
            $contents = preg_replace($pattern, 'APP_KEY='.$oldKey, $contents) ?? $contents;
        } else {
            $contents = rtrim($contents).PHP_EOL.PHP_EOL.'APP_KEY='.$oldKey.PHP_EOL;
        }

        File::put($envPath, $contents);
    }

    private function assertTrustedDownloadUrl(string $url): void
    {
        $allowedHosts = array_values(array_filter(array_map('strtolower', (array) config('updates.trusted_download_hosts', []))));

        if ($allowedHosts === []) {
            return;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        if ($host === '' || ! in_array($host, $allowedHosts, true)) {
            throw new RuntimeException('Untrusted download host: '.$host);
        }
    }
}
