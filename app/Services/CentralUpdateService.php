<?php

namespace App\Services;

use App\Services\GithubReleaseMetadataService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request as RequestFacade;

class CentralUpdateService
{
    public function checkForUpdates(string $currentVersion): ?array
    {
        $baseUrl = rtrim((string) config('updates.central_base_url', ''), '/');

        if ($baseUrl === '') {
            return null;
        }

        $cacheKey = $this->updateCacheKey($currentVersion);
        $cacheTtl = (int) config('updates.check_cache_ttl', 300);

        $payload = Cache::remember($cacheKey, now()->addSeconds($cacheTtl), function () use ($baseUrl, $currentVersion): ?array {
            // Avoid self-HTTP on single-threaded dev servers (php artisan serve).
            // If the configured central URL points to the same app, resolve locally.
            if ($this->isSelfRequest($baseUrl)) {
                return $this->localPayload($currentVersion, $baseUrl);
            }

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
                    'checksum_url' => (string) ($data['checksum_url'] ?? ''),
                    'unavailable' => false,
                ];
            } catch (\Throwable $exception) {
                return $this->githubFallbackPayload($currentVersion, $baseUrl, true);
            }
        });

        if (is_array($payload) && (bool) ($payload['unavailable'] ?? false)) {
            Cache::forget($cacheKey);
        }

        return $payload;
    }

    public function getCachedUpdatePayload(string $currentVersion): ?array
    {
        $cacheKey = $this->updateCacheKey($currentVersion);
        $cached = Cache::get($cacheKey);

        if (is_array($cached) && (bool) ($cached['unavailable'] ?? false)) {
            Cache::forget($cacheKey);

            return null;
        }

        return $cached;
    }

    private function updateCacheKey(string $currentVersion): string
    {
        return 'updates.check.' . sha1(rtrim((string) config('updates.central_base_url', ''), '/'). '|' . $currentVersion . '|' . (string) config('updates.channel_token', ''));
    }

    private function isSelfRequest(string $baseUrl): bool
    {
        $host = parse_url($baseUrl, PHP_URL_HOST);
        if (! is_string($host) || $host === '') {
            return false;
        }

        $host = strtolower($host);
        $localHosts = ['localhost', '127.0.0.1', '::1'];
        $centralDomain = strtolower((string) env('CENTRAL_DOMAIN', ''));
        if ($centralDomain !== '') {
            $localHosts[] = $centralDomain;
        }

        try {
            $currentHost = strtolower((string) RequestFacade::getHost());
        } catch (\Throwable) {
            $currentHost = '';
        }

        return in_array($host, $localHosts, true)
            || ($currentHost !== '' && $host === $currentHost);
    }

    private function localPayload(string $currentVersion, string $baseUrl): array
    {
        $token = (string) config('updates.channel_token', '');
        $tokenQuery = $token !== '' ? '?token='.urlencode($token) : '';

        $github = app(GithubReleaseMetadataService::class)->fetchLatestReleaseMetadata();

        if ($github !== null) {
            $latestVersion = (string) ($github['latest_version'] ?? $currentVersion);
            $releaseNotes = (string) ($github['release_notes'] ?? '');
            $publishedAt = $github['published_at'] ?? null;
            $githubPackageUrl = app(GithubReleaseMetadataService::class)->resolveLatestReleasePackageDownloadUrl();
            $githubChecksumUrl = app(GithubReleaseMetadataService::class)->resolveLatestReleaseChecksumUrl();
            $downloadUrl = $githubPackageUrl ?? $baseUrl.'/system-updates/download'.$tokenQuery;
            $checksumUrl = $githubPackageUrl !== null
                ? ($githubChecksumUrl ?? '')
                : $baseUrl.'/system-updates/checksum'.$tokenQuery;
        } else {
            $latestVersion = (string) config('updates.latest_version', $currentVersion);
            $releaseNotes = (string) config('updates.release_notes', '');
            $publishedAt = config('updates.published_at');
            $downloadUrl = $baseUrl.'/system-updates/download'.$tokenQuery;
            $checksumUrl = $baseUrl.'/system-updates/checksum'.$tokenQuery;
        }

        return [
            'has_update' => version_compare($latestVersion, $currentVersion, '>'),
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion,
            'release_notes' => $releaseNotes,
            'published_at' => $publishedAt,
            'download_url' => $downloadUrl,
            'checksum_url' => $checksumUrl,
            'unavailable' => false,
        ];
    }

    private function githubFallbackPayload(string $currentVersion, string $baseUrl, bool $markUnavailable): array
    {
        $token = (string) config('updates.channel_token', '');
        $tokenQuery = $token !== '' ? '?token='.urlencode($token) : '';

        $github = app(GithubReleaseMetadataService::class)->fetchLatestReleaseMetadata();

        if ($github !== null) {
            $latestVersion = (string) ($github['latest_version'] ?? $currentVersion);
            $githubPackageUrl = app(GithubReleaseMetadataService::class)->resolveLatestReleasePackageDownloadUrl();
            $githubChecksumUrl = app(GithubReleaseMetadataService::class)->resolveLatestReleaseChecksumUrl();
            $fallbackDownloadUrl = $baseUrl.'/system-updates/download'.$tokenQuery;
            $fallbackChecksumUrl = $baseUrl.'/system-updates/checksum'.$tokenQuery;

            return [
                'has_update' => version_compare($latestVersion, $currentVersion, '>'),
                'current_version' => $currentVersion,
                'latest_version' => $latestVersion,
                'release_notes' => (string) ($github['release_notes'] ?? ''),
                'published_at' => $github['published_at'] ?? null,
                'download_url' => $githubPackageUrl ?? $fallbackDownloadUrl,
                'checksum_url' => $githubPackageUrl !== null
                    ? ($githubChecksumUrl ?? '')
                    : $fallbackChecksumUrl,
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
