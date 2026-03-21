<?php

namespace App\Multitenancy\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Concerns\UsesMultitenancyConfig;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Exceptions\InvalidConfiguration;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class SwitchTenantDatabaseConnectionTask implements SwitchTenantTask
{
    use UsesMultitenancyConfig;

    protected array $tenantConnectionDefaults;

    public function __construct()
    {
        $tenantConnectionName = $this->tenantDatabaseConnectionName();
        $this->tenantConnectionDefaults = (array) config("database.connections.{$tenantConnectionName}", []);
    }

    public function makeCurrent(IsTenant $tenant): void
    {
        $this->setTenantConnectionConfig([
            'database' => $tenant->database,
            'host' => $tenant->db_host ?: ($this->tenantConnectionDefaults['host'] ?? null),
            'port' => $tenant->db_port ?: ($this->tenantConnectionDefaults['port'] ?? null),
            'username' => $tenant->db_username ?: ($this->tenantConnectionDefaults['username'] ?? null),
            'password' => $tenant->db_password ?: ($this->tenantConnectionDefaults['password'] ?? null),
        ]);
    }

    public function forgetCurrent(): void
    {
        $this->setTenantConnectionConfig([
            'database' => $this->tenantConnectionDefaults['database'] ?? null,
            'host' => $this->tenantConnectionDefaults['host'] ?? null,
            'port' => $this->tenantConnectionDefaults['port'] ?? null,
            'username' => $this->tenantConnectionDefaults['username'] ?? null,
            'password' => $this->tenantConnectionDefaults['password'] ?? null,
        ]);
    }

    protected function setTenantConnectionConfig(array $overrides): void
    {
        $tenantConnectionName = $this->tenantDatabaseConnectionName();

        if ($tenantConnectionName === $this->landlordDatabaseConnectionName()) {
            throw InvalidConfiguration::tenantConnectionIsEmptyOrEqualsToLandlordConnection();
        }

        if (is_null(config("database.connections.{$tenantConnectionName}"))) {
            throw InvalidConfiguration::tenantConnectionDoesNotExist($tenantConnectionName);
        }

        $connectionConfig = array_merge($this->tenantConnectionDefaults, $overrides);

        config([
            "database.connections.{$tenantConnectionName}" => $connectionConfig,
        ]);

        app('db')->extend($tenantConnectionName, function ($config, $name) use ($connectionConfig) {
            $config = array_merge($config, $connectionConfig);

            return app('db.factory')->make($config, $name);
        });

        DB::purge($tenantConnectionName);
        Model::setConnectionResolver(app('db'));
    }
}
