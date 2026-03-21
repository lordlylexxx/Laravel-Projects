<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UpdateController extends Controller
{
    public function check(Request $request): JsonResponse
    {
        $this->authorizeChannel($request);

        $currentVersion = (string) $request->query('version', '0.0.0');
        $latestVersion = (string) config('updates.latest_version', '1.0.0');
        $hasUpdate = version_compare($latestVersion, $currentVersion, '>');

        $downloadParams = [];
        $token = (string) $request->query('token', '');

        if ($token !== '') {
            $downloadParams['token'] = $token;
        }

        return response()->json([
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion,
            'has_update' => $hasUpdate,
            'release_notes' => (string) config('updates.release_notes', ''),
            'published_at' => config('updates.published_at'),
            'download_url' => route('updates.download', $downloadParams),
        ]);
    }

    public function download(Request $request): BinaryFileResponse
    {
        $this->authorizeChannel($request);

        $filename = (string) config('updates.package_filename', 'latest-update.zip');
        $path = storage_path('app/public/updates/' . $filename);

        abort_unless(File::exists($path), 404, 'Update package not found.');

        return response()->download($path, $filename, [
            'Content-Type' => 'application/zip',
        ]);
    }

    private function authorizeChannel(Request $request): void
    {
        $expectedToken = (string) config('updates.channel_token', '');

        if ($expectedToken === '') {
            return;
        }

        $providedToken = (string) $request->query('token', '');

        abort_unless(hash_equals($expectedToken, $providedToken), 403, 'Invalid update channel token.');
    }
}
