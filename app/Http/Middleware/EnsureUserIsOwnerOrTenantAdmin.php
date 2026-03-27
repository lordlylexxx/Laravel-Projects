<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOwnerOrTenantAdmin
{
    /**
     * Allow owner users and tenant-scoped admins to access owner management pages.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect('/login');
        }

        $currentTenant = Tenant::current();

        if (! $currentTenant) {
            return redirect('/dashboard')
                ->with('error', 'Tenant context is required for this section.');
        }

        if ($user->isOwner()) {
            $canAccessTenant = (int) ($user->tenant_id ?? 0) === (int) $currentTenant->id
                || (int) optional($user->ownedTenant)->id === (int) $currentTenant->id;

            if ($canAccessTenant) {
                return $next($request);
            }
        }

        if ($user->isAdmin()) {
            if ((int) $user->tenant_id === (int) $currentTenant->id) {
                return $next($request);
            }
        }

        return redirect('/dashboard')
            ->with('error', 'This section is for tenant managers only.');
    }
}
