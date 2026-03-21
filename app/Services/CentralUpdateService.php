<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CentralUpdateService
{
    public function checkForUpdates(string $currentVersion): ?array
    {
        $baseUrl = rtrim((string) config('updates.central_base_url', ''), '/');

        if ($baseUrl === '') {
            return null;
        }

        $params = [
            'version' => $currentVersion,
        ];

        $token = (string) config('updates.channel_token', '');

        if ($token !== '') {
            $params['token'] = $token;
        }

        try {
            $response = Http::timeout(5)->acceptJson()->get($baseUrl . '/system-updates/check', $params);

            if (! $response->ok()) {
                return [
                    'has_update' => false,
                    'unavailable' => true,
                    'message' => 'Unable to reach central update server.',
                ];
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
            return [
                'has_update' => false,
                'unavailable' => true,
                'message' => 'Unable to check updates at the moment.',
            ];
        }
    }
}
