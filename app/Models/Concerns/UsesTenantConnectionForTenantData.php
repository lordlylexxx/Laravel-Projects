<?php

namespace App\Models\Concerns;

use App\Models\Tenant;

trait UsesTenantConnectionForTenantData
{
    public function getConnectionName()
    {
        $tenantConnection = config('multitenancy.tenant_database_connection_name', 'tenant');
        $defaultConnection = config('database.default');
        $landlordConnection = config('multitenancy.landlord_database_connection_name', $defaultConnection);

        // Keep framework tests on the configured test/default connection.
        if (app()->environment('testing')) {
            return $defaultConnection;
        }

        // If tenant is already resolved, always use tenant DB.
        if (Tenant::checkCurrent()) {
            return $tenantConnection;
        }

        // If an authenticated tenant-scoped user is acting, force tenant DB.
        if (app()->bound('request')) {
            $user = request()->user();

            if ($user && ! empty($user->tenant_id)) {
                return $tenantConnection;
            }
        }

        return $landlordConnection;
    }
}
