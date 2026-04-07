<?php

namespace App\Models;

use App\Models\Concerns\UsesPermissionTablesConnection;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use UsesPermissionTablesConnection;
}
