<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRequestUsesCentralPort
{
    public function handle(Request $request, Closure $next): Response
    {
        // Extract domain from host header (without port)
        $host = $request->getHost();
        
        // Handle IPv6 format [::1]:8000 and IPv4 format 127.0.0.1:8000
        // getHost() returns just the host without port, but handle edge cases
        $hostWithoutPort = $host;
        if (strpos($host, '[') === 0) {
            // IPv6 format: extract from [::1]
            $hostWithoutPort = substr($host, 1, strpos($host, ']') - 1);
        } elseif (strpos($host, ':') !== false && strpos($host, ':') === strrpos($host, ':')) {
            // IPv4 format with port: extract before last colon
            $hostWithoutPort = substr($host, 0, strrpos($host, ':'));
        }
        
        $centralDomain = env('CENTRAL_DOMAIN', 'localhost');
        
        // Check if it's a central domain (localhost, 127.0.0.1, ::1, or CENTRAL_DOMAIN)
        $isCentralDomain = in_array($hostWithoutPort, [$centralDomain, '127.0.0.1', 'localhost', '::1'], true);
        
        if (!$isCentralDomain) {
            abort(404);
        }

        return $next($request);
    }
}
