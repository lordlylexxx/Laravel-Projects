<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUpdateTicketStatusRequest;
use App\Models\Tenant;
use App\Models\UpdateTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UpdateTicketController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $tenantId = $request->query('tenant_id');

        $tickets = UpdateTicket::query()
            ->with('tenant')
            ->when($status === 'open' || $status === 'resolved', fn ($q) => $q->where('status', $status))
            ->when($tenantId !== null && $tenantId !== '' && ctype_digit((string) $tenantId), fn ($q) => $q->where('tenant_id', (int) $tenantId))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $tenantFilterOptions = Tenant::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.update-tickets.index', [
            'tickets' => $tickets,
            'tenantFilterOptions' => $tenantFilterOptions,
            'filters' => [
                'status' => $status,
                'tenant_id' => $tenantId,
            ],
        ]);
    }

    public function show(UpdateTicket $updateTicket): View
    {
        $updateTicket->load('tenant');

        return view('admin.update-tickets.show', [
            'ticket' => $updateTicket,
        ]);
    }

    public function update(UpdateUpdateTicketStatusRequest $request, UpdateTicket $updateTicket): RedirectResponse
    {
        $action = $request->validated('action');
        $actorLandlordId = $this->resolveLandlordActorId($request);

        if ($action === 'resolve') {
            $updateTicket->update([
                'status' => UpdateTicket::STATUS_RESOLVED,
                'resolution_notes' => $request->validated('resolution_notes'),
                'reopen_note' => null,
                'resolved_at' => now(),
                'resolved_by_landlord_user_id' => $actorLandlordId,
            ]);
            $message = 'Ticket marked as resolved.';
        } elseif ($action === 'reopen') {
            $updateTicket->update([
                'status' => UpdateTicket::STATUS_OPEN,
                'reopen_note' => $request->validated('reopen_note'),
                'resolved_at' => null,
                'resolved_by_landlord_user_id' => null,
            ]);
            $message = 'Ticket reopened.';
        } else {
            $updateTicket->update([
                'status' => UpdateTicket::STATUS_OPEN,
                'reopen_note' => null,
                'resolved_at' => null,
                'resolved_by_landlord_user_id' => null,
            ]);
            $message = 'Ticket marked as open.';
        }

        return redirect()->route('admin.update-tickets.show', $updateTicket)->with('success', $message);
    }

    private function resolveLandlordActorId(Request $request): ?int
    {
        $email = (string) ($request->user()?->email ?? '');
        if ($email === '') {
            return $request->user()?->id;
        }

        $id = DB::connection('landlord')
            ->table('users')
            ->where('email', $email)
            ->value('id');

        return $id ? (int) $id : $request->user()?->id;
    }
}
