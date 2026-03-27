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

        if ($user->isOwner()) {
            return $next($request);
        }

        if ($user->isAdmin()) {
            $currentTenant = Tenant::current();

            if ($currentTenant && (int) $user->tenant_id === (int) $currentTenant->id) {
                return $next($request);
            }
        }

        return redirect()->route('dashboard')
            ->with('error', 'This section is for tenant managers only.');
    }
}
