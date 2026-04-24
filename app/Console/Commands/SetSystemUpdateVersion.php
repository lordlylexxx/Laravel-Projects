<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetSystemUpdateVersion extends Command
{
    protected $signature = 'system-updates:set-version
        {version : The new APP_RELEASE_VERSION (e.g. 1.0.5)}
        {--env= : Absolute path to the .env file. Defaults to the project .env}';

    protected $description = 'Update APP_RELEASE_VERSION in .env and refresh config cache so self-update reflects the new version.';

    public function handle(): int
    {
        $version = trim((string) $this->argument('version'));

        if ($version === '' || ! preg_match('/^[A-Za-z0-9._-]+$/', $version)) {
            $this->error('Invalid version. Use letters, numbers, dots, dashes or underscores (e.g. 1.0.5 or v1.0.5-dev).');

            return self::FAILURE;
        }

        $envPath = (string) ($this->option('env') ?: base_path('.env'));

        if (! File::exists($envPath)) {
            $this->error('.env file not found at: '.$envPath);

            return self::FAILURE;
        }

        $contents = File::get($envPath);
        $pattern = '/^APP_RELEASE_VERSION=.*$/m';
        $line = 'APP_RELEASE_VERSION='.$version;

        if (preg_match($pattern, $contents) === 1) {
            $contents = preg_replace($pattern, $line, $contents) ?? $contents;
        } else {
            $contents = rtrim($contents).PHP_EOL.$line.PHP_EOL;
        }

        File::put($envPath, $contents);

        $this->info('APP_RELEASE_VERSION set to '.$version.' in '.$envPath);

        $this->call('config:clear');

        $this->line('Current config:');
        $this->line('  config(updates.current_version) = '.(string) config('updates.current_version'));
        $this->line('  config(app.version)             = '.(string) config('app.version'));

        return self::SUCCESS;
    }
}
