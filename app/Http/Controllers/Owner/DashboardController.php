<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

            $dashboardTenant = $currentTenant;
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

            $dashboardTenant = $user->tenant;
        }

        $proFeatures = [
            'is_pro' => false,
            'has_advanced_reporting' => false,
            'has_analytics_dashboard' => false,
            'unlimited_listings' => false,
            'priority_support' => false,
            'featured_listing_promotion' => false,
            'advanced_analytics' => false,
            'total_listings' => (int) ($stats['total_properties'] ?? 0),
            'max_listings' => null,
            'featured_listings' => 0,
            'monthly_revenue' => 0,
            'monthly_bookings' => 0,
            'avg_booking_value' => 0,
            'booking_conversion_rate' => 0,
        ];

        if ($dashboardTenant) {
            $tenantId = (int) $dashboardTenant->id;
            $tenantPropertiesQuery = Accommodation::query()->where('tenant_id', $tenantId);
            $tenantBookingsQuery = Booking::query()->forTenant($tenantId);
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $totalBookings = (int) (clone $tenantBookingsQuery)->count();
            $successfulBookings = (int) (clone $tenantBookingsQuery)
                ->whereIn('status', ['confirmed', 'paid', 'completed'])
                ->count();
            $monthlyBookings = (int) (clone $tenantBookingsQuery)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();
            $monthlyRevenue = (float) (clone $tenantBookingsQuery)
                ->whereIn('status', ['confirmed', 'paid', 'completed'])
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('total_price');
            $totalRevenue = (float) (clone $tenantBookingsQuery)
                ->whereIn('status', ['confirmed', 'paid', 'completed'])
                ->sum('total_price');

            $proFeatures = [
                'is_pro' => (string) $dashboardTenant->plan === Tenant::PLAN_PRO && $dashboardTenant->hasActiveSubscription(),
                'has_advanced_reporting' => $dashboardTenant->hasFeature('advanced_reporting'),
                'has_analytics_dashboard' => $dashboardTenant->hasFeature('analytics_dashboard'),
                'unlimited_listings' => is_null($dashboardTenant->maxListings()),
                'priority_support' => $dashboardTenant->hasFeature('priority_support'),
                'featured_listing_promotion' => $dashboardTenant->hasFeature('featured_listings'),
                'advanced_analytics' => $dashboardTenant->hasFeature('analytics_dashboard'),
                'total_listings' => (int) (clone $tenantPropertiesQuery)->count(),
                'max_listings' => $dashboardTenant->maxListings(),
                'featured_listings' => (int) (clone $tenantPropertiesQuery)->where('is_featured', true)->count(),
                'monthly_revenue' => (int) round($monthlyRevenue),
                'monthly_bookings' => $monthlyBookings,
                'avg_booking_value' => $successfulBookings > 0 ? (int) round($totalRevenue / $successfulBookings) : 0,
                'booking_conversion_rate' => $totalBookings > 0 ? round(($successfulBookings / $totalBookings) * 100, 1) : 0,
            ];
        }

        return view('owner.dashboard', compact(
            'stats',
            'properties',
            'recent_bookings',
            'unread_messages',
            'proFeatures'
        ));
    }
}

