<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppRelease extends Model
{
    protected $connection = 'landlord';

    protected $fillable = [
        'tag',
        'title',
        'changelog',
        'release_url',
        'published_at',
        'is_stable',
        'is_required',
        'synced_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_stable' => 'boolean',
        'is_required' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public function tenantUpdates(): HasMany
    {
        return $this->hasMany(TenantUpdate::class);
    }
}
