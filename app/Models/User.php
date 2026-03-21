<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        'last_login'
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
            'last_login' => 'datetime'
        ];
    }

    // Role constants
    const ROLE_CLIENT = 'client';
    const ROLE_OWNER = 'owner';
    const ROLE_ADMIN = 'admin';

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
            self::ROLE_ADMIN => 'Administrator'
        ];
        
        return $labels[$this->role] ?? $this->role;
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
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

    public function hasRole($role)
    {
        return $this->role === $role;
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
            'name' => $this->name . "'s Space",
            'slug' => Str::slug($this->name . '-' . $this->id . '-' . Str::random(6)),
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
            // Use custom app title if provided
            if (!empty($customizationData['app_title'])) {
                $tenantData['app_title'] = $customizationData['app_title'];
                $tenantData['database'] = $this->buildTenantDatabaseName($customizationData['app_title']);
            }

            // Apply theme colors
            if (!empty($customizationData['primary_color'])) {
                $tenantData['primary_color'] = $customizationData['primary_color'];
            }
            if (!empty($customizationData['accent_color'])) {
                $tenantData['accent_color'] = $customizationData['accent_color'];
            }

            // Apply logo if provided
            if (!empty($customizationData['logo_path'])) {
                $tenantData['logo_path'] = $customizationData['logo_path'];
            }

            // Apply locale
            if (!empty($customizationData['locale'])) {
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
        $tenantStartPort = (int) env('TENANT_PORT_START', 8001);
        $nextPort = max(Tenant::query()->max('app_port') ?? ($tenantStartPort - 1), $tenantStartPort - 1) + 1;
        $slugBase = Str::slug($this->name . '-' . $this->id);

        return [
            'domain' => $slugBase . '.' . $baseDomain,
            'app_port' => $nextPort,
            'database' => str_replace('-', '_', $slugBase),
            'db_host' => env('TENANT_DB_HOST', env('DB_HOST', '127.0.0.1')),
            'db_port' => (int) env('TENANT_DB_PORT', env('DB_PORT', 3306)),
            'db_username' => env('TENANT_DB_USERNAME', env('DB_USERNAME', 'root')),
            'db_password' => env('TENANT_DB_PASSWORD', env('DB_PASSWORD', '')),
        ];
    }

    private function buildTenantDatabaseName(string $businessName): string
    {
        $base = Str::slug($businessName, '_');
        $base = preg_replace('/[^A-Za-z0-9_]/', '', $base ?? '') ?: '';

        if ($base === '') {
            $base = 'tenant_' . $this->id;
        }

        // Keep a safe identifier length for MySQL and reserve room for suffix if needed.
        $base = substr($base, 0, 58);
        $database = $base;

        if (Tenant::query()->where('database', $database)->exists()) {
            $suffix = '_' . $this->id;
            $database = substr($base, 0, max(1, 64 - strlen($suffix))) . $suffix;
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

    public function getDashboardRoute()
    {
        if (Tenant::checkCurrent()) {
            return route('dashboard');
        }

        switch ($this->role) {
            case self::ROLE_ADMIN:
                return route('admin.dashboard');
            case self::ROLE_OWNER:
                return route('owner.dashboard');
            default:
                return route('dashboard');
        }
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

