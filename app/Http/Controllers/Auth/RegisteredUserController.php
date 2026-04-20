<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantLifecycleLog;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        if (Tenant::checkCurrent()) {
            return view('tenant.auth.register', [
                'tenant' => Tenant::current(),
            ]);
        }

        return view('auth.register-wizard');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = Tenant::current();
        $isTenantSignup = ! is_null($tenant);
        $provisionedTenant = null;

        // Validate basic fields
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => $isTenantSignup ? ['nullable', 'in:client'] : ['nullable', 'in:client,owner'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];

        // Add customization validation for owners
        $isOwnerRegistration = ! $isTenantSignup && $request->input('role') === 'owner';
        if ($isOwnerRegistration) {
            $rules = array_merge($rules, [
                'subscription_plan' => ['nullable', 'in:basic,plus,pro,promo'],
                'app_title' => ['nullable', 'string', 'max:255'],
                'primary_color' => ['nullable', 'regex:/^#[0-9A-F]{6}$/i'],
                'accent_color' => ['nullable', 'regex:/^#[0-9A-F]{6}$/i'],
                'locale' => ['nullable', 'in:en,es,fr,de'],
                'logo_path' => ['nullable', 'image', 'max:5120', 'mimes:jpeg,png,gif,webp'],
                'feature_bookings' => ['nullable', 'in:0,1'],
                'feature_messaging' => ['nullable', 'in:0,1'],
                'feature_reviews' => ['nullable', 'in:0,1'],
                'feature_payments' => ['nullable', 'in:0,1'],
            ]);
        }

        $request->validate($rules);

        $role = $isTenantSignup
            ? User::ROLE_CLIENT
            : $request->input('role', User::ROLE_CLIENT);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'tenant_id' => $isTenantSignup ? $tenant?->getKey() : null,
            'phone' => $request->phone,
        ]);

        $user->syncRbacFromLegacyRole();

        if ($isTenantSignup && $tenant) {
            $user->syncEffectiveTenantPermissions($tenant);
        }

        if (! $isTenantSignup && $user->isOwner()) {
            $customizationData = null;
            if ($isOwnerRegistration) {
                $customizationData = [
                    'subscription_plan' => $request->input('subscription_plan'),
                    'app_title' => $request->input('app_title'),
                    'primary_color' => $request->input('primary_color', '#2E7D32'),
                    'accent_color' => $request->input('accent_color', '#43A047'),
                    'locale' => $request->input('locale', 'en'),
                    'feature_bookings' => $request->boolean('feature_bookings', true),
                    'feature_messaging' => $request->boolean('feature_messaging', true),
                    'feature_reviews' => $request->boolean('feature_reviews', true),
                    'feature_payments' => $request->boolean('feature_payments', true),
                    'logo_path' => null,
                ];

                if ($request->hasFile('logo_path')) {
                    $logoPath = $request->file('logo_path')->store('tenant-logos', 'public');
                    $customizationData['logo_path'] = $logoPath;
                }
            }

            $provisionedTenant = $user->ensureTenant($customizationData);

            if ($provisionedTenant) {
                TenantLifecycleLog::create([
                    'tenant_id' => $provisionedTenant->id,
                    'actor_user_id' => $user->id,
                    'action' => 'tenant.onboarding.started',
                    'reason' => 'Owner registered; awaiting mock payment and admin approval.',
                    'before_state' => [
                        'owner_email' => $user->email,
                    ],
                    'after_state' => [
                        'tenant_name' => $provisionedTenant->name,
                        'tenant_slug' => $provisionedTenant->slug,
                        'onboarding_status' => $provisionedTenant->onboarding_status,
                    ],
                ]);
            }
        }

        event(new Registered($user));

        if ($isTenantSignup) {
            Auth::login($user);

            return redirect()->to('/')
                ->with('success', 'Welcome to '.$tenant->name.'! Your account has been created. Browse our accommodations below.');
        }

        if ($user->isOwner() && $provisionedTenant) {
            Auth::login($user);

            return redirect()
                ->route('owner.onboarding.payment')
                ->with('success', 'Account created. Complete mock payment to submit your space for approval.');
        }

        Auth::login($user);

        return redirect($user->getDashboardRoute())
            ->with('success', 'Welcome to Impasugong Accommodations! Your account has been created.');
    }
}
