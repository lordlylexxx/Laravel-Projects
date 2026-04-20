<?php

namespace App\Services;

use App\Services\GithubReleaseMetadataService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CentralUpdateService
{
    public function checkForUpdates(string $currentVersion): ?array
    {
        $baseUrl = rtrim((string) config('updates.central_base_url', ''), '/');

        if ($baseUrl === '') {
            return null;
        }

        $cacheKey = 'updates.check.' . sha1($baseUrl . '|' . $currentVersion . '|' . (string) config('updates.channel_token', ''));

        return Cache::remember($cacheKey, now()->addSeconds(45), function () use ($baseUrl, $currentVersion): ?array {
            $params = [
                'version' => $currentVersion,
            ];

            $token = (string) config('updates.channel_token', '');

            if ($token !== '') {
                $params['token'] = $token;
            }

            try {
                $response = Http::connectTimeout(1)
                    ->timeout(2)
                    ->acceptJson()
                    ->get($baseUrl . '/system-updates/check', $params);

                if (! $response->ok()) {
                    return $this->githubFallbackPayload($currentVersion, $baseUrl, true);
                }

                $data = $response->json();

                if (! is_array($data)) {
                    return null;
                }

                return [
                    'has_update' => (bool) ($data['has_update'] ?? false),
                    'current_version' => (string) ($data['current_version'] ?? $currentVersion),
                    'latest_version' => (string) ($data['latest_version'] ?? $currentVersion),
                    'release_notes' => (string) ($data['release_notes'] ?? ''),
                    'published_at' => $data['published_at'] ?? null,
                    'download_url' => (string) ($data['download_url'] ?? ''),
                    'unavailable' => false,
                ];
            } catch (\Throwable $exception) {
                return $this->githubFallbackPayload($currentVersion, $baseUrl, true);
            }
        });
    }

    private function githubFallbackPayload(string $currentVersion, string $baseUrl, bool $markUnavailable): array
    {
        $github = app(GithubReleaseMetadataService::class)->fetchLatestReleaseMetadata();

        if ($github !== null) {
            $latestVersion = (string) ($github['latest_version'] ?? $currentVersion);

            return [
                'has_update' => version_compare($latestVersion, $currentVersion, '>'),
                'current_version' => $currentVersion,
                'latest_version' => $latestVersion,
                'release_notes' => (string) ($github['release_notes'] ?? ''),
                'published_at' => $github['published_at'] ?? null,
                'download_url' => $baseUrl . '/system-updates/download',
                'unavailable' => false,
                'message' => $markUnavailable ? 'Central channel unavailable. Showing latest release from GitHub metadata.' : '',
            ];
        }

        return [
            'has_update' => false,
            'unavailable' => true,
            'message' => 'Unable to check updates at the moment.',
        ];
    }
}
