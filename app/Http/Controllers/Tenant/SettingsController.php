<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AppRelease;
use App\Models\Tenant;
use App\Services\TenantSelfUpdateService;
use App\Services\TenantUpdateService;
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

        $current = $tenantUpdateService->getCurrentRelease((int) $tenant->id);
        if (! $current) {
            $fallbackRelease = AppRelease::query()
                ->orderByDesc('is_stable')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->first();

            if ($fallbackRelease) {
                $current = $tenantUpdateService
                    ->backfillCurrentReleaseForTenant($tenant, $fallbackRelease)
                    ->load('release');
            }
        }

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
            'release_id' => ['required', 'integer', Rule::exists(AppRelease::class, 'id')],
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
