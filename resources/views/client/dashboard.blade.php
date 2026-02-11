<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Impasugong Accommodations</title>
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
        
        /* Main Content */
        .main-content {
            padding-top: 90px;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            padding: 60px 40px;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('/COMMUNAL.jpg') no-repeat center center/cover;
            opacity: 0.1;
        }
        
        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .hero p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        /* Search Section */
        .search-section {
            background: var(--white);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(27, 94, 32, 0.15);
        }
        
        .search-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .search-input-group {
            flex: 1;
            min-width: 200px;
        }
        
        .search-input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--green-dark);
            font-size: 0.9rem;
        }
        
        .search-input-group input,
        .search-input-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--green-soft);
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s;
        }
        
        .search-input-group input:focus,
        .search-input-group select:focus {
            border-color: var(--green-primary);
        }
        
        .search-btn {
            padding: 12px 30px;
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            align-self: flex-end;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
        }
        
        /* Section */
        .section {
            padding: 50px 40px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .section-header h2 {
            font-size: 1.8rem;
            color: var(--green-dark);
        }
        
        .view-all {
            color: var(--green-primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .view-all:hover {
            text-decoration: underline;
        }
        
        /* Property Categories */
        .categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }
        
        .category-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }
        
        .category-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(27, 94, 32, 0.2);
        }
        
        .category-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        
        .category-content {
            padding: 20px;
        }
        
        .category-badge {
            display: inline-block;
            background: var(--green-soft);
            color: var(--green-dark);
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .category-content h3 {
            font-size: 1.2rem;
            color: var(--green-dark);
            margin-bottom: 8px;
        }
        
        .category-content p {
            color: var(--green-medium);
            font-size: 0.9rem;
        }
        
        /* Properties Grid */
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
        }
        
        .property-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            transition: all 0.3s ease;
        }
        
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(27, 94, 32, 0.2);
        }
        
        .property-img-wrapper {
            position: relative;
        }
        
        .property-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        
        .property-type-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--green-primary);
            color: var(--white);
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .property-favorite {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 38px;
            height: 38px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 1.2rem;
        }
        
        .property-favorite:hover {
            background: var(--green-pale);
            transform: scale(1.1);
        }
        
        .property-content {
            padding: 20px;
        }
        
        .property-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--green-primary);
            margin-bottom: 8px;
        }
        
        .property-price span {
            font-size: 0.85rem;
            font-weight: 400;
            color: var(--green-medium);
        }
        
        .property-title {
            font-size: 1.1rem;
            color: var(--green-dark);
            margin-bottom: 8px;
        }
        
        .property-location {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--green-medium);
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .property-features {
            display: flex;
            gap: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--green-soft);
        }
        
        .feature {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--green-primary);
            font-size: 0.85rem;
        }
        
        .property-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
        }
        
        .stars {
            color: #ffc107;
        }
        
        .rating-count {
            color: var(--green-medium);
            font-size: 0.85rem;
        }
        
        .book-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
        }
        
        .book-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
        }
        
        /* Footer */
        .footer {
            background: var(--green-dark);
            color: var(--white);
            padding: 40px;
            text-align: center;
        }
        
        .footer p {
            opacity: 0.8;
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
            
            .hero {
                padding: 40px 20px;
            }
            
            .hero h1 {
                font-size: 1.8rem;
            }
            
            .search-row {
                flex-direction: column;
            }
            
            .section {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-logo">
            <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 15px; text-decoration: none;">
                <img src="/1.jpg" alt="Municipality Logo">
                <span>Impasugong</span>
            </a>
        </div>
        <ul class="nav-links">
            <a href="{{ route('dashboard') }}" class="active">Browse</a>
            <a href="{{ route('bookings.index') }}">My Bookings</a>
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
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Find Your Perfect Stay</h1>
                <p>Discover traveller-inns, Airbnb stays, and daily rentals in Impasugong</p>
                
                <div class="search-section">
                    <form action="{{ route('accommodations.index') }}" method="GET">
                        <div class="search-row">
                            <div class="search-input-group">
                                <label>Location</label>
                                <input type="text" name="search" placeholder="Where do you want to stay?">
                            </div>
                            <div class="search-input-group">
                                <label>Check In</label>
                                <input type="date" name="check_in" min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="search-input-group">
                                <label>Check Out</label>
                                <input type="date" name="check_out" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                            <div class="search-input-group">
                                <label>Guests</label>
                                <select name="guests">
                                    <option value="1">1 Guest</option>
                                    <option value="2">2 Guests</option>
                                    <option value="3">3 Guests</option>
                                    <option value="4">4 Guests</option>
                                    <option value="5">5+ Guests</option>
                                </select>
                            </div>
                            <button type="submit" class="search-btn">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        
        <!-- Property Categories -->
        <section class="section">
            <div class="section-header">
                <h2>Browse by Type</h2>
                <a href="{{ route('accommodations.index') }}" class="view-all">View All →</a>
            </div>
            
            <div class="categories">
                <a href="{{ route('accommodations.index', ['type' => 'traveller-inn']) }}" class="category-card animate delay-1">
                    <img src="/COMMUNAL.jpg" alt="Traveller-Inns" class="category-img">
                    <div class="category-content">
                        <span class="category-badge">🏨 Traditional</span>
                        <h3>Traveller-Inns</h3>
                        <p>Cozy, affordable inns for budget travelers</p>
                    </div>
                </a>
                
                <a href="{{ route('accommodations.index', ['type' => 'airbnb']) }}" class="category-card animate delay-2">
                    <img src="/1.jpg" alt="Airbnb" class="category-img">
                    <div class="category-content">
                        <span class="category-badge">🏠 Unique Stays</span>
                        <h3>Airbnb Rentals</h3>
                        <p>Unique homes hosted by locals</p>
                    </div>
                </a>
                
                <a href="{{ route('accommodations.index', ['type' => 'daily-rental']) }}" class="category-card animate delay-3">
                    <img src="/2.jpg" alt="Daily Rentals" class="category-img">
                    <div class="category-content">
                        <span class="category-badge">📅 Flexible</span>
                        <h3>Daily Rentals</h3>
                        <p>Flexible daily stays for any occasion</p>
                    </div>
                </a>
            </div>
        </section>
        
        <!-- Footer -->
        <footer class="footer">
            <p>© 2024 Municipality of Impasugong. All Rights Reserved.</p>
        </footer>
    </div>
</body>
</html>
