<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - Impasugong Accommodations</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-50: #F9FAFB; --gray-100: #F3F4F6; --gray-200: #E5E7EB;
            --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151;
            --gray-800: #1F2937;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }
        
        /* Navigation */
        .navbar {
            background: var(--white);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(27, 94, 32, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        
        .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-logo img { width: 45px; height: 45px; border-radius: 50%; border: 2px solid var(--green-primary); }
        .nav-logo span { font-size: 1.2rem; font-weight: 700; color: var(--green-dark); }
        
        .nav-links { display: flex; gap: 25px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--gray-600); font-weight: 500; padding: 8px 12px; border-radius: 8px; transition: all 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: var(--green-soft); color: var(--green-dark); }
        
        .nav-actions { display: flex; gap: 15px; align-items: center; }
        .nav-btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.3s; cursor: pointer; border: none; }
        .nav-btn.primary { background: var(--green-primary); color: var(--white); }
        .nav-btn.secondary { background: var(--green-soft); color: var(--green-dark); }
        
        /* Main Layout */
        .main-content { padding-top: 100px; padding-bottom: 40px; max-width: 1200px; margin: 0 auto; padding-left: 40px; padding-right: 40px; }
        
        /* Back Button */
        .back-link { display: inline-flex; align-items: center; gap: 8px; color: var(--green-primary); text-decoration: none; font-weight: 500; margin-bottom: 20px; transition: all 0.3s; }
        .back-link:hover { color: var(--green-dark); }
        
        /* Booking Card */
        .booking-card { background: var(--white); border-radius: 20px; box-shadow: 0 4px 30px rgba(27, 94, 32, 0.1); overflow: hidden; }
        
        .booking-header { background: linear-gradient(135deg, var(--green-dark), var(--green-primary)); padding: 30px; color: white; }
        .booking-header-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
        .booking-id { font-size: 0.9rem; opacity: 0.8; }
        .status-badge { display: inline-block; padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; font-weight: 600; background: white; color: var(--green-dark); }
        .booking-title { font-size: 1.8rem; margin-bottom: 5px; }
        .booking-date { opacity: 0.9; }
        
        .booking-body { padding: 30px; }
        
        .section-title { font-size: 1.1rem; color: var(--green-dark); margin-bottom: 15px; font-weight: 600; }
        
        /* Property Info */
        .property-info { display: flex; gap: 25px; margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid var(--gray-200); }
        .property-image { width: 200px; height: 150px; border-radius: 15px; object-fit: cover; }
        .property-details { flex: 1; }
        .property-name { font-size: 1.4rem; color: var(--gray-800); margin-bottom: 8px; font-weight: 600; }
        .property-location { display: flex; align-items: center; gap: 6px; color: var(--gray-500); margin-bottom: 15px; }
        
        /* Info Grid */
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .info-box { background: var(--cream); padding: 20px; border-radius: 15px; }
        .info-label { font-size: 0.8rem; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .info-value { font-size: 1.1rem; color: var(--gray-800); font-weight: 600; }
        
        /* Price Box */
        .price-box { background: linear-gradient(135deg, var(--green-dark), var(--green-primary)); padding: 25px; border-radius: 15px; color: white; text-align: center; }
        .price-label { font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px; }
        .price-value { font-size: 2rem; font-weight: 700; }
        
        /* Messages Section */
        .messages-section { margin-top: 30px; padding-top: 30px; border-top: 1px solid var(--gray-200); }
        .message-item { background: var(--cream); padding: 20px; border-radius: 15px; margin-bottom: 15px; }
        .message-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .message-sender { font-weight: 600; color: var(--green-dark); }
        .message-time { font-size: 0.8rem; color: var(--gray-500); }
        .message-content { color: var(--gray-700); line-height: 1.6; }
        
        /* Action Buttons */
        .action-btns { display: flex; gap: 15px; margin-top: 30px; }
        .btn { padding: 14px 28px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: var(--green-primary); color: white; }
        .btn-primary:hover { background: var(--green-dark); transform: translateY(-2px); }
        .btn-outline { background: transparent; border: 2px solid var(--green-primary); color: var(--green-primary); }
        .btn-outline:hover { background: var(--green-primary); color: white; }
        .btn-danger { background: #EF4444; color: white; }
        .btn-danger:hover { background: #DC2626; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .nav-links { display: none; }
            .main-content { padding: 100px 20px 40px; }
            .property-info { flex-direction: column; }
            .property-image { width: 100%; height: 200px; }
            .booking-header-top { flex-direction: column; gap: 15px; }
            .action-btns { flex-direction: column; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="nav-logo">
            <img src="/1.jpg" alt="Logo">
            <span>Impasugong</span>
        </a>
        
        <ul class="nav-links">
            <li><a href="{{ route('bookings.index') }}" class="active">My Bookings</a></li>
            <li><a href="{{ route('accommodations.index') }}">Properties</a></li>
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
        <a href="{{ route('bookings.index') }}" class="back-link">← Back to My Bookings</a>
        
        @if(isset($booking))
            <div class="booking-card">
                <div class="booking-header">
                    <div class="booking-header-top">
                        <div>
                            <span class="booking-id">Booking #{{ $booking->id }}</span>
                            <h1 class="booking-title">{{ $booking->accommodation->name ?? 'N/A' }}</h1>
                            <p class="booking-date">Booked on {{ $booking->created_at->format('F d, Y') }}</p>
                        </div>
                        <span class="status-badge">{{ ucfirst($booking->status) }}</span>
                    </div>
                </div>
                
                <div class="booking-body">
                    <!-- Property Info -->
                    <div class="property-info">
                        @if($booking->accommodation && $booking->accommodation->primary_image)
                            <img src="{{ asset('storage/' . $booking->accommodation->primary_image) }}" alt="{{ $booking->accommodation->name }}" class="property-image">
                        @else
                            <img src="/COMMUNAL.jpg" alt="Property" class="property-image">
                        @endif
                        
                        <div class="property-details">
                            <h2 class="property-name">{{ $booking->accommodation->name ?? 'N/A' }}</h2>
                            <div class="property-location">
                                📍 {{ $booking->accommodation->address ?? 'Impasugong, Bukidnon' }}
                            </div>
                            <p style="color: var(--gray-600); line-height: 1.6;">
                                {{ $booking->accommodation->description ?? 'No description available.' }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Booking Details -->
                    <h3 class="section-title">Booking Details</h3>
                    <div class="info-grid">
                        <div class="info-box">
                            <div class="info-label">Check-In Date</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('F d, Y') }}</div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Check-Out Date</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('F d, Y') }}</div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Number of Guests</div>
                            <div class="info-value">{{ $booking->number_of_guests ?? 1 }} Guest(s)</div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Booking Type</div>
                            <div class="info-value">{{ ucfirst(str_replace('-', ' ', $booking->accommodation->type ?? 'Standard')) }}</div>
                        </div>
                    </div>
                    
                    <!-- Pricing -->
                    <div style="display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: center;">
                        <div class="price-box" style="text-align: left;">
                            <div class="price-label">Total Amount Paid</div>
                            <div class="price-value">₱{{ number_format($booking->total_price, 2) }}</div>
                        </div>
                        
                        <div style="background: var(--cream); padding: 20px; border-radius: 15px;">
                            <div style="font-size: 0.8rem; color: var(--gray-500); margin-bottom: 5px;">Price per night</div>
                            <div style="font-size: 1.2rem; font-weight: 600; color: var(--gray-800);">
                                ₱{{ number_format($booking->accommodation->price_per_night ?? 0, 2) }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Messages Thread -->
                    @if(isset($booking->messages) && count($booking->messages) > 0)
                        <div class="messages-section">
                            <h3 class="section-title">Conversation</h3>
                            @foreach($booking->messages as $message)
                                <div class="message-item">
                                    <div class="message-header">
                                        <span class="message-sender">{{ $message->sender->name ?? 'Unknown' }}</span>
                                        <span class="message-time">{{ $message->created_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                    <div class="message-content">{{ $message->content }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    @if($booking->status == 'pending' || $booking->status == 'confirmed')
                        <div class="action-btns">
                            <a href="{{ route('bookings.cancel', $booking) }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')">
                                Cancel Booking
                            </a>
                            <a href="{{ route('messages.index') }}" class="btn btn-outline">
                                Contact Host
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="booking-card" style="padding: 60px; text-align: center;">
                <h2 style="color: var(--gray-700); margin-bottom: 15px;">Booking Not Found</h2>
                <p style="color: var(--gray-500); margin-bottom: 25px;">The booking you're looking for doesn't exist or has been removed.</p>
                <a href="{{ route('bookings.index') }}" class="btn btn-primary">Back to My Bookings</a>
            </div>
        @endif
    </main>
</body>
</html>

