<?php

namespace App\Providers;

use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Message;
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

            $unreadMessagesCount = Message::query()
                ->where('receiver_id', $user->id)
                ->when(Tenant::checkCurrent(), function ($query) {
                    $tenant = Tenant::current();

                    return $tenant ? $query->where('tenant_id', $tenant->id) : $query;
                })
                ->unread()
                ->count();

            $view->with('tenantUpdate', $tenantUpdate)
                ->with('unreadMessagesCount', $unreadMessagesCount);
        });

        View::composer('client.partials.top-navbar', function ($view): void {
            if (! Auth::check()) {
                $view->with('unreadMessagesCount', 0);

                return;
            }

            $user = Auth::user();

            $unreadMessagesCount = Message::query()
                ->where('receiver_id', $user->id)
                ->when(Tenant::checkCurrent(), function ($query) {
                    $tenant = Tenant::current();

                    return $tenant ? $query->where('tenant_id', $tenant->id) : $query;
                })
                ->unread()
                ->count();

            $view->with('unreadMessagesCount', $unreadMessagesCount);
        });
    }
}
