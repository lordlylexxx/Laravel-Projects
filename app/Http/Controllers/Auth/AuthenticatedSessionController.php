<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Tenant;
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
        if (Tenant::checkCurrent()) {
            return view('tenant.auth.login', [
                'tenant' => Tenant::current(),
            ]);
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        if (! Tenant::checkCurrent() && $request->user()?->isClient()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Client accounts can only log in from tenant subdomain apps.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Update last login
        $request->user()->updateLastLogin();

        // In tenant mode, avoid cross-app intended URL leakage (8000 <-> 8001)
        // and always land on the tenant dashboard.
        if (Tenant::checkCurrent()) {
            return redirect()->route('dashboard');
        }

        return redirect()->intended($request->user()->getDashboardRoute());
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
