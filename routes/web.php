<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantLandingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Central\UpdateController as CentralUpdateController;
use App\Http\Controllers\SystemUpdatePageController;
use App\Models\Tenant;
use Illuminate\Support\Facades\Route;

$centralDomain = env('CENTRAL_DOMAIN', parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost');
$registerCentralRoutes = function () {
    // Central app routes
    Route::middleware('central.port')->group(function () {
        // Landing page route (accessible to everyone)
        Route::get('/', function () {
            $featuredTenants = Tenant::query()
                ->with('owner')
                ->where('domain_enabled', true)
                ->whereIn('subscription_status', ['trialing', 'active'])
                ->latest('id')
                ->take(8)
                ->get();

            return view('landingpage', compact('featuredTenants'));
        })->name('landing');

        Route::prefix('system-updates')->name('updates.')->group(function () {
            Route::get('/check', [CentralUpdateController::class, 'check'])->name('check');
            Route::get('/download', [CentralUpdateController::class, 'download'])->name('download');
        });

        // Public routes
        Route::middleware('guest')->group(function () {
            Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

            Route::post('register', [RegisteredUserController::class, 'store']);

            Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

            Route::post('login', [AuthenticatedSessionController::class, 'store']);

            Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

            Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

            Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

            Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
        });

        // Authenticated routes (common for all roles)
        Route::middleware(['auth', 'tenant.context'])->group(function () {
            Route::get('verify-email', EmailVerificationPromptController::class)
                ->name('verification.notice');

            Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

            Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

            Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

            Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

            Route::put('password', [PasswordController::class, 'update'])->name('password.update');

            Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

            // Profile routes - accessible to all authenticated users
            Route::get('/profile', function () {
                return view('profile.new-edit');
            })->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

            // Messages - accessible to all authenticated users
            Route::prefix('messages')->name('messages.')->group(function () {
                Route::get('/', [\App\Http\Controllers\MessageController::class, 'index'])->name('index');
                Route::get('/{message}', [\App\Http\Controllers\MessageController::class, 'show'])->name('show');
                Route::post('/', [\App\Http\Controllers\MessageController::class, 'store'])->name('store');
                Route::post('/{message}/reply', [\App\Http\Controllers\MessageController::class, 'reply'])->name('reply');
                Route::put('/{message}/read', [\App\Http\Controllers\MessageController::class, 'markAsRead'])->name('mark-read');
                Route::put('/{message}/archive', [\App\Http\Controllers\MessageController::class, 'archive'])->name('archive');
                Route::delete('/{message}', [\App\Http\Controllers\MessageController::class, 'destroy'])->name('destroy');
            });

            // Central dashboard redirect (no client pages on central app)
            Route::get('/dashboard', function () {
                $user = request()->user();

                if (! $user) {
                    return redirect('/login');
                }

                if ($user->isAdmin()) {
                    return redirect('/admin/dashboard');
                }

                if ($user->isOwner()) {
                    return redirect('/owner/dashboard');
                }

                return redirect('/')
                    ->with('error', 'Client pages are available on tenant subdomain apps.');
            })->name('dashboard');

            Route::get('/home', function () {
                return redirect()->route('dashboard');
            })->name('home');
        });

        // ============ OWNER ROUTES ============
        // Only owners can access these routes
        Route::middleware(['auth', 'tenant.context', 'owner'])->prefix('owner')->name('owner.')->group(function () {
            // Owner Dashboard
            Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
            Route::get('/reports/monthly', [OwnerDashboardController::class, 'monthlyReport'])->name('reports.monthly');
            Route::get('/reports/monthly/download-sales', [OwnerDashboardController::class, 'downloadMonthlySalesPdf'])->name('reports.monthly.download-sales');
            Route::get('/reports/monthly/download-guests', [OwnerDashboardController::class, 'downloadMonthlyGuestsPdf'])->name('reports.monthly.download-guests');
            Route::get('/system-updates', [SystemUpdatePageController::class, 'ownerIndex'])->name('updates.index');
            Route::post('/system-updates/mark-installed', [SystemUpdatePageController::class, 'ownerMarkInstalled'])->name('updates.mark-installed');

            // Owner Landing Page Customization
            Route::get('/landing-page', [TenantLandingController::class, 'edit'])->name('landing.edit');
            Route::put('/landing-page', [TenantLandingController::class, 'update'])->name('landing.update');

            // Owner Accommodation Management
            Route::get('/accommodations', [\App\Http\Controllers\AccommodationController::class, 'ownerIndex'])
                ->name('accommodations.index');
            Route::resource('/accommodations', \App\Http\Controllers\AccommodationController::class)
                ->except(['index']);

            // Owner Booking Management
            Route::prefix('bookings')->name('bookings.')->group(function () {
                Route::get('/', [\App\Http\Controllers\BookingController::class, 'index'])->name('index');
                Route::get('/{booking}', [\App\Http\Controllers\BookingController::class, 'show'])->name('show');
                Route::put('/{booking}/status', [\App\Http\Controllers\BookingController::class, 'updateStatus'])->name('update-status');
                Route::put('/{booking}/mark-paid', [\App\Http\Controllers\BookingController::class, 'markAsPaid'])->name('mark-paid');
                Route::put('/{booking}/complete', [\App\Http\Controllers\BookingController::class, 'complete'])->name('complete');
                Route::post('/{booking}/message', [\App\Http\Controllers\BookingController::class, 'sendMessage'])->name('message');
            });
        });

        // ============ ADMIN ROUTES ============
        // Only admins can access these routes
        Route::middleware(['auth', 'tenant.context', 'admin'])->prefix('admin')->name('admin.')->group(function () {
            // Admin Dashboard with Sales Monitoring
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
            Route::get('/system-updates', [SystemUpdatePageController::class, 'adminIndex'])->name('updates.index');
            Route::post('/system-updates/mark-installed', [SystemUpdatePageController::class, 'adminMarkInstalled'])->name('updates.mark-installed');

            // Tenant Management
            Route::get('/tenants', [AdminDashboardController::class, 'tenants'])->name('tenants');
            Route::get('/tenant-lifecycle-logs', [AdminDashboardController::class, 'tenantLifecycleLogs'])->name('tenants.lifecycle-logs');
            Route::get('/users', function () {
                return redirect()->route('admin.tenants');
            })->name('users');
            Route::put('/tenants/{tenant}/plan', [AdminDashboardController::class, 'updateTenantPlan'])->name('tenants.update-plan');
            Route::put('/tenants/{tenant}/subscription', [AdminDashboardController::class, 'updateTenantSubscription'])->name('tenants.update-subscription');
            Route::put('/tenants/{tenant}/profile', [AdminDashboardController::class, 'updateTenantProfile'])->name('tenants.update-profile');
            Route::put('/tenants/{tenant}/domain-status', [AdminDashboardController::class, 'toggleTenantDomain'])->name('tenants.toggle-domain');
            Route::post('/tenants/{tenant}/resend-onboarding-email', [AdminDashboardController::class, 'resendTenantOnboardingEmail'])->name('tenants.resend-onboarding-email');

            // Booking Reports
            Route::post('/reports/monthly-booking-pdf', [AdminDashboardController::class, 'downloadMonthlyBookingPdf'])->name('monthly-booking-pdf');
            Route::get('/reports/monthly-booking-pdf', [AdminDashboardController::class, 'generateMonthlyBookingReport'])->name('monthly-booking-report');

            // Booking Management
            // Message Management
            Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'adminIndex'])->name('messages');

            // Property Management (Admin can view all properties)
            Route::prefix('../owner/accommodations')->name('owner.accommodations.')->group(function () {
                Route::get('/', [\App\Http\Controllers\AccommodationController::class, 'ownerIndex'])->name('index');
                Route::get('/{accommodation}', [\App\Http\Controllers\AccommodationController::class, 'show'])->name('show');
            });
        });

        require __DIR__.'/auth.php';
    });
};

$centralHosts = array_values(array_unique([$centralDomain, 'localhost', '127.0.0.1', '::1']));

foreach ($centralHosts as $host) {
    Route::domain($host)->group($registerCentralRoutes);
}

Route::middleware(['tenant.port', 'tenant.required', 'tenant.active', 'tenant.session'])
    ->group(function () {
            Route::get('/', [TenantLandingController::class, 'showPublic'])
                ->name('landing');

            Route::get('/dashboard', [ClientDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('/accommodations', [\App\Http\Controllers\AccommodationController::class, 'index'])
                ->name('accommodations.index');

            Route::get('/accommodations/{accommodation}', [\App\Http\Controllers\AccommodationController::class, 'show'])
                ->name('accommodations.show');

            Route::middleware(['auth', 'client'])->group(function () {
                Route::post('/accommodations/{accommodation}/book', [\App\Http\Controllers\BookingController::class, 'store'])
                    ->name('accommodations.book');

                Route::prefix('bookings')->name('bookings.')->group(function () {
                    Route::get('/', [\App\Http\Controllers\BookingController::class, 'index'])->name('index');
                    Route::get('/{booking}', [\App\Http\Controllers\BookingController::class, 'show'])->name('show');
                    Route::put('/{booking}/cancel', [\App\Http\Controllers\BookingController::class, 'cancel'])->name('cancel');
                    Route::post('/{booking}/message', [\App\Http\Controllers\BookingController::class, 'sendMessage'])->name('message');
                    Route::get('/{booking}/payment', [\App\Http\Controllers\BookingController::class, 'payment'])->name('payment');
                    Route::post('/{booking}/payment/confirm', [\App\Http\Controllers\BookingController::class, 'confirmPayment'])->name('payment.confirm');
                });

                Route::get('/home', function () {
                    return redirect()->route('dashboard');
                })->name('home');
            });

            // Tenant admin dashboard alias for views shared with central app.
            Route::middleware(['auth', 'tenant.manager'])->group(function () {
                Route::get('/admin/dashboard', [OwnerDashboardController::class, 'index'])
                    ->name('admin.dashboard');
            });

            // Tenant manager routes (same owner pages/functions, available to owner or tenant admin)
            Route::middleware(['auth', 'tenant.manager'])->prefix('owner')->name('owner.')->group(function () {
                Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
                Route::get('/reports/monthly', [OwnerDashboardController::class, 'monthlyReport'])->name('reports.monthly');
                Route::get('/reports/monthly/download-sales', [OwnerDashboardController::class, 'downloadMonthlySalesPdf'])->name('reports.monthly.download-sales');
                Route::get('/reports/monthly/download-guests', [OwnerDashboardController::class, 'downloadMonthlyGuestsPdf'])->name('reports.monthly.download-guests');
                Route::get('/system-updates', [SystemUpdatePageController::class, 'ownerIndex'])->name('updates.index');
                Route::post('/system-updates/mark-installed', [SystemUpdatePageController::class, 'ownerMarkInstalled'])->name('updates.mark-installed');

                Route::get('/landing-page', [TenantLandingController::class, 'edit'])->name('landing.edit');
                Route::put('/landing-page', [TenantLandingController::class, 'update'])->name('landing.update');

                Route::get('/accommodations', [\App\Http\Controllers\AccommodationController::class, 'ownerIndex'])
                    ->name('accommodations.index');
                Route::resource('/accommodations', \App\Http\Controllers\AccommodationController::class)
                    ->except(['index']);

                Route::prefix('bookings')->name('bookings.')->group(function () {
                    Route::get('/', [\App\Http\Controllers\BookingController::class, 'index'])->name('index');
                    Route::get('/{booking}', [\App\Http\Controllers\BookingController::class, 'show'])->name('show');
                    Route::put('/{booking}/status', [\App\Http\Controllers\BookingController::class, 'updateStatus'])->name('update-status');
                    Route::put('/{booking}/mark-paid', [\App\Http\Controllers\BookingController::class, 'markAsPaid'])->name('mark-paid');
                    Route::put('/{booking}/complete', [\App\Http\Controllers\BookingController::class, 'complete'])->name('complete');
                    Route::post('/{booking}/message', [\App\Http\Controllers\BookingController::class, 'sendMessage'])->name('message');
                });
            });

            Route::middleware('auth')->group(function () {
                Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])
                    ->name('messages.index');

                Route::get('/messages/{message}', [\App\Http\Controllers\MessageController::class, 'show'])
                    ->name('messages.show');

                Route::post('/messages', [\App\Http\Controllers\MessageController::class, 'store'])
                    ->name('messages.store');

                Route::post('/messages/{message}/reply', [\App\Http\Controllers\MessageController::class, 'reply'])
                    ->name('messages.reply');

                Route::put('/messages/{message}/read', [\App\Http\Controllers\MessageController::class, 'markAsRead'])
                    ->name('messages.mark-read');

                Route::put('/messages/{message}/archive', [\App\Http\Controllers\MessageController::class, 'archive'])
                    ->name('messages.archive');

                Route::delete('/messages/{message}', [\App\Http\Controllers\MessageController::class, 'destroy'])
                    ->name('messages.destroy');
            });

            Route::middleware('auth')->group(function () {
                Route::get('/profile', function () {
                    return view('profile.new-edit');
                })->name('profile.edit');
                Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
                Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
            });

            require __DIR__.'/auth.php';
    });
