<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * For tenant clients: reload Spatie permission relations each request and block URLs when guest
 * capabilities (bookings.self, messages.use, profile.self) are disabled — avoids stale checks when
 * navigating between pages.
 */
class EnsureTenantClientGuestRbacOnAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Tenant::current();
        $user = $request->user();

        if (! $tenant || ! $user instanceof User || ! $user->isClient()) {
            return $next($request);
        }

        if ((int) ($user->tenant_id ?? 0) !== (int) $tenant->id) {
            return $next($request);
        }

        $user->unsetRelation('roles');
        $user->unsetRelation('permissions');

        if ($request->is('bookings', 'bookings/*')) {
            abort_unless($user->tenantClientMayManageOwnStays(), 403);
        }

        if ($request->is('accommodations/*/book') && $request->isMethod('POST')) {
            abort_unless($user->tenantClientMayManageOwnStays(), 403);
        }

        if ($request->is('messages', 'messages/*')) {
            abort_unless($user->tenantClientMayUseMessaging(), 403);
        }

        if ($request->is('profile', 'profile/*')) {
            abort_unless($user->tenantClientMayEditOwnProfile(), 403);
        }

        if ($request->is('password') && in_array($request->method(), ['PUT', 'PATCH'], true)) {
            abort_unless($user->tenantClientMayEditOwnProfile(), 403);
        }

        return $next($request);
    }
}
