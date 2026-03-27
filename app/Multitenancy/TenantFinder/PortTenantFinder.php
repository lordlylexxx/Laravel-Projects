<?php

namespace App\Multitenancy\TenantFinder;

use Illuminate\Http\Request;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class PortTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?IsTenant
    {
        $centralDomain = env('CENTRAL_DOMAIN', 'localhost');
        $requestHost = $request->getHost();

        // Extract domain without port
        // Handle IPv6 format [::1]:8000 and IPv4 format 127.0.0.1:8000
        $domainWithoutPort = $requestHost;
        if (strpos($requestHost, '[') === 0) {
            // IPv6 format: extract from [::1]
            $domainWithoutPort = substr($requestHost, 1, strpos($requestHost, ']') - 1);
        } elseif (strpos($requestHost, ':') !== false && strpos($requestHost, ':') === strrpos($requestHost, ':')) {
            // IPv4 format with port: extract before last colon
            $domainWithoutPort = substr($requestHost, 0, strrpos($requestHost, ':'));
        }

        // Check if accessing central app (localhost, 127.0.0.1, ::1, or CENTRAL_DOMAIN)
        if (in_array($domainWithoutPort, [$centralDomain, 'localhost', '127.0.0.1', '::1'], true)) {
            return null;
        }

        // Find tenant by domain (all tenants on port 8000)
        return app(IsTenant::class)::query()
            ->where('domain', $domainWithoutPort)
            ->first();
    }
}
