<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TenantAdminProvisionedMail;
use App\Models\Tenant;
use App\Models\TenantLifecycleLog;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Multitenancy\Actions\MakeTenantCurrentAction;
use Spatie\Multitenancy\Actions\ForgetCurrentTenantAction;

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
                'subscription_plan' => ['nullable', 'in:basic,plus,pro'],
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

                // Handle logo upload
                if ($request->hasFile('logo_path')) {
                    $logoPath = $request->file('logo_path')->store('tenant-logos', 'public');
                    $customizationData['logo_path'] = $logoPath;
                }
            }

            $provisionedTenant = $user->ensureTenant($customizationData);

            if ($provisionedTenant) {
                $this->provisionTenantAdminAndSendEmail($user, $provisionedTenant);

                TenantLifecycleLog::create([
                    'tenant_id' => $provisionedTenant->id,
                    'actor_user_id' => $user->id,
                    'action' => 'tenant.onboarding.completed',
                    'reason' => 'Owner signup provisioning completed.',
                    'before_state' => [
                        'owner_email' => $user->email,
                    ],
                    'after_state' => [
                        'tenant_name' => $provisionedTenant->name,
                        'tenant_slug' => $provisionedTenant->slug,
                        'tenant_url' => $provisionedTenant->publicUrl(),
                    ],
                ]);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        // For tenant registrations, redirect to landing page; otherwise to dashboard
        if ($isTenantSignup) {
            return redirect()->to('/')
                ->with('success', 'Welcome to ' . $tenant->name . '! Your account has been created. Browse our accommodations below.');
        }

        return redirect($user->getDashboardRoute())
            ->with('success', 'Welcome to Impasugong Accommodations! Your account has been created.');
    }

    /**
     * Create a tenant-scoped admin account and send credentials to the owner email.
     */
    private function provisionTenantAdminAndSendEmail(User $owner, Tenant $tenant): void
    {
        try {
            $adminEmail = $this->buildUniqueTenantAdminEmail($tenant);
            $plainPassword = Str::random(12);

            // Make tenant current to ensure admin user is created in tenant database
            app(MakeTenantCurrentAction::class)->execute($tenant);

            try {
                $tenantAdmin = User::create([
                    'name' => $tenant->name . ' Admin',
                    'email' => $adminEmail,
                    'password' => Hash::make($plainPassword),
                    'role' => User::ROLE_ADMIN,
                    'tenant_id' => $tenant->id,
                    'phone' => null,
                ]);

                Log::info('Tenant admin account created successfully.', [
                    'tenant_id' => $tenant->id,
                    'admin_user_id' => $tenantAdmin->id,
                    'admin_email' => $adminEmail,
                ]);
            } finally {
                // Restore no tenant context
                app(ForgetCurrentTenantAction::class)->execute($tenant);
            }

            // Send email to owner with admin credentials
            try {
                Mail::to($owner->email)->send(new TenantAdminProvisionedMail(
                    ownerName: $owner->name,
                    businessName: $tenant->name,
                    businessUrl: $tenant->publicUrl(),
                    adminEmail: $tenantAdmin->email,
                    adminPassword: $plainPassword
                ));

                Log::info('Tenant admin provisioning email sent.', [
                    'owner_email' => $owner->email,
                    'tenant_id' => $tenant->id,
                    'admin_email' => $tenantAdmin->email,
                ]);
            } catch (\Throwable $exception) {
                Log::warning('Failed to send tenant admin provisioning email.', [
                    'owner_user_id' => $owner->id,
                    'tenant_id' => $tenant->id,
                    'admin_email' => $adminEmail,
                    'error' => $exception->getMessage(),
                ]);
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to provision tenant admin account.', [
                'owner_user_id' => $owner->id,
                'tenant_id' => $tenant->id,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }
    }

    /**
     * Build a unique tenant admin email for login credentials.
     */
    private function buildUniqueTenantAdminEmail(Tenant $tenant): string
    {
        $base = 'admin@' . ($tenant->domain ?: ($tenant->slug . '.localhost'));

        // Check in landlord database to avoid duplicate emails globally
        if (! DB::connection('landlord')->table('users')->where('email', $base)->exists()) {
            return $base;
        }

        $prefix = 'admin+' . ($tenant->slug ?: 'tenant');
        $domain = 'impastay.local';
        $counter = 1;

        do {
            $candidate = $prefix . $counter . '@' . $domain;
            $counter++;
        } while (DB::connection('landlord')->table('users')->where('email', $candidate)->exists());

        return $candidate;
    }

}
