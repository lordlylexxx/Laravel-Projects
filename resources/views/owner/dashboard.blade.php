<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - VerdeVistas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
        .user-info {
            text-align: left;
        }
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
            background: linear-gradient(135deg, var(--orange-500), #EA580C); 
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
        
        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(27, 94, 32, 0.08);
            text-align: center;
            transition: all 0.3s;
            border: 1px solid var(--green-soft);
        }
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 15px 40px rgba(27, 94, 32, 0.15);
        }
        .stat-icon { 
            width: 60px; 
            height: 60px; 
            border-radius: 14px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.5rem;
            margin: 0 auto 15px;
        }
        .stat-icon.green { background: linear-gradient(135deg, var(--green-soft), var(--green-pale)); color: var(--green-dark); }
        .stat-icon.blue { background: linear-gradient(135deg, #DBEAFE, #BFDBFE); color: var(--blue-500); }
        .stat-icon.orange { background: linear-gradient(135deg, #FEF3C7, #FDE68A); color: var(--orange-500); }
        .stat-icon.purple { background: linear-gradient(135deg, #EDE9FE, #DDD6FE); color: var(--purple-500); }
        
        .stat-card .value { font-size: 1.8rem; font-weight: bold; color: var(--green-dark); margin-bottom: 5px; }
        .stat-card .label { color: var(--gray-500); font-size: 0.85rem; }
        
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
        
        /* Quick Actions */
        .quick-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .quick-action-card {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(27, 94, 32, 0.08);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
            text-decoration: none;
            display: block;
            border: 1px solid var(--green-soft);
        }
        .quick-action-card:hover { 
            border-color: var(--green-primary); 
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(27, 94, 32, 0.15);
        }
        .quick-action-card .icon { 
            font-size: 2.5rem; 
            color: var(--green-primary); 
            margin-bottom: 15px; 
        }
        .quick-action-card h4 { color: var(--green-dark); margin-bottom: 8px; font-size: 1rem; }
        .quick-action-card p { color: var(--gray-500); font-size: 0.85rem; }
        
        /* Revenue Cards */
        .revenue-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .revenue-card {
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            color: white;
            padding: 25px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(27, 94, 32, 0.3);
        }
        .revenue-card .icon { font-size: 2rem; margin-bottom: 10px; opacity: 0.9; }
        .revenue-card .value { font-size: 2rem; font-weight: bold; margin-bottom: 5px; }
        .revenue-card .label { font-size: 0.9rem; opacity: 0.9; }
        
        /* Table */
        .property-table { width: 100%; border-collapse: collapse; }
        .property-table th, .property-table td { padding: 15px; text-align: left; border-bottom: 1px solid var(--gray-200); }
        .property-table th { font-weight: 600; color: var(--gray-600); font-size: 0.8rem; text-transform: uppercase; background: var(--cream); }
        .property-table tr:hover { background: var(--green-white); }
        
        /* Status Badges */
        .status-badge { display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.active { background: linear-gradient(135deg, var(--green-soft), var(--green-pale)); color: var(--green-dark); }
        .status-badge.inactive { background: var(--gray-200); color: var(--gray-600); }
        .status-badge.pending { background: linear-gradient(135deg, #FEF3C7, #FDE68A); color: #B45309; }
        .status-badge.confirmed { background: linear-gradient(135deg, #DBEAFE, #BFDBFE); color: #1D4ED8; }
        .status-badge.cancelled { background: linear-gradient(135deg, #FEE2E2, #FECACA); color: #DC2626; }
        
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
        }
        .settings-icon:hover {
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: white;
            transform: rotate(90deg);
        }
        
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
        .delay-4 { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="{{ route('owner.dashboard') }}" class="nav-logo">
            <img src="/SYSTEMLOGO.png" alt="VerdeVistas Logo">
            <span>VerdeVistas</span>
        </a>
        
        <ul class="nav-links">
            <li><a href="{{ route('owner.dashboard') }}" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="{{ route('owner.accommodations.index') }}"><i class="fas fa-building"></i> My Units</a></li>
            <li><a href="{{ route('bookings.index') }}"><i class="fas fa-calendar-check"></i> Bookings</a></li>
            <li><a href="{{ route('messages.index') }}"><i class="fas fa-envelope"></i> Messages</a></li>
        </ul>
        
        <div class="nav-actions">
            <!-- User Display -->
            <div class="user-display">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/avatars/' . Auth::user()->avatar . '?v=' . time()) }}" alt="{{ Auth::user()->name }}" class="user-avatar" style="object-fit: cover;">
                @else
                    <div class="user-avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
                @endif
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
                </div>
            </div>
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
                <h3 class="sidebar-title">Management</h3>
                <ul class="sidebar-menu">
                    <li><a href="{{ route('owner.dashboard') }}" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="{{ route('owner.accommodations.index') }}"><i class="fas fa-building"></i> My Units</a></li>
                    <li><a href="{{ route('bookings.index') }}"><i class="fas fa-calendar-alt"></i> Bookings
                        @if(($stats['pending_bookings'] ?? 0) > 0)
                            <span class="badge">{{ $stats['pending_bookings'] }}</span>
                        @endif
                    </a></li>
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
                <h1><i class="fas fa-home" style="color: var(--green-primary); margin-right: 12px;"></i>Unit Management Dashboard</h1>
                <p>Monitor your properties and booking performance</p>
            </div>
            
            <!-- Quick Stats -->
            <div class="stats-grid animate delay-1">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-building"></i></div>
                    <div class="value">{{ $stats['total_properties'] ?? 0 }}</div>
                    <div class="label">Total Units</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-check-circle"></i></div>
                    <div class="value">{{ $stats['active_properties'] ?? 0 }}</div>
                    <div class="label">Active Units</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-calendar-check"></i></div>
                    <div class="value">{{ $stats['total_bookings'] ?? 0 }}</div>
                    <div class="label">Total Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-clock"></i></div>
                    <div class="value">{{ $stats['pending_bookings'] ?? 0 }}</div>
                    <div class="label">Pending Requests</div>
                </div>
            </div>
            
            <!-- Revenue Overview -->
            <div class="revenue-grid animate delay-2">
                <div class="revenue-card">
                    <div class="icon"><i class="fas fa-peso-sign"></i></div>
                    <div class="value">₱{{ number_format($stats['total_earnings'] ?? 0, 0, '.', ',') }}</div>
                    <div class="label">Total Earnings</div>
                </div>
                <div class="revenue-card">
                    <div class="icon"><i class="fas fa-check-double"></i></div>
                    <div class="value">{{ $stats['confirmed_bookings'] ?? 0 }}</div>
                    <div class="label">Confirmed Bookings</div>
                </div>
                <div class="revenue-card">
                    <div class="icon"><i class="fas fa-star"></i></div>
                    <div class="value">{{ number_format($properties->avg('rating') ?? 0, 1) }}</div>
                    <div class="label">Avg Rating</div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions animate delay-2">
                <a href="{{ route('owner.accommodations.create') }}" class="quick-action-card">
                    <div class="icon"><i class="fas fa-plus-circle"></i></div>
                    <h4>Add New Unit</h4>
                    <p>List a new accommodation</p>
                </a>
                <a href="{{ route('owner.accommodations.index') }}" class="quick-action-card">
                    <div class="icon"><i class="fas fa-edit"></i></div>
                    <h4>Manage Units</h4>
                    <p>Edit property details</p>
                </a>
                <a href="{{ route('bookings.index') }}" class="quick-action-card">
                    <div class="icon"><i class="fas fa-tasks"></i></div>
                    <h4>Booking Requests</h4>
                    <p>Review pending bookings</p>
                </a>
                <a href="{{ route('messages.index') }}" class="quick-action-card">
                    <div class="icon"><i class="fas fa-reply"></i></div>
                    <h4>Messages</h4>
                    <p>Respond to inquiries</p>
                </a>
            </div>
            
            <!-- My Units Table -->
            <div class="dashboard-card animate delay-3">
                <h3><i class="fas fa-list icon"></i>My Units</h3>
                @if(isset($properties) && count($properties) > 0)
                    <table class="property-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-building"></i> Property</th>
                                <th><i class="fas fa-tag"></i> Type</th>
                                <th><i class="fas fa-peso-sign"></i> Price</th>
                                <th><i class="fas fa-ticket-alt"></i> Bookings</th>
                                <th><i class="fas fa-star"></i> Rating</th>
                                <th><i class="fas fa-toggle-on"></i> Status</th>
                                <th><i class="fas fa-cog"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($properties as $property)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 15px;">
                                            @if($property->primary_image)
                                                <img src="{{ asset('storage/' . $property->primary_image) }}" alt="{{ $property->name }}" style="width: 50px; height: 50px; border-radius: 10px; object-fit: cover;">
                                            @else
                                                <img src="/COMMUNAL.jpg" alt="{{ $property->name }}" style="width: 50px; height: 50px; border-radius: 10px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <div style="font-weight: 600; color: var(--gray-800);">{{ $property->name }}</div>
                                                <div style="font-size: 0.85rem; color: var(--gray-500);">{{ $property->address }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span style="background: var(--green-soft); padding: 4px 10px; border-radius: 6px; font-size: 0.85rem;">{{ ucfirst(str_replace('-', ' ', $property->type)) }}</span></td>
                                    <td><strong>₱{{ number_format($property->price_per_night, 0, '.', ',') }}</strong></td>
                                    <td>{{ $property->bookings_count ?? 0 }}</td>
                                    <td>
                                        @if($property->rating > 0)
                                            <span style="color: var(--amber-500);"><i class="fas fa-star"></i> {{ number_format($property->rating, 1) }}</span>
                                        @else
                                            <span style="color: var(--gray-400);"><i class="fas fa-minus"></i> No ratings</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $property->is_available ? 'active' : 'inactive' }}">
                                            <i class="fas {{ $property->is_available ? 'fa-check' : 'fa-times' }}"></i>
                                            {{ $property->is_available ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('owner.accommodations.edit', $property) }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: var(--green-soft); color: var(--green-dark); border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: all 0.3s;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 40px; color: var(--gray-400);">
                        <i class="fas fa-home" style="font-size: 3rem; margin-bottom: 15px; color: var(--gray-300);"></i>
                        <p>No properties yet. Add your first property!</p>
                        <a href="{{ route('owner.accommodations.create') }}" style="display: inline-block; margin-top: 15px; padding: 12px 25px; background: var(--green-primary); color: white; border-radius: 8px; text-decoration: none;">
                            <i class="fas fa-plus"></i> Add Property
                        </a>
                    </div>
                @endif
            </div>
            
            <!-- Recent Booking Requests -->
            <div class="dashboard-card animate delay-4">
                <h3><i class="fas fa-calendar-check icon"></i>Recent Booking Requests</h3>
                @if(isset($recent_bookings) && count($recent_bookings) > 0)
                    <table class="property-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user"></i> Guest</th>
                                <th><i class="fas fa-building"></i> Property</th>
                                <th><i class="fas fa-calendar-alt"></i> Check-In</th>
                                <th><i class="fas fa-money-bill-wave"></i> Amount</th>
                                <th><i class="fas fa-info-circle"></i> Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_bookings as $booking)
                                <tr>
                                    <td>{{ $booking->client->name ?? 'N/A' }}</td>
                                    <td>{{ $booking->accommodation->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</td>
                                    <td><strong>₱{{ number_format($booking->total_price, 0, '.', ',') }}</strong></td>
                                    <td><span class="status-badge {{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 40px; color: var(--gray-400);">
                        <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 15px; color: var(--gray-300);"></i>
                        <p>No booking requests yet</p>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
