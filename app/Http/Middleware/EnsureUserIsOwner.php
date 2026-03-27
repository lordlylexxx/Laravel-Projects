<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOwner
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (! $user) {
            return redirect('/login');
        }

        $currentTenant = Tenant::current();

        if ($currentTenant) {
            $canAccessTenant = (int) ($user->tenant_id ?? 0) === (int) $currentTenant->id
                || (int) optional($user->ownedTenant)->id === (int) $currentTenant->id;

            if (! $canAccessTenant) {
                return redirect('/')
                    ->with('error', 'You do not have access to this tenant.');
            }
        }

        // Only owners can access these routes.
        if ($user->role !== 'owner') {
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard')
                    ->with('error', 'This section is for property owners only.');
            } elseif ($user->role === 'client') {
                return redirect('/dashboard')
                    ->with('error', 'This section is for property owners only.');
            }
        }
        
        return $next($request);
    }
}
