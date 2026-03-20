<?php

namespace App\Providers;

use App\Models\Accommodation;
use App\Models\Booking;
use App\Policies\AccommodationPolicy;
use App\Policies\BookingPolicy;
use Illuminate\Support\Facades\Gate;
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
    }
}
