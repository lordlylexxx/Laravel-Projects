<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Accommodations - Impasugong</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
        }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%); min-height: 100vh; }
        .navbar { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 20px rgba(27, 94, 32, 0.1); position: fixed; width: 100%; top: 0; z-index: 1000; }
        .nav-logo { display: flex; align-items: center; gap: 15px; }
        .nav-logo img { width: 50px; height: 50px; border-radius: 50%; border: 2px solid var(--green-primary); }
        .nav-logo span { font-size: 1.3rem; font-weight: 700; color: var(--green-dark); }
        .nav-links { display: flex; gap: 30px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--green-primary); font-weight: 500; }
        .nav-links a.active { color: var(--green-dark); }
        .nav-actions { display: flex; align-items: center; gap: 20px; }
        .notification-btn { position: relative; background: none; border: none; cursor: pointer; font-size: 1.5rem; color: var(--green-primary); }
        .notification-badge { position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; font-size: 0.7rem; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--green-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        .main-content { padding-top: 90px; max-width: 1400px; margin: 0 auto; padding: 90px 40px 40px; }
        
        /* Search Section */
        .search-section { background: var(--white); padding: 30px; border-radius: 20px; box-shadow: 0 10px 40px rgba(27, 94, 32, 0.15); margin-bottom: 40px; }
        .search-row { display: flex; gap: 15px; flex-wrap: wrap; }
        .search-input-group { flex: 1; min-width: 200px; }
        .search-input-group label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--green-dark); font-size: 0.9rem; }
        .search-input-group input, .search-input-group select { width: 100%; padding: 12px 15px; border: 2px solid var(--green-soft); border-radius: 10px; font-size: 1rem; outline: none; transition: border-color 0.3s; }
        .search-input-group input:focus, .search-input-group select:focus { border-color: var(--green-primary); }
        .search-btn { padding: 12px 30px; background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: var(--white); border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s; align-self: flex-end; }
        .search-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4); }
        
        /* Filters */
        .filters-section { display: flex; gap: 15px; margin-bottom: 30px; flex-wrap: wrap; }
        .filter-chip { padding: 10px 20px; background: var(--white); border: 2px solid var(--green-soft); border-radius: 50px; cursor: pointer; transition: all 0.3s; font-weight: 500; color: var(--green-dark); }
        .filter-chip:hover, .filter-chip.active { background: var(--green-primary); border-color: var(--green-primary); color: var(--white); }
        
        /* Section Header */
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .section-header h2 { font-size: 1.8rem; color: var(--green-dark); }
        .results-count { color: var(--green-medium); }
        
        /* Properties Grid */
        .properties-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; }
        .property-card { background: var(--white); border-radius: 20px; overflow: hidden; box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1); transition: all 0.3s ease; }
        .property-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(27, 94, 32, 0.2); }
        .property-img-wrapper { position: relative; }
        .property-img { width: 100%; height: 220px; object-fit: cover; }
        .property-type-badge { position: absolute; top: 15px; left: 15px; background: var(--green-primary); color: var(--white); padding: 6px 15px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        .property-favorite { position: absolute; top: 15px; right: 15px; width: 38px; height: 38px; background: var(--white); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; border: none; font-size: 1.2rem; }
        .property-favorite:hover { background: var(--green-pale); transform: scale(1.1); }
        .property-content { padding: 20px; }
        .property-price { font-size: 1.4rem; font-weight: 700; color: var(--green-primary); margin-bottom: 8px; }
        .property-price span { font-size: 0.85rem; font-weight: 400; color: var(--green-medium); }
        .property-title { font-size: 1.1rem; color: var(--green-dark); margin-bottom: 8px; }
        .property-location { display: flex; align-items: center; gap: 6px; color: var(--green-medium); font-size: 0.9rem; margin-bottom: 15px; }
        .property-features { display: flex; gap: 15px; padding-top: 15px; border-top: 1px solid var(--green-soft); }
        .feature { display: flex; align-items: center; gap: 6px; color: var(--green-primary); font-size: 0.85rem; }
        .property-rating { display: flex; align-items: center; gap: 5px; margin-top: 10px; }
        .stars { color: #ffc107; }
        .rating-count { color: var(--green-medium); font-size: 0.85rem; }
        .book-btn { width: 100%; padding: 12px; background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: var(--white); border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s; margin-top: 15px; }
        .book-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4); }
        
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 40px; }
        .pagination button { padding: 10px 18px; border: 2px solid var(--green-soft); background: white; border-radius: 10px; cursor: pointer; transition: all 0.3s; font-weight: 500; }
        .pagination button.active { background: var(--green-primary); color: white; border-color: var(--green-primary); }
        .pagination button:hover { border-color: var(--green-primary); }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-logo">
            <a href="{{ route('landing') }}" style="display: flex; align-items: center; gap: 15px; text-decoration: none;">
                <img src="/1.jpg" alt="Municipality Logo">
                <span>Impasugong</span>
            </a>
        </div>
        <ul class="nav-links">
            <li><a href="{{ route('accommodations.index') }}" class="active">Browse</a></li>
            <li><a href="{{ route('bookings.index') }}">My Bookings</a></li>
            <li><a href="{{ route('messages.index') }}">Messages</a></li>
        </ul>
        <div class="nav-actions">
            <a href="{{ route('messages.index') }}" class="notification-btn" style="text-decoration: none;">
                🔔
                <span class="notification-badge">3</span>
            </a>
            <div class="nav-menu" style="display: flex; gap: 15px; align-items: center;">
                <div onclick="event.preventDefault(); document.getElementById('profile-form').submit();" style="cursor: pointer;">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="user-avatar" 
                             style="width: 40px; height: 40px; object-fit: cover; border: 2px solid var(--green-primary);">
                    @else
                        <div class="user-avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
                    @endif
                </div>
                <form id="profile-form" action="{{ route('profile.edit') }}" method="GET" style="display: none;"></form>
                <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();" 
                   style="padding: 8px 16px; background: var(--green-dark); color: white; border-radius: 8px; text-decoration: none; font-size: 0.9rem;">
                   Logout
                </a>
            </div>
        </div>
    </nav>
    
    <div class="main-content">
        <!-- Search Section -->
        <div class="search-section">
            <form>
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
        
        <!-- Filters -->
        <div class="filters-section">
            <button class="filter-chip active">All Properties</button>
            <button class="filter-chip">Traveller-Inn</button>
            <button class="filter-chip">Airbnb</button>
            <button class="filter-chip">Daily Rental</button>
            <button class="filter-chip">Under ₱1,000</button>
            <button class="filter-chip">₱1,000 - ₱2,000</button>
            <button class="filter-chip">Over ₱2,000</button>
        </div>
        
        <!-- Results -->
        <div class="section-header">
            <h2>Available Accommodations</h2>
            <span class="results-count">156 properties found</span>
        </div>
        
        <div class="properties-grid">
            <!-- Property 1 -->
            <div class="property-card">
                <div class="property-img-wrapper">
                    <img src="/COMMUNAL.jpg" alt="Mountain View Inn" class="property-img">
                    <span class="property-type-badge">Traveller-Inn</span>
                    <button class="property-favorite">♡</button>
                </div>
                <div class="property-content">
                    <div class="property-price">₱1,500 <span>/ night</span></div>
                    <h3 class="property-title">Mountain View Inn</h3>
                    <div class="property-location">📍 Brgy. Poblacion, Impasugong</div>
                    <div class="property-features">
                        <span class="feature">🛏️ 2 Beds</span>
                        <span class="feature">🚿 1 Bath</span>
                        <span class="feature">📶 WiFi</span>
                    </div>
                    <div class="property-rating">
                        <span class="stars">★★★★★</span>
                        <span class="rating-count</span>
                    ">(12 reviews)</div>
                    <button class="book-btn">Book Now</button>
                </div>
            </div>
            
            <!-- Property 2 -->
            <div class="property-card">
                <div class="property-img-wrapper">
                    <img src="/1.jpg" alt="Cozy Garden House" class="property-img">
                    <span class="property-type-badge">Airbnb</span>
                    <button class="property-favorite">♡</button>
                </div>
                <div class="property-content">
                    <div class="property-price">₱2,800 <span>/ night</span></div>
                    <h3 class="property-title">Cozy Garden House</h3>
                    <div class="property-location">📍 Brgy. Kapitan, Impasugong</div>
                    <div class="property-features">
                        <span class="feature">🛏️ 3 Beds</span>
                        <span class="feature">🚿 2 Baths</span>
                        <span class="feature">🍳 Kitchen</span>
                    </div>
                    <div class="property-rating">
                        <span class="stars">★★★★★</span>
                        <span class="rating-count">(8 reviews)</span>
                    </div>
                    <button class="book-btn">Book Now</button>
                </div>
            </div>
            
            <!-- Property 3 -->
            <div class="property-card">
                <div class="property-img-wrapper">
                    <img src="/2.jpg" alt="Riverside Apartment" class="property-img">
                    <span class="property-type-badge">Daily Rental</span>
                    <button class="property-favorite">♡</button>
                </div>
                <div class="property-content">
                    <div class="property-price">₱1,200 <span>/ day</span></div>
                    <h3 class="property-title">Riverside Apartment</h3>
                    <div class="property-location">📍 Brgy. Centro, Impasugong</div>
                    <div class="property-features">
                        <span class="feature">🛏️ 1 Bed</span>
                        <span class="feature">🚿 1 Bath</span>
                        <span class="feature">📶 WiFi</span>
                    </div>
                    <div class="property-rating">
                        <span class="stars">★★★★☆</span>
                        <span class="rating-count">(15 reviews)</span>
                    </div>
                    <button class="book-btn">Book Now</button>
                </div>
            </div>
            
            <!-- Property 4 -->
            <div class="property-card">
                <div class="property-img-wrapper">
                    <img src="/airbnb1.jpg" alt="Forest Cabin" class="property-img">
                    <span class="property-type-badge">Airbnb</span>
                    <button class="property-favorite">♡</button>
                </div>
                <div class="property-content">
                    <div class="property-price">₱3,500 <span>/ night</span></div>
                    <h3 class="property-title">Forest Cabin Retreat</h3>
                    <div class="property-location">📍 Brgy. Malitbog, Impasugong</div>
                    <div class="property-features">
                        <span class="feature">🛏️ 4 Beds</span>
                        <span class="feature">🚿 2 Baths</span>
                        <span class="feature">🔥 Fireplace</span>
                    </div>
                    <div class="property-rating">
                        <span class="stars">★★★★★</span>
                        <span class="rating-count">(22 reviews)</span>
                    </div>
                    <button class="book-btn">Book Now</button>
                </div>
            </div>
            
            <!-- Property 5 -->
            <div class="property-card">
                <div class="property-img-wrapper">
                    <img src="/inn1.jpg" alt="Town Inn" class="property-img">
                    <span class="property-type-badge">Traveller-Inn</span>
                    <button class="property-favorite">♡</button>
                </div>
                <div class="property-content">
                    <div class="property-price">₱800 <span>/ night</span></div>
                    <h3 class="property-title">Town Inn Basic</h3>
                    <div class="property-location">📍 Brgy. Poblacion, Impasugong</div>
                    <div class="property-features">
                        <span class="feature">🛏️ 1 Bed</span>
                        <span class="feature">🚿 1 Bath</span>
                        <span class="feature">📶 WiFi</span>
                    </div>
                    <div class="property-rating">
                        <span class="stars">★★★★☆</span>
                        <span class="rating-count">(35 reviews)</span>
                    </div>
                    <button class="book-btn">Book Now</button>
                </div>
            </div>
            
            <!-- Property 6 -->
            <div class="property-card">
                <div class="property-img-wrapper">
                    <img src="/accommodation1.jpg" alt="Villa Rosa" class="property-img">
                    <span class="property-type-badge">Daily Rental</span>
                    <button class="property-favorite">♡</button>
                </div>
                <div class="property-content">
                    <div class="property-price">₱4,000 <span>/ day</span></div>
                    <h3 class="property-title">Villa Rosa</h3>
                    <div class="property-location">📍 Brgy. Haguit, Impasugong</div>
                    <div class="property-features">
                        <span class="feature">🛏️ 5 Beds</span>
                        <span class="feature">🚿 3 Baths</span>
                        <span class="feature">🏊 Pool</span>
                    </div>
                    <div class="property-rating">
                        <span class="stars">★★★★★</span>
                        <span class="rating-count">(9 reviews)</span>
                    </div>
                    <button class="book-btn">Book Now</button>
                </div>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="pagination">
            <button><</button>
            <button class="active">1</button>
            <button>2</button>
            <button>3</button>
            <button>...</button>
            <button>26</button>
            <button>></button>
        </div>
    </div>
    
    <script>
        // Simple favorite toggle
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
        
        // Filter chips
        document.querySelectorAll('.filter-chip').forEach(chip => {
            chip.addEventListener('click', function() {
                document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>

