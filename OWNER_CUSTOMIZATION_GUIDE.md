# Owner Registration Customization - Implementation Guide

## Overview
Owners can now customize their app preferences during registration through a 3-step wizard:
1. **Step 1**: Account details (name, email, password, role selection)
2. **Step 2**: Customization (app name, colors, logo, language, feature selection)
3. **Step 3**: Review and confirmation

## Features Available for Customization

### 1. Business Name/App Title
- Custom app name that displays across their tenant instance
- Falls back to auto-generated name if not provided
- Example: "Sarah's Space Stays" instead of "Sarah M-123-abc123's Space"

### 2. Theme Colors
- **Primary Color**: Main brand color used in buttons, links, highlights
- **Accent Color**: Secondary color for complementary elements
- Interactive color pickers with hex code input
- Defaults: Primary #2E7D32, Accent #43A047

### 3. Business Logo
- Upload custom business/tenant logo (JPEG, PNG, GIF, WebP)
- Max 5MB file size
- Stored in `storage/app/public/tenant-logos/`
- Accessible via `$tenant->getLogoUrl()`

### 4. Language/Locale
- Choose preferred interface language
- Options: English, Español, Français, Deutsch
- Stored and retrievable via `$tenant->getLocale()`

### 5. Feature Toggles
Owners can enable/disable features for their app:
- ✅ **Booking System** (default: enabled)
  - Allow guests to book accommodations
- ✅ **Messaging** (default: enabled)
  - Enable direct communication with guests
- ✅ **Reviews** (default: enabled)
  - Allow guests to leave reviews
- ✅ **Payments** (default: enabled)
  - Accept online payments for bookings

## Using Customization in Views

### Display Custom App Title
```blade
<!-- In navbar or header -->
<h1 class="app-title">{{ $tenant->getAppTitle() }}</h1>

<!-- Output: "Sarah's Space Stays" (if set during registration) -->
<!-- Fallback: "Sarah M-123-abc123's Space" (auto-generated) -->
```

### Apply Custom Theme Colors
```blade
<!-- In CSS -->
<style>
    :root {
        --primary-color: {{ $currentTenant->getPrimaryColor() }};
        --accent-color: {{ $currentTenant->getAccentColor() }};
    }
</style>

<!-- In HTML -->
<button style="background: {{ $currentTenant->getPrimaryColor() }};">
    Click Me
</button>
```

### Display Logo
```blade
@if($currentTenant->getLogoUrl())
    <img src="{{ $currentTenant->getLogoUrl() }}" 
         alt="{{ $currentTenant->getAppTitle() }}"
         class="tenant-logo">
@else
    <img src="/SYSTEMLOGO.png" alt="Default Logo">
@endif
```

### Check Feature Availability
```blade
<!-- Show booking section only if enabled -->
@if($currentTenant->isFeatureEnabled('bookings'))
    <div class="bookings-section">
        <!-- Booking UI -->
    </div>
@endif

<!-- Show messaging only if enabled -->
@if($currentTenant->isFeatureEnabled('messaging'))
    <div class="messaging-section">
        <!-- Messaging UI -->
    </div>
@endif
```

### Get All Features Status
```blade
@php
    $features = $currentTenant->getEnabledFeatures();
    // Returns: [
    //     'bookings' => true,
    //     'messaging' => true,
    //     'reviews' => true,
    //     'payments' => true,
    // ]
@endphp

@foreach($features as $featureName => $isEnabled)
    <p>{{ ucfirst($featureName) }}: @if($isEnabled) ✓ Enabled @else ✗ Disabled @endif</p>
@endforeach
```

## Using Customization in Controllers

### Check Feature in Controller
```php
class BookingController extends Controller
{
    public function store(Request $request)
    {
        $tenant = Tenant::current();
        
        // Check if booking feature is enabled
        if (!$tenant->isFeatureEnabled('bookings')) {
            return response()->json([
                'message' => 'Booking feature is not enabled for this property'
            ], 403);
        }
        
        // Create booking...
    }
}
```

### Use Custom Settings
```php
class DashboardController extends Controller
{
    public function index()
    {
        $tenant = Tenant::current();
        
        return view('dashboard', [
            'appTitle' => $tenant->getAppTitle(),
            'themeColor' => $tenant->getPrimaryColor(),
            'enabledFeatures' => $tenant->getEnabledFeatures(),
        ]);
    }
}
```

## Database Schema

```sql
-- Added columns to tenants table
ALTER TABLE tenants ADD (
    app_title VARCHAR(255) NULL COMMENT 'Custom app/business name',
    primary_color VARCHAR(7) DEFAULT '#2E7D32' COMMENT 'Primary theme color (hex)',
    accent_color VARCHAR(7) DEFAULT '#43A047' COMMENT 'Accent theme color (hex)',
    logo_path VARCHAR(255) NULL COMMENT 'Path to uploaded logo',
    locale VARCHAR(5) DEFAULT 'en' COMMENT 'Language preference (en, es, fr, de)',
    feature_bookings BOOLEAN DEFAULT TRUE COMMENT 'Enable/disable booking system',
    feature_messaging BOOLEAN DEFAULT TRUE COMMENT 'Enable/disable messaging',
    feature_reviews BOOLEAN DEFAULT TRUE COMMENT 'Enable/disable reviews',
    feature_payments BOOLEAN DEFAULT TRUE COMMENT 'Enable/disable online payments'
);
```

## Registration Flow

### For Client Registrations
1. User selects "Find Accommodation"
2. Fills: name, email, phone, password
3. Account created → Redirected to client dashboard
4. *(No customization step - clients don't get their own apps)*

### For Owner Registrations
1. User selects "List My Property"
2. **Step 1**: Account details (name, email, phone, password)
3. **Step 2**: Customization
   - App Title: "Sarah's Space Stays"
   - Primary Color: #FF6B6B
   - Accent Color: #FFA500
   - Logo: Upload image
   - Language: English
   - Features: All enabled (default)
4. **Step 3**: Review all settings
5. Account created → Tenant created with customization → Redirected to owner dashboard

## File Locations

- **Registration Wizard View**: `resources/views/auth/register-wizard.blade.php`
- **Migration**: `database/migrations/2026_03_21_000001_add_customization_to_tenants_table.php`
- **Logo Storage**: `storage/app/public/tenant-logos/`
- **Helper Methods**: `app/Models/Tenant.php` (lines 143-200)

## Implementation Checklist

- ✅ Database migration applied
- ✅ Tenant model updated with customization fields
- ✅ User model updated to apply customization
- ✅ Registration controller updated to validate and save customization
- ✅ Registration wizard view created
- ✅ Helper methods added to Tenant model
- ⏳ **Next**: Update existing views/controllers to use customization (optional enhancements)

### Optional Enhancements

Consider implementing in views/controllers:
1. Owner settings page to edit customization after registration
2. Navbar component that uses `getAppTitle()` and `getPrimaryColor()`
3. Dashboard header showing custom colors and logo
4. Feature-gated UI (hide features that owner disabled)
5. Language/locale switching in app settings

## Example: Updated Navbar with Customization

```blade
<!-- In resources/views/owner/partials/top-navbar.blade.php -->
@php
    $currentTenant = \App\Models\Tenant::current();
    $appTitle = $currentTenant?->getAppTitle() ?? 'ImpaStay';
    $primaryColor = $currentTenant?->getPrimaryColor() ?? '#2E7D32';
    $logoUrl = $currentTenant?->getLogoUrl();
@endphp

<nav class="navbar" style="background-color: {{ $primaryColor }};">
    <div class="nav-logo">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt={{ $appTitle }} class="tenant-logo">
        @else
            <span>{{ $appTitle }}</span>
        @endif
    </div>
    <!-- Rest of navbar -->
</nav>

<style>
    .navbar {
        background-color: {{ $primaryColor }} !important;
    }
    
    .nav-button {
        color: {{ $primaryColor }};
        border-color: {{ $primaryColor }};
    }
    
    .nav-button:hover {
        background-color: {{ $primaryColor }};
    }
</style>
```

## Testing the Registration Flow

1. Navigate to registration page: `http://127.0.0.1:8000/register`
2. Select "List My Property" (owner role)
3. Fill in account details
4. Click "Next →"
5. Customize app preferences:
   - Enter app title: "My Awesome Space"
   - Select colors
   - Upload logo
   - Select language and features
6. Click "Next →"
7. Review summary
8. Click "Create Account"
9. Verify redirect to owner dashboard
10. Check database for filled customization fields

## Troubleshooting

### Logo not uploading
- Check file size < 5MB
- Verify file is image format (JPEG, PNG, GIF, WebP)
- Ensure `storage/app/public/` is writable
- Run: `php artisan storage:link` if symbolic link missing

### Colors not applying
- Verify hex format is correct (e.g., #FF6B6B)
- Clear view cache: `php artisan view:clear`
- Hard refresh browser (Ctrl+Shift+R or Cmd+Shift+R)

### Customization fields NULL in database
- Verify migration ran: `php artisan migrate --list`
- Check database columns exist: `SHOW COLUMNS FROM tenants;`
- Verify form field names match column names

---

**Last Updated**: March 21, 2026
**Status**: ✅ Ready for Testing
