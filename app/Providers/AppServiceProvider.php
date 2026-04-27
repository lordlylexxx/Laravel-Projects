<?php

namespace App\Providers;

use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Tenant;
use App\Policies\AccommodationPolicy;
use App\Policies\BookingPolicy;
use App\Services\Messaging\CentralSupportInboxService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
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
        $this->ensureCaBundleConfigured();

        Gate::policy(Accommodation::class, AccommodationPolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);

        View::composer('owner.partials.top-navbar', function ($view): void {
            if (! Tenant::checkCurrent() || ! Auth::check()) {
                $view->with('unreadMessagesCount', 0);

                return;
            }

            $user = Auth::user();

            if (! $user || (! $user->isOwner() && ! $user->isAdmin())) {
                $view->with('unreadMessagesCount', 0);

                return;
            }

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

        View::composer('admin.partials.top-navbar', function ($view): void {
            if (! Auth::check()) {
                $view->with('unreadMessagesCount', 0);

                return;
            }

            $user = Auth::user();

            if (Tenant::checkCurrent() && $user && ($user->isOwner() || $user->isAdmin())) {
                $tenant = Tenant::current();
                $unreadMessagesCount = Message::query()
                    ->where('receiver_id', $user->id)
                    ->when($tenant, fn ($query) => $query->where('tenant_id', $tenant->id))
                    ->unread()
                    ->count();

                $view->with('unreadMessagesCount', $unreadMessagesCount);

                return;
            }

            if ($user->isAdmin() && $user->tenant_id === null && ! Tenant::checkCurrent()) {
                $view->with('unreadMessagesCount', app(CentralSupportInboxService::class)->unreadTotal());
            } else {
                $view->with('unreadMessagesCount', 0);
            }
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

        Blade::directive('fileSize', function ($expression) {
            return "<?php echo e(\\App\\Providers\\AppServiceProvider::formatFileSize($expression)); ?>";
        });
    }

    /**
     * Ensure PHP/Guzzle can verify SSL certificates even when php.ini has no
     * openssl.cafile / curl.cainfo set (common on Windows dev setups).
     * Falls back to a bundled cacert.pem in storage/certs.
     */
    private function ensureCaBundleConfigured(): void
    {
        $existing = (string) (getenv('CURL_CA_BUNDLE') ?: '');
        $iniCurl = (string) ini_get('curl.cainfo');
        $iniOpenssl = (string) ini_get('openssl.cafile');

        if ($existing !== '' || $iniCurl !== '' || $iniOpenssl !== '') {
            return;
        }

        $bundlePath = base_path('storage/certs/cacert.pem');

        if (! is_file($bundlePath)) {
            return;
        }

        putenv('CURL_CA_BUNDLE='.$bundlePath);
        $_ENV['CURL_CA_BUNDLE'] = $bundlePath;
        $_SERVER['CURL_CA_BUNDLE'] = $bundlePath;
    }

    public static function formatFileSize(mixed $size): string
    {
        if (! is_numeric($size)) {
            return (string) $size;
        }

        try {
            return \Illuminate\Support\Number::fileSize($size);
        } catch (\RuntimeException $exception) {
            return self::formatBytes((float) $size);
        }
    }

    private static function formatBytes(float $bytes, int $decimals = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max(0, $bytes);

        if ($bytes === 0.0) {
            return '0 B';
        }

        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $bytes /= 1024 ** $power;

        return number_format($bytes, $decimals) . ' ' . $units[$power];
    }
}
