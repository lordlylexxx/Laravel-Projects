@php($current = $active ?? '')

<nav class="navbar">
    <a href="/dashboard" class="nav-logo">
        <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo">
        <span>ImpaStay</span>
    </a>

    <ul class="nav-links">
        <li><a href="/dashboard" class="{{ $current === 'browse' ? 'active' : '' }}"><i class="fas fa-search"></i> Browse</a></li>
        <li><a href="/accommodations" class="{{ $current === 'accommodations' ? 'active' : '' }}"><i class="fas fa-building"></i> Accommodations</a></li>
        <li><a href="/bookings" class="{{ $current === 'bookings' ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> My Bookings</a></li>
        <li><a href="/messages" class="{{ $current === 'messages' ? 'active' : '' }}"><i class="fas fa-envelope"></i> Messages @if(($unreadMessagesCount ?? 0) > 0)<span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;border-radius:999px;padding:0 5px;background:#EF4444;color:#fff;font-size:0.68rem;font-weight:700;margin-left:6px;">{{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}</span>@endif</a></li>
        <li><a href="/profile" class="{{ $current === 'settings' ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>

    <div class="nav-actions">
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