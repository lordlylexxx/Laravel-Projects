<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with sales monitoring analytics.
     */
    public function index()
    {
        // Current date range
        $now = now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();
        $lastYearStart = $now->copy()->subYear()->startOfYear();
        $lastYearEnd = $now->copy()->subYear()->endOfYear();

        // ============ REVENUE METRICS ============
        $totalRevenue = Booking::whereIn('status', ['confirmed', 'completed', 'paid'])->sum('total_price');
        
        $weeklyRevenue = Booking::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['confirmed', 'completed', 'paid'])
            ->sum('total_price');
        
        $monthlyRevenue = Booking::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('status', ['confirmed', 'completed', 'paid'])
            ->sum('total_price');
        
        $yearlyRevenue = Booking::whereBetween('created_at', [$startOfYear, $endOfYear])
            ->whereIn('status', ['confirmed', 'completed', 'paid'])
            ->sum('total_price');
        
        // Growth rate calculation
        $lastMonthRevenue = Booking::whereBetween('created_at', [
            $now->copy()->subMonth()->startOfMonth(),
            $now->copy()->subMonth()->endOfMonth()
        ])
            ->whereIn('status', ['confirmed', 'completed', 'paid'])
            ->sum('total_price');
        
        $growthRate = $lastMonthRevenue > 0 
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // ============ BOOKING METRICS ============
        $totalBookings = Booking::count();
        $activeClients = User::clients()->where('is_active', true)->count();
        
        // Calculate occupancy rate
        $totalAccommodations = Accommodation::count();
        $occupancyRate = $this->calculateOccupancyRate($startOfMonth, $endOfMonth);

        // ============ TOP PERFORMING UNIT ============
        $topProperty = Booking::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('status', ['confirmed', 'completed', 'paid'])
            ->select('accommodation_id', DB::raw('sum(total_price) as revenue'))
            ->groupBy('accommodation_id')
            ->orderByDesc('revenue')
            ->with('accommodation')
            ->first();

        // ============ MONTHLY CHART DATA ============
        $monthlyRevenueData = [];
        $monthlyBookingsData = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthStart = $now->copy()->month($i)->startOfMonth();
            $monthEnd = $now->copy()->month($i)->endOfMonth();
            
            $monthKey = strtolower($monthStart->format('M'));
            $monthlyRevenueData[$monthKey] = Booking::whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereIn('status', ['confirmed', 'completed', 'paid'])
                ->sum('total_price');
            
            $monthlyBookingsData[$monthKey] = Booking::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        }

        // ============ REVENUE BY PROPERTY TYPE ============
        $revenueByType = [];
        foreach (['traveller-inn', 'airbnb', 'daily-rental'] as $type) {
            $revenueByType[$type] = Booking::whereHas('accommodation', function($query) use ($type) {
                $query->where('type', $type);
            })
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('status', ['confirmed', 'completed', 'paid'])
            ->sum('total_price');
        }

        // ============ KPI SUMMARY ============
        $kpis = [
            'total_users' => User::count(),
            'total_accommodations' => $totalAccommodations,
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'active_clients' => $activeClients,
            'verified_properties' => Accommodation::where('is_verified', true)->count(),
            'average_booking_value' => Booking::whereIn('status', ['confirmed', 'completed', 'paid'])->avg('total_price') ?? 0,
        ];

        // ============ RECENT ACTIVITY ============
        $recentBookings = Booking::with(['client', 'accommodation'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'weeklyRevenue', 'monthlyRevenue', 'yearlyRevenue', 'totalRevenue',
            'totalBookings', 'activeClients', 'occupancyRate', 'topProperty', 'growthRate',
            'monthlyRevenueData', 'monthlyBookingsData', 'revenueByType',
            'kpis', 'recentBookings'
        ));
    }

    /**
     * Calculate occupancy rate for a date range.
     */
    private function calculateOccupancyRate($startDate, $endDate)
    {
        $totalAccommodations = Accommodation::count();
        if ($totalAccommodations === 0) return 0;

        $days = $startDate->diffInDays($endDate) + 1;
        $totalCapacity = $totalAccommodations * $days;

        $bookedNights = Booking::whereBetween('check_in_date', [$startDate, $endDate])
            ->orWhereBetween('check_out_date', [$startDate, $endDate])
            ->whereIn('status', ['confirmed', 'completed', 'paid'])
            ->get()
            ->sum(function ($booking) use ($startDate, $endDate) {
                $checkIn = max($booking->check_in_date, $startDate);
                $checkOut = min($booking->check_out_date, $endDate);
                return $checkIn->diffInDays($checkOut) + 1;
            });

        return $totalCapacity > 0 ? round(($bookedNights / $totalCapacity) * 100, 1) : 0;
    }

    /**
     * Display all tenants (admin).
     */
    public function tenants()
    {
        $tenants = Tenant::query()
            ->with('owner:id,name,email')
            ->orderBy('name')
            ->paginate(15);

        $databaseUsageMbByDatabase = collect();
        $tenantDatabases = $tenants->getCollection()
            ->pluck('database')
            ->filter()
            ->unique()
            ->values();

        if ($tenantDatabases->isNotEmpty()) {
            try {
                $databaseUsageMbByDatabase = DB::connection('landlord')
                    ->table('information_schema.tables')
                    ->selectRaw('table_schema as database_name, ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size_mb')
                    ->whereIn('table_schema', $tenantDatabases)
                    ->groupBy('table_schema')
                    ->pluck('size_mb', 'database_name');
            } catch (\Throwable $exception) {
                $databaseUsageMbByDatabase = collect();
            }
        }

        return view('admin.tenants', compact('tenants', 'databaseUsageMbByDatabase'));
    }

    public function users(): RedirectResponse
    {
        return redirect()->route('admin.tenants');
    }

    /**
     * Display all bookings (admin).
     */
    public function bookings()
    {
        $bookings = Booking::with(['client', 'accommodation'])
            ->latest()
            ->paginate(10);
        
        return view('admin.bookings', compact('bookings'));
    }

    public function updateTenantPlan(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'plan' => ['required', 'in:' . implode(',', [Tenant::PLAN_BASIC, Tenant::PLAN_PLUS, Tenant::PLAN_PRO])],
        ]);

        $planChanged = $tenant->plan !== $validated['plan'];

        $updates = [
            'plan' => $validated['plan'],
        ];

        if ($planChanged) {
            $updates['subscription_status'] = 'active';
            $updates['current_period_starts_at'] = now();
            $updates['current_period_ends_at'] = now()->addMonth();
            $updates['trial_ends_at'] = null;
        }

        $tenant->update([
            ...$updates,
        ]);

        $message = $planChanged
            ? "Plan and subscription updated for {$tenant->name}."
            : "Plan already set for {$tenant->name}.";

        return back()->with('success', $message);
    }

    public function toggleTenantDomain(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'domain_enabled' => ['required', 'boolean'],
        ]);

        $enabled = (bool) $validated['domain_enabled'];

        $tenant->update([
            'domain_enabled' => $enabled,
            'domain_disabled_at' => $enabled ? null : now(),
        ]);

        $state = $enabled ? 'enabled' : 'disabled';

        return back()->with('success', "Tenant domain {$state} for {$tenant->name}.");
    }
}

