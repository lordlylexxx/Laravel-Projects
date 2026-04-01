<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties - Impasugong Accommodations</title>
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
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-md: 0 10px 25px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.12);
        }
        
        @unless(auth()->user()?->isClient())
        /* Legacy fixed nav (non–client users on this page) */
        .navbar {
            background: var(--white);
            padding: 0 40px;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-logo img { width: 45px; height: 45px; border-radius: 0; border: none; object-fit: contain; }
        .nav-logo span { font-size: 1.3rem; font-weight: 700; color: var(--green-dark); }
        .nav-links { display: flex; gap: 8px; list-style: none; }
        .nav-links a {
            text-decoration: none;
            color: var(--gray-600);
            font-weight: 500;
            padding: 10px 18px;
            border-radius: 10px;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }
        .nav-links a:hover { background: var(--green-soft); color: var(--green-dark); }
        .nav-links a.active { background: var(--green-primary); color: var(--white); }
        .nav-actions { display: flex; gap: 12px; align-items: center; }
        .nav-btn {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }
        .nav-btn.primary { background: var(--green-primary); color: var(--white); }
        .nav-btn.primary:hover { background: var(--green-dark); transform: translateY(-1px); }
        .nav-btn.secondary { background: var(--green-soft); color: var(--green-dark); }
        .nav-btn.secondary:hover { background: var(--green-pale); }
        @endunless

        @if(auth()->user()?->isClient())
            @include('client.partials.top-navbar-styles')
        @endif

        body {
            font-family: var(--client-nav-font, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif);
            background: var(--cream);
            color: var(--gray-800);
            line-height: 1.6;
        }
        
        /* Main Content — offset matches fixed nav height */
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding-top: var(--client-nav-offset, 90px);
            padding-left: 40px;
            padding-right: 40px;
            padding-bottom: 60px;
        }
        
        /* Page Header */
        .page-header { 
            margin-bottom: 40px; 
            text-align: center;
        }
        .page-header-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }
        .page-header-logos img {
            width: 66px;
            height: 66px;
            object-fit: contain;
            border-radius: 12px;
            background: #fff;
            padding: 6px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.12);
        }
        .page-header h1 { 
            font-size: 2.2rem; 
            color: var(--green-dark); 
            margin-bottom: 8px;
            font-weight: 700;
        }
        .page-header p { 
            color: var(--gray-500); 
            font-size: 1.05rem;
        }
        
        /* Filter Bar — single horizontal row (scroll on very narrow widths) */
        .filter-bar {
            background: var(--white);
            padding: 16px 20px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            margin-bottom: 35px;
            display: flex;
            flex-wrap: nowrap;
            gap: 10px 12px;
            align-items: center;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .filter-group { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .filter-group--search {
            flex: 1 1 160px;
            min-width: 140px;
            flex-shrink: 1;
        }
        .filter-group--search .filter-input {
            width: 100%;
            min-width: 0;
        }
        .filter-group label { 
            font-size: 0.85rem; 
            font-weight: 600; 
            color: var(--gray-600);
            white-space: nowrap;
        }
        
        .filter-input {
            padding: 10px 12px;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.2s;
            min-width: 0;
        }
        .filter-input[type="number"] {
            width: 6.5rem;
            min-width: 5.5rem;
            max-width: 7rem;
        }
        .filter-input:focus { border-color: var(--green-primary); }
        
        .filter-select {
            padding: 10px 12px;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 0.9rem;
            outline: none;
            background: var(--white);
            cursor: pointer;
            transition: all 0.2s;
            min-width: 0;
        }
        .filter-group .filter-select[name="type"] {
            width: 8.5rem;
        }
        .filter-group .filter-select[name="guests"] {
            width: 6.5rem;
        }
        .filter-select:focus { border-color: var(--green-primary); }
        
        .filter-btn {
            padding: 10px 22px;
            background: var(--green-primary);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
            white-space: nowrap;
        }
        .filter-btn:hover { background: var(--green-dark); }
        
        /* Properties Grid */
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
            gap: 20px;
        }
        
        /* Property Card */
        .property-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }
        .property-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }
        
        .property-image-wrapper {
            position: relative;
            height: 170px;
            overflow: hidden;
        }
        
        .property-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .property-card:hover .property-image { transform: scale(1.05); }
        
        .property-type-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: var(--green-primary);
            color: var(--white);
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .property-favorite {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 36px;
            height: 36px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 1rem;
            box-shadow: var(--shadow);
        }
        .property-favorite:hover {
            background: var(--green-pale);
            transform: scale(1.1);
        }
        
        .property-content { padding: 16px; }
        
        .property-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--green-primary);
            margin-bottom: 8px;
        }
        .property-price span {
            font-size: 0.9rem;
            font-weight: 400;
            color: var(--gray-500);
        }
        
        .property-title {
            font-size: 1.05rem;
            color: var(--gray-800);
            margin-bottom: 10px;
            font-weight: 600;
            line-height: 1.3;
        }
        
        .property-location {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--gray-500);
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .property-location svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }
        
        .property-description {
            color: var(--gray-600);
            font-size: 0.85rem;
            line-height: 1.45;
            margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .property-features {
            display: flex;
            gap: 14px;
            padding-top: 12px;
            border-top: 1px solid var(--gray-200);
            margin-bottom: 14px;
        }
        
        .feature {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--gray-600);
            font-size: 0.82rem;
        }
        
        .feature svg {
            width: 18px;
            height: 18px;
            color: var(--green-primary);
            flex-shrink: 0;
        }
        
        .property-rating {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }
        
        .stars {
            color: #F59E0B;
            font-size: 0.88rem;
            letter-spacing: 1px;
        }
        
        .rating-count {
            color: var(--gray-500);
            font-size: 0.85rem;
        }
        
        .view-btn {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 0.92rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .view-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 125, 50, 0.35);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 40px;
            background: var(--white);
            border-radius: 20px;
            grid-column: 1 / -1;
        }
        .empty-state svg {
            width: 80px;
            height: 80px;
            color: var(--gray-300);
            margin-bottom: 20px;
        }
        .empty-state h3 {
            color: var(--gray-700);
            font-size: 1.4rem;
            margin-bottom: 10px;
        }
        .empty-state p { color: var(--gray-500); }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 50px;
        }
        .pagination a, .pagination span {
            padding: 12px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        .pagination a { background: var(--white); color: var(--gray-700); border: 1px solid var(--gray-200); }
        .pagination a:hover, .pagination a.active { background: var(--green-primary); color: var(--white); border-color: var(--green-primary); }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .properties-grid { grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); }
        }
        
        @media (max-width: 768px) {
            @unless(auth()->user()?->isClient())
            .navbar { padding: 0 20px; height: 60px; }
            .nav-logo img { width: 38px; height: 38px; }
            .nav-logo span { font-size: 1.1rem; }
            .nav-links { display: none; }
            @endunless
            .main-content {
                padding-top: calc(var(--client-nav-offset, 90px) - 10px);
                padding-left: 20px;
                padding-right: 20px;
                padding-bottom: 40px;
            }
            .page-header-logos img { width: 54px; height: 54px; }
            .page-header h1 { font-size: 1.8rem; }
            .filter-bar {
                flex-wrap: wrap;
                flex-direction: column;
                align-items: stretch;
                overflow-x: visible;
            }
            .filter-group,
            .filter-group--search {
                flex-shrink: 1;
                width: 100%;
            }
            .filter-group .filter-select[name="type"],
            .filter-group .filter-select[name="guests"] {
                width: 100%;
            }
            .filter-input[type="number"] {
                width: 100%;
                max-width: none;
            }
            .filter-btn { width: 100%; }
            .properties-grid { grid-template-columns: 1fr; }
        }

    </style>
</head>
<body>
    <!-- Navigation -->
    @if(auth()->user()?->isClient())
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
            <li><a href="{{ route('bookings.index') }}">My Bookings</a></li>
            <li><a href="{{ route('messages.index') }}">Messages</a></li>
            <li><a href="{{ route('profile.edit') }}">Settings</a></li>
        </ul>
        
        <div class="nav-actions">
            <a href="{{ route('profile.edit') }}" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: var(--green-soft); color: var(--green-dark); text-decoration: none; transition: all 0.3s;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
            </a>
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="nav-btn primary">Logout</button>
            </form>
        </div>
    </nav>
    @endif
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-logos">
                <img src="/Love%20Impasugong.png" alt="Love Impasugong Logo">
                <img src="/SYSTEMLOGO.png" alt="System Logo">
            </div>
            <h1>Find Your Perfect Stay</h1>
            <p>Discover traveller-inns, Airbnb stays, and daily rentals in Impasugong</p>
        </div>
        
        <!-- Filter Bar -->
        <form action="{{ route('accommodations.index') }}" method="GET" class="filter-bar">
            <div class="filter-group">
                <label>Type:</label>
                <select name="type" class="filter-select">
                    <option value="">All Types</option>
                    <option value="traveller-inn" {{ request('type') == 'traveller-inn' ? 'selected' : '' }}>Traveller-Inn</option>
                    <option value="airbnb" {{ request('type') == 'airbnb' ? 'selected' : '' }}>Airbnb</option>
                    <option value="daily-rental" {{ request('type') == 'daily-rental' ? 'selected' : '' }}>Daily Rental</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label>Min Price:</label>
                <input type="number" name="min_price" class="filter-input" placeholder="₱0" value="{{ request('min_price') }}">
            </div>
            
            <div class="filter-group">
                <label>Max Price:</label>
                <input type="number" name="max_price" class="filter-input" placeholder="₱10,000" value="{{ request('max_price') }}">
            </div>
            
            <div class="filter-group">
                <label>Guests:</label>
                <select name="guests" class="filter-select">
                    <option value="">Any</option>
                    <option value="1" {{ request('guests') == '1' ? 'selected' : '' }}>1 Guest</option>
                    <option value="2" {{ request('guests') == '2' ? 'selected' : '' }}>2 Guests</option>
                    <option value="3" {{ request('guests') == '3' ? 'selected' : '' }}>3 Guests</option>
                    <option value="4" {{ request('guests') == '4' ? 'selected' : '' }}>4 Guests</option>
                    <option value="5" {{ request('guests') == '5' ? 'selected' : '' }}>5+ Guests</option>
                </select>
            </div>
            
            <div class="filter-group filter-group--search">
                <input type="text" name="search" class="filter-input" placeholder="Search properties..." value="{{ request('search') }}" aria-label="Search properties">
            </div>
            
            <button type="submit" class="filter-btn">🔍 Search</button>
        </form>
        
        <!-- Properties Grid -->
        @if(isset($accommodations) && count($accommodations) > 0)
            <div class="properties-grid">
                @foreach($accommodations as $accommodation)
                    <div class="property-card">
                        <div class="property-image-wrapper">
                            @if($accommodation->primary_image)
                                <img src="{{ $accommodation->primary_image_url }}" alt="{{ $accommodation->name }}" class="property-image">
                            @else
                                <img src="/COMMUNAL.jpg" alt="{{ $accommodation->name }}" class="property-image">
                            @endif
                            <span class="property-type-badge">{{ str_replace('-', ' ', $accommodation->type) }}</span>
                            <button class="property-favorite" title="Add to favorites">♡</button>
                        </div>
                        
                        <div class="property-content">
                            <div class="property-price">₱{{ number_format($accommodation->price_per_night, 0, '.', ',') }} <span>/ night</span></div>
                            <h3 class="property-title">{{ $accommodation->name }}</h3>
                            <div class="property-location">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $accommodation->address }}, Brgy. {{ $accommodation->barangay }}
                            </div>
                            
                            <p class="property-description">{{ Str::limit($accommodation->description, 100) }}</p>
                            
                            <div class="property-features">
                                <div class="feature">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                    </svg>
                                    {{ $accommodation->bedrooms ?? 1 }} Bed
                                </div>
                                <div class="feature">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                    </svg>
                                    {{ $accommodation->bathrooms ?? 1 }} Bath
                                </div>
                                <div class="feature">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    {{ $accommodation->max_guests ?? 2 }} Guests
                                </div>
                            </div>
                            
                            <div class="property-rating">
                                <span class="stars">★★★★★</span>
                                <span class="rating-count">({{ $accommodation->total_reviews ?? 0 }} reviews)</span>
                            </div>
                            
                            <a href="{{ route('accommodations.show', $accommodation) }}" class="view-btn">View Details</a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if(isset($accommodations) && method_exists($accommodations, 'links'))
                <div class="pagination">
                    {{ $accommodations->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3>No Properties Found</h3>
                <p>Try adjusting your filters or search criteria.</p>
            </div>
        @endif
    </main>
    
    <script>
        // Favorite toggle
        document.querySelectorAll('.property-favorite').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.textContent === '♡') {
                    this.textContent = '♥';
                    this.style.color = '#dc3545';
                } else {
                    this.textContent = '♡';
                    this.style.color = '';
                }
            });
        });
    </script>
</body>
</html>
