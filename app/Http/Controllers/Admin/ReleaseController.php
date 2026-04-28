<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppRelease;
use App\Services\AdminReleaseService;
use App\Services\ReleaseRegistryService;
use App\Support\SemanticVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class ReleaseController extends Controller
{
    public function index(AdminReleaseService $adminReleaseService): View
    {
        $ordered = AppRelease::query()->get()->sort(function (AppRelease $a, AppRelease $b): int {
            return -version_compare(
                SemanticVersion::normalize((string) $a->tag),
                SemanticVersion::normalize((string) $b->tag)
            );
        })->values();

        $page = max(1, (int) request('page', 1));
        $perPage = 20;
        $releases = new LengthAwarePaginator(
            $ordered->slice(($page - 1) * $perPage, $perPage)->values(),
            $ordered->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.releases.index', [
            'releases' => $releases,
            'stats' => $adminReleaseService->getUpdateStatistics(),
        ]);
    }

    public function sync(Request $request, ReleaseRegistryService $releaseRegistryService): JsonResponse|RedirectResponse
    {
        $result = $releaseRegistryService->syncFromGitHub();

        if (! empty($result['error'])) {
            if ($request->ajax()) {
                return response()->json(['message' => $result['error']], 422);
            }

            return back()->with('error', $result['error']);
        }

        $message = "Releases synced. New: {$result['synced']}, Updated: {$result['updated']}, Skipped: {$result['skipped']}.";

        if ($request->ajax()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function markRequired(Request $request, AppRelease $release, AdminReleaseService $adminReleaseService): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'grace_days' => ['nullable', 'integer', 'min:0', 'max:60'],
        ]);

        $adminReleaseService->markAsRequired((int) $release->id, (int) ($validated['grace_days'] ?? 7));

        $message = "Release {$release->tag} marked as required.";

        if ($request->ajax()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function notifyAll(Request $request, AppRelease $release, AdminReleaseService $adminReleaseService): JsonResponse|RedirectResponse
    {
        $count = $adminReleaseService->notifyAllTenantsOfUpdate((int) $release->id);

        $message = "Created {$count} tenant update-available record(s) for {$release->tag}.";

        if ($request->ajax()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function forceMarkAllUpdated(Request $request, AppRelease $release, AdminReleaseService $adminReleaseService): JsonResponse|RedirectResponse
    {
        $count = $adminReleaseService->forceMarkAllAsUpdated((int) $release->id);

        $message = "Force-marked {$count} tenant(s) as updated to {$release->tag}.";

        if ($request->ajax()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }
}
