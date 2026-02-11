<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Impasugong Accommodations</title>
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
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
        }
        
        /* Navigation */
        .navbar {
            background: var(--green-dark);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .nav-logo img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid var(--green-light);
        }
        
        .nav-logo span {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--white);
        }
        
        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--green-pale);
            font-weight: 500;
            transition: color 0.3s;
            padding: 8px 15px;
            border-radius: 8px;
        }
        
        .nav-links a:hover,
        .nav-links a.active {
            color: var(--white);
            background: rgba(255, 255, 255, 0.1);
        }
        
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .notification-btn {
            position: relative;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            cursor: pointer;
            font-size: 1.3rem;
            color: var(--white);
            padding: 10px;
            border-radius: 10px;
        }
        
        .notification-badge {
            position: absolute;
            top: -3px;
            right: -3px;
            background: var(--danger);
            color: white;
            font-size: 0.65rem;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--green-light);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 2px solid var(--white);
        }
        
        .user-info {
            text-align: left;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--white);
            font-size: 0.95rem;
        }
        
        .user-role {
            font-size: 0.75rem;
            color: var(--green-pale);
        }
        
        /* Sidebar Layout */
        .dashboard-layout {
            display: flex;
            padding-top: 80px;
        }
        
        /* Sidebar */
        .sidebar {
            width: 300px;
            background: var(--white);
            min-height: calc(100vh - 80px);
            padding: 30px 0;
            box-shadow: 2px 0 20px rgba(27, 94, 32, 0.1);
        }
        
        .sidebar-section {
            margin-bottom: 25px;
        }
        
        .sidebar-title {
            font-size: 0.75rem;
            color: var(--green-medium);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 0 25px;
            margin-bottom: 12px;
            font-weight: 600;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 25px;
            color: var(--green-dark);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: var(--green-soft);
            border-left-color: var(--green-primary);
        }
        
        .sidebar-menu li a .icon {
            font-size: 1.3rem;
        }
        
        .sidebar-menu li a .badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.75rem;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px 40px;
        }
        
        /* Welcome Section */
        .welcome-section {
            margin-bottom: 30px;
        }
        
        .welcome-section h1 {
            font-size: 2rem;
            color: var(--green-dark);
            margin-bottom: 8px;
        }
        
        .welcome-section p {
            color: var(--green-medium);
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(27, 94, 32, 0.15);
        }
        
        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
        }
        
        .stat-icon.green { background: var(--green-soft); }
        .stat-icon.blue { background: #E3F2FD; }
        .stat-icon.orange { background: #FFF3E0; }
        .stat-icon.purple { background: #F3E5F5; }
        .stat-icon.red { background: #FFEBEE; }
        
        .stat-info h3 {
            font-size: 1.6rem;
            color: var(--green-dark);
            margin-bottom: 4px;
        }
        
        .stat-info p {
            color: var(--green-medium);
            font-size: 0.85rem;
        }
        
        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }
        
        /* Cards */
        .card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            margin-bottom: 25px;
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
            padding: 25px;
        }
        
        /* Button Styles */
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
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.3);
        }
        
        .btn-secondary {
            background: var(--green-soft);
            color: var(--green-dark);
        }
        
        .btn-secondary:hover {
            background: var(--green-pale);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        
        /* Table Styles */
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 14px;
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
        
        /* User Info in Table */
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
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-badge.active { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.pending { background: #FFF3E0; color: #E65100; }
        .status-badge.confirmed { background: #E3F2FD; color: #1565C0; }
        .status-badge.cancelled { background: #FFEBEE; color: #C62828; }
        .status-badge.completed { background: #E8F5E9; color: #2E7D32; }
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
        
        /* Action Buttons */
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
            text-decoration: none;
        }
        
        .action-btn.view { background: var(--green-soft); color: var(--green-primary); }
        .action-btn.edit { background: #E3F2FD; color: #1976D2; }
        .action-btn.message { background: #FFF3E0; color: #E65100; }
        .action-btn.delete { background: #FFEBEE; color: #C62828; }
        
        .action-btn:hover { transform: scale(1.1); }
        
        /* Quick Actions */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .quick-stat {
            background: var(--cream);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .quick-stat:hover {
            background: var(--green-soft);
        }
        
        .quick-stat .icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .quick-stat h4 {
            color: var(--green-dark);
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        .quick-stat p {
            color: var(--green-medium);
            font-size: 0.85rem;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        .delay-1 { animation-delay: 0.15s; }
        .delay-2 { animation-delay: 0.3s; }
        .delay-3 { animation-delay: 0.45s; }
        .delay-4 { animation-delay: 0.6s; }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }
            
            .nav-links {
                display: none;
            }
            
            .dashboard-layout {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                min-height: auto;
            }
            
            .main-content {
                padding: 20px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-logo">
            <img src="/1.jpg" alt="Municipality Logo">
            <span>Admin Panel</span>
        </div>
        <ul class="nav-links">
            <a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a>
            <a href="{{ route('admin.users') }}">Users</a>
            <a href="{{ route('owner.accommodations.index') }}">Properties</a>
            <a href="{{ route('bookings.index') }}">Bookings</a>
        </ul>
        <div class="nav-actions">
            <a href="{{ route('messages.index') }}" class="notification-btn">
                🔔
                @if(isset($unread_messages) && $unread_messages > 0)
                    <span class="notification-badge">{{ $unread_messages }}</span>
                @endif
            </a>
            <div class="user-menu" onclick="event.preventDefault(); document.getElementById('profile-form').submit();" style="cursor: pointer;">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" 
                         alt="{{ Auth::user()->name }}" 
                         class="user-avatar" 
                         style="width: 38px; height: 38px; object-fit: cover; border: 2px solid var(--white);">
                @else
                    <div class="user-avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
                @endif
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();" 
               style="padding: 8px 16px; background: rgba(255,255,255,0.2); color: white; border-radius: 8px; text-decoration: none; font-size: 0.85rem;">
               Logout
            </a>
        </div>
    </nav>
    
    <form id="user-menu-form" action="{{ route('profile.edit') }}" method="GET" style="display: none;"></form>
    
    <!-- Dashboard Layout -->
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-section">
                <h3 class="sidebar-title">Main Menu</h3>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="active">
                            <span class="icon">📊</span>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users') }}">
                            <span class="icon">👥</span>
                            Users
                            @if(isset($stats['total_users']))
                                <span class="badge">{{ $stats['total_users'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('owner.accommodations.index') }}">
                            <span class="icon">🏠</span>
                            Properties
                            @if(isset($stats['total_properties']))
                                <span class="badge">{{ $stats['total_properties'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bookings.index') }}">
                            <span class="icon">📅</span>
                            Bookings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('messages.index') }}">
                            <span class="icon">💬</span>
                            Messages
                            @if(isset($unread_messages) && $unread_messages > 0)
                                <span class="badge">{{ $unread_messages }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="sidebar-section">
                <h3 class="sidebar-title">Account</h3>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('profile.edit') }}">
                            <span class="icon">👤</span>
                            Profile
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="icon">🚪</span>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section animate">
                <h1>Admin Dashboard</h1>
                <p>Overview of Impasugong Accommodations Platform</p>
            </div>
            
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card animate delay-1">
                    <div class="stat-icon green">👥</div>
                    <div class="stat-info">
                        <h3>{{ $stats['total_users'] ?? 0 }}</h3>
                        <p>Total Users</p>
                    </div>
                </div>
                
                <div class="stat-card animate delay-2">
                    <div class="stat-icon blue">🏠</div>
                    <div class="stat-info">
                        <h3>{{ $stats['total_properties'] ?? 0 }}</h3>
                        <p>Properties</p>
                    </div>
                </div>
                
                <div class="stat-card animate delay-3">
                    <div class="stat-icon orange">📅</div>
                    <div class="stat-info">
                        <h3>{{ $stats['total_bookings'] ?? 0 }}</h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
                
                <div class="stat-card animate delay-4">
                    <div class="stat-icon red">⏳</div>
                    <div class="stat-info">
                        <h3>{{ $stats['pending_verifications'] ?? 0 }}</h3>
                        <p>Pending Verifications</p>
                    </div>
                </div>
            </div>
            
            <!-- Content Grid -->
            <div class="content-grid">
                <div class="left-content">
                    <!-- Recent Users -->
                    <div class="card animate delay-2">
                        <div class="card-header">
                            <h3>Recent Users</h3>
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Joined</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recent_users as $user)
                                        <tr>
                                            <td>
                                                <div class="user-info-cell">
                                                    <div class="user-avatar-small">{{ substr($user->name, 0, 2) }}</div>
                                                    <div class="user-details">
                                                        <h4>{{ $user->name }}</h4>
                                                        <p>{{ $user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="role-badge {{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                                            <td><span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="action-btns">
                                                    <a href="{{ route('messages.index') }}" class="action-btn message" title="Message">💬</a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Bookings -->
                    <div class="card animate delay-3">
                        <div class="card-header">
                            <h3>Recent Bookings</h3>
                            <a href="{{ route('bookings.index') }}" class="btn btn-secondary btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Property</th>
                                            <th>Guest</th>
                                            <th>Dates</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recent_bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->accommodation->name ?? 'N/A' }}</td>
                                            <td>{{ $booking->client->name ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d') }}-{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d, Y') }}</td>
                                            <td>₱{{ number_format($booking->total_price, 2) }}</td>
                                            <td><span class="status-badge {{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="right-content">
                    <!-- Quick Stats -->
                    <div class="card animate delay-2">
                        <div class="card-header">
                            <h3>Quick Overview</h3>
                        </div>
                        <div class="card-body">
                            <div class="quick-stats">
                                <a href="{{ route('admin.users') }}" class="quick-stat" style="text-decoration: none;">
                                    <span class="icon">👥</span>
                                    <h4>{{ $stats['active_users'] ?? 0 }}</h4>
                                    <p>Active Users</p>
                                </a>
                                <a href="{{ route('admin.users') }}" class="quick-stat" style="text-decoration: none;">
                                    <span class="icon">🆕</span>
                                    <h4>{{ $stats['new_users_this_month'] ?? 0 }}</h4>
                                    <p>New This Month</p>
                                </a>
                                <a href="{{ route('owner.accommodations.index') }}" class="quick-stat" style="text-decoration: none;">
                                    <span class="icon">🏨</span>
                                    <h4>{{ $stats['total_properties'] ?? 0 }}</h4>
                                    <p>Properties</p>
                                </a>
                                <a href="{{ route('bookings.index') }}" class="quick-stat" style="text-decoration: none;">
                                    <span class="icon">⭐</span>
                                    <h4>4.8</h4>
                                    <p>Avg Rating</p>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Messages -->
                    <div class="card animate delay-3">
                        <div class="card-header">
                            <h3>Recent Messages</h3>
                            <a href="{{ route('messages.index') }}" class="btn btn-primary btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            <ul class="message-list" style="list-style: none; padding: 0;">
                                @foreach($recent_messages as $message)
                                <li class="message-item" style="display: flex; gap: 15px; padding: 15px; border-bottom: 1px solid var(--green-soft);">
                                    <div class="user-avatar-small">{{ substr($message->sender->name, 0, 2) }}</div>
                                    <div class="message-content" style="flex: 1;">
                                        <h4 style="color: var(--green-dark); font-size: 0.9rem; margin-bottom: 4px;">{{ $message->sender->name }}</h4>
                                        <p style="color: var(--green-medium); font-size: 0.85rem; margin-bottom: 6px;">{{ Str::limit($message->content, 50) }}</p>
                                        <span class="message-time" style="font-size: 0.75rem; color: var(--green-light);">{{ $message->created_at->diffForHumans() }}</span>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Admin Actions -->
                    <div class="card animate delay-4">
                        <div class="card-header">
                            <h3>Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <a href="{{ route('messages.index') }}" class="btn btn-primary" style="width: 100%;">📢 Send Announcement</a>
                                <a href="{{ route('admin.users') }}" class="btn btn-secondary" style="width: 100%;">📊 Generate Report</a>
                                <a href="{{ route('profile.edit') }}" class="btn btn-secondary" style="width: 100%;">👤 Edit Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <!-- Laravel Blade Data -->
    <script>
        // Pass PHP data to JavaScript if needed
        window.Laravel = {
            userName: '{{ Auth::user()->name }}',
            userRole: '{{ Auth::user()->role }}'
        };
    </script>
</body>
</html>
