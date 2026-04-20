<?php

namespace App\Models;

use App\Models\Concerns\UsesTenantConnectionWithLandlordFallback;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TenantCustomRole extends Model
{
    use UsesTenantConnectionWithLandlordFallback;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_custom_role_id');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(TenantCustomRolePermission::class);
    }

    /**
     * @return list<string>
     */
    public function permissionNames(): array
    {
        return $this->permissions()
            ->orderBy('permission_name')
            ->pluck('permission_name')
            ->map(fn ($value) => (string) $value)
            ->values()
            ->all();
    }

    public static function normalizeSlug(string $name): string
    {
        $slug = Str::slug($name);

        return $slug === '' ? 'custom-role' : $slug;
    }
}
