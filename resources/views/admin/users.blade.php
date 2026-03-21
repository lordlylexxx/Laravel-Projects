<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --green-dark: #1B5E20;
            --green-primary: #2E7D32;
            --green-medium: #43A047;
            --green-light: #66BB6A;
            --green-pale: #81C784;
            --green-soft: #C8E6C9;
            --green-white: #E8F5E9;
            --white: #FFFFFF;
            --cream: #F1F8E9;
            --gray-600: #4B5563;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
        }
        
        /* Same styles as admin/dashboard.blade.php for navbar */
        .navbar {
            background: var(--white);
            padding: 0 40px;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        
        .nav-logo img {
            width: 45px;
            height: 45px;
            border-radius: 0;
            border: none;
            object-fit: contain;
        }
        
        .nav-logo span {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--green-dark);
        }

        .nav-links { display: flex; gap: 8px; list-style: none; }
        .nav-links a {
            text-decoration: none;
            color: var(--gray-600);
            font-weight: 500;
            padding: 10px 16px;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-links a:hover, .nav-links a.active {
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: var(--white);
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
        }

        .nav-actions { display: flex; gap: 15px; align-items: center; }
        .user-display {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: linear-gradient(135deg, var(--green-soft), var(--green-white));
            border-radius: 10px;
            border: 1px solid var(--green-soft);
        }
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .user-info { text-align: left; }
        .user-name {
            font-weight: 700;
            color: var(--green-dark);
            font-size: 0.95rem;
            line-height: 1.2;
        }
        .user-role {
            font-size: 0.75rem;
            color: var(--green-medium);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .nav-btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-btn.primary {
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            color: var(--white);
        }
        .nav-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
        }
        
        .dashboard-layout {
            display: flex;
            padding-top: 80px;
        }
        
        .main-content {
            flex: 1;
            padding: 30px 40px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            font-size: 2rem;
            color: var(--green-dark);
        }
        
        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .search-input {
            flex: 1;
            max-width: 400px;
            padding: 12px 20px;
            border: 2px solid var(--green-soft);
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
        }
        
        .search-input:focus {
            border-color: var(--green-primary);
        }
        
        .filter-select {
            padding: 12px 20px;
            border: 2px solid var(--green-soft);
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
            background: white;
            cursor: pointer;
        }
        
        .card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
        }
        
        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--green-soft);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h3 {
            font-size: 1.15rem;
            color: var(--green-dark);
            font-weight: 600;
        }
        
        .card-body {
            padding: 0;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: var(--white);
        }
        
        .btn-secondary {
            background: var(--green-soft);
            color: var(--green-dark);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--green-soft);
        }
        
        th {
            font-weight: 600;
            color: var(--green-dark);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: var(--cream);
        }
        
        td {
            color: var(--green-medium);
            font-size: 0.9rem;
        }
        
        tr:hover {
            background: var(--green-white);
        }
        
        .user-info-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-avatar-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--green-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .user-details h4 {
            color: var(--green-dark);
            margin-bottom: 2px;
            font-size: 0.95rem;
        }
        
        .user-details p {
            font-size: 0.8rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-badge.active { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.pending { background: #FFF3E0; color: #E65100; }
        .status-badge.inactive { background: #FFEBEE; color: #C62828; }
        
        .role-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .role-badge.client { background: #E3F2FD; color: #1565C0; }
        .role-badge.owner { background: #FFF3E0; color: #E65100; }
        .role-badge.admin { background: var(--green-soft); color: var(--green-dark); }
        
        .action-btns {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .action-btn.view { background: var(--green-soft); color: var(--green-primary); }
        .action-btn.edit { background: #E3F2FD; color: #1976D2; }
        .action-btn.message { background: #FFF3E0; color: #E65100; }
        .action-btn.delete { background: #FFEBEE; color: #C62828; }
        
        .action-btn:hover { transform: scale(1.1); }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 20px;
        }
        
        .pagination button {
            padding: 8px 15px;
            border: 2px solid var(--green-soft);
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .pagination button.active {
            background: var(--green-primary);
            color: white;
            border-color: var(--green-primary);
        }

        @media (max-width: 768px) {
            .navbar { padding: 0 20px; height: 60px; }
            .nav-links { display: none; }
            .main-content { padding: 20px; }
        }

        @include('admin.partials.top-navbar-styles')
    </style>
</head>
<body>
    <!-- Navigation -->
    @include('admin.partials.top-navbar', ['active' => 'users'])
    
    <!-- Dashboard Layout -->
    <div class="dashboard-layout">
        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>User Management</h1>
                <button class="btn btn-primary">+ Add New User</button>
            </div>
            
            <div class="search-filter">
                <input type="text" class="search-input" placeholder="Search users by name or email...">
                <select class="filter-select">
                    <option value="">All Roles</option>
                    <option value="client">Clients</option>
                    <option value="owner">Owners</option>
                    <option value="admin">Admins</option>
                </select>
                <select class="filter-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <h3>Owner Domains ({{ isset($owners) ? $owners->count() : 0 }})</h3>
                    <span style="font-size: 0.8rem; color: var(--gray-600);">Each owner subdomain</span>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Owner</th>
                                    <th>Email</th>
                                    <th>Domain</th>
                                    <th>Public URL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($owners ?? collect()) as $owner)
                                    @php
                                        $ownerTenant = $owner->tenant ?? $owner->ownedTenant;
                                        $ownerDomain = $ownerTenant?->domain;
                                        $ownerUrl = $ownerTenant?->publicUrl();
                                        $domainEnabled = (bool) ($ownerTenant?->domain_enabled ?? true);
                                    @endphp
                                    <tr>
                                        <td>{{ $owner->name }}</td>
                                        <td>{{ $owner->email }}</td>
                                        <td>
                                            {{ $ownerDomain ?? 'Not assigned yet' }}
                                            <div style="font-size: 0.75rem; margin-top: 4px; color: {{ $domainEnabled ? 'var(--green-primary)' : '#B91C1C' }}; font-weight: 600;">
                                                {{ $domainEnabled ? 'Domain enabled' : 'Domain disabled' }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($ownerUrl && $domainEnabled)
                                                <a href="{{ $ownerUrl }}" target="_blank" style="color: var(--green-primary); font-weight: 600; text-decoration: none;">
                                                    Open site
                                                </a>
                                                <div style="font-size: 0.75rem; color: var(--gray-600); margin-top: 4px;">{{ $ownerUrl }}</div>
                                            @elseif($ownerUrl && ! $domainEnabled)
                                                <span style="color: #B91C1C;">Disabled by admin</span>
                                            @else
                                                <span style="color: var(--warning);">Pending provisioning</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; color: var(--gray-600);">No owner accounts found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>All Users ({{ $users->total() }})</h3>
                    <button class="btn btn-secondary btn-sm">Export CSV</button>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    @php
                                        $nameParts = preg_split('/\s+/', trim((string) $user->name)) ?: [];
                                        $initials = strtoupper(collect($nameParts)->filter()->take(2)->map(fn ($part) => substr($part, 0, 1))->implode(''));
                                        $statusClass = !$user->is_active ? 'inactive' : (is_null($user->email_verified_at) ? 'pending' : 'active');
                                        $statusLabel = ucfirst($statusClass);
                                        $lastLogin = $user->last_login ? $user->last_login->diffForHumans() : 'Never';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="user-info-cell">
                                                <div class="user-avatar-small">{{ $initials ?: 'U' }}</div>
                                                <div class="user-details">
                                                    <h4>{{ $user->name }}</h4>
                                                    <p>{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="role-badge {{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                                        <td>{{ $user->phone ?? 'N/A' }}</td>
                                        <td><span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                        <td>{{ $lastLogin }}</td>
                                        <td>
                                            <div class="action-btns">
                                                <a href="mailto:{{ $user->email }}" class="action-btn message" title="Email">✉️</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: var(--gray-600);">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($users->hasPages())
                        <div class="pagination">
                            @if($users->onFirstPage())
                                <button disabled style="opacity: 0.5; cursor: not-allowed;">&lt;</button>
                            @else
                                <button onclick="window.location.href='{{ $users->previousPageUrl() }}'">&lt;</button>
                            @endif

                            @foreach($users->getUrlRange(max(1, $users->currentPage() - 1), min($users->lastPage(), $users->currentPage() + 1)) as $page => $url)
                                <button class="{{ $users->currentPage() === $page ? 'active' : '' }}" onclick="window.location.href='{{ $url }}'">{{ $page }}</button>
                            @endforeach

                            @if($users->hasMorePages())
                                <button onclick="window.location.href='{{ $users->nextPageUrl() }}'">&gt;</button>
                            @else
                                <button disabled style="opacity: 0.5; cursor: not-allowed;">&gt;</button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>

