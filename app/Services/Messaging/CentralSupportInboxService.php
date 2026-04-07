<?php

namespace App\Services\Messaging;

use App\Models\Message;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorConcrete;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Multitenancy\Actions\ForgetCurrentTenantAction;
use Spatie\Multitenancy\Actions\MakeTenantCurrentAction;

/**
 * Aggregates tenant↔central-support threads (proxy user) across all tenant databases
 * for landlord central admins.
 */
final class CentralSupportInboxService
{
    public function unreadTotal(): int
    {
        $total = 0;

        foreach ($this->provisionedTenants() as $tenant) {
            try {
                app(MakeTenantCurrentAction::class)->execute($tenant);
                $proxy = TenantCentralSupportProxyUser::ensure($tenant);
                $total += Message::query()
                    ->where('receiver_id', $proxy->id)
                    ->unread()
                    ->count();
            } catch (\Throwable) {
                // Skip broken or unreachable tenant DBs.
            } finally {
                app(ForgetCurrentTenantAction::class)->execute($tenant);
            }
        }

        return $total;
    }

    /**
     * @return LengthAwarePaginator<int, object>
     */
    public function paginateInbox(Request $request, int $perPage = 25): LengthAwarePaginator
    {
        $rows = new Collection;

        foreach ($this->provisionedTenants() as $tenant) {
            try {
                app(MakeTenantCurrentAction::class)->execute($tenant);
                $proxy = TenantCentralSupportProxyUser::ensure($tenant);

                $latestIds = Message::query()
                    ->where(function ($q) use ($proxy): void {
                        $q->where('sender_id', $proxy->id)
                            ->orWhere('receiver_id', $proxy->id);
                    })
                    ->selectRaw('MAX(id) as aggregate_id')
                    ->groupByRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END', [$proxy->id])
                    ->pluck('aggregate_id');

                if ($latestIds->isEmpty()) {
                    continue;
                }

                $unreadByCounterpart = array_fill_keys(
                    Message::query()
                        ->where('receiver_id', $proxy->id)
                        ->unread()
                        ->pluck('sender_id')
                        ->unique()
                        ->all(),
                    true
                );

                $messages = Message::query()
                    ->with(['sender', 'receiver'])
                    ->whereIn('id', $latestIds)
                    ->get();

                foreach ($messages as $message) {
                    $other = (int) $message->sender_id === (int) $proxy->id
                        ? $message->receiver
                        : $message->sender;
                    $counterpartId = (int) ($other?->id ?? 0);
                    $threadHasUnread = $counterpartId > 0 && ($unreadByCounterpart[$counterpartId] ?? false);

                    $rows->push($this->inboxRow($tenant, $message, $proxy, $threadHasUnread));
                }
            } catch (\Throwable) {
            } finally {
                app(ForgetCurrentTenantAction::class)->execute($tenant);
            }
        }

        $sorted = $rows->sortByDesc(fn (object $r) => $r->created_at->timestamp)->values();
        $page = max(1, (int) $request->get('page', 1));
        $slice = $sorted->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginatorConcrete(
            $slice,
            $sorted->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    private function inboxRow(Tenant $tenant, Message $message, User $proxy, bool $threadHasUnread): object
    {
        $other = (int) $message->sender_id === (int) $proxy->id
            ? $message->receiver
            : $message->sender;

        return (object) [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'message_id' => $message->id,
            'subject' => $message->subject,
            'preview' => Str::limit(strip_tags((string) $message->content), 140),
            'created_at' => $message->created_at,
            'is_unread' => $threadHasUnread,
            'counterpart_name' => $other?->name ?? 'User',
        ];
    }

    /**
     * @return \Illuminate\Support\LazyCollection<int, Tenant>
     */
    private function provisionedTenants()
    {
        return Tenant::query()
            ->where('database_provisioned', true)
            ->orderBy('name')
            ->cursor();
    }
}
