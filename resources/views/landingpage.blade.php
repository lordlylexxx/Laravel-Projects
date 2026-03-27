<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpaStay | Impasugong Accommodations</title>
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
            left: 0;
            right: 0;
            z-index: 1000;
            border-bottom: 2px solid var(--green-soft);
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.1);
        }
        
        .nav-brand { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
        }
        .nav-brand img.main-logo { 
            height: 55px; 
            width: auto; 
            border-radius: 8px; 
        }
        .nav-brand .logo-divider { 
            height: 45px; 
            width: 3px; 
            background: linear-gradient(180deg, var(--green-dark), var(--green-primary), var(--green-medium)); 
            border-radius: 2px; 
        }
        .nav-brand .collaboration-logos { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        .nav-brand .collaboration-logos img { 
            height: 45px; 
            width: auto; 
            border-radius: 6px;
        }
        .nav-brand .system-name { 
            font-size: 1.5rem; 
            font-weight: 800; 
            color: var(--green-dark); 
            letter-spacing: -0.5px; 
        }
        .nav-brand .tagline { 
            font-size: 0.7rem; 
            color: var(--green-medium); 
            margin-left: 8px;
            display: inline;
            line-height: 1.2;
        }
        
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
        
        .hero-buttons { display: flex; gap: 16px; justify-content: center; margin-bottom: 0; }
        .hero-buttons .btn { padding: 14px 32px; font-size: 1rem; }
        
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
        
        /* Pricing Section */
        .pricing-section {
            padding: 100px 40px; 
            background: transparent;
        }

        .pricing-header {
            text-align: center;
            color: var(--green-dark);
            max-width: 760px;
            margin: 0 auto 45px;
        }
        .pricing-header h2 {
            font-size: 2.2rem;
            margin-bottom: 12px;
            font-weight: 700;
        }
        .pricing-header p {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--green-dark);
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .pricing-card {
            background: rgba(255, 255, 255, 0.14);
            border-radius: 20px;
            padding: 36px 30px;
            transition: all 0.4s ease;
            border: 1px solid rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(4px);
        }
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }
        .pricing-card.featured {
            border: 2px solid var(--green-primary);
            transform: scale(1.03);
        }
        .plan-name {
            font-size: 1.25rem;
            color: var(--green-dark);
            margin-bottom: 10px;
            font-weight: 700;
        }
        .plan-price {
            font-size: 2rem;
            font-weight: 800;
            color: var(--green-dark);
            margin-bottom: 20px;
        }
        .plan-features {
            list-style: none;
            padding: 0;
            margin: 0;
            color: var(--green-dark);
        }
        .plan-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 0.95rem;
            line-height: 1.4;
        }
        .plan-features li::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--green-dark);
        }
        .plan-register-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin-top: 22px;
            padding: 12px 14px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            border: 1px solid var(--green-primary);
            color: var(--green-primary);
            background: rgba(255, 255, 255, 0.75);
            transition: all 0.25s ease;
        }
        .plan-register-btn:hover {
            background: var(--green-primary);
            color: var(--white);
            transform: translateY(-2px);
        }
        
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
            .carousel-section, .pricing-section { padding: 50px 20px; }
            .carousel-slide { min-width: 280px; }
            .carousel-header h2 { font-size: 1.6rem; }
            .pricing-header h2 { font-size: 1.6rem; }
            .pricing-card.featured { transform: none; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-brand">
            <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo" class="main-logo">
            <div>
                <span class="system-name">ImpaStay</span>
                <span class="tagline">| Impasugong Accommodations</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="#properties"><i class="fas fa-building"></i> Properties</a></li>
            <li><a href="#pricing"><i class="fas fa-tags"></i> Pricing</a></li>
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
            <a href="#properties" class="btn btn-outline"><i class="fas fa-search"></i> Browse Tenant Portals</a>
        </div>
        
    </section>
    
    <!-- Tenant Carousel Section -->
    <section class="carousel-section" id="properties">
        <div class="carousel-header animate">
            <h2><i class="fas fa-store" style="color: var(--green-primary); margin-right: 10px;"></i>Featured Tenant Portals</h2>
            <p>Explore active accommodation providers in Impasugong</p>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-track" id="carouselTrack">
                @forelse(($featuredTenants ?? collect()) as $tenant)
                    @php
                        $settings = $tenant->landingSettings();
                        $tenantLogo = $tenant->getLogoUrl() ?: ($settings['hero_image_url'] ?? '/SYSTEMLOGO.png');
                        $planName = match ($tenant->plan) {
                            'pro' => 'Premium',
                            'plus' => 'Standard',
                            'basic' => 'Basic',
                            default => 'Tenant',
                        };
                    @endphp
                    <div class="carousel-slide">
                        <div class="property-card">
                            <img src="{{ $tenantLogo }}" alt="{{ $tenant->name }}" class="property-img">
                            <div class="property-content">
                                <span class="property-type"><i class="fas fa-store"></i> {{ $planName }} Portal</span>
                                <h3>{{ $tenant->name }}</h3>
                                <div class="property-location"><i class="fas fa-globe"></i> {{ $tenant->domain ?: 'localhost' }}</div>
                                <div class="property-features">
                                    <span class="feature"><i class="fas fa-calendar-check"></i> Booking Enabled</span>
                                    <span class="feature"><i class="fas fa-message"></i> Messaging {{ $tenant->feature_messaging ? 'On' : 'Off' }}</span>
                                    <span class="feature"><i class="fas fa-user"></i> Owner: {{ $tenant->owner?->name ?? 'N/A' }}</span>
                                </div>
                                <div class="property-footer">
                                    <div class="property-price">Visit Portal <span>live app</span></div>
                                    <div class="property-rating">
                                        <a href="{{ $tenant->publicUrl() }}" class="btn btn-outline" style="padding: 8px 14px; font-size: 0.85rem;">Open</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="carousel-slide" style="min-width: 100%; margin: 0;">
                        <div class="property-card" style="padding: 32px; text-align: center;">
                            <h3 style="margin-bottom: 12px;">No Tenant Portals Yet</h3>
                            <p style="color: var(--green-medium);">Tenant showcases will appear here as owners complete onboarding.</p>
                        </div>
                    </div>
                @endforelse
            </div>
            
            <div class="carousel-controls">
                <button class="carousel-btn" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
                <button class="carousel-btn" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>
    
    <!-- Pricing Section -->
    <section class="pricing-section" id="pricing">
        <div class="pricing-header animate">
            <h2><i class="fas fa-tags" style="margin-right: 10px;"></i>Pricing Plans for Property Owners</h2>
            <p>Choose a plan that fits your rental business and unlock the tools you need to grow on ImpaStay.</p>
        </div>

        <div class="pricing-grid">
            <div class="pricing-card animate delay-1">
                <h3 class="plan-name">Basic Plan</h3>
                <div class="plan-price">₱299</div>
                <ul class="plan-features">
                    <li>3 property listings</li>
                    <li>Basic reporting</li>
                    <li>Booking management</li>
                </ul>
                <a href="{{ route('register', ['role' => 'owner', 'plan' => 'basic']) }}" class="plan-register-btn">
                    Register for Basic
                </a>
            </div>

            <div class="pricing-card featured animate delay-2">
                <h3 class="plan-name">Standard Plan</h3>
                <div class="plan-price">₱499</div>
                <ul class="plan-features">
                    <li>Up to 10 listings</li>
                    <li>Advanced reporting</li>
                    <li>Analytics dashboard</li>
                </ul>
                <a href="{{ route('register', ['role' => 'owner', 'plan' => 'plus']) }}" class="plan-register-btn">
                    Register for Standard
                </a>
            </div>

            <div class="pricing-card animate delay-3">
                <h3 class="plan-name">Premium Plan</h3>
                <div class="plan-price">₱799</div>
                <ul class="plan-features">
                    <li>Unlimited listings</li>
                    <li>Priority support</li>
                    <li>Featured listing promotion</li>
                    <li>Advanced analytics</li>
                </ul>
                <a href="{{ route('register', ['role' => 'owner', 'plan' => 'pro']) }}" class="plan-register-btn">
                    Register for Premium
                </a>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <p><strong>ImpaStay</strong> | Impasugong Accommodations Platform</p>
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
