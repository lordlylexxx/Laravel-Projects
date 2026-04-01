<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        
        /* Image Gallery */
        .gallery-container { margin-bottom: 30px; }
        .main-image { width: 100%; height: 450px; border-radius: 20px; object-fit: cover; cursor: pointer; }
        .thumbnail-row { display: flex; gap: 15px; margin-top: 15px; overflow-x: auto; padding-bottom: 10px; }
        .thumbnail { width: 120px; height: 80px; border-radius: 10px; object-fit: cover; cursor: pointer; opacity: 0.6; transition: all 0.3s; border: 3px solid transparent; flex-shrink: 0; }
        .thumbnail:hover, .thumbnail.active { opacity: 1; border-color: var(--green-primary); }
        
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
                <li><a href="{{ route('bookings.index') }}">My Bookings</a></li>
                <li><a href="{{ route('messages.index') }}">Messages</a></li>
                <li><a href="{{ route('profile.edit') }}">Settings</a></li>
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
        
        <!-- Image Gallery -->
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

                $primaryImageUrl = $galleryImages[0] ?? '/COMMUNAL.jpg';
            @endphp
            <img src="{{ $primaryImageUrl }}" alt="{{ $accommodation->name }}" class="main-image" id="mainImage">
            @if(count($galleryImages) > 1)
                <div class="thumbnail-row">
                    @foreach($galleryImages as $index => $imageUrl)
                        <img src="{{ $imageUrl }}" 
                             alt="{{ $accommodation->name }}" 
                             class="thumbnail {{ $index === 0 ? 'active' : '' }}"
                             onclick="changeImage(this, '{{ $imageUrl }}')">
                    @endforeach
                </div>
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
                        $canBookAccommodation = $authUser && $authUser->isClient() && (int) $accommodation->owner_id !== (int) $authUser->id;
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
                            
                            <div class="form-group">
                                <label>Special Requests (Optional)</label>
                                <textarea name="special_requests" rows="3" placeholder="Any special requests..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-book">
                                Continue to Payment
                            </button>
                        </form>
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
        function changeImage(thumbnail, imageUrl) {
            document.getElementById('mainImage').src = imageUrl;
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
        }
    </script>
</body>
</html>
