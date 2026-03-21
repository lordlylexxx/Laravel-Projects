<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    public const PLAN_BASIC = 'basic';
    public const PLAN_PLUS = 'plus';
    public const PLAN_PRO = 'pro';

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'domain_enabled',
        'domain_disabled_at',
        'app_port',
        'database',
        'db_host',
        'db_port',
        'db_username',
        'db_password',
        'owner_user_id',
        'plan',
        'subscription_status',
        'trial_ends_at',
        'current_period_starts_at',
        'current_period_ends_at',
        'metadata',
        'app_title',
        'primary_color',
        'accent_color',
        'logo_path',
        'locale',
        'feature_bookings',
        'feature_messaging',
        'feature_reviews',
        'feature_payments',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'current_period_starts_at' => 'datetime',
            'current_period_ends_at' => 'datetime',
            'domain_enabled' => 'boolean',
            'domain_disabled_at' => 'datetime',
            'app_port' => 'integer',
            'db_port' => 'integer',
            'db_password' => 'encrypted',
            'metadata' => 'array',
            'feature_bookings' => 'boolean',
            'feature_messaging' => 'boolean',
            'feature_reviews' => 'boolean',
            'feature_payments' => 'boolean',
        ];
    }

    public function publicUrl(): string
    {
        $host = env('TENANCY_BASE_HOST', parse_url((string) config('app.url'), PHP_URL_HOST) ?: '127.0.0.1');

        if ($this->app_port) {
            return 'http://' . $host . ':' . $this->app_port;
        }

        if ($this->domain) {
            return 'http://' . $this->domain;
        }

        return 'http://' . $host;
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function hasActiveSubscription(): bool
    {
        return in_array($this->subscription_status, ['trialing', 'active'], true);
    }

    public function maxListings(): ?int
    {
        return match ($this->plan) {
            self::PLAN_BASIC => 5,
            self::PLAN_PLUS => 20,
            self::PLAN_PRO => null,
            default => 5,
        };
    }

    public function canCreateAccommodation(int $currentCount): bool
    {
        if (! $this->hasActiveSubscription()) {
            return false;
        }

        $maxListings = $this->maxListings();

        return is_null($maxListings) || $currentCount < $maxListings;
    }

    public function landingSettings(): array
    {
        $metadata = is_array($this->metadata) ? $this->metadata : [];
        $landing = is_array($metadata['landing'] ?? null) ? $metadata['landing'] : [];

        return array_merge($this->defaultLandingSettings(), $landing);
    }

    public function updateLandingSettings(array $settings): void
    {
        $metadata = is_array($this->metadata) ? $this->metadata : [];
        $metadata['landing'] = array_merge($this->defaultLandingSettings(), $settings);

        $this->update(['metadata' => $metadata]);
    }

    private function defaultLandingSettings(): array
    {
        $ownerName = $this->owner?->name ?? 'Owner';

        return [
            'hero_title' => $this->name . ' Stays',
            'hero_subtitle' => 'Book trusted accommodations managed by ' . $ownerName . '.',
            'cta_text' => 'Browse Accommodations',
            'cta_url' => '/accommodations',
            'login_section_title' => 'Access Your Account',
            'login_section_subtitle' => 'Use login if you already have an account, or sign up as a new user.',
            'login_text' => 'Login',
            'signup_text' => 'Sign Up',
            'about_title' => 'About Our Property Network',
            'about_text' => 'We offer comfortable stays and responsive support for travelers who want a smooth booking experience.',
            'primary_color' => '#14532d',
            'accent_color' => '#16a34a',
            'hero_image_url' => asset('SYSTEMLOGO.png'),
        ];
    }

    /**
     * Get the app title/display name for the tenant
     * Falls back to tenant name if app_title is not set
     */
    public function getAppTitle(): string
    {
        return $this->app_title ?: $this->name;
    }

    /**
     * Get the primary theme color
     */
    public function getPrimaryColor(): string
    {
        return $this->primary_color ?? '#2E7D32';
    }

    /**
     * Get the accent theme color
     */
    public function getAccentColor(): string
    {
        return $this->accent_color ?? '#43A047';
    }

    /**
     * Get the logo path URL
     */
    public function getLogoUrl(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    /**
     * Check if a specific feature is enabled
     */
    public function isFeatureEnabled(string $feature): bool
    {
        $featureKey = 'feature_' . $feature;
        if (!property_exists($this, $featureKey)) {
            return false;
        }
        
        return (bool) $this->{$featureKey};
    }

    /**
     * Get all enabled features
     */
    public function getEnabledFeatures(): array
    {
        return [
            'bookings' => $this->feature_bookings ?? true,
            'messaging' => $this->feature_messaging ?? true,
            'reviews' => $this->feature_reviews ?? true,
            'payments' => $this->feature_payments ?? true,
        ];
    }

    /**
     * Get the tenant's preferred locale
     */
    public function getLocale(): string
    {
        return $this->locale ?? 'en';
    }
}

