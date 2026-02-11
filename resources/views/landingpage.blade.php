<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VerdeVistas | Impasugong Accommodations</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.85) 50%, rgba(27, 94, 32, 0.1) 100%),
                        url('/COMMUNAL.jpg') no-repeat center center/cover;
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--green-dark);
        }
        
        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            border-bottom: 2px solid var(--green-soft);
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.1);
        }
        
        .nav-brand { display: flex; align-items: center; gap: 15px; }
        .nav-brand img { height: 50px; width: auto; border-radius: 8px; }
        .nav-brand .system-name { font-size: 1.6rem; font-weight: 700; color: var(--green-dark); letter-spacing: -0.5px; }
        .nav-brand .tagline { font-size: 0.75rem; color: var(--green-medium); margin-left: 10px; }
        
        .nav-links { display: flex; gap: 30px; list-style: none; }
        .nav-links a { 
            text-decoration: none; 
            color: var(--green-dark); 
            font-weight: 600; 
            padding: 10px 18px; 
            border-radius: 8px; 
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }
        .nav-links a:hover { background: var(--green-soft); color: var(--green-dark); }
        
        .nav-buttons { display: flex; gap: 12px; }
        .btn {
            padding: 12px 26px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-outline {
            background: transparent;
            color: var(--green-dark);
            border: 2px solid var(--green-primary);
        }
        .btn-outline:hover { background: var(--green-primary); color: var(--white); }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            color: var(--white);
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4); }
        
        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 120px 40px 80px;
            text-align: center;
            background: linear-gradient(135deg, rgba(27, 94, 32, 0.08) 0%, rgba(46, 125, 50, 0.05) 100%);
        }
        
        .hero-badge { 
            display: inline-flex; 
            align-items: center;
            gap: 10px;
            background: var(--white); 
            padding: 12px 28px; 
            border-radius: 50px; 
            font-size: 0.9rem; 
            font-weight: 600; 
            margin-bottom: 30px; 
            border: 2px solid var(--green-soft);
            box-shadow: 0 4px 15px rgba(27, 94, 32, 0.1);
        }
        .hero-badge i { color: var(--green-primary); }
        
        .hero h1 { 
            font-size: 3.5rem; 
            color: var(--green-dark); 
            margin-bottom: 20px; 
            letter-spacing: -1px;
            font-weight: 800;
        }
        .hero h1 span { color: var(--green-primary); }
        
        .hero p { 
            font-size: 1.2rem; 
            color: var(--green-medium); 
            max-width: 700px; 
            margin-bottom: 40px; 
            line-height: 1.7;
        }
        
        .hero-buttons { display: flex; gap: 16px; justify-content: center; margin-bottom: 50px; }
        .hero-buttons .btn { padding: 14px 32px; font-size: 1rem; }
        
        .hero-stats { 
            display: flex; 
            gap: 50px; 
            justify-content: center; 
            flex-wrap: wrap;
            background: var(--white);
            padding: 30px 50px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(27, 94, 32, 0.1);
        }
        .stat-item { text-align: center; }
        .stat-number { font-size: 2.2rem; font-weight: 800; color: var(--green-dark); }
        .stat-label { font-size: 0.85rem; color: var(--green-medium); text-transform: uppercase; letter-spacing: 1px; margin-top: 5px; }
        
        /* Carousel Section */
        .carousel-section { 
            padding: 80px 40px; 
            background: var(--white);
        }
        .carousel-header { text-align: center; margin-bottom: 50px; }
        .carousel-header h2 { 
            font-size: 2.2rem; 
            color: var(--green-dark); 
            margin-bottom: 12px; 
            font-weight: 700;
        }
        .carousel-header p { font-size: 1rem; color: var(--green-medium); }
        
        .carousel-container { max-width: 1400px; margin: 0 auto; position: relative; overflow: hidden; }
        .carousel-track { display: flex; transition: transform 0.5s ease-in-out; }
        .carousel-slide { min-width: 320px; margin: 0 15px; }
        
        .property-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.12);
            transition: all 0.4s ease;
            border: 1px solid var(--green-soft);
        }
        .property-card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 20px 50px rgba(27, 94, 32, 0.2); 
        }
        
        .property-img { width: 100%; height: 200px; object-fit: cover; }
        .property-content { padding: 22px; }
        .property-type { 
            display: inline-flex; 
            align-items: center;
            gap: 6px;
            background: var(--green-soft); 
            color: var(--green-dark); 
            padding: 6px 14px; 
            border-radius: 50px; 
            font-size: 0.8rem; 
            font-weight: 600; 
            margin-bottom: 12px;
        }
        .property-content h3 { font-size: 1.15rem; color: var(--green-dark); margin-bottom: 8px; font-weight: 700; }
        .property-location { display: flex; align-items: center; gap: 6px; color: var(--green-medium); font-size: 0.85rem; margin-bottom: 15px; }
        .property-features { display: flex; gap: 15px; margin-bottom: 18px; padding-bottom: 15px; border-bottom: 1px solid var(--green-soft); }
        .feature { display: flex; align-items: center; gap: 6px; color: var(--green-dark); font-size: 0.85rem; }
        .property-footer { display: flex; justify-content: space-between; align-items: center; }
        .property-price { font-size: 1.4rem; font-weight: 700; color: var(--green-primary); }
        .property-price span { font-size: 0.85rem; font-weight: 400; color: var(--green-medium); }
        .property-rating { display: flex; align-items: center; gap: 5px; }
        .stars { color: #F59E0B; }
        
        .carousel-controls { display: flex; justify-content: center; gap: 12px; margin-top: 40px; }
        .carousel-btn {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--green-soft);
            color: var(--green-dark);
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .carousel-btn:hover { background: var(--green-primary); color: var(--white); transform: scale(1.1); }
        
        /* Features Section */
        .features-section { 
            padding: 100px 40px; 
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
        }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; max-width: 1200px; margin: 0 auto; }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.4s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .feature-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2); }
        .feature-icon { 
            font-size: 3rem; 
            color: var(--green-primary); 
            margin-bottom: 20px; 
        }
        .feature-card h3 { font-size: 1.25rem; color: var(--green-dark); margin-bottom: 12px; font-weight: 700; }
        .feature-card p { color: var(--green-medium); line-height: 1.7; font-size: 0.95rem; }
        
        /* Footer */
        .footer { 
            background: var(--white); 
            padding: 40px; 
            text-align: center; 
            border-top: 2px solid var(--green-soft);
        }
        .footer p { color: var(--green-medium); font-size: 0.9rem; }
        .footer a { color: var(--green-primary); text-decoration: none; font-weight: 600; }
        .footer a:hover { text-decoration: underline; }
        
        /* Animations */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .animate { animation: fadeInUp 0.6s ease forwards; }
        .delay-1 { animation-delay: 0.15s; }
        .delay-2 { animation-delay: 0.3s; }
        .delay-3 { animation-delay: 0.45s; }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .navbar { padding: 15px 25px; }
            .nav-links { gap: 15px; }
            .nav-links a { padding: 8px 12px; font-size: 0.9rem; }
            .hero h1 { font-size: 2.8rem; }
        }
        
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; flex-direction: column; gap: 15px; }
            .nav-brand .tagline { display: none; }
            .nav-links { display: none; }
            .hero { padding: 100px 20px 40px; }
            .hero h1 { font-size: 2rem; }
            .hero p { font-size: 1rem; }
            .hero-buttons { flex-direction: column; align-items: center; }
            .hero-stats { gap: 30px; padding: 20px 30px; }
            .stat-number { font-size: 1.6rem; }
            .carousel-section, .features-section { padding: 50px 20px; }
            .carousel-slide { min-width: 280px; }
            .carousel-header h2 { font-size: 1.6rem; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-brand">
            <img src="/SYSTEMLOGO.jpg" alt="VerdeVistas Logo">
            <div>
                <span class="system-name">VerdeVistas</span>
                <span class="tagline">| Impasugong Accommodations</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="#properties"><i class="fas fa-building"></i> Properties</a></li>
            <li><a href="#features"><i class="fas fa-info-circle"></i> About</a></li>
        </ul>
        <div class="nav-buttons">
            <a href="/login" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="/register" class="btn btn-primary"><i class="fas fa-user-plus"></i> Register</a>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-badge animate">
            <i class="fas fa-home"></i>
            <span>Your Gateway to Impasugong Accommodations</span>
        </div>
        
        <h1 class="animate delay-1">Find Your Perfect <span>Stay</span></h1>
        
        <p class="animate delay-2">
            Discover traveller-inns, Airbnb stays, and daily rentals. 
            Book unique accommodations and experience local hospitality.
        </p>
        
        <div class="hero-buttons animate delay-2">
            <a href="/register" class="btn btn-primary"><i class="fas fa-rocket"></i> Get Started</a>
            <a href="/login" class="btn btn-outline"><i class="fas fa-search"></i> Browse Properties</a>
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
                <div class="stat-label">Avg Rating</div>
            </div>
        </div>
    </section>
    
    <!-- Accommodations Carousel Section -->
    <section class="carousel-section" id="properties">
        <div class="carousel-header animate">
            <h2><i class="fas fa-star" style="color: var(--green-primary); margin-right: 10px;"></i>Featured Accommodations</h2>
            <p>Handpicked properties with great reviews and amenities</p>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-track" id="carouselTrack">
                <!-- Property 1 -->
                <div class="carousel-slide">
                    <div class="property-card">
                        <img src="/COMMUNAL.jpg" alt="Mountain View Inn" class="property-img">
                        <div class="property-content">
                            <span class="property-type"><i class="fas fa-bed"></i> Traveller-Inn</span>
                            <h3>Mountain View Inn</h3>
                            <div class="property-location"><i class="fas fa-map-marker-alt"></i> Brgy. Poblacion, Impasugong</div>
                            <div class="property-features">
                                <span class="feature"><i class="fas fa-bed"></i> 2 Beds</span>
                                <span class="feature"><i class="fas fa-bath"></i> 1 Bath</span>
                                <span class="feature"><i class="fas fa-wifi"></i> WiFi</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱1,500 <span>/ night</span></div>
                                <div class="property-rating">
                                    <span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
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
                            <span class="property-type"><i class="fas fa-home"></i> Airbnb</span>
                            <h3>Cozy Garden House</h3>
                            <div class="property-location"><i class="fas fa-map-marker-alt"></i> Brgy. Kapitan, Impasugong</div>
                            <div class="property-features">
                                <span class="feature"><i class="fas fa-bed"></i> 3 Beds</span>
                                <span class="feature"><i class="fas fa-bath"></i> 2 Baths</span>
                                <span class="feature"><i class="fas fa-utensils"></i> Kitchen</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱2,800 <span>/ night</span></div>
                                <div class="property-rating">
                                    <span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
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
                            <span class="property-type"><i class="fas fa-calendar"></i> Daily Rental</span>
                            <h3>Riverside Apartment</h3>
                            <div class="property-location"><i class="fas fa-map-marker-alt"></i> Brgy. Centro, Impasugong</div>
                            <div class="property-features">
                                <span class="feature"><i class="fas fa-bed"></i> 1 Bed</span>
                                <span class="feature"><i class="fas fa-bath"></i> 1 Bath</span>
                                <span class="feature"><i class="fas fa-wifi"></i> WiFi</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱1,200 <span>/ day</span></div>
                                <div class="property-rating">
                                    <span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i></span>
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
                            <span class="property-type"><i class="fas fa-home"></i> Airbnb</span>
                            <h3>Forest Cabin Retreat</h3>
                            <div class="property-location"><i class="fas fa-map-marker-alt"></i> Brgy. Malitbog, Impasugong</div>
                            <div class="property-features">
                                <span class="feature"><i class="fas fa-bed"></i> 4 Beds</span>
                                <span class="feature"><i class="fas fa-bath"></i> 2 Baths</span>
                                <span class="feature"><i class="fas fa-fire"></i> Fireplace</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱3,500 <span>/ night</span></div>
                                <div class="property-rating">
                                    <span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
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
                            <span class="property-type"><i class="fas fa-bed"></i> Traveller-Inn</span>
                            <h3>Town Inn Basic</h3>
                            <div class="property-location"><i class="fas fa-map-marker-alt"></i> Brgy. Poblacion, Impasugong</div>
                            <div class="property-features">
                                <span class="feature"><i class="fas fa-bed"></i> 1 Bed</span>
                                <span class="feature"><i class="fas fa-bath"></i> 1 Bath</span>
                                <span class="feature"><i class="fas fa-wifi"></i> WiFi</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱800 <span>/ night</span></div>
                                <div class="property-rating">
                                    <span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i></span>
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
                            <span class="property-type"><i class="fas fa-calendar"></i> Daily Rental</span>
                            <h3>Villa Rosa</h3>
                            <div class="property-location"><i class="fas fa-map-marker-alt"></i> Brgy. Haguit, Impasugong</div>
                            <div class="property-features">
                                <span class="feature"><i class="fas fa-bed"></i> 5 Beds</span>
                                <span class="feature"><i class="fas fa-bath"></i> 3 Baths</span>
                                <span class="feature"><i class="fas fa-swimming-pool"></i> Pool</span>
                            </div>
                            <div class="property-footer">
                                <div class="property-price">₱4,000 <span>/ day</span></div>
                                <div class="property-rating">
                                    <span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                                    <span>(9)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="carousel-controls">
                <button class="carousel-btn" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
                <button class="carousel-btn" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="features-grid">
            <div class="feature-card animate delay-1">
                <div class="feature-icon"><i class="fas fa-search-location"></i></div>
                <h3>Easy Search</h3>
                <p>Find your perfect rental with our powerful search and filter options.</p>
            </div>
            
            <div class="feature-card animate delay-2">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Secure Booking</h3>
                <p>Book with confidence knowing all transactions are secure and protected.</p>
            </div>
            
            <div class="feature-card animate delay-3">
                <div class="feature-icon"><i class="fas fa-comments"></i></div>
                <h3>Direct Communication</h3>
                <p>Chat directly with property owners for inquiries and special requests.</p>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <p><strong>VerdeVistas</strong> | Impasugong Accommodations Platform</p>
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
        const slideWidth = 350;
        
        function updateCarousel() {
            const maxIndex = Math.max(0, totalSlides - visibleSlides);
            currentIndex = Math.min(currentIndex, maxIndex);
            const offset = -currentIndex * slideWidth;
            carouselTrack.style.transform = `translateX(${offset}px)`;
        }
        
        prevBtn.addEventListener('click', function() {
            if (currentIndex > 0) { currentIndex--; updateCarousel(); }
        });
        
        nextBtn.addEventListener('click', function() {
            const maxIndex = Math.max(0, totalSlides - visibleSlides);
            if (currentIndex < maxIndex) { currentIndex++; updateCarousel(); }
        });
        
        setInterval(function() {
            const maxIndex = Math.max(0, totalSlides - visibleSlides);
            if (currentIndex < maxIndex) { currentIndex++; } else { currentIndex = 0; }
            updateCarousel();
        }, 5000);
    </script>
</body>
</html>
