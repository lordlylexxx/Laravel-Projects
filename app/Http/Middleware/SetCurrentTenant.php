<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Tenant::checkCurrent()) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user || ! $user->isOwner()) {
            return $next($request);
        }

        $tenant = $user->ensureTenant();

        if ($tenant) {
            $tenant->makeCurrent();
        }

        return $next($request);
    }
}
