<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerOnboardingComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'owner') {
            return $next($request);
        }

        $landlordConnection = (string) config('multitenancy.landlord_database_connection_name', 'landlord');

        try {
            if (! Schema::connection($landlordConnection)->hasTable('tenants')) {
                return $next($request);
            }
        } catch (\Throwable) {
            return $next($request);
        }

        $tenant = $user->ownedTenant;

        if (! $tenant instanceof Tenant) {
            if ($user->isOwner() && Tenant::isRequestHostForCentralLandlordApp($request)) {
                $tenant = $user->ensureTenant();
            }

            if (! $tenant instanceof Tenant) {
                return $next($request);
            }
        }

        if ((string) $tenant->onboarding_status === Tenant::ONBOARDING_APPROVED) {
            return $next($request);
        }

        if ($tenant->onboarding_status === Tenant::ONBOARDING_AWAITING_PAYMENT) {
            return redirect(route('owner.onboarding.payment', [], false));
        }

        return redirect(route('owner.onboarding.status', [], false));
    }
}
