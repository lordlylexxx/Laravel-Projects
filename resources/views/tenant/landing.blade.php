<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>{{ $tenant->name }} | Accommodations</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --green-dark: {{ $settings['primary_color'] ?? '#1B5E20' }};
            --green-primary: {{ $settings['accent_color'] ?? '#2E7D32' }};
            --green-medium: color-mix(in srgb, var(--green-primary) 82%, #ffffff);
            --green-light: color-mix(in srgb, var(--green-primary) 70%, #ffffff);
            --green-pale: color-mix(in srgb, var(--green-primary) 45%, #ffffff);
            --green-soft: color-mix(in srgb, var(--green-primary) 20%, #ffffff);
            --green-white: color-mix(in srgb, var(--green-primary) 10%, #ffffff);
            --white: #FFFFFF;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.85) 50%, color-mix(in srgb, var(--green-dark) 10%, transparent) 100%),
                        url('/COMMUNAL.jpg') no-repeat center center/cover;
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--green-dark);
        }

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
            box-shadow: 0 4px 20px color-mix(in srgb, var(--green-dark) 16%, transparent);
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .brand-mark {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--white);
            background: linear-gradient(145deg, var(--green-dark), var(--green-primary));
            box-shadow: 0 8px 20px color-mix(in srgb, var(--green-primary) 30%, transparent);
            flex-shrink: 0;
        }

        .brand-logo {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            object-fit: contain;
            background: var(--white);
            box-shadow: 0 8px 20px color-mix(in srgb, var(--green-primary) 30%, transparent);
            border: 1px solid var(--green-soft);
            padding: 4px;
            flex-shrink: 0;
        }

        .nav-brand .system-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--green-dark);
            letter-spacing: -0.5px;
        }

        .nav-brand .tagline {
            font-size: 0.72rem;
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
            display: inline-flex;
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
            box-shadow: 0 4px 15px color-mix(in srgb, var(--green-primary) 35%, transparent);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px color-mix(in srgb, var(--green-primary) 45%, transparent); }

        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 120px 40px 80px;
            text-align: center;
            background: linear-gradient(135deg, color-mix(in srgb, var(--green-dark) 8%, transparent) 0%, color-mix(in srgb, var(--green-primary) 5%, transparent) 100%);
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
            box-shadow: 0 4px 15px color-mix(in srgb, var(--green-dark) 10%, transparent);
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
            max-width: 760px;
            margin-bottom: 40px;
            line-height: 1.7;
        }

        .hero-buttons { display: flex; gap: 16px; justify-content: center; margin-bottom: 0; flex-wrap: wrap; }
        .hero-buttons .btn { padding: 14px 32px; font-size: 1rem; }

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
            box-shadow: 0 8px 30px color-mix(in srgb, var(--green-dark) 14%, transparent);
            transition: all 0.4s ease;
            border: 1px solid var(--green-soft);
        }
        .property-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px color-mix(in srgb, var(--green-dark) 25%, transparent);
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

        .about {
            padding: 80px 40px;
            background: transparent;
        }

        .about-card {
            max-width: 980px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.14);
            border-radius: 20px;
            padding: 36px 30px;
            border: 1px solid rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(4px);
            color: var(--green-dark);
        }

        .about-card h2 {
            font-size: 2rem;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .about-card p {
            font-size: 1rem;
            line-height: 1.7;
            color: var(--green-dark);
        }

        .footer {
            background: var(--white);
            padding: 40px;
            text-align: center;
            border-top: 2px solid var(--green-soft);
        }
        .footer p { color: var(--green-medium); font-size: 0.9rem; }

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .animate { animation: fadeInUp 0.6s ease forwards; }
        .delay-1 { animation-delay: 0.15s; }
        .delay-2 { animation-delay: 0.3s; }

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
            .carousel-section, .about { padding: 50px 20px; }
            .carousel-slide { min-width: 280px; }
            .carousel-header h2, .about-card h2 { font-size: 1.6rem; }
        }
    </style>
</head>
<body>
    @php
        $currentUser = auth()->user();
        $tenantNavLogo = $tenant->getLogoUrl();
        $canUseTenantPortal = false;

        if ($currentUser) {
            if ($currentUser->isOwner()) {
                $canUseTenantPortal = (int) ($currentUser->tenant_id ?? 0) === (int) $tenant->id
                    || (int) optional($currentUser->ownedTenant)->id === (int) $tenant->id;
            } elseif ($currentUser->isAdmin() || $currentUser->isClient()) {
                $canUseTenantPortal = (int) ($currentUser->tenant_id ?? 0) === (int) $tenant->id;
            }
        }
    @endphp

    <nav class="navbar">
        <div class="nav-brand">
            @if(filled($tenantNavLogo))
                <img src="{{ $tenantNavLogo }}" alt="{{ $tenant->name }}" class="brand-logo" width="54" height="54"
                     onerror="this.onerror=null;this.src='{{ asset('SYSTEMLOGO.png') }}';">
            @else
                <div class="brand-mark">{{ strtoupper(substr($tenant->name, 0, 1)) }}</div>
            @endif
            <div>
                <span class="system-name">{{ $tenant->name }}</span>
                <span class="tagline">| {{ $settings['hero_subtitle'] }}</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="#properties"><i class="fas fa-building"></i> Properties</a></li>
            <li><a href="#about"><i class="fas fa-circle-info"></i> About</a></li>
        </ul>
        <div class="nav-buttons">
            @if($canUseTenantPortal)
                @if($currentUser->isClient())
                    <a href="{{ route('accommodations.index') }}" class="btn btn-outline"><i class="fas fa-search"></i> Browse</a>
                @endif
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="btn btn-primary"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            @else
                @if(auth()->check())
                    <span class="btn btn-outline" style="cursor: default;"><i class="fas fa-triangle-exclamation"></i> Wrong tenant account</span>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="btn btn-primary"><i class="fas fa-right-left"></i> Switch Account</button>
                    </form>
                @else
                    <a href="/login" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> {{ $settings['login_text'] }}</a>
                    <a href="/register" class="btn btn-primary"><i class="fas fa-user-plus"></i> {{ $settings['signup_text'] }}</a>
                @endif
            @endif
        </div>
    </nav>

    <section class="hero">
        <div class="hero-badge animate">
            <i class="fas fa-store"></i>
            <span>Welcome to {{ $tenant->name }}</span>
        </div>

        <h1 class="animate delay-1">{{ $tenant->name }} <span>Accommodations</span></h1>

        <p class="animate delay-2">{{ $settings['hero_subtitle'] }}</p>

        <div class="hero-buttons animate delay-2">
            <a href="{{ auth()->check() ? route('accommodations.index') : route('landing.browse-accommodations') }}" class="btn btn-primary"><i class="fas fa-rocket"></i> {{ $settings['cta_text'] }}</a>
            <a href="#properties" class="btn btn-outline"><i class="fas fa-search"></i> Browse Properties</a>
        </div>
    </section>

    <section class="carousel-section" id="properties">
        <div class="carousel-header animate">
            <h2><i class="fas fa-star" style="color: var(--green-primary); margin-right: 10px;"></i>Featured Accommodations</h2>
            <p>Curated stays from {{ $tenant->name }}</p>
        </div>

        <div class="carousel-container">
            <div class="carousel-track" id="carouselTrack">
                @forelse(($featuredAccommodations ?? collect()) as $accommodation)
                    @php
                        $typeIcon = match ($accommodation->type) {
                            'airbnb' => 'fas fa-home',
                            'daily-rental' => 'fas fa-calendar',
                            default => 'fas fa-bed',
                        };
                        $priceAmount = $accommodation->price_per_night ?: $accommodation->price_per_day;
                        $priceUnit = $accommodation->price_per_night ? 'night' : 'day';
                    @endphp
                    <div class="carousel-slide">
                        <div class="property-card">
                            <img src="{{ $accommodation->primary_image_url }}" alt="{{ $accommodation->name }}" class="property-img" loading="lazy">
                            <div class="property-content">
                                <span class="property-type"><i class="{{ $typeIcon }}"></i> {{ $accommodation->type_label }}</span>
                                <h3>{{ $accommodation->name }}</h3>
                                <div class="property-location"><i class="fas fa-map-marker-alt"></i> Brgy. {{ $accommodation->barangay ?: 'Impasugong' }}</div>
                                <div class="property-features">
                                    <span class="feature"><i class="fas fa-bed"></i> {{ (int) ($accommodation->bedrooms ?? 0) }} Beds</span>
                                    <span class="feature"><i class="fas fa-bath"></i> {{ (int) ($accommodation->bathrooms ?? 0) }} Baths</span>
                                    <span class="feature"><i class="fas fa-users"></i> {{ (int) ($accommodation->max_guests ?? 1) }} Guests</span>
                                </div>
                                <div class="property-footer">
                                    <div class="property-price">₱{{ number_format((float) $priceAmount, 0) }} <span>/ {{ $priceUnit }}</span></div>
                                    <div class="property-rating">
                                        <span class="stars"><i class="fas fa-star"></i></span>
                                        <span>{{ number_format((float) ($accommodation->rating ?? 0), 1) }} ({{ (int) ($accommodation->total_reviews ?? 0) }})</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="carousel-slide" style="min-width: 100%; margin: 0;">
                        <div class="property-card" style="padding: 32px; text-align: center;">
                            <h3 style="margin-bottom: 12px;">No Accommodations Yet</h3>
                            <p style="color: var(--green-medium);">This tenant has no published accommodations yet.</p>
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

    <section class="about" id="about">
        <div class="about-card animate">
            <h2>{{ $settings['about_title'] }}</h2>
            <p>{{ $settings['about_text'] }}</p>
        </div>
    </section>

    <footer class="footer">
        <p><strong>{{ $tenant->name }}</strong> | Hosted on {{ $tenant->domain }}:{{ env('CENTRAL_PORT', 8000) }}</p>
    </footer>

    <script>
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
