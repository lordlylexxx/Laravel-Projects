<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Message;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Messaging\CentralSupportInboxService;
use App\Services\Messaging\TenantCentralSupportProxyUser;
use Database\Seeders\RbacCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Multitenancy\Actions\ForgetCurrentTenantAction;
use Spatie\Multitenancy\Actions\MakeTenantCurrentAction;

class MessageController extends Controller
{
    /**
     * Display user's messages.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $user->assertTenantGuestMayUseMessages();
        $currentTenant = Tenant::current();
        $this->assertTenantAdminHasPermission($user, $currentTenant, User::PERM_MESSAGES_MANAGE);

        // One inbox row per conversation partner (latest message in each pair).
        $messages = Message::query()
            ->with(['sender', 'receiver', 'booking.accommodation'])
            ->whereIn('id', function ($sub) use ($user, $currentTenant): void {
                $sub->from('messages')
                    ->selectRaw('MAX(id) as id')
                    ->where(function ($q) use ($user): void {
                        $q->where('receiver_id', $user->id)
                            ->orWhere('sender_id', $user->id);
                    })
                    ->when($currentTenant, fn ($q) => $q->where('tenant_id', $currentTenant->id))
                    ->groupByRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END', [$user->id]);
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        $unreadCount = Message::where('receiver_id', $user->id)
            ->when($currentTenant, fn ($query) => $query->where('tenant_id', $currentTenant->id))
            ->unread()
            ->count();

        $unreadByPartner = array_fill_keys(
            Message::query()
                ->where('receiver_id', $user->id)
                ->unread()
                ->when($currentTenant, fn ($q) => $q->where('tenant_id', $currentTenant->id))
                ->pluck('sender_id')
                ->unique()
                ->all(),
            true
        );

        $conversationMessages = Collection::make();
        $replyAnchorMessage = null;
        $selectedMessage = $messages->first();

        $partnerId = $request->filled('partner') ? (int) $request->query('partner') : null;
        if ($partnerId) {
            $onPage = $messages->first(fn (Message $m) => $this->otherPartyUserId($m, (int) $user->id) === $partnerId);
            if ($onPage) {
                $selectedMessage = $onPage;
            } else {
                $head = Message::query()
                    ->with(['sender', 'receiver', 'booking.accommodation'])
                    ->where(function ($q) use ($user, $partnerId): void {
                        $q->where(function ($x) use ($user, $partnerId): void {
                            $x->where('sender_id', $user->id)->where('receiver_id', $partnerId);
                        })->orWhere(function ($x) use ($user, $partnerId): void {
                            $x->where('sender_id', $partnerId)->where('receiver_id', $user->id);
                        });
                    })
                    ->when($currentTenant, fn ($q) => $q->where('tenant_id', $currentTenant->id))
                    ->orderByDesc('id')
                    ->first();
                if ($head) {
                    $selectedMessage = $head;
                }
            }
        }

        if ($selectedMessage) {
            $counterpartId = $this->otherPartyUserId($selectedMessage, (int) $user->id);
            $conversationMessages = $this->conversationBetween((int) $user->id, $counterpartId, $currentTenant?->id);
            $replyAnchorMessage = $conversationMessages->last();
        }

        $canDeleteSelectedConversation = $selectedMessage !== null
            && $this->userCanDeleteTenantMessage($user, $selectedMessage, $currentTenant);

        return view('messages.index', compact(
            'messages',
            'unreadCount',
            'unreadByPartner',
            'conversationMessages',
            'replyAnchorMessage',
            'selectedMessage',
            'canDeleteSelectedConversation'
        ));
    }

    /**
     * Compose a new thread (tenant managers, or tenant clients messaging owner/admin).
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $user->assertTenantGuestMayUseMessages();
        $currentTenant = $this->currentTenantOrActivateForMessaging($user);
        $this->assertTenantAdminHasPermission($user, $currentTenant, User::PERM_MESSAGES_MANAGE);

        abort_unless($currentTenant, 404);

        $isManager = $this->userIsTenantManager($user, $currentTenant);
        $isClientComposer = $user->isClient()
            && (int) ($user->tenant_id ?? 0) === (int) $currentTenant->id;

        abort_unless($isManager || $isClientComposer, 403);

        $teamUserIds = User::query()
            ->where('tenant_id', $currentTenant->id)
            ->whereIn('role', [User::ROLE_ADMIN, User::ROLE_OWNER])
            ->pluck('id');

        if ($currentTenant->owner_user_id) {
            $teamUserIds = $teamUserIds->push($currentTenant->owner_user_id)->unique();
        }

        $team = User::query()
            ->whereIn('id', $teamUserIds)
            ->where('id', '!=', $user->id)
            ->where('email', 'not like', '__impastay_central_support.tenant-%')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        $clients = collect();
        if ($isManager) {
            $clients = User::query()
                ->where('tenant_id', $currentTenant->id)
                ->where('role', User::ROLE_CLIENT)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
        }

        return view('messages.create', compact('clients', 'team', 'currentTenant', 'isClientComposer'));
    }

    /**
     * Mark every unread message addressed to the current user as read (tenant-scoped when applicable).
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->assertTenantGuestMayUseMessages();
        $currentTenant = Tenant::current();

        $query = Message::query()
            ->where('receiver_id', $user->id)
            ->where('status', Message::STATUS_SENT);

        if ($currentTenant) {
            $query->where('tenant_id', $currentTenant->id);
        }

        $query->update([
            'status' => Message::STATUS_READ,
            'read_at' => now(),
        ]);

        return redirect('/messages')
            ->with('success', 'All messages marked as read.');
    }

    /**
     * Display a specific message.
     */
    public function show(Message $message)
    {
        $user = Auth::user();
        abort_unless($user, 403);
        $user->assertTenantGuestMayUseMessages();
        $currentTenant = Tenant::current();

        if ($currentTenant && (int) $message->tenant_id !== (int) $currentTenant->id) {
            abort(404);
        }

        // Check if user is sender or receiver
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403);
        }

        // Mark as read if user is receiver
        if ($message->receiver_id === $user->id && $message->is_unread) {
            $message->markAsRead();
        }

        // Get conversation thread
        $thread = Message::where(function ($query) use ($message) {
            $query->where(function ($q) use ($message) {
                $q->where('sender_id', $message->sender_id)
                    ->where('receiver_id', $message->receiver_id);
            })->orWhere(function ($q) use ($message) {
                $q->where('sender_id', $message->receiver_id)
                    ->where('receiver_id', $message->sender_id);
            });
        })
            ->when($currentTenant, fn ($query) => $query->where('tenant_id', $currentTenant->id))
            ->where('id', '!=', $message->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        $canDeleteConversation = $this->userCanDeleteTenantMessage($user, $message, $currentTenant);

        return view('messages.show', compact('message', 'thread', 'canDeleteConversation'));
    }

    /**
     * Send a new message.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $user->assertTenantGuestMayUseMessages();
        $currentTenant = $this->currentTenantOrActivateForMessaging($user);
        $this->assertTenantAdminHasPermission($user, $currentTenant, User::PERM_MESSAGES_MANAGE);

        if ($request->filled('recipient_key')) {
            abort_unless($currentTenant, 404);
            if ($this->userIsTenantManager($user, $currentTenant)) {
            return $this->storeTenantManagerOutbound($request, $user, $currentTenant);
            }
            if ($user->isClient()
                && (int) ($user->tenant_id ?? 0) === (int) $currentTenant->id) {
                return $this->storeClientToTeamOutbound($request, $user, $currentTenant);
            }

            abort(403);
        }

        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'booking_id' => 'nullable|exists:bookings,id',
            'type' => 'nullable|in:general,booking_inquiry,booking_response,complaint,feedback',
        ]);

        $validated['sender_id'] = $user->id;
        $validated['type'] = $validated['type'] ?? Message::TYPE_GENERAL;

        $receiver = User::findOrFail($validated['receiver_id']);

        if (TenantCentralSupportProxyUser::isProxy($receiver)) {
            abort(403);
        }

        if ($user->isClient() && $currentTenant) {
            abort_unless((int) ($user->tenant_id ?? 0) === (int) $currentTenant->id, 403);
            $this->assertClientCanMessageTeamMember($user, $receiver, $currentTenant);
        }

        if ($currentTenant && ! $this->receiverIsReachableInTenant($receiver, $currentTenant)) {
            return back()->withErrors([
                'receiver_id' => 'Selected receiver is not available in this tenant.',
            ]);
        }

        $tenantId = null;

        if (! empty($validated['booking_id'])) {
            $tenantId = Booking::whereKey($validated['booking_id'])->value('tenant_id');
        }

        if (! $tenantId) {
            $tenantId = $user->tenant_id;
        }

        if ($currentTenant) {
            $tenantId = $currentTenant->id;
        }

        $validated['tenant_id'] = $tenantId;

        $message = Message::create($validated);

        return redirect('/messages/'.$message->getKey())
            ->with('success', 'Message sent successfully!');
    }

    private function storeTenantManagerOutbound(Request $request, User $user, Tenant $currentTenant): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_key' => ['required', 'string'],
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        $key = $validated['recipient_key'];

        if ($key === 'central') {
            $receiver = TenantCentralSupportProxyUser::ensure($currentTenant);
        } elseif (preg_match('/^user:(\d+)$/', $key, $m)) {
            $receiver = User::findOrFail((int) $m[1]);
            $this->assertTenantManagerCanMessageRecipient($user, $receiver, $currentTenant);
        } else {
            return back()->withErrors(['recipient_key' => 'Choose a valid recipient.'])->withInput();
        }

        if (! TenantCentralSupportProxyUser::isProxy($receiver)
            && ! $this->receiverIsReachableInTenant($receiver, $currentTenant)) {
            return back()->withErrors(['recipient_key' => 'Selected recipient is not in this tenant.'])->withInput();
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'tenant_id' => $currentTenant->id,
            'subject' => $validated['subject'] ?? null,
            'content' => $validated['content'],
            'type' => Message::TYPE_GENERAL,
        ]);

        if (TenantCentralSupportProxyUser::isProxy($receiver)) {
            $notify = config('impastay.central_support_notify_email');
            if (filled($notify)) {
                try {
                    $body = "Tenant: {$currentTenant->name} (ID {$currentTenant->id})\n"
                        ."From: {$user->name} <{$user->email}>\n\n"
                        .($validated['subject'] ? "Subject: {$validated['subject']}\n\n" : '')
                        .$validated['content'];
                    Mail::raw($body, function ($mail) use ($currentTenant, $notify): void {
                        $mail->to($notify)
                            ->subject('[ImpaStay] Message from tenant: '.$currentTenant->name);
                    });
                } catch (\Throwable) {
                    // Avoid failing the request if mail is misconfigured.
                }
            }
        }

        return redirect('/messages/'.$message->getKey())
            ->with('success', 'Message sent successfully!');
    }

    private function storeClientToTeamOutbound(Request $request, User $user, Tenant $currentTenant): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_key' => ['required', 'string'],
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        $key = $validated['recipient_key'];
        if ($key === 'central') {
            abort(403);
        }

        if (! preg_match('/^user:(\d+)$/', $key, $m)) {
            return back()->withErrors(['recipient_key' => 'Choose a valid recipient.'])->withInput();
        }

        $receiver = User::findOrFail((int) $m[1]);
        $this->assertClientCanMessageTeamMember($user, $receiver, $currentTenant);

        if (! $this->receiverIsReachableInTenant($receiver, $currentTenant)) {
            return back()->withErrors(['recipient_key' => 'Selected recipient is not available in this tenant.'])->withInput();
        }

        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'tenant_id' => $currentTenant->id,
            'subject' => $validated['subject'] ?? null,
            'content' => $validated['content'],
            'type' => Message::TYPE_GENERAL,
        ]);

        return redirect('/messages?partner='.(int) $receiver->id)
            ->with('success', 'Message sent successfully!');
    }

    private function assertClientCanMessageTeamMember(User $client, User $receiver, Tenant $tenant): void
    {
        abort_unless($client->isClient(), 403);
        abort_if(TenantCentralSupportProxyUser::isProxy($receiver), 403);
        abort_if((int) $client->id === (int) $receiver->id, 403);

        $adminInTenant = $receiver->isAdmin()
            && (int) ($receiver->tenant_id ?? 0) === (int) $tenant->id;

        $ownerForTenant = $receiver->isOwner() && (
            (int) ($receiver->tenant_id ?? 0) === (int) $tenant->id
            || (int) optional($receiver->ownedTenant)->id === (int) $tenant->id
            || $this->tenantIsOwnedBySamePerson($receiver, $tenant)
        );

        abort_unless($adminInTenant || $ownerForTenant, 403, 'You can only message the property owner or an administrator for this business.');
    }

    private function receiverIsReachableInTenant(User $receiver, Tenant $tenant): bool
    {
        if (TenantCentralSupportProxyUser::isProxy($receiver)) {
            return true;
        }

        return (int) ($receiver->tenant_id ?? 0) === (int) $tenant->id
            || $this->tenantIsOwnedBySamePerson($receiver, $tenant)
            || (int) optional($receiver->ownedTenant)->id === (int) $tenant->id;
    }

    /**
     * Use Spatie’s current tenant when the host is tenant-scoped; otherwise resolve the business
     * from the owner or tenant-admin profile and activate it so tenant DB User/Message queries work.
     */
    private function currentTenantOrActivateForMessaging(?User $user): ?Tenant
    {
        $current = Tenant::current();
        if ($current) {
            return $current;
        }

        if (! $user) {
            return null;
        }

        $resolved = $this->resolveTenantForMessageComposer($user);
        if ($resolved) {
            app(MakeTenantCurrentAction::class)->execute($resolved);

            return Tenant::current();
        }

        return null;
    }

    /**
     * When the request is not on a tenant host (no current tenant), resolve the landlord tenant
     * from the owner or tenant-admin profile so compose can run and tenant DB queries work.
     */
    private function resolveTenantForMessageComposer(?User $user): ?Tenant
    {
        if (! $user) {
            return null;
        }

        if ($user->isOwner()) {
            $owned = $user->relationLoaded('ownedTenant')
                ? $user->ownedTenant
                : $user->ownedTenant()->first();

            if ($owned instanceof Tenant) {
                return $owned;
            }

            if ($user->tenant_id) {
                return Tenant::query()->find($user->tenant_id);
            }

            if ($user->email) {
                $landlordOwnerUser = User::on($this->landlordConnection())
                    ->where('email', $user->email)
                    ->where('role', User::ROLE_OWNER)
                    ->first();

                if ($landlordOwnerUser) {
                    $fromLandlordOwner = Tenant::query()->where('owner_user_id', $landlordOwnerUser->id)->first();
                    if ($fromLandlordOwner instanceof Tenant) {
                        return $fromLandlordOwner;
                    }
                }
            }

            return null;
        }

        if ($user->isAdmin() && $user->tenant_id) {
            return Tenant::query()->find($user->tenant_id);
        }

        return null;
    }

    private function userIsTenantManager(?User $user, ?Tenant $currentTenant): bool
    {
        if (! $user || ! $currentTenant) {
            return false;
        }

        if ($user->isOwner()) {
            $ownsTenant = (int) ($user->tenant_id ?? 0) === (int) $currentTenant->id
                || (int) optional($user->ownedTenant)->id === (int) $currentTenant->id
                || $this->tenantIsOwnedBySamePerson($user, $currentTenant);

            if ($ownsTenant) {
                return true;
            }
        }

        if ($user->isAdmin()) {
            return $user->tenant_id === null || (int) $user->tenant_id === (int) $currentTenant->id;
        }

        return false;
    }

    private function assertTenantManagerCanMessageRecipient(User $actor, User $receiver, Tenant $tenant): void
    {
        abort_if(TenantCentralSupportProxyUser::isProxy($receiver), 403);
        abort_if((int) $receiver->id === (int) $actor->id, 403);

        $receiverBelongsToTenant = (int) ($receiver->tenant_id ?? 0) === (int) $tenant->id
            || $this->tenantIsOwnedBySamePerson($receiver, $tenant);

        abort_unless($receiverBelongsToTenant, 403);

        $allowed = $receiver->isClient()
            || ($receiver->isAdmin() && ($receiver->tenant_id === null || (int) $receiver->tenant_id === (int) $tenant->id))
            || ($receiver->isOwner() && (
                (int) ($receiver->tenant_id ?? 0) === (int) $tenant->id
                || (int) optional($receiver->ownedTenant)->id === (int) $tenant->id
                || $this->tenantIsOwnedBySamePerson($receiver, $tenant)
            ));

        abort_unless($allowed, 403, 'You can only message clients or team members in your business.');
    }

    private function landlordConnection(): string
    {
        return (string) config('multitenancy.landlord_database_connection_name', config('database.default'));
    }

    private function otherPartyUserId(Message $message, int $viewerId): int
    {
        return (int) $message->sender_id === $viewerId
            ? (int) $message->receiver_id
            : (int) $message->sender_id;
    }

    private function userCanDeleteTenantMessage(User $user, Message $message, ?Tenant $currentTenant): bool
    {
        $uid = (int) $user->id;
        if ((int) $message->sender_id === $uid || (int) $message->receiver_id === $uid) {
            return true;
        }

        if (! $currentTenant || ! $message->tenant_id
            || (int) $message->tenant_id !== (int) $currentTenant->id) {
            return false;
        }

        return $user->isOwner() || $user->isAdmin();
    }

    private function deletePairwiseConversation(Message $anchor, ?Tenant $tenantScope): void
    {
        $query = Message::query()
            ->where(function ($outer) use ($anchor): void {
                $outer->where(function ($q) use ($anchor): void {
                    $q->where('sender_id', $anchor->sender_id)
                        ->where('receiver_id', $anchor->receiver_id);
                })->orWhere(function ($q) use ($anchor): void {
                    $q->where('sender_id', $anchor->receiver_id)
                        ->where('receiver_id', $anchor->sender_id);
                });
            });

        if ($tenantScope) {
            $query->where('tenant_id', $tenantScope->id);
        } elseif ($anchor->tenant_id) {
            $query->where('tenant_id', $anchor->tenant_id);
        } else {
            $query->whereNull('tenant_id');
        }

        $query->delete();
    }

    /**
     * @return Collection<int, Message>
     */
    private function conversationBetween(int $userId, int $counterpartId, ?int $tenantId): Collection
    {
        return Message::query()
            ->where(function ($q) use ($userId, $counterpartId): void {
                $q->where(function ($x) use ($userId, $counterpartId): void {
                    $x->where('sender_id', $userId)->where('receiver_id', $counterpartId);
                })->orWhere(function ($x) use ($userId, $counterpartId): void {
                    $x->where('sender_id', $counterpartId)->where('receiver_id', $userId);
                });
            })
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->with(['sender', 'receiver'])
            ->orderBy('created_at')
            ->get();
    }

    private function assertTenantAdminHasPermission(User $user, ?Tenant $tenant, string $permission): void
    {
        if (! $tenant || ! $user->isAdmin()) {
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

    /**
     * Tenant DB owner rows may not match tenants.owner_user_id (landlord user id); match by email instead.
     */
    private function tenantIsOwnedBySamePerson(User $user, Tenant $tenant): bool
    {
        if (! $user->isOwner() || ! $user->email || ! $tenant->owner_user_id) {
            return false;
        }

        $landlordOwner = User::on($this->landlordConnection())->find($tenant->owner_user_id);

        if (! $landlordOwner) {
            return false;
        }

        return strcasecmp((string) $landlordOwner->email, (string) $user->email) === 0;
    }

    /**
     * Reply to a message.
     */
    public function reply(Request $request, Message $message)
    {
        $user = $request->user();
        $user->assertTenantGuestMayUseMessages();
        $currentTenant = Tenant::current();
        $this->assertTenantAdminHasPermission($user, $currentTenant, User::PERM_MESSAGES_MANAGE);

        if ($currentTenant && (int) $message->tenant_id !== (int) $currentTenant->id) {
            abort(404);
        }

        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        // Create reply
        $message->reply($validated['content'], $user);

        return back()->with('success', 'Reply sent successfully!');
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(Message $message)
    {
        $user = Auth::user();
        abort_unless($user, 403);
        $user->assertTenantGuestMayUseMessages();
        $currentTenant = Tenant::current();
        $this->assertTenantAdminHasPermission($user, $currentTenant, User::PERM_MESSAGES_MANAGE);

        if ($currentTenant && (int) $message->tenant_id !== (int) $currentTenant->id) {
            abort(404);
        }

        if (Auth::id() === $message->receiver_id) {
            $message->markAsRead();
        }

        return back();
    }

    /**
     * Archive a message.
     */
    public function archive(Message $message)
    {
        $user = Auth::user();
        abort_unless($user, 403);
        $user->assertTenantGuestMayUseMessages();
        $currentTenant = Tenant::current();
        $this->assertTenantAdminHasPermission($user, $currentTenant, User::PERM_MESSAGES_MANAGE);

        if ($currentTenant && (int) $message->tenant_id !== (int) $currentTenant->id) {
            abort(404);
        }

        if (Auth::id() === $message->receiver_id || Auth::id() === $message->sender_id) {
            $message->archive();
        }

        return redirect('/messages')
            ->with('success', 'Message archived.');
    }

    /**
     * Delete an entire conversation (all rows between the two participants in this tenant scope).
     */
    public function destroy(Message $message): RedirectResponse
    {
        $currentTenant = Tenant::current();

        if ($currentTenant && (int) $message->tenant_id !== (int) $currentTenant->id) {
            abort(404);
        }

        $user = Auth::user();
        abort_unless($user, 403);
        $user->assertTenantGuestMayUseMessages();
        $this->assertTenantAdminHasPermission($user, $currentTenant, User::PERM_MESSAGES_MANAGE);
        abort_unless($this->userCanDeleteTenantMessage($user, $message, $currentTenant), 403);

        $this->deletePairwiseConversation($message, $currentTenant);

        return redirect('/messages')
            ->with('success', 'Conversation deleted.');
    }

    /**
     * Get unread messages count (API).
     */
    public function unreadCount()
    {
        $currentTenant = Tenant::current();

        $count = Message::where('receiver_id', Auth::id())
            ->when($currentTenant, fn ($query) => $query->where('tenant_id', $currentTenant->id))
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Landlord central admin: inbox of all tenant threads with ImpaStay (proxy user).
     */
    public function adminIndex(Request $request)
    {
        $this->assertLandlordCentralMessagingAdmin($request);

        $paginator = app(CentralSupportInboxService::class)->paginateInbox($request);
        $tenants = Tenant::query()
            ->where('database_provisioned', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.messages.index', [
            'paginator' => $paginator,
            'tenants' => $tenants,
        ]);
    }

    /**
     * Landlord central admin: open a support thread inside a tenant database.
     */
    public function adminShow(Request $request, Tenant $tenant, int $message): View
    {
        $this->assertLandlordCentralMessagingAdmin($request);
        abort_unless($tenant->database_provisioned, 404);

        app(MakeTenantCurrentAction::class)->execute($tenant);

        try {
            $proxy = TenantCentralSupportProxyUser::ensure($tenant);
            $messageModel = Message::with(['sender', 'receiver', 'booking.accommodation'])->findOrFail($message);

            abort_unless(
                (int) $messageModel->tenant_id === (int) $tenant->id
                    && (
                        (int) $messageModel->sender_id === (int) $proxy->id
                        || (int) $messageModel->receiver_id === (int) $proxy->id
                    ),
                404
            );

            if ($messageModel->receiver_id === $proxy->id && $messageModel->is_unread) {
                $messageModel->markAsRead();
            }

            $threadMessages = Message::where(function ($query) use ($messageModel): void {
                $query->where(function ($q) use ($messageModel): void {
                    $q->where('sender_id', $messageModel->sender_id)
                        ->where('receiver_id', $messageModel->receiver_id);
                })->orWhere(function ($q) use ($messageModel): void {
                    $q->where('sender_id', $messageModel->receiver_id)
                        ->where('receiver_id', $messageModel->sender_id);
                });
            })
                ->where('tenant_id', $tenant->id)
                ->where('id', '!=', $messageModel->id)
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'asc')
                ->get();

            $timeline = $threadMessages->concat([$messageModel])->sortBy('created_at')->values();

            return view('admin.messages.show', [
                'tenant' => $tenant,
                'message' => $messageModel,
                'timeline' => $timeline,
                'proxy' => $proxy,
            ]);
        } finally {
            app(ForgetCurrentTenantAction::class)->execute($tenant);
        }
    }

    /**
     * Landlord central admin: reply as ImpaStay (proxy) in a tenant thread.
     */
    public function adminReply(Request $request, Tenant $tenant, int $message): RedirectResponse
    {
        $this->assertLandlordCentralMessagingAdmin($request);
        abort_unless($tenant->database_provisioned, 404);

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        app(MakeTenantCurrentAction::class)->execute($tenant);

        try {
            $proxy = TenantCentralSupportProxyUser::ensure($tenant);
            $messageModel = Message::findOrFail($message);

            abort_unless(
                (int) $messageModel->tenant_id === (int) $tenant->id
                    && (
                        (int) $messageModel->sender_id === (int) $proxy->id
                        || (int) $messageModel->receiver_id === (int) $proxy->id
                    ),
                404
            );

            $reply = $messageModel->reply($validated['content'], $proxy);

            return redirect()->route('admin.messages.thread', [
                'tenant' => $tenant->getKey(),
                'message' => $reply->getKey(),
            ])->with('success', 'Reply sent.');
        } finally {
            app(ForgetCurrentTenantAction::class)->execute($tenant);
        }
    }

    /**
     * Landlord central admin: delete an entire ImpaStay (proxy) support thread in the tenant database.
     */
    public function adminDestroy(Request $request, Tenant $tenant, int $message): RedirectResponse
    {
        $this->assertLandlordCentralMessagingAdmin($request);
        abort_unless($tenant->database_provisioned, 404);

        app(MakeTenantCurrentAction::class)->execute($tenant);

        try {
            $proxy = TenantCentralSupportProxyUser::ensure($tenant);
            $messageModel = Message::findOrFail($message);

            abort_unless(
                (int) $messageModel->tenant_id === (int) $tenant->id
                    && (
                        (int) $messageModel->sender_id === (int) $proxy->id
                        || (int) $messageModel->receiver_id === (int) $proxy->id
                    ),
                404
            );

            $this->deletePairwiseConversation($messageModel, $tenant);

            return redirect()->route('admin.messages', [], false)
                ->with('success', 'Conversation deleted.');
        } finally {
            app(ForgetCurrentTenantAction::class)->execute($tenant);
        }
    }

    /**
     * Landlord central admin: start a thread to a tenant user (sender = proxy).
     */
    public function adminContactUser(Request $request): RedirectResponse
    {
        $this->assertLandlordCentralMessagingAdmin($request);

        $validated = $request->validate([
            'tenant_id' => 'required|integer',
            'recipient_email' => 'required|email',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        $tenant = Tenant::query()
            ->whereKey($validated['tenant_id'])
            ->where('database_provisioned', true)
            ->firstOrFail();

        app(MakeTenantCurrentAction::class)->execute($tenant);

        try {
            $recipient = User::query()
                ->where('tenant_id', $tenant->id)
                ->where('email', $validated['recipient_email'])
                ->first();

            if (! $recipient) {
                return back()
                    ->withErrors(['recipient_email' => 'No user with that email exists for this tulogan.'])
                    ->withInput();
            }

            abort_if(TenantCentralSupportProxyUser::isProxy($recipient), 422);

            $proxy = TenantCentralSupportProxyUser::ensure($tenant);

            $message = Message::create([
                'sender_id' => $proxy->id,
                'receiver_id' => $recipient->id,
                'tenant_id' => $tenant->id,
                'subject' => $validated['subject'] ?? null,
                'content' => $validated['content'],
                'type' => Message::TYPE_GENERAL,
            ]);

            return redirect()->route('admin.messages.thread', [
                'tenant' => $tenant->getKey(),
                'message' => $message->getKey(),
            ])->with('success', 'Message sent to '.$recipient->name.'.');
        } finally {
            app(ForgetCurrentTenantAction::class)->execute($tenant);
        }
    }

    private function assertLandlordCentralMessagingAdmin(Request $request): void
    {
        $user = $request->user();
        abort_unless($user && $user->isAdmin() && $user->tenant_id === null, 403);
    }
}
