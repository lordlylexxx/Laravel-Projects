<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Impasugong Accommodations</title>
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
        .notification-btn { position: relative; background: none; border: none; cursor: pointer; font-size: 1.4rem; color: var(--green-primary); padding: 8px; }
        .notification-badge { position: absolute; top: 0; right: 0; background: var(--red-500); color: white; font-size: 0.65rem; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .user-menu { display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 5px 12px; border-radius: 10px; transition: all 0.3s; }
        .user-menu:hover { background: var(--green-soft); }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--green-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        .user-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
        .user-info { text-align: left; }
        .user-name { font-weight: 600; color: var(--gray-800); font-size: 0.9rem; }
        .user-role { font-size: 0.75rem; color: var(--gray-500); }
        
        /* Main Container */
        .main-container { padding-top: 90px; max-width: 1400px; margin: 0 auto; padding: 90px 40px 40px; }
        
        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }
        
        .welcome-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        
        .welcome-content { position: relative; z-index: 1; }
        .welcome-content h1 { font-size: 2rem; margin-bottom: 8px; }
        .welcome-content p { opacity: 0.9; font-size: 1rem; margin-bottom: 20px; }
        .quick-actions { display: flex; gap: 15px; flex-wrap: wrap; }
        .action-btn { padding: 12px 24px; border-radius: 10px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .action-btn.primary { background: var(--white); color: var(--green-dark); }
        .action-btn.primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); }
        .action-btn.secondary { background: rgba(255, 255, 255, 0.15); color: var(--white); border: 2px solid rgba(255, 255, 255, 0.3); }
        .action-btn.secondary:hover { background: rgba(255, 255, 255, 0.25); }
        
        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(27, 94, 32, 0.15); }
        .stat-icon { width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; }
        .stat-icon.green { background: var(--green-soft); }
        .stat-icon.blue { background: #E3F2FD; }
        .stat-icon.orange { background: #FFF3E0; }
        .stat-icon.purple { background: #F3E5F5; }
        .stat-icon.red { background: #FFEBEE; }
        .stat-info h3 { font-size: 2rem; font-weight: 700; color: var(--green-dark); margin-bottom: 3px; }
        .stat-info p { color: var(--gray-500); font-size: 0.9rem; }
        
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
        .section-header a { color: var(--green-primary); text-decoration: none; font-size: 0.9rem; font-weight: 500; }
        .section-header a:hover { text-decoration: underline; }
        .section-body { padding: 25px; }
        
        /* Booking Tracker */
        .booking-tracker { display: grid; gap: 20px; }
        .booking-item {
            display: flex;
            gap: 20px;
            padding: 20px;
            background: var(--cream);
            border-radius: 14px;
            align-items: center;
            transition: all 0.3s;
        }
        .booking-item:hover { background: var(--green-soft); }
        .booking-img { width: 120px; height: 90px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
        .booking-details { flex: 1; }
        .booking-details h4 { color: var(--gray-800); font-size: 1.1rem; margin-bottom: 5px; }
        .booking-details p { color: var(--gray-500); font-size: 0.85rem; margin-bottom: 8px; }
        .booking-meta { display: flex; gap: 20px; font-size: 0.85rem; }
        .booking-meta span { display: flex; align-items: center; gap: 5px; color: var(--gray-600); }
        
        /* Status Steps */
        .booking-steps { display: flex; align-items: center; gap: 10px; margin-top: 15px; }
        .step { display: flex; align-items: center; gap: 8px; }
        .step-dot { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; }
        .step-dot.completed { background: var(--green-primary); color: white; }
        .step-dot.current { background: var(--orange-500); color: white; }
        .step-dot.pending { background: var(--gray-300); color: var(--gray-500); }
        .step-line { width: 40px; height: 3px; background: var(--gray-300); border-radius: 2px; }
        .step-line.completed { background: var(--green-primary); }
        
        /* Payment Status */
        .payment-status { display: flex; align-items: center; gap: 10px; }
        .payment-badge { padding: 5px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; }
        .payment-badge.paid { background: #D1FAE5; color: #065F46; }
        .payment-badge.pending { background: #FEF3C7; color: #92400E; }
        .payment-badge.unpaid { background: #FEE2E2; color: #991B1B; }
        
        /* Profile Card */
        .profile-card { display: flex; gap: 25px; align-items: center; }
        .profile-avatar { width: 100px; height: 100px; border-radius: 50%; background: var(--green-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; flex-shrink: 0; overflow: hidden; }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-info h3 { font-size: 1.3rem; color: var(--gray-800); margin-bottom: 5px; }
        .profile-info p { color: var(--gray-500); font-size: 0.9rem; margin-bottom: 8px; }
        .role-badge { display: inline-block; padding: 5px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        .role-badge.client { background: var(--green-soft); color: var(--green-dark); }
        
        /* Saved Properties */
        .saved-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .property-card { background: var(--cream); border-radius: 14px; overflow: hidden; transition: all 0.3s; }
        .property-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(27, 94, 32, 0.15); }
        .property-img { width: 100%; height: 160px; object-fit: cover; }
        .property-content { padding: 20px; }
        .property-content h4 { color: var(--gray-800); font-size: 1rem; margin-bottom: 8px; }
        .property-content p { color: var(--gray-500); font-size: 0.85rem; margin-bottom: 10px; }
        .property-price { font-size: 1.3rem; font-weight: 700; color: var(--green-primary); }
        .property-price span { font-size: 0.85rem; font-weight: 400; color: var(--gray-500); }
        
        /* Notifications */
        .notification-list { display: flex; flex-direction: column; }
        .notification-item { display: flex; gap: 15px; padding: 15px; border-bottom: 1px solid var(--gray-200); transition: all 0.3s; }
        .notification-item:hover { background: var(--cream); }
        .notification-item:last-child { border-bottom: none; }
        .notification-icon { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
        .notification-icon.info { background: #E3F2FD; }
        .notification-icon.success { background: #D1FAE5; }
        .notification-icon.warning { background: #FEF3C7; }
        .notification-content { flex: 1; }
        .notification-content h4 { font-size: 0.95rem; color: var(--gray-800); margin-bottom: 3px; }
        .notification-content p { font-size: 0.85rem; color: var(--gray-500); }
        .notification-time { font-size: 0.75rem; color: var(--gray-400); }
        
        /* Buttons */
        .btn { padding: 10px 20px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: var(--white); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46, 125, 50, 0.3); }
        .btn-secondary { background: var(--green-soft); color: var(--green-dark); }
        .btn-secondary:hover { background: var(--green-pale); }
        .btn-sm { padding: 8px 16px; font-size: 0.85rem; }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar { display: none; }
        }
        
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .nav-links { display: none; }
            .main-container { padding: 80px 20px 30px; }
            .welcome-section { padding: 30px 20px; }
            .profile-card { flex-direction: column; text-align: center; }
            .booking-item { flex-direction: column; }
            .booking-img { width: 100%; height: 150px; }
        }
        
        /* Animations */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        .animate { animation: fadeInUp 0.6s ease forwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        
        /* Counter Animation */
        .counter { font-size: 2rem; font-weight: 700; color: var(--green-dark); }
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
            <li><a href="{{ route('accommodations.index') }}" class="active">Browse</a></li>
            <li><a href="{{ route('bookings.index') }}">My Bookings</a></li>
            <li><a href="{{ route('messages.index') }}">Messages</a></li>
        </ul>
        
        <div class="nav-actions">
            <a href="{{ route('messages.index') }}" class="notification-btn">
                🔔
                <span class="notification-badge">3</span>
            </a>
            
            <div class="user-menu" onclick="event.preventDefault(); document.getElementById('profile-form').submit();">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="user-avatar">
                @else
                    <div class="user-avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
                @endif
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">Client</div>
                </div>
            </div>
            <form id="profile-form" action="{{ route('profile.edit') }}" method="GET" style="display: none;"></form>
        </div>
    </nav>
    
    <!-- Main Container -->
    <div class="main-container">
        <!-- Welcome Section -->
        <div class="welcome-section animate">
            <div class="welcome-content">
                <h1>Welcome back, {{ Auth::user()->name }}! 👋</h1>
                <p>Find your perfect stay in Impasugong. Browse unique accommodations and plan your next adventure.</p>
                <div class="quick-actions">
                    <a href="{{ route('accommodations.index') }}" class="action-btn primary">
                        🔍 Browse Properties
                    </a>
                    <a href="{{ route('bookings.index') }}" class="action-btn secondary">
                        📅 My Bookings
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card animate delay-1">
                <div class="stat-icon green">📅</div>
                <div class="stat-info">
                    <h3 class="counter">3</h3>
                    <p>Total Bookings</p>
                </div>
            </div>
            <div class="stat-card animate delay-2">
                <div class="stat-icon blue">⏳</div>
                <div class="stat-info">
                    <h3 class="counter">1</h3>
                    <p>Upcoming Stays</p>
                </div>
            </div>
            <div class="stat-card animate delay-3">
                <div class="stat-icon orange">✅</div>
                <div class="stat-info">
                    <h3 class="counter">2</h3>
                    <p>Completed</p>
                </div>
            </div>
            <div class="stat-card animate delay-4">
                <div class="stat-icon purple">💰</div>
                <div class="stat-info">
                    <h3 class="counter">₱{{ number_format(12500, 0, '.', ',') }}</h3>
                    <p>Total Spent</p>
                </div>
            </div>
        </div>
        
        <!-- Main Grid -->
        <div class="stats-grid" style="grid-template-columns: 2fr 1fr;">
            <!-- Upcoming Bookings -->
            <div class="section-card animate delay-2">
                <div class="section-header">
                    <h3>📅 Upcoming Stays</h3>
                    <a href="{{ route('bookings.index') }}">View All →</a>
                </div>
                <div class="section-body">
                    <div class="booking-tracker">
                        <!-- Booking Item 1 -->
                        <div class="booking-item">
                            <img src="/COMMUNAL.jpg" alt="Mountain View Inn" class="booking-img">
                            <div class="booking-details">
                                <h4>Mountain View Inn</h4>
                                <p>📍 Brgy. Poblacion, Impasugong</p>
                                <div class="booking-meta">
                                    <span>📆 Dec 15-18, 2024</span>
                                    <span>👥 2 Guests</span>
                                    <span>₱4,500</span>
                                </div>
                                <div class="booking-steps">
                                    <div class="step">
                                        <div class="step-dot completed">✓</div>
                                        <span>Booking</span>
                                    </div>
                                    <div class="step-line completed"></div>
                                    <div class="step">
                                        <div class="step-dot current">2</div>
                                        <span>Confirm</span>
                                    </div>
                                    <div class="step-line"></div>
                                    <div class="step">
                                        <div class="step-dot pending">3</div>
                                        <span>Stay</span>
                                    </div>
                                </div>
                            </div>
                            <div class="payment-status">
                                <span class="payment-badge pending">Pending</span>
                            </div>
                        </div>
                        
                        <!-- Booking Item 2 -->
                        <div class="booking-item">
                            <img src="/1.jpg" alt="Cozy Garden House" class="booking-img">
                            <div class="booking-details">
                                <h4>Cozy Garden House</h4>
                                <p>📍 Brgy. Kapitan, Impasugong</p>
                                <div class="booking-meta">
                                    <span>📆 Dec 20-25, 2024</span>
                                    <span>👥 4 Guests</span>
                                    <span>₱16,800</span>
                                </div>
                                <div class="booking-steps">
                                    <div class="step">
                                        <div class="step-dot completed">✓</div>
                                        <span>Booking</span>
                                    </div>
                                    <div class="step-line completed"></div>
                                    <div class="step">
                                        <div class="step-dot completed">✓</div>
                                        <span>Confirm</span>
                                    </div>
                                    <div class="step-line completed"></div>
                                    <div class="step">
                                        <div class="step-dot pending">3</div>
                                        <span>Stay</span>
                                    </div>
                                </div>
                            </div>
                            <div class="payment-status">
                                <span class="payment-badge paid">Paid</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column -->
            <div>
                <!-- Profile Summary -->
                <div class="section-card animate delay-3">
                    <div class="section-header">
                        <h3>👤 Profile</h3>
                        <a href="{{ route('profile.edit') }}">Edit →</a>
                    </div>
                    <div class="section-body">
                        <div class="profile-card">
                            @if(Auth::user()->avatar)
                                <div class="profile-avatar">
                                    <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" alt="Avatar">
                                </div>
                            @else
                                <div class="profile-avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
                            @endif
                            <div class="profile-info">
                                <h3>{{ Auth::user()->name }}</h3>
                                <p>{{ Auth::user()->email }}</p>
                                <span class="role-badge client">{{ ucfirst(Auth::user()->role) }}</span>
                            </div>
                        </div>
                        <div style="margin-top: 20px;">
                            <a href="{{ route('profile.edit') }}" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">
                                ⚙️ Account Settings
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Notifications -->
                <div class="section-card animate delay-4">
                    <div class="section-header">
                        <h3>🔔 Notifications</h3>
                    </div>
                    <div class="notification-list">
                        <div class="notification-item">
                            <div class="notification-icon info">ℹ️</div>
                            <div class="notification-content">
                                <h4>Booking Confirmed!</h4>
                                <p>Your booking at Cozy Garden House has been confirmed.</p>
                            </div>
                            <span class="notification-time">2h ago</span>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon success">🎉</div>
                            <div class="notification-content">
                                <h4>Special Offer</h4>
                                <p>Get 10% off on weekend stays this month!</p>
                            </div>
                            <span class="notification-time">1d ago</span>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon warning">⏰</div>
                            <div class="notification-content">
                                <h4>Upcoming Check-in</h4>
                                <p>Your stay at Mountain View Inn is in 5 days.</p>
                            </div>
                            <span class="notification-time">2d ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Saved Properties -->
        <div class="section-card animate delay-4">
            <div class="section-header">
                <h3>❤️ Saved Accommodations</h3>
                <a href="{{ route('accommodations.index') }}">Browse More →</a>
            </div>
            <div class="section-body">
                <div class="saved-grid">
                    <div class="property-card">
                        <img src="/airbnb1.jpg" alt="Forest Cabin" class="property-img">
                        <div class="property-content">
                            <h4>Forest Cabin Retreat</h4>
                            <p>📍 Brgy. Malitbog, Impasugong</p>
                            <div class="property-price">₱3,500 <span>/ night</span></div>
                            <button class="btn btn-primary btn-sm" style="width: 100%; margin-top: 10px; justify-content: center;">
                                Book Now
                            </button>
                        </div>
                    </div>
                    
                    <div class="property-card">
                        <img src="/inn2.jpg" alt="Mountain Lodge" class="property-img">
                        <div class="property-content">
                            <h4>Mountain Lodge</h4>
                            <p>📍 Brgy. Kalingag, Impasugong</p>
                            <div class="property-price">₱2,000 <span>/ night</span></div>
                            <button class="btn btn-primary btn-sm" style="width: 100%; margin-top: 10px; justify-content: center;">
                                Book Now
                            </button>
                        </div>
                    </div>
                    
                    <div class="property-card">
                        <img src="/accommodation1.jpg" alt="Villa Rosa" class="property-img">
                        <div class="property-content">
                            <h4>Villa Rosa</h4>
                            <p>📍 Brgy. Haguit, Impasugong</p>
                            <div class="property-price">₱4,000 <span>/ day</span></div>
                            <button class="btn btn-primary btn-sm" style="width: 100%; margin-top: 10px; justify-content: center;">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Counter animation
        function animateCounters() {
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
                const prefix = counter.textContent.includes('₱') ? '₱' : '';
                const suffix = counter.textContent.includes('/') ? counter.textContent.split('/')[1] : '';
                let current = 0;
                const increment = target / 30;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = prefix + current.toLocaleString() + (suffix ? '/' + suffix : '');
                        clearInterval(timer);
                    } else {
                        counter.textContent = prefix + Math.floor(current).toLocaleString() + (suffix ? '/' + suffix : '');
                    }
                }, 30);
            });
        }
        
        // Run animation on load
        animateCounters();
    </script>
</body>
</html>
