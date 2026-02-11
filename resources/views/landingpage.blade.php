<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impasugong Accommodations | Traveller-Inns, Airbnb & Daily Rentals</title>
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
            --bg-light: #f5f5f5;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, rgba(27, 94, 32, 0.6), rgba(46, 125, 50, 0.7)),
                        url('/COMMUNAL.jpg') no-repeat center center/cover;
            background-attachment: fixed;
            background-color: #f5f5f5;
            min-height: 100vh;
            color: #1B5E20;
        }
        
        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(27, 94, 32, 0.2);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
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
            border: 3px solid #2E7D32;
        }
        
        .nav-logo span {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1B5E20;
        }
        
        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #1B5E20;
            font-weight: 500;
            transition: color 0.3s;
            padding: 8px 15px;
            border-radius: 8px;
        }
        
        .nav-links a:hover {
            color: #2E7D32;
            background: rgba(46, 125, 50, 0.1);
        }
        
        .nav-buttons {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            padding: 12px 28px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-light {
            background: #E8F5E9;
            color: #1B5E20;
            border: 2px solid #2E7D32;
        }
        
        .btn-light:hover {
            background: #C8E6C9;
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: #2E7D32;
            color: #FFFFFF;
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(46, 125, 50, 0.4);
            background: #1B5E20;
        }
        
        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 120px 40px 60px;
            text-align: center;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.75), rgba(232, 245, 233, 0.85)),
                        url('/COMMUNAL.jpg') no-repeat center center/cover;
            background-attachment: fixed;
        }
        
        .hero-badge {
            display: inline-block;
            background: #2E7D32;
            padding: 10px 25px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 25px;
            color: #FFFFFF;
        }
        
        .hero h1 {
            font-size: 4rem;
            color: #1B5E20;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }
        
        .hero h1 span {
            color: #2E7D32;
        }
        
        .hero p {
            font-size: 1.4rem;
            color: #2E7D32;
            max-width: 700px;
            margin-bottom: 35px;
            line-height: 1.7;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 50px;
        }
        
        .hero-stats {
            display: flex;
            gap: 60px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: #2E7D32;
        }
        
        .stat-label {
            font-size: 1rem;
            color: #1B5E20;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
        
        /* Carousel Section */
        .carousel-section {
            padding: 80px 40px;
            background: #FFFFFF;
            border-top: 4px solid #2E7D32;
        }
        
        .carousel-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .carousel-header h2 {
            font-size: 2.5rem;
            color: #1B5E20;
            margin-bottom: 15px;
        }
        
        .carousel-header p {
            font-size: 1.2rem;
            color: #2E7D32;
        }
        
        /* Carousel Container */
        .carousel-container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }
        
        .carousel-track {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        
        .carousel-slide {
            min-width: 350px;
            margin: 0 15px;
        }
        
        .property-card {
            background: #FFFFFF;
            border-radius: 25px;
            overflow: hidden;
            border: 1px solid #C8E6C9;
            transition: all 0.4s ease;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
        }
        
        .property-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(27, 94, 32, 0.2);
        }
        
        .property-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        
        .property-content {
            padding: 25px;
        }
        
        .property-type {
            display: inline-block;
            background: #E8F5E9;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1B5E20;
        }
        
        .property-content h3 {
            font-size: 1.3rem;
            color: #1B5E20;
            margin-bottom: 10px;
        }
        
        .property-location {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #2E7D32;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .property-features {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #E8F5E9;
        }
        
        .feature {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #43A047;
            font-size: 0.85rem;
        }
        
        .property-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .property-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2E7D32;
        }
        
        .property-price span {
            font-size: 0.85rem;
            font-weight: 400;
            color: #66BB6A;
        }
        
        .property-rating {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .stars {
            color: #ffc107;
        }
        
        /* Rating count */
        .property-rating span:last-child {
            color: #43A047;
            font-size: 0.85rem;
        }
        
        /* Carousel Controls */
        .carousel-controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 40px;
        }
        
        .carousel-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #2E7D32;
            border: none;
            color: #FFFFFF;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .carousel-btn:hover {
            background: #1B5E20;
            transform: scale(1.1);
        }
        
        /* Features Section */
        .features-section {
            padding: 100px 40px;
            background: #E8F5E9;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .feature-card {
            background: #FFFFFF;
            border-radius: 25px;
            padding: 40px 30px;
            text-align: center;
            border: 1px solid #C8E6C9;
            transition: all 0.4s ease;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.08);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(27, 94, 32, 0.15);
        }
        
        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            font-size: 1.4rem;
            color: #1B5E20;
            margin-bottom: 15px;
        }
        
        .feature-card p {
            color: #43A047;
            line-height: 1.7;
            font-size: 1rem;
        }
        
        /* Footer */
        .footer {
            background: #1B5E20;
            padding: 40px;
            text-align: center;
        }
        
        .footer p {
            color: #E8F5E9;
            font-size: 0.9rem;
        }
        
        .footer a {
            color: #81C784;
            text-decoration: none;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate {
            animation: fadeInUp 0.8s ease forwards;
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
                padding: 100px 20px 40px;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .hero-stats {
                gap: 30px;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            .carousel-section,
            .features-section {
                padding: 50px 20px;
            }
            
            .carousel-slide {
                min-width: 280px;
            }
            
            .carousel-header h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-logo">
            <a href="{{ route('landing') }}">
                <img src="/1.jpg" alt="Municipality Logo">
            </a>
            <span>Impasugong</span>
        </div>
        <ul class="nav-links">
            <li><a href="#properties">Properties</a></li>
            <li><a href="#features">How It Works</a></li>
            <li><a href="#about">About</a></li>
            @auth
                <li><a href="{{ route('profile.edit') }}">Profile</a></li>
            @endauth
        </ul>
        <div class="nav-buttons">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light">Dashboard</a>
                @elseif(Auth::user()->role === 'owner')
                    <a href="{{ route('owner.dashboard') }}" class="btn btn-light">Dashboard</a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-light">Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-light">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            @endauth
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-badge animate">🏡 #1 Property Rental Platform in Impasugong</div>
        
        <h1 class="animate delay-1">Find Your Perfect <span>Rental Property</span></h1>
        
        <p class="animate delay-2">
            Discover traveller-inns, Airbnb stays, and daily property rentals. 
            Book unique accommodations and experience local hospitality like never before.
        </p>
        
        <div class="hero-buttons animate delay-3">
            <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
            <a href="{{ route('login') }}" class="btn btn-light">Browse Properties</a>
        </div>
        
        <div class="hero-stats animate delay-3">
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Properties</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">1,200+</div>
                <div class="stat-label">Happy Guests</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">50+</div>
                <div class="stat-label">Local Hosts</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">4.8</div>
                <div class="stat-label">Average Rating</div>
            </div>
        </div>
    </section>
    
    <!-- Accommodations Carousel Section -->
    <section class="carousel-section" id="properties">
        <div class="carousel-header animate">
            <h2>Featured Accommodations</h2>
            <p>Handpicked properties with great reviews and amenities</p>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-track" id="carouselTrack">
                <!-- Property 1 -->
                <div class="carousel-slide">
                    <div class="property-card">
                        <img src="/COMMUNAL.jpg" alt="Mountain View Inn" class="property-img">
                        <div class="property-content">
                            <span class="property-type">🏨 Traveller-Inn</span>
                            <h3>Mountain View Inn</h3>
                            <div class="property-location">📍 Brgy. Poblacion, Impasugong</div>
                            <div class="property-features">
                                <span class="feature">🛏️ 2 Beds</span>
                                <span class="feature">🚿 1 Bath</span>
                                <span class="feature">📶 WiFi</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱1,500 <span>/ night</span></div>
                                <div class="property-rating">
                                    <span class="stars">★★★★★</span>
                                    <span>(12)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Property 2 -->
                <div class="carousel-slide">
                    <div class="property-card">
                        <img src="/1.jpg" alt="Cozy Garden House" class="property-img">
                        <div class="property-content">
                            <span class="property-type">🏠 Airbnb</span>
                            <h3>Cozy Garden House</h3>
                            <div class="property-location">📍 Brgy. Kapitan, Impasugong</div>
                            <div class="property-features">
                                <span class="feature">🛏️ 3 Beds</span>
                                <span class="feature">🚿 2 Baths</span>
                                <span class="feature">🍳 Kitchen</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱2,800 <span>/ night</span></div>
                                <div class="property-rating">
                                    <span class="stars">★★★★★</span>
                                    <span>(8)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Property 3 -->
                <div class="carousel-slide">
                    <div class="property-card">
                        <img src="/2.jpg" alt="Riverside Apartment" class="property-img">
                        <div class="property-content">
                            <span class="property-type">📅 Daily Rental</span>
                            <h3>Riverside Apartment</h3>
                            <div class="property-location">📍 Brgy. Centro, Impasugong</div>
                            <div class="property-features">
                                <span class="feature">🛏️ 1 Bed</span>
                                <span class="feature">🚿 1 Bath</span>
                                <span class="feature">📶 WiFi</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱1,200 <span>/ day</span></div>
                                <div class="property-rating">
                                    <span class="stars">★★★★☆</span>
                                    <span>(15)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Property 4 -->
                <div class="carousel-slide">
                    <div class="property-card">
                        <img src="/airbnb1.jpg" alt="Forest Cabin" class="property-img">
                        <div class="property-content">
                            <span class="property-type">🏠 Airbnb</span>
                            <h3>Forest Cabin Retreat</h3>
                            <div class="property-location">📍 Brgy. Malitbog, Impasugong</div>
                            <div class="property-features">
                                <span class="feature">🛏️ 4 Beds</span>
                                <span class="feature">🚿 2 Baths</span>
                                <span class="feature">🔥 Fireplace</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱3,500 <span>/ night</span></div>
                                <div class="property-rating">
                                    <span class="stars">★★★★★</span>
                                    <span>(22)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Property 5 -->
                <div class="carousel-slide">
                    <div class="property-card">
                        <img src="/inn1.jpg" alt="Town Inn" class="property-img">
                        <div class="property-content">
                            <span class="property-type">🏨 Traveller-Inn</span>
                            <h3>Town Inn Basic</h3>
                            <div class="property-location">📍 Brgy. Poblacion, Impasugong</div>
                            <div class="property-features">
                                <span class="feature">🛏️ 1 Bed</span>
                                <span class="feature">🚿 1 Bath</span>
                                <span class="feature">📶 WiFi</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱800 <span>/ night</span></div>
                                <div class="property-rating">
                                    <span class="stars">★★★★☆</span>
                                    <span>(35)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Property 6 -->
                <div class="carousel-slide">
                    <div class="property-card">
                        <img src="/accommodation1.jpg" alt="Villa Rosa" class="property-img">
                        <div class="property-content">
                            <span class="property-type">📅 Daily Rental</span>
                            <h3>Villa Rosa</h3>
                            <div class="property-location">📍 Brgy. Haguit, Impasugong</div>
                            <div class="property-features">
                                <span class="feature">🛏️ 5 Beds</span>
                                <span class="feature">🚿 3 Baths</span>
                                <span class="feature">🏊 Pool</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱4,000 <span>/ day</span></div>
                                <div class="property-rating">
                                    <span class="stars">★★★★★</span>
                                    <span>(9)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Property 7 -->
                <div class="carousel-slide">
                    <div class="property-card">
                        <img src="/airbnb2.jpg" alt="Lakeside Villa" class="property-img">
                        <div class="property-content">
                            <span class="property-type">🏠 Airbnb</span>
                            <h3>Lakeside Villa</h3>
                            <div class="property-location">📍 Brgy. Bontoc, Impasugong</div>
                            <div class="property-features">
                                <span class="feature">🛏️ 4 Beds</span>
                                <span class="feature">🚿 3 Baths</span>
                                <span class="feature">🏖️ Lake Access</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱5,500 <span>/ night</span></div>
                                <div class="property-rating">
                                    <span class="stars">★★★★★</span>
                                    <span>(18)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Property 8 -->
                <div class="carousel-slide">
                    <div class="property-card">
                        <img src="/inn2.jpg" alt="Mountain Lodge" class="property-img">
                        <div class="property-content">
                            <span class="property-type">🏨 Traveller-Inn</span>
                            <h3>Mountain Lodge</h3>
                            <div class="property-location">📍 Brgy. Kalingag, Impasugong</div>
                            <div class="property-features">
                                <span class="feature">🛏️ 3 Beds</span>
                                <span class="feature">🚿 2 Baths</span>
                                <span class="feature">🌄 Mountain View</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱2,000 <span>/ night</span></div>
                                <div class="property-rating">
                                    <span class="stars">★★★★★</span>
                                    <span>(25)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="carousel-controls">
                <button class="carousel-btn" id="prevBtn">←</button>
                <button class="carousel-btn" id="nextBtn">→</button>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="features-grid">
            <div class="feature-card animate delay-1">
                <div class="feature-icon">🔍</div>
                <h3>Easy Search</h3>
                <p>Find your perfect rental with our powerful search and filter options. Search by location, price, amenities, and more.</p>
            </div>
            
            <div class="feature-card animate delay-2">
                <div class="feature-icon">🔒</div>
                <h3>Secure Booking</h3>
                <p>Book with confidence knowing that all transactions are secure and your personal information is protected.</p>
            </div>
            
            <div class="feature-card animate delay-3">
                <div class="feature-icon">💬</div>
                <h3>Direct Communication</h3>
                <p>Chat directly with property owners to ask questions, negotiate rates, and arrange special requests.</p>
            </div>
            
            <div class="feature-card animate delay-1">
                <div class="feature-icon">⭐</div>
                <h3>Verified Reviews</h3>
                <p>Read genuine reviews from verified guests to make informed decisions about your stay.</p>
            </div>
            
            <div class="feature-card animate delay-2">
                <div class="feature-icon">💳</div>
                <h3>Flexible Payments</h3>
                <p>Multiple payment options including cash, bank transfer, and online payment methods.</p>
            </div>
            
            <div class="feature-card animate delay-3">
                <div class="feature-icon">📞</div>
                <h3>24/7 Support</h3>
                <p>Our support team is available around the clock to assist you with any questions or concerns.</p>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer" id="about">
        <p>© 2024 Municipality of Impasugong. All Rights Reserved.</p>
        <p style="margin-top: 10px;">
            <a href="#">Privacy Policy</a> | 
            <a href="#">Terms of Service</a> | 
            <a href="#">Contact Us</a>
        </p>
    </footer>
    
    <script>
        // Carousel functionality
        const carouselTrack = document.getElementById('carouselTrack');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        
        let currentIndex = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const totalSlides = slides.length;
        const visibleSlides = window.innerWidth < 768 ? 1 : 3;
        const slideWidth = 380; // 350px + 30px margin
        
        function updateCarousel() {
            const maxIndex = Math.max(0, totalSlides - visibleSlides);
            currentIndex = Math.min(currentIndex, maxIndex);
            
            const offset = -currentIndex * slideWidth;
            carouselTrack.style.transform = `translateX(${offset}px)`;
        }
        
        prevBtn.addEventListener('click', function() {
            if (currentIndex > 0) {
                currentIndex--;
                updateCarousel();
            }
        });
        
        nextBtn.addEventListener('click', function() {
            const maxIndex = Math.max(0, totalSlides - visibleSlides);
            if (currentIndex < maxIndex) {
                currentIndex++;
                updateCarousel();
            }
        });
        
        // Auto-advance carousel
        setInterval(function() {
            const maxIndex = Math.max(0, totalSlides - visibleSlides);
            if (currentIndex < maxIndex) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateCarousel();
        }, 5000);
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const newVisibleSlides = window.innerWidth < 768 ? 1 : 3;
            if (newVisibleSlides !== visibleSlides) {
                location.reload();
            }
        });
        
        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>

