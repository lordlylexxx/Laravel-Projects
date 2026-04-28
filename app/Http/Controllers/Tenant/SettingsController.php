<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AppRelease;
use App\Models\Tenant;
use App\Services\TenantSelfUpdateService;
use App\Services\TenantUpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(TenantUpdateService $tenantUpdateService): View
    {
        $tenant = Tenant::current();
        abort_unless($tenant, 404);

        // Do not auto-backfill "current" from the newest registry row: that makes the tenant appear
        // already on the latest tag (by semver), so getAvailableUpdates() returns nothing even
        // though they never applied. Use tenants:backfill-updates or apply flow to set current.
        $current = $tenantUpdateService->getCurrentRelease((int) $tenant->id);

        $available = $tenantUpdateService->getAvailableUpdates((int) $tenant->id);

        return view('owner.settings.updates', [
            'tenant' => $tenant,
            'currentRelease' => $current?->release,
            'currentTenantUpdate' => $current,
            'availableReleases' => $available,
            'newestAvailableTag' => $available->first()?->tag,
        ]);
    }

    public function applyUpdate(
        Request $request,
        TenantSelfUpdateService $tenantSelfUpdateService,
        TenantUpdateService $tenantUpdateService
    ): JsonResponse|RedirectResponse {
        $tenant = Tenant::current();
        abort_unless($tenant, 404);

        $validated = $request->validate([
            'release_id' => ['required', 'integer', Rule::exists(AppRelease::class, 'id')],
        ]);

        $releaseId = (int) $validated['release_id'];
        $availableIds = $tenantUpdateService
            ->getAvailableUpdates((int) $tenant->id)
            ->pluck('id')
            ->all();

        if ($availableIds === [] || ! in_array($releaseId, $availableIds, true)) {
            if ($request->ajax()) {
                return response()->json(['message' => 'That release is not available to apply for this tenant.'], 422);
            }

            return back()->with('error', 'That release is not available to apply for this tenant.');
        }

        $result = $tenantSelfUpdateService->applyUpdate((int) $tenant->id, $releaseId);

        if ($request->ajax()) {
            return response()->json(
                ['message' => $result['message']],
                $result['ok'] ? 200 : 422
            );
        }

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }
}
