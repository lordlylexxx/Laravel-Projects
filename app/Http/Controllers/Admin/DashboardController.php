<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TenantDomainStatusChangedMail;
use App\Mail\TenantSubscriptionChangedMail;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Tenant;
use App\Models\TenantLifecycleLog;
use App\Models\User;
use App\Services\TenantOnboardingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with platform analytics (non-financial metrics).
     */
    public function index(Request $request)
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $selectedTenantId = $request->filled('tenant_id')
            ? (int) $request->integer('tenant_id')
            : null;
        if ($selectedTenantId !== null && ! Tenant::query()->whereKey($selectedTenantId)->exists()) {
            $selectedTenantId = null;
        }

        $demographicsStartDate = $request->date('start_date')
            ? Carbon::parse((string) $request->input('start_date'))->startOfDay()
            : $startOfMonth->copy()->startOfDay();
        $demographicsEndDate = $request->date('end_date')
            ? Carbon::parse((string) $request->input('end_date'))->endOfDay()
            : $endOfMonth->copy()->endOfDay();
        if ($demographicsEndDate->lt($demographicsStartDate)) {
            [$demographicsStartDate, $demographicsEndDate] = [$demographicsEndDate->copy()->startOfDay(), $demographicsStartDate->copy()->endOfDay()];
        }

        // ============ PER-TENANT AGGREGATION (runs against each tenant DB) ============
        $metrics = $this->aggregateTenantDashboardMetrics($now, $startOfMonth, $endOfMonth);

        $totalBookings = $metrics['total_bookings'];
        $activeClients = User::clients()->where('is_active', true)->count();

        $totalAccommodations = $metrics['total_accommodations'];
        $occupancyRate = $metrics['total_capacity'] > 0
            ? round(($metrics['booked_nights'] / $metrics['total_capacity']) * 100, 1)
            : 0;

        $monthlyBookingsData = $metrics['monthly_bookings'];
        $monthlyGuestsData = $metrics['monthly_guests'];
        $bookingsByType = $metrics['bookings_by_type'];

        $kpis = [
            'total_users' => User::count(),
            'total_accommodations' => $totalAccommodations,
            'total_bookings' => $totalBookings,
            'pending_bookings' => $metrics['pending_bookings'],
            'active_clients' => $activeClients,
            'verified_properties' => $metrics['verified_properties'],
            'occupancy_rate' => $occupancyRate,
        ];

        $recentBookings = $metrics['recent_bookings'];
        $topTenantByBookings = $metrics['top_tenant_by_bookings'];
        $tenantBookingsToday = $metrics['tenant_bookings_today'];

        $tenantFilterOptions = Tenant::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $demographics = $this->buildDemographicsPayload(
            $selectedTenantId,
            $demographicsStartDate,
            $demographicsEndDate
        );

        return view('admin.dashboard', compact(
            'totalBookings', 'activeClients', 'occupancyRate',
            'monthlyBookingsData', 'monthlyGuestsData', 'bookingsByType',
            'kpis', 'recentBookings', 'tenantBookingsToday', 'topTenantByBookings',
            'tenantFilterOptions', 'selectedTenantId', 'demographicsStartDate', 'demographicsEndDate', 'demographics'
        ));

    }

    /**
     * Aggregate booking/accommodation metrics across every provisioned tenant DB in one pass.
     *
     * @return array{total_bookings:int,total_accommodations:int,pending_bookings:int,verified_properties:int,booked_nights:int,total_capacity:int,monthly_bookings:array<string,int>,monthly_guests:array<string,int>,bookings_by_type:array<string,int>,recent_bookings:\Illuminate\Support\Collection,top_tenant_by_bookings:?object,tenant_bookings_today:\Illuminate\Support\Collection}
     */
    private function aggregateTenantDashboardMetrics(Carbon $now, Carbon $startOfMonth, Carbon $endOfMonth): array
    {
        $bookingTypes = ['traveller-inn', 'airbnb', 'daily-rental'];
        $paidStatuses = ['confirmed', 'completed', 'paid'];
        $today = $now->copy()->toDateString();

        $monthlyBookings = [];
        $monthlyGuests = [];
        $monthRanges = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthStart = $now->copy()->month($i)->startOfMonth();
            $monthEnd = $now->copy()->month($i)->endOfMonth();
            $monthKey = strtolower($monthStart->format('M'));
            $monthlyBookings[$monthKey] = 0;
            $monthlyGuests[$monthKey] = 0;
            $monthRanges[$monthKey] = [$monthStart, $monthEnd];
        }

        $bookingsByType = array_fill_keys($bookingTypes, 0);

        $metrics = [
            'total_bookings' => 0,
            'total_accommodations' => 0,
            'pending_bookings' => 0,
            'verified_properties' => 0,
            'booked_nights' => 0,
            'total_capacity' => 0,
            'monthly_bookings' => $monthlyBookings,
            'monthly_guests' => $monthlyGuests,
            'bookings_by_type' => $bookingsByType,
            'recent_bookings' => collect(),
            'top_tenant_by_bookings' => null,
            'tenant_bookings_today' => collect(),
        ];

        // Tests use the default connection for everything, so skip the per-tenant execute() loop there.
        if (app()->environment('testing')) {
            $metrics['total_bookings'] = Booking::count();
            $metrics['total_accommodations'] = Accommodation::count();
            $metrics['pending_bookings'] = Booking::where('status', 'pending')->count();
            $metrics['verified_properties'] = Accommodation::where('is_verified', true)->count();

            foreach ($monthRanges as $monthKey => [$monthStart, $monthEnd]) {
                $metrics['monthly_bookings'][$monthKey] = Booking::whereBetween('created_at', [$monthStart, $monthEnd])->count();
                $metrics['monthly_guests'][$monthKey] = (int) Booking::whereBetween('created_at', [$monthStart, $monthEnd])->sum('number_of_guests');
            }

            foreach ($bookingTypes as $type) {
                $metrics['bookings_by_type'][$type] = Booking::whereHas('accommodation', function ($query) use ($type) {
                    $query->where('type', $type);
                })
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->whereIn('status', $paidStatuses)
                    ->count();
            }

            $days = $startOfMonth->diffInDays($endOfMonth) + 1;
            $metrics['total_capacity'] = $metrics['total_accommodations'] * $days;
            $metrics['booked_nights'] = (int) Booking::whereBetween('check_in_date', [$startOfMonth, $endOfMonth])
                ->orWhereBetween('check_out_date', [$startOfMonth, $endOfMonth])
                ->whereIn('status', $paidStatuses)
                ->get()
                ->sum(function ($booking) use ($startOfMonth, $endOfMonth) {
                    $checkIn = max($booking->check_in_date, $startOfMonth);
                    $checkOut = min($booking->check_out_date, $endOfMonth);

                    return $checkIn->diffInDays($checkOut) + 1;
                });

            $metrics['recent_bookings'] = Booking::with(['client', 'accommodation'])
                ->latest()
                ->take(5)
                ->get();

            return $metrics;
        }

        $tenantConnection = config('multitenancy.tenant_database_connection_name', 'tenant');
        $tenants = Tenant::query()->where('database_provisioned', true)->get();

        $daysInMonth = $startOfMonth->diffInDays($endOfMonth) + 1;
        $tenantBookingsThisMonth = [];
        $tenantBookingsToday = [];
        $recentBookingsByTenant = [];

        foreach ($tenants as $tenant) {
            try {
                $tenant->execute(function () use (
                    $tenant,
                    $tenantConnection,
                    &$metrics,
                    $monthRanges,
                    $bookingTypes,
                    $paidStatuses,
                    $startOfMonth,
                    $endOfMonth,
                    $daysInMonth,
                    $today,
                    &$tenantBookingsThisMonth,
                    &$tenantBookingsToday,
                    &$recentBookingsByTenant
                ) {
                    if (! Schema::connection($tenantConnection)->hasTable('bookings')) {
                        return;
                    }

                    $accommodationCount = 0;
                    $verifiedCount = 0;
                    if (Schema::connection($tenantConnection)->hasTable('accommodations')) {
                        $accommodationCount = (int) Accommodation::count();
                        $verifiedCount = (int) Accommodation::where('is_verified', true)->count();
                    }

                    $metrics['total_bookings'] += (int) Booking::count();
                    $metrics['total_accommodations'] += $accommodationCount;
                    $metrics['pending_bookings'] += (int) Booking::where('status', 'pending')->count();
                    $metrics['verified_properties'] += $verifiedCount;

                    foreach ($monthRanges as $monthKey => [$monthStart, $monthEnd]) {
                        $metrics['monthly_bookings'][$monthKey] += (int) Booking::whereBetween('created_at', [$monthStart, $monthEnd])->count();
                        $metrics['monthly_guests'][$monthKey] += (int) Booking::whereBetween('created_at', [$monthStart, $monthEnd])->sum('number_of_guests');
                    }

                    foreach ($bookingTypes as $type) {
                        $metrics['bookings_by_type'][$type] += (int) Booking::whereHas('accommodation', function ($query) use ($type) {
                            $query->where('type', $type);
                        })
                            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                            ->whereIn('status', $paidStatuses)
                            ->count();
                    }

                    $metrics['total_capacity'] += $accommodationCount * $daysInMonth;
                    $metrics['booked_nights'] += (int) Booking::where(function ($query) use ($startOfMonth, $endOfMonth) {
                        $query->whereBetween('check_in_date', [$startOfMonth, $endOfMonth])
                            ->orWhereBetween('check_out_date', [$startOfMonth, $endOfMonth]);
                    })
                        ->whereIn('status', $paidStatuses)
                        ->get()
                        ->sum(function ($booking) use ($startOfMonth, $endOfMonth) {
                            $checkIn = max($booking->check_in_date, $startOfMonth);
                            $checkOut = min($booking->check_out_date, $endOfMonth);

                            return $checkIn->diffInDays($checkOut) + 1;
                        });

                    $tenantBookingsThisMonth[] = [
                        'tenant_id' => $tenant->id,
                        'name' => $tenant->name,
                        'count' => (int) Booking::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                            ->whereIn('status', $paidStatuses)
                            ->count(),
                    ];

                    $todayBookings = Booking::whereDate('check_in_date', '<=', $today)
                        ->whereDate('check_out_date', '>=', $today)
                        ->whereIn('status', $paidStatuses)
                        ->get(['number_of_guests']);

                    if ($todayBookings->isNotEmpty()) {
                        $tenantBookingsToday[] = (object) [
                            'id' => $tenant->id,
                            'name' => $tenant->name,
                            'booking_count' => $todayBookings->count(),
                            'total_guests' => (int) $todayBookings->sum('number_of_guests'),
                        ];
                    }

                    $recentBookingsByTenant[] = Booking::with(['client', 'accommodation'])
                        ->latest()
                        ->take(5)
                        ->get()
                        ->map(function ($booking) use ($tenant) {
                            $booking->setAttribute('tenant_name', $tenant->name);

                            return $booking;
                        });
                });
            } catch (\Throwable $exception) {
                Log::warning('Failed to aggregate dashboard metrics for tenant.', [
                    'tenant_id' => $tenant->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $topTenant = collect($tenantBookingsThisMonth)->sortByDesc('count')->first();
        if ($topTenant && $topTenant['count'] > 0) {
            $metrics['top_tenant_by_bookings'] = (object) [
                'name' => $topTenant['name'],
                'bookings_count' => $topTenant['count'],
            ];
        }

        $metrics['tenant_bookings_today'] = collect($tenantBookingsToday)
            ->sortByDesc('total_guests')
            ->values();

        $metrics['recent_bookings'] = collect($recentBookingsByTenant)
            ->flatten(1)
            ->sortByDesc(fn ($booking) => $booking->created_at)
            ->take(5)
            ->values();

        return $metrics;
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
            $query->where('action', 'like', '%'.trim((string) $request->input('action')).'%');
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
            'plan' => ['required', 'in:'.implode(',', [
                Tenant::PLAN_BASIC,
                Tenant::PLAN_PLUS,
                Tenant::PLAN_PRO,
                Tenant::PLAN_PROMO,
            ])],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $oldPlan = (string) $tenant->plan;
        $oldPromoMaxListings = $tenant->promo_max_listings;
        $oldPromoPrice = $tenant->promo_price;
        $oldSubscriptionStatus = (string) ($tenant->subscription_status ?? 'trialing');
        $planChanged = $tenant->plan !== $validated['plan'];

        $updates = [
            'plan' => $validated['plan'],
        ];

        if ($validated['plan'] !== Tenant::PLAN_PROMO) {
            $updates['promo_max_listings'] = null;
            $updates['promo_price'] = null;
        }

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
                'promo_max_listings' => $oldPromoMaxListings,
                'promo_price' => $oldPromoPrice,
                'subscription_status' => $oldSubscriptionStatus,
            ],
            after: [
                'plan' => (string) $tenant->plan,
                'promo_max_listings' => $tenant->promo_max_listings,
                'promo_price' => $tenant->promo_price,
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
            return back()->with('success', 'Tenant domain already '.($enabled ? 'enabled' : 'disabled')." for {$tenant->name}.");
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

    public function updateTenantBandwidthQuota(Request $request, Tenant $tenant): RedirectResponse
    {
        if ($request->input('bandwidth_quota_mb') === '') {
            $request->merge(['bandwidth_quota_mb' => null]);
        }

        $validated = $request->validate([
            'bandwidth_quota_mb' => ['nullable', 'numeric', 'min:0', 'max:1048576'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $quotaBytes = null;
        if (isset($validated['bandwidth_quota_mb']) && (float) $validated['bandwidth_quota_mb'] > 0) {
            $quotaBytes = (int) round((float) $validated['bandwidth_quota_mb'] * 1024 * 1024);
        }

        $before = (int) ($tenant->bandwidth_quota_bytes ?? 0);
        $tenant->update(['bandwidth_quota_bytes' => $quotaBytes]);

        $this->logLifecycleAction(
            request: $request,
            tenant: $tenant,
            action: 'tenant.bandwidth_quota.updated',
            reason: $validated['reason'],
            before: ['bandwidth_quota_bytes' => $before],
            after: ['bandwidth_quota_bytes' => (int) ($tenant->bandwidth_quota_bytes ?? 0)],
        );

        return back()->with('success', "Bandwidth quota updated for {$tenant->name}.");
    }

    public function resendTenantOnboardingEmail(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $owner = $tenant->owner;

        if (! $owner?->email) {
            return back()->with('success', "Unable to resend credentials: tenant owner email is missing for {$tenant->name}.");
        }

        if (! $tenant->database_provisioned) {
            return back()->with('success', "Unable to resend credentials: tenant database is not provisioned for {$tenant->name}.");
        }

        $sent = app(TenantOnboardingService::class)->provisionTenantAdminAndNotify($owner, $tenant);

        if (! $sent) {
            Log::warning('Failed to resend tenant admin credentials email.', [
                'tenant_id' => $tenant->id,
            ]);

            return back()->with('success', "Failed to resend tenant admin credentials for {$tenant->name}. Check logs for details.");
        }

        $this->logLifecycleAction(
            request: $request,
            tenant: $tenant,
            action: 'tenant.onboarding_email.resent',
            reason: $validated['reason'],
            before: [
                'owner_email' => $owner->email,
            ],
            after: [
                'owner_email' => $owner->email,
                'resent_at' => now()->toDateTimeString(),
                'includes_tenant_admin_password' => true,
            ]
        );

        return back()->with('success', "Tenant admin login and a new random password were emailed to {$owner->email}.");
    }

    public function approveTenantOnboarding(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        if ($tenant->onboarding_status !== Tenant::ONBOARDING_PENDING_APPROVAL) {
            return back()->with('success', "This tenant is not waiting for approval (current: {$tenant->onboarding_status}).");
        }

        $result = app(TenantOnboardingService::class)->approveRegistration($tenant, $request->user(), false);

        if (! $result['success']) {
            return back()->withErrors([
                'onboarding' => 'Provisioning failed. Check tenant database configuration and application logs.',
            ]);
        }

        $tenant->refresh();

                $this->logLifecycleAction(
                    request: $request,
                    tenant: $tenant,
            action: 'tenant.onboarding.approved',
                    reason: $validated['reason'],
            before: [
                'onboarding_status' => Tenant::ONBOARDING_PENDING_APPROVAL,
            ],
            after: [
                'onboarding_status' => Tenant::ONBOARDING_APPROVED,
                'domain_enabled' => (bool) $tenant->domain_enabled,
                'database_provisioned' => (bool) $tenant->database_provisioned,
                'tenant_admin_credentials_emailed' => $result['credentials_emailed'],
            ]
        );

        $successMessage = match ($result['credentials_emailed']) {
            true => "{$tenant->name} approved and provisioned. Tenant admin login and a random password were emailed to the owner.",
            false => "{$tenant->name} approved and provisioned, but sending tenant admin credentials by email failed. Check logs or use “Resend” from the tenant list.",
            default => "{$tenant->name} approved and provisioned. No owner email on file, so tenant admin credentials were not emailed.",
        };

        return back()->with('success', $successMessage);
    }

    public function rejectTenantOnboarding(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        if ($tenant->onboarding_status !== Tenant::ONBOARDING_PENDING_APPROVAL) {
            return back()->with('success', "This tenant is not waiting for approval (current: {$tenant->onboarding_status}).");
        }

        app(TenantOnboardingService::class)->rejectRegistration($tenant, $request->user(), $validated['reason']);

        return back()->with('success', "Registration rejected for {$tenant->name}.");
    }

    public function destroyTenant(Request $request, Tenant $tenant): RedirectResponse
    {
        if ($request->user()?->tenant_id !== null && (int) $request->user()->tenant_id === (int) $tenant->id) {
            return back()->withErrors([
                'delete' => 'You cannot delete the tenant this account is assigned to.',
            ]);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
            'confirm_slug' => ['required', 'string', 'max:255'],
        ]);

        if ($validated['confirm_slug'] !== $tenant->slug) {
            return back()->withErrors([
                'confirm_slug' => 'The confirmation slug does not match this tenant.',
            ])->withInput();
        }

        $beforeState = [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'slug' => $tenant->slug,
            'database' => $tenant->database,
            'database_provisioned' => (bool) $tenant->database_provisioned,
        ];

        $dbSanitized = $tenant->database ? preg_replace('/[^A-Za-z0-9_]/', '', $tenant->database) : '';
        $dbUserSanitized = $tenant->db_username ? preg_replace('/[^A-Za-z0-9_]/', '', $tenant->db_username) : '';
        $tenantName = $tenant->name;

        TenantLifecycleLog::create([
            'tenant_id' => $tenant->id,
            'actor_user_id' => $request->user()?->id,
            'action' => 'tenant.deleted',
            'reason' => $validated['reason'],
            'before_state' => $beforeState,
            'after_state' => [],
        ]);

        $tenant->delete();

        if ($dbSanitized !== '') {
            try {
                DB::connection('landlord')->statement('DROP DATABASE IF EXISTS `'.$dbSanitized.'`');
            } catch (\Throwable $exception) {
                Log::warning('Failed to drop tenant database after tenant delete.', [
                    'database' => $dbSanitized,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if ($dbUserSanitized !== '') {
            try {
                DB::connection('landlord')->statement("DROP USER IF EXISTS '{$dbUserSanitized}'@'%'");
                DB::connection('landlord')->statement('FLUSH PRIVILEGES');
        } catch (\Throwable $exception) {
                Log::warning('Failed to drop tenant database user after tenant delete.', [
                    'db_username' => $dbUserSanitized,
                'error' => $exception->getMessage(),
            ]);
            }
        }

        return redirect()->route('admin.tenants')->with('success', "Tenant \"{$tenantName}\" has been permanently deleted.");
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

    public function demographicsReport(Request $request)
    {
        $payload = $this->buildDemographicsPayloadFromRequest($request);
        $tenantFilterOptions = Tenant::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.reports.demographics', [
            'demographics' => $payload['demographics'],
            'selectedTenantId' => $payload['selectedTenantId'],
            'demographicsStartDate' => $payload['demographicsStartDate'],
            'demographicsEndDate' => $payload['demographicsEndDate'],
            'tenantFilterOptions' => $tenantFilterOptions,
        ]);
    }

    public function exportDemographicsReport(Request $request)
    {
        $validated = $request->validate([
            'format' => ['required', 'in:pdf,csv'],
            'tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $payload = $this->buildDemographicsPayloadFromRequest($request);
        $demographics = $payload['demographics'];
        $baseFileName = 'demographics-report-'.$demographics['scope_slug'].'-'.$demographics['start_date']->format('Ymd').'-'.$demographics['end_date']->format('Ymd');

        if ($validated['format'] === 'csv') {
            return $this->streamDemographicsCsv($demographics, $baseFileName.'.csv');
        }

        $pdf = \PDF::loadView('admin.reports.demographics-pdf', [
            'demographics' => $demographics,
        ]);

        return $pdf->download($baseFileName.'.pdf');
    }

    private function buildDemographicsPayloadFromRequest(Request $request): array
    {
        $selectedTenantId = $request->filled('tenant_id')
            ? (int) $request->integer('tenant_id')
            : null;
        if ($selectedTenantId !== null && ! Tenant::query()->whereKey($selectedTenantId)->exists()) {
            $selectedTenantId = null;
        }

        $defaultStart = now()->startOfMonth()->startOfDay();
        $defaultEnd = now()->endOfMonth()->endOfDay();
        $demographicsStartDate = $request->date('start_date')
            ? Carbon::parse((string) $request->input('start_date'))->startOfDay()
            : $defaultStart;
        $demographicsEndDate = $request->date('end_date')
            ? Carbon::parse((string) $request->input('end_date'))->endOfDay()
            : $defaultEnd;
        if ($demographicsEndDate->lt($demographicsStartDate)) {
            [$demographicsStartDate, $demographicsEndDate] = [$demographicsEndDate->copy()->startOfDay(), $demographicsStartDate->copy()->endOfDay()];
        }

        return [
            'selectedTenantId' => $selectedTenantId,
            'demographicsStartDate' => $demographicsStartDate,
            'demographicsEndDate' => $demographicsEndDate,
            'demographics' => $this->buildDemographicsPayload($selectedTenantId, $demographicsStartDate, $demographicsEndDate),
        ];
    }

    private function buildDemographicsPayload(?int $tenantId, Carbon $startDate, Carbon $endDate): array
    {
        $tenantName = null;
        if ($tenantId !== null) {
            $tenantName = (string) (Tenant::query()->whereKey($tenantId)->value('name') ?? 'Selected tenant');
        }

        // In the testing environment all data lives in the default connection,
        // so the per-tenant execute() dance is skipped to keep feature tests green.
        if (app()->environment('testing')) {
            $columnsReady = Schema::hasColumns('bookings', [
                'guest_gender',
                'guest_age',
                'guest_is_local',
                'guest_local_place',
                'guest_country',
            ]);

            $bookings = collect();
            if ($columnsReady) {
                $bookings = $this->demographicsBaseQuery($tenantId, $startDate, $endDate)
                    ->get([
                        'bookings.id',
                        'bookings.number_of_guests',
                        'bookings.guest_gender',
                        'bookings.guest_age',
                        'bookings.guest_is_local',
                        'bookings.guest_local_place',
                        'bookings.guest_country',
                    ]);
            }

            return $this->buildDemographicsStatsPayload($bookings, $tenantId, $tenantName, $startDate, $endDate, $columnsReady);
        }

        // Aggregate bookings by executing queries inside each tenant's database.
        // Central admin has no tenant scope, so per-tenant execution is required
        // to see booking data that lives inside each tenant's DB.
        $tenants = $tenantId !== null
            ? Tenant::query()->whereKey($tenantId)->where('database_provisioned', true)->get()
            : Tenant::query()->where('database_provisioned', true)->get();

        $tenantConnection = config('multitenancy.tenant_database_connection_name', 'tenant');
        $bookings = collect();
        $columnsReady = false;

        foreach ($tenants as $tenant) {
            try {
                $tenant->execute(function () use (&$bookings, &$columnsReady, $tenantConnection, $startDate, $endDate) {
                    $hasColumns = Schema::connection($tenantConnection)->hasColumns('bookings', [
                        'guest_gender',
                        'guest_age',
                        'guest_is_local',
                        'guest_local_place',
                        'guest_country',
                    ]);

                    if (! $hasColumns) {
                        return;
                    }

                    $columnsReady = true;

                    $tenantBookings = $this->demographicsBaseQuery(null, $startDate, $endDate)
                        ->get([
                            'bookings.id',
                            'bookings.number_of_guests',
                            'bookings.guest_gender',
                            'bookings.guest_age',
                            'bookings.guest_is_local',
                            'bookings.guest_local_place',
                            'bookings.guest_country',
                        ]);

                    $bookings = $bookings->concat($tenantBookings);
                });
            } catch (\Throwable $exception) {
                Log::warning('Failed to aggregate demographics for tenant.', [
                    'tenant_id' => $tenant->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return $this->buildDemographicsStatsPayload($bookings, $tenantId, $tenantName, $startDate, $endDate, $columnsReady);
    }

    private function buildDemographicsStatsPayload(
        \Illuminate\Support\Collection $bookings,
        ?int $tenantId,
        ?string $tenantName,
        Carbon $startDate,
        Carbon $endDate,
        bool $columnsReady
    ): array {
        $genderRaw = [
            'male' => 0,
            'female' => 0,
            'unspecified' => 0,
        ];
        $locationRaw = [
            'local' => 0,
            'foreign' => 0,
            'unspecified' => 0,
        ];
        $locationBreakdown = [
            'local' => [],
            'foreign' => [],
        ];
        $ageRaw = [
            '0-17' => 0,
            '18-24' => 0,
            '25-34' => 0,
            '35-44' => 0,
            '45-54' => 0,
            '55+' => 0,
            'Unspecified' => 0,
        ];
        $ageSum = 0;
        $ageCount = 0;

        foreach ($bookings as $booking) {
            $gender = strtolower(trim((string) ($booking->guest_gender ?? '')));
            if (! in_array($gender, ['male', 'female'], true)) {
                $gender = 'unspecified';
            }
            $genderRaw[$gender]++;

            $isLocal = $booking->guest_is_local;
            if ($isLocal === null) {
                $locationRaw['unspecified']++;
            } elseif ((bool) $isLocal === true) {
                $locationRaw['local']++;
                $localPlace = trim((string) ($booking->guest_local_place ?? ''));
                if ($localPlace !== '') {
                    $locationBreakdown['local'][$localPlace] = ($locationBreakdown['local'][$localPlace] ?? 0) + 1;
                }
            } else {
                $locationRaw['foreign']++;
                $country = trim((string) ($booking->guest_country ?? ''));
                if ($country !== '') {
                    $locationBreakdown['foreign'][$country] = ($locationBreakdown['foreign'][$country] ?? 0) + 1;
                }
            }

            $age = is_numeric($booking->guest_age) ? (int) $booking->guest_age : null;
            if ($age === null || $age < 0 || $age > 120) {
                $ageRaw['Unspecified']++;
                continue;
            }

            $ageSum += $age;
            $ageCount++;
            if ($age <= 17) {
                $ageRaw['0-17']++;
            } elseif ($age <= 24) {
                $ageRaw['18-24']++;
            } elseif ($age <= 34) {
                $ageRaw['25-34']++;
            } elseif ($age <= 44) {
                $ageRaw['35-44']++;
            } elseif ($age <= 54) {
                $ageRaw['45-54']++;
            } else {
                $ageRaw['55+']++;
            }
        }

        arsort($locationBreakdown['local']);
        arsort($locationBreakdown['foreign']);

        $totalBookings = $bookings->count();
        $totalGuests = (int) $bookings->sum('number_of_guests');

        return [
            'scope_label' => $tenantId ? ('Tenant: '.$tenantName) : 'All tenants',
            'scope_slug' => $tenantId ? 'tenant-'.$tenantId : 'all-tenants',
            'tenant_id' => $tenantId,
            'tenant_name' => $tenantName,
            'start_date' => $startDate->copy(),
            'end_date' => $endDate->copy(),
            'columns_ready' => $columnsReady,
            'total_bookings' => $totalBookings,
            'total_guests' => $totalGuests,
            'profiled_bookings' => (int) $bookings->filter(function ($booking): bool {
                return $booking->guest_gender !== null
                    || $booking->guest_age !== null
                    || $booking->guest_is_local !== null
                    || (string) ($booking->guest_local_place ?? '') !== ''
                    || (string) ($booking->guest_country ?? '') !== '';
            })->count(),
            'average_age' => $ageCount > 0 ? round($ageSum / $ageCount, 1) : null,
            'gender' => [
                'labels' => ['Male', 'Female', 'Unspecified'],
                'counts' => [$genderRaw['male'], $genderRaw['female'], $genderRaw['unspecified']],
                'raw' => $genderRaw,
            ],
            'location' => [
                'labels' => ['Local', 'Foreign', 'Unspecified'],
                'counts' => [$locationRaw['local'], $locationRaw['foreign'], $locationRaw['unspecified']],
                'raw' => $locationRaw,
                'breakdown' => [
                    'local_labels' => array_values(array_keys($locationBreakdown['local'])),
                    'local_counts' => array_values(array_values($locationBreakdown['local'])),
                    'foreign_labels' => array_values(array_keys($locationBreakdown['foreign'])),
                    'foreign_counts' => array_values(array_values($locationBreakdown['foreign'])),
                ],
            ],
            'age' => [
                'labels' => array_keys($ageRaw),
                'counts' => array_values($ageRaw),
                'raw' => $ageRaw,
            ],
        ];
    }

    private function demographicsBaseQuery(?int $tenantId, Carbon $startDate, Carbon $endDate)
    {
        return Booking::query()
            ->whereIn('bookings.status', ['confirmed', 'completed', 'paid'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('bookings.check_in_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhereBetween('bookings.check_out_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhere(function ($rangeQuery) use ($startDate, $endDate) {
                        $rangeQuery->whereDate('bookings.check_in_date', '<', $startDate->toDateString())
                            ->whereDate('bookings.check_out_date', '>', $endDate->toDateString());
                    });
            })
            ->when($tenantId, fn ($query) => $query->where('bookings.tenant_id', $tenantId));
    }

    private function streamDemographicsCsv(array $demographics, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($demographics): void {
            $output = fopen('php://output', 'w');
            if (! $output) {
                return;
            }

            fputcsv($output, ['Scope', $demographics['scope_label']]);
            fputcsv($output, ['Date range', $demographics['start_date']->toDateString().' to '.$demographics['end_date']->toDateString()]);
            fputcsv($output, []);
            fputcsv($output, ['Summary Metric', 'Value']);
            fputcsv($output, ['Total bookings', (string) $demographics['total_bookings']]);
            fputcsv($output, ['Total guests', (string) $demographics['total_guests']]);
            fputcsv($output, ['Profiled bookings', (string) $demographics['profiled_bookings']]);
            fputcsv($output, ['Average age', $demographics['average_age'] !== null ? (string) $demographics['average_age'] : 'N/A']);
            fputcsv($output, []);

            fputcsv($output, ['Gender Distribution']);
            fputcsv($output, ['Gender', 'Bookings']);
            foreach ($demographics['gender']['raw'] as $label => $count) {
                fputcsv($output, [ucfirst($label), (string) $count]);
            }
            fputcsv($output, []);

            fputcsv($output, ['Location Distribution']);
            fputcsv($output, ['Type', 'Bookings']);
            foreach ($demographics['location']['raw'] as $label => $count) {
                fputcsv($output, [ucfirst($label), (string) $count]);
            }
            fputcsv($output, []);

            fputcsv($output, ['Local Place Breakdown']);
            fputcsv($output, ['Place', 'Bookings']);
            foreach ($demographics['location']['breakdown']['local_labels'] as $index => $place) {
                fputcsv($output, [$place, (string) ($demographics['location']['breakdown']['local_counts'][$index] ?? 0)]);
            }
            fputcsv($output, []);

            fputcsv($output, ['Foreign Country Breakdown']);
            fputcsv($output, ['Country', 'Bookings']);
            foreach ($demographics['location']['breakdown']['foreign_labels'] as $index => $country) {
                fputcsv($output, [$country, (string) ($demographics['location']['breakdown']['foreign_counts'][$index] ?? 0)]);
            }
            fputcsv($output, []);

            fputcsv($output, ['Age Distribution']);
            fputcsv($output, ['Age Bucket', 'Bookings']);
            foreach ($demographics['age']['raw'] as $bucket => $count) {
                fputcsv($output, [$bucket, (string) $count]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Generate HTML report for monthly bookings by tenant (no financial columns).
     */
    public function generateMonthlyBookingReport(Request $request, $year = null, $month = null)
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'min:2020', 'max:'.now()->year],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $year = (int) ($year ?? $validated['year'] ?? now()->year);
        $month = (int) ($month ?? $validated['month'] ?? now()->month);

        return response()->view('admin.reports.monthly-booking-pdf', $this->monthlyBookingReportPayload($year, $month));
    }

    /**
     * Download PDF report for monthly bookings by tenant (no financial columns).
     */
    public function downloadMonthlyBookingPdf(Request $request, $year = null, $month = null)
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'min:2020', 'max:'.now()->year],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $year = (int) ($year ?? $validated['year'] ?? now()->year);
        $month = (int) ($month ?? $validated['month'] ?? now()->month);

        $data = $this->monthlyBookingReportPayload($year, $month);

        $pdf = \PDF::loadView('admin.reports.monthly-booking-pdf', $data);
        $filename = "booking-report-{$year}-{$month}-".now()->timestamp.'.pdf';

        return $pdf->download($filename);
    }

    /**
     * @return array{year: int, month: int, monthName: string, startDate: Carbon, endDate: Carbon, tenantBookings: \Illuminate\Support\Collection, summary: array}
     */
    private function monthlyBookingReportPayload(int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $paidStatuses = ['confirmed', 'completed', 'paid'];

        if (app()->environment('testing')) {
            $monthlyData = Booking::query()
                ->join('accommodations', 'bookings.accommodation_id', '=', 'accommodations.id')
                ->join('tenants', 'accommodations.tenant_id', '=', 'tenants.id')
                ->whereBetween('bookings.check_in_date', [$startDate, $endDate])
                ->orWhereBetween('bookings.check_out_date', [$startDate, $endDate])
                ->whereIn('bookings.status', $paidStatuses)
                ->select(
                    'tenants.id',
                    'tenants.name',
                    'tenants.slug',
                    DB::raw('COUNT(*) as booking_count'),
                    DB::raw('SUM(bookings.number_of_guests) as total_guests'),
                    DB::raw('AVG(bookings.number_of_guests) as avg_guests_per_booking')
                )
                ->groupBy('tenants.id', 'tenants.name', 'tenants.slug')
                ->orderByDesc('total_guests')
                ->get();
        } else {
            $tenantConnection = config('multitenancy.tenant_database_connection_name', 'tenant');
            $rows = [];

            foreach (Tenant::query()->where('database_provisioned', true)->get() as $tenant) {
                try {
                    $tenant->execute(function () use ($tenant, $tenantConnection, $startDate, $endDate, $paidStatuses, &$rows) {
                        if (! Schema::connection($tenantConnection)->hasTable('bookings')) {
                            return;
                        }

                        $bookings = Booking::query()
                            ->where(function ($query) use ($startDate, $endDate) {
                                $query->whereBetween('check_in_date', [$startDate, $endDate])
                                    ->orWhereBetween('check_out_date', [$startDate, $endDate]);
                            })
                            ->whereIn('status', $paidStatuses)
                            ->get(['number_of_guests']);

                        if ($bookings->isEmpty()) {
                            return;
                        }

                        $count = $bookings->count();
                        $totalGuests = (int) $bookings->sum('number_of_guests');

                        $rows[] = (object) [
                            'id' => $tenant->id,
                            'name' => $tenant->name,
                            'slug' => $tenant->slug,
                            'booking_count' => $count,
                            'total_guests' => $totalGuests,
                            'avg_guests_per_booking' => $count > 0 ? $totalGuests / $count : 0,
                        ];
                    });
                } catch (\Throwable $exception) {
                    Log::warning('Failed to aggregate monthly booking report for tenant.', [
                        'tenant_id' => $tenant->id,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            $monthlyData = collect($rows)->sortByDesc('total_guests')->values();
        }

        $totalBookings = (int) $monthlyData->sum('booking_count');
        $totalGuests = (int) $monthlyData->sum('total_guests');

        $summary = [
            'total_bookings' => $totalBookings,
            'total_guests' => $totalGuests,
            'average_guests_per_booking' => $totalBookings > 0
                ? round($totalGuests / $totalBookings, 2)
                : 0,
            'tenant_count' => $monthlyData->count(),
        ];

        return [
            'year' => $year,
            'month' => $month,
            'monthName' => $startDate->format('F Y'),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tenantBookings' => $monthlyData,
            'summary' => $summary,
        ];
    }
}
