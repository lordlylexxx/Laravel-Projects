<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateTicketRequest;
use App\Models\Tenant;
use App\Models\UpdateTicket;
use App\Models\User;
use Database\Seeders\RbacCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;

class UpdateTicketController extends Controller
{
    public function ownerStore(StoreUpdateTicketRequest $request): RedirectResponse
    {
        $this->assertStaffTenantReportsPermission($request);

        $tenant = $this->resolveTenantForRequest($request);
        abort_unless($tenant, 404);

        $this->createTicketForTenant($request, $tenant);

        return redirect()->back()
            ->with('success', 'Support ticket submitted. Central admin will review it.');
    }

    public function ownerShow(Request $request, UpdateTicket $updateTicket): View
    {
        $this->assertStaffTenantReportsPermission($request);
        $this->assertTicketBelongsToStaffTenant($request, $updateTicket);

        $isAdminScopedPath = $request->is('admin/*') || $request->routeIs('admin.*');

        return view('update-tickets.show-staff', [
            'ticket' => $updateTicket,
            'tenant' => $this->resolveTenantForRequest($request),
            'backToUpdatesPath' => $isAdminScopedPath ? '/admin/system-updates' : '/settings/updates',
        ]);
    }

    public function clientIndex(Request $request): View
    {
        $this->assertClientMaySubmit($request);

        $tenant = Tenant::current();
        abort_unless($tenant, 404);

        $user = $request->user();
        $landlordId = $this->resolveLandlordUserId($user);
        $email = (string) ($user?->email ?? '');

        $tickets = UpdateTicket::query()
            ->where('tenant_id', $tenant->id)
            ->where(function ($q) use ($landlordId, $email): void {
                $q->where('reporter_email', $email);
                if ($landlordId) {
                    $q->orWhere('reporter_landlord_user_id', $landlordId);
                }
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('update-tickets.index-client', [
            'tickets' => $tickets,
        ]);
    }

    public function clientStore(StoreUpdateTicketRequest $request): RedirectResponse
    {
        $this->assertClientMaySubmit($request);

        $tenant = Tenant::current();
        abort_unless($tenant, 404);

        $ticket = $this->createTicketForTenant($request, $tenant);

        return redirect()->route('update-tickets.show', $ticket)
            ->with('success', 'Support ticket submitted. Central admin will review it.');
    }

    public function clientShow(Request $request, UpdateTicket $updateTicket): View
    {
        $this->assertClientMaySubmit($request);
        $this->assertClientOwnsTicket($request, $updateTicket);

        return view('update-tickets.show-client', [
            'ticket' => $updateTicket,
        ]);
    }

    private function createTicketForTenant(StoreUpdateTicketRequest $request, Tenant $tenant): UpdateTicket
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $landlordId = $this->resolveLandlordUserId($user);
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('update-tickets/'.$tenant->id, 'public');
        }

        return UpdateTicket::query()->create([
            'tenant_id' => $tenant->id,
            'reporter_landlord_user_id' => $landlordId,
            'reporter_role' => (string) $user->role,
            'reporter_name' => (string) $user->name,
            'reporter_email' => (string) $user->email,
            'subject' => $request->validated('subject'),
            'body' => $request->validated('body'),
            'attachment_path' => $attachmentPath,
            'status' => UpdateTicket::STATUS_OPEN,
        ]);
    }

    private function resolveTenantForRequest(Request $request): ?Tenant
    {
        $current = Tenant::current();
        if ($current) {
            return $current;
        }

        $user = $request->user();
        if ($user?->tenant_id) {
            return Tenant::query()->find($user->tenant_id);
        }

        return null;
    }

    private function resolveLandlordUserId(?User $user): ?int
    {
        if (! $user?->email) {
            return null;
        }

        $id = DB::connection('landlord')
            ->table('users')
            ->where('email', $user->email)
            ->value('id');

        return $id ? (int) $id : null;
    }

    private function assertStaffTenantReportsPermission(Request $request): void
    {
        $user = $request->user();
        abort_unless($user && ($user->isOwner() || $user->isAdmin()), 403);

        $tenant = $this->resolveTenantForRequest($request);
        abort_unless($tenant, 404);
        abort_unless((int) ($user->tenant_id ?? 0) === (int) $tenant->id, 403);

        // Owners may always access update pages; tenant admins need reports.view.
        if (! $user->isAdmin()) {
            return;
        }

        $currentTenant = Tenant::current();
        if (! $currentTenant || (int) ($user->tenant_id ?? 0) !== (int) $currentTenant->id) {
            return;
        }

        $allowed = $user->hasPermission(User::PERM_REPORTS_VIEW);
        if (! $allowed) {
            RbacCatalog::ensurePermissionsExist();
            RbacCatalog::ensureRolesAndGrantPermissions();
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $user->syncRbacFromLegacyRole();
            $user->syncEffectiveTenantPermissions($currentTenant);
            $user->refresh();
            $allowed = $user->hasPermission(User::PERM_REPORTS_VIEW);
        }

        abort_unless($allowed, 403);
    }

    private function assertClientMaySubmit(Request $request): void
    {
        $user = $request->user();
        $tenant = Tenant::current();

        abort_unless($user instanceof User && $user->isClient(), 403);
        abort_unless($tenant && (int) ($user->tenant_id ?? 0) === (int) $tenant->id, 403);
        abort_unless($user->tenantClientMaySubmitUpdateTickets(), 403);
    }

    private function assertTicketBelongsToStaffTenant(Request $request, UpdateTicket $ticket): void
    {
        $tenant = $this->resolveTenantForRequest($request);
        abort_unless($tenant && (int) $ticket->tenant_id === (int) $tenant->id, 404);
    }

    private function assertClientOwnsTicket(Request $request, UpdateTicket $ticket): void
    {
        $tenant = Tenant::current();
        abort_unless($tenant && (int) $ticket->tenant_id === (int) $tenant->id, 404);

        $user = $request->user();
        $landlordId = $this->resolveLandlordUserId($user);
        $email = (string) ($user?->email ?? '');

        $owns = ((string) $ticket->reporter_email === $email)
            || ($landlordId && (int) ($ticket->reporter_landlord_user_id ?? 0) === $landlordId);

        abort_unless($owns, 403);
    }

    /**
     * @return \Illuminate\Support\Collection<int, UpdateTicket>
     */
    public static function recentTicketsForTenant(?int $tenantId, int $limit = 20)
    {
        if (! $tenantId) {
            return collect();
        }

        return UpdateTicket::query()
            ->where('tenant_id', $tenantId)
            ->latest()
            ->take($limit)
            ->get();
    }
}
