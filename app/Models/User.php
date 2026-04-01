<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\UsesTenantConnectionWithLandlordFallback;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    use HasRoles {
        hasRole as private hasSpatieRole;
        hasPermissionTo as private hasSpatiePermission;
        hasAnyPermission as private hasAnySpatiePermission;
    }
    use UsesTenantConnectionWithLandlordFallback;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tenant_id',
        'phone',
        'address',
        'bio',
        'avatar',
        'is_active',
        'last_login',
        'notification_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login' => 'datetime',
            'notification_preferences' => 'array',
        ];
    }

    // Role constants
    const ROLE_CLIENT = 'client';

    const ROLE_OWNER = 'owner';

    const ROLE_ADMIN = 'admin';

    public const PERM_USERS_VIEW = 'users.view';

    public const PERM_USERS_CREATE = 'users.create';

    public const PERM_USERS_UPDATE = 'users.update';

    public const PERM_USERS_ACTIVATE = 'users.activate';

    public const PERM_USERS_ASSIGN_ROLES = 'users.assign_roles';

    public const PERM_USERS_ASSIGN_PERMISSIONS = 'users.assign_permissions';

    public const PERM_ACCOMMODATIONS_CREATE = 'accommodations.create';

    public const PERM_ACCOMMODATIONS_UPDATE = 'accommodations.update';

    public const PERM_ACCOMMODATIONS_DELETE = 'accommodations.delete';

    public const PERM_BOOKINGS_MANAGE = 'bookings.manage';

    public const PERM_MESSAGES_MANAGE = 'messages.manage';

    public const PERM_REPORTS_VIEW = 'reports.view';

    protected string $guard_name = 'web';

    // Relationships
    public function accommodations()
    {
        return $this->hasMany(Accommodation::class, 'owner_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function ownedTenant(): HasOne
    {
        return $this->hasOne(Tenant::class, 'owner_user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // Scopes
    public function scopeClients($query)
    {
        return $query->where('role', self::ROLE_CLIENT);
    }

    public function scopeOwners($query)
    {
        return $query->where('role', self::ROLE_OWNER);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRecentLogins($query, $days = 30)
    {
        return $query->where('last_login', '>=', now()->subDays($days));
    }

    // Accessors
    public function getRoleLabelAttribute()
    {
        $labels = [
            self::ROLE_CLIENT => 'Client',
            self::ROLE_OWNER => 'Accommodation Owner',
            self::ROLE_ADMIN => 'Administrator',
        ];

        return $labels[$this->role] ?? $this->role;
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/avatars/'.$this->avatar);
        }

        // Return default avatar based on initials
        $initials = strtoupper(substr($this->name, 0, 2));

        return "https://ui-avatars.com/api/?name={$initials}&background=2E7D32&color=fff&size=128";
    }

    public function getIsClientAttribute()
    {
        return $this->role === self::ROLE_CLIENT;
    }

    public function getIsOwnerAttribute()
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function getIsAdminAttribute()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    // Methods
    public function isClient()
    {
        return $this->role === self::ROLE_CLIENT;
    }

    public function isOwner()
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function hasRole($role): bool
    {
        if (is_array($role)) {
            return in_array($this->role, $role, true) || ($this->withPermissionsTeamContext(
                fn (): bool => $this->hasSpatieRole($role, $this->guard_name)
            ) ?? false);
        }

        if (is_string($role) && $this->role === $role) {
            return true;
        }

        return $this->withPermissionsTeamContext(
            fn (): bool => $this->hasSpatieRole($role, $this->guard_name)
        ) ?? false;
    }

    public function hasPermission($permission): bool
    {
        if (! is_array($permission) && ! is_string($permission)) {
            return false;
        }

        if (is_array($permission)) {
            return $this->withPermissionsTeamContext(
                fn (): bool => $this->hasAnySpatiePermission($permission)
            ) ?? false;
        }

        return $this->withPermissionsTeamContext(
            fn (): bool => $this->hasSpatiePermission($permission, $this->guard_name)
        ) ?? false;
    }

    public function syncRbacFromLegacyRole(): void
    {
        if (! in_array($this->role, [self::ROLE_ADMIN, self::ROLE_OWNER, self::ROLE_CLIENT], true)) {
            return;
        }

        $this->withPermissionsTeamContext(function (): void {
            $this->syncRoles([$this->role]);
        });
    }

    public function syncTenantPermissions(array $permissions): void
    {
        $this->withPermissionsTeamContext(function () use ($permissions): void {
            $this->syncPermissions($permissions);
        });
    }

    private function withPermissionsTeamContext(callable $callback): mixed
    {
        if (! config('permission.teams')) {
            return $callback();
        }

        $registrar = app(PermissionRegistrar::class);
        $previousTeamId = $registrar->getPermissionsTeamId();

        $teamId = $this->resolvePermissionsTeamId();
        if ($teamId === null) {
            return null;
        }

        $registrar->setPermissionsTeamId($teamId);

        try {
            return $callback();
        } finally {
            $registrar->setPermissionsTeamId($previousTeamId);
        }
    }

    private function resolvePermissionsTeamId(): ?int
    {
        if ($this->tenant_id !== null) {
            return (int) $this->tenant_id;
        }

        $currentTenant = Tenant::current();
        if ($currentTenant) {
            return (int) $currentTenant->id;
        }

        if ($this->isOwner() && $this->ownedTenant) {
            return (int) $this->ownedTenant->id;
        }

        return null;
    }

    public function updateLastLogin()
    {
        $this->update(['last_login' => now()]);
    }

    public function ensureTenant($customizationData = null): ?Tenant
    {
        if (! $this->isOwner()) {
            return null;
        }

        if ($this->tenant) {
            $defaults = $this->defaultTenantConnectionAttributes();

            foreach ($defaults as $key => $value) {
                if (is_null($this->tenant->{$key}) || $this->tenant->{$key} === '') {
                    $this->tenant->{$key} = $value;
                }
            }

            if ($this->tenant->isDirty()) {
                $this->tenant->save();
            }

            return $this->tenant;
        }

        $tenantData = [
            'name' => $this->name."'s Space",
            'slug' => Str::slug($this->name.'-'.$this->id.'-'.Str::random(6)),
            'owner_user_id' => $this->id,
            'plan' => Tenant::PLAN_BASIC,
            'subscription_status' => 'trialing',
            'trial_ends_at' => now()->addDays(14),
            'current_period_starts_at' => now(),
            'current_period_ends_at' => now()->addMonth(),
            ...$this->defaultTenantConnectionAttributes(),
        ];

        // Apply customization if provided
        if ($customizationData && is_array($customizationData)) {
            if (! empty($customizationData['subscription_plan'])
                && in_array($customizationData['subscription_plan'], [Tenant::PLAN_BASIC, Tenant::PLAN_PLUS, Tenant::PLAN_PRO], true)) {
                $tenantData['plan'] = $customizationData['subscription_plan'];
            }

            // Use custom app title if provided
            if (! empty($customizationData['app_title'])) {
                $tenantSlug = $this->buildTenantSlug($customizationData['app_title']);

                $tenantData['name'] = $customizationData['app_title'];
                $tenantData['app_title'] = $customizationData['app_title'];
                $tenantData['slug'] = $tenantSlug;
                $tenantData['domain'] = $this->buildTenantDomainFromSlug($tenantSlug);
                $tenantData['database'] = $this->buildTenantDatabaseName($customizationData['app_title']);
            }

            // Apply theme colors
            if (! empty($customizationData['primary_color'])) {
                $tenantData['primary_color'] = $customizationData['primary_color'];
            }
            if (! empty($customizationData['accent_color'])) {
                $tenantData['accent_color'] = $customizationData['accent_color'];
            }

            // Apply logo if provided
            if (! empty($customizationData['logo_path'])) {
                $tenantData['logo_path'] = $customizationData['logo_path'];
            }

            // Apply locale
            if (! empty($customizationData['locale'])) {
                $tenantData['locale'] = $customizationData['locale'];
            }

            // Apply feature flags
            if (isset($customizationData['feature_bookings'])) {
                $tenantData['feature_bookings'] = (bool) $customizationData['feature_bookings'];
            }
            if (isset($customizationData['feature_messaging'])) {
                $tenantData['feature_messaging'] = (bool) $customizationData['feature_messaging'];
            }
            if (isset($customizationData['feature_reviews'])) {
                $tenantData['feature_reviews'] = (bool) $customizationData['feature_reviews'];
            }
            if (isset($customizationData['feature_payments'])) {
                $tenantData['feature_payments'] = (bool) $customizationData['feature_payments'];
            }
        }

        $tenant = Tenant::create($tenantData);

        // Automatically create and migrate the tenant database right after owner registration.
        $this->provisionTenantDatabase($tenant);

        $this->update(['tenant_id' => $tenant->id]);

        return $this->fresh()->tenant;
    }

    private function defaultTenantConnectionAttributes(): array
    {
        $baseDomain = env('TENANT_BASE_DOMAIN', env('CENTRAL_DOMAIN', parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost'));
        $slugBase = Str::slug($this->name.'-'.$this->id);

        return [
            'domain' => $slugBase.'.'.$baseDomain,
            // Tenants are now domain-based and share the central app port.
            'app_port' => null,
            'database' => str_replace('-', '_', $slugBase),
            'db_host' => env('TENANT_DB_HOST', env('DB_HOST', '127.0.0.1')),
            'db_port' => (int) env('TENANT_DB_PORT', env('DB_PORT', 3306)),
            'db_username' => env('TENANT_DB_USERNAME', env('DB_USERNAME', 'root')),
            'db_password' => env('TENANT_DB_PASSWORD', env('DB_PASSWORD', '')),
        ];
    }

    private function buildTenantSlug(string $businessName): string
    {
        $base = Str::slug($businessName);

        if ($base === '') {
            $base = 'tenant-'.$this->id;
        }

        // Keep room for uniqueness suffixes.
        $base = substr($base, 0, 48);
        $slug = $base;

        if (Tenant::query()->where('slug', $slug)->exists()) {
            $suffix = '-'.$this->id;
            $slug = substr($base, 0, max(1, 63 - strlen($suffix))).$suffix;
        }

        $counter = 2;
        while (Tenant::query()->where('slug', $slug)->exists()) {
            $suffix = '-'.$this->id.'-'.$counter;
            $slug = substr($base, 0, max(1, 63 - strlen($suffix))).$suffix;
            $counter++;
        }

        return $slug;
    }

    private function buildTenantDomainFromSlug(string $slug): string
    {
        $baseDomain = env('TENANT_BASE_DOMAIN', env('CENTRAL_DOMAIN', parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost'));

        return $slug.'.'.$baseDomain;
    }

    private function buildTenantDatabaseName(string $businessName): string
    {
        $base = Str::slug($businessName, '_');
        $base = preg_replace('/[^A-Za-z0-9_]/', '', $base ?? '') ?: '';

        if ($base === '') {
            $base = 'tenant_'.$this->id;
        }

        // Keep a safe identifier length for MySQL and reserve room for suffix if needed.
        $base = substr($base, 0, 58);
        $database = $base;

        if (Tenant::query()->where('database', $database)->exists()) {
            $suffix = '_'.$this->id;
            $database = substr($base, 0, max(1, 64 - strlen($suffix))).$suffix;
        }

        return $database;
    }

    private function provisionTenantDatabase(Tenant $tenant): void
    {
        if (! $tenant->database) {
            return;
        }

        try {
            $exitCode = Artisan::call('tenants:provision-db', [
                'tenantId' => $tenant->id,
            ]);

            if ($exitCode !== 0) {
                Log::warning('Tenant DB provisioning returned non-zero status.', [
                    'tenant_id' => $tenant->id,
                    'database' => $tenant->database,
                    'exit_code' => $exitCode,
                    'output' => Artisan::output(),
                ]);
            }
        } catch (\Throwable $exception) {
            Log::error('Tenant DB provisioning failed during owner registration.', [
                'tenant_id' => $tenant->id,
                'database' => $tenant->database,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function getDashboardRoute(): string
    {
        if (Tenant::checkCurrent()) {
            return '/dashboard';
        }

        return match ($this->role) {
            self::ROLE_ADMIN => '/admin/dashboard',
            self::ROLE_OWNER => '/owner/dashboard',
            default => '/dashboard',
        };
    }

    // Dashboard statistics methods
    public function getClientBookingsCount()
    {
        return $this->bookings()->count();
    }

    public function getOwnerAccommodationsCount()
    {
        return $this->accommodations()->count();
    }

    public function getUnreadMessagesCount()
    {
        return $this->receivedMessages()->unread()->count();
    }

    public function getPendingBookingsCount()
    {
        if ($this->isOwner()) {
            return Booking::forOwner($this->id)->pending()->count();
        }

        return $this->bookings()->pending()->count();
    }
}
