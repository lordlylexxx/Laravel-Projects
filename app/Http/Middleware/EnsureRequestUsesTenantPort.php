<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRequestUsesTenantPort
{
    public function handle(Request $request, Closure $next): Response
    {
        $centralPort = (int) env('CENTRAL_PORT', 8000);
        $tenantStartPort = (int) env('TENANT_PORT_START', 8001);
        $requestPort = (int) $request->getPort();

        if ($requestPort === $centralPort || $requestPort < $tenantStartPort) {
            abort(404);
        }

        return $next($request);
    }
}
