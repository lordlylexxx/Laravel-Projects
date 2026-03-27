<nav class="navbar">
    @php
        $currentTenant = \App\Models\Tenant::current();
        $current = $active ?? '';
    @endphp

    <a href="/owner/dashboard" class="nav-logo">
        <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo">
        <span>
            ImpaStay
            @if($currentTenant)
                <small style="display:block; font-size:0.72rem; font-weight:600; color: var(--green-medium); margin-top:2px;">{{ $currentTenant->name }}</small>
            @endif
        </span>
    </a>

    <ul class="nav-links">
        <li><a href="/owner/dashboard" class="{{ $current === 'dashboard' || request()->routeIs('owner.dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="/owner/accommodations" class="{{ $current === 'accommodations' || request()->routeIs('owner.accommodations.*') ? 'active' : '' }}"><i class="fas fa-building"></i> My Units</a></li>
        <li><a href="/owner/bookings" class="{{ $current === 'bookings' || request()->routeIs('owner.bookings.*') ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> Bookings</a></li>
        <li><a href="/owner/system-updates" class="{{ $current === 'updates' || request()->routeIs('owner.updates.*') || request()->routeIs('admin.updates.*') ? 'active' : '' }}"><i class="fas fa-cloud-download-alt"></i> Updates</a></li>
        <li><a href="/messages" class="{{ $current === 'messages' || request()->routeIs('messages.*') ? 'active' : '' }}"><i class="fas fa-envelope"></i> Messages</a></li>
        <li><a href="/profile" class="{{ $current === 'settings' || request()->routeIs('profile.edit') ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a></li>
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
