<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
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

        if (! $isTenantSignup && $user->isOwner()) {
            $customizationData = null;
            if ($isOwnerRegistration) {
                $customizationData = [
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

                // Handle logo upload
                if ($request->hasFile('logo_path')) {
                    $logoPath = $request->file('logo_path')->store('tenant-logos', 'public');
                    $customizationData['logo_path'] = $logoPath;
                }
            }

            $user->ensureTenant($customizationData);
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role
        return redirect($user->getDashboardRoute())
            ->with('success', 'Welcome to Impasugong Accommodations! Your account has been created.');
    }
}
