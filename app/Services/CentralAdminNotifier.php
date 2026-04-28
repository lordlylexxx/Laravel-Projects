<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use App\Notifications\Central\AdminImportantNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CentralAdminNotifier
{
    /**
     * Notify Tulogans admins when a new owner registers on the central app and a tenant is provisioned (plan / onboarding flow).
     */
    public function notifyNewOwnerRegistered(Tenant $tenant, User $owner): void
    {
        try {
            $connection = (string) config('multitenancy.landlord_database_connection_name', 'landlord');
            $admins = User::on($connection)
                ->where('role', User::ROLE_ADMIN)
                ->whereNull('tenant_id')
                ->get();

            if ($admins->isEmpty()) {
                return;
            }

            $planKey = (string) ($tenant->plan ?? Tenant::PLAN_BASIC);
            $planLabel = Tenant::planLabel($planKey);
            $ownerName = (string) $owner->name;
            $ownerEmail = (string) $owner->email;
            $tenantName = (string) $tenant->name;

            Notification::send(
                $admins,
                new AdminImportantNotification(
                    title: 'New owner registered',
                    body: "{$ownerName} ({$ownerEmail}) registered for {$tenantName} on the {$planLabel} plan. They are on onboarding (awaiting payment).",
                    actionUrl: '/admin/tenants',
                    actionLabel: 'Open Tulogans',
                )
            );
        } catch (\Throwable $exception) {
            Log::warning('Central admin new-owner registration notification failed.', [
                'tenant_id' => $tenant->id,
                'owner_user_id' => $owner->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function notifyOnboardingPaymentPendingReview(Tenant $tenant): void
    {
        try {
            $connection = (string) config('multitenancy.landlord_database_connection_name', 'landlord');
            $admins = User::on($connection)
                ->where('role', User::ROLE_ADMIN)
                ->whereNull('tenant_id')
                ->get();

            if ($admins->isEmpty()) {
                return;
            }

            Notification::send(
                $admins,
                new AdminImportantNotification(
                    title: 'Onboarding payment submitted',
                    body: "{$tenant->name} submitted payment and is pending Tulogans approval.",
                    actionUrl: '/admin/tenants',
                    actionLabel: 'Open Tulogans',
                )
            );
        } catch (\Throwable $exception) {
            Log::warning('Central admin onboarding notification failed.', [
                'tenant_id' => $tenant->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
