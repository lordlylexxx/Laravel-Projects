<?php

namespace App\Services;

use App\Models\AppRelease;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReleaseRegistryService
{
    public function syncFromGitHub(): array
    {
        $repo = trim((string) config('releases.github_repo', ''));
        $token = trim((string) config('releases.github_token', ''));

        if ($repo === '') {
            return ['synced' => 0, 'updated' => 0, 'skipped' => 0, 'error' => 'GitHub repository is not configured.'];
        }

        $request = Http::acceptJson()
            ->timeout(30)
            ->withUserAgent((string) config('app.name', 'Laravel').' release-sync')
            ->withOptions([
                'verify' => $this->resolveTlsVerifyOption(),
            ]);
        if ($token !== '') {
            $request = $request->withToken($token);
        }

        try {
            $response = $request->get("https://api.github.com/repos/{$repo}/releases", [
                'per_page' => 100,
            ]);
        } catch (ConnectionException $exception) {
            $message = 'GitHub connection failed: '.$exception->getMessage();
            Log::warning('Failed to sync releases from GitHub.', [
                'repo' => $repo,
                'error' => $message,
            ]);
            return ['synced' => 0, 'updated' => 0, 'skipped' => 0, 'error' => $message];
        }

        if (! $response->ok()) {
            $message = "GitHub request failed with HTTP {$response->status()}.";
            Log::warning('Failed to sync releases from GitHub.', [
                'repo' => $repo,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['synced' => 0, 'updated' => 0, 'skipped' => 0, 'error' => $message];
        }

        $payloads = (array) $response->json();
        if ($payloads === []) {
            // Some environments/tokens intermittently return an empty list.
            // Fall back to the latest release endpoint so new tags are still discovered.
            try {
                $latestResponse = $request->get("https://api.github.com/repos/{$repo}/releases/latest");
                if ($latestResponse->ok()) {
                    $latestPayload = (array) $latestResponse->json();
                    if (($latestPayload['tag_name'] ?? '') !== '') {
                        $payloads = [$latestPayload];
                    }
                }
            } catch (\Throwable) {
                // Keep payloads empty and return zero counts below.
            }
        }

        $synced = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($payloads as $releasePayload) {
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
