<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    public function getConnectionName()
    {
        return config('multitenancy.landlord_database_connection_name', config('database.default'));
    }
}
