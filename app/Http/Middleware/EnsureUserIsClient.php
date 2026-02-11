<?php

namespace App\Http\Middleware;

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
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Only clients can access these routes
        if ($user->role !== 'client') {
            // Redirect to their appropriate dashboard
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'This section is for clients only.');
            } elseif ($user->role === 'owner') {
                return redirect()->route('owner.dashboard')
                    ->with('error', 'This section is for clients only.');
            }
        }
        
        return $next($request);
    }
}
