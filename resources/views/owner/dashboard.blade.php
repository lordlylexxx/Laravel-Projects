<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - Impasugong Accommodations</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-50: #F9FAFB; --gray-100: #F3F4F6; --gray-200: #E5E7EB;
            --gray-300: #D1D5DB; --gray-400: #9CA3AF; --gray-500: #6B7280;
            --gray-600: #4B5563; --gray-700: #374151; --gray-800: #1F2937;
            --blue-500: #3B82F6; --orange-500: #F97316; --purple-500: #8B5CF6;
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
        .sidebar-menu li a .badge { margin-left: auto; background: var(--orange-500); color: white; padding: 3px 10px; border-radius: 50px; font-size: 0.75rem; }
        
        /* Main Content */
        .main-content { flex: 1; padding: 30px 40px; margin-left: 280px; min-height: calc(100vh - 80px); }
        
        /* Page Header */
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 2rem; color: var(--green-dark); margin-bottom: 5px; }
        .page-header p { color: var(--gray-500); }
        
        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card .icon { font-size: 2rem; margin-bottom: 10px; }
        .stat-card .value { font-size: 1.8rem; font-weight: bold; color: var(--green-dark); margin-bottom: 5px; }
        .stat-card .label { font-size: 0.85rem; color: var(--gray-500); }
        
        /* Dashboard Card */
        .dashboard-card {
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 25px;
        }
        .dashboard-card h3 { font-size: 1.1rem; color: var(--gray-800); margin-bottom: 20px; font-weight: 600; }
        
        /* Property Table */
        .property-table { width: 100%; border-collapse: collapse; }
        .property-table th, .property-table td { padding: 15px; text-align: left; border-bottom: 1px solid var(--gray-200); }
        .property-table th { font-weight: 600; color: var(--gray-600); font-size: 0.85rem; text-transform: uppercase; background: var(--cream); }
        .property-table tr:hover { background: var(--green-white); }
        
        /* Status Badges */
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.active { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.inactive { background: var(--gray-200); color: var(--gray-600); }
        .status-badge.pending { background: #FFF3E0; color: #E65100; }
        .status-badge.confirmed { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.cancelled { background: #FFEBEE; color: #C62828; }
        
        /* Quick Actions */
        .quick-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .quick-action-card {
            background: var(--white);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-decoration: none;
            display: block;
        }
        .quick-action-card:hover { border-color: var(--green-primary); transform: translateY(-5px); }
        .quick-action-card .icon { font-size: 2.5rem; margin-bottom: 15px; }
        .quick-action-card h4 { color: var(--green-dark); margin-bottom: 8px; font-size: 1rem; }
        .quick-action-card p { color: var(--gray-500); font-size: 0.85rem; }
        
        /* Revenue Card */
        .revenue-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .revenue-card {
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
        }
        .revenue-card .icon { font-size: 2rem; margin-bottom: 10px; }
        .revenue-card .value { font-size: 2rem; font-weight: bold; margin-bottom: 5px; }
        .revenue-card .label { font-size: 0.9rem; opacity: 0.9; }
        
        /* Responsive */
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
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="{{ route('owner.dashboard') }}" class="nav-logo">
            <img src="/1.jpg" alt="Logo">
            <span>Impasugong</span>
        </a>
        
        <ul class="nav-links">
            <li><a href="{{ route('owner.dashboard') }}" class="active">Dashboard</a></li>
            <li><a href="{{ route('owner.accommodations.index') }}">My Units</a></li>
            <li><a href="{{ route('bookings.index') }}">Bookings</a></li>
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
                <h3 class="sidebar-title">Management</h3>
                <ul class="sidebar-menu">
                    <li><a href="{{ route('owner.dashboard') }}" class="active">
                        <span class="icon">📊</span> Dashboard
                    </a></li>
                    <li><a href="{{ route('owner.accommodations.index') }}">
                        <span class="icon">🏠</span> My Units
                    </a></li>
                    <li><a href="{{ route('bookings.index') }}">
                        <span class="icon">📅</span> Bookings
                        @if(($stats['pending_bookings'] ?? 0) > 0)
                            <span class="badge">{{ $stats['pending_bookings'] }}</span>
                        @endif
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
                <h1>Unit Management Dashboard</h1>
                <p>Monitor your properties and booking performance</p>
            </div>
            
            <!-- Quick Stats -->
            <div class="stats-grid animate delay-1">
                <div class="stat-card">
                    <div class="icon">🏠</div>
                    <div class="value">{{ $stats['total_properties'] ?? 0 }}</div>
                    <div class="label">Total Units</div>
                </div>
                <div class="stat-card">
                    <div class="icon">✅</div>
                    <div class="value">{{ $stats['active_properties'] ?? 0 }}</div>
                    <div class="label">Active Units</div>
                </div>
                <div class="stat-card">
                    <div class="icon">📅</div>
                    <div class="value">{{ $stats['total_bookings'] ?? 0 }}</div>
                    <div class="label">Total Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="icon">⏳</div>
                    <div class="value">{{ $stats['pending_bookings'] ?? 0 }}</div>
                    <div class="label">Pending Requests</div>
                </div>
            </div>
            
            <!-- Revenue Overview -->
            <div class="revenue-grid animate delay-2">
                <div class="revenue-card">
                    <div class="icon">💰</div>
                    <div class="value">₱{{ number_format($stats['total_earnings'] ?? 0, 0, '.', ',') }}</div>
                    <div class="label">Total Earnings</div>
                </div>
                <div class="revenue-card">
                    <div class="icon">📈</div>
                    <div class="value">{{ $stats['confirmed_bookings'] ?? 0 }}</div>
                    <div class="label">Confirmed Bookings</div>
                </div>
                <div class="revenue-card">
                    <div class="icon">⭐</div>
                    <div class="value">{{ number_format($properties->avg('rating') ?? 0, 1) }}</div>
                    <div class="label">Avg Rating</div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions animate delay-2">
                <a href="{{ route('owner.accommodations.create') }}" class="quick-action-card">
                    <div class="icon">➕</div>
                    <h4>Add New Unit</h4>
                    <p>List a new accommodation</p>
                </a>
                <a href="{{ route('owner.accommodations.index') }}" class="quick-action-card">
                    <div class="icon">📋</div>
                    <h4>Manage Units</h4>
                    <p>Edit property details</p>
                </a>
                <a href="{{ route('bookings.index') }}" class="quick-action-card">
                    <div class="icon">📅</div>
                    <h4>Booking Requests</h4>
                    <p>Review pending bookings</p>
                </a>
                <a href="{{ route('messages.index') }}" class="quick-action-card">
                    <div class="icon">💬</div>
                    <h4>Messages</h4>
                    <p>Respond to inquiries</p>
                </a>
            </div>
            
            <!-- My Units Table -->
            <div class="dashboard-card animate delay-3">
                <h3>📋 My Units</h3>
                @if(isset($properties) && count($properties) > 0)
                    <table class="property-table">
                        <thead>
                            <tr>
                                <th>Property</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Bookings</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($properties as $property)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 15px;">
                                            @if($property->primary_image)
                                                <img src="{{ asset('storage/' . $property->primary_image) }}" alt="{{ $property->name }}" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                                            @else
                                                <img src="/COMMUNAL.jpg" alt="{{ $property->name }}" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <div style="font-weight: 600; color: var(--gray-800);">{{ $property->name }}</div>
                                                <div style="font-size: 0.85rem; color: var(--gray-500);">{{ $property->address }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ ucfirst(str_replace('-', ' ', $property->type)) }}</td>
                                    <td>₱{{ number_format($property->price_per_night, 0, '.', ',') }}</td>
                                    <td>{{ $property->bookings_count ?? 0 }}</td>
                                    <td>
                                        @if($property->rating > 0)
                                            ⭐ {{ number_format($property->rating, 1) }}
                                        @else
                                            <span style="color: var(--gray-400);">No ratings</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $property->is_available ? 'active' : 'inactive' }}">
                                            {{ $property->is_available ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('owner.accommodations.edit', $property) }}" style="padding: 8px 15px; background: var(--green-soft); color: var(--green-dark); border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 500;">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 40px; color: var(--gray-400);">
                        <p>No properties yet. Add your first property!</p>
                        <a href="{{ route('owner.accommodations.create') }}" style="display: inline-block; margin-top: 15px; padding: 12px 25px; background: var(--green-primary); color: white; border-radius: 8px; text-decoration: none;">Add Property</a>
                    </div>
                @endif
            </div>
            
            <!-- Recent Booking Requests -->
            <div class="dashboard-card animate delay-4">
                <h3>📅 Recent Booking Requests</h3>
                @if(isset($recent_bookings) && count($recent_bookings) > 0)
                    <table class="property-table">
                        <thead>
                            <tr>
                                <th>Guest</th>
                                <th>Property</th>
                                <th>Check-In</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_bookings as $booking)
                                <tr>
                                    <td>{{ $booking->client->name ?? 'N/A' }}</td>
                                    <td>{{ $booking->accommodation->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</td>
                                    <td>₱{{ number_format($booking->total_price, 0, '.', ',') }}</td>
                                    <td><span class="status-badge {{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 40px; color: var(--gray-400);">
                        <p>No booking requests yet</p>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
