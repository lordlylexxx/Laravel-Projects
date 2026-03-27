<?php

namespace App\Models\Concerns;

use App\Models\Tenant;

trait UsesTenantConnectionWithLandlordFallback
{
    public function getConnectionName()
    {
        $tenantConnection = config('multitenancy.tenant_database_connection_name', 'tenant');
        $defaultConnection = config('database.default');
        $landlordConnection = config('multitenancy.landlord_database_connection_name', $defaultConnection);

        // If tenant resolution has already happened, always use tenant connection.
        if (Tenant::checkCurrent()) {
            return $tenantConnection;
        }

        // Keep framework tests on the configured test connection.
        if (app()->environment('testing')) {
            return $defaultConnection;
        }

        if (! $this->isTenantAppRequest()) {
            return $landlordConnection;
        }

        return Tenant::checkCurrent() ? $tenantConnection : $landlordConnection;
    }

    private function isTenantAppRequest(): bool
    {
        $appInstance = env('APP_INSTANCE');

        if ($appInstance === 'tenant') {
            return true;
        }

        if ($appInstance === 'central') {
            return false;
        }

        if (! app()->bound('request')) {
            return false;
        }

        $serverPort = (int) request()->server('SERVER_PORT', 0);
        $centralPort = (int) env('CENTRAL_PORT', 8000);

        return $serverPort > 0 && $serverPort !== $centralPort;
    }
}
