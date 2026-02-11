<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has the required role
        if ($user->role !== $role) {
            // Redirect to their appropriate dashboard based on role
            return redirect()->route($user->getDashboardRoute())
                ->with('error', 'You do not have access to this section.');
        }
        
        return $next($request);
    }
}
