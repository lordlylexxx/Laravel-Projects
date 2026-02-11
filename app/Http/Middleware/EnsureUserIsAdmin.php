<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
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
        
        // Only admins can access these routes
        if ($user->role !== 'admin') {
            // Redirect to their appropriate dashboard
            if ($user->role === 'owner') {
                return redirect()->route('owner.dashboard')
                    ->with('error', 'This section is for administrators only.');
            } elseif ($user->role === 'client') {
                return redirect()->route('dashboard')
                    ->with('error', 'This section is for administrators only.');
            }
        }
        
        return $next($request);
    }
}
