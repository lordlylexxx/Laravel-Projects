<?php

namespace App\Services;

use App\Mail\TenantAdminProvisionedMail;
use App\Models\Tenant;
use App\Models\TenantLifecycleLog;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Multitenancy\Actions\ForgetCurrentTenantAction;
use Spatie\Multitenancy\Actions\MakeTenantCurrentAction;

class TenantOnboardingService
{
    public function provisionDatabaseIfNeeded(Tenant $tenant): bool
    {
        if (! $tenant->database) {
            Log::warning('Tenant has no database name for provisioning.', ['tenant_id' => $tenant->id]);

            return false;
        }

        $tenant->refresh();

        if ($tenant->database_provisioned) {
            return true;
        }

        try {
            $exitCode = Artisan::call('tenants:provision-db', [
                'tenantId' => $tenant->id,
            ]);
            $tenant->refresh();

            return $exitCode === 0 && (bool) $tenant->database_provisioned;
        } catch (\Throwable $exception) {
            Log::error('Tenant DB provisioning failed.', [
                'tenant_id' => $tenant->id,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Approve tenant registration: provision DB (if needed), activate domain, create tenant admin + email credentials to owner.
     *
     * @param  bool  $allowFromPendingPayment  When true, allows approving from awaiting_payment (e.g. seed / admin override).
     * @return array{success: bool, credentials_emailed: bool|null} credentials_emailed null when skipped (e.g. no owner email)
     */
    public function approveRegistration(Tenant $tenant, ?User $actor, bool $allowFromPendingPayment = false): array
    {
        $allowed = [Tenant::ONBOARDING_PENDING_APPROVAL];
        if ($allowFromPendingPayment) {
            $allowed[] = Tenant::ONBOARDING_AWAITING_PAYMENT;
        }

        if ($tenant->onboarding_status === Tenant::ONBOARDING_APPROVED && $tenant->database_provisioned) {
            return ['success' => true, 'credentials_emailed' => null];
        }

        if (! in_array($tenant->onboarding_status, $allowed, true)) {
            return ['success' => false, 'credentials_emailed' => null];
        }

        if (! $this->provisionDatabaseIfNeeded($tenant)) {
            return ['success' => false, 'credentials_emailed' => null];
        }

        $tenant->refresh();

        $tenant->update([
            'onboarding_status' => Tenant::ONBOARDING_APPROVED,
            'domain_enabled' => true,
            'domain_disabled_at' => null,
            'onboarding_approved_at' => now(),
            'onboarding_approved_by' => $actor?->id,
        ]);

        $owner = $tenant->owner;
        $credentialsEmailed = null;
        if ($owner) {
            $credentialsEmailed = $this->provisionTenantAdminAndNotify($owner, $tenant);
        }

        return ['success' => true, 'credentials_emailed' => $credentialsEmailed];
    }

    public function rejectRegistration(Tenant $tenant, User $actor, string $reason): void
    {
        if ($tenant->onboarding_status !== Tenant::ONBOARDING_PENDING_APPROVAL) {
            return;
        }

        $tenant->update([
            'onboarding_status' => Tenant::ONBOARDING_REJECTED,
        ]);

        TenantLifecycleLog::create([
            'tenant_id' => $tenant->id,
            'actor_user_id' => $actor->id,
            'action' => 'tenant.onboarding.rejected',
            'reason' => $reason,
            'before_state' => [
                'onboarding_status' => Tenant::ONBOARDING_PENDING_APPROVAL,
            ],
            'after_state' => [
                'onboarding_status' => Tenant::ONBOARDING_REJECTED,
            ],
        ]);
    }

    /**
     * Create or update the dedicated tenant-database admin user, assign RBAC, and email login details to the owner.
     * If an admin already exists, the password is rotated and the new random password is emailed (approval / resend flows).
     */
    public function provisionTenantAdminAndNotify(User $owner, Tenant $tenant): bool
    {
        if ($owner->email === null || $owner->email === '') {
            Log::warning('Tenant admin notify skipped: owner has no email.', ['tenant_id' => $tenant->id]);

            return false;
        }

        $tenantAdmin = null;
        $plainPassword = '';

        try {
            app(MakeTenantCurrentAction::class)->execute($tenant);
            $previousTeam = getPermissionsTeamId();
            setPermissionsTeamId($tenant->id);

            try {
                $existing = User::query()
                    ->where('tenant_id', $tenant->id)
                    ->where('role', User::ROLE_ADMIN)
                    ->oldest('id')
                    ->first();

                $plainPassword = Str::random(16);

                if ($existing) {
                    $existing->update([
                        'password' => Hash::make($plainPassword),
                    ]);
                    $tenantAdmin = $existing->fresh();

                    Log::info('Tenant admin password rotated for credential email.', [
                        'tenant_id' => $tenant->id,
                        'admin_user_id' => $tenantAdmin->id,
                        'admin_email' => $tenantAdmin->email,
                    ]);
                } else {
                    $adminEmail = $this->buildUniqueTenantAdminEmail($tenant);

                    $tenantAdmin = User::create([
                        'name' => $tenant->name.' Admin',
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
                }

                $tenantAdmin->syncRbacFromLegacyRole();
            } finally {
                setPermissionsTeamId($previousTeam);
                app(ForgetCurrentTenantAction::class)->execute($tenant);
            }

            if ($tenantAdmin === null) {
                return false;
            }

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

            return true;
        } catch (\Throwable $exception) {
            Log::error('Failed to provision tenant admin or send credentials email.', [
                'owner_user_id' => $owner->id,
                'tenant_id' => $tenant->id,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return false;
        }
    }

    public function buildUniqueTenantAdminEmail(Tenant $tenant): string
    {
        $base = 'admin@'.($tenant->domain ?: ($tenant->slug.'.localhost'));

        if (! DB::connection('landlord')->table('users')->where('email', $base)->exists()) {
            return $base;
        }

        $prefix = 'admin+'.($tenant->slug ?: 'tenant');
        $domain = 'impastay.local';
        $counter = 1;

        do {
            $candidate = $prefix.$counter.'@'.$domain;
            $counter++;
        } while (DB::connection('landlord')->table('users')->where('email', $candidate)->exists());

        return $candidate;
    }
}
