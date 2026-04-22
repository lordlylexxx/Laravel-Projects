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

        // Keep CLI / non-request contexts central when explicitly configured.
        if ($appInstance === 'central' && ! app()->bound('request')) {
            return false;
        }

        if (! app()->bound('request')) {
            return false;
        }

        $requestHost = request()->getHost();
        $centralDomain = (string) env(
            'CENTRAL_DOMAIN',
            parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost'
        );

        return ! in_array($requestHost, [$centralDomain, 'localhost', '127.0.0.1', '::1'], true);
    }
}
