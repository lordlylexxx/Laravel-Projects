<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function getConnectionName()
    {
        return config('multitenancy.landlord_database_connection_name', config('database.default'));
    }
}
