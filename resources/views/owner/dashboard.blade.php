<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - Impasugong Accommodations</title>
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
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
        }
        
        /* Navigation */
        .navbar {
            background: var(--white);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(27, 94, 32, 0.1);
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
            border: 2px solid var(--green-primary);
        }
        
        .nav-logo span {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--green-dark);
        }
        
        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--green-primary);
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover,
        .nav-links a.active {
            color: var(--green-dark);
        }
        
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .notification-btn {
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.5rem;
            color: var(--green-primary);
            text-decoration: none;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--green-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        /* Sidebar Layout */
        .dashboard-layout {
            display: flex;
            padding-top: 80px;
        }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--white);
            min-height: calc(100vh - 80px);
            padding: 30px 0;
            box-shadow: 2px 0 20px rgba(27, 94, 32, 0.1);
        }
        
        .sidebar-section {
            margin-bottom: 30px;
        }
        
        .sidebar-title {
            font-size: 0.8rem;
            color: var(--green-medium);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 25px;
            margin-bottom: 15px;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
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
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
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
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        
        .stat-icon.green {
            background: var(--green-soft);
        }
        
        .stat-icon.blue {
            background: #E3F2FD;
        }
        
        .stat-icon.orange {
            background: #FFF3E0;
        }
        
        .stat-icon.purple {
            background: #F3E5F5;
        }
        
        .stat-info h3 {
            font-size: 1.8rem;
            color: var(--green-dark);
            margin-bottom: 5px;
        }
        
        .stat-info p {
            color: var(--green-medium);
            font-size: 0.9rem;
        }
        
        /* Cards */
        .card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            margin-bottom: 30px;
        }
        
        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--green-soft);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h3 {
            font-size: 1.2rem;
            color: var(--green-dark);
        }
        
        .card-body {
            padding: 25px;
        }
        
        /* Button Styles */
        .btn {
            padding: 12px 25px;
            border-radius: 10px;
            font-size: 0.95rem;
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
            padding: 8px 15px;
            font-size: 0.85rem;
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
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--green-soft);
        }
        
        th {
            font-weight: 600;
            color: var(--green-dark);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            color: var(--green-medium);
        }
        
        tr:hover {
            background: var(--cream);
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-badge.pending {
            background: #FFF3E0;
            color: #E65100;
        }
        
        .status-badge.confirmed {
            background: var(--green-soft);
            color: var(--green-dark);
        }
        
        .status-badge.cancelled {
            background: #FFEBEE;
            color: #C62828;
        }
        
        .status-badge.completed {
            background: #E3F2FD;
            color: #1565C0;
        }
        
        .status-badge.paid {
            background: #E8F5E9;
            color: #2E7D32;
        }
        
        /* Property Card in Table */
        .property-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .property-thumb {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            object-fit: cover;
        }
        
        .property-details h4 {
            color: var(--green-dark);
            margin-bottom: 3px;
        }
        
        .property-details p {
            font-size: 0.85rem;
        }
        
        /* Action Buttons */
        .action-btns {
            display: flex;
            gap: 10px;
        }
        
        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .action-btn.view {
            background: var(--green-soft);
            color: var(--green-primary);
        }
        
        .action-btn.edit {
            background: #E3F2FD;
            color: #1976D2;
        }
        
        .action-btn.delete {
            background: #FFEBEE;
            color: #C62828;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .quick-action-card {
            background: var(--white);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-decoration: none;
        }
        
        .quick-action-card:hover {
            border-color: var(--green-primary);
            transform: translateY(-5px);
        }
        
        .quick-action-card .icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .quick-action-card h4 {
            color: var(--green-dark);
            margin-bottom: 8px;
        }
        
        .quick-action-card p {
            color: var(--green-medium);
            font-size: 0.9rem;
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
        
        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }
        
        /* Responsive */
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
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-logo">
            <a href="{{ route('owner.dashboard') }}" style="display: flex; align-items: center; gap: 15px; text-decoration: none;">
                <img src="/1.jpg" alt="Municipality Logo">
                <span>Impasugong</span>
            </a>
        </div>
        <ul class="nav-links">
            <a href="{{ route('owner.dashboard') }}" class="active">Dashboard</a>
            <a href="{{ route('owner.accommodations.index') }}">My Properties</a>
            <a href="{{ route('bookings.index') }}">Bookings</a>
            <a href="{{ route('messages.index') }}">Messages</a>
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
                         style="width: 40px; height: 40px; object-fit: cover; border: 2px solid var(--white);">
                @else
                    <div class="user-avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
                @endif
            </div>
            <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();" 
               style="padding: 8px 16px; background: var(--green-dark); color: white; border-radius: 8px; text-decoration: none; font-size: 0.9rem;">
               Logout
            </a>
        </div>
    </nav>
    
    <form id="profile-form" action="{{ route('profile.edit') }}" method="GET" style="display: none;"></form>
    
    <!-- Dashboard Layout -->
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-section">
                <h3 class="sidebar-title" style="font-weight: 600;">Main Menu</h3>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('owner.dashboard') }}" class="active">
                            <span class="icon">📊</span>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('owner.accommodations.index') }}">
                            <span class="icon">🏠</span>
                            My Properties
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
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="sidebar-section">
                <h3 class="sidebar-title" style="font-weight: 600;">Account</h3>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('profile.edit') }}">
                            <span class="icon">👤</span>
                            Profile
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="margin: 0; padding: 0;">
                            @csrf
                            <button type="submit" style="display: flex; align-items: center; gap: 15px; width: 100%; padding: 15px 25px; background: none; border: none; cursor: pointer; color: #C62828; font: inherit; border-left: 4px solid transparent; transition: all 0.3s ease;">
                                <span class="icon">🚪</span>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section animate">
                <h1>Welcome back, {{ Auth::user()->name }}!</h1>
                <p>Here's what's happening with your properties today.</p>
            </div>
            
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card animate delay-1">
                    <div class="stat-icon green">🏠</div>
                    <div class="stat-info">
                        <h3>{{ $stats['active_properties'] ?? 0 }}</h3>
                        <p>Active Properties</p>
                    </div>
                </div>
                
                <div class="stat-card animate delay-2">
                    <div class="stat-icon blue">📅</div>
                    <div class="stat-info">
                        <h3>{{ $stats['total_bookings'] ?? 0 }}</h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
                
                <div class="stat-card animate delay-3">
                    <div class="stat-icon orange">⏳</div>
                    <div class="stat-info">
                        <h3>{{ $stats['pending_bookings'] ?? 0 }}</h3>
                        <p>Pending Requests</p>
                    </div>
                </div>
                
                <div class="stat-card animate delay-1">
                    <div class="stat-icon purple">💰</div>
                    <div class="stat-info">
                        <h3>₱{{ number_format($stats['total_earnings'] ?? 0, 0) }}</h3>
                        <p>Total Earnings</p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card animate delay-2">
                <div class="card-header">
                    <h3>Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="{{ route('owner.accommodations.create') }}" class="quick-action-card">
                            <span class="icon">➕</span>
                            <h4>Add Property</h4>
                            <p>List a new accommodation</p>
                        </a>
                        <a href="{{ route('bookings.index') }}" class="quick-action-card">
                            <span class="icon">📅</span>
                            <h4>View Bookings</h4>
                            <p>Manage reservations</p>
                        </a>
                        <a href="{{ route('messages.index') }}" class="quick-action-card">
                            <span class="icon">💬</span>
                            <h4>Messages</h4>
                            <p>Respond to inquiries</p>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="quick-action-card">
                            <span class="icon">👤</span>
                            <h4>Edit Profile</h4>
                            <p>Update your details</p>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Recent Bookings -->
            <div class="card animate delay-3">
                <div class="card-header">
                    <h3>Recent Booking Requests</h3>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_bookings as $booking)
                                <tr>
                                    <td>
                                        <div class="property-info">
                                            @if($booking->accommodation && $booking->accommodation->primary_image)
                                                <img src="{{ asset('storage/' . $booking->accommodation->primary_image) }}" alt="{{ $booking->accommodation->name }}" class="property-thumb">
                                            @else
                                                <img src="/COMMUNAL.jpg" alt="Property" class="property-thumb">
                                            @endif
                                            <div class="property-details">
                                                <h4>{{ $booking->accommodation->name ?? 'N/A' }}</h4>
                                                <p>{{ ucfirst($booking->accommodation->type ?? '') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $booking->client->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d') }} - {{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</td>
                                    <td>₱{{ number_format($booking->total_price, 2) }}</td>
                                    <td><span class="status-badge {{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="{{ route('bookings.show', $booking) }}" class="action-btn view" title="View">👁️</a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px;">
                                        <p style="color: var(--green-medium);">No recent bookings found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>
</html>
