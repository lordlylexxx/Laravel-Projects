<?php

namespace App\Services;

use App\Models\AppRelease;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReleaseRegistryService
{
    /** @var array<string, ?Carbon> */
    private array $commitPublishedAtCache = [];

    public function syncFromGitHub(): array
    {
        $repo = (string) config('releases.github_repo');
        $token = (string) config('releases.github_token');

        if ($repo === '') {
            return ['synced' => 0, 'updated' => 0, 'skipped' => 0, 'error' => 'GitHub repository is not configured. Set GITHUB_REPO in .env.'];
        }

        $this->commitPublishedAtCache = [];
        $request = $this->buildGithubClient($token);

        try {
            $releases = $this->fetchGithubPaginated($request, "repos/{$repo}/releases");
        } catch (ConnectionException $exception) {
            Log::warning('GitHub release sync failed due to TLS/connection issue.', [
                'repo' => $repo,
                'error' => $exception->getMessage(),
            ]);

            return [
                'synced' => 0,
                'updated' => 0,
                'skipped' => 0,
                'error' => 'Unable to connect to GitHub over HTTPS. Check CA bundle/certificate settings (GITHUB_CA_BUNDLE or php.ini curl.cainfo).',
            ];
        }

        if ($releases === null) {
            return [
                'synced' => 0,
                'updated' => 0,
                'skipped' => 0,
                'error' => 'GitHub API request failed while listing releases. Check GITHUB_REPO, token scopes (repo/public_repo), and rate limits.',
            ];
        }

        $synced = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($releases as $releasePayload) {
            if (! is_array($releasePayload)) {
                $skipped++;

                continue;
            }

            $tag = trim((string) ($releasePayload['tag_name'] ?? ''));
            if ($tag === '') {
                $skipped++;

                continue;
            }

            $attributes = [
                'title' => (string) ($releasePayload['name'] ?? $tag),
                'changelog' => (string) ($releasePayload['body'] ?? ''),
                'release_url' => (string) ($releasePayload['html_url'] ?? ''),
                'published_at' => $this->parseDate($releasePayload['published_at'] ?? null),
                'is_stable' => ! ((bool) ($releasePayload['prerelease'] ?? false)),
                'synced_at' => now(),
            ];

            $model = AppRelease::query()->where('tag', $tag)->first();
            if ($model) {
                $model->fill($attributes);
                if ($model->isDirty()) {
                    $model->save();
                    $updated++;
                } else {
                    $skipped++;
                }
            } else {
                AppRelease::query()->create(array_merge($attributes, ['tag' => $tag]));
                $synced++;
            }
        }

        // Tags exist without a GitHub "Release" object; /releases omits them. Import missing tags so sync works after `git tag` + push.
        try {
            $tags = $this->fetchGithubPaginated($request, "repos/{$repo}/tags");
        } catch (ConnectionException $exception) {
            Log::warning('GitHub tag sync failed due to TLS/connection issue.', [
                'repo' => $repo,
                'error' => $exception->getMessage(),
            ]);

            return array_merge(compact('synced', 'updated', 'skipped'), [
                'error' => 'Releases synced, but listing tags failed (connection). Partial sync.',
            ]);
        }

        if ($tags === null) {
            return array_merge(compact('synced', 'updated', 'skipped'), [
                'error' => 'Releases synced, but GitHub failed while listing tags (HTTP error). Tag-only versions may be missing.',
            ]);
        }

        foreach ($tags as $tagPayload) {
            if (! is_array($tagPayload)) {
                $skipped++;

                continue;
            }

            $tag = trim((string) ($tagPayload['name'] ?? ''));
            if ($tag === '') {
                $skipped++;

                continue;
            }

            if (AppRelease::query()->where('tag', $tag)->exists()) {
                $skipped++;

                continue;
            }

            $publishedAt = $this->resolveCommitPublishedAt($request, $tagPayload);
            $attributes = [
                'title' => $tag,
                'changelog' => '',
                'release_url' => "https://github.com/{$repo}/tree/{$tag}",
                'published_at' => $publishedAt,
                'is_stable' => ! $this->tagLooksLikePrerelease($tag),
                'synced_at' => now(),
            ];

            AppRelease::query()->create(array_merge($attributes, ['tag' => $tag]));
            $synced++;
        }

        return compact('synced', 'updated', 'skipped');
    }

    public function getLatestStableRelease(): ?AppRelease
    {
        return AppRelease::query()
            ->where('is_stable', true)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->first();
    }

    public function markAsRequired(int $releaseId): ?AppRelease
    {
        $release = AppRelease::query()->find($releaseId);
        if (! $release) {
            return null;
        }

        $release->update(['is_required' => true]);

        return $release->fresh();
    }

    private function buildGithubClient(string $token): PendingRequest
    {
        $request = Http::acceptJson()
            ->timeout(25)
            ->withHeaders([
                'User-Agent' => 'Laravel-Impastay-ReleaseSync',
            ])
            ->withOptions([
                'verify' => $this->resolveTlsVerifyOption(),
            ]);

        if ($token !== '') {
            $request = $request->withToken($token);
        }

        return $request;
    }

    /**
     * @return list<mixed>|null null when the first page returns a non-success HTTP status
     */
    private function fetchGithubPaginated(PendingRequest $request, string $path): ?array
    {
        $items = [];
        $page = 1;

        do {
            try {
                $response = $request->get("https://api.github.com/{$path}", [
                    'per_page' => 100,
                    'page' => $page,
                ]);
            } catch (ConnectionException $exception) {
                throw $exception;
            }

            if (! $response->ok()) {
                Log::warning('GitHub API paginated request failed.', [
                    'path' => $path,
                    'page' => $page,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return $page === 1 ? null : $items;
            }

            $batch = $response->json();
            if (! is_array($batch)) {
                break;
            }

            if ($batch === []) {
                break;
            }

            foreach ($batch as $row) {
                $items[] = $row;
            }

            if (count($batch) < 100) {
                break;
            }

            $page++;
        } while (true);

        return $items;
    }

    private function resolveCommitPublishedAt(PendingRequest $request, array $tagPayload): ?Carbon
    {
        $sha = isset($tagPayload['commit']['sha']) && is_string($tagPayload['commit']['sha'])
            ? $tagPayload['commit']['sha']
            : '';
        if ($sha === '') {
            return null;
        }

        if (array_key_exists($sha, $this->commitPublishedAtCache)) {
            return $this->commitPublishedAtCache[$sha];
        }

        $commitUrl = isset($tagPayload['commit']['url']) && is_string($tagPayload['commit']['url'])
            ? $tagPayload['commit']['url']
            : '';
        if ($commitUrl === '') {
            $this->commitPublishedAtCache[$sha] = null;

            return null;
        }

        try {
            $response = $request->get($commitUrl);
        } catch (ConnectionException $exception) {
            Log::warning('GitHub commit lookup failed during tag sync.', [
                'sha' => $sha,
                'error' => $exception->getMessage(),
            ]);
            $this->commitPublishedAtCache[$sha] = null;

            return null;
        }

        if (! $response->ok()) {
            $this->commitPublishedAtCache[$sha] = null;

            return null;
        }

        $json = $response->json();
        if (! is_array($json)) {
            $this->commitPublishedAtCache[$sha] = null;

            return null;
        }

        $raw = data_get($json, 'commit.committer.date') ?? data_get($json, 'commit.author.date');
        $parsed = $this->parseDate(is_string($raw) ? $raw : null);
        $this->commitPublishedAtCache[$sha] = $parsed;

        return $parsed;
    }

    private function tagLooksLikePrerelease(string $tag): bool
    {
        $t = strtolower($tag);
        foreach (['-dev', '-alpha', '-beta', '-rc', '-preview', '-canary'] as $needle) {
            if (str_contains($t, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function resolveTlsVerifyOption(): bool|string
    {
        $verify = config('releases.github_ssl_verify', true);

        if (is_string($verify)) {
            $normalized = strtolower(trim($verify));
            if ($normalized === 'false' || $normalized === '0' || $normalized === 'off') {
                return false;
            }
            if ($normalized === 'true' || $normalized === '1' || $normalized === 'on') {
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
