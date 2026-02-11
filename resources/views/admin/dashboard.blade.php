<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Impasugong Accommodations</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-50: #F9FAFB; --gray-100: #F3F4F6; --gray-200: #E5E7EB;
            --gray-300: #D1D5DB; --gray-400: #9CA3AF; --gray-500: #6B7280;
            --gray-600: #4B5563; --gray-700: #374151; --gray-800: #1F2937;
            --blue-500: #3B82F6; --blue-600: #2563EB;
            --red-500: #EF4444; --orange-500: #F97316;
            --purple-500: #8B5CF6;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
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
        
        .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-logo img { width: 45px; height: 45px; border-radius: 50%; border: 2px solid var(--green-primary); }
        .nav-logo span { font-size: 1.2rem; font-weight: 700; color: var(--green-dark); }
        
        .nav-links { display: flex; gap: 25px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--gray-600); font-weight: 500; padding: 8px 12px; border-radius: 8px; transition: all 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: var(--green-soft); color: var(--green-dark); }
        
        .nav-actions { display: flex; gap: 15px; align-items: center; }
        .nav-btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.3s; cursor: pointer; border: none; }
        .nav-btn.primary { background: var(--green-primary); color: var(--white); }
        .nav-btn.primary:hover { background: var(--green-dark); }
        .nav-btn.secondary { background: var(--green-soft); color: var(--green-dark); }
        
        /* Main Layout */
        .dashboard-layout { display: flex; padding-top: 80px; }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--white);
            min-height: calc(100vh - 80px);
            padding: 30px 0;
            box-shadow: 2px 0 20px rgba(27, 94, 32, 0.1);
            position: fixed;
            height: calc(100vh - 80px);
            overflow-y: auto;
        }
        
        .sidebar-section { margin-bottom: 25px; }
        .sidebar-title { font-size: 0.75rem; color: var(--green-medium); text-transform: uppercase; letter-spacing: 1.5px; padding: 0 25px; margin-bottom: 12px; font-weight: 600; }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li a { display: flex; align-items: center; gap: 15px; padding: 14px 25px; color: var(--gray-700); text-decoration: none; transition: all 0.3s; border-left: 4px solid transparent; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: var(--green-soft); border-left-color: var(--green-primary); color: var(--green-dark); }
        .sidebar-menu li a .icon { font-size: 1.3rem; }
        .sidebar-menu li a .badge { margin-left: auto; background: var(--red-500); color: white; padding: 3px 10px; border-radius: 50px; font-size: 0.75rem; }
        
        /* Main Content */
        .main-content { flex: 1; padding: 30px 40px; margin-left: 280px; min-height: calc(100vh - 80px); }
        
        /* Page Header */
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 2rem; color: var(--green-dark); margin-bottom: 5px; }
        .page-header p { color: var(--gray-500); }
        
        /* KPI Cards Grid */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .kpi-card {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s;
        }
        .kpi-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(27, 94, 32, 0.15); }
        .kpi-icon { width: 55px; height: 55px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .kpi-icon.green { background: var(--green-soft); }
        .kpi-icon.blue { background: #E3F2FD; }
        .kpi-icon.orange { background: #FFF3E0; }
        .kpi-icon.purple { background: #F3E5F5; }
        .kpi-icon.red { background: #FFEBEE; }
        .kpi-info h3 { font-size: 1.8rem; color: var(--green-dark); margin-bottom: 3px; }
        .kpi-info p { color: var(--gray-500); font-size: 0.85rem; }
        
        /* Section Cards */
        .section-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
            margin-bottom: 25px;
            overflow: hidden;
        }
        .section-header { padding: 20px 25px; border-bottom: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; }
        .section-header h3 { font-size: 1.1rem; color: var(--gray-800); font-weight: 600; }
        .section-header .tabs { display: flex; gap: 10px; }
        .section-header .tab { padding: 8px 16px; border-radius: 8px; border: none; background: var(--gray-100); color: var(--gray-600); cursor: pointer; font-weight: 500; transition: all 0.3s; }
        .section-header .tab.active { background: var(--green-primary); color: white; }
        .section-body { padding: 25px; }
        
        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .stat-box {
            background: var(--white);
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
        }
        .stat-box h4 { font-size: 0.85rem; color: var(--gray-500); margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-box .value { font-size: 2rem; font-weight: 700; color: var(--green-dark); margin-bottom: 5px; }
        .stat-box .trend { font-size: 0.85rem; display: flex; align-items: center; gap: 5px; }
        .stat-box .trend.up { color: var(--green-primary); }
        .stat-box .trend.down { color: var(--red-500); }
        .stat-box .subtext { font-size: 0.8rem; color: var(--gray-400); margin-top: 8px; }
        
        /* Charts Container */
        .chart-container { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; margin-bottom: 25px; }
        .chart-box { background: var(--white); padding: 25px; border-radius: 14px; box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08); }
        .chart-box h4 { font-size: 1rem; color: var(--gray-800); margin-bottom: 20px; font-weight: 600; }
        
        /* CSS Line Chart */
        .line-chart { height: 250px; position: relative; display: flex; align-items: flex-end; gap: 8px; padding-bottom: 30px; }
        .chart-bar { flex: 1; background: linear-gradient(to top, var(--green-primary), var(--green-medium)); border-radius: 6px 6px 0 0; position: relative; transition: all 0.3s; min-height: 20px; }
        .chart-bar:hover { background: linear-gradient(to top, var(--green-dark), var(--green-primary)); }
        .chart-bar span { position: absolute; bottom: -25px; left: 50%; transform: translateX(-50%); font-size: 0.7rem; color: var(--gray-500); white-space: nowrap; }
        
        /* CSS Pie Chart */
        .pie-container { display: flex; align-items: center; gap: 30px; justify-content: center; }
        .pie-chart { width: 180px; height: 180px; border-radius: 50%; background: conic-gradient(var(--green-primary) 0deg 120deg, var(--blue-500) 120deg 200deg, var(--orange-500) 200deg 280deg, var(--purple-500) 280deg 340deg, var(--gray-300) 340deg 360deg); position: relative; }
        .pie-chart::before { content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100px; height: 100px; background: white; border-radius: 50%; }
        .pie-legend { display: flex; flex-direction: column; gap: 12px; }
        .legend-item { display: flex; align-items: center; gap: 10px; font-size: 0.9rem; }
        .legend-color { width: 14px; height: 14px; border-radius: 4px; }
        
        /* Tables */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 14px; text-align: left; border-bottom: 1px solid var(--gray-200); }
        .data-table th { font-weight: 600; color: var(--gray-600); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; background: var(--cream); }
        .data-table tr:hover { background: var(--green-white); }
        .data-table td { color: var(--gray-700); font-size: 0.9rem; }
        
        /* Status Badges */
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.pending { background: #FFF3E0; color: #E65100; }
        .status-badge.confirmed { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.completed { background: #E3F2FD; color: #1565C0; }
        .status-badge.cancelled { background: #FFEBEE; color: #C62828; }
        .status-badge.active { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.inactive { background: var(--gray-200); color: var(--gray-600); }
        
        /* Role Badges */
        .role-badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
        .role-badge.admin { background: #FEE2E2; color: #991B1B; }
        .role-badge.owner { background: #FEF3C7; color: #92400E; }
        .role-badge.client { background: #D1FAE5; color: #065F46; }
        
        /* Quick Actions */
        .quick-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .quick-stat { background: var(--cream); padding: 20px; border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.3s; }
        .quick-stat:hover { background: var(--green-soft); }
        .quick-stat .icon { font-size: 2rem; margin-bottom: 10px; }
        .quick-stat h4 { color: var(--green-dark); font-size: 1.3rem; margin-bottom: 5px; }
        .quick-stat p { color: var(--gray-600); font-size: 0.85rem; }
        
        /* Messages List */
        .message-item { display: flex; gap: 15px; padding: 15px; border-bottom: 1px solid var(--gray-200); transition: all 0.3s; }
        .message-item:hover { background: var(--green-white); }
        .message-item:last-child { border-bottom: none; }
        .message-avatar { width: 45px; height: 45px; border-radius: 50%; background: var(--green-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; flex-shrink: 0; }
        .message-content { flex: 1; }
        .message-content h4 { color: var(--gray-800); font-size: 0.95rem; margin-bottom: 3px; }
        .message-content p { color: var(--gray-500); font-size: 0.85rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .message-time { font-size: 0.75rem; color: var(--gray-400); }
        
        /* Progress Bar */
        .progress-bar { height: 8px; background: var(--gray-200); border-radius: 4px; overflow: hidden; margin-top: 10px; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, var(--green-primary), var(--green-medium)); border-radius: 4px; transition: width 0.5s ease; }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .chart-container { grid-template-columns: 1fr; }
        }
        
        @media (max-width: 1024px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
        }
        
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .nav-links { display: none; }
            .main-content { padding: 20px; }
            .stats-grid { grid-template-columns: 1fr; }
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        }
        
        /* Animations */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate { animation: fadeInUp 0.6s ease forwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="{{ route('landing') }}" class="nav-logo">
            <img src="/1.jpg" alt="Logo">
            <span>Impasugong</span>
        </a>
        
        <ul class="nav-links">
            <li><a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a></li>
            <li><a href="{{ route('admin.users') }}">Users</a></li>
            <li><a href="{{ route('admin.bookings') }}">Bookings</a></li>
            <li><a href="{{ route('messages.index') }}">Messages</a></li>
        </ul>
        
        <div class="nav-actions">
            <form action="{{ route('profile.edit') }}" method="GET">
                @csrf
                <button type="submit" class="nav-btn secondary">⚙️ Settings</button>
            </form>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-btn primary">Logout</button>
            </form>
        </div>
    </nav>
    
    <!-- Dashboard Layout -->
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-section">
                <h3 class="sidebar-title">Analytics</h3>
                <ul class="sidebar-menu">
                    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="icon">📊</span> Dashboard
                    </a></li>
                    <li><a href="{{ route('admin.bookings') }}" class="{{ request()->routeIs('admin.bookings') ? 'active' : '' }}">
                        <span class="icon">📅</span> Bookings
                    </a></li>
                </ul>
            </div>
            
            <div class="sidebar-section">
                <h3 class="sidebar-title">Management</h3>
                <ul class="sidebar-menu">
                    <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <span class="icon">👥</span> Users <span class="badge">{{ $kpis['total_users'] ?? 0 }}</span>
                    </a></li>
                    <li><a href="{{ route('admin.bookings') }}" class="{{ request()->routeIs('admin.bookings') ? 'active' : '' }}">
                        <span class="icon">📅</span> Bookings <span class="badge">{{ $kpis['pending_bookings'] ?? 0 }}</span>
                    </a></li>
                    <li><a href="{{ route('owner.accommodations.index') }}" class="{{ request()->routeIs('owner.accommodations.*') ? 'active' : '' }}">
                        <span class="icon">🏠</span> Properties
                    </a></li>
                    <li><a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">
                        <span class="icon">💬</span> Messages
                    </a></li>
                </ul>
            </div>
            
            <div class="sidebar-section">
                <h3 class="sidebar-title">Account</h3>
                <ul class="sidebar-menu">
                    <li><a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <span class="icon">👤</span> My Profile
                    </a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: flex; align-items: center; gap: 15px; padding: 14px 25px; margin: 0; cursor: pointer; color: #C62828; text-decoration: none; transition: all 0.3s; border-left: 4px solid transparent;">
                            @csrf
                            <span class="icon">🚪</span>
                            <button type="submit" style="background: none; border: none; cursor: pointer; color: inherit; font: inherit; padding: 0; margin: 0;">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <div class="page-header animate">
                <h1>Admin Dashboard</h1>
                <p>Welcome back! Here's your business overview.</p>
            </div>
            
            <!-- KPI Cards -->
            <div class="kpi-grid">
                <div class="kpi-card animate delay-1">
                    <div class="kpi-icon green">👥</div>
                    <div class="kpi-info">
                        <h3>{{ number_format($kpis['total_users'] ?? 0) }}</h3>
                        <p>Total Users</p>
                    </div>
                </div>
                <div class="kpi-card animate delay-2">
                    <div class="kpi-icon blue">🏠</div>
                    <div class="kpi-info">
                        <h3>{{ number_format($kpis['total_accommodations'] ?? 0) }}</h3>
                        <p>Properties</p>
                    </div>
                </div>
                <div class="kpi-card animate delay-3">
                    <div class="kpi-icon orange">📅</div>
                    <div class="kpi-info">
                        <h3>{{ number_format($kpis['total_bookings'] ?? 0) }}</h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
                <div class="kpi-card animate delay-4">
                    <div class="kpi-icon purple">💰</div>
                    <div class="kpi-info">
                        <h3>₱{{ number_format($kpis['total_revenue'] ?? 0, 0, '.', ',') }}</h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                <div class="kpi-card animate delay-1">
                    <div class="kpi-icon red">⏳</div>
                    <div class="kpi-info">
                        <h3>{{ number_format($kpis['pending_bookings'] ?? 0) }}</h3>
                        <p>Pending Bookings</p>
                    </div>
                </div>
            </div>
            
            <!-- Weekly Stats -->
            <div class="stats-grid animate delay-2">
                <div class="stat-box">
                    <h4>📅 This Week</h4>
                    <div class="value">{{ number_format($weeklyBookings ?? 0) }}</div>
                    <div class="trend up">Bookings</div>
                    <div class="subtext">Occupancy Rate: {{ $weeklyOccupancyRate ?? 0 }}%</div>
                </div>
                <div class="stat-box">
                    <h4>💵 Weekly Revenue</h4>
                    <div class="value">₱{{ number_format($weeklyRevenue ?? 0, 0, '.', ',') }}</div>
                    <div class="trend up">↑ Growing</div>
                    <div class="subtext">Most booked: {{ $weeklyMostBooked->accommodation->name ?? 'N/A' }}</div>
                </div>
                <div class="stat-box">
                    <h4>📈 Monthly Revenue</h4>
                    <div class="value">₱{{ number_format($monthlyRevenue ?? 0, 0, '.', ',') }}</div>
                    <div class="trend {{ $monthlyGrowthRate >= 0 ? 'up' : 'down' }}">
                        {{ $monthlyGrowthRate >= 0 ? '↑' : '↓' }} {{ abs($monthlyGrowthRate ?? 0) }}%
                    </div>
                    <div class="subtext">vs last month</div>
                </div>
                <div class="stat-box">
                    <h4>📅 Monthly Bookings</h4>
                    <div class="value">{{ number_format($monthlyBookings ?? 0) }}</div>
                    <div class="trend up">Active</div>
                    <div class="subtext">{{ $clientActivity ?? 0 }} new clients</div>
                </div>
            </div>
            
            <!-- Charts Row -->
            <div class="chart-container animate delay-3">
                <!-- Revenue Chart -->
                <div class="chart-box">
                    <h4>📊 Revenue & Bookings (Last 12 Months)</h4>
                    <div class="line-chart">
                        @if(isset($revenueChartData) && count($revenueChartData) > 0)
                            @foreach($revenueChartData as $index => $revenue)
                                @php
                                    $maxRevenue = max($revenueChartData);
                                    $height = $maxRevenue > 0 ? ($revenue / $maxRevenue) * 200 : 10;
                                    $bookingHeight = $maxRevenue > 0 ? (($bookingsChartData[$index] / max($bookingsChartData)) * 200) : 10;
                                @endphp
                                <div class="chart-bar" style="height: {{ $height + 20 }}px;" title="Revenue: ₱{{ number_format($revenue, 0, '.', ',') }}">
                                    <span>{{ substr($monthLabels[$index] ?? '', 0, 3) }}</span>
                                </div>
                            @endforeach
                        @else
                            <p style="color: var(--gray-400); text-align: center; width: 100%;">No data available</p>
                        @endif
                    </div>
                    <div style="display: flex; justify-content: center; gap: 30px; margin-top: 15px;">
                        <span style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--gray-600);">
                            <span style="width: 12px; height: 12px; background: var(--green-primary); border-radius: 3px;"></span>
                            Revenue (PHP)
                        </span>
                    </div>
                </div>
                
                <!-- Occupancy Chart -->
                <div class="chart-box">
                    <h4>🏠 Properties by Type</h4>
                    <div class="pie-container">
                        <div class="pie-chart"></div>
                        <div class="pie-legend">
                            <div class="legend-item">
                                <span class="legend-color" style="background: var(--green-primary);"></span>
                                <span>Traveller-Inn ({{ $occupancyByType['traveller-inn'] ?? 0 }})</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: var(--blue-500);"></span>
                                <span>Airbnb ({{ $occupancyByType['airbnb'] ?? 0 }})</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: var(--orange-500);"></span>
                                <span>Daily Rental ({{ $occupancyByType['daily-rental'] ?? 0 }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Yearly Stats -->
            <div class="section-card animate delay-4">
                <div class="section-header">
                    <h3>📈 Yearly Business Insights</h3>
                </div>
                <div class="section-body">
                    <div class="stats-grid">
                        <div class="stat-box">
                            <h4>💰 Annual Revenue</h4>
                            <div class="value">₱{{ number_format($yearlyRevenue ?? 0, 0, '.', ',') }}</div>
                            <div class="trend {{ $yearlyGrowthRate >= 0 ? 'up' : 'down' }}">
                                {{ $yearlyGrowthRate >= 0 ? '↑' : '↓' }} {{ abs($yearlyGrowthRate ?? 0) }}%
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ min(100, ($yearlyRevenue / 5000000) * 100) }}%"></div>
                            </div>
                        </div>
                        <div class="stat-box">
                            <h4>📅 Total Bookings</h4>
                            <div class="value">{{ number_format($yearlyBookings ?? 0) }}</div>
                            <div class="trend up">vs {{ number_format($lastYearBookings ?? 0) }} last year</div>
                        </div>
                        <div class="stat-box">
                            <h4>🏆 Top Property</h4>
                            <div class="value" style="font-size: 1.2rem;">{{ $yearlyMostProfitable->accommodation->name ?? 'N/A' }}</div>
                            <div class="subtext">Most profitable this year</div>
                        </div>
                        <div class="stat-box">
                            <h4>📊 Avg Booking Value</h4>
                            <div class="value">₱{{ number_format($kpis['average_booking_value'] ?? 0, 0, '.', ',') }}</div>
                            <div class="subtext">Per transaction</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Grid -->
            <div class="stats-grid">
                <!-- Recent Bookings -->
                <div class="section-card">
                    <div class="section-header">
                        <h3>📅 Recent Bookings</h3>
                        <a href="{{ route('admin.bookings') }}" style="color: var(--green-primary); text-decoration: none; font-size: 0.9rem;">View All →</a>
                    </div>
                    <div class="section-body" style="padding: 0;">
                        @if(isset($recentBookings) && count($recentBookings) > 0)
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Property</th>
                                        <th>Guest</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $booking)
                                        <tr>
                                            <td>{{ $booking->accommodation->name ?? 'N/A' }}</td>
                                            <td>{{ $booking->client->name ?? 'N/A' }}</td>
                                            <td><span class="status-badge {{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                                            <td>₱{{ number_format($booking->total_price, 0, '.', ',') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p style="padding: 25px; color: var(--gray-400); text-align: center;">No bookings yet</p>
                        @endif
                    </div>
                </div>
                
                <!-- Recent Users -->
                <div class="section-card">
                    <div class="section-header">
                        <h3>👥 Recent Users</h3>
                        <a href="{{ route('admin.users') }}" style="color: var(--green-primary); text-decoration: none; font-size: 0.9rem;">View All →</a>
                    </div>
                    <div class="section-body" style="padding: 0;">
                        @if(isset($recentUsers) && count($recentUsers) > 0)
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                        <tr>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--green-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.8rem;">
                                                        {{ substr($user->name, 0, 2) }}
                                                    </div>
                                                    <div>
                                                        <div style="font-weight: 500;">{{ $user->name }}</div>
                                                        <div style="font-size: 0.8rem; color: var(--gray-500);">{{ $user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="role-badge {{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                                            <td><span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p style="padding: 25px; color: var(--gray-400); text-align: center;">No users yet</p>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Add hover effects to sidebar menu
        document.querySelectorAll('.sidebar-menu li a').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href') === '#') {
                    e.preventDefault();
                    document.querySelectorAll('.sidebar-menu li a').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
