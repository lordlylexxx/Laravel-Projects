<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $currentTenant = Tenant::current();

        if (! $user) {
            return redirect()->route('accommodations.index');
        }

        if ($user->isOwner() || ($user->isAdmin() && $currentTenant && (int) $user->tenant_id === (int) $currentTenant->id)) {
            return redirect()->route('owner.dashboard');
        }

        if (! $user->isClient()) {
            return redirect()->route('accommodations.index');
        }

        $canManageOwnStays = $user->tenantClientMayManageOwnStays();

        if ($canManageOwnStays) {
            $bookingsBaseQuery = Booking::query()
                ->forClient($user->id)
                ->when($currentTenant, fn ($query) => $query->forTenant($currentTenant->id));

            $today = Carbon::today();

            $upcomingBookingsCount = (clone $bookingsBaseQuery)
                ->whereDate('check_out_date', '>=', $today)
                ->whereNotIn('status', [Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETED])
                ->count();

            $pendingBookingsCount = (clone $bookingsBaseQuery)
                ->where('status', Booking::STATUS_PENDING)
                ->count();

            $completedBookingsCount = (clone $bookingsBaseQuery)
                ->where(function ($query) use ($today) {
                    $query->where('status', Booking::STATUS_COMPLETED)
                        ->orWhereDate('check_out_date', '<', $today);
                })
                ->count();

            $nextUpcomingBooking = (clone $bookingsBaseQuery)
                ->with(['accommodation', 'accommodation.owner'])
                ->whereDate('check_out_date', '>=', $today)
                ->whereNotIn('status', [Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETED])
                ->orderBy('check_in_date')
                ->first();

            $ytdSpend = (clone $bookingsBaseQuery)
                ->whereIn('status', [Booking::STATUS_PAID, Booking::STATUS_COMPLETED])
                ->whereDate('created_at', '>=', Carbon::now()->startOfYear())
                ->sum('total_price');
        } else {
            $upcomingBookingsCount = 0;
            $pendingBookingsCount = 0;
            $completedBookingsCount = 0;
            $nextUpcomingBooking = null;
            $ytdSpend = 0;
        }

        $unreadMessagesCount = Message::query()
            ->forUser($user->id)
            ->when($currentTenant, fn ($query) => $query->forTenant($currentTenant->id))
            ->unread()
            ->count();

        $featuredAccommodations = Accommodation::query()
            ->available()
            ->when($currentTenant, fn ($query) => $query->forTenant($currentTenant->id))
            ->with('owner')
            ->latest()
            ->limit(6)
            ->get();

        $typeCounts = Accommodation::query()
            ->available()
            ->when($currentTenant, fn ($query) => $query->forTenant($currentTenant->id))
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $categoryCounts = [
            'traveller-inn' => (int) ($typeCounts['traveller-inn'] ?? 0),
            'airbnb' => (int) ($typeCounts['airbnb'] ?? 0),
            'daily-rental' => (int) ($typeCounts['daily-rental'] ?? 0),
        ];

        $availabilityAccommodations = Accommodation::query()
            ->available()
            ->when($currentTenant, fn ($query) => $query->forTenant($currentTenant->id))
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        $availabilityEventsByAccommodation = [];
        $availabilityAccommodationIds = $availabilityAccommodations->pluck('id')->values()->all();

        if ($availabilityAccommodationIds !== []) {
            $availabilityBookings = Booking::query()
                ->when($currentTenant, fn ($query) => $query->forTenant($currentTenant->id))
                ->whereIn('accommodation_id', $availabilityAccommodationIds)
                ->whereIn('status', [
                    Booking::STATUS_PENDING,
                    Booking::STATUS_CONFIRMED,
                    Booking::STATUS_PAID,
                ])
                ->whereDate('check_out_date', '>=', Carbon::today()->subMonths(1)->toDateString())
                ->get(['accommodation_id', 'check_in_date', 'check_out_date', 'status']);

            $availabilityEventsByAccommodation = $availabilityBookings
                ->groupBy('accommodation_id')
                ->map(function ($rows) {
                    return $rows->map(function ($row) {
                        return [
                            'start' => Carbon::parse($row->check_in_date)->toDateString(),
                            'end' => Carbon::parse($row->check_out_date)->toDateString(),
                            'status' => (string) $row->status,
                        ];
                    })->values()->all();
                })
                ->all();
        }

        return view('client.dashboard', compact(
            'featuredAccommodations',
            'unreadMessagesCount',
            'upcomingBookingsCount',
            'pendingBookingsCount',
            'completedBookingsCount',
            'nextUpcomingBooking',
            'ytdSpend',
            'categoryCounts',
            'canManageOwnStays',
            'availabilityAccommodations',
            'availabilityEventsByAccommodation'
        ));
    }
}
