<?php

namespace App\Models;

use App\Observers\TenantObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

#[ObservedBy([TenantObserver::class])]
class Tenant extends BaseTenant
{
    /**
     * Tenant configuration rows live on the landlord database (never on per-tenant DBs).
     */
    protected $connection = 'landlord';

    public const PLAN_BASIC = 'basic';

    public const PLAN_PLUS = 'plus';

    public const PLAN_PRO = 'pro';

    /** Promotional: Premium feature set; optional `promo_max_listings` (null = unlimited) and `promo_price` (null = ₱0 in catalog). */
    public const PLAN_PROMO = 'promo';

    public const ONBOARDING_AWAITING_PAYMENT = 'awaiting_payment';

    public const ONBOARDING_PENDING_APPROVAL = 'pending_approval';

    public const ONBOARDING_APPROVED = 'approved';

    public const ONBOARDING_REJECTED = 'rejected';

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
        'promo_max_listings',
        'promo_price',
        'subscription_status',
        'trial_ends_at',
        'current_period_starts_at',
        'current_period_ends_at',
        'metadata',
        'app_title',
        'primary_color',
        'accent_color',
        'logo_path',
        'gcash_qr_path',
        'locale',
        'feature_bookings',
        'feature_messaging',
        'feature_reviews',
        'feature_payments',
        'database_provisioned',
        'database_provisioned_at',
        'provisioning_error',
        'onboarding_status',
        'payment_reference',
        'payment_submitted_at',
        'onboarding_approved_at',
        'onboarding_approved_by',
        'bandwidth_usage_bytes',
        'bandwidth_quota_bytes',
        'bandwidth_last_recorded_at',
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
            'database_provisioned' => 'boolean',
            'database_provisioned_at' => 'datetime',
            'payment_submitted_at' => 'datetime',
            'onboarding_approved_at' => 'datetime',
            'metadata' => 'array',
            'feature_bookings' => 'boolean',
            'feature_messaging' => 'boolean',
            'feature_reviews' => 'boolean',
            'feature_payments' => 'boolean',
            'bandwidth_usage_bytes' => 'integer',
            'bandwidth_quota_bytes' => 'integer',
            'bandwidth_last_recorded_at' => 'datetime',
            'promo_max_listings' => 'integer',
            'promo_price' => 'decimal:2',
        ];
    }

    /**
     * Share of quota used (0–100), or null when no quota is set.
     */
    public function bandwidthUsagePercent(): ?float
    {
        $quota = (int) ($this->bandwidth_quota_bytes ?? 0);
        if ($quota < 1) {
            return null;
        }

        $used = (int) ($this->bandwidth_usage_bytes ?? 0);

        return round(min(100, ($used / $quota) * 100), 1);
    }

    public function publicUrl(): string
    {
        $host = env('TENANCY_BASE_HOST', parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost');
        $port = ':'.env('CENTRAL_PORT', 8000);

        if ($this->domain) {
            return 'http://'.$this->domain.$port;
        }

        return 'http://'.$host.$port;
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function onboardingApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'onboarding_approved_by');
    }

    public function isRegistrationFullyApproved(): bool
    {
        return (string) ($this->onboarding_status ?? self::ONBOARDING_APPROVED) === self::ONBOARDING_APPROVED
            && (bool) $this->database_provisioned;
    }

    public function mockSubscriptionAmount(): float
    {
        $details = self::getPlanDetails();
        $plan = (string) ($this->plan ?? self::PLAN_BASIC);

        if ($plan === self::PLAN_PROMO && $this->promo_price !== null) {
            return (float) $this->promo_price;
        }

        return (float) ($details[$plan]['price'] ?? $details[self::PLAN_BASIC]['price'] ?? 0);
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

    public function updateTickets(): HasMany
    {
        return $this->hasMany(UpdateTicket::class, 'tenant_id');
    }

    public function hasActiveSubscription(): bool
    {
        return in_array($this->subscription_status, ['trialing', 'active'], true);
    }

    public function maxListings(): ?int
    {
        return match ($this->normalizedPlan()) {
            self::PLAN_BASIC => 3,
            self::PLAN_PLUS => 10,
            self::PLAN_PRO => null,
            self::PLAN_PROMO => $this->promo_max_listings,
            default => 3,
        };
    }

    /**
     * Check if tenant has access to a specific feature based on subscription plan
     */
    public function hasFeature(string $feature): bool
    {
        if (! $this->hasActiveSubscription()) {
            return false;
        }

        return match ($this->normalizedPlan()) {
            self::PLAN_BASIC => in_array($feature, [
                'bookings',
                'basic_reporting',
            ]),
            self::PLAN_PLUS => in_array($feature, [
                'bookings',
                'messaging',
                'advanced_reporting',
                'analytics_dashboard',
            ]),
            self::PLAN_PRO, self::PLAN_PROMO => in_array($feature, [
                'bookings',
                'messaging',
                'reviews',
                'advanced_reporting',
                'analytics_dashboard',
                'priority_support',
                'featured_listings',
            ]),
            default => false,
        };
    }

    /**
     * Get an array of all available features for this plan
     */
    public function getAvailableFeatures(): array
    {
        return match ($this->normalizedPlan()) {
            self::PLAN_BASIC => [
                'bookings' => true,
                'messaging' => false,
                'reviews' => false,
                'basic_reporting' => true,
                'advanced_reporting' => false,
                'analytics_dashboard' => false,
                'priority_support' => false,
                'featured_listings' => false,
            ],
            self::PLAN_PLUS => [
                'bookings' => true,
                'messaging' => true,
                'reviews' => false,
                'basic_reporting' => true,
                'advanced_reporting' => true,
                'analytics_dashboard' => true,
                'priority_support' => false,
                'featured_listings' => false,
            ],
            self::PLAN_PRO, self::PLAN_PROMO => [
                'bookings' => true,
                'messaging' => true,
                'reviews' => true,
                'basic_reporting' => true,
                'advanced_reporting' => true,
                'analytics_dashboard' => true,
                'priority_support' => true,
                'featured_listings' => true,
            ],
            default => [],
        };
    }

    private function normalizedPlan(): string
    {
        $plan = (string) $this->plan;

        if (str_starts_with($plan, 'custom:')) {
            // Custom plans default to premium capabilities unless explicitly modeled later.
            return self::PLAN_PRO;
        }

        if ($plan === self::PLAN_PROMO) {
            return self::PLAN_PROMO;
        }

        return $plan;
    }

    /**
     * Get plan details with features and pricing
     */
    public static function getPlanDetails(): array
    {
        return [
            self::PLAN_BASIC => [
                'name' => 'Basic Plan',
                'price' => 299,
                'currency' => '₱',
                'max_listings' => 3,
                'features' => [
                    '3 property listings',
                    'Basic reporting',
                    'Booking management',
                ],
            ],
            self::PLAN_PLUS => [
                'name' => 'Standard Plan',
                'price' => 499,
                'currency' => '₱',
                'max_listings' => 10,
                'features' => [
                    'Up to 10 listings',
                    'Advanced reporting',
                    'Analytics dashboard',
                ],
            ],
            self::PLAN_PRO => [
                'name' => 'Premium Plan',
                'price' => 799,
                'currency' => '₱',
                'max_listings' => null, // unlimited
                'features' => [
                    'Unlimited listings',
                    'Priority support',
                    'Featured listing promotion',
                    'Advanced analytics',
                ],
            ],
            self::PLAN_PROMO => [
                'name' => 'Promo (custom)',
                'price' => 0,
                'currency' => '₱',
                'max_listings' => null,
                'features' => [
                    'Promotional / partner pricing',
                    'Premium feature set',
                    'Custom listing cap and monthly price (admin)',
                ],
            ],
        ];
    }

    /**
     * Human-readable plan label for admin UI and emails.
     */
    public static function planLabel(string $plan): string
    {
        return match ($plan) {
            self::PLAN_BASIC => 'Basic',
            self::PLAN_PLUS => 'Standard',
            self::PLAN_PRO => 'Premium',
            self::PLAN_PROMO => 'Promo (custom)',
            default => ucfirst($plan),
        };
    }

    public function canCreateAccommodation(int $currentCount): bool
    {
        if (! $this->hasActiveSubscription()) {
            return false;
        }

        if (! $this->hasFeature('bookings')) {
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
            'hero_title' => $this->name.' Stays',
            'hero_subtitle' => 'Book trusted accommodations managed by '.$ownerName.'.',
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
        return $this->logo_path ? asset('storage/'.$this->logo_path) : null;
    }

    public function getGcashQrUrl(): ?string
    {
        return $this->gcash_qr_path ? asset('storage/'.$this->gcash_qr_path) : null;
    }

    /**
     * Check if a specific feature is enabled
     */
    public function isFeatureEnabled(string $feature): bool
    {
        $featureKey = 'feature_'.$feature;
        if (! property_exists($this, $featureKey)) {
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
