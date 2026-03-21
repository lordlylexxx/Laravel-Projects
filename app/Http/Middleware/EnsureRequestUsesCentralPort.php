<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRequestUsesCentralPort
{
    public function handle(Request $request, Closure $next): Response
    {
        $centralPort = (int) env('CENTRAL_PORT', 8000);

        if ((int) $request->getPort() !== $centralPort) {
            abort(404);
        }

        return $next($request);
    }
}
