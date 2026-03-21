<?php

namespace App\Providers;

use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Tenant;
use App\Policies\AccommodationPolicy;
use App\Policies\BookingPolicy;
use App\Services\CentralUpdateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Accommodation::class, AccommodationPolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);

        View::composer([
            'owner.partials.top-navbar',
            'admin.partials.top-navbar',
        ], function ($view): void {
            if (! Tenant::checkCurrent() || ! Auth::check()) {
                $view->with('tenantUpdate', null);

                return;
            }

            $user = Auth::user();

            if (! $user || (! $user->isOwner() && ! $user->isAdmin())) {
                $view->with('tenantUpdate', null);

                return;
            }

            $service = app(CentralUpdateService::class);
            $tenantUpdate = $service->checkForUpdates((string) config('updates.current_version', '1.0.0'));

            $view->with('tenantUpdate', $tenantUpdate);
        });
    }
}
