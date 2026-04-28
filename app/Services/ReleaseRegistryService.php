<?php

namespace App\Services;

use App\Models\AppRelease;
use App\Support\SemanticVersion;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReleaseRegistryService
{
    /**
     * @return array{synced: int, updated: int, skipped: int, error?: string}
     */
    public function syncFromGitHub(): array
    {
        try {
            return $this->syncFromGitHubUnsafe();
        } catch (ConnectionException $exception) {
            Log::warning('GitHub release sync failed due to TLS/connection issue.', [
                'error' => $exception->getMessage(),
            ]);

            return [
                'synced' => 0,
                'updated' => 0,
                'skipped' => 0,
                'error' => 'Unable to connect to GitHub over HTTPS. Check CA bundle/certificate settings (GITHUB_CA_BUNDLE or php.ini curl.cainfo).',
            ];
        }
    }

    /**
     * @return array{synced: int, updated: int, skipped: int, error?: string}
     */
    private function syncFromGitHubUnsafe(): array
    {
        $repo = $this->normalizeGithubRepo((string) config('releases.github_repo'));
        $token = (string) config('releases.github_token');

        if ($repo === '') {
            return [
                'synced' => 0,
                'updated' => 0,
                'skipped' => 0,
                'error' => 'GitHub repository is not configured. Set GITHUB_REPO or CENTRAL_GITHUB_REPO to owner/repository in .env.',
            ];
        }

        if (substr_count($repo, '/') !== 1) {
            return [
                'synced' => 0,
                'updated' => 0,
                'skipped' => 0,
                'error' => 'Invalid repository format. Use owner/repository (no extra path segments).',
            ];
        }

        [$owner, $name] = array_map('trim', explode('/', $repo, 2));

        if ($owner === '' || $name === '') {
            return [
                'synced' => 0,
                'updated' => 0,
                'skipped' => 0,
                'error' => 'Invalid repository format. Set both owner and repository name.',
            ];
        }

        $baseRequest = Http::acceptJson()
            ->timeout(45)
            ->withHeaders([
                'User-Agent' => (string) config('app.name', 'Laravel').' (ReleaseRegistryService)',
                'Accept' => 'application/vnd.github+json',
            ])
            ->withOptions([
                'verify' => $this->resolveTlsVerifyOption(),
            ]);

        $useAuthToken = $token !== '';
        $perPage = (int) config('releases.github_releases_per_page', 100);
        $page = 1;
        $synced = 0;
        $updated = 0;
        $skipped = 0;
        /** @var array<string, true> $tagsSeen */
        $tagsSeen = [];

        do {
            $client = $useAuthToken ? $baseRequest->withToken($token) : $baseRequest;

            $response = $client->get(
                sprintf(
                    'https://api.github.com/repos/%s/%s/releases',
                    rawurlencode($owner),
                    rawurlencode($name)
                ),
                [
                    'per_page' => $perPage,
                    'page' => $page,
                ]
            );

            /*
             * Mis-scoped, expired, or invalid tokens sometimes yield HTTP 200 with an empty
             * JSON array for public repos, while the same request without Authorization returns
             * releases. Fall back to unauthenticated requests for the rest of this sync.
             */
            if ($useAuthToken && $page === 1 && $response->ok()) {
                $probe = $response->json();
                if (is_array($probe) && $probe === []) {
                    $anonymous = $baseRequest->get(
                        sprintf(
                            'https://api.github.com/repos/%s/%s/releases',
                            rawurlencode($owner),
                            rawurlencode($name)
                        ),
                        [
                            'per_page' => $perPage,
                            'page' => 1,
                        ]
                    );

                    if ($anonymous->ok()) {
                        $anonPayload = $anonymous->json();
                        if (is_array($anonPayload) && $anonPayload !== []) {
                            Log::warning('GitHub releases sync: authenticated request returned no releases, but unauthenticated request succeeded. Using unauthenticated API for this sync; fix or remove GITHUB_TOKEN / CENTRAL_GITHUB_TOKEN if the token is invalid or lacks repo scope.', [
                                'repo' => $repo,
                            ]);
                            $useAuthToken = false;
                            $response = $anonymous;
                        }
                    }
                }
            }

            if (! $response->ok()) {
                Log::warning('Failed to sync releases from GitHub.', [
                    'repo' => $repo,
                    'page' => $page,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'synced' => 0,
                    'updated' => 0,
                    'skipped' => 0,
                    'error' => sprintf(
                        'GitHub API returned HTTP %d. Check repository name, visibility, and token or rate limits.',
                        $response->status()
                    ),
                ];
            }

            $pagePayload = $response->json();

            if (! is_array($pagePayload)) {
                return [
                    'synced' => 0,
                    'updated' => 0,
                    'skipped' => 0,
                    'error' => 'GitHub returned an unexpected response body.',
                ];
            }

            if ($pagePayload === []) {
                break;
            }

            foreach ($pagePayload as $releasePayload) {
                if (! is_array($releasePayload)) {
                    $skipped++;

                    continue;
                }

                if ((bool) ($releasePayload['draft'] ?? false)) {
                    $skipped++;

                    continue;
                }

                $tag = trim((string) ($releasePayload['tag_name'] ?? ''));
                if ($tag === '') {
                    $skipped++;

                    continue;
                }

                $tagsSeen[$tag] = true;

                $this->upsertReleaseFromGithubPayload(
                    $releasePayload,
                    $tag,
                    $synced,
                    $updated,
                    $skipped
                );
            }

            $page++;
        } while (count($pagePayload) === $perPage);

        if (config('releases.sync_git_tags', true)) {
            $this->importTagsNotCoveredByReleases(
                $owner,
                $name,
                $baseRequest,
                $useAuthToken,
                $token,
                $tagsSeen,
                $synced,
                $updated,
                $skipped
            );
        }

        return compact('synced', 'updated', 'skipped');
    }

    public function getLatestStableRelease(): ?AppRelease
    {
        $stable = AppRelease::query()->where('is_stable', true)->get();

        return SemanticVersion::newestRelease($stable);
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

    /**
     * @param  array<string, true>  $tagsSeen
     */
    private function importTagsNotCoveredByReleases(
        string $owner,
        string $name,
        PendingRequest $baseRequest,
        bool $useAuthToken,
        string $token,
        array $tagsSeen,
        int &$synced,
        int &$updated,
        int &$skipped
    ): void {
        $perPage = (int) config('releases.github_releases_per_page', 100);
        $page = 1;
        $triedAuthFallback = false;

        do {
            $client = ($useAuthToken && $token !== '') ? $baseRequest->withToken($token) : $baseRequest;

            $response = $client->get(
                sprintf(
                    'https://api.github.com/repos/%s/%s/tags',
                    rawurlencode($owner),
                    rawurlencode($name)
                ),
                [
                    'per_page' => $perPage,
                    'page' => $page,
                ]
            );

            if ($useAuthToken && $page === 1 && $response->ok()) {
                $probe = $response->json();
                if (is_array($probe) && $probe === [] && $token !== '') {
                    $anonymous = $baseRequest->get(
                        sprintf(
                            'https://api.github.com/repos/%s/%s/tags',
                            rawurlencode($owner),
                            rawurlencode($name)
                        ),
                        [
                            'per_page' => $perPage,
                            'page' => 1,
                        ]
                    );

                    if ($anonymous->ok()) {
                        $anonPayload = $anonymous->json();
                        if (is_array($anonPayload) && $anonPayload !== []) {
                            if (! $triedAuthFallback) {
                                Log::warning('GitHub tags sync: authenticated request returned no tags, but unauthenticated request succeeded. Using unauthenticated API for tag import.', [
                                    'repo' => $owner.'/'.$name,
                                ]);
                                $triedAuthFallback = true;
                            }
                            $useAuthToken = false;
                            $response = $anonymous;
                        }
                    }
                }
            }

            if (! $response->ok()) {
                Log::warning('GitHub tag list request failed.', [
                    'repo' => $owner.'/'.$name,
                    'page' => $page,
                    'status' => $response->status(),
                ]);

                return;
            }

            $rows = $response->json();
            if (! is_array($rows) || $rows === []) {
                break;
            }

            foreach ($rows as $row) {
                if (! is_array($row)) {
                    $skipped++;

                    continue;
                }

                $tag = trim((string) ($row['name'] ?? ''));
                if ($tag === '') {
                    $skipped++;

                    continue;
                }

                if (isset($tagsSeen[$tag])) {
                    continue;
                }

                $tagsSeen[$tag] = true;

                $sha = trim((string) ($row['commit']['sha'] ?? ''));
                $publishedAt = $sha !== ''
                    ? $this->fetchCommitCommitterDate($owner, $name, $sha, $baseRequest, $useAuthToken, $token)
                    : null;

                $synthetic = [
                    'name' => $tag,
                    'body' => '',
                    'html_url' => sprintf(
                        'https://github.com/%s/%s/tree/%s',
                        $owner,
                        $name,
                        rawurlencode($tag)
                    ),
                    'published_at' => $publishedAt?->toIso8601String(),
                    'prerelease' => $this->tagLooksLikePrerelease($tag),
                ];

                $this->upsertReleaseFromGithubPayload($synthetic, $tag, $synced, $updated, $skipped);
            }

            $page++;
        } while (count($rows) === $perPage);
    }

    private function fetchCommitCommitterDate(
        string $owner,
        string $name,
        string $sha,
        PendingRequest $baseRequest,
        bool $useAuthToken,
        string $token
    ): ?Carbon {
        $client = ($useAuthToken && $token !== '') ? $baseRequest->withToken($token) : $baseRequest;

        $response = $client->get(sprintf(
            'https://api.github.com/repos/%s/%s/commits/%s',
            rawurlencode($owner),
            rawurlencode($name),
            rawurlencode($sha)
        ));

        if (! $response->ok()) {
            return null;
        }

        $data = $response->json();
        if (! is_array($data)) {
            return null;
        }

        $date = $data['commit']['committer']['date'] ?? $data['commit']['author']['date'] ?? null;

        return $this->parseDate(is_string($date) ? $date : null);
    }

    private function tagLooksLikePrerelease(string $tag): bool
    {
        return (bool) preg_match('/-(dev|alpha|beta|rc|pre|snapshot)([.\d]|$)/i', $tag)
            || str_contains(strtolower($tag), 'nightly');
    }

    /**
     * @param  array<string, mixed>  $releasePayload
     */
    private function upsertReleaseFromGithubPayload(
        array $releasePayload,
        string $tag,
        int &$synced,
        int &$updated,
        int &$skipped
    ): void {
        $attributes = [
            'title' => (string) ($releasePayload['name'] ?? $tag),
            'changelog' => (string) ($releasePayload['body'] ?? ''),
            'release_url' => (string) ($releasePayload['html_url'] ?? ''),
            'published_at' => $this->parseDate($releasePayload['published_at'] ?? null),
            'is_stable' => ! ((bool) ($releasePayload['prerelease'] ?? false)),
        ];

        $model = AppRelease::query()->where('tag', $tag)->first();
        if ($model) {
            $model->fill($attributes);
            if ($model->isDirty()) {
                $model->synced_at = now();
                $model->save();
                $updated++;
            } else {
                $skipped++;
            }
        } else {
            AppRelease::query()->create(array_merge($attributes, [
                'tag' => $tag,
                'synced_at' => now(),
            ]));
            $synced++;
        }
    }

    private function normalizeGithubRepo(string $repo): string
    {
        $repo = trim($repo);
        if ($repo === '') {
            return '';
        }

        $repo = (string) preg_replace('#^https?://github\.com/#i', '', $repo);
        $repo = (string) preg_replace('#^github\.com/#i', '', $repo);
        $repo = rtrim($repo, '/');
        if (str_ends_with(strtolower($repo), '.git')) {
            $repo = substr($repo, 0, -4);
        }

        return trim($repo);
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
