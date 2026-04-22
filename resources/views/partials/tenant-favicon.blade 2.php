@php
    $tenantFaviconTenant = $tenant ?? \App\Models\Tenant::current();
    $tenantFaviconHref = ($tenantFaviconTenant && $tenantFaviconTenant->getLogoUrl())
        ? $tenantFaviconTenant->getLogoUrl()
        : asset('SYSTEMLOGO.png');
@endphp
<link rel="icon" href="{{ $tenantFaviconHref }}">
