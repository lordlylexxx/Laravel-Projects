<?php

namespace App\Http\Middleware;

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
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Only owners can access these routes
        if ($user->role !== 'owner') {
            // Redirect to their appropriate dashboard
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'This section is for property owners only.');
            } elseif ($user->role === 'client') {
                return redirect()->route('dashboard')
                    ->with('error', 'This section is for property owners only.');
            }
        }
        
        return $next($request);
    }
}
