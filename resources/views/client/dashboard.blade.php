<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - Impasugong Accommodations</title>
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
        
        /* Main Content */
        .main-content { padding-top: 90px; }
        
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
        .hero-content { max-width: 1400px; margin: 0 auto; position: relative; z-index: 1; }
        .hero h1 { font-size: 2.5rem; margin-bottom: 10px; font-weight: 700; }
        .hero p { font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px; }
        
        /* Search Section */
        .search-section {
            background: var(--white);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(27, 94, 32, 0.15);
        }
        .search-row { display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; }
        .search-input-group { flex: 1; min-width: 200px; }
        .search-input-group label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--green-dark); font-size: 0.9rem; }
        .search-input-group input, .search-input-group select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--green-soft);
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
        }
        .search-input-group input:focus, .search-input-group select:focus {
            border-color: var(--green-primary);
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.1);
        }
        .search-btn {
            padding: 14px 30px;
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .search-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4); }
        
        /* Section */
        .section { padding: 50px 40px; max-width: 1400px; margin: 0 auto; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .section-header h2 { font-size: 1.8rem; color: var(--green-dark); font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .view-all { color: var(--green-primary); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 6px; transition: all 0.3s; }
        .view-all:hover { color: var(--green-dark); gap: 10px; }
        
        /* Category Cards */
        .categories { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 50px; }
        .category-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            transition: all 0.3s;
            cursor: pointer;
            border: 1px solid var(--green-soft);
        }
        .category-card:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(27, 94, 32, 0.2); }
        .category-img { width: 100%; height: 180px; object-fit: cover; }
        .category-content { padding: 20px; }
        .category-badge { 
            display: inline-block; 
            background: linear-gradient(135deg, var(--green-soft), var(--green-pale)); 
            color: var(--green-dark); 
            padding: 6px 14px; 
            border-radius: 50px; 
            font-size: 0.8rem; 
            font-weight: 600;
            margin-bottom: 10px;
        }
        .category-content h3 { font-size: 1.2rem; color: var(--green-dark); margin-bottom: 8px; }
        .category-content p { color: var(--green-medium); font-size: 0.9rem; }
        
        /* Property Cards */
        .properties-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; }
        .property-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            transition: all 0.3s;
            border: 1px solid var(--green-soft);
        }
        .property-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(27, 94, 32, 0.2); }
        .property-img-wrapper { position: relative; }
        .property-img { width: 100%; height: 220px; object-fit: cover; }
        .property-type-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
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
            width: 40px;
            height: 40px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 1.2rem;
            color: var(--gray-400);
        }
        .property-favorite:hover { background: var(--green-pale); color: var(--red-500); transform: scale(1.1); }
        .property-content { padding: 20px; }
        .property-price { font-size: 1.4rem; font-weight: 700; color: var(--green-primary); margin-bottom: 8px; }
        .property-price span { font-size: 0.85rem; font-weight: 400; color: var(--green-medium); }
        .property-title { font-size: 1.1rem; color: var(--green-dark); margin-bottom: 8px; font-weight: 600; }
        .property-location { display: flex; align-items: center; gap: 6px; color: var(--green-medium); font-size: 0.9rem; margin-bottom: 15px; }
        .property-features { display: flex; gap: 15px; padding-top: 15px; border-top: 1px solid var(--green-soft); }
        .feature { display: flex; align-items: center; gap: 6px; color: var(--green-primary); font-size: 0.85rem; }
        .property-rating { display: flex; align-items: center; gap: 5px; margin-top: 10px; }
        .stars { color: var(--amber-500); }
        .rating-count { color: var(--green-medium); font-size: 0.85rem; }
        .book-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .book-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4); }
        
        /* Footer */
        .footer { background: var(--green-dark); color: var(--white); padding: 40px; text-align: center; }
        .footer p { opacity: 0.8; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .nav-links { display: none; }
            .hero { padding: 40px 20px; }
            .hero h1 { font-size: 1.8rem; }
            .search-row { flex-direction: column; }
            .section { padding: 30px 20px; }
        }
        
        /* Animations */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate { animation: fadeInUp 0.6s ease forwards; }
        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }
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
            <li><a href="{{ route('accommodations.index') }}" class="active"><i class="fas fa-search"></i> Browse</a></li>
            <li><a href="{{ route('bookings.index') }}"><i class="fas fa-calendar-alt"></i> My Bookings</a></li>
            <li><a href="{{ route('messages.index') }}"><i class="fas fa-envelope"></i> Messages</a></li>
        </ul>
        
        <div class="nav-actions">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-btn primary"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1><i class="fas fa-home" style="margin-right: 12px;"></i>Find Your Perfect Stay</h1>
                <p>Discover traveller-inns, Airbnb stays, and daily rentals in Impasugong</p>
                
                <div class="search-section">
                    <form action="{{ route('accommodations.index') }}" method="GET">
                        <div class="search-row">
                            <div class="search-input-group">
                                <label><i class="fas fa-map-marker-alt"></i> Location</label>
                                <input type="text" name="search" placeholder="Where do you want to stay?">
                            </div>
                            <div class="search-input-group">
                                <label><i class="fas fa-calendar-check"></i> Check In</label>
                                <input type="date" name="check_in" min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="search-input-group">
                                <label><i class="fas fa-calendar-times"></i> Check Out</label>
                                <input type="date" name="check_out" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                            <div class="search-input-group">
                                <label><i class="fas fa-users"></i> Guests</label>
                                <select name="guests">
                                    <option value="1">1 Guest</option>
                                    <option value="2">2 Guests</option>
                                    <option value="3">3 Guests</option>
                                    <option value="4">4 Guests</option>
                                    <option value="5">5+ Guests</option>
                                </select>
                            </div>
                            <button type="submit" class="search-btn"><i class="fas fa-search"></i> Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        
        <!-- Property Categories -->
        <section class="section">
            <div class="section-header">
                <h2><i class="fas fa-th-large"></i>Browse by Type</h2>
                <a href="{{ route('accommodations.index') }}" class="view-all"><i class="fas fa-arrow-right"></i> View All</a>
            </div>
            
            <div class="categories">
                <div class="category-card animate delay-1">
                    <img src="/COMMUNAL.jpg" alt="Traveller-Inns" class="category-img">
                    <div class="category-content">
                        <span class="category-badge"><i class="fas fa-bed"></i> Traditional</span>
                        <h3>Traveller-Inns</h3>
                        <p>Cozy, affordable inns for budget travelers</p>
                    </div>
                </div>
                
                <div class="category-card animate delay-2">
                    <img src="/1.jpg" alt="Airbnb" class="category-img">
                    <div class="category-content">
                        <span class="category-badge"><i class="fas fa-home"></i> Unique Stays</span>
                        <h3>Airbnb Rentals</h3>
                        <p>Unique homes hosted by locals</p>
                    </div>
                </div>
                
                <div class="category-card animate delay-3">
                    <img src="/2.jpg" alt="Daily Rentals" class="category-img">
                    <div class="category-content">
                        <span class="category-badge"><i class="fas fa-calendar"></i> Flexible</span>
                        <h3>Daily Rentals</h3>
                        <p>Flexible daily stays for any occasion</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Featured Properties -->
        <section class="section">
            <div class="section-header">
                <h2><i class="fas fa-star"></i>Featured Accommodations</h2>
                <a href="{{ route('accommodations.index') }}" class="view-all"><i class="fas fa-arrow-right"></i> View All</a>
            </div>
            
            <div class="properties-grid">
                <!-- Property 1 -->
                <div class="property-card animate delay-1">
                    <div class="property-img-wrapper">
                        <img src="/COMMUNAL.jpg" alt="Mountain View Inn" class="property-img">
                        <span class="property-type-badge"><i class="fas fa-bed"></i> Traveller-Inn</span>
                        <button class="property-favorite"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="property-content">
                        <div class="property-price">₱1,500 <span>/ night</span></div>
                        <h3 class="property-title">Mountain View Inn</h3>
                        <div class="property-location"><i class="fas fa-map-marker-alt"></i> Brgy. Poblacion, Impasugong</div>
                        <div class="property-features">
                            <span class="feature"><i class="fas fa-bed"></i> 2 Beds</span>
                            <span class="feature"><i class="fas fa-bath"></i> 1 Bath</span>
                            <span class="feature"><i class="fas fa-wifi"></i> WiFi</span>
                        </div>
                        <div class="property-rating">
                            <span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                            <span class="rating-count">(12 reviews)</span>
                        </div>
                        <button class="book-btn"><i class="fas fa-ticket-alt"></i> Book Now</button>
                    </div>
                </div>
                
                <!-- Property 2 -->
                <div class="property-card animate delay-2">
                    <div class="property-img-wrapper">
                        <img src="/1.jpg" alt="Cozy Garden House" class="property-img">
                        <span class="property-type-badge"><i class="fas fa-home"></i> Airbnb</span>
                        <button class="property-favorite"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="property-content">
                        <div class="property-price">₱2,800 <span>/ night</span></div>
                        <h3 class="property-title">Cozy Garden House</h3>
                        <div class="property-location"><i class="fas fa-map-marker-alt"></i> Brgy. Kapitan, Impasugong</div>
                        <div class="property-features">
                            <span class="feature"><i class="fas fa-bed"></i> 3 Beds</span>
                            <span class="feature"><i class="fas fa-bath"></i> 2 Baths</span>
                            <span class="feature"><i class="fas fa-utensils"></i> Kitchen</span>
                        </div>
                        <div class="property-rating">
                            <span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                            <span class="rating-count">(8 reviews)</span>
                        </div>
                        <button class="book-btn"><i class="fas fa-ticket-alt"></i> Book Now</button>
                    </div>
                </div>
                
                <!-- Property 3 -->
                <div class="property-card animate delay-3">
                    <div class="property-img-wrapper">
                        <img src="/2.jpg" alt="Riverside Apartment" class="property-img">
                        <span class="property-type-badge"><i class="fas fa-calendar"></i> Daily Rental</span>
                        <button class="property-favorite"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="property-content">
                        <div class="property-price">₱1,200 <span>/ day</span></div>
                        <h3 class="property-title">Riverside Apartment</h3>
                        <div class="property-location"><i class="fas fa-map-marker-alt"></i> Brgy. Centro, Impasugong</div>
                        <div class="property-features">
                            <span class="feature"><i class="fas fa-bed"></i> 1 Bed</span>
                            <span class="feature"><i class="fas fa-bath"></i> 1 Bath</span>
                            <span class="feature"><i class="fas fa-wifi"></i> WiFi</span>
                        </div>
                        <div class="property-rating">
                            <span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i></span>
                            <span class="rating-count">(15 reviews)</span>
                        </div>
                        <button class="book-btn"><i class="fas fa-ticket-alt"></i> Book Now</button>
                    </div>
                </div>
                
                <!-- Property 4 -->
                <div class="property-card animate delay-1">
                    <div class="property-img-wrapper">
                        <img src="/airbnb1.jpg" alt="Forest Cabin" class="property-img">
                        <span class="property-type-badge"><i class="fas fa-home"></i> Airbnb</span>
                        <button class="property-favorite"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="property-content">
                        <div class="property-price">₱3,500 <span>/ night</span></div>
                        <h3 class="property-title">Forest Cabin Retreat</h3>
                        <div class="property-location"><i class="fas fa-map-marker-alt"></i> Brgy. Malitbog, Impasugong</div>
                        <div class="property-features">
                            <span class="feature"><i class="fas fa-bed"></i> 4 Beds</span>
                            <span class="feature"><i class="fas fa-bath"></i> 2 Baths</span>
                            <span class="feature"><i class="fas fa-fire"></i> Fireplace</span>
                        </div>
                        <div class="property-rating">
                            <span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                            <span class="rating-count">(22 reviews)</span>
                        </div>
                        <button class="book-btn"><i class="fas fa-ticket-alt"></i> Book Now</button>
                    </div>
                </div>
                
                <!-- Property 5 -->
                <div class="property-card animate delay-2">
                    <div class="property-img-wrapper">
                        <img src="/inn1.jpg" alt="Town Inn" class="property-img">
                        <span class="property-type-badge"><i class="fas fa-bed"></i> Traveller-Inn</span>
                        <button class="property-favorite"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="property-content">
                        <div class="property-price">₱800 <span>/ night</span></div>
                        <h3 class="property-title">Town Inn Basic</h3>
                        <div class="property-location"><i class="fas fa-map-marker-alt"></i> Brgy. Poblacion, Impasugong</div>
                        <div class="property-features">
                            <span class="feature"><i class="fas fa-bed"></i> 1 Bed</span>
                            <span class="feature"><i class="fas fa-bath"></i> 1 Bath</span>
                            <span class="feature"><i class="fas fa-wifi"></i> WiFi</span>
                        </div>
                        <div class="property-rating">
                            <span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i></span>
                            <span class="rating-count">(35 reviews)</span>
                        </div>
                        <button class="book-btn"><i class="fas fa-ticket-alt"></i> Book Now</button>
                    </div>
                </div>
                
                <!-- Property 6 -->
                <div class="property-card animate delay-3">
                    <div class="property-img-wrapper">
                        <img src="/accommodation1.jpg" alt="Villa Rosa" class="property-img">
                        <span class="property-type-badge"><i class="fas fa-calendar"></i> Daily Rental</span>
                        <button class="property-favorite"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="property-content">
                        <div class="property-price">₱4,000 <span>/ day</span></div>
                        <h3 class="property-title">Villa Rosa</h3>
                        <div class="property-location"><i class="fas fa-map-marker-alt"></i> Brgy. Haguit, Impasugong</div>
                        <div class="property-features">
                            <span class="feature"><i class="fas fa-bed"></i> 5 Beds</span>
                            <span class="feature"><i class="fas fa-bath"></i> 3 Baths</span>
                            <span class="feature"><i class="fas fa-swimming-pool"></i> Pool</span>
                        </div>
                        <div class="property-rating">
                            <span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                            <span class="rating-count">(9 reviews)</span>
                        </div>
                        <button class="book-btn"><i class="fas fa-ticket-alt"></i> Book Now</button>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Footer -->
        <footer class="footer">
            <p><i class="fas fa-copyright"></i> 2024 Municipality of Impasugong. All Rights Reserved.</p>
        </footer>
    </div>
    
    <script>
        // Simple favorite toggle
        document.querySelectorAll('.property-favorite').forEach(btn => {
            btn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (icon.classList.contains('far')) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    this.style.color = '#dc3545';
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    this.style.color = '';
                }
            });
        });
    </script>
</body>
</html>
