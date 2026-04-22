<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Services\GithubReleaseMetadataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class UpdateController extends Controller
{
    public function check(Request $request, GithubReleaseMetadataService $githubReleases): JsonResponse
    {
        $this->authorizeChannel($request);

        $currentVersion = (string) $request->query('version', '0.0.0');

        $fromGithub = $githubReleases->fetchLatestReleaseMetadata();

        if ($fromGithub !== null) {
            $latestVersion = $fromGithub['latest_version'];
            $releaseNotes = $fromGithub['release_notes'];
            $publishedAt = $fromGithub['published_at'];
        } else {
            $latestVersion = (string) config('updates.latest_version', '1.0.0');
            $releaseNotes = (string) config('updates.release_notes', '');
            $publishedAt = config('updates.published_at');
        }

        $hasUpdate = version_compare($latestVersion, $currentVersion, '>');

        $downloadParams = [];
        $token = (string) $request->query('token', '');

        if ($token !== '') {
            $downloadParams['token'] = $token;
        }

        $githubPackageUrl = $githubReleases->resolveLatestReleasePackageDownloadUrl();
        $downloadUrl = $githubPackageUrl !== null
            ? $githubPackageUrl
            : route('updates.download', $downloadParams);

        return response()->json([
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion,
            'has_update' => $hasUpdate,
            'release_notes' => $releaseNotes,
            'published_at' => $publishedAt,
            'download_url' => $downloadUrl,
        ]);
    }

    public function download(Request $request, GithubReleaseMetadataService $githubReleases): Response|BinaryFileResponse
    {
        $this->authorizeChannel($request);

        $githubUrl = $githubReleases->resolveLatestReleasePackageDownloadUrl();

        if ($githubUrl !== null) {
            return redirect()->away($githubUrl);
        }

        $filename = (string) config('updates.package_filename', 'latest-update.zip');
        $path = storage_path('app/public/updates/'.$filename);

        if (! File::exists($path)) {
            return response()->view('central.update-package-missing', [
                'path' => $path,
                'filename' => $filename,
            ], 404);
        }

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
