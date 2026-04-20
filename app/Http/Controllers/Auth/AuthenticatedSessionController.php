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
    public function create(Request $request): View
    {
        if (Tenant::checkCurrent()) {
            $portal = $request->query('portal');
            if (! in_array($portal, ['admin', 'client'], true)) {
                $portal = null;
            }

            return view('tenant.auth.login', [
                'tenant' => Tenant::current(),
                'portal' => $portal,
            ]);
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $portal = $request->input('portal');
        if (! in_array($portal, ['admin', 'client'], true)) {
            $portal = null;
        }

        $request->authenticate();

        $currentTenant = Tenant::current();
        $user = $request->user();

        if ($user && ! $user->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Your account is currently inactive. Please contact your business administrator.',
            ])->onlyInput('email');
        }

        if (! $currentTenant && $user?->isClient()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Client accounts can only log in from tenant subdomain apps.',
            ])->onlyInput('email');
        }

        // In tenant context, enforce tenant membership for all role types.
        if ($currentTenant && $user) {
            if ($portal === 'client' && ($user->isOwner() || $user->isAdmin())) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'This is the client portal. Please use the tenant admin login.',
                ])->onlyInput('email');
            }

            if ($portal === 'admin' && $user->isClient()) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'This is the tenant admin portal. Please use the client login.',
                ])->onlyInput('email');
            }

            $belongsToCurrentTenant = false;

            if ($user->isOwner()) {
                $belongsToCurrentTenant = (int) ($user->tenant_id ?? 0) === (int) $currentTenant->id
                    || (int) optional($user->ownedTenant)->id === (int) $currentTenant->id;
            } elseif ($user->isAdmin() || $user->isClient()) {
                $belongsToCurrentTenant = (int) ($user->tenant_id ?? 0) === (int) $currentTenant->id;
            }

            if (! $belongsToCurrentTenant) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'This account does not belong to this tenant.',
                ])->onlyInput('email');
            }
        }

        // Prevent central admins from authenticating on a tenant subdomain.
        if ($currentTenant && $user?->isAdmin() && (int) ($user->tenant_id ?? 0) !== (int) $currentTenant->id) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'This admin account does not belong to this tenant.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Update last login
        $user?->updateLastLogin();

        // In tenant mode, redirect to the correct tenant dashboard by role (honour url.intended, e.g. after landing CTA → login).
        if ($currentTenant) {
            if ($user?->isOwner() || $user?->isAdmin()) {
                return $this->tenantSafeIntendedRedirect($request, url('/owner/dashboard'));
            }

            return $this->tenantSafeIntendedRedirect($request, route('dashboard'));
        }

        return redirect()->intended($request->user()->getDashboardRoute());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $isTenantContext = Tenant::checkCurrent();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $isTenantContext ? redirect()->to('/') : redirect('/');
    }

    private function tenantSafeIntendedRedirect(Request $request, string $fallback): RedirectResponse
    {
        $intended = (string) $request->session()->get('url.intended', '');

        if ($intended !== '' && ! $this->isSafeTenantIntendedUrl($request, $intended)) {
            $request->session()->forget('url.intended');

            return redirect()->to($fallback);
        }

        return redirect()->intended($fallback);
    }

    private function isSafeTenantIntendedUrl(Request $request, string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        // Relative intended URLs are safe on the current host.
        if (! is_string($host) || $host === '') {
            return true;
        }

        return strcasecmp($host, $request->getHost()) === 0;
    }
}
