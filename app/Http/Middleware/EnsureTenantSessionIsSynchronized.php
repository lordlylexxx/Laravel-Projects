<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantSessionIsSynchronized
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            abort(404);
        }

        $sessionKey = 'ensure_valid_tenant_session_tenant_id';
        $currentTenantId = (string) $tenant->getKey();
        $sessionTenantId = (string) $request->session()->get($sessionKey, '');

        if ($sessionTenantId !== '' && $sessionTenantId !== $currentTenantId && Auth::check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->put($sessionKey, $tenant->getKey());

        return $next($request);
    }
}
