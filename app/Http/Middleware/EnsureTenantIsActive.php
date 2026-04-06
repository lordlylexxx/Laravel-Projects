<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            abort(404);
        }

        if ((string) ($tenant->onboarding_status ?? Tenant::ONBOARDING_APPROVED) !== Tenant::ONBOARDING_APPROVED) {
            return response()->view('tenant.pending-onboarding', [
                'tenant' => $tenant,
                'message' => 'This business space is not active yet. The owner is completing registration or waiting for approval.',
            ], 403);
        }

        $isDomainDisabled = $tenant->domain_enabled === false;
        $isSubscriptionBlocked = in_array((string) $tenant->subscription_status, ['past_due', 'cancelled'], true);

        if ($isDomainDisabled || $isSubscriptionBlocked) {
            return response()->view('tenant.disabled', [
                'tenant' => $tenant,
                'message' => 'Pay your subscription to continue using our services.',
            ], 402);
        }

        return $next($request);
    }
}
