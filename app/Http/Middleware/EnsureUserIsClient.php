<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsClient
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

        if ($currentTenant && (int) ($user->tenant_id ?? 0) !== (int) $currentTenant->id) {
            return redirect('/')
                ->with('error', 'You do not have access to this tenant.');
        }

        // Only clients can access these routes.
        if ($user->role !== 'client') {
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard')
                    ->with('error', 'This section is for clients only.');
            } elseif ($user->role === 'owner') {
                return redirect('/owner/dashboard')
                    ->with('error', 'This section is for clients only.');
            }
        }
        
        return $next($request);
    }
}
