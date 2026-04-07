<?php

namespace App\Models;

use App\Models\Concerns\UsesPermissionTablesConnection;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use UsesPermissionTablesConnection;
}
