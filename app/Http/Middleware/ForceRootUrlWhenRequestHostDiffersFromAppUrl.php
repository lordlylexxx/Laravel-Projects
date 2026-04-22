<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * When APP_URL does not match the current request origin (host or port),
 * absolute route() URLs can jump to the wrong app origin (e.g. central port instead
 * of tenant port) and may also break session-bound requests.
 */
class ForceRootUrlWhenRequestHostDiffersFromAppUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $configHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        $configPort = parse_url((string) config('app.url'), PHP_URL_PORT);
        $requestHost = $request->getHost();
        $requestPort = $request->getPort();
        $defaultPort = $request->isSecure() ? 443 : 80;

        $effectiveConfigPort = $configPort !== null ? (int) $configPort : $defaultPort;

        $hostDiffers = $configHost && strcasecmp((string) $configHost, $requestHost) !== 0;
        $portDiffers = (int) $requestPort !== $effectiveConfigPort;

        if ($hostDiffers || $portDiffers) {
            URL::forceRootUrl($request->getSchemeAndHttpHost());
        }

        return $next($request);
    }
}
