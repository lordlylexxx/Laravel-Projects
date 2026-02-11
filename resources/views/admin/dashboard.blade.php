<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VerdeVistas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            --purple-500: #8B5CF6; --cyan-500: #06B6D4;
            --amber-500: #F59E0B;
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
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        
        .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-logo img { width: 45px; height: 45px; border-radius: 50%; border: 3px solid var(--green-primary); }
        .nav-logo span { font-size: 1.3rem; font-weight: 700; color: var(--green-dark); }
        
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
        
        .nav-actions { display: flex; gap: 12px; align-items: center; }
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
        
        /* Main Layout */
        .dashboard-layout { display: flex; padding-top: 80px; }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--white);
            min-height: calc(100vh - 80px);
            padding: 25px 0;
            box-shadow: 4px 0 20px rgba(27, 94, 32, 0.1);
            position: fixed;
            height: calc(100vh - 80px);
            overflow-y: auto;
        }
        
        .sidebar-section { margin-bottom: 20px; }
        .sidebar-title { 
            font-size: 0.7rem; 
            color: var(--green-medium); 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            padding: 0 25px; 
            margin-bottom: 10px; 
            font-weight: 700; 
        }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li a { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            padding: 14px 25px; 
            color: var(--gray-700); 
            text-decoration: none; 
            transition: all 0.3s; 
            border-left: 4px solid transparent;
            margin: 4px 8px;
            border-radius: 8px;
        }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { 
            background: linear-gradient(135deg, var(--green-soft), var(--green-white)); 
            border-left-color: var(--green-primary);
            color: var(--green-dark);
            transform: translateX(4px);
        }
        .sidebar-menu li a .icon { 
            font-size: 1.2rem; 
            width: 24px;
            text-align: center;
        }
        .sidebar-menu li a .badge { 
            margin-left: auto; 
            background: linear-gradient(135deg, var(--red-500), #DC2626); 
            color: white; 
            padding: 4px 10px; 
            border-radius: 50px; 
            font-size: 0.7rem; 
            font-weight: 700;
        }
        
        /* Main Content */
        .main-content { flex: 1; padding: 30px 40px; margin-left: 280px; min-height: calc(100vh - 80px); }
        
        /* Page Header */
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 2rem; color: var(--green-dark); margin-bottom: 5px; font-weight: 700; }
        .page-header p { color: var(--gray-500); font-size: 0.95rem; }
        
        /* KPI Cards */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .kpi-card {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(27, 94, 32, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s;
            border: 1px solid var(--green-soft);
        }
        .kpi-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 15px 40px rgba(27, 94, 32, 0.15);
        }
        .kpi-icon { 
            width: 60px; 
            height: 60px; 
            border-radius: 14px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.5rem;
        }
        .kpi-icon.green { background: linear-gradient(135deg, var(--green-soft), var(--green-pale)); color: var(--green-dark); }
        .kpi-icon.blue { background: linear-gradient(135deg, #DBEAFE, #BFDBFE); color: var(--blue-600); }
        .kpi-icon.orange { background: linear-gradient(135deg, #FEF3C7, #FDE68A); color: var(--amber-500); }
        .kpi-icon.purple { background: linear-gradient(135deg, #EDE9FE, #DDD6FE); color: var(--purple-500); }
        .kpi-icon.red { background: linear-gradient(135deg, #FEE2E2, #FECACA); color: var(--red-500); }
        .kpi-icon.cyan { background: linear-gradient(135deg, #CFFAFE, #A5F3FC); color: var(--cyan-500); }
        
        .kpi-info h3 { font-size: 1.8rem; color: var(--green-dark); margin-bottom: 3px; font-weight: 700; }
        .kpi-info p { color: var(--gray-500); font-size: 0.85rem; }
        
        /* Dashboard Card */
        .dashboard-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(27, 94, 32, 0.08);
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid var(--green-soft);
        }
        .dashboard-card h3 { 
            font-size: 1.1rem; 
            color: var(--gray-800); 
            margin-bottom: 20px; 
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .dashboard-card h3 .icon { color: var(--green-primary); }
        
        /* Content Grid */
        .content-grid { display: grid; grid-template-columns: 320px 1fr; gap: 25px; }
        .content-left { display: flex; flex-direction: column; gap: 20px; }
        .content-right { display: flex; flex-direction: column; gap: 20px; }
        
        /* Chart Container */
        .chart-container { position: relative; height: 320px; }
        .chart-container-sm { position: relative; height: 280px; }
        
        /* Table */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 14px; text-align: left; border-bottom: 1px solid var(--gray-200); }
        .data-table th { font-weight: 600; color: var(--gray-600); font-size: 0.8rem; text-transform: uppercase; background: var(--cream); }
        .data-table tr:hover { background: var(--green-white); }
        
        /* Status Badges */
        .status-badge { display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.active { background: linear-gradient(135deg, var(--green-soft), var(--green-pale)); color: var(--green-dark); }
        .status-badge.pending { background: linear-gradient(135deg, #FEF3C7, #FDE68A); color: #B45309; }
        .status-badge.confirmed { background: linear-gradient(135deg, #DBEAFE, #BFDBFE); color: #1D4ED8; }
        .status-badge.cancelled { background: linear-gradient(135deg, #FEE2E2, #FECACA); color: #DC2626; }
        .status-badge.completed { background: linear-gradient(135deg, var(--green-soft), var(--green-pale)); color: var(--green-dark); }
        
        /* Quick Stats Grid */
        .quick-stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .quick-stat-card {
            background: linear-gradient(135deg, var(--cream), var(--green-white));
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid var(--green-soft);
        }
        .quick-stat-card:hover { 
            background: linear-gradient(135deg, var(--green-soft), var(--green-pale)); 
            transform: translateY(-3px);
        }
        .quick-stat-card .icon { font-size: 1.8rem; color: var(--green-primary); margin-bottom: 10px; }
        .quick-stat-card h4 { font-size: 1.4rem; color: var(--green-dark); margin-bottom: 5px; font-weight: 700; }
        .quick-stat-card p { color: var(--gray-600); font-size: 0.85rem; }
        
        /* Gear Icon (Settings) - Icon Only */
        .settings-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--green-soft), var(--green-pale));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--green-dark);
            font-size: 1.2rem;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }
        .settings-icon:hover {
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: white;
            transform: rotate(90deg);
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .content-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 1024px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
        }
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .nav-links { display: none; }
            .main-content { padding: 20px; }
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
            <img src="/SYSTEMLOGO.png" alt="VerdeVistas Logo">
            <span>VerdeVistas</span>
        </a>
        
        <ul class="nav-links">
            <li><a href="{{ route('admin.dashboard') }}" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="{{ route('admin.bookings') }}"><i class="fas fa-calendar-check"></i> Bookings</a></li>
            <li><a href="{{ route('messages.index') }}"><i class="fas fa-envelope"></i> Messages</a></li>
        </ul>
        
        <div class="nav-actions">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-btn primary"><i class="fas fa-sign-out-alt"></i> Logout</button>
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
                    <li><a href="{{ route('admin.dashboard') }}" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                    <li><a href="{{ route('admin.bookings') }}"><i class="fas fa-calendar-alt"></i> Bookings</a></li>
                </ul>
            </div>
            
            <div class="sidebar-section">
                <h3 class="sidebar-title">Management</h3>
                <ul class="sidebar-menu">
                    <li><a href="{{ route('admin.users') }}"><i class="fas fa-users-cog"></i> Users <span class="badge">{{ $kpis['total_users'] ?? 0 }}</span></a></li>
                    <li><a href="{{ route('owner.accommodations.index') }}"><i class="fas fa-building"></i> Properties</a></li>
                    <li><a href="{{ route('messages.index') }}"><i class="fas fa-comments"></i> Messages</a></li>
                </ul>
            </div>
            
            <!-- Gear Icon Only (Settings) - Lower-Left Corner -->
            <div style="position: absolute; bottom: 20px; left: 25px;">
                <a href="{{ route('profile.edit') }}" class="settings-icon" title="Settings">
                    <i class="fas fa-cog"></i>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <div class="page-header animate">
                <h1><i class="fas fa-chart-line" style="color: var(--green-primary); margin-right: 12px;"></i>Sales Monitoring Dashboard</h1>
                <p>Business performance metrics and analytics</p>
            </div>
            
            <!-- KPI Cards -->
            <div class="kpi-grid animate delay-1">
                <div class="kpi-card">
                    <div class="kpi-icon green"><i class="fas fa-peso-sign"></i></div>
                    <div class="kpi-info">
                        <h3>₱{{ number_format($kpis['total_revenue'] ?? 0, 0, '.', ',') }}</h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon blue"><i class="fas fa-calendar-week"></i></div>
                    <div class="kpi-info">
                        <h3>₱{{ number_format($weeklyRevenue ?? 0, 0, '.', ',') }}</h3>
                        <p>Weekly Revenue</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon orange"><i class="fas fa-calendar"></i></div>
                    <div class="kpi-info">
                        <h3>₱{{ number_format($monthlyRevenue ?? 0, 0, '.', ',') }}</h3>
                        <p>Monthly Revenue</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon purple"><i class="fas fa-calendar-year"></i></div>
                    <div class="kpi-info">
                        <h3>₱{{ number_format($yearlyRevenue ?? 0, 0, '.', ',') }}</h3>
                        <p>Yearly Revenue</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon cyan"><i class="fas fa-ticket-alt"></i></div>
                    <div class="kpi-info">
                        <h3>{{ number_format($kpis['total_bookings'] ?? 0) }}</h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon green"><i class="fas fa-users"></i></div>
                    <div class="kpi-info">
                        <h3>{{ number_format($kpis['active_clients'] ?? 0) }}</h3>
                        <p>Active Clients</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon blue"><i class="fas fa-bed"></i></div>
                    <div class="kpi-info">
                        <h3>{{ $occupancyRate ?? 0 }}%</h3>
                        <p>Occupancy Rate</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon orange"><i class="fas fa-trophy"></i></div>
                    <div class="kpi-info">
                        <h3 style="font-size: 1.2rem;">{{ $topProperty->accommodation->name ?? 'N/A' }}</h3>
                        <p>Top Performing Unit</p>
                    </div>
                </div>
            </div>
            
            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Left Column -->
                <div class="content-left">
                    <!-- Business KPI Overview -->
                    <div class="dashboard-card animate delay-2">
                        <h3><i class="fas fa-bullseye icon"></i>Business KPI Overview</h3>
                        <div class="quick-stats-grid">
                            <div class="quick-stat-card">
                                <div class="icon"><i class="fas fa-trending-up"></i></div>
                                <h4>{{ $growthRate ?? 0 }}%</h4>
                                <p>Growth Rate</p>
                            </div>
                            <div class="quick-stat-card">
                                <div class="icon"><i class="fas fa-star"></i></div>
                                <h4>₱{{ number_format($kpis['average_booking_value'] ?? 0, 0) }}</h4>
                                <p>Avg Booking</p>
                            </div>
                            <div class="quick-stat-card">
                                <div class="icon"><i class="fas fa-home"></i></div>
                                <h4>{{ number_format($kpis['total_accommodations'] ?? 0) }}</h4>
                                <p>Total Units</p>
                            </div>
                            <div class="quick-stat-card">
                                <div class="icon"><i class="fas fa-check-circle"></i></div>
                                <h4>{{ number_format($kpis['verified_properties'] ?? 0) }}</h4>
                                <p>Verified Units</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Revenue Distribution -->
                    <div class="dashboard-card animate delay-3">
                        <h3><i class="fas fa-chart-pie icon"></i>Revenue Distribution</h3>
                        <div class="chart-container-sm">
                            <canvas id="revenueDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="content-right">
                    <!-- Monthly Revenue Trend -->
                    <div class="dashboard-card animate delay-2">
                        <h3><i class="fas fa-chart-area icon"></i>Monthly Revenue Trend</h3>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Bookings Per Month -->
                    <div class="dashboard-card animate delay-3">
                        <h3><i class="fas fa-chart-bar icon"></i>Bookings Per Month</h3>
                        <div class="chart-container">
                            <canvas id="bookingsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Bookings -->
            <div class="dashboard-card animate delay-4">
                <h3><i class="fas fa-history icon"></i>Recent Bookings</h3>
                @if(isset($recentBookings) && count($recentBookings) > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-building"></i> Property</th>
                                <th><i class="fas fa-user"></i> Client</th>
                                <th><i class="fas fa-calendar-check"></i> Check-In</th>
                                <th><i class="fas fa-money-bill-wave"></i> Amount</th>
                                <th><i class="fas fa-info-circle"></i> Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                                <tr>
                                    <td><strong>{{ $booking->accommodation->name ?? 'N/A' }}</strong></td>
                                    <td>{{ $booking->client->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</td>
                                    <td><strong>₱{{ number_format($booking->total_price, 0, '.', ',') }}</strong></td>
                                    <td><span class="status-badge {{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 40px; color: var(--gray-400);">
                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; color: var(--gray-300);"></i>
                        <p>No recent bookings</p>
                    </div>
                @endif
            </div>
        </main>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Revenue Line Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: [
                            {{ $monthlyRevenueData['jan'] ?? 0 }},
                            {{ $monthlyRevenueData['feb'] ?? 0 }},
                            {{ $monthlyRevenueData['mar'] ?? 0 }},
                            {{ $monthlyRevenueData['apr'] ?? 0 }},
                            {{ $monthlyRevenueData['may'] ?? 0 }},
                            {{ $monthlyRevenueData['jun'] ?? 0 }},
                            {{ $monthlyRevenueData['jul'] ?? 0 }},
                            {{ $monthlyRevenueData['aug'] ?? 0 }},
                            {{ $monthlyRevenueData['sep'] ?? 0 }},
                            {{ $monthlyRevenueData['oct'] ?? 0 }},
                            {{ $monthlyRevenueData['nov'] ?? 0 }},
                            {{ $monthlyRevenueData['dec'] ?? 0 }}
                        ],
                        borderColor: 'rgb(46, 125, 50)',
                        backgroundColor: 'rgba(46, 125, 50, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(46, 125, 50)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
            
            // Bookings Bar Chart
            const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
            new Chart(bookingsCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Bookings',
                        data: [
                            {{ $monthlyBookingsData['jan'] ?? 0 }},
                            {{ $monthlyBookingsData['feb'] ?? 0 }},
                            {{ $monthlyBookingsData['mar'] ?? 0 }},
                            {{ $monthlyBookingsData['apr'] ?? 0 }},
                            {{ $monthlyBookingsData['may'] ?? 0 }},
                            {{ $monthlyBookingsData['jun'] ?? 0 }},
                            {{ $monthlyBookingsData['jul'] ?? 0 }},
                            {{ $monthlyBookingsData['aug'] ?? 0 }},
                            {{ $monthlyBookingsData['sep'] ?? 0 }},
                            {{ $monthlyBookingsData['oct'] ?? 0 }},
                            {{ $monthlyBookingsData['nov'] ?? 0 }},
                            {{ $monthlyBookingsData['dec'] ?? 0 }}
                        ],
                        backgroundColor: 'rgba(59, 162, 246, 0.8)',
                        borderColor: 'rgb(59, 162, 246)',
                        borderWidth: 1,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Revenue Distribution Doughnut Chart
            const distributionCtx = document.getElementById('revenueDistributionChart').getContext('2d');
            new Chart(distributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Traveller-Inn', 'Airbnb', 'Daily Rental'],
                    datasets: [{
                        data: [
                            {{ $revenueByType['traveller-inn'] ?? 0 }},
                            {{ $revenueByType['airbnb'] ?? 0 }},
                            {{ $revenueByType['daily-rental'] ?? 0 }}
                        ],
                        backgroundColor: [
                            'rgb(46, 125, 50)',
                            'rgb(59, 162, 246)',
                            'rgb(249, 115, 22)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
