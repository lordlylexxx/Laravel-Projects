<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppRelease;
use App\Services\AdminReleaseService;
use App\Services\ReleaseRegistryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReleaseController extends Controller
{
    public function index(AdminReleaseService $adminReleaseService): View
    {
        return view('admin.releases.index', [
            'releases' => AppRelease::query()->orderByDesc('published_at')->orderByDesc('id')->paginate(20),
            'stats' => $adminReleaseService->getUpdateStatistics(),
        ]);
    }

    public function sync(ReleaseRegistryService $releaseRegistryService): RedirectResponse
    {
        $result = $releaseRegistryService->syncFromGitHub();

        return back()->with('success', "Releases synced. New: {$result['synced']}, Updated: {$result['updated']}, Skipped: {$result['skipped']}.");
    }

    public function markRequired(Request $request, AppRelease $release, AdminReleaseService $adminReleaseService): RedirectResponse
    {
        $validated = $request->validate([
            'grace_days' => ['nullable', 'integer', 'min:0', 'max:60'],
        ]);

        $adminReleaseService->markAsRequired((int) $release->id, (int) ($validated['grace_days'] ?? 7));

        return back()->with('success', "Release {$release->tag} marked as required.");
    }

    public function notifyAll(AppRelease $release, AdminReleaseService $adminReleaseService): RedirectResponse
    {
        $count = $adminReleaseService->notifyAllTenantsOfUpdate((int) $release->id);

        return back()->with('success', "Created {$count} tenant update-available record(s) for {$release->tag}.");
    }

    public function forceMarkAllUpdated(AppRelease $release, AdminReleaseService $adminReleaseService): RedirectResponse
    {
        $count = $adminReleaseService->forceMarkAllAsUpdated((int) $release->id);

        return back()->with('success', "Force-marked {$count} tenant(s) as updated to {$release->tag}.");
    }
}
