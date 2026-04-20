<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

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

            $properties = (clone $propertiesQuery)->withCount('bookings')->latest()->paginate(5, ['*'], 'units_page');
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

            $properties = $user->accommodations()->withCount('bookings')->latest()->paginate(5, ['*'], 'units_page');
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
                'is_pro' => in_array((string) $dashboardTenant->plan, [Tenant::PLAN_PRO, Tenant::PLAN_PROMO], true) && $dashboardTenant->hasActiveSubscription(),
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

        $ownerId = $isTenantAdmin ? null : $user->id;
        [$trendLabels, $revenueTrend, $bookingsTrend] = $this->buildMonthlyTrendData($dashboardTenant?->id, $ownerId);
        $bookingStatusBreakdown = $this->buildBookingStatusBreakdown($dashboardTenant?->id, $ownerId);

        return view('owner.dashboard', compact(
            'stats',
            'properties',
            'recent_bookings',
            'unread_messages',
            'proFeatures',
            'trendLabels',
            'revenueTrend',
            'bookingsTrend',
            'bookingStatusBreakdown'
        ));
    }

    private function buildMonthlyTrendData(?int $tenantId, ?int $ownerId): array
    {
        // Show 1-month trend using daily points from the last 30 days.
        $days = collect(range(29, 0))->map(function (int $offset) {
            return Carbon::now()->subDays($offset)->startOfDay();
        })->push(Carbon::now()->startOfDay());

        $labels = $days->map(fn (Carbon $day) => $day->format('M d'));

        $baseQuery = Booking::query()->whereIn('status', [
            Booking::STATUS_CONFIRMED,
            Booking::STATUS_PAID,
            Booking::STATUS_COMPLETED,
        ]);

        if ($tenantId) {
            $baseQuery->forTenant($tenantId);
        } elseif ($ownerId) {
            $baseQuery->forOwner($ownerId);
        }

        $groupedRevenue = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as trend_date, SUM(total_price) as total_revenue')
            ->groupBy('trend_date')
            ->pluck('total_revenue', 'trend_date');

        $groupedBookings = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as trend_date, COUNT(*) as total_bookings')
            ->groupBy('trend_date')
            ->pluck('total_bookings', 'trend_date');

        $revenueTrend = $days->map(function (Carbon $day) use ($groupedRevenue) {
            return (float) ($groupedRevenue[$day->toDateString()] ?? 0);
        });

        $bookingsTrend = $days->map(function (Carbon $day) use ($groupedBookings) {
            return (int) ($groupedBookings[$day->toDateString()] ?? 0);
        });

        return [$labels->values()->all(), $revenueTrend->values()->all(), $bookingsTrend->values()->all()];
    }

    private function buildBookingStatusBreakdown(?int $tenantId, ?int $ownerId): array
    {
        $query = Booking::query();

        if ($tenantId) {
            $query->forTenant($tenantId);
        } elseif ($ownerId) {
            $query->forOwner($ownerId);
        }

        $counts = $query
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'pending' => (int) ($counts[Booking::STATUS_PENDING] ?? 0),
            'confirmed' => (int) ($counts[Booking::STATUS_CONFIRMED] ?? 0),
            'paid' => (int) ($counts[Booking::STATUS_PAID] ?? 0),
            'completed' => (int) ($counts[Booking::STATUS_COMPLETED] ?? 0),
            'cancelled' => (int) ($counts[Booking::STATUS_CANCELLED] ?? 0),
        ];
    }

    public function monthlyReport(Request $request)
    {
        $this->assertTenantAdminHasPermission($request, User::PERM_REPORTS_VIEW);

        $tenantId = $this->resolveManagedTenantId($request);

        if (! $tenantId) {
            return redirect('/owner/dashboard')->with('error', 'Tenant report is not available for this account.');
        }

        $year = max(2020, min((int) $request->input('year', now()->year), (int) now()->year));
        $month = max(1, min((int) $request->input('month', now()->month), 12));

        $report = $this->buildMonthlyTenantReport($tenantId, $year, $month);

        return view('owner.reports.monthly', [
            'year' => $year,
            'month' => $month,
            'monthName' => Carbon::create($year, $month, 1)->format('F Y'),
            'monthlySales' => $report['monthly_sales'],
            'monthlyGuests' => $report['monthly_guests'],
            'monthlyBookings' => $report['monthly_bookings'],
            'dailyBreakdown' => $report['daily_breakdown'],
        ]);
    }

    public function downloadMonthlySalesPdf(Request $request)
    {
        $this->assertTenantAdminHasPermission($request, User::PERM_REPORTS_VIEW);

        $tenantId = $this->resolveManagedTenantId($request);

        if (! $tenantId) {
            return redirect('/owner/dashboard')->with('error', 'Tenant report is not available for this account.');
        }

        $year = max(2020, min((int) $request->input('year', now()->year), (int) now()->year));
        $month = max(1, min((int) $request->input('month', now()->month), 12));

        $report = $this->buildMonthlyTenantReport($tenantId, $year, $month);
        $monthName = Carbon::create($year, $month, 1)->format('F Y');

        $pdf = \PDF::loadView('owner.reports.monthly-sales-pdf', [
            'year' => $year,
            'month' => $month,
            'monthName' => $monthName,
            'monthlySales' => $report['monthly_sales'],
            'monthlyGuests' => $report['monthly_guests'],
            'monthlyBookings' => $report['monthly_bookings'],
            'dailyBreakdown' => $report['daily_breakdown'],
        ]);

        return $pdf->download('tenant-monthly-sales-report-'.$year.'-'.str_pad((string) $month, 2, '0', STR_PAD_LEFT).'.pdf');
    }

    public function downloadMonthlyGuestsPdf(Request $request)
    {
        $this->assertTenantAdminHasPermission($request, User::PERM_REPORTS_VIEW);

        $tenantId = $this->resolveManagedTenantId($request);

        if (! $tenantId) {
            return redirect('/owner/dashboard')->with('error', 'Tenant report is not available for this account.');
        }

        $year = max(2020, min((int) $request->input('year', now()->year), (int) now()->year));
        $month = max(1, min((int) $request->input('month', now()->month), 12));

        $report = $this->buildMonthlyTenantReport($tenantId, $year, $month);
        $monthName = Carbon::create($year, $month, 1)->format('F Y');

        $pdf = \PDF::loadView('owner.reports.monthly-guests-pdf', [
            'year' => $year,
            'month' => $month,
            'monthName' => $monthName,
            'monthlySales' => $report['monthly_sales'],
            'monthlyGuests' => $report['monthly_guests'],
            'monthlyBookings' => $report['monthly_bookings'],
            'dailyBreakdown' => $report['daily_breakdown'],
        ]);

        return $pdf->download('tenant-monthly-guests-report-'.$year.'-'.str_pad((string) $month, 2, '0', STR_PAD_LEFT).'.pdf');
    }

    private function resolveManagedTenantId(Request $request): ?int
    {
        $user = $request->user();
        $currentTenant = Tenant::current();

        $isTenantAdmin = $user->isAdmin()
            && $currentTenant
            && (int) $user->tenant_id === (int) $currentTenant->id;

        if ($isTenantAdmin) {
            return (int) $currentTenant->id;
        }

        if ($user->isOwner()) {
            return (int) ($user->tenant_id ?? optional($user->tenant)->id ?? 0) ?: null;
        }

        return null;
    }

    private function assertTenantAdminHasPermission(Request $request, string $permission): void
    {
        $user = $request->user();
        $currentTenant = Tenant::current();

        if (! $user || ! $user->isAdmin() || ! $currentTenant) {
            return;
        }

        if ((int) ($user->tenant_id ?? 0) !== (int) $currentTenant->id) {
            return;
        }

        $allowed = $user->hasPermission($permission);
        if (! $allowed) {
            RbacCatalog::ensurePermissionsExist();
            RbacCatalog::ensureRolesAndGrantPermissions();
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $user->syncRbacFromLegacyRole();
            $user->syncEffectiveTenantPermissions($currentTenant);
            $user->refresh();
            $allowed = $user->hasPermission($permission);
        }

        abort_unless($allowed, 403);
    }

    private function buildMonthlyTenantReport(int $tenantId, int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $baseQuery = Booking::query()
            ->forTenant($tenantId)
            ->whereBetween('check_in_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', [
                Booking::STATUS_CONFIRMED,
                Booking::STATUS_PAID,
                Booking::STATUS_COMPLETED,
            ]);

        $monthlySales = (float) (clone $baseQuery)->sum('total_price');
        $monthlyGuests = (int) (clone $baseQuery)->sum('number_of_guests');
        $monthlyBookings = (int) (clone $baseQuery)->count();

        $dailyBreakdown = (clone $baseQuery)
            ->selectRaw('DATE(check_in_date) as report_date, COUNT(*) as booking_count, SUM(number_of_guests) as total_guests, SUM(total_price) as total_sales')
            ->groupBy(DB::raw('DATE(check_in_date)'))
            ->orderBy('report_date')
            ->get();

        return [
            'monthly_sales' => $monthlySales,
            'monthly_guests' => $monthlyGuests,
            'monthly_bookings' => $monthlyBookings,
            'daily_breakdown' => $dailyBreakdown,
        ];
    }
}
