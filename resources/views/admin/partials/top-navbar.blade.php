@php
    $current = $active ?? '';
    $isTenantContext = \App\Models\Tenant::checkCurrent();
    $dashboardHref = $isTenantContext ? '/owner/dashboard' : '/admin/dashboard';
    $unitsHref = $isTenantContext ? '/owner/accommodations' : '/admin/tenants';
    $bookingsHref = '/owner/bookings';
    $updatesHref = $isTenantContext ? '/owner/system-updates' : '/admin/system-updates';
    $landingHref = $isTenantContext ? '/' : '/';
@endphp

<nav class="navbar">
    <a href="{{ $landingHref }}" class="nav-logo">
        <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo">
        <span>ImpaStay</span>
    </a>

    <ul class="nav-links">
        <li><a href="{{ $dashboardHref }}" class="{{ $current === 'dashboard' ? 'active' : '' }}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="{{ $unitsHref }}" class="{{ $current === 'tenants' ? 'active' : '' }}"><i class="fas fa-building-user"></i> My Units</a></li>
        @if($isTenantContext)
            <li><a href="{{ $bookingsHref }}" class="{{ $current === 'bookings' ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> Bookings</a></li>
        @endif
        <li><a href="{{ $updatesHref }}" class="{{ $current === 'updates' ? 'active' : '' }}"><i class="fas fa-cloud-download-alt"></i> Updates</a></li>
        <li><a href="/messages" class="{{ $current === 'messages' ? 'active' : '' }}"><i class="fas fa-envelope"></i> Messages</a></li>
        <li><a href="/profile" class="{{ $current === 'settings' ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>

    <div class="nav-actions">
        @if(($tenantUpdate['has_update'] ?? false) && !empty($tenantUpdate['download_url']))
            <a href="{{ $tenantUpdate['download_url'] }}" class="nav-btn" title="Update to v{{ $tenantUpdate['latest_version'] ?? '' }}">
                <i class="fas fa-download"></i>
                Update v{{ $tenantUpdate['latest_version'] }}
            </a>
        @elseif(($tenantUpdate['unavailable'] ?? false) === true)
            <span class="nav-btn" title="{{ $tenantUpdate['message'] ?? 'Update server is unavailable.' }}" style="opacity: .8; cursor: default;">
                <i class="fas fa-cloud-slash"></i>
                Updates Offline
            </span>
        @endif

        <div class="user-display">
            @if(Auth::user()->avatar)
                <img src="{{ asset('storage/avatars/' . Auth::user()->avatar . '?v=' . time()) }}" alt="{{ Auth::user()->name }}" class="user-avatar" style="object-fit: cover;">
            @else
                <div class="user-avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
            @endif
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
            </div>
        </div>
        <form action="/logout" method="POST">
            @csrf
            <button type="submit" class="nav-btn primary"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </div>
</nav>
