<?php

namespace App\Services\Messaging;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * In-app messages require sender/receiver rows in the tenant DB. Landlord "central admin"
 * users do not exist there, so we use a non-login proxy user per tenant.
 */
final class TenantCentralSupportProxyUser
{
    public static function emailForTenant(Tenant $tenant): string
    {
        return '__impastay_central_support.tenant-'.(int) $tenant->getKey().'@internal.invalid';
    }

    public static function isProxy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return str_starts_with((string) $user->email, '__impastay_central_support.tenant-');
    }

    public static function ensure(Tenant $tenant): User
    {
        $email = self::emailForTenant($tenant);

        $user = User::query()->where('email', $email)->first();

        if ($user) {
            return $user;
        }

        $user = User::create([
            'name' => 'ImpaStay (Central Admin)',
            'email' => $email,
            'password' => Hash::make(Str::random(48)),
            'role' => User::ROLE_CLIENT,
            'tenant_id' => $tenant->id,
            'is_active' => false,
        ]);

        $user->syncRbacFromLegacyRole();

        return $user;
    }
}
