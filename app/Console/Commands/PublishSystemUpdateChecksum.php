<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishSystemUpdateChecksum extends Command
{
    protected $signature = 'system-updates:publish-checksum
        {--package= : Absolute package path. Defaults to storage/app/public/updates/{CENTRAL_UPDATE_PACKAGE_FILENAME}}
        {--output= : Absolute checksum output path. Defaults to package filename + .sha256}';

    protected $description = 'Generate a SHA-256 checksum file for the central update package.';

    public function handle(): int
    {
        $packagePath = $this->resolvePackagePath();

        if (! File::exists($packagePath) || ! File::isFile($packagePath)) {
            $this->error('Update package not found: '.$packagePath);

            return self::FAILURE;
        }

        $checksum = hash_file('sha256', $packagePath);

        if (! is_string($checksum) || $checksum === '') {
            $this->error('Unable to compute SHA-256 checksum for package.');

            return self::FAILURE;
        }

        $outputPath = $this->resolveOutputPath($packagePath);
        File::ensureDirectoryExists(dirname($outputPath));

        $line = sprintf("%s  %s\n", $checksum, basename($packagePath));
        File::put($outputPath, $line);

        $this->info('Checksum file published.');
        $this->line('Package: '.$packagePath);
        $this->line('Checksum: '.$checksum);
        $this->line('Output: '.$outputPath);

        return self::SUCCESS;
    }

    private function resolvePackagePath(): string
    {
        $provided = trim((string) $this->option('package'));

        if ($provided !== '') {
            return $provided;
        }

        $filename = (string) config('updates.package_filename', 'latest-update.zip');

        return storage_path('app/public/updates/'.$filename);
    }

    private function resolveOutputPath(string $packagePath): string
    {
        $provided = trim((string) $this->option('output'));

        if ($provided !== '') {
            return $provided;
        }

        $configured = trim((string) config('updates.checksum_filename', ''));

        if ($configured !== '') {
            return storage_path('app/public/updates/'.$configured);
        }

        return $packagePath.'.sha256';
    }
}
