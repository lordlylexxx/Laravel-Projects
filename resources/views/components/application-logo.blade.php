@php
    $currentTenant = \App\Models\Tenant::checkCurrent() ? \App\Models\Tenant::current() : null;
    $logoUrl = $currentTenant?->getLogoUrl() ?: asset('SYSTEMLOGO.png');
@endphp

<img src="{{ $logoUrl }}" alt="{{ $currentTenant?->name ?? config('app.name', 'ImpaStay') }}" {{ $attributes->merge(['class' => 'object-contain']) }} onerror="this.onerror=null;this.src='{{ asset('SYSTEMLOGO.png') }}';">
