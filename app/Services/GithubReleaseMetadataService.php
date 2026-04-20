<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GithubReleaseMetadataService
{
    /**
     * @return array{latest_version: string, release_notes: string, published_at: string|null}|null
     */
    public function fetchLatestReleaseMetadata(): ?array
    {
        $repo = trim((string) config('updates.github_repo', ''));

        if ($repo === '' || substr_count($repo, '/') !== 1) {
            return null;
        }

        [$owner, $name] = array_map('trim', explode('/', $repo, 2));

        if ($owner === '' || $name === '') {
            return null;
        }

        $ttl = (int) config('updates.github_cache_ttl', 300);

        if ($ttl < 60) {
            $ttl = 60;
        }

        $hasToken = (string) config('updates.github_token', '') !== '';
        $cacheKey = 'updates.github.latest.'.sha1($repo.'|'.($hasToken ? '1' : '0'));

        $cached = Cache::get($cacheKey);

        if (is_array($cached)
            && isset($cached['latest_version'], $cached['release_notes'])
            && is_string($cached['latest_version'])) {
            return [
                'latest_version' => $cached['latest_version'],
                'release_notes' => (string) $cached['release_notes'],
                'published_at' => isset($cached['published_at']) && is_string($cached['published_at'])
                    ? $cached['published_at']
                    : null,
            ];
        }

        $fresh = $this->requestLatest($owner, $name);

        if ($fresh !== null) {
            Cache::put($cacheKey, $fresh, now()->addSeconds($ttl));
        }

        return $fresh;
    }

    /**
     * @return array{latest_version: string, release_notes: string, published_at: string|null}|null
     */
    private function requestLatest(string $owner, string $name): ?array
    {
        $url = sprintf('https://api.github.com/repos/%s/%s/releases/latest', rawurlencode($owner), rawurlencode($name));

        $token = (string) config('updates.github_token', '');

        $request = Http::connectTimeout(2)
            ->timeout(8)
            ->withHeaders([
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ])
            ->withUserAgent((string) config('app.name', 'Laravel').' (Central Update Check)');

        if ($token !== '') {
            $request = $request->withToken($token);
        }

        try {
            $response = $request->get($url);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->ok()) {
            return null;
        }

        $data = $response->json();

        if (! is_array($data)) {
            return null;
        }

        $rawVersion = (string) ($data['tag_name'] ?? '');

        if ($rawVersion === '') {
            $rawVersion = (string) ($data['name'] ?? '');
        }

        if ($rawVersion === '') {
            return null;
        }

        $latestVersion = $this->normalizeVersion($rawVersion);

        if ($latestVersion === '') {
            return null;
        }

        $publishedAt = $data['published_at'] ?? null;

        return [
            'latest_version' => $latestVersion,
            'release_notes' => (string) ($data['body'] ?? ''),
            'published_at' => is_string($publishedAt) ? $publishedAt : null,
        ];
    }

    private function normalizeVersion(string $tag): string
    {
        $tag = trim($tag);

        if ($tag === '') {
            return '';
        }

        if (str_starts_with(strtolower($tag), 'v')) {
            return substr($tag, 1);
        }

        return $tag;
    }
}
