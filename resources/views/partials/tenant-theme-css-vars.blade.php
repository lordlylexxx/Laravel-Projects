@php
    $themeTenant = $themeTenant ?? (\App\Models\Tenant::checkCurrent() ? \App\Models\Tenant::current() : null);

    if ($themeTenant instanceof \App\Models\Tenant) {
        $primary = (string) $themeTenant->getPrimaryColor();
        $accent = (string) $themeTenant->getAccentColor();
        if (! preg_match('/^#[0-9A-Fa-f]{6}$/i', $primary)) {
            $primary = '#2E7D32';
        }
        if (! preg_match('/^#[0-9A-Fa-f]{6}$/i', $accent)) {
            $accent = '#43A047';
        }
    } else {
        $primary = '#2E7D32';
        $accent = '#43A047';
    }
@endphp
            --green-dark: color-mix(in srgb, {{ $primary }} 72%, #000000);
            --green-primary: {{ $primary }};
            --green-medium: color-mix(in srgb, {{ $primary }} 52%, {{ $accent }});
            --green-light: color-mix(in srgb, {{ $primary }} 42%, #ffffff);
            --green-pale: color-mix(in srgb, {{ $accent }} 38%, #ffffff);
            --green-soft: color-mix(in srgb, {{ $primary }} 20%, #ffffff);
            --green-white: color-mix(in srgb, {{ $primary }} 10%, #ffffff);
            --cream: color-mix(in srgb, {{ $primary }} 6%, #f6faf4);
            --white: #FFFFFF;
            --primary: {{ $primary }};
            --accent: {{ $accent }};
