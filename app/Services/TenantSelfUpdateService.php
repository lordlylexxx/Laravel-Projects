<?php

namespace App\Services;

use App\Models\AppRelease;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use ZipArchive;

class TenantSelfUpdateService
{
    public function __construct(
        private readonly TenantUpdateService $tenantUpdateService
    ) {}

    public function applyUpdate(int $tenantId, int $releaseId): array
    {
        $tenant = Tenant::query()->find($tenantId);
        if (! $tenant) {
            return ['ok' => false, 'message' => 'Tenant not found.'];
        }

        $release = AppRelease::query()->find($releaseId);
        if (! $release) {
            return ['ok' => false, 'message' => 'Release not found.'];
        }

        $current = $this->tenantUpdateService->getCurrentRelease($tenantId)?->release;
        if ($current && $current->published_at && $release->published_at && $release->published_at->lte($current->published_at)) {
            return ['ok' => false, 'message' => "Refusing to apply {$release->tag} because it is not newer than current {$current->tag}."];
        }

        try {
            $downloadUrl = $this->resolveDownloadUrl($release);
            $this->downloadAndApplyRelease($downloadUrl, (string) $release->tag);

            $exit = Artisan::call('tenants:migrate', ['tenantId' => (string) $tenantId]);
            if ($exit !== 0) {
                $message = 'Tenant migration failed while applying update.';
                $this->tenantUpdateService->markAsFailed($tenantId, $releaseId, $message);
                return ['ok' => false, 'message' => $message];
            }

            $this->tenantUpdateService->markAsUpdated($tenantId, $releaseId);
            return ['ok' => true, 'message' => "Update {$release->tag} downloaded, installed, and applied successfully."];
        } catch (\Throwable $exception) {
            Log::error('Tenant self-update failed.', [
                'tenant_id' => $tenantId,
                'release_id' => $releaseId,
                'error' => $exception->getMessage(),
            ]);

            $this->tenantUpdateService->markAsFailed($tenantId, $releaseId, $exception->getMessage());

            return ['ok' => false, 'message' => 'Update failed: '.$exception->getMessage()];
        }
    }

    private function resolveDownloadUrl(AppRelease $release): string
    {
        $repo = trim((string) config('releases.github_repo', ''));
        $tag = trim((string) $release->tag);

        if ($repo === '' || $tag === '' || substr_count($repo, '/') !== 1) {
            throw new \RuntimeException('GitHub repository or release tag is not configured correctly.');
        }

        return "https://api.github.com/repos/{$repo}/zipball/{$tag}";
    }

    private function downloadAndApplyRelease(string $downloadUrl, string $tag): void
    {
        $tempRoot = storage_path('app/updates/tmp/'.uniqid('tenant-update-', true));
        $zipPath = $tempRoot.'/release.zip';
        $extractPath = $tempRoot.'/extract';

        File::ensureDirectoryExists($extractPath);

        try {
            $request = Http::timeout(120)
                ->withUserAgent((string) config('app.name', 'Laravel').' self-updater')
                ->accept('application/zip')
                ->withOptions([
                    'verify' => $this->resolveTlsVerifyOption(),
                ]);

            $token = trim((string) config('releases.github_token', ''));
            if ($token !== '') {
                $request = $request->withToken($token);
            }

            $response = $request->sink($zipPath)->get($downloadUrl);
            if (! $response->successful()) {
                throw new \RuntimeException("Failed to download release package for {$tag} (HTTP {$response->status()}).");
            }

            $zip = new ZipArchive;
            if ($zip->open($zipPath) !== true) {
                throw new \RuntimeException('Downloaded update package is invalid or cannot be opened.');
            }

            $zip->extractTo($extractPath);
            $zip->close();

            $roots = array_values(array_filter((array) File::directories($extractPath)));
            if ($roots === []) {
                throw new \RuntimeException('No extracted release contents were found.');
            }

            $sourceRoot = $roots[0];
            $this->copyReleaseTree($sourceRoot, base_path());
            $this->runPostInstallCommands();
        } finally {
            File::deleteDirectory($tempRoot);
        }
    }

    private function copyReleaseTree(string $sourceRoot, string $targetRoot): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceRoot, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $sourcePath = (string) $item->getPathname();
            $relativePath = ltrim(str_replace('\\', '/', substr($sourcePath, strlen($sourceRoot))), '/');

            if ($relativePath === '' || $this->shouldSkipPath($relativePath)) {
                continue;
            }

            $destination = $targetRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

            if ($item->isDir()) {
                File::ensureDirectoryExists($destination);
                continue;
            }

            File::ensureDirectoryExists(dirname($destination));
            File::copy($sourcePath, $destination);
        }
    }

    private function shouldSkipPath(string $relativePath): bool
    {
        $blocked = [
            '.env',
            '.git',
            'node_modules',
            'vendor',
            'storage',
            'bootstrap/cache',
            'public/storage',
            // Protect critical update flow files from being downgraded/overwritten by release packages.
            'config/releases.php',
            'app/Services/TenantSelfUpdateService.php',
            'app/Http/Controllers/Tenant/SettingsController.php',
            'app/Http/Controllers/Admin/ReleaseController.php',
            'app/Services/ReleaseRegistryService.php',
            'resources/views/admin/releases/index.blade.php',
            'resources/views/owner/settings/updates.blade.php',
        ];

        foreach ($blocked as $prefix) {
            if ($relativePath === $prefix || str_starts_with($relativePath, $prefix.'/')) {
                return true;
            }
        }

        return false;
    }

    private function runPostInstallCommands(): void
    {
        $commands = [];
        $composerCommand = $this->resolveComposerCommand();
        if ($composerCommand !== null) {
            $commands[] = $composerCommand.' install --no-interaction --prefer-dist';
        } else {
            Log::warning('Composer executable not found during tenant self-update; skipping composer install.', [
                'base_path' => base_path(),
            ]);
        }

        $commands[] = 'php artisan migrate --force';
        $commands[] = 'php artisan optimize:clear';

        foreach ($commands as $command) {
            $result = Process::path(base_path())
                ->timeout(900)
                ->run($command);

            if (! $result->successful()) {
                throw new \RuntimeException("Update command failed: {$command}\n".$result->errorOutput());
            }
        }

        if (File::exists(base_path('package.json'))) {
            $npmInstall = Process::path(base_path())
                ->timeout(1200)
                ->run('npm install');
            if (! $npmInstall->successful()) {
                throw new \RuntimeException("Update command failed: npm install\n".$npmInstall->errorOutput());
            }

            $npmBuild = Process::path(base_path())
                ->timeout(1200)
                ->run('npm run build');
            if (! $npmBuild->successful()) {
                throw new \RuntimeException("Update command failed: npm run build\n".$npmBuild->errorOutput());
            }
        }
    }

    private function resolveComposerCommand(): ?string
    {
        if (File::exists(base_path('composer.phar'))) {
            return 'php composer.phar';
        }

        $composerBat = env('APPDATA')
            ? rtrim((string) env('APPDATA'), '\\/').DIRECTORY_SEPARATOR.'ComposerSetup'.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'composer.bat'
            : '';
        if ($composerBat !== '' && File::exists($composerBat)) {
            return '"'.$composerBat.'"';
        }

        $whereResult = Process::path(base_path())
            ->timeout(20)
            ->run('where composer');
        if ($whereResult->successful()) {
            $firstPath = trim((string) preg_split('/\r\n|\r|\n/', trim($whereResult->output()))[0] ?? '');
            if ($firstPath !== '') {
                return '"'.$firstPath.'"';
            }
        }

        return null;
    }

    private function resolveTlsVerifyOption(): bool|string
    {
        $verify = config('releases.github_ssl_verify', true);

        if (is_string($verify)) {
            $normalized = strtolower(trim($verify));
            if (in_array($normalized, ['false', '0', 'off'], true)) {
                return false;
            }
            if (in_array($normalized, ['true', '1', 'on'], true)) {
                $verify = true;
            }
        }

        if ($verify === false) {
            return false;
        }

        $bundlePath = (string) config('releases.github_ca_bundle', '');
        if ($bundlePath !== '' && is_file($bundlePath)) {
            return $bundlePath;
        }

        return true;
    }
}
