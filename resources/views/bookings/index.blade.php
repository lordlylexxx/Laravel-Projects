<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Impasugong Accommodations</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-50: #F9FAFB; --gray-100: #F3F4F6; --gray-200: #E5E7EB;
            --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151;
            --gray-800: #1F2937;
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
        .nav-btn.secondary { background: var(--green-soft); color: var(--green-dark); }
        
        /* Main Layout */
        .main-content { padding-top: 100px; padding-bottom: 40px; max-width: 1400px; margin: 0 auto; padding-left: 40px; padding-right: 40px; }
        
        /* Page Header */
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 2rem; color: var(--green-dark); margin-bottom: 5px; }
        .page-header p { color: var(--gray-500); }
        
        /* Filter Tabs */
        .filter-tabs { display: flex; gap: 10px; margin-bottom: 25px; flex-wrap: wrap; }
        .filter-tab { padding: 10px 20px; border-radius: 8px; border: none; background: var(--white); color: var(--gray-600); cursor: pointer; font-weight: 500; transition: all 0.3s; text-decoration: none; }
        .filter-tab:hover { background: var(--green-soft); }
        .filter-tab.active { background: var(--green-primary); color: white; }
        
        /* Bookings Grid */
        .bookings-grid { display: grid; gap: 20px; }
        
        .booking-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .booking-card:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(27, 94, 32, 0.15); }
        
        .booking-header { padding: 20px 25px; border-bottom: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; }
        .booking-id { font-size: 0.85rem; color: var(--gray-500); }
        .booking-date { font-size: 0.9rem; color: var(--gray-600); }
        
        .booking-body { padding: 25px; display: flex; gap: 25px; }
        
        .property-image { width: 180px; height: 130px; border-radius: 12px; object-fit: cover; flex-shrink: 0; }
        
        .booking-details { flex: 1; }
        .property-name { font-size: 1.3rem; color: var(--green-dark); margin-bottom: 8px; font-weight: 600; }
        .property-location { display: flex; align-items: center; gap: 6px; color: var(--gray-500); font-size: 0.9rem; margin-bottom: 15px; }
        
        .booking-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .info-item { background: var(--cream); padding: 12px 15px; border-radius: 10px; }
        .info-label { font-size: 0.75rem; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
        .info-value { font-weight: 600; color: var(--gray-800); }
        
        .booking-footer { padding: 20px 25px; background: var(--cream); display: flex; justify-content: space-between; align-items: center; }
        
        /* Status Badges */
        .status-badge { display: inline-block; padding: 6px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        .status-badge.pending { background: #FFF3E0; color: #E65100; }
        .status-badge.confirmed { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.cancelled { background: #FFEBEE; color: #C62828; }
        .status-badge.completed { background: #E3F2FD; color: #1565C0; }
        .status-badge.paid { background: #E8F5E9; color: #2E7D32; }
        
        /* Action Buttons */
        .action-btns { display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: var(--green-primary); color: white; }
        .btn-primary:hover { background: var(--green-dark); }
        .btn-secondary { background: var(--gray-200); color: var(--gray-700); }
        .btn-outline { background: transparent; border: 2px solid var(--green-primary); color: var(--green-primary); }
        .btn-outline:hover { background: var(--green-primary); color: white; }
        
        /* Empty State */
        .empty-state { text-align: center; padding: 60px 20px; background: var(--white); border-radius: 16px; }
        .empty-icon { font-size: 4rem; margin-bottom: 20px; }
        .empty-state h3 { font-size: 1.5rem; color: var(--gray-700); margin-bottom: 10px; }
        .empty-state p { color: var(--gray-500); margin-bottom: 25px; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .nav-links { display: none; }
            .main-content { padding: 100px 20px 40px; }
            .booking-body { flex-direction: column; }
            .property-image { width: 100%; height: 200px; }
            .booking-footer { flex-direction: column; gap: 15px; align-items: stretch; }
            .action-btns { justify-content: center; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="nav-logo">
            <img src="/1.jpg" alt="Logo">
            <span>Impasugong</span>
        </a>
        
        <ul class="nav-links">
            @auth
                @if(Auth::user()->isAdmin())
                    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                @elseif(Auth::user()->isOwner())
                    <li><a href="{{ route('owner.dashboard') }}" class="{{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                @else
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Browse</a></li>
                @endif
            @endauth
            <li><a href="{{ route('accommodations.index') }}" class="{{ request()->routeIs('accommodations.*') ? 'active' : '' }}">Properties</a></li>
            <li><a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}">My Bookings</a></li>
            <li><a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">Messages</a></li>
        </ul>
        
        <div class="nav-actions">
            <a href="{{ route('profile.edit') }}" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: var(--green-soft); color: var(--green-dark); text-decoration: none; transition: all 0.3s;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-btn primary">Logout</button>
            </form>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h1>My Bookings</h1>
            <p>View and manage your accommodation bookings</p>
        </div>
        
        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="{{ route('bookings.index') }}" class="filter-tab {{ !request('status') ? 'active' : '' }}">All</a>
            <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="filter-tab {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
            <a href="{{ route('bookings.index', ['status' => 'confirmed']) }}" class="filter-tab {{ request('status') == 'confirmed' ? 'active' : '' }}">Confirmed</a>
            <a href="{{ route('bookings.index', ['status' => 'completed']) }}" class="filter-tab {{ request('status') == 'completed' ? 'active' : '' }}">Completed</a>
            <a href="{{ route('bookings.index', ['status' => 'cancelled']) }}" class="filter-tab {{ request('status') == 'cancelled' ? 'active' : '' }}">Cancelled</a>
        </div>
        
        <!-- Bookings List -->
        @if(isset($bookings) && count($bookings) > 0)
            <div class="bookings-grid">
                @foreach($bookings as $booking)
                    <div class="booking-card">
                        <div class="booking-header">
                            <span class="booking-id">Booking #{{ $booking->id }}</span>
                            <span class="booking-date">{{ $booking->created_at->format('M d, Y') }}</span>
                        </div>
                        
                        <div class="booking-body">
                            @if($booking->accommodation && $booking->accommodation->primary_image)
                                <img src="{{ asset('storage/' . $booking->accommodation->primary_image) }}" alt="{{ $booking->accommodation->name }}" class="property-image">
                            @else
                                <img src="/COMMUNAL.jpg" alt="Property" class="property-image">
                            @endif
                            
                            <div class="booking-details">
                                <h3 class="property-name">{{ $booking->accommodation->name ?? 'N/A' }}</h3>
                                <div class="property-location">
                                    📍 {{ $booking->accommodation->address ?? 'Impasugong' }}
                                </div>
                                
                                <div class="booking-info">
                                    <div class="info-item">
                                        <div class="info-label">Check-In</div>
                                        <div class="info-value">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Check-Out</div>
                                        <div class="info-value">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Guests</div>
                                        <div class="info-value">{{ $booking->number_of_guests ?? 1 }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Total</div>
                                        <div class="info-value">₱{{ number_format($booking->total_price, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="booking-footer">
                            <span class="status-badge {{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                            <div class="action-btns">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-primary">View Details</a>
                                @if($booking->status == 'pending' || $booking->status == 'confirmed')
                                    <a href="{{ route('bookings.cancel', $booking) }}" class="btn btn-outline" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if(isset($bookings) && method_exists($bookings, 'links'))
                <div style="margin-top: 30px;">
                    {{ $bookings->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <h3>No Bookings Found</h3>
                <p>You haven't made any bookings yet. Start exploring accommodations!</p>
                <a href="{{ route('accommodations.index') }}" class="btn btn-primary">Browse Properties</a>
            </div>
        @endif
    </main>
</body>
</html>

