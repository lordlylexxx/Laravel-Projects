<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with analytics.
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

        // ============ WEEKLY INSIGHTS ============
        $weeklyBookings = Booking::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        $weeklyRevenue = Booking::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['confirmed', 'completed', 'paid'])
            ->sum('total_price');
        
        $weeklyMostBooked = Booking::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->select('accommodation_id', DB::raw('count(*) as total'))
            ->groupBy('accommodation_id')
            ->orderByDesc('total')
            ->with('accommodation')
            ->first();
        
        $weeklyOccupancyRate = $this->calculateOccupancyRate($startOfWeek, $endOfWeek);

        // ============ MONTHLY INSIGHTS ============
        $monthlyBookings = Booking::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $monthlyRevenue = Booking::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('status', ['confirmed', 'completed', 'paid'])
            ->sum('total_price');
        
        $lastMonthRevenue = Booking::whereBetween('created_at', [
            $now->copy()->subMonth()->startOfMonth(),
            $now->copy()->subMonth()->endOfMonth()
        ])
            ->whereIn('status', ['confirmed', 'completed', 'paid'])
            ->sum('total_price');
        
        $monthlyGrowthRate = $lastMonthRevenue > 0 
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        $monthlyMostBooked = Booking::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->select('accommodation_id', DB::raw('count(*) as total'))
            ->groupBy('accommodation_id')
            ->orderByDesc('total')
            ->with('accommodation')
            ->first();

        $clientActivity = User::clients()->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        // ============ YEARLY INSIGHTS ============
        $yearlyRevenue = Booking::whereBetween('created_at', [$startOfYear, $endOfYear])
            ->whereIn('status', ['confirmed', 'completed', 'paid'])
            ->sum('total_price');
        
        $lastYearRevenue = Booking::whereBetween('created_at', [$lastYearStart, $lastYearEnd])
            ->whereIn('status', ['confirmed', 'completed', 'paid'])
            ->sum('total_price');
        
        $yearlyGrowthRate = $lastYearRevenue > 0 
            ? round((($yearlyRevenue - $lastYearRevenue) / $lastYearRevenue) * 100, 1)
            : 0;

        $yearlyBookings = Booking::whereBetween('created_at', [$startOfYear, $endOfYear])->count();
        $lastYearBookings = Booking::whereBetween('created_at', [$lastYearStart, $lastYearEnd])->count();

        $yearlyMostProfitable = Booking::whereBetween('created_at', [$startOfYear, $endOfYear])
            ->whereIn('status', ['confirmed', 'completed', 'paid'])
            ->select('accommodation_id', DB::raw('sum(total_price) as revenue'))
            ->groupBy('accommodation_id')
            ->orderByDesc('revenue')
            ->with('accommodation')
            ->first();

        // ============ CHART DATA ============
        // Monthly revenue chart (last 12 months)
        $revenueChartData = [];
        $bookingsChartData = [];
        $monthLabels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = $now->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $now->copy()->subMonths($i)->endOfMonth();
            $monthLabels[] = $monthStart->format('M Y');
            
            $revenueChartData[] = Booking::whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereIn('status', ['confirmed', 'completed', 'paid'])
                ->sum('total_price');
            
            $bookingsChartData[] = Booking::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        }

        // Occupancy distribution by type
        $occupancyByType = Accommodation::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        // ============ KPI SUMMARY ============
        $kpis = [
            'total_users' => User::count(),
            'total_accommodations' => Accommodation::count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Booking::whereIn('status', ['confirmed', 'completed', 'paid'])->sum('total_price'),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'active_owners' => User::owners()->count(),
            'new_clients_this_month' => User::clients()->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'average_booking_value' => Booking::whereIn('status', ['confirmed', 'completed', 'paid'])->avg('total_price') ?? 0,
        ];

        // ============ RECENT ACTIVITY ============
        $recentBookings = Booking::with(['client', 'accommodation'])
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::latest()
            ->take(5)
            ->get();

        $unreadMessages = Message::where('receiver_id', auth()->id())
            ->where('is_unread', true)
            ->count();

        return view('admin.dashboard', compact(
            'weeklyBookings', 'weeklyRevenue', 'weeklyMostBooked', 'weeklyOccupancyRate',
            'monthlyBookings', 'monthlyRevenue', 'monthlyGrowthRate', 'monthlyMostBooked', 'clientActivity',
            'yearlyRevenue', 'yearlyGrowthRate', 'yearlyBookings', 'lastYearBookings', 'yearlyMostProfitable',
            'revenueChartData', 'bookingsChartData', 'monthLabels', 'occupancyByType',
            'kpis', 'recentBookings', 'recentUsers', 'unreadMessages'
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
     * Display all users (admin).
     */
    public function users()
    {
        $users = User::withCount('bookings')
            ->latest()
            ->paginate(10);
        
        return view('admin.users', compact('users'));
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
}

