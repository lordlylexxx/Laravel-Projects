<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RecordTenantBandwidthUsage
{
    private const MAX_BYTES_PER_REQUEST = 52428800; // 50 MiB cap per request (avoid huge single spikes)

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! Tenant::checkCurrent()) {
            return $response;
        }

        if ($this->shouldSkipPath($request)) {
            return $response;
        }

        $tenant = Tenant::current();
        if (! $tenant) {
            return $response;
        }

        $bytes = $this->estimateTransferBytes($request, $response);
        if ($bytes < 1) {
            return $response;
        }

        $bytes = min($bytes, self::MAX_BYTES_PER_REQUEST);

        try {
            Tenant::query()->whereKey($tenant->id)->increment('bandwidth_usage_bytes', $bytes, [
                'bandwidth_last_recorded_at' => now(),
            ]);
        } catch (\Throwable) {
            // Non-fatal: monitoring must not break tenant responses.
        }

        return $response;
    }

    private function shouldSkipPath(Request $request): bool
    {
        $path = $request->path();

        return (bool) preg_match('~\.(ico|css|js|map|png|jpe?g|gif|webp|woff2?|ttf|svg|eot)$~i', $path);
    }

    private function estimateTransferBytes(Request $request, Response $response): int
    {
        $in = (int) $request->header('Content-Length', 0);

        $out = 0;
        if ($response instanceof StreamedResponse || $response instanceof BinaryFileResponse) {
            $out = (int) ($response->headers->get('Content-Length') ?: 0);
        } elseif ($response->headers->has('Content-Length')) {
            $out = (int) $response->headers->get('Content-Length');
        } else {
            $content = $response->getContent();
            if (is_string($content)) {
                $out = strlen($content);
            }
        }

        return max(0, $in) + max(0, $out);
    }
}
