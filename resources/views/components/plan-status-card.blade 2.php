@php
    use App\Helpers\FeatureHelper;
    $currentPlan = FeatureHelper::currentPlan();
    $availableFeatures = FeatureHelper::getAvailable();
    $remainingListings = FeatureHelper::remainingListings();
    $maxListings = \Spatie\Multitenancy\Models\Tenant::current()?->maxListings();
@endphp

<div class="plan-status-card">
    <div class="plan-header">
        <h3 class="plan-title">
            <i class="fas fa-crown"></i>
            {{ $currentPlan['name'] ?? 'Basic Plan' }}
        </h3>
        <div class="plan-limits">
            @if($maxListings === null)
                <span class="badge badge-unlimited">Unlimited Listings</span>
            @else
                <span class="badge badge-limited">
                    {{ $remainingListings }} / {{ $maxListings }} Listings Available
                </span>
            @endif
        </div>
    </div>

    <div class="plan-features">
        <div class="features-grid">
            @foreach($availableFeatures as $feature => $enabled)
                <div class="feature-item {{ $enabled ? 'available' : 'unavailable' }}">
                    <div class="feature-icon">
                        @if($enabled)
                            <i class="fas fa-check-circle" style="color: #4caf50;"></i>
                        @else
                            <i class="fas fa-lock" style="color: #ccc;"></i>
                        @endif
                    </div>
                    <div class="feature-name">
                        {{ str_replace('_', ' ', ucwords(str_replace('-', ' ', $feature))) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="plan-actions">
        <a href="/" class="btn btn-sm btn-outline">
            <i class="fas fa-list"></i> View All Plans
        </a>
        @if(!auth()->user()->isAdmin())
            <a href="mailto:admin@company.com?subject=Plan%20Upgrade" class="btn btn-sm btn-primary">
                <i class="fas fa-arrow-up"></i> Upgrade Plan
            </a>
        @endif
    </div>
</div>

<style>
.plan-status-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    border: 2px solid #e8f5e9;
    margin-bottom: 20px;
}

.plan-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.plan-title {
    color: #1b5e20;
    font-size: 1.2rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.plan-limits {
    display: flex;
    gap: 10px;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.badge-unlimited {
    background: #c8e6c9;
    color: #1b5e20;
}

.badge-limited {
    background: #fff3cd;
    color: #856404;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.feature-item {
    padding: 12px;
    border-radius: 6px;
    text-align: center;
    border: 1px solid #e0e0e0;
}

.feature-item.available {
    background: #f1f8f6;
    border-color: #4caf50;
}

.feature-item.unavailable {
    background: #fafafa;
    border-color: #e0e0e0;
    opacity: 0.6;
}

.feature-icon {
    margin-bottom: 8px;
}

.feature-name {
    font-size: 0.85rem;
    color: #424242;
    font-weight: 500;
}

.plan-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn {
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: none;
    cursor: pointer;
}

.btn-outline {
    background: transparent;
    color: #2e7d32;
    border: 2px solid #2e7d32;
}

.btn-outline:hover {
    background: #2e7d32;
    color: white;
}

.btn-primary {
    background: #2e7d32;
    color: white;
}

.btn-primary:hover {
    background: #1b5e20;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.8rem;
}
</style>
