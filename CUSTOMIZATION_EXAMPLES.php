<?php
/**
 * Example Usage of Owner Customization in Controllers and Views
 * 
 * This file demonstrates how to implement owner customization throughout the app.
 */

// ============================================================================
// EXAMPLE 1: Using Customization in Owner Dashboard Controller
// ============================================================================

namespace App\Http\Controllers\Owner;

use App\Models\Tenant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $currentTenant = Tenant::current();
        $user = auth()->user();
        
        // Get customization settings
        $appTitle = $currentTenant->getAppTitle();
        $primaryColor = $currentTenant->getPrimaryColor();
        $accentColor = $currentTenant->getAccentColor();
        $logoUrl = $currentTenant->getLogoUrl();
        $locale = $currentTenant->getLocale();
        $features = $currentTenant->getEnabledFeatures();
        
        return view('owner.dashboard', [
            'appTitle' => $appTitle,
            'primaryColor' => $primaryColor,
            'accentColor' => $accentColor,
            'logoUrl' => $logoUrl,
            'enabledFeatures' => $features,
            // ... other data
        ]);
    }
}

// ============================================================================
// EXAMPLE 2: Feature-Gated UI in Blade Templates
// ============================================================================

/**
 * Blade template using feature toggles
 * File: resources/views/owner/dashboard.blade.php
 */

?>

<div class="dashboard">
    <!-- Use custom app title in header -->
    <header class="dashboard-header" style="background-color: {{ $primaryColor }};">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $appTitle }}" class="tenant-logo">
        @else
            <h1>{{ $appTitle }}</h1>
        @endif
    </header>

    <!-- Dashboard content -->
    <div class="dashboard-content" style="--primary-color: {{ $primaryColor }}; --accent-color: {{ $accentColor }};">
        
        <!-- Conditional: Show bookings section only if enabled -->
        @if($enabledFeatures['bookings'])
            <div class="card bookings-card">
                <h3 style="color: {{ $primaryColor }};">Recent Bookings</h3>
                <!-- Bookings content -->
            </div>
        @else
            <div class="alert" style="background-color: {{ $accentColor }}20;">
                <p>Booking feature is disabled. Enable it in settings to show bookings.</p>
            </div>
        @endif

        <!-- Conditional: Show messaging section only if enabled -->
        @if($enabledFeatures['messaging'])
            <div class="card messaging-card">
                <h3 style="color: {{ $primaryColor }};">Messages</h3>
                <!-- Messaging content -->
            </div>
        @endif

        <!-- Conditional: Show reviews section only if enabled -->
        @if($enabledFeatures['reviews'])
            <div class="card reviews-card">
                <h3 style="color: {{ $primaryColor }};">Guest Reviews</h3>
                <!-- Reviews content -->
            </div>
        @endif

        <!-- Conditional: Show payment stats only if enabled -->
        @if($enabledFeatures['payments'])
            <div class="card payments-card">
                <h3 style="color: {{ $primaryColor }};">Payment Information</h3>
                <!-- Payments content -->
            </div>
        @endif
    </div>

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --accent-color: {{ $accentColor }};
        }

        .dashboard-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            border-radius: 8px;
        }

        .dashboard-header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .card h3 {
            color: var(--primary-color);
            border-bottom: 3px solid var(--accent-color);
            padding-bottom: 10px;
        }

        .card button {
            background-color: var(--primary-color);
        }

        .card button:hover {
            opacity: 0.9;
        }
    </style>
</div>

<?php
// ============================================================================
// EXAMPLE 3: Helper Blade Component
// ============================================================================

/**
 * Create a reusable Blade component for displaying features
 * File: resources/views/components/feature-section.blade.php
 */
?>

@props([
    'title',
    'feature',
    'color' => '#2E7D32',
    'icon' => '📦'
])

@php
    $currentTenant = \App\Models\Tenant::current();
    $isEnabled = $currentTenant->isFeatureEnabled($feature);
@endphp

@if($isEnabled)
    <div class="feature-section" style="border-left: 4px solid {{ $color }};">
        <h3 style="color: {{ $color }};">{{ $icon }} {{ $title }}</h3>
        {{ $slot }}
    </div>
@else
    <div class="feature-disabled" style="background-color: {{ $color }}20; padding: 15px; border-radius: 8px;">
        <p><strong>{{ $title }}</strong> is disabled for your account.</p>
        <small>Contact support to enable this feature.</small>
    </div>
@endif

<style>
    .feature-section {
        padding: 15px;
        background: white;
        border-radius: 8px;
        margin: 10px 0;
    }

    .feature-disabled {
        color: #666;
    }
</style>

<?php
// ============================================================================
// EXAMPLE 4: Using in Accommodations Controller
// ============================================================================

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Accommodation;

class AccommodationController extends Controller
{
    public function index()
    {
        $tenant = Tenant::current();
        
        // Check if owner has booking feature enabled
        if (!$tenant->isFeatureEnabled('bookings')) {
            return redirect()->back()
                ->with('warning', 'Booking feature is disabled. Enable it in your settings.');
        }

        $accommodations = Accommodation::where('tenant_id', $tenant->id)
            ->paginate(15);

        return view('accommodations.index', [
            'accommodations' => $accommodations,
            'primaryColor' => $tenant->getPrimaryColor(),
            'enabledFeatures' => $tenant->getEnabledFeatures(),
        ]);
    }

    public function store(Request $request)
    {
        $tenant = Tenant::current();

        // Validate booking feature is enabled
        abort_unless(
            $tenant->isFeatureEnabled('bookings'),
            403,
            'Bookings are disabled for this property.'
        );

        // Create accommodation
        // ...
    }
}

<?php
// ============================================================================
// EXAMPLE 5: API Response with Feature Data
// ============================================================================

/**
 * Return feature status in API responses
 */

namespace App\Http\Controllers\Api;

class TenantController extends Controller
{
    public function getSettings()
    {
        $tenant = Tenant::current();

        return response()->json([
            'app_title' => $tenant->getAppTitle(),
            'primary_color' => $tenant->getPrimaryColor(),
            'accent_color' => $tenant->getAccentColor(),
            'logo_url' => $tenant->getLogoUrl(),
            'locale' => $tenant->getLocale(),
            'features' => $tenant->getEnabledFeatures(),
            'theme' => [
                'primaryColor' => $tenant->getPrimaryColor(),
                'accentColor' => $tenant->getAccentColor(),
            ],
        ]);
    }
}

<?php
// ============================================================================
// EXAMPLE 6: Middleware to Check Feature Availability
// ============================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFeatureEnabled
{
    /**
     * Handle an incoming request.
     *
     * Usage: Route::post('/bookings', [BookingController::class, 'store'])
     *     ->middleware('check.feature:bookings');
     */
    public function handle(Request $request, Closure $next, string $feature)
    {
        $tenant = Tenant::current();

        if (!$tenant->isFeatureEnabled($feature)) {
            return response()->json([
                'message' => "The {$feature} feature is not available.",
                'feature' => $feature,
                'enabled' => false,
            ], 403);
        }

        return $next($request);
    }
}

// In routes/web.php or routes/api.php:
// Route::post('/bookings', BookingController@store)->middleware('check.feature:bookings');

<?php
// ============================================================================
// EXAMPLE 7: Settings/Profile Update Page
// ============================================================================

/**
 * Allow owners to update customization after registration
 * File: resources/views/owner/settings.blade.php
 */
?>

<form method="POST" action="{{ route('owner.settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- App Title -->
    <div class="form-group">
        <label>App Title</label>
        <input type="text" name="app_title" value="{{ $tenant->app_title }}" 
               placeholder="Your app name">
    </div>

    <!-- Color Pickers -->
    <div class="form-group">
        <label>Primary Color</label>
        <input type="color" name="primary_color" value="{{ $tenant->getPrimaryColor() }}">
    </div>

    <div class="form-group">
        <label>Accent Color</label>
        <input type="color" name="accent_color" value="{{ $tenant->getAccentColor() }}">
    </div>

    <!-- Logo Upload -->
    <div class="form-group">
        <label>Logo</label>
        @if($tenant->getLogoUrl())
            <div>
                <img src="{{ $tenant->getLogoUrl() }}" alt="Current Logo" style="max-width: 200px;">
                <label><input type="checkbox" name="remove_logo"> Remove logo</label>
            </div>
        @endif
        <input type="file" name="logo_path" accept="image/*">
    </div>

    <!-- Language Selection -->
    <div class="form-group">
        <label>Preferred Language</label>
        <select name="locale">
            <option value="en" {{ $tenant->locale === 'en' ? 'selected' : '' }}>English</option>
            <option value="es" {{ $tenant->locale === 'es' ? 'selected' : '' }}>Español</option>
            <option value="fr" {{ $tenant->locale === 'fr' ? 'selected' : '' }}>Français</option>
            <option value="de" {{ $tenant->locale === 'de' ? 'selected' : '' }}>Deutsch</option>
        </select>
    </div>

    <!-- Feature Toggles -->
    <div class="form-group">
        <label>Enabled Features</label>
        
        <label class="checkbox">
            <input type="checkbox" name="feature_bookings" {{ $tenant->feature_bookings ? 'checked' : '' }}>
            Booking System
        </label>
        
        <label class="checkbox">
            <input type="checkbox" name="feature_messaging" {{ $tenant->feature_messaging ? 'checked' : '' }}>
            Messaging
        </label>
        
        <label class="checkbox">
            <input type="checkbox" name="feature_reviews" {{ $tenant->feature_reviews ? 'checked' : '' }}>
            Reviews
        </label>
        
        <label class="checkbox">
            <input type="checkbox" name="feature_payments" {{ $tenant->feature_payments ? 'checked' : '' }}>
            Online Payments
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>

<?php
/**
 * Controller to handle settings update
 * 
 * namespace App\Http\Controllers\Owner;
 * 
 * class SettingsController extends Controller
 * {
 *     public function update(Request $request)
 *     {
 *         $tenant = Tenant::current();
 *         
 *         $validated = $request->validate([
 *             'app_title' => 'nullable|string|max:255',
 *             'primary_color' => 'nullable|regex:/^#[0-9A-F]{6}$/i',
 *             'accent_color' => 'nullable|regex:/^#[0-9A-F]{6}$/i',
 *             'locale' => 'nullable|in:en,es,fr,de',
 *             'logo_path' => 'nullable|image|max:5120',
 *             'feature_bookings' => 'nullable|boolean',
 *             'feature_messaging' => 'nullable|boolean',
 *             'feature_reviews' => 'nullable|boolean',
 *             'feature_payments' => 'nullable|boolean',
 *         ]);
 *         
 *         // Handle file upload
 *         if ($request->hasFile('logo_path')) {
 *             $validated['logo_path'] = $request->file('logo_path')
 *                 ->store('tenant-logos', 'public');
 *         }
 *         
 *         $tenant->update($validated);
 *         
 *         return redirect()->back()->with('success', 'Settings updated!');
 *     }
 * }
 */
