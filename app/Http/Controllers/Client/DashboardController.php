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

        return view('client.dashboard', compact(
            'featuredAccommodations',
            'unreadMessagesCount',
            'upcomingBookingsCount',
            'pendingBookingsCount',
            'completedBookingsCount',
            'nextUpcomingBooking',
            'ytdSpend',
            'categoryCounts'
        ));
    }
}
