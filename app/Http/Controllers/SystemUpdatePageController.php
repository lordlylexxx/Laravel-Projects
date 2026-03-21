<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\UpdateLog;
use App\Services\CentralUpdateService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SystemUpdatePageController extends Controller
{
    public function ownerIndex(Request $request, CentralUpdateService $updates): View
    {
        return $this->renderPage('owner', $request, $updates);
    }

    public function adminIndex(Request $request, CentralUpdateService $updates): View
    {
        return $this->renderPage('admin', $request, $updates);
    }

    public function ownerMarkInstalled(Request $request): RedirectResponse
    {
        return $this->markInstalled('owner', $request);
    }

    public function adminMarkInstalled(Request $request): RedirectResponse
    {
        return $this->markInstalled('admin', $request);
    }

    private function renderPage(string $navType, Request $request, CentralUpdateService $updates): View
    {
        $currentTenant = Tenant::current();
        $tenantId = $currentTenant?->id ?? $request->user()?->tenant_id;
        $currentVersion = (string) config('updates.current_version', '1.0.0');
        $payload = $updates->checkForUpdates($currentVersion) ?? [];

        $latestVersion = (string) ($payload['latest_version'] ?? config('updates.latest_version', $currentVersion));
        $hasUpdate = (bool) ($payload['has_update'] ?? version_compare($latestVersion, $currentVersion, '>'));
        $isUnavailable = (bool) ($payload['unavailable'] ?? false);

        $channelToken = (string) config('updates.channel_token', '');
        $fallbackDownloadUrl = rtrim((string) config('updates.central_base_url', ''), '/') . '/system-updates/download';

        if ($fallbackDownloadUrl !== '/system-updates/download' && $channelToken !== '') {
            $fallbackDownloadUrl .= '?token=' . urlencode($channelToken);
        }

        $downloadUrl = (string) ($payload['download_url'] ?? $fallbackDownloadUrl);
        $status = $isUnavailable
            ? 'unavailable'
            : ($hasUpdate ? 'update_available' : 'up_to_date');

        $log = UpdateLog::create([
            'tenant_id' => $tenantId,
            'user_id' => $request->user()?->id,
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion,
            'release_notes' => (string) ($payload['release_notes'] ?? config('updates.release_notes', '')),
            'download_url' => $downloadUrl,
            'channel_status' => $status,
            'status_message' => (string) ($payload['message'] ?? ''),
            'checked_at' => now(),
        ]);

        $history = UpdateLog::query()
            ->when($navType === 'admin' && ! $tenantId, fn ($query) => $query, fn ($query) => $query->where('tenant_id', $tenantId))
            ->latest('checked_at')
            ->take(12)
            ->get();

        return view('system-updates.index', [
            'navType' => $navType,
            'user' => $request->user(),
            'currentVersion' => $currentVersion,
            'latestVersion' => $latestVersion,
            'hasUpdate' => $hasUpdate,
            'isUnavailable' => $isUnavailable,
            'statusMessage' => (string) ($payload['message'] ?? ''),
            'releaseNotes' => (string) ($payload['release_notes'] ?? config('updates.release_notes', 'No release notes provided.')),
            'publishedAt' => $payload['published_at'] ?? config('updates.published_at'),
            'downloadUrl' => $downloadUrl,
            'centralBaseUrl' => (string) config('updates.central_base_url', ''),
            'history' => $history,
            'lastCheckLogId' => $log->id,
            'markInstalledRoute' => $navType === 'admin'
                ? route('admin.updates.mark-installed')
                : route('owner.updates.mark-installed'),
        ]);
    }

    private function markInstalled(string $navType, Request $request): RedirectResponse
    {
        $currentTenant = Tenant::current();
        $tenantId = $currentTenant?->id ?? $request->user()?->tenant_id;

        $log = UpdateLog::query()
            ->when($navType === 'admin' && ! $tenantId, fn ($query) => $query, fn ($query) => $query->where('tenant_id', $tenantId))
            ->latest('checked_at')
            ->first();

        if ($log) {
            $log->update([
                'installed_at' => now(),
                'channel_status' => 'installed',
                'status_message' => 'Marked as installed by ' . ($request->user()?->name ?? 'system') . '.',
            ]);
        }

        return redirect()->route($navType === 'admin' ? 'admin.updates.index' : 'owner.updates.index')
            ->with('success', 'Latest update has been marked as installed.');
    }
}
