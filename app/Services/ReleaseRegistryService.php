<?php

namespace App\Services;

use App\Models\AppRelease;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReleaseRegistryService
{
    public function syncFromGitHub(): array
    {
        $repo = (string) config('releases.github_repo');
        $token = (string) config('releases.github_token');

        if ($repo === '') {
            return ['synced' => 0, 'updated' => 0, 'skipped' => 0];
        }

        $request = Http::acceptJson()->timeout(20);
        if ($token !== '') {
            $request = $request->withToken($token);
        }

        $response = $request->get("https://api.github.com/repos/{$repo}/releases");
        if (! $response->ok()) {
            Log::warning('Failed to sync releases from GitHub.', [
                'repo' => $repo,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['synced' => 0, 'updated' => 0, 'skipped' => 0];
        }

        $synced = 0;
        $updated = 0;
        $skipped = 0;

        foreach ((array) $response->json() as $releasePayload) {
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
}
