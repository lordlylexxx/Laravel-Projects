<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantUpdate extends Model
{
    protected $connection = 'landlord';

    public const STATUS_UPDATE_AVAILABLE = 'update_available';
    public const STATUS_UPDATED = 'updated';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'tenant_id',
        'app_release_id',
        'status',
        'is_current',
        'applied_at',
        'required_at',
        'grace_until',
        'failure_reason',
        'metadata',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'applied_at' => 'datetime',
        'required_at' => 'datetime',
        'grace_until' => 'datetime',
        'metadata' => 'array',
    ];

    public function release(): BelongsTo
    {
        return $this->belongsTo(AppRelease::class, 'app_release_id');
    }

    public function appRelease(): BelongsTo
    {
        return $this->belongsTo(AppRelease::class, 'app_release_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
