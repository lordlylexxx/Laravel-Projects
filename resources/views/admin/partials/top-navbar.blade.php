@php
    $current = $active ?? '';
    $host = request()->getHost();
    $centralDomain = (string) config('app.domain', 'localhost');
    $isCentralHost = in_array($host, [$centralDomain, 'localhost', '127.0.0.1', '::1'], true);
    $isTenantScopedAdmin = auth()->check()
        && auth()->user()->isAdmin()
        && !empty(auth()->user()->tenant_id)
        && ! $isCentralHost;
    $isTenantContext = \App\Models\Tenant::checkCurrent() || $isTenantScopedAdmin;

    // Use host-local paths to avoid cross-domain route URL generation.
    $dashboardHref = $isTenantContext ? '/owner/dashboard' : '/admin/dashboard';
    $unitsHref = $isTenantContext ? '/owner/accommodations' : '/admin/tenants';
    $bookingsHref = '/owner/bookings';
    $reportsHref = '/owner/reports/monthly';
    $updatesHref = $isTenantContext ? '/owner/system-updates' : '/admin/system-updates';
    $messagesHref = '/messages';
    $settingsHref = '/profile';
    $landingHref = '/';
@endphp

<nav class="navbar">
    <a href="{{ $landingHref }}" class="nav-logo">
        <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo">
        <span>ImpaStay</span>
    </a>

    <ul class="nav-links">
        <li><a href="{{ $dashboardHref }}" class="{{ $current === 'dashboard' ? 'active' : '' }}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="{{ $unitsHref }}" class="{{ $current === 'tenants' ? 'active' : '' }}"><i class="fas fa-building-user"></i> Tulogans</a></li>
        @if($isTenantContext)
            <li><a href="{{ $reportsHref }}" class="{{ $current === 'reports' ? 'active' : '' }}"><i class="fas fa-chart-column"></i> Reports</a></li>
            <li><a href="{{ $bookingsHref }}" class="{{ $current === 'bookings' ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> Bookings</a></li>
        @endif
        <li><a href="{{ $updatesHref }}" class="{{ $current === 'updates' ? 'active' : '' }}"><i class="fas fa-cloud-download-alt"></i> Updates</a></li>
        <li><a href="{{ $messagesHref }}" class="{{ $current === 'messages' ? 'active' : '' }}"><i class="fas fa-envelope"></i> Messages @if(($unreadMessagesCount ?? 0) > 0)<span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;border-radius:999px;padding:0 5px;background:#EF4444;color:#fff;font-size:0.68rem;font-weight:700;margin-left:6px;">{{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}</span>@endif</a></li>
        <li><a href="{{ $settingsHref }}" class="{{ $current === 'settings' ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>

    <div class="nav-actions">
        @if(($tenantUpdate['has_update'] ?? false) && !empty($tenantUpdate['download_url']))
            <a href="{{ $tenantUpdate['download_url'] }}" class="nav-btn" title="Update to v{{ $tenantUpdate['latest_version'] ?? '' }}">
                <i class="fas fa-download"></i>
                Update v{{ $tenantUpdate['latest_version'] }}
            </a>
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
