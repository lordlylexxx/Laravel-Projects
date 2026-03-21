<?php

namespace App\Multitenancy\TenantFinder;

use Illuminate\Http\Request;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class PortTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?IsTenant
    {
        $centralPort = (int) env('CENTRAL_PORT', 8000);
        $requestPort = (int) $request->getPort();

        if ($requestPort === $centralPort) {
            return null;
        }

        return app(IsTenant::class)::query()
            ->where('app_port', $requestPort)
            ->where(function ($query) {
                $query->where('domain_enabled', true)
                    ->orWhereNull('domain_enabled');
            })
            ->first();
    }
}
