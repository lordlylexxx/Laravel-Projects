@php($current = $active ?? '')

<nav class="navbar">
    <a href="{{ route('landing') }}" class="nav-logo">
        <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo">
        <span>ImpaStay</span>
    </a>

    <ul class="nav-links">
        <li><a href="{{ route('admin.dashboard') }}" class="{{ $current === 'dashboard' ? 'active' : '' }}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.tenants') }}" class="{{ $current === 'tenants' ? 'active' : '' }}"><i class="fas fa-building-user"></i> Tenants</a></li>
        <li><a href="{{ route('admin.bookings') }}" class="{{ $current === 'bookings' ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> Bookings</a></li>
        <li><a href="{{ route('admin.updates.index') }}" class="{{ $current === 'updates' ? 'active' : '' }}"><i class="fas fa-cloud-download-alt"></i> Updates</a></li>
        <li><a href="{{ route('messages.index') }}" class="{{ $current === 'messages' ? 'active' : '' }}"><i class="fas fa-envelope"></i> Messages</a></li>
        <li><a href="{{ route('profile.edit') }}" class="{{ $current === 'settings' ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a></li>
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
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-btn primary"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </div>
</nav>
