<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Update last login
        $user = $request->user();
        try {
            $user->update(['last_login' => now()]);
        } catch (\Exception $e) {
            // Ignore if last_login column doesn't exist
        }

        // Redirect based on role - with fallback
        try {
            $redirectRoute = $user->getDashboardRoute();
            return redirect()->intended($redirectRoute);
        } catch (\Exception $e) {
            // Fallback redirect
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isOwner()) {
                return redirect()->route('owner.dashboard');
            }
            return redirect()->route('dashboard');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
