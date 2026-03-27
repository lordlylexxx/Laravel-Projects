<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TenantDomainStatusChangedMail;
use App\Mail\TenantOnboardingSummaryMail;
use App\Mail\TenantSubscriptionChangedMail;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Tenant;
use App\Models\TenantLifecycleLog;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        // ============ TENANT BOOKINGS FOR TODAY ============
        $tenantBookingsToday = $this->getTenantBookingsForToday();

        return view('admin.dashboard', compact(
            'weeklyRevenue', 'monthlyRevenue', 'yearlyRevenue', 'totalRevenue',
            'totalBookings', 'activeClients', 'occupancyRate', 'topProperty', 'growthRate',
            'monthlyRevenueData', 'monthlyBookingsData', 'revenueByType',
            'kpis', 'recentBookings', 'tenantBookingsToday'
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

        $tenantIds = $tenants->getCollection()->pluck('id')->all();
        $latestLifecycleByTenant = TenantLifecycleLog::query()
            ->whereIn('tenant_id', $tenantIds)
            ->latest('id')
            ->get()
            ->unique('tenant_id')
            ->keyBy('tenant_id');

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

        return view('admin.tenants', compact('tenants', 'databaseUsageMbByDatabase', 'latestLifecycleByTenant'));
    }

    public function users(): RedirectResponse
    {
        return redirect()->route('admin.tenants');
    }

    public function tenantLifecycleLogs(Request $request)
    {
        $query = TenantLifecycleLog::query()
            ->with(['tenant:id,name,slug', 'actor:id,name,email'])
            ->latest();

        if ($request->filled('tenant')) {
            $tenantSearch = trim((string) $request->input('tenant'));
            $query->whereHas('tenant', function ($tenantQuery) use ($tenantSearch) {
                $tenantQuery->where('name', 'like', "%{$tenantSearch}%")
                    ->orWhere('slug', 'like', "%{$tenantSearch}%");
            });
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . trim((string) $request->input('action')) . '%');
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.tenant-lifecycle-logs', compact('logs'));
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
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $oldPlan = (string) $tenant->plan;
        $oldSubscriptionStatus = (string) ($tenant->subscription_status ?? 'trialing');
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

        $this->logLifecycleAction(
            request: $request,
            tenant: $tenant,
            action: 'tenant.plan.updated',
            reason: $validated['reason'],
            before: [
                'plan' => $oldPlan,
                'subscription_status' => $oldSubscriptionStatus,
            ],
            after: [
                'plan' => (string) $tenant->plan,
                'subscription_status' => (string) ($tenant->subscription_status ?? 'trialing'),
            ]
        );

        if ($tenant->owner?->email) {
            try {
                Mail::to($tenant->owner->email)->send(new TenantSubscriptionChangedMail(
                    tenantName: $tenant->name,
                    ownerName: $tenant->owner->name,
                    plan: (string) $tenant->plan,
                    subscriptionStatus: (string) ($tenant->subscription_status ?? 'trialing'),
                    periodEndsAt: $tenant->current_period_ends_at,
                    reason: $validated['reason'],
                    changedBy: (string) ($request->user()?->name ?? 'System')
                ));
            } catch (\Throwable $exception) {
                Log::warning('Failed to send tenant subscription update email.', [
                    'tenant_id' => $tenant->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $message = $planChanged
            ? "Plan and subscription updated for {$tenant->name}."
            : "Plan already set for {$tenant->name}.";

        return back()->with('success', $message);
    }

    public function toggleTenantDomain(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'domain_enabled' => ['required', 'boolean'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $oldDomainEnabled = (bool) ($tenant->domain_enabled ?? true);
        $enabled = (bool) $validated['domain_enabled'];

        if ($oldDomainEnabled === $enabled) {
            return back()->with('success', "Tenant domain already " . ($enabled ? 'enabled' : 'disabled') . " for {$tenant->name}.");
        }

        $tenant->update([
            'domain_enabled' => $enabled,
            'domain_disabled_at' => $enabled ? null : now(),
        ]);

        $this->logLifecycleAction(
            request: $request,
            tenant: $tenant,
            action: 'tenant.domain_status.updated',
            reason: $validated['reason'],
            before: [
                'domain_enabled' => $oldDomainEnabled,
                'domain_disabled_at' => $oldDomainEnabled ? null : optional($tenant->domain_disabled_at)?->toDateTimeString(),
            ],
            after: [
                'domain_enabled' => $enabled,
                'domain_disabled_at' => optional($tenant->domain_disabled_at)?->toDateTimeString(),
            ]
        );

        if ($tenant->owner?->email) {
            try {
                Mail::to($tenant->owner->email)->send(new TenantDomainStatusChangedMail(
                    tenantName: $tenant->name,
                    ownerName: $tenant->owner->name,
                    businessUrl: $tenant->publicUrl(),
                    enabled: $enabled,
                    reason: $validated['reason'],
                    changedBy: (string) ($request->user()?->name ?? 'System')
                ));
            } catch (\Throwable $exception) {
                Log::warning('Failed to send tenant domain status email.', [
                    'tenant_id' => $tenant->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $state = $enabled ? 'enabled' : 'disabled';

        return back()->with('success', "Tenant domain {$state} for {$tenant->name}.");
    }

    public function updateTenantSubscription(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'subscription_status' => ['required', 'in:trialing,active,past_due,cancelled'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $oldSubscriptionStatus = (string) ($tenant->subscription_status ?? 'trialing');
        $newSubscriptionStatus = (string) $validated['subscription_status'];

        if ($oldSubscriptionStatus === $newSubscriptionStatus) {
            return back()->with('success', "Subscription status already {$newSubscriptionStatus} for {$tenant->name}.");
        }

        $tenant->update([
            'subscription_status' => $newSubscriptionStatus,
        ]);

        $this->logLifecycleAction(
            request: $request,
            tenant: $tenant,
            action: 'tenant.subscription_status.updated',
            reason: $validated['reason'],
            before: [
                'subscription_status' => $oldSubscriptionStatus,
            ],
            after: [
                'subscription_status' => $newSubscriptionStatus,
            ]
        );

        if ($tenant->owner?->email) {
            try {
                Mail::to($tenant->owner->email)->send(new TenantSubscriptionChangedMail(
                    tenantName: $tenant->name,
                    ownerName: $tenant->owner->name,
                    plan: (string) $tenant->plan,
                    subscriptionStatus: $newSubscriptionStatus,
                    periodEndsAt: $tenant->current_period_ends_at,
                    reason: $validated['reason'],
                    changedBy: (string) ($request->user()?->name ?? 'System')
                ));
            } catch (\Throwable $exception) {
                Log::warning('Failed to send tenant subscription status email.', [
                    'tenant_id' => $tenant->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return back()->with('success', "Subscription status updated for {$tenant->name}.");
    }

    public function updateTenantProfile(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'app_title' => ['nullable', 'string', 'max:255'],
            'locale' => ['nullable', 'in:en,es,fr,de'],
            'primary_color' => ['nullable', 'regex:/^#[0-9A-F]{6}$/i'],
            'accent_color' => ['nullable', 'regex:/^#[0-9A-F]{6}$/i'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $before = [
            'name' => $tenant->name,
            'app_title' => $tenant->app_title,
            'locale' => $tenant->locale,
            'primary_color' => $tenant->primary_color,
            'accent_color' => $tenant->accent_color,
        ];

        $tenant->update([
            'name' => $validated['name'],
            'app_title' => $validated['app_title'] ?? null,
            'locale' => $validated['locale'] ?? $tenant->locale,
            'primary_color' => $validated['primary_color'] ?? $tenant->primary_color,
            'accent_color' => $validated['accent_color'] ?? $tenant->accent_color,
        ]);

        $this->logLifecycleAction(
            request: $request,
            tenant: $tenant,
            action: 'tenant.profile.updated',
            reason: $validated['reason'],
            before: $before,
            after: [
                'name' => $tenant->name,
                'app_title' => $tenant->app_title,
                'locale' => $tenant->locale,
                'primary_color' => $tenant->primary_color,
                'accent_color' => $tenant->accent_color,
            ]
        );

        return back()->with('success', "Tenant profile updated for {$tenant->name}.");
    }

    public function resendTenantOnboardingEmail(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $tenantAdmin = User::query()
            ->where('tenant_id', $tenant->id)
            ->where('role', User::ROLE_ADMIN)
            ->oldest('id')
            ->first();

        if (! $tenant->owner?->email || ! $tenantAdmin?->email) {
            return back()->with('success', "Unable to resend onboarding email: tenant owner/admin email is missing for {$tenant->name}.");
        }

        try {
            Mail::to($tenant->owner->email)->send(new TenantOnboardingSummaryMail(
                ownerName: $tenant->owner->name,
                tenantName: $tenant->name,
                businessUrl: $tenant->publicUrl(),
                tenantAdminEmail: $tenantAdmin->email,
                resetPasswordUrl: rtrim($tenant->publicUrl(), '/') . '/forgot-password',
                reason: $validated['reason'],
                changedBy: (string) ($request->user()?->name ?? 'System')
            ));
        } catch (\Throwable $exception) {
            Log::warning('Failed to resend tenant onboarding email.', [
                'tenant_id' => $tenant->id,
                'error' => $exception->getMessage(),
            ]);

            return back()->with('success', "Failed to resend onboarding email for {$tenant->name}. Check logs for details.");
        }

        $this->logLifecycleAction(
            request: $request,
            tenant: $tenant,
            action: 'tenant.onboarding_email.resent',
            reason: $validated['reason'],
            before: [
                'owner_email' => $tenant->owner->email,
                'tenant_admin_email' => $tenantAdmin->email,
            ],
            after: [
                'owner_email' => $tenant->owner->email,
                'tenant_admin_email' => $tenantAdmin->email,
                'resent_at' => now()->toDateTimeString(),
            ]
        );

        return back()->with('success', "Onboarding email resent for {$tenant->name}.");
    }

    private function logLifecycleAction(
        Request $request,
        Tenant $tenant,
        string $action,
        ?string $reason,
        array $before,
        array $after
    ): void {
        TenantLifecycleLog::create([
            'tenant_id' => $tenant->id,
            'actor_user_id' => $request->user()?->id,
            'action' => $action,
            'reason' => $reason,
            'before_state' => $before,
            'after_state' => $after,
        ]);
    }

    /**
     * Get tenant bookings for today with guest counts.
     */
    public function getTenantBookingsForToday()
    {
        $today = now()->toDateString();

        $bookingsByTenant = Booking::query()
            ->join('accommodations', 'bookings.accommodation_id', '=', 'accommodations.id')
            ->join('tenants', 'accommodations.tenant_id', '=', 'tenants.id')
            ->whereDate('bookings.check_in_date', '<=', $today)
            ->whereDate('bookings.check_out_date', '>=', $today)
            ->whereIn('bookings.status', ['confirmed', 'completed', 'paid'])
            ->select(
                'tenants.id',
                'tenants.name',
                DB::raw('COUNT(*) as booking_count'),
                DB::raw('SUM(bookings.number_of_guests) as total_guests')
            )
            ->groupBy('tenants.id', 'tenants.name')
            ->orderByDesc('total_guests')
            ->get();

        return $bookingsByTenant;
    }

    /**
     * Generate PDF report for monthly bookings by tenant.
     */
    public function generateMonthlyBookingReport(Request $request, $year = null, $month = null)
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'min:2020', 'max:' . now()->year],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $year = $year ?? $validated['year'] ?? now()->year;
        $month = $month ?? $validated['month'] ?? now()->month;

        // Create date range for the requested month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get bookings data by tenant for the month
        $monthlyData = Booking::query()
            ->join('accommodations', 'bookings.accommodation_id', '=', 'accommodations.id')
            ->join('tenants', 'accommodations.tenant_id', '=', 'tenants.id')
            ->whereBetween('bookings.check_in_date', [$startDate, $endDate])
            ->orWhereBetween('bookings.check_out_date', [$startDate, $endDate])
            ->whereIn('bookings.status', ['confirmed', 'completed', 'paid'])
            ->select(
                'tenants.id',
                'tenants.name',
                'tenants.slug',
                DB::raw('COUNT(*) as booking_count'),
                DB::raw('SUM(bookings.number_of_guests) as total_guests'),
                DB::raw('SUM(bookings.total_price) as total_revenue'),
                DB::raw('AVG(bookings.total_price) as avg_booking_value')
            )
            ->groupBy('tenants.id', 'tenants.name', 'tenants.slug')
            ->orderByDesc('total_guests')
            ->get();

        // Summary statistics
        $summary = [
            'total_bookings' => $monthlyData->sum('booking_count'),
            'total_guests' => $monthlyData->sum('total_guests'),
            'total_revenue' => $monthlyData->sum('total_revenue'),
            'average_guests_per_booking' => $monthlyData->count() > 0 
                ? round($monthlyData->sum('total_guests') / $monthlyData->sum('booking_count'), 2) 
                : 0,
        ];

        $data = [
            'year' => $year,
            'month' => $month,
            'monthName' => $startDate->format('F Y'),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tenantBookings' => $monthlyData,
            'summary' => $summary,
        ];

        return response()->view('admin.reports.monthly-booking-pdf', $data);
    }

    /**
     * Download PDF report for monthly bookings by tenant.
     */
    public function downloadMonthlyBookingPdf(Request $request, $year = null, $month = null)
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'min:2020', 'max:' . now()->year],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $year = $year ?? $validated['year'] ?? now()->year;
        $month = $month ?? $validated['month'] ?? now()->month;

        // Create date range for the requested month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get bookings data by tenant for the month
        $monthlyData = Booking::query()
            ->join('accommodations', 'bookings.accommodation_id', '=', 'accommodations.id')
            ->join('tenants', 'accommodations.tenant_id', '=', 'tenants.id')
            ->whereBetween('bookings.check_in_date', [$startDate, $endDate])
            ->orWhereBetween('bookings.check_out_date', [$startDate, $endDate])
            ->whereIn('bookings.status', ['confirmed', 'completed', 'paid'])
            ->select(
                'tenants.id',
                'tenants.name',
                'tenants.slug',
                DB::raw('COUNT(*) as booking_count'),
                DB::raw('SUM(bookings.number_of_guests) as total_guests'),
                DB::raw('SUM(bookings.total_price) as total_revenue'),
                DB::raw('AVG(bookings.total_price) as avg_booking_value')
            )
            ->groupBy('tenants.id', 'tenants.name', 'tenants.slug')
            ->orderByDesc('total_guests')
            ->get();

        // Summary statistics
        $summary = [
            'total_bookings' => $monthlyData->sum('booking_count'),
            'total_guests' => $monthlyData->sum('total_guests'),
            'total_revenue' => $monthlyData->sum('total_revenue'),
            'average_guests_per_booking' => $monthlyData->count() > 0 
                ? round($monthlyData->sum('total_guests') / $monthlyData->sum('booking_count'), 2) 
                : 0,
        ];

        $data = [
            'year' => $year,
            'month' => $month,
            'monthName' => $startDate->format('F Y'),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tenantBookings' => $monthlyData,
            'summary' => $summary,
        ];

        $pdf = \PDF::loadView('admin.reports.monthly-booking-pdf', $data);
        $filename = "booking-report-{$year}-{$month}-" . now()->timestamp . '.pdf';

        return $pdf->download($filename);
    }
}

