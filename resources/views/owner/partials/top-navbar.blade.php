<nav class="navbar" id="appNavbar">
    @php
        $currentTenant = \App\Models\Tenant::current();
        $current = $active ?? '';
        $authUser = Auth::user();
        $displayRole = $authUser?->tenantCustomRole?->name ?: ucfirst((string) $authUser?->role);
        $dashboardHref = '/owner/dashboard';
        $reportsHref = '/owner/reports/monthly';
        $unitsHref = '/owner/accommodations';
        $bookingsHref = '/owner/bookings';
        $updatesHref = '/settings/updates';
        $usersHref = '/owner/users';
        $messagesHref = '/messages';
        $profileHref = '/profile';
        $logoutHref = '/logout';
        $isOwnerRole = $authUser?->isOwner() ?? false;
        $canSeeDashboard = $isOwnerRole || ($authUser?->isAdmin() && \App\Models\Tenant::checkCurrent()) || $authUser?->hasAnyPermission([
            \App\Models\User::PERM_USERS_VIEW,
            \App\Models\User::PERM_ACCOMMODATIONS_CREATE,
            \App\Models\User::PERM_ACCOMMODATIONS_UPDATE,
            \App\Models\User::PERM_ACCOMMODATIONS_DELETE,
            \App\Models\User::PERM_BOOKINGS_MANAGE,
            \App\Models\User::PERM_MESSAGES_MANAGE,
            \App\Models\User::PERM_REPORTS_VIEW,
        ]);
        $canSeeReports = $isOwnerRole || $authUser?->hasPermission(\App\Models\User::PERM_REPORTS_VIEW);
        $canSeeUnits = $isOwnerRole || $authUser?->hasAnyPermission([
            \App\Models\User::PERM_ACCOMMODATIONS_CREATE,
            \App\Models\User::PERM_ACCOMMODATIONS_UPDATE,
            \App\Models\User::PERM_ACCOMMODATIONS_DELETE,
        ]);
        $canSeeBookings = $isOwnerRole || $authUser?->hasPermission(\App\Models\User::PERM_BOOKINGS_MANAGE);
        $canSeeUsers = $isOwnerRole || $authUser?->hasPermission(\App\Models\User::PERM_USERS_VIEW);
        $canSeeUpdates = $isOwnerRole || $authUser?->hasPermission(\App\Models\User::PERM_REPORTS_VIEW);
        $canSeeMessages = $isOwnerRole || $authUser?->hasPermission(\App\Models\User::PERM_MESSAGES_MANAGE);
    @endphp

    <a href="{{ $dashboardHref }}" class="nav-logo">
        <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo">
        <span>
            ImpaStay
            @if($currentTenant)
                <small style="display:block; font-size:0.72rem; font-weight:600; color: var(--green-medium); margin-top:2px;">{{ $currentTenant->name }}</small>
            @endif
        </span>
    </a>

    <button type="button" class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false"
            onclick="var n=document.getElementById('appNavbar');var o=n.classList.toggle('nav-open');this.setAttribute('aria-expanded',o?'true':'false');">
        <i class="fas fa-bars"></i>
    </button>

    <ul class="nav-links">
        @if($canSeeDashboard)
            <li><a href="{{ $dashboardHref }}" class="{{ $current === 'dashboard' || request()->routeIs('owner.dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i> Dashboard</a></li>
        @endif
        @if($canSeeReports)
            <li><a href="{{ $reportsHref }}" class="{{ $current === 'reports' || request()->routeIs('owner.reports.*') ? 'active' : '' }}"><i class="fas fa-chart-column"></i> Reports</a></li>
        @endif
        @if($canSeeUnits)
            <li><a href="{{ $unitsHref }}" class="{{ $current === 'accommodations' || request()->routeIs('owner.accommodations.*') ? 'active' : '' }}"><i class="fas fa-building"></i> My Units</a></li>
        @endif
        @if($canSeeBookings)
            <li><a href="{{ $bookingsHref }}" class="{{ $current === 'bookings' || request()->routeIs('owner.bookings.*') ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> Bookings</a></li>
        @endif
        @if($canSeeUsers)
            <li><a href="{{ $usersHref }}" class="{{ $current === 'users' || request()->routeIs('owner.users.*') ? 'active' : '' }}"><i class="fas fa-users-cog"></i> Users</a></li>
        @endif
        @if($canSeeUpdates)
            <li><a href="{{ $updatesHref }}" class="{{ $current === 'updates' || request()->routeIs('owner.settings.updates.*') || request()->routeIs('admin.updates.*') ? 'active' : '' }}"><i class="fas fa-cloud-download-alt"></i> Updates</a></li>
        @endif
        @if($canSeeMessages)
            <li><a href="{{ $messagesHref }}" class="{{ $current === 'messages' || request()->routeIs('messages.*') ? 'active' : '' }}"><i class="fas fa-envelope"></i> Messages @if(($unreadMessagesCount ?? 0) > 0)<span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;border-radius:999px;padding:0 5px;background:#EF4444;color:#fff;font-size:0.68rem;font-weight:700;margin-left:6px;">{{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}</span>@endif</a></li>
        @endif
        <li><a href="{{ $profileHref }}" class="{{ $current === 'settings' || request()->routeIs('profile.edit') ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>

    <div class="nav-actions">
        <div class="user-display">
            @if($authUser?->avatar)
                <img src="{{ asset('storage/avatars/' . $authUser->avatar . '?v=' . time()) }}" alt="{{ $authUser->name }}" class="user-avatar" style="object-fit: cover;">
            @else
                <div class="user-avatar">{{ substr((string) $authUser?->name, 0, 2) }}</div>
            @endif
            <div class="user-info">
                <div class="user-name">{{ $authUser?->name }}</div>
                <div class="user-role">{{ $displayRole }}</div>
            </div>
        </div>

        <form action="{{ $logoutHref }}" method="POST">
            @csrf
            <button type="submit" class="nav-btn primary"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </div>
</nav>
