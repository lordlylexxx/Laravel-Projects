<?php

namespace App\Http\Controllers;

use App\Jobs\InstallSystemUpdateJob;
use App\Jobs\RestorePreviousSystemUpdateJob;
use App\Models\Tenant;
use App\Models\UpdateLog;
use App\Models\User;
use App\Services\CentralUpdateService;
use Database\Seeders\RbacCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;

class SystemUpdatePageController extends Controller
{
    public function ownerIndex(Request $request, CentralUpdateService $updates): View
    {
        $this->assertTenantAdminHasPermission($request, User::PERM_REPORTS_VIEW);

        return $this->renderPage('owner', $request, $updates);
    }

    public function adminIndex(Request $request, CentralUpdateService $updates): View
    {
        return $this->renderPage('admin', $request, $updates);
    }

    public function ownerMarkInstalled(Request $request): RedirectResponse
    {
        $this->assertTenantAdminHasPermission($request, User::PERM_REPORTS_VIEW);

        return $this->markInstalled('owner', $request);
    }

    public function adminMarkInstalled(Request $request): RedirectResponse
    {
        return $this->markInstalled('admin', $request);
    }

    public function ownerInstall(Request $request, CentralUpdateService $updates): RedirectResponse
    {
        $this->assertTenantAdminHasPermission($request, User::PERM_REPORTS_VIEW);

        return $this->queueInstall('owner', $request, $updates);
    }

    public function adminInstall(Request $request, CentralUpdateService $updates): RedirectResponse
    {
        return $this->queueInstall('admin', $request, $updates);
    }

    public function ownerRestore(Request $request): RedirectResponse
    {
        $this->assertTenantAdminHasPermission($request, User::PERM_REPORTS_VIEW);

        return $this->queueRestore('owner', $request);
    }

    public function adminRestore(Request $request): RedirectResponse
    {
        return $this->queueRestore('admin', $request);
    }

    private function renderPage(string $navType, Request $request, CentralUpdateService $updates): View
    {
        $currentTenant = Tenant::current();
        $tenantId = $currentTenant?->id ?? $request->user()?->tenant_id;
        $currentVersion = (string) config('updates.current_version', '1.0.0');
        $payload = $updates->getCachedUpdatePayload($currentVersion) ?? [];

        // Keep the page fast: refresh update metadata after response if cache is cold.
        if ($payload === []) {
            app()->terminating(function () use ($updates, $currentVersion): void {
                $updates->checkForUpdates($currentVersion);
            });
        }

        $latestVersion = (string) ($payload['latest_version'] ?? config('updates.latest_version', $currentVersion));
        $hasUpdate = (bool) ($payload['has_update'] ?? version_compare($latestVersion, $currentVersion, '>'));
        $isUnavailable = (bool) ($payload['unavailable'] ?? false);

        $channelToken = (string) config('updates.channel_token', '');
        $fallbackDownloadUrl = rtrim((string) config('updates.central_base_url', ''), '/').'/system-updates/download';

        if ($fallbackDownloadUrl !== '/system-updates/download' && $channelToken !== '') {
            $fallbackDownloadUrl .= '?token='.urlencode($channelToken);
        }

        $downloadUrl = (string) ($payload['download_url'] ?? $fallbackDownloadUrl);
        $checksumUrl = (string) ($payload['checksum_url'] ?? '');
        $status = $isUnavailable
            ? 'unavailable'
            : ($hasUpdate ? 'update_available' : 'up_to_date');

        $resolvedLandlordUserId = $this->resolveLandlordUserId($request);
        $log = UpdateLog::create([
            'tenant_id' => $tenantId,
            'user_id' => $resolvedLandlordUserId,
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion,
            'release_notes' => (string) ($payload['release_notes'] ?? config('updates.release_notes', '')),
            'download_url' => $downloadUrl,
            'checksum_url' => $checksumUrl !== '' ? $checksumUrl : null,
            'channel_status' => $status,
            'status_message' => (string) ($payload['message'] ?? ''),
            'checked_at' => now(),
        ]);

        $history = UpdateLog::query()
            ->when($navType === 'admin' && ! $tenantId, fn ($query) => $query, fn ($query) => $query->where('tenant_id', $tenantId))
            ->latest('checked_at')
            ->paginate(5, ['*'], 'history_page')
            ->withQueryString();

        $isTenantContext = Tenant::checkCurrent();
        $ownerUpdateTicketStoreRoute = '/owner/update-tickets';
        $updateTicketShowPathPrefix = '/owner/update-tickets';
        $installStatusRoute = ($navType === 'admin' && ! $isTenantContext)
            ? '/admin/system-updates/status'
            : '/owner/system-updates/status';

        $installRoute = $navType === 'admin'
            ? URL::signedRoute('admin.updates.install', [], null, false)
            : URL::signedRoute('owner.updates.install', [], null, false);
        $restoreRoute = $navType === 'admin'
            ? URL::signedRoute('admin.updates.restore', [], null, false)
            : URL::signedRoute('owner.updates.restore', [], null, false);
        $markInstalledRoute = $navType === 'admin'
            ? URL::signedRoute('admin.updates.mark-installed', [], null, false)
            : URL::signedRoute('owner.updates.mark-installed', [], null, false);

        if ($navType === 'admin' && $isTenantContext) {
            $ownerUpdateTicketStoreRoute = '/admin/system-updates/tickets/report';
            $updateTicketShowPathPrefix = '/admin/system-updates/tickets/report';
        }

        $updateTickets = collect();
        if ($tenantId && ($navType !== 'admin' || $isTenantContext)) {
            $updateTickets = UpdateTicketController::recentTicketsForTenant((int) $tenantId, 20);
        }

        $scopeHistoryQuery = UpdateLog::query()
            ->when($navType === 'admin' && ! $tenantId, fn ($query) => $query, fn ($query) => $query->where('tenant_id', $tenantId));

        $latestInstallActivity = (clone $scopeHistoryQuery)
            ->whereIn('channel_status', ['installing', 'restoring'])
            ->latest('checked_at')
            ->first();

        $latestBackupLog = (clone $scopeHistoryQuery)
            ->whereNotNull('backup_path')
            ->latest('checked_at')
            ->first();

        $restoreAvailable = $latestBackupLog !== null
            && is_string($latestBackupLog->backup_path)
            && $latestBackupLog->backup_path !== ''
            && File::exists($latestBackupLog->backup_path);

        $activeUpdateLogId = $latestInstallActivity?->id;

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
            'markInstalledRoute' => $markInstalledRoute,
            'installRoute' => $installRoute,
            'restoreRoute' => $restoreRoute,
            'installStatusRoute' => $installStatusRoute,
            'updateTickets' => $updateTickets,
            'ownerUpdateTicketStoreRoute' => $ownerUpdateTicketStoreRoute,
            'updateTicketShowPathPrefix' => $updateTicketShowPathPrefix,
            'tenantId' => $tenantId,
            'restoreAvailable' => $restoreAvailable,
            'activeUpdateLogId' => $activeUpdateLogId,
            'installInProgress' => $latestInstallActivity !== null,
            'latestInstallActivity' => $latestInstallActivity,
        ]);
    }

    public function installStatus(Request $request): JsonResponse
    {
        $currentTenant = Tenant::current();
        $tenantId = $currentTenant?->id ?? $request->user()?->tenant_id;
        $isTenantContext = Tenant::checkCurrent();
        $updateLogId = $request->query('update_log_id');

        $query = UpdateLog::query()
            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId), fn ($query) => $query->whereNull('tenant_id'));

        if ($updateLogId !== null && $updateLogId !== '') {
            $latest = (clone $query)
                ->whereKey((int) $updateLogId)
                ->first();
        } else {
            $latest = (clone $query)
                ->whereIn('channel_status', ['installing', 'restoring', 'installed', 'failed'])
                ->latest('checked_at')
                ->first();
        }

        if (! $latest) {
            return response()->json([
                'status' => 'idle',
                'progress_percent' => 0,
                'current_step' => null,
                'message' => null,
                'install_started_at' => null,
                'install_finished_at' => null,
                'install_error' => null,
            ]);
        }

        return response()->json([
            'status' => $latest->channel_status,
            'progress_percent' => (int) ($latest->progress_percent ?? 0),
            'current_step' => $latest->current_step,
            'message' => $latest->status_message,
            'install_started_at' => optional($latest->install_started_at)->toIso8601String(),
            'install_finished_at' => optional($latest->install_finished_at)->toIso8601String(),
            'install_error' => $latest->install_error,
            'is_tenant_context' => $isTenantContext,
        ]);
    }

    private function queueInstall(string $navType, Request $request, CentralUpdateService $updates): RedirectResponse
    {
        $currentTenant = Tenant::current();
        $tenantId = $currentTenant?->id ?? $request->user()?->tenant_id;
        $currentVersion = (string) config('updates.current_version', '1.0.0');
        $payload = $updates->checkForUpdates($currentVersion) ?? [];
        $downloadUrl = (string) ($payload['download_url'] ?? '');
        $checksumUrl = (string) ($payload['checksum_url'] ?? '');
        $latestVersion = (string) ($payload['latest_version'] ?? $currentVersion);

        if ($downloadUrl === '') {
            return $this->redirectToUpdatesPage($navType, $tenantId)
                ->with('error', 'Cannot start install: update package URL is not available.');
        }

        if (version_compare($latestVersion, $currentVersion, '<=')) {
            return $this->redirectToUpdatesPage($navType, $tenantId)
                ->with('error', 'There is no newer version available to install.');
        }

        if ($this->hasActiveUpdateOperation($navType, $tenantId)) {
            return $this->redirectToUpdatesPage($navType, $tenantId)
                ->with('error', 'An update operation is already in progress.');
        }

        $log = UpdateLog::create([
            'tenant_id' => $tenantId,
            'user_id' => $this->resolveLandlordUserId($request),
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion,
            'release_notes' => (string) ($payload['release_notes'] ?? ''),
            'download_url' => $downloadUrl,
            'checksum_url' => $checksumUrl !== '' ? $checksumUrl : null,
            'channel_status' => 'installing',
            'progress_percent' => 0,
            'current_step' => 'queued',
            'status_message' => 'Install queued by '.($request->user()?->name ?? 'system').'.',
            'checked_at' => now(),
            'install_started_at' => now(),
            'install_error' => null,
        ]);

        InstallSystemUpdateJob::dispatch($log->id, $downloadUrl, $checksumUrl !== '' ? $checksumUrl : null, $tenantId ? (int) $tenantId : null);

        return $this->redirectToUpdatesPage($navType, $tenantId)
            ->with('success', 'Update installation started. Refresh in a few moments to check progress.');
    }

    private function queueRestore(string $navType, Request $request): RedirectResponse
    {
        $currentTenant = Tenant::current();
        $tenantId = $currentTenant?->id ?? $request->user()?->tenant_id;

        if ($this->hasActiveUpdateOperation($navType, $tenantId)) {
            return $this->redirectToUpdatesPage($navType, $tenantId)
                ->with('error', 'An update operation is already in progress.');
        }

        $backupLog = UpdateLog::query()
            ->when($navType === 'admin' && ! $tenantId, fn ($query) => $query, fn ($query) => $query->where('tenant_id', $tenantId))
            ->whereNotNull('backup_path')
            ->latest('checked_at')
            ->first();

        if (! $backupLog || ! is_string($backupLog->backup_path) || $backupLog->backup_path === '' || ! File::exists($backupLog->backup_path)) {
            return $this->redirectToUpdatesPage($navType, $tenantId)
                ->with('error', 'No backup package is available for restore.');
        }

        $currentVersion = (string) config('updates.current_version', '1.0.0');
        $targetVersion = (string) ($backupLog->backup_version ?: $backupLog->current_version ?: $currentVersion);

        $restoreLog = UpdateLog::create([
            'tenant_id' => $tenantId,
            'user_id' => $this->resolveLandlordUserId($request),
            'current_version' => $currentVersion,
            'latest_version' => $targetVersion,
            'download_url' => $backupLog->backup_path,
            'channel_status' => 'restoring',
            'progress_percent' => 0,
            'current_step' => 'queued',
            'status_message' => 'Restore queued by '.($request->user()?->name ?? 'system').'.',
            'checked_at' => now(),
            'install_started_at' => now(),
            'restored_from_update_log_id' => $backupLog->id,
            'install_error' => null,
        ]);

        RestorePreviousSystemUpdateJob::dispatch(
            $restoreLog->id,
            $backupLog->backup_path,
            $tenantId ? (int) $tenantId : null,
            $targetVersion
        );

        return $this->redirectToUpdatesPage($navType, $tenantId)
            ->with('success', 'Restore started. Refresh in a few moments to check progress.');
    }

    private function markInstalled(string $navType, Request $request): RedirectResponse
    {
        $currentTenant = Tenant::current();
        $tenantId = $currentTenant?->id ?? $request->user()?->tenant_id;

        if ($this->hasActiveUpdateOperation($navType, $tenantId)) {
            return $this->redirectToUpdatesPage($navType, $tenantId)
                ->with('error', 'An update operation is already in progress.');
        }

        $log = UpdateLog::query()
            ->when($navType === 'admin' && ! $tenantId, fn ($query) => $query, fn ($query) => $query->where('tenant_id', $tenantId))
            ->latest('checked_at')
            ->first();

        if ($log) {
            $log->update([
                'installed_at' => now(),
                'channel_status' => 'installed',
                'status_message' => 'Marked as installed by '.($request->user()?->name ?? 'system').'.',
            ]);
        }

        $isTenantContext = Tenant::checkCurrent();

        return redirect()->to(($navType === 'admin' && ! $isTenantContext) ? '/admin/system-updates' : '/owner/system-updates')
            ->with('success', 'Latest update has been marked as installed.');
    }

    private function redirectToUpdatesPage(string $navType, mixed $tenantId): RedirectResponse
    {
        $isTenantContext = Tenant::checkCurrent();

        return redirect()->to(($navType === 'admin' && ! $tenantId && ! $isTenantContext) ? '/admin/system-updates' : '/owner/system-updates');
    }

    private function hasActiveUpdateOperation(string $navType, mixed $tenantId): bool
    {
        return UpdateLog::query()
            ->when($navType === 'admin' && ! $tenantId, fn ($query) => $query, fn ($query) => $query->where('tenant_id', $tenantId))
            ->whereIn('channel_status', ['installing', 'restoring'])
            ->where('checked_at', '>=', now()->subHours(2))
            ->exists();
    }

    private function resolveLandlordUserId(Request $request): ?int
    {
        $requestUser = $request->user();
        if (! $requestUser?->email) {
            return null;
        }

        $cacheKey = 'updates.landlord_user_id.'.sha1(strtolower((string) $requestUser->email));
        $resolvedLandlordUserId = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($requestUser) {
            return DB::connection('landlord')
                ->table('users')
                ->where('email', $requestUser->email)
                ->value('id');
        });

        return $resolvedLandlordUserId ? (int) $resolvedLandlordUserId : null;
    }

    public function statusJson(Request $request): JsonResponse
    {
        return $this->installStatus($request);
    }

    private function assertTenantAdminHasPermission(Request $request, string $permission): void
    {
        $user = $request->user();
        $tenant = Tenant::current();

        if (! $tenant || ! $user || ! $user->isAdmin()) {
            return;
        }

        if ((int) ($user->tenant_id ?? 0) !== (int) $tenant->id) {
            return;
        }

        $allowed = $user->hasPermission($permission);
        if (! $allowed) {
            RbacCatalog::ensurePermissionsExist();
            RbacCatalog::ensureRolesAndGrantPermissions();
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $user->syncRbacFromLegacyRole();
            $user->syncEffectiveTenantPermissions($tenant);
            $user->refresh();
            $allowed = $user->hasPermission($permission);
        }

        abort_unless($allowed, 403);
    }
}
