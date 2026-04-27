<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\TenantSelfUpdateService;
use App\Services\TenantUpdateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request, TenantUpdateService $tenantUpdateService): View
    {
        $tenant = Tenant::current();
        abort_unless($tenant, 404);

        $current = $tenantUpdateService->getCurrentRelease((int) $tenant->id);
        $available = $tenantUpdateService->getAvailableUpdates((int) $tenant->id);

        return view('owner.settings.updates', [
            'tenant' => $tenant,
            'currentRelease' => $current?->release,
            'currentTenantUpdate' => $current,
            'availableReleases' => $available,
        ]);
    }

    public function applyUpdate(
        Request $request,
        TenantSelfUpdateService $tenantSelfUpdateService,
        TenantUpdateService $tenantUpdateService
    ): RedirectResponse {
        $tenant = Tenant::current();
        abort_unless($tenant, 404);

        $validated = $request->validate([
            'release_id' => ['required', 'integer', 'exists:app_releases,id'],
        ]);

        $releaseId = (int) $validated['release_id'];
        $availableIds = $tenantUpdateService
            ->getAvailableUpdates((int) $tenant->id)
            ->pluck('id')
            ->all();

        if ($availableIds === [] || ! in_array($releaseId, $availableIds, true)) {
            return back()->with('error', 'That release is not available to apply for this tenant.');
        }

        $result = $tenantSelfUpdateService->applyUpdate((int) $tenant->id, $releaseId);

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }
}
