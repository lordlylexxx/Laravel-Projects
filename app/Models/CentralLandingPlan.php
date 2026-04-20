<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentralLandingPlan extends Model
{
    public const ALLOWED_PLAN_KEYS = [
        Tenant::PLAN_BASIC,
        Tenant::PLAN_PLUS,
        Tenant::PLAN_PRO,
        Tenant::PLAN_PROMO,
    ];

    protected $fillable = [
        'tenant_plan_key',
        'title',
        'price_amount',
        'features',
        'aggregate_catalog_features',
        'button_label',
        'is_visible',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_amount' => 'decimal:2',
            'features' => 'array',
            'is_visible' => 'boolean',
            'is_featured' => 'boolean',
            'aggregate_catalog_features' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * All feature lines from Basic, Standard, Premium, then Promo (for marketing “full checklist” cards).
     *
     * @return array<int, string>
     */
    public static function mergedTierCatalogFeatureLines(): array
    {
        $details = Tenant::getPlanDetails();
        $lines = [];
        foreach ([Tenant::PLAN_BASIC, Tenant::PLAN_PLUS, Tenant::PLAN_PRO, Tenant::PLAN_PROMO] as $key) {
            foreach (($details[$key]['features'] ?? []) as $line) {
                $lines[] = (string) $line;
            }
        }

        return $lines;
    }

    /**
     * @return array{name: string, price: float|int, currency: string, features: array<int, string>}|null
     */
    public function catalogSlice(): ?array
    {
        $key = (string) $this->tenant_plan_key;
        if (! in_array($key, self::ALLOWED_PLAN_KEYS, true)) {
            return null;
        }

        $details = Tenant::getPlanDetails();

        return $details[$key] ?? null;
    }

    public function effectiveTitle(): string
    {
        $title = $this->title;
        if (is_string($title) && $title !== '') {
            return $title;
        }

        return (string) ($this->catalogSlice()['name'] ?? 'Plan');
    }

    public function effectivePrice(): float
    {
        if ($this->price_amount !== null) {
            return (float) $this->price_amount;
        }

        return (float) ($this->catalogSlice()['price'] ?? 0);
    }

    public function effectiveCurrency(): string
    {
        return (string) ($this->catalogSlice()['currency'] ?? '₱');
    }

    /**
     * @return array<int, string>
     */
    public function effectiveFeatures(): array
    {
        $features = $this->features;
        if (is_array($features) && $features !== []) {
            return array_values(array_map(static fn ($line) => (string) $line, $features));
        }

        if ($this->aggregate_catalog_features) {
            return self::mergedTierCatalogFeatureLines();
        }

        $slice = $this->catalogSlice();
        if ($slice === null) {
            return [];
        }

        /** @var array<int, string> $fromCatalog */
        $fromCatalog = $slice['features'] ?? [];

        return array_values($fromCatalog);
    }

    public function effectiveButtonLabel(): string
    {
        return 'Register';
    }

    /**
     * How catalog features are chosen for this row (admin UI + overrides column).
     */
    public function featureSelectionMode(): string
    {
        $features = $this->features;
        if (is_array($features) && $features !== []) {
            return 'custom_pick';
        }

        if ($this->aggregate_catalog_features) {
            return 'full_catalog';
        }

        return 'tier_catalog';
    }

    public function registerUrl(): string
    {
        return route('register', [
            'role' => 'owner',
            'plan' => (string) $this->tenant_plan_key,
        ]);
    }

    public function usesCatalogForFeatures(): bool
    {
        $features = $this->features;

        return $features === null || $features === [];
    }
}
