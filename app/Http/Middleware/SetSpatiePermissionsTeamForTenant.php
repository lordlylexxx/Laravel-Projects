<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetSpatiePermissionsTeamForTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Tenant::current();

        if ($tenant) {
            setPermissionsTeamId($tenant->id);
        } else {
            setPermissionsTeamId(null);
        }

        return $next($request);
    }
}
