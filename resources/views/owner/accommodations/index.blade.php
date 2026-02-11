<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Accommodations - Owner Dashboard</title>
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
        .dashboard-layout { display: flex; padding-top: 80px; }
        .sidebar { width: 280px; background: var(--white); min-height: calc(100vh - 80px); padding: 30px 0; box-shadow: 2px 0 20px rgba(27, 94, 32, 0.1); }
        .sidebar-title { font-size: 0.8rem; color: var(--green-medium); text-transform: uppercase; letter-spacing: 1px; padding: 0 25px; margin-bottom: 15px; }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li a { display: flex; align-items: center; gap: 15px; padding: 15px 25px; color: var(--green-dark); text-decoration: none; border-left: 4px solid transparent; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: var(--green-soft); border-left-color: var(--green-primary); }
        .sidebar-menu li a .icon { font-size: 1.3rem; }
        .main-content { flex: 1; padding: 30px 40px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 2rem; color: var(--green-dark); }
        .page-header p { color: var(--green-medium); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--white); padding: 25px; border-radius: 15px; box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1); display: flex; align-items: center; gap: 20px; }
        .stat-icon { width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; }
        .stat-icon.green { background: var(--green-soft); }
        .stat-icon.blue { background: #E3F2FD; }
        .stat-icon.orange { background: #FFF3E0; }
        .stat-info h3 { font-size: 1.8rem; color: var(--green-dark); }
        .stat-info p { color: var(--green-medium); font-size: 0.9rem; }
        .card { background: var(--white); border-radius: 15px; box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1); margin-bottom: 25px; }
        .card-header { padding: 20px 25px; border-bottom: 1px solid var(--green-soft); display: flex; justify-content: space-between; align-items: center; }
        .card-header h3 { font-size: 1.2rem; color: var(--green-dark); }
        .btn { padding: 12px 25px; border-radius: 10px; font-size: 0.95rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: var(--white); }
        .btn-secondary { background: var(--green-soft); color: var(--green-dark); }
        .btn-sm { padding: 8px 15px; font-size: 0.85rem; }
        .properties-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; padding: 25px; }
        .property-card { background: var(--white); border-radius: 15px; overflow: hidden; box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1); transition: all 0.3s ease; }
        .property-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(27, 94, 32, 0.2); }
        .property-img { width: 100%; height: 200px; object-fit: cover; }
        .property-content { padding: 20px; }
        .property-type-badge { display: inline-block; background: var(--green-soft); color: var(--green-dark); padding: 5px 12px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; margin-bottom: 10px; }
        .property-content h3 { font-size: 1.2rem; color: var(--green-dark); margin-bottom: 8px; }
        .property-location { color: var(--green-medium); font-size: 0.9rem; margin-bottom: 15px; }
        .property-features { display: flex; gap: 15px; padding-top: 15px; border-top: 1px solid var(--green-soft); margin-bottom: 15px; }
        .feature { display: flex; align-items: center; gap: 6px; color: var(--green-primary); font-size: 0.85rem; }
        .property-price { font-size: 1.4rem; font-weight: 700; color: var(--green-primary); }
        .property-price span { font-size: 0.85rem; font-weight: 400; color: var(--green-medium); }
        .property-actions { display: flex; gap: 10px; margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--green-soft); }
        .action-btn { flex: 1; padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; transition: all 0.3s; }
        .action-btn.edit { background: var(--green-soft); color: var(--green-primary); }
        .action-btn.delete { background: #FFEBEE; color: #C62828; }
        .action-btn:hover { transform: scale(1.02); }
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        .status-badge.active { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.inactive { background: #FFEBEE; color: #C62828; }
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state .icon { font-size: 4rem; margin-bottom: 20px; }
        .empty-state h3 { color: var(--green-dark); margin-bottom: 10px; }
        .empty-state p { color: var(--green-medium); margin-bottom: 20px; }
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
            <li><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('owner.accommodations.index') }}" class="active">My Properties</a></li>
            <li><a href="{{ route('bookings.index') }}">Bookings</a></li>
            <li><a href="{{ route('messages.index') }}">Messages</a></li>
        </ul>
        <div class="nav-actions" style="display: flex; gap: 15px; align-items: center;">
            <div onclick="event.preventDefault(); document.getElementById('profile-form').submit();" style="cursor: pointer;">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" 
                         alt="{{ Auth::user()->name }}" 
                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--green-primary);">
                @else
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--green-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
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
    </nav>
    
    <div class="dashboard-layout">
        <aside class="sidebar">
            <div class="sidebar-section">
                <h3 class="sidebar-title">Main Menu</h3>
                <ul class="sidebar-menu">
                    <li><a href="#"><span class="icon">📊</span> Dashboard</a></li>
                    <li><a href="#" class="active"><span class="icon">🏠</span> My Properties</a></li>
                    <li><a href="#"><span class="icon">📅</span> Bookings</a></li>
                    <li><a href="#"><span class="icon">💬</span> Messages</a></li>
                    <li><a href="#"><span class="icon">📈</span> Analytics</a></li>
                </ul>
            </div>
        </aside>
        
        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1>My Accommodations</h1>
                    <p>Manage your property listings</p>
                </div>
                <button class="btn btn-primary">+ Add New Property</button>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon green">🏠</div>
                    <div class="stat-info">
                        <h3>5</h3>
                        <p>Total Properties</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue">✅</div>
                    <div class="stat-info">
                        <h3>4</h3>
                        <p>Verified</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange">⏳</div>
                    <div class="stat-info">
                        <h3>1</h3>
                        <p>Pending Verification</p>
                    </div>
                </div>
            </div>
            
            <div class="properties-grid">
                <div class="property-card">
                    <img src="/COMMUNAL.jpg" alt="Mountain View Inn" class="property-img">
                    <div class="property-content">
                        <span class="property-type-badge">Traveller-Inn</span>
                        <h3>Mountain View Inn</h3>
                        <div class="property-location">📍 Brgy. Poblacion, Impasugong</div>
                        <div class="property-features">
                            <span class="feature">🛏️ 2 Beds</span>
                            <span class="feature">🚿 1 Bath</span>
                            <span class="feature">📶 WiFi</span>
                        </div>
                        <div class="property-price">₱1,500 <span>/ night</span></div>
                        <div style="margin-top: 10px;">
                            <span class="status-badge active">Active</span>
                        </div>
                        <div class="property-actions">
                            <button class="action-btn edit">Edit</button>
                            <button class="action-btn delete">Delete</button>
                        </div>
                    </div>
                </div>
                
                <div class="property-card">
                    <img src="/1.jpg" alt="Cozy Garden House" class="property-img">
                    <div class="property-content">
                        <span class="property-type-badge">Airbnb</span>
                        <h3>Cozy Garden House</h3>
                        <div class="property-location">📍 Brgy. Kapitan, Impasugong</div>
                        <div class="property-features">
                            <span class="feature">🛏️ 3 Beds</span>
                            <span class="feature">🚿 2 Baths</span>
                            <span class="feature">🍳 Kitchen</span>
                        </div>
                        <div class="property-price">₱2,800 <span>/ night</span></div>
                        <div style="margin-top: 10px;">
                            <span class="status-badge active">Active</span>
                        </div>
                        <div class="property-actions">
                            <button class="action-btn edit">Edit</button>
                            <button class="action-btn delete">Delete</button>
                        </div>
                    </div>
                </div>
                
                <div class="property-card">
                    <img src="/2.jpg" alt="Riverside Apartment" class="property-img">
                    <div class="property-content">
                        <span class="property-type-badge">Daily Rental</span>
                        <h3>Riverside Apartment</h3>
                        <div class="property-location">📍 Brgy. Centro, Impasugong</div>
                        <div class="property-features">
                            <span class="feature">🛏️ 1 Bed</span>
                            <span class="feature">🚿 1 Bath</span>
                            <span class="feature">📶 WiFi</span>
                        </div>
                        <div class="property-price">₱1,200 <span>/ day</span></div>
                        <div style="margin-top: 10px;">
                            <span class="status-badge active">Active</span>
                        </div>
                        <div class="property-actions">
                            <button class="action-btn edit">Edit</button>
                            <button class="action-btn delete">Delete</button>
                        </div>
                    </div>
                </div>
                
                <div class="property-card">
                    <img src="/airbnb1.jpg" alt="Forest Cabin" class="property-img">
                    <div class="property-content">
                        <span class="property-type-badge">Airbnb</span>
                        <h3>Forest Cabin Retreat</h3>
                        <div class="property-location">📍 Brgy. Malitbog, Impasugong</div>
                        <div class="property-features">
                            <span class="feature">🛏️ 4 Beds</span>
                            <span class="feature">🚿 2 Baths</span>
                            <span class="feature">🔥 Fireplace</span>
                        </div>
                        <div class="property-price">₱3,500 <span>/ night</span></div>
                        <div style="margin-top: 10px;">
                            <span class="status-badge inactive">Under Review</span>
                        </div>
                        <div class="property-actions">
                            <button class="action-btn edit">Edit</button>
                            <button class="action-btn delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

