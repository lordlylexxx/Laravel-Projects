<?php

namespace App\Models\Concerns;

use App\Models\Tenant;

trait UsesTenantConnectionWithLandlordFallback
{
    public function getConnectionName()
    {
        $tenantConnection = config('multitenancy.tenant_database_connection_name', 'tenant');
        $landlordConnection = config('multitenancy.landlord_database_connection_name', config('database.default'));

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
