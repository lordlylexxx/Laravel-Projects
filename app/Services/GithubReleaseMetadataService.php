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
        $payload = $this->getLatestReleasePayload();

        if ($payload === null) {
            return null;
        }

        return [
            'latest_version' => $payload['latest_version'],
            'release_notes' => $payload['release_notes'],
            'published_at' => $payload['published_at'],
        ];
    }

    /**
     * Resolve a browser-downloadable URL for the latest GitHub release package.
     *
     * Priority: optional exact/substring asset name match (CENTRAL_GITHUB_RELEASE_ASSET),
     * then first .zip release asset, then any first asset, then source archive
     * (github.com/owner/repo/archive/refs/tags/{tag}.zip).
     */
    public function resolveLatestReleasePackageDownloadUrl(): ?string
    {
        $payload = $this->getLatestReleasePayload();

        if ($payload === null) {
            return null;
        }

        $owner = $payload['owner'];
        $name = $payload['name'];
        $tagName = $payload['tag_name'];

        if ($tagName === '') {
            return null;
        }

        $assets = $payload['assets'];
        $preferred = trim((string) config('updates.github_release_asset', ''));

        if ($preferred !== '') {
            foreach ($assets as $asset) {
                if (strcasecmp($asset['name'], $preferred) === 0) {
                    return $asset['url'];
                }
            }
            foreach ($assets as $asset) {
                if (str_contains(strtolower($asset['name']), strtolower($preferred))) {
                    return $asset['url'];
                }
            }
        }

        foreach ($assets as $asset) {
            if (str_ends_with(strtolower($asset['name']), '.zip')) {
                return $asset['url'];
            }
        }

        if ($assets !== []) {
            return $assets[0]['url'];
        }

        return sprintf(
            'https://github.com/%s/%s/archive/refs/tags/%s.zip',
            rawurlencode($owner),
            rawurlencode($name),
            rawurlencode($tagName)
        );
    }

    public function resolveLatestReleaseChecksumUrl(): ?string
    {
        $payload = $this->getLatestReleasePayload();

        if ($payload === null) {
            return null;
        }

        $assets = $payload['assets'];
        $preferred = trim((string) config('updates.github_release_checksum_asset', ''));

        if ($preferred !== '') {
            foreach ($assets as $asset) {
                if (strcasecmp($asset['name'], $preferred) === 0) {
                    return $asset['url'];
                }
            }

            foreach ($assets as $asset) {
                if (str_contains(strtolower($asset['name']), strtolower($preferred))) {
                    return $asset['url'];
                }
            }
        }

        foreach ($assets as $asset) {
            $lowerName = strtolower($asset['name']);

            if (str_ends_with($lowerName, '.sha256') || str_ends_with($lowerName, '.checksum') || str_ends_with($lowerName, '.txt')) {
                return $asset['url'];
            }
        }

        return null;
    }

    /**
     * @return array{
     *     latest_version: string,
     *     release_notes: string,
     *     published_at: string|null,
     *     tag_name: string,
     *     owner: string,
     *     name: string,
     *     assets: list<array{name: string, url: string}>
     * }|null
     */
    private function getLatestReleasePayload(): ?array
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
            && isset($cached['latest_version'], $cached['release_notes'], $cached['tag_name'], $cached['owner'], $cached['name'])
            && is_string($cached['latest_version'])
            && is_string($cached['tag_name'])
            && is_string($cached['owner'])
            && is_string($cached['name'])
            && isset($cached['assets']) && is_array($cached['assets'])) {
            return [
                'latest_version' => $cached['latest_version'],
                'release_notes' => (string) $cached['release_notes'],
                'published_at' => isset($cached['published_at']) && is_string($cached['published_at'])
                    ? $cached['published_at']
                    : null,
                'tag_name' => $cached['tag_name'],
                'owner' => $cached['owner'],
                'name' => $cached['name'],
                'assets' => $this->normalizeAssetsList($cached['assets']),
            ];
        }

        $fresh = $this->requestLatest($owner, $name);

        if ($fresh !== null) {
            Cache::put($cacheKey, $fresh, now()->addSeconds($ttl));
        }

        return $fresh;
    }

    /**
     * @param  list<mixed>  $raw
     * @return list<array{name: string, url: string}>
     */
    private function normalizeAssetsList(array $raw): array
    {
        $out = [];

        foreach ($raw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $assetName = (string) ($row['name'] ?? '');
            $url = (string) ($row['url'] ?? $row['browser_download_url'] ?? '');
            if ($assetName === '' || $url === '') {
                continue;
            }
            $out[] = ['name' => $assetName, 'url' => $url];
        }

        return $out;
    }

    /**
     * @return array{
     *     latest_version: string,
     *     release_notes: string,
     *     published_at: string|null,
     *     tag_name: string,
     *     owner: string,
     *     name: string,
     *     assets: list<array{name: string, url: string}>
     * }|null
     */
    private function requestLatest(string $owner, string $name): ?array
    {
        $url = sprintf('https://api.github.com/repos/%s/%s/releases/latest', rawurlencode($owner), rawurlencode($name));

        $token = (string) config('updates.github_token', '');

        $request = Http::connectTimeout(1)
            ->timeout(3)
            ->withHeaders([
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ])
            ->withUserAgent((string) config('app.name', 'Laravel').' (Central Update Check)');

        $caBundle = $this->resolveCaBundle();
        if ($caBundle !== null) {
            $request = $request->withOptions(['verify' => $caBundle]);
        }

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

        $tagName = (string) ($data['tag_name'] ?? '');

        if ($tagName === '') {
            $tagName = (string) ($data['name'] ?? '');
        }

        if ($tagName === '') {
            return null;
        }

        $latestVersion = $this->normalizeVersion($tagName);

        if ($latestVersion === '') {
            return null;
        }

        $publishedAt = $data['published_at'] ?? null;
        $assetsRaw = $data['assets'] ?? [];
        $assets = [];

        if (is_array($assetsRaw)) {
            foreach ($assetsRaw as $asset) {
                if (! is_array($asset)) {
                    continue;
                }
                $assetName = (string) ($asset['name'] ?? '');
                $browserUrl = (string) ($asset['browser_download_url'] ?? '');
                if ($assetName === '' || $browserUrl === '') {
                    continue;
                }
                $assets[] = ['name' => $assetName, 'url' => $browserUrl];
            }
        }

        return [
            'latest_version' => $latestVersion,
            'release_notes' => (string) ($data['body'] ?? ''),
            'published_at' => is_string($publishedAt) ? $publishedAt : null,
            'tag_name' => $tagName,
            'owner' => $owner,
            'name' => $name,
            'assets' => $assets,
        ];
    }

    private function resolveCaBundle(): ?string
    {
        foreach ([getenv('CURL_CA_BUNDLE'), ini_get('curl.cainfo'), ini_get('openssl.cafile')] as $candidate) {
            $candidate = (string) $candidate;
            if ($candidate !== '' && is_file($candidate)) {
                return $candidate;
            }
        }

        $bundled = base_path('storage/certs/cacert.pem');

        return is_file($bundled) ? $bundled : null;
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
