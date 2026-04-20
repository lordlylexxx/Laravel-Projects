<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>{{ $accommodation->name }} - Impasugong Accommodations</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @php
            $authUser = auth()->user();
            $currentTenant = \App\Models\Tenant::current();
            $isTenantManager = $authUser && (
                $authUser->isOwner()
                || ($authUser->isAdmin() && $currentTenant && ((int) $authUser->tenant_id === (int) $currentTenant->id || $authUser->tenant_id === null))
            );
        @endphp
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-50: #F9FAFB; --gray-100: #F3F4F6; --gray-200: #E5E7EB;
            --gray-300: #D1D5DB; --gray-400: #9CA3AF; --gray-500: #6B7280;
            --gray-600: #4B5563; --gray-700: #374151; --gray-800: #1F2937;
            --blue-500: #3B82F6;
            --orange-500: #F97316;
        }
        
        @unless($isTenantManager || $authUser?->isClient())
        /* Legacy fixed nav (guest / non–client) */
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
        @endunless

        @if($isTenantManager)
            @include('owner.partials.top-navbar-styles')
        @elseif($authUser?->isClient())
            @include('client.partials.top-navbar-styles')
        @endif

        body {
            font-family: var(--client-nav-font, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif);
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }
        
        .btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.3s; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: var(--green-primary); color: var(--white); }
        .btn-primary:hover { background: var(--green-dark); }
        .btn-secondary { background: var(--green-soft); color: var(--green-dark); }
        
        /* Main Container */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding-top: var(--client-nav-offset, 90px);
            padding-left: 20px;
            padding-right: 20px;
            padding-bottom: 40px;
        }
        
        /* Breadcrumb */
        .breadcrumb { display: flex; gap: 10px; margin-bottom: 20px; font-size: 0.9rem; }
        .breadcrumb a { color: var(--green-primary); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { color: var(--gray-500); }
        
        /* Image Gallery — carousel + lightbox */
        .gallery-container { margin-bottom: 30px; }
        .gallery-carousel {
            position: relative;
        }
        .carousel-main-wrap {
            position: relative;
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(27, 94, 32, 0.12);
        }
        .main-image {
            width: 100%;
            height: 450px;
            border-radius: 20px;
            object-fit: cover;
            cursor: zoom-in;
            display: block;
        }
        .main-image:focus {
            outline: 3px solid var(--green-primary);
            outline-offset: 2px;
        }
        .carousel-hint {
            position: absolute;
            bottom: 14px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(27, 94, 32, 0.88);
            color: var(--white);
            font-size: 0.78rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 999px;
            pointer-events: none;
            opacity: 0.95;
        }
        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: none;
            background: var(--white);
            color: var(--green-dark);
            box-shadow: 0 2px 12px rgba(27, 94, 32, 0.15);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            transition: background 0.2s, transform 0.2s, opacity 0.2s;
        }
        .carousel-btn.prev { left: 12px; }
        .carousel-btn.next { right: 12px; }
        .carousel-btn:hover:not(:disabled) {
            background: var(--green-soft);
            transform: translateY(-50%) scale(1.05);
        }
        .carousel-btn:disabled {
            opacity: 0.35;
            cursor: not-allowed;
            transform: translateY(-50%);
        }
        .carousel-counter {
            position: absolute;
            top: 14px;
            right: 14px;
            background: rgba(255, 255, 255, 0.92);
            color: var(--green-dark);
            font-size: 0.82rem;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 999px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            pointer-events: none;
        }
        .thumbnail-row { display: flex; gap: 15px; margin-top: 15px; overflow-x: auto; padding-bottom: 10px; scroll-snap-type: x mandatory; }
        .thumbnail { width: 120px; height: 80px; border-radius: 10px; object-fit: cover; cursor: pointer; opacity: 0.6; transition: all 0.3s; border: 3px solid transparent; flex-shrink: 0; scroll-snap-align: start; }
        .thumbnail:hover, .thumbnail.active { opacity: 1; border-color: var(--green-primary); }

        /* Lightbox */
        .lightbox {
            position: fixed;
            inset: 0;
            z-index: 10000;
            background: rgba(15, 23, 42, 0.92);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 56px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }
        .lightbox.is-open {
            opacity: 1;
            visibility: visible;
        }
        .lightbox-img {
            max-width: 100%;
            max-height: calc(100vh - 96px);
            width: auto;
            height: auto;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.45);
        }
        .lightbox-close {
            position: absolute;
            top: 16px;
            right: 20px;
            width: 44px;
            height: 44px;
            border: none;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
            transition: background 0.2s;
        }
        .lightbox-close:hover { background: rgba(255, 255, 255, 0.28); }
        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
            border: none;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .lightbox-nav:hover { background: rgba(255, 255, 255, 0.3); }
        .lightbox-nav.prev { left: 16px; }
        .lightbox-nav.next { right: 16px; }
        .lightbox-caption {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.9rem;
            text-align: center;
            max-width: 90%;
        }
        
        /* Content Grid */
        .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        
        /* Info Card */
        .info-card {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
            margin-bottom: 25px;
        }
        
        .property-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .property-header h1 { font-size: 1.8rem; color: var(--green-dark); margin-bottom: 8px; }
        .property-location { display: flex; align-items: center; gap: 8px; color: var(--gray-500); font-size: 1rem; margin-bottom: 10px; }
        .rating { display: flex; align-items: center; gap: 8px; }
        .rating-stars { color: #FFC107; font-size: 1.1rem; }
        .rating-value { font-weight: 600; color: var(--gray-700); }
        .rating-count { color: var(--gray-500); font-size: 0.9rem; }
        
        .type-badge {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        .type-badge.traveller-inn { background: #E3F2FD; color: #1565C0; }
        .type-badge.airbnb { background: #FFF3E0; color: #E65100; }
        .type-badge.daily-rental { background: #D1FAE5; color: #065F46; }
        
        .section-title { font-size: 1.2rem; color: var(--green-dark); margin-bottom: 15px; font-weight: 600; }
        
        .description { color: var(--gray-600); line-height: 1.8; margin-bottom: 25px; }
        
        /* Features Grid */
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .feature-item { display: flex; align-items: center; gap: 12px; padding: 15px; background: var(--cream); border-radius: 12px; }
        .feature-icon { font-size: 1.5rem; }
        .feature-text h4 { font-size: 0.85rem; color: var(--gray-500); margin-bottom: 3px; }
        .feature-text p { font-weight: 600; color: var(--gray-800); }
        
        /* Amenities */
        .amenities-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px; }
        .amenity-item { display: flex; align-items: center; gap: 10px; padding: 12px 15px; background: var(--green-white); border-radius: 10px; }
        .amenity-item span { color: var(--green-primary); font-size: 1.2rem; }
        
        /* Map Section */
        
        /* Booking Card */
        .booking-card {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
            position: sticky;
            top: 100px;
        }
        
        .price-display { margin-bottom: 25px; }
        .price-display .amount { font-size: 2rem; font-weight: 700; color: var(--green-dark); }
        .price-display .period { color: var(--gray-500); font-size: 1rem; }
        
        /* Booking Form */
        .booking-form .form-group { margin-bottom: 15px; }
        .booking-form label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--gray-700); font-size: 0.9rem; }
        .booking-form input, .booking-form select, .booking-form textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        .booking-form input:focus, .booking-form select:focus, .booking-form textarea:focus {
            outline: none;
            border-color: var(--green-primary);
        }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        
        .price-breakdown { background: var(--cream); border-radius: 12px; padding: 20px; margin: 20px 0; }
        .price-row { display: flex; justify-content: space-between; margin-bottom: 10px; color: var(--gray-600); }
        .price-row.total { border-top: 1px solid var(--gray-300); padding-top: 10px; margin-top: 10px; font-weight: 700; color: var(--gray-800); font-size: 1.1rem; }
        
        .btn-book { width: 100%; padding: 15px; font-size: 1.1rem; justify-content: center; margin-bottom: 15px; }
        .btn-wishlist { width: 100%; justify-content: center; background: transparent; border: 2px solid var(--gray-300); color: var(--gray-600); }
        .btn-wishlist:hover { border-color: var(--red-500); color: var(--red-500); }
        
        .host-info { display: flex; align-items: center; gap: 15px; padding: 20px 0; border-top: 1px solid var(--gray-200); margin-top: 20px; }
        .host-avatar { width: 50px; height: 50px; border-radius: 50%; background: var(--green-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 600; }
        .host-details h4 { color: var(--gray-800); margin-bottom: 3px; }
        .host-details p { color: var(--gray-500); font-size: 0.85rem; }
        
        /* House Rules */
        .rules-list { list-style: none; }
        .rules-list li { display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--gray-200); }
        .rules-list li:last-child { border-bottom: none; }
        .rules-list span { color: var(--green-primary); font-size: 1.1rem; }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .content-grid { grid-template-columns: 1fr; }
            .booking-card { position: static; }
        }
        
        @media (max-width: 768px) {
            @unless($isTenantManager || $authUser?->isClient())
            .navbar { padding: 0 20px; height: 60px; }
            .nav-links { display: none; }
            @endunless
            .main-container { padding-top: calc(var(--client-nav-offset, 90px) - 10px); }
            .main-image { height: 300px; }
            .carousel-btn { width: 40px; height: 40px; font-size: 1rem; }
            .carousel-btn.prev { left: 8px; }
            .carousel-btn.next { right: 8px; }
            .lightbox { padding: 56px 12px 72px; }
            .lightbox-nav.prev { left: 6px; }
            .lightbox-nav.next { right: 6px; }
            .form-row { grid-template-columns: 1fr; }
            .property-header { flex-direction: column; gap: 15px; }
        }

        /* Animations */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate { animation: fadeInUp 0.6s ease forwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
    </style>
</head>
<body class="{{ $isTenantManager ? 'owner-nav-page' : '' }}">
    <!-- Navigation -->
    @if($isTenantManager)
    @include('owner.partials.top-navbar', ['active' => 'accommodations'])
    @elseif(auth()->user()?->isClient())
    @include('client.partials.top-navbar', ['active' => 'accommodations'])
    @else
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="nav-logo">
            <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo">
            <span>Impasugong</span>
        </a>
        
        <ul class="nav-links">
            <li><a href="{{ route('dashboard') }}">Browse</a></li>
            <li><a href="{{ route('accommodations.index') }}" class="active">Accommodations</a></li>
            @auth
                @if(Auth::user()->role === 'owner')
                    <li><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                @elseif(Auth::user()->role === 'admin')
                    <li><a href="{{ \App\Models\Tenant::checkCurrent() ? '/owner/dashboard' : '/admin/dashboard' }}">Dashboard</a></li>
                @elseif(Auth::user()->role !== 'client')
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @endif
                @if(Auth::user()->tenantClientMayManageOwnStays())
                <li><a href="{{ route('bookings.index') }}">My Bookings</a></li>
                @endif
                @if(Auth::user()->tenantClientMayUseMessaging())
                    <li><a href="{{ route('messages.index', [], false) }}">Messages</a></li>
                @endif
                @if(Auth::user()->tenantClientMayEditOwnProfile())
                <li><a href="{{ route('profile.edit') }}">Settings</a></li>
                @endif
            @endauth
        </ul>
        
        @auth
        <div class="nav-actions">
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </div>
        @else
        <div class="nav-actions">
            <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
        </div>
        @endauth
    </nav>
    @endif
    
    <!-- Main Container -->
    <div class="main-container {{ $isTenantManager ? 'with-owner-nav' : '' }}">
        <!-- Breadcrumb -->
        <div class="breadcrumb animate">
            <a href="{{ route('landing') }}">Home</a>
            <span>›</span>
            <a href="{{ route('accommodations.index') }}">Accommodations</a>
            <span>›</span>
            <span>{{ $accommodation->name }}</span>
        </div>
        
        <!-- Image Gallery (carousel + lightbox) -->
        <div class="gallery-container animate delay-1">
            @php
                $images = is_array($accommodation->images) ? $accommodation->images : [];
                $galleryImages = collect($images)
                    ->filter(fn ($image) => is_string($image) && trim($image) !== '')
                    ->values()
                    ->all();

                if ($accommodation->primary_image) {
                    array_unshift($galleryImages, $accommodation->primary_image);
                }

                $galleryImages = collect($galleryImages)
                    ->map(fn ($image) => '/storage/' . ltrim((string) $image, '/'))
                    ->unique()
                    ->values()
                    ->all();

                $galleryCount = count($galleryImages);
                if ($galleryCount === 0) {
                    $galleryImages = ['/COMMUNAL.jpg'];
                    $galleryCount = 1;
                }
                $primaryImageUrl = $galleryImages[0];
            @endphp
            <script type="application/json" id="accommodation-gallery-data">@json($galleryImages)</script>
            <div class="gallery-carousel" id="accommodationGallery">
                @if($galleryCount > 1)
                    <button type="button" class="carousel-btn prev" id="carouselPrev" aria-label="Previous photo">‹</button>
                    <button type="button" class="carousel-btn next" id="carouselNext" aria-label="Next photo">›</button>
                @endif
                <div class="carousel-main-wrap">
                    @if($galleryCount > 1)
                        <span class="carousel-counter" id="carouselCounter" aria-live="polite">1 / {{ $galleryCount }}</span>
                    @endif
                    <img src="{{ $galleryImages[0] }}"
                         alt="{{ $accommodation->name }}"
                         class="main-image"
                         id="carouselMain"
                         data-accommodation-name="{{ e($accommodation->name) }}"
                         tabindex="0"
                         role="button"
                         aria-label="View full size photo. Use arrow keys to change slide when focused.">
                    <span class="carousel-hint">Click for full size</span>
                </div>
            </div>
            @if($galleryCount > 1)
                <div class="thumbnail-row" id="carouselThumbnails" role="tablist" aria-label="Photo thumbnails">
                    @foreach($galleryImages as $index => $imageUrl)
                        <img src="{{ $imageUrl }}" 
                             alt=""
                             class="thumbnail {{ $index === 0 ? 'active' : '' }}"
                             data-index="{{ $index }}"
                             role="tab"
                             tabindex="0"
                             aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                             aria-label="Show photo {{ $index + 1 }} of {{ $galleryCount }}">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="lightbox" id="photoLightbox" role="dialog" aria-modal="true" aria-label="Full size photos">
            <button type="button" class="lightbox-close" id="lightboxClose" aria-label="Close full screen photo">×</button>
            @if($galleryCount > 1)
                <button type="button" class="lightbox-nav prev" id="lightboxPrev" aria-label="Previous photo">‹</button>
                <button type="button" class="lightbox-nav next" id="lightboxNext" aria-label="Next photo">›</button>
            @endif
            <img src="" alt="" class="lightbox-img" id="lightboxImg">
            @if($galleryCount > 1)
                <p class="lightbox-caption" id="lightboxCaption"></p>
            @endif
        </div>
        
        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Left Column -->
            <div>
                <!-- Property Info -->
                <div class="info-card animate delay-2">
                    <div class="property-header">
                        <div>
                            <span class="type-badge {{ $accommodation->type }}">{{ str_replace('-', ' ', ucfirst($accommodation->type)) }}</span>
                            <h1>{{ $accommodation->name }}</h1>
                            <div class="property-location">📍 {{ $accommodation->address }}</div>
                            <div class="rating">
                                <span class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($accommodation->rating))
                                            ★
                                        @elseif($i - 0.5 <= $accommodation->rating)
                                            ★
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </span>
                                <span class="rating-value">{{ number_format($accommodation->rating, 1) }}</span>
                                <span class="rating-count">({{ $accommodation->total_reviews }} reviews)</span>
                            </div>
                        </div>
                    </div>
                    
                    <p class="description">{{ $accommodation->description }}</p>
                    
                    <!-- Features -->
                    <h3 class="section-title">Property Details</h3>
                    <div class="features-grid">
                        <div class="feature-item">
                            <span class="feature-icon">🛏️</span>
                            <div class="feature-text">
                                <h4>Bedrooms</h4>
                                <p>{{ $accommodation->bedrooms }}</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">🚿</span>
                            <div class="feature-text">
                                <h4>Bathrooms</h4>
                                <p>{{ $accommodation->bathrooms }}</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">👥</span>
                            <div class="feature-text">
                                <h4>Max Guests</h4>
                                <p>{{ $accommodation->max_guests }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Amenities -->
                <div class="info-card animate delay-2">
                    <h3 class="section-title">Amenities</h3>
                    <div class="amenities-grid">
                        @if(is_array($accommodation->amenities))
                            @foreach($accommodation->amenities as $amenity)
                                <div class="amenity-item">
                                    <span>✓</span>
                                    <span>{{ $amenity }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <!-- House Rules -->
                <div class="info-card animate delay-2">
                    <h3 class="section-title">House Rules</h3>
                    <ul class="rules-list">
                        @if($accommodation->house_rules)
                            @foreach(explode('.', $accommodation->house_rules) as $rule)
                                @if(trim($rule))
                                    <li><span>•</span> {{ trim($rule) }}</li>
                                @endif
                            @endforeach
                        @else
                            <li><span>•</span> Standard house rules apply</li>
                            <li><span>•</span> No smoking inside the property</li>
                            <li><span>•</span> Pets allowed with prior notice</li>
                            <li><span>•</span> Check-in: 2PM | Check-out: 11AM</li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <!-- Right Column - Booking Card -->
            <div>
                <div class="booking-card animate delay-3">
                    <div class="price-display">
                        <span class="amount">₱{{ number_format($accommodation->price_per_night, 0, '.', ',') }}</span>
                        <span class="period">/ night</span>
                        @if($accommodation->price_per_day)
                            <span class="period" style="margin-left: 10px;">or ₱{{ number_format($accommodation->price_per_day, 0, '.', ',') }}/day</span>
                        @endif
                    </div>
                    
                    @php
                        $authUser = auth()->user();
                        $canBookAccommodation = $authUser && $authUser->isClient() && (int) $accommodation->owner_id !== (int) $authUser->id && $authUser->tenantClientMayManageOwnStays();
                    @endphp

                    @auth
                        @if($canBookAccommodation)
                        <form class="booking-form" method="POST" action="{{ route('accommodations.book', $accommodation) }}">
                            @csrf
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Check-in</label>
                                    <input type="date" name="check_in_date" min="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Check-out</label>
                                    <input type="date" name="check_out_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Guests</label>
                                <select name="number_of_guests" required>
                                    @for($i = 1; $i <= $accommodation->max_guests; $i++)
                                        <option value="{{ $i }}">{{ $i }} Guest{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Guest Gender (Optional)</label>
                                    <select name="guest_gender">
                                        <option value="">Prefer not to say</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="unspecified">Unspecified</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Guest Age (Optional)</label>
                                    <input type="number" name="guest_age" min="0" max="120" placeholder="e.g. 28">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Guest Location Type (Optional)</label>
                                <select name="guest_is_local" id="guestIsLocalSelect">
                                    <option value="">Select location type</option>
                                    <option value="1">Local</option>
                                    <option value="0">Foreign</option>
                                </select>
                            </div>

                            <div class="form-group" id="guestLocalPlaceGroup" style="display:none;">
                                <label>Local Place (Optional)</label>
                                <input type="text" name="guest_local_place" id="guestLocalPlaceInput" placeholder="e.g. Bukidnon">
                            </div>

                            <div class="form-group" id="guestCountryGroup" style="display:none;">
                                <label>Country (Optional)</label>
                                <input type="text" name="guest_country" id="guestCountryInput" placeholder="e.g. Japan">
                            </div>
                            
                            <div class="form-group">
                                <label>Special Requests (Optional)</label>
                                <textarea name="special_requests" rows="3" placeholder="Any special requests..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-book">
                                Continue to Payment
                            </button>
                        </form>
                        @elseif($authUser && $authUser->isClient() && (int) $accommodation->owner_id !== (int) $authUser->id)
                            <div style="text-align: center; padding: 16px 0 10px; color: var(--gray-600); font-weight: 600;">
                                Booking is disabled for your account. Contact the business if you need access.
                            </div>
                        @else
                            <div style="text-align: center; padding: 16px 0 10px; color: var(--gray-600); font-weight: 600;">
                                Booking is available for client accounts only.
                            </div>
                        @endif

                        <button class="btn btn-wishlist">
                            ❤️ Add to Wishlist
                        </button>

                        <div class="host-info">
                            <div class="host-avatar">{{ substr($accommodation->owner->name ?? 'HO', 0, 2) }}</div>
                            <div class="host-details">
                                <h4>{{ $accommodation->owner->name ?? 'Host' }}</h4>
                                <p>Property Owner</p>
                            </div>
                        </div>
                    @else
                        <div style="text-align: center; padding: 30px 0;">
                            <p style="color: var(--gray-500); margin-bottom: 20px;">Please login to book this property</p>
                            <a href="{{ route('login') }}" class="btn btn-primary btn-book">Login to Book</a>
                            <a href="{{ route('register') }}" class="btn btn-wishlist">Create Account</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
    
    <script>
        (function () {
            var dataEl = document.getElementById('accommodation-gallery-data');
            if (!dataEl) return;

            var urls = [];
            try {
                urls = JSON.parse(dataEl.textContent || '[]');
            } catch (e) {
                return;
            }
            if (!urls.length) return;

            var main = document.getElementById('carouselMain');
            var prevBtn = document.getElementById('carouselPrev');
            var nextBtn = document.getElementById('carouselNext');
            var counter = document.getElementById('carouselCounter');
            var thumbs = document.querySelectorAll('#carouselThumbnails .thumbnail');
            var lightbox = document.getElementById('photoLightbox');
            var lightboxImg = document.getElementById('lightboxImg');
            var lightboxClose = document.getElementById('lightboxClose');
            var lightboxPrev = document.getElementById('lightboxPrev');
            var lightboxNext = document.getElementById('lightboxNext');
            var lightboxCaption = document.getElementById('lightboxCaption');

            var n = urls.length;
            var i = 0;
            var accName = main ? (main.getAttribute('data-accommodation-name') || 'Photo') : 'Photo';

            var AUTOPLAY_MS = 5000;
            var autoTimer = null;
            var galleryHoverPause = false;

            function clampIndex(x) {
                if (n <= 0) return 0;
                var r = x % n;
                return r < 0 ? r + n : r;
            }

            function showSlide(index) {
                i = clampIndex(index);
                if (main) {
                    main.src = urls[i];
                    main.alt = accName + ' — photo ' + (i + 1) + ' of ' + n;
                }
                if (counter) counter.textContent = (i + 1) + ' / ' + n;
                thumbs.forEach(function (t, idx) {
                    var on = idx === i;
                    t.classList.toggle('active', on);
                    t.setAttribute('aria-selected', on ? 'true' : 'false');
                });
                if (prevBtn) prevBtn.disabled = n <= 1;
                if (nextBtn) nextBtn.disabled = n <= 1;
            }

            function stopAutoplay() {
                if (autoTimer) {
                    clearInterval(autoTimer);
                    autoTimer = null;
                }
            }

            function autoplayShouldAdvance() {
                if (n <= 1) return false;
                if (document.hidden) return false;
                if (lightbox && lightbox.classList.contains('is-open')) return false;
                if (galleryHoverPause) return false;
                return true;
            }

            function startAutoplay() {
                stopAutoplay();
                if (n <= 1) return;
                autoTimer = setInterval(function () {
                    if (autoplayShouldAdvance()) step(1, true);
                }, AUTOPLAY_MS);
            }

            /** @param {boolean} [fromTimer] when true, skip restarting the interval */
            function step(delta, fromTimer) {
                showSlide(i + delta);
                if (lightbox && lightbox.classList.contains('is-open') && lightboxImg) {
                    lightboxImg.src = urls[i];
                    lightboxImg.alt = accName + ' — full size, photo ' + (i + 1) + ' of ' + n;
                    if (lightboxCaption) lightboxCaption.textContent = n > 1 ? 'Photo ' + (i + 1) + ' of ' + n : '';
                }
                if (!fromTimer && n > 1 && (!lightbox || !lightbox.classList.contains('is-open'))) {
                    startAutoplay();
                }
            }

            function openLightbox() {
                if (!lightbox || !lightboxImg) return;
                stopAutoplay();
                lightboxImg.src = urls[i];
                lightboxImg.alt = accName + ' — full size, photo ' + (i + 1) + ' of ' + n;
                if (lightboxCaption) lightboxCaption.textContent = n > 1 ? 'Photo ' + (i + 1) + ' of ' + n : '';
                lightbox.classList.add('is-open');
                document.body.style.overflow = 'hidden';
                if (lightboxClose) lightboxClose.focus();
            }

            function closeLightbox() {
                if (!lightbox) return;
                lightbox.classList.remove('is-open');
                document.body.style.overflow = '';
                if (lightboxImg) lightboxImg.src = '';
                startAutoplay();
            }

            if (prevBtn) prevBtn.addEventListener('click', function () { step(-1); });
            if (nextBtn) nextBtn.addEventListener('click', function () { step(1); });

            thumbs.forEach(function (thumb) {
                thumb.addEventListener('click', function () {
                    var idx = parseInt(thumb.getAttribute('data-index'), 10);
                    if (!isNaN(idx)) {
                        showSlide(idx);
                        if (!lightbox || !lightbox.classList.contains('is-open')) startAutoplay();
                    }
                });
                thumb.addEventListener('keydown', function (ev) {
                    if (ev.key === 'Enter' || ev.key === ' ') {
                        ev.preventDefault();
                        var idx = parseInt(thumb.getAttribute('data-index'), 10);
                        if (!isNaN(idx)) {
                            showSlide(idx);
                            if (!lightbox || !lightbox.classList.contains('is-open')) startAutoplay();
                        }
                    }
                });
            });

            if (main) {
                main.addEventListener('click', openLightbox);
                main.addEventListener('keydown', function (ev) {
                    if (ev.key === 'Enter' || ev.key === ' ') {
                        ev.preventDefault();
                        openLightbox();
                    }
                    if (n > 1 && ev.key === 'ArrowLeft') {
                        ev.preventDefault();
                        step(-1);
                    }
                    if (n > 1 && ev.key === 'ArrowRight') {
                        ev.preventDefault();
                        step(1);
                    }
                });
            }

            if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
            if (lightboxPrev) lightboxPrev.addEventListener('click', function () { step(-1); });
            if (lightboxNext) lightboxNext.addEventListener('click', function () { step(1); });

            if (lightbox) {
                lightbox.addEventListener('click', function (ev) {
                    if (ev.target === lightbox) closeLightbox();
                });
            }

            document.addEventListener('keydown', function (ev) {
                if (!lightbox || !lightbox.classList.contains('is-open')) return;
                if (ev.key === 'Escape') {
                    ev.preventDefault();
                    closeLightbox();
                }
                if (n > 1 && ev.key === 'ArrowLeft') {
                    ev.preventDefault();
                    step(-1, false);
                }
                if (n > 1 && ev.key === 'ArrowRight') {
                    ev.preventDefault();
                    step(1, false);
                }
            });

            document.addEventListener('visibilitychange', function () {
                if (document.hidden) stopAutoplay();
                else if (n > 1 && (!lightbox || !lightbox.classList.contains('is-open'))) startAutoplay();
            });

            var galleryWrap = document.querySelector('.gallery-container');
            if (galleryWrap && n > 1) {
                galleryWrap.addEventListener('mouseenter', function () {
                    galleryHoverPause = true;
                });
                galleryWrap.addEventListener('mouseleave', function () {
                    galleryHoverPause = false;
                });
            }

            /* Touch swipe on main image */
            if (main && n > 1) {
                var touchStartX = null;
                main.addEventListener('touchstart', function (ev) {
                    touchStartX = ev.changedTouches[0].screenX;
                }, { passive: true });
                main.addEventListener('touchend', function (ev) {
                    if (touchStartX === null) return;
                    var dx = ev.changedTouches[0].screenX - touchStartX;
                    touchStartX = null;
                    if (Math.abs(dx) < 50) return;
                    if (dx < 0) step(1);
                    else step(-1);
                }, { passive: true });
            }

            showSlide(0);
            startAutoplay();
        })();

        (function () {
            var locationType = document.getElementById('guestIsLocalSelect');
            var localGroup = document.getElementById('guestLocalPlaceGroup');
            var countryGroup = document.getElementById('guestCountryGroup');
            var localInput = document.getElementById('guestLocalPlaceInput');
            var countryInput = document.getElementById('guestCountryInput');
            if (!locationType || !localGroup || !countryGroup || !localInput || !countryInput) return;

            function syncLocationFields() {
                var value = locationType.value;
                if (value === '1') {
                    localGroup.style.display = '';
                    countryGroup.style.display = 'none';
                    localInput.required = true;
                    countryInput.required = false;
                    countryInput.value = '';
                    return;
                }
                if (value === '0') {
                    localGroup.style.display = 'none';
                    countryGroup.style.display = '';
                    localInput.required = false;
                    countryInput.required = true;
                    localInput.value = '';
                    return;
                }

                localGroup.style.display = 'none';
                countryGroup.style.display = 'none';
                localInput.required = false;
                countryInput.required = false;
                localInput.value = '';
                countryInput.value = '';
            }

            locationType.addEventListener('change', syncLocationFields);
            syncLocationFields();
        })();
    </script>
</body>
</html>
