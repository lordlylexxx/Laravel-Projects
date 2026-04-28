@php
    $current = $active ?? '';
    $tenant = \App\Models\Tenant::current();
    $tenantDisplayName = $tenant?->name ?: config('app.name', 'ImpaStay');
@endphp

<nav class="navbar" id="appNavbar">
    <a href="/dashboard" class="nav-logo">
        <img src="/SYSTEMLOGO.png" alt="" width="45" height="45">
        <span class="nav-logo-text">
            <span class="nav-logo-title">{{ $tenantDisplayName }}</span>
            <span class="nav-logo-subtitle">Impasugong Accommodations</span>
        </span>
    </a>

    <button type="button" class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false"
            onclick="var n=document.getElementById('appNavbar');var o=n.classList.toggle('nav-open');this.setAttribute('aria-expanded',o?'true':'false');">
        <i class="fas fa-bars"></i>
    </button>

    <ul class="nav-links">
        <li><a href="/dashboard" class="{{ $current === 'dashboard' ? 'active' : '' }}"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="/accommodations" class="{{ $current === 'accommodations' ? 'active' : '' }}"><i class="fas fa-building"></i> Accommodations</a></li>
        @if(Auth::user()->tenantClientMayManageOwnStays())
            <li><a href="/bookings" class="{{ $current === 'bookings' ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> My Bookings</a></li>
        @endif
        @if(Auth::user()->tenantClientMayUseMessaging())
            <li><a href="/messages" class="{{ $current === 'messages' ? 'active' : '' }}"><i class="fas fa-envelope"></i> Messages @if(($unreadMessagesCount ?? 0) > 0)<span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;border-radius:999px;padding:0 5px;background:#EF4444;color:#fff;font-size:0.68rem;font-weight:700;margin-left:6px;">{{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}</span>@endif</a></li>
        @endif
        @if(Auth::user()->tenantClientMaySubmitUpdateTickets())
            <li><a href="/update-tickets" class="{{ $current === 'update-tickets' ? 'active' : '' }}"><i class="fas fa-life-ring"></i> Support</a></li>
        @endif
        @if(Auth::user()->tenantClientMayEditOwnProfile())
            <li><a href="/profile" class="{{ $current === 'settings' ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a></li>
        @endif
    </ul>

    <div class="nav-actions">
        @include('partials.notification-bell')
        <div class="user-display">
            @if(Auth::user()->avatar)
                <img src="{{ asset('storage/avatars/' . Auth::user()->avatar . '?v=' . time()) }}" alt="{{ Auth::user()->name }}" class="user-avatar" style="object-fit: cover;">
            @else
                <div class="user-avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
            @endif
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">Client</div>
            </div>
        </div>

        <form action="/logout" method="POST">
            @csrf
            <button type="submit" class="nav-btn primary"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </div>
</nav>