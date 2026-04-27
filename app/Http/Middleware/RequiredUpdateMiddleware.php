<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantUpdateService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiredUpdateMiddleware
{
    public function __construct(
        private readonly TenantUpdateService $tenantUpdateService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Tenant::current();
        if (! $tenant || ! $request->user()) {
            return $next($request);
        }

        if ($request->routeIs('owner.settings.updates.*') || $request->routeIs('settings.updates.*')) {
            return $next($request);
        }

        if ($this->tenantUpdateService->isUpdateRequired((int) $tenant->id)) {
            return redirect()->route('settings.updates.index')
                ->with('error', 'A required update must be applied before continuing.');
        }

        return $next($request);
    }
}
