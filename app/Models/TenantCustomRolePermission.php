<?php

namespace App\Models;

use App\Models\Concerns\UsesTenantConnectionWithLandlordFallback;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantCustomRolePermission extends Model
{
    use UsesTenantConnectionWithLandlordFallback;

    protected $fillable = [
        'tenant_custom_role_id',
        'permission_name',
    ];

    public function tenantCustomRole(): BelongsTo
    {
        return $this->belongsTo(TenantCustomRole::class);
    }
}
