<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties - Impasugong Accommodations</title>
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
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--cream);
            color: var(--gray-800);
            line-height: 1.6;
        }
        
        /* Fixed Navigation */
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
            z-index: 1000;
        }
        
        .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-logo img { width: 45px; height: 45px; border-radius: 50%; border: 2px solid var(--green-primary); object-fit: cover; }
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
        
        .user-avatar { 
            width: 42px; 
            height: 42px; 
            border-radius: 50%; 
            background: var(--green-primary); 
            color: white; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: 600;
            font-size: 0.95rem;
            border: 2px solid var(--white);
            box-shadow: var(--shadow);
        }
        
        /* Main Content */
        .main-content { 
            padding-top: 90px; 
            max-width: 1400px; 
            margin: 0 auto; 
            padding: 90px 40px 60px;
        }
        
        /* Page Header */
        .page-header { 
            margin-bottom: 40px; 
            text-align: center;
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
        
        /* Filter Bar */
        .filter-bar {
            background: var(--white);
            padding: 20px 30px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            margin-bottom: 35px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filter-group { display: flex; align-items: center; gap: 10px; }
        .filter-group label { 
            font-size: 0.85rem; 
            font-weight: 600; 
            color: var(--gray-600);
            white-space: nowrap;
        }
        
        .filter-input {
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s;
            min-width: 150px;
        }
        .filter-input:focus { border-color: var(--green-primary); }
        
        .filter-select {
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 0.95rem;
            outline: none;
            background: var(--white);
            cursor: pointer;
            transition: all 0.2s;
        }
        .filter-select:focus { border-color: var(--green-primary); }
        
        .filter-btn {
            padding: 12px 28px;
            background: var(--green-primary);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-left: auto;
        }
        .filter-btn:hover { background: var(--green-dark); }
        
        /* Properties Grid */
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 30px;
        }
        
        /* Property Card */
        .property-card {
            background: var(--white);
            border-radius: 20px;
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
            height: 220px;
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
            top: 15px;
            left: 15px;
            background: var(--green-primary);
            color: var(--white);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .property-favorite {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 42px;
            height: 42px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 1.2rem;
            box-shadow: var(--shadow);
        }
        .property-favorite:hover {
            background: var(--green-pale);
            transform: scale(1.1);
        }
        
        .property-content { padding: 25px; }
        
        .property-price {
            font-size: 1.6rem;
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
            font-size: 1.2rem;
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
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .property-features {
            display: flex;
            gap: 20px;
            padding-top: 18px;
            border-top: 1px solid var(--gray-200);
            margin-bottom: 20px;
        }
        
        .feature {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-600);
            font-size: 0.9rem;
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
            margin-bottom: 20px;
        }
        
        .stars {
            color: #F59E0B;
            font-size: 1rem;
            letter-spacing: 2px;
        }
        
        .rating-count {
            color: var(--gray-500);
            font-size: 0.85rem;
        }
        
        .view-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 1rem;
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
            .properties-grid { grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); }
        }
        
        @media (max-width: 768px) {
            .navbar { padding: 0 20px; height: 60px; }
            .nav-logo img { width: 38px; height: 38px; }
            .nav-logo span { font-size: 1.1rem; }
            .nav-links { display: none; }
            .main-content { padding: 80px 20px 40px; }
            .page-header h1 { font-size: 1.8rem; }
            .filter-bar { flex-direction: column; align-items: stretch; }
            .filter-btn { margin-left: 0; }
            .properties-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="{{ route('accommodations.index') }}" class="nav-logo">
            <img src="/1.jpg" alt="Logo">
            <span>Impasugong</span>
        </a>
        
        <ul class="nav-links">
            <li><a href="{{ route('accommodations.index') }}" class="active">Properties</a></li>
            <li><a href="{{ route('bookings.index') }}">My Bookings</a></li>
            <li><a href="{{ route('messages.index') }}">Messages</a></li>
        </ul>
        
        <div class="nav-actions">
            <form action="{{ route('profile.edit') }}" method="GET">
                @csrf
                <button type="submit" class="nav-btn secondary">⚙️ Settings</button>
            </form>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-btn primary">Logout</button>
            </form>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
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
            
            <div class="filter-group" style="flex: 1;">
                <input type="text" name="search" class="filter-input" placeholder="Search properties..." value="{{ request('search') }}">
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
                                <img src="{{ asset('storage/' . $accommodation->primary_image) }}" alt="{{ $accommodation->name }}" class="property-image">
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
