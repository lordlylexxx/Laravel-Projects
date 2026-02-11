<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Impasugong Accommodations</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
        
        /* Dashboard Cards */
        .dashboard-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .dashboard-card h3 {
            margin-bottom: 20px;
            color: #333;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        /* KPI Widget */
        .kpi-widget {
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
        }
        
        .kpi-widget .icon { font-size: 2.5rem; margin-bottom: 10px; }
        .kpi-widget .value { font-size: 2rem; font-weight: bold; margin-bottom: 5px; }
        .kpi-widget .label { font-size: 0.9rem; opacity: 0.9; }
        
        /* Stat Cards Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: var(--white);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card .icon { font-size: 1.8rem; margin-bottom: 10px; }
        .stat-card .value { font-size: 1.5rem; font-weight: bold; color: var(--green-dark); margin-bottom: 5px; }
        .stat-card .label { font-size: 0.85rem; color: var(--gray-500); }
        
        /* Layout Grid */
        .content-grid { display: grid; grid-template-columns: 300px 1fr; gap: 25px; }
        .content-left { display: flex; flex-direction: column; gap: 20px; }
        .content-right { display: flex; flex-direction: column; gap: 20px; }
        
        /* Chart Containers */
        .chart-container { position: relative; height: 300px; }
        .chart-container-sm { position: relative; height: 250px; }
        
        /* Table Styles */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 12px; text-align: left; border-bottom: 1px solid var(--gray-200); }
        .data-table th { font-weight: 600; color: var(--gray-600); font-size: 0.85rem; text-transform: uppercase; background: var(--cream); }
        .data-table tr:hover { background: var(--green-white); }
        
        /* Status Badges */
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.active { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.pending { background: #FFF3E0; color: #E65100; }
        .status-badge.confirmed { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.cancelled { background: #FFEBEE; color: #C62828; }
        .status-badge.completed { background: #E3F2FD; color: #1565C0; }
        
        /* Progress Bar */
        .progress-bar { height: 8px; background: var(--gray-200); border-radius: 4px; overflow: hidden; margin-top: 10px; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, var(--green-primary), var(--green-medium)); border-radius: 4px; transition: width 0.5s ease; }
        
        /* Growth Rate */
        .growth-rate { display: flex; align-items: center; gap: 5px; font-size: 0.85rem; }
        .growth-rate.up { color: var(--green-primary); }
        .growth-rate.down { color: var(--red-500); }
        
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
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
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
                    <li><a href="{{ route('admin.dashboard') }}" class="active">
                        <span class="icon">📊</span> Dashboard
                    </a></li>
                    <li><a href="{{ route('admin.bookings') }}">
                        <span class="icon">📅</span> Bookings
                    </a></li>
                </ul>
            </div>
            
            <div class="sidebar-section">
                <h3 class="sidebar-title">Management</h3>
                <ul class="sidebar-menu">
                    <li><a href="{{ route('admin.users') }}">
                        <span class="icon">👥</span> Users <span class="badge">{{ $kpis['total_users'] ?? 0 }}</span>
                    </a></li>
                    <li><a href="{{ route('owner.accommodations.index') }}">
                        <span class="icon">🏠</span> Properties
                    </a></li>
                    <li><a href="{{ route('messages.index') }}">
                        <span class="icon">💬</span> Messages
                    </a></li>
                </ul>
            </div>
            
            <!-- Settings Icon at Lower-Left Corner -->
            <div style="position: absolute; bottom: 20px; left: 25px; padding: 12px; background: var(--green-soft); border-radius: 10px;">
                <a href="{{ route('profile.edit') }}" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; color: var(--green-dark); text-decoration: none; transition: all 0.3s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <div class="page-header animate">
                <h1>Sales Monitoring Dashboard</h1>
                <p>Business performance metrics and analytics</p>
            </div>
            
            <!-- KPI Stats Grid -->
            <div class="stats-grid animate delay-1">
                <div class="stat-card">
                    <div class="icon" style="color: var(--green-primary);">💰</div>
                    <div class="value">₱{{ number_format($kpis['total_revenue'] ?? 0, 0, '.', ',') }}</div>
                    <div class="label">Total Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="icon" style="color: var(--blue-500);">📅</div>
                    <div class="value">₱{{ number_format($weeklyRevenue ?? 0, 0, '.', ',') }}</div>
                    <div class="label">Weekly Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="icon" style="color: var(--orange-500);">📆</div>
                    <div class="value">₱{{ number_format($monthlyRevenue ?? 0, 0, '.', ',') }}</div>
                    <div class="label">Monthly Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="icon" style="color: var(--purple-500);">📈</div>
                    <div class="value">₱{{ number_format($yearlyRevenue ?? 0, 0, '.', ',') }}</div>
                    <div class="label">Yearly Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="icon" style="color: #E91E63;">📋</div>
                    <div class="value">{{ number_format($kpis['total_bookings'] ?? 0) }}</div>
                    <div class="label">Total Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="icon" style="color: #00BCD4;">👥</div>
                    <div class="value">{{ number_format($kpis['active_clients'] ?? 0) }}</div>
                    <div class="label">Active Clients</div>
                </div>
                <div class="stat-card">
                    <div class="icon" style="color: #FF9800;">📊</div>
                    <div class="value">{{ $occupancyRate ?? 0 }}%</div>
                    <div class="label">Occupancy Rate</div>
                </div>
                <div class="stat-card">
                    <div class="icon" style="color: #9C27B0;">🏆</div>
                    <div class="value" style="font-size: 1rem;">{{ $topProperty->accommodation->name ?? 'N/A' }}</div>
                    <div class="label">Top Performing Unit</div>
                </div>
            </div>
            
            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Left Column -->
                <div class="content-left">
                    <!-- Business KPI Overview -->
                    <div class="dashboard-card animate delay-2">
                        <h3>📊 Business KPI Overview</h3>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="stat-card">
                                    <div class="stat-icon" style="color: var(--green-primary); font-size: 1.5rem; margin-bottom: 8px;">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="value" style="font-size: 1.2rem;">{{ $growthRate ?? 0 }}%</div>
                                    <div class="label">Growth Rate</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="stat-card">
                                    <div class="stat-icon" style="color: var(--blue-500); font-size: 1.5rem; margin-bottom: 8px;">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="value" style="font-size: 1.2rem;">{{ number_format($kpis['average_booking_value'] ?? 0, 0) }}</div>
                                    <div class="label">Avg Booking (₱)</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-icon" style="color: var(--orange-500); font-size: 1.5rem; margin-bottom: 8px;">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <div class="value" style="font-size: 1.2rem;">{{ number_format($kpis['total_accommodations'] ?? 0) }}</div>
                                    <div class="label">Total Units</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-icon" style="color: var(--purple-500); font-size: 1.5rem; margin-bottom: 8px;">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="value" style="font-size: 1.2rem;">{{ number_format($kpis['verified_properties'] ?? 0) }}</div>
                                    <div class="label">Verified Units</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Revenue Distribution -->
                    <div class="dashboard-card animate delay-3">
                        <h3>💰 Revenue Distribution</h3>
                        <div class="chart-container-sm">
                            <canvas id="revenueDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="content-right">
                    <!-- Monthly Revenue Trend -->
                    <div class="dashboard-card animate delay-2">
                        <h3>📈 Monthly Revenue Trend</h3>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Bookings Per Month -->
                    <div class="dashboard-card animate delay-3">
                        <h3>📅 Bookings Per Month</h3>
                        <div class="chart-container">
                            <canvas id="bookingsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="dashboard-card animate delay-4">
                <h3>📋 Recent Bookings</h3>
                @if(isset($recentBookings) && count($recentBookings) > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Property</th>
                                <th>Client</th>
                                <th>Check-In</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                                <tr>
                                    <td>{{ $booking->accommodation->name ?? 'N/A' }}</td>
                                    <td>{{ $booking->client->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</td>
                                    <td>₱{{ number_format($booking->total_price, 0, '.', ',') }}</td>
                                    <td><span class="status-badge {{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; color: var(--gray-400); padding: 20px;">No recent bookings</p>
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
                        tension: 0.4
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
            
            // Bookings Per Month Bar Chart
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
                        backgroundColor: 'rgba(67, 160, 71, 0.8)',
                        borderColor: 'rgb(46, 125, 50)',
                        borderWidth: 1
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
                        ]
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
