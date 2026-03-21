<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the owner dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $currentTenant = Tenant::current();
        $isTenantAdmin = $user->isAdmin()
            && $currentTenant
            && (int) $user->tenant_id === (int) $currentTenant->id;

        if ($isTenantAdmin) {
            $tenantId = $currentTenant->id;

            $propertiesQuery = Accommodation::query()->where('tenant_id', $tenantId);
            $bookingsQuery = Booking::query()->forTenant($tenantId);

            $stats = [
                'total_properties' => (clone $propertiesQuery)->count(),
                'active_properties' => (clone $propertiesQuery)->where('is_available', true)->count(),
                'total_bookings' => (clone $bookingsQuery)->count(),
                'pending_bookings' => (clone $bookingsQuery)->pending()->count(),
                'confirmed_bookings' => (clone $bookingsQuery)->confirmed()->count(),
                'total_earnings' => (clone $bookingsQuery)->whereIn('status', ['confirmed', 'paid', 'completed'])->sum('total_price'),
            ];

            $properties = (clone $propertiesQuery)->withCount('bookings')->latest()->take(5)->get();
            $recent_bookings = (clone $bookingsQuery)->with(['client', 'accommodation'])->latest()->take(5)->get();
            $unread_messages = Message::where('receiver_id', $user->id)
                ->where('tenant_id', $tenantId)
                ->unread()
                ->count();
        } else {
            $stats = [
                'total_properties' => $user->accommodations()->count(),
                'active_properties' => $user->accommodations()->where('is_available', true)->count(),
                'total_bookings' => Booking::forOwner($user->id)->count(),
                'pending_bookings' => Booking::forOwner($user->id)->pending()->count(),
                'confirmed_bookings' => Booking::forOwner($user->id)->confirmed()->count(),
                'total_earnings' => Booking::forOwner($user->id)->whereIn('status', ['confirmed', 'paid', 'completed'])->sum('total_price'),
            ];

            $properties = $user->accommodations()->withCount('bookings')->latest()->take(5)->get();
            $recent_bookings = Booking::forOwner($user->id)->with(['client', 'accommodation'])->latest()->take(5)->get();
            $unread_messages = Message::where('receiver_id', $user->id)->unread()->count();
        }

        return view('owner.dashboard', compact(
            'stats',
            'properties',
            'recent_bookings',
            'unread_messages'
        ));
    }
}

