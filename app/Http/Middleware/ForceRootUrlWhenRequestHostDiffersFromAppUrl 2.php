<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * When APP_URL uses a different host than the browser (e.g. localhost vs 127.0.0.1),
 * absolute route() URLs break session cookies on POST and cause 419 CSRF errors.
 */
class ForceRootUrlWhenRequestHostDiffersFromAppUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $configHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        $requestHost = $request->getHost();

        if ($configHost && strcasecmp((string) $configHost, $requestHost) !== 0) {
            URL::forceRootUrl($request->getSchemeAndHttpHost());
        }

        return $next($request);
    }
}
