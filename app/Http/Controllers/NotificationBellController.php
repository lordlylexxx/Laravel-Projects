<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\Central\AdminImportantNotification;
use App\Notifications\Tenant\ClientImportantNotification;
use App\Notifications\Tenant\StaffImportantNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class NotificationBellController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $types = $this->allowedTypes($user);

        if ($types === []) {
            return response()->json(['items' => [], 'unread_count' => 0]);
        }

        if (! $this->notificationsTableAvailable($user)) {
            return response()->json([
                'items' => [],
                'unread_count' => 0,
                'schema_pending' => true,
            ]);
        }

        $items = $user->notifications()
            ->whereIn('type', $types)
            ->orderByDesc('created_at')
            ->limit(40)
            ->get()
            ->map(function (DatabaseNotification $notification): array {
                $data = $notification->data;

                return [
                    'id' => $notification->id,
                    'title' => (string) ($data['title'] ?? ''),
                    'body' => (string) ($data['body'] ?? ''),
                    'action_url' => $data['action_url'] ?? null,
                    'action_label' => $data['action_label'] ?? null,
                    'read_at' => $notification->read_at?->toIso8601String(),
                    'created_at' => $notification->created_at?->toIso8601String(),
                ];
            })
            ->values()
            ->all();

        $unreadCount = $user->unreadNotifications()->whereIn('type', $types)->count();

        return response()->json([
            'items' => $items,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $types = $this->allowedTypes($user);

        if (! $this->notificationsTableAvailable($user)) {
            return response()->json(['ok' => true]);
        }

        /** @var DatabaseNotification|null $notification */
        $notification = $user->notifications()->whereKey($id)->first();

        if (! $notification instanceof DatabaseNotification) {
            return response()->json(['ok' => false, 'message' => 'Not found.'], 404);
        }

        if (! in_array($notification->type, $types, true)) {
            return response()->json(['ok' => false, 'message' => 'Forbidden.'], 403);
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $types = $this->allowedTypes($user);

        if ($types === [] || ! $this->notificationsTableAvailable($user)) {
            return response()->json(['ok' => true, 'unread_count' => 0]);
        }

        $updated = $user->notifications()
            ->whereIn('type', $types)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'ok' => true,
            'marked' => $updated,
            'unread_count' => 0,
        ]);
    }

    /**
     * @return list<class-string>
     */
    private function allowedTypes(User $user): array
    {
        if ($user->isClient()) {
            return [ClientImportantNotification::class];
        }

        $landlordConnection = (string) config('multitenancy.landlord_database_connection_name', 'landlord');

        // Tulogans / central admins: rows live on the landlord connection (no tenant_id).
        if ($user->isAdmin() && $user->tenant_id === null && $user->getConnectionName() === $landlordConnection) {
            return [AdminImportantNotification::class];
        }

        // Property owners and per-tenant staff admins (tenant database users).
        if ($user->isOwner() || ($user->isAdmin() && $user->tenant_id !== null)) {
            return [StaffImportantNotification::class];
        }

        return [];
    }

    private function notificationsTableAvailable(User $user): bool
    {
        $connection = $user->getConnectionName();

        try {
            return Schema::connection($connection)->hasTable('notifications');
        } catch (\Throwable $exception) {
            Log::debug('Could not check notifications table.', [
                'connection' => $connection,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
