<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Impasugong Accommodations</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
            padding: 0 40px;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(27, 94, 32, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-logo img { width: 45px; height: 45px; border-radius: 0; border: none; object-fit: contain; }
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
        .bookings-grid { display: grid; gap: 20px; align-items: start; }
        
        .booking-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
            overflow: hidden;
            transition: all 0.3s;
            align-self: start;
            height: fit-content;
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
        .btn-danger { background: #EF4444; color: white; }
        .btn-danger:hover { background: #DC2626; }
        .btn-secondary { background: var(--gray-200); color: var(--gray-700); }
        .btn-outline { background: transparent; border: 2px solid var(--green-primary); color: var(--green-primary); }
        .btn-outline:hover { background: var(--green-primary); color: white; }
        .toggle-actions-btn {
            width: 100%;
            padding: 9px 12px;
            border-radius: 8px;
            border: 1px solid var(--green-primary);
            background: var(--white);
            color: var(--green-dark);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .toggle-actions-btn:hover {
            background: var(--green-soft);
        }

        /* Owner compact grid view (4 cards per row) */
        .owner-bookings-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }
        .owner-bookings-grid .booking-header {
            padding: 12px 14px;
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }
        .owner-bookings-grid .booking-body {
            padding: 14px;
            flex-direction: column;
            gap: 12px;
        }
        .owner-bookings-grid .property-image {
            width: 100%;
            height: 120px;
            border-radius: 10px;
        }
        .owner-bookings-grid .property-name {
            font-size: 1rem;
            line-height: 1.3;
            margin-bottom: 6px;
        }
        .owner-bookings-grid .property-location {
            font-size: 0.8rem;
            margin-bottom: 10px;
        }
        .owner-bookings-grid .booking-info {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            margin-bottom: 0;
        }
        .owner-bookings-grid .info-item {
            padding: 8px 10px;
            border-radius: 8px;
        }
        .owner-bookings-grid .info-label {
            font-size: 0.65rem;
        }
        .owner-bookings-grid .info-value {
            font-size: 0.85rem;
        }
        .owner-bookings-grid .booking-footer {
            padding: 12px 14px;
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }
        .owner-bookings-grid .action-btns {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }
        .owner-bookings-grid .owner-actions-panel {
            display: none;
        }
        .owner-bookings-grid .owner-actions-panel.open {
            display: grid;
        }
        .owner-bookings-grid .btn {
            width: 100%;
            justify-content: center;
            padding: 9px 12px;
            font-size: 0.85rem;
        }

        @media (max-width: 1400px) {
            .owner-bookings-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 1100px) {
            .owner-bookings-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        
        /* Empty State */
        .empty-state { text-align: center; padding: 60px 20px; background: var(--white); border-radius: 16px; }
        .empty-icon { font-size: 4rem; margin-bottom: 20px; }
        .empty-state h3 { font-size: 1.5rem; color: var(--gray-700); margin-bottom: 10px; }
        .empty-state p { color: var(--gray-500); margin-bottom: 25px; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar { padding: 0 20px; height: 60px; }
            .nav-links { display: none; }
            .main-content { padding: 100px 20px 40px; }
            .booking-body { flex-direction: column; }
            .property-image { width: 100%; height: 200px; }
            .booking-footer { flex-direction: column; gap: 15px; align-items: stretch; }
            .action-btns { justify-content: center; }
            .owner-bookings-grid { grid-template-columns: 1fr; }
        }

        @if(auth()->user()?->isOwner())
            @include('owner.partials.top-navbar-styles')
        @elseif(auth()->user()?->isClient())
            @include('client.partials.top-navbar-styles')
        @endif
    </style>
</head>
<body class="{{ auth()->user()?->isOwner() ? 'owner-nav-page' : '' }}">
    <!-- Navigation -->
    @if(auth()->user()?->isOwner())
        @include('owner.partials.top-navbar')
    @else
    @if(auth()->user()?->isClient())
        @include('client.partials.top-navbar', ['active' => 'bookings'])
    @else
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="nav-logo">
            <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo">
            <span>Impasugong</span>
        </a>
        
        <ul class="nav-links">
            @auth
                @if(Auth::user()->isAdmin())
                    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                @else
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Browse</a></li>
                @endif
            @endauth
            <li><a href="{{ route(Auth::check() && Auth::user()->isOwner() && \Illuminate\Support\Facades\Route::has('owner.accommodations.index') ? 'owner.accommodations.index' : (\Illuminate\Support\Facades\Route::has('accommodations.index') ? 'accommodations.index' : 'dashboard')) }}" class="{{ request()->routeIs('accommodations.*') || request()->routeIs('owner.accommodations.*') ? 'active' : '' }}">Browse</a></li>
            <li><a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}">My Bookings</a></li>
            <li><a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">Messages</a></li>
            <li><a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">Settings</a></li>
        </ul>
        
        <div class="nav-actions">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-btn primary">Logout</button>
            </form>
        </div>
    </nav>
    @endif
    @endif
    
    <!-- Main Content -->
    <main class="main-content {{ auth()->user()?->isOwner() ? 'with-owner-nav' : '' }}">
        @php
            $isOwner = Auth::check() && Auth::user()->isOwner();
            $bookingsIndexRoute = Auth::check() && Auth::user()->isOwner() ? 'owner.bookings.index' : 'bookings.index';
            $bookingsShowRoute = Auth::check() && Auth::user()->isOwner() ? 'owner.bookings.show' : 'bookings.show';
        @endphp

        <div class="page-header">
            <h1>My Bookings</h1>
            <p>View and manage your accommodation bookings</p>
        </div>
        
        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="{{ route($bookingsIndexRoute) }}" class="filter-tab {{ !request('status') ? 'active' : '' }}">All</a>
            <a href="{{ route($bookingsIndexRoute, ['status' => 'pending']) }}" class="filter-tab {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
            <a href="{{ route($bookingsIndexRoute, ['status' => 'confirmed']) }}" class="filter-tab {{ request('status') == 'confirmed' ? 'active' : '' }}">Confirmed</a>
            <a href="{{ route($bookingsIndexRoute, ['status' => 'completed']) }}" class="filter-tab {{ request('status') == 'completed' ? 'active' : '' }}">Completed</a>
            <a href="{{ route($bookingsIndexRoute, ['status' => 'cancelled']) }}" class="filter-tab {{ request('status') == 'cancelled' ? 'active' : '' }}">Cancelled</a>
        </div>
        
        <!-- Bookings List -->
        @if(isset($bookings) && count($bookings) > 0)
            <div class="bookings-grid {{ $isOwner ? 'owner-bookings-grid' : '' }}">
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
                            @if($isOwner)
                                <button type="button" class="toggle-actions-btn" data-target="owner-actions-{{ $booking->id }}" aria-expanded="false">
                                    Show Actions
                                </button>
                                <div class="action-btns owner-actions-panel" id="owner-actions-{{ $booking->id }}">
                                    <a href="{{ route($bookingsShowRoute, $booking) }}" class="btn btn-primary">View Details</a>
                                    @if($booking->status === 'pending')
                                        <form action="{{ route('owner.bookings.update-status', $booking) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-primary">Approve</button>
                                        </form>
                                        <form action="{{ route('owner.bookings.update-status', $booking) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Decline this booking request?')">Decline</button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <div class="action-btns">
                                    <a href="{{ route($bookingsShowRoute, $booking) }}" class="btn btn-primary">View Details</a>
                                    @if(Auth::check() && Auth::user()->isClient() && ($booking->status == 'pending' || $booking->status == 'confirmed'))
                                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-outline" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</button>
                                        </form>
                                    @endif
                                </div>
                            @endif
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
                <a href="{{ route(Auth::check() && Auth::user()->isOwner() && \Illuminate\Support\Facades\Route::has('owner.accommodations.index') ? 'owner.accommodations.index' : (\Illuminate\Support\Facades\Route::has('accommodations.index') ? 'accommodations.index' : 'dashboard')) }}" class="btn btn-primary">Browse Properties</a>
            </div>
        @endif
    </main>

    <script>
        document.querySelectorAll('.toggle-actions-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var panel = document.getElementById(button.dataset.target);
                if (!panel) return;

                var willOpen = !panel.classList.contains('open');

                document.querySelectorAll('.owner-actions-panel.open').forEach(function(openPanel) {
                    openPanel.classList.remove('open');
                });

                document.querySelectorAll('.toggle-actions-btn').forEach(function(btn) {
                    btn.textContent = 'Show Actions';
                    btn.setAttribute('aria-expanded', 'false');
                });

                if (willOpen) {
                    panel.classList.add('open');
                    button.textContent = 'Hide Actions';
                    button.setAttribute('aria-expanded', 'true');
                }
            });
        });
    </script>
</body>
</html>

