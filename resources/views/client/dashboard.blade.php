<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Client Dashboard - ImpaStay</title>
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
            --blue-500: #3B82F6; --orange-500: #F97316; --purple-500: #8B5CF6;
            --amber-500: #F59E0B;
        }

        @include('client.partials.top-navbar-styles')
        
        body {
            font-family: var(--client-nav-font, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif);
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }
        
        /* No padding-top on .main-content — it showed body gradient between nav and hero.
           Offset is inside .hero so the hero background fills flush under the fixed bar. */
        .main-content { padding-top: 0; }
        
        /* Hero Section — overflow must not be hidden or drop-shadows clip (logos look cropped) */
        .hero {
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            padding: calc(var(--client-nav-offset, 108px) + 52px) 40px 64px;
            color: var(--white);
            position: relative;
            overflow: visible;
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('/COMMUNAL.jpg') no-repeat center center/cover;
            opacity: 0.1;
            pointer-events: none;
        }
        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            text-align: center;
        }
        .hero-logos {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: clamp(12px, 2.5vw, 24px);
            margin-bottom: 8px;
            padding: 6px 0 4px;
            min-height: 150px;
        }
        .hero-logos img {
            display: block;
            width: 150px;
            height: 150px;
            max-width: 150px;
            max-height: 150px;
            object-fit: contain;
            object-position: center;
            border-radius: 0;
            background: transparent;
            padding: 0;
            border: none;
            flex-shrink: 0;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.4));
        }
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.35em;
        }
        .hero h1 > i { margin: 0; }
        .hero p { font-size: 1.1rem; opacity: 0.9; margin-bottom: 0; }
        
        /* Section */
        .section { padding: 50px 40px; max-width: 1400px; margin: 0 auto; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .section-header h2 { font-size: 1.8rem; color: var(--green-dark); font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .view-all { color: var(--green-primary); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 6px; transition: all 0.3s; }
        .view-all:hover { color: var(--green-dark); gap: 10px; }

        .messages-link {
            position: relative;
        }
        .badge-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 18px;
            height: 18px;
            margin-left: 6px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
            color: var(--white);
            background: linear-gradient(135deg, #EF4444, #F97316);
            padding: 0 5px;
            line-height: 1;
        }

        .snapshot-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 32px;
        }
        .snapshot-card {
            background: var(--white);
            border: 1px solid var(--green-soft);
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 6px 20px rgba(27, 94, 32, 0.08);
        }
        .snapshot-card h4 {
            color: var(--green-dark);
            font-size: 0.92rem;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .snapshot-value {
            font-size: 1.8rem;
            color: var(--green-primary);
            font-weight: 700;
            margin-bottom: 4px;
        }
        .snapshot-meta {
            color: var(--gray-500);
            font-size: 0.82rem;
        }
        .next-trip {
            border-left: 4px solid var(--green-primary);
        }
        .next-trip-empty {
            color: var(--gray-600);
            font-size: 0.9rem;
        }
        
        /* Category Cards */
        .categories { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 50px; }
        .category-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            transition: all 0.3s;
            cursor: pointer;
            border: 1px solid var(--green-soft);
        }
        .category-card:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(27, 94, 32, 0.2); }
        .category-img { width: 100%; height: 180px; object-fit: cover; }
        .category-content { padding: 20px; }
        .category-badge { 
            display: inline-block; 
            background: linear-gradient(135deg, var(--green-soft), var(--green-pale)); 
            color: var(--green-dark); 
            padding: 6px 14px; 
            border-radius: 50px; 
            font-size: 0.8rem; 
            font-weight: 600;
            margin-bottom: 10px;
        }
        .category-content h3 { font-size: 1.2rem; color: var(--green-dark); margin-bottom: 8px; }
        .category-content p { color: var(--green-medium); font-size: 0.9rem; }
        
        /* Property Cards */
        .properties-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; }
        .property-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            transition: all 0.3s;
            border: 1px solid var(--green-soft);
        }
        .property-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(27, 94, 32, 0.2); }
        .property-img-wrapper { position: relative; }
        .property-img { width: 100%; height: 220px; object-fit: cover; }
        .property-type-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            color: var(--white);
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .property-favorite {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 1.2rem;
            color: var(--gray-400);
        }
        .property-favorite:hover { background: var(--green-pale); color: var(--red-500); transform: scale(1.1); }
        .property-content { padding: 20px; }
        .property-price { font-size: 1.4rem; font-weight: 700; color: var(--green-primary); margin-bottom: 8px; }
        .property-price span { font-size: 0.85rem; font-weight: 400; color: var(--green-medium); }
        .property-title { font-size: 1.1rem; color: var(--green-dark); margin-bottom: 8px; font-weight: 600; }
        .property-location { display: flex; align-items: center; gap: 6px; color: var(--green-medium); font-size: 0.9rem; margin-bottom: 15px; }
        .property-features { display: flex; gap: 15px; padding-top: 15px; border-top: 1px solid var(--green-soft); }
        .feature { display: flex; align-items: center; gap: 6px; color: var(--green-primary); font-size: 0.85rem; }
        .property-rating { display: flex; align-items: center; gap: 5px; margin-top: 10px; }
        .stars { color: var(--amber-500); }
        .rating-count { color: var(--green-medium); font-size: 0.85rem; }
        .book-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .book-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4); }
        
        /* Footer */
        .footer { background: var(--green-dark); color: var(--white); padding: 40px; text-align: center; }
        .footer p { opacity: 0.8; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero {
                padding-top: calc(var(--client-nav-offset, 108px) + 40px);
                padding-left: 20px;
                padding-right: 20px;
                padding-bottom: 40px;
            }
            .hero h1 { font-size: 1.8rem; }
            .hero-logos {
                min-height: 120px;
                margin-bottom: 6px;
                padding: 4px 0 2px;
            }
            .hero-logos img {
                width: 120px;
                height: 120px;
                max-width: 120px;
                max-height: 120px;
            }
            .section { padding: 30px 20px; }
        }

        /* Animations */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate { animation: fadeInUp 0.6s ease forwards; }
        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }
    </style>
</head>
<body>
    <!-- Navigation -->
    @include('client.partials.top-navbar', ['active' => 'dashboard'])
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <div class="hero-logos">
                    <img src="{{ asset('Love Impasugong.png') }}" alt="Love Impasugong" width="150" height="150">
                    <img src="{{ asset('SYSTEMLOGO.png') }}" alt="ImpaStay Logo" width="150" height="150">
                    <img src="{{ asset('Lgu Socmed Template-02.png') }}" alt="LGU Impasugong" width="150" height="150">
                </div>
                <h1><i class="fas fa-home" aria-hidden="true"></i>Find Your Perfect Stay</h1>
                <p>Discover traveller-inns, Airbnb stays, and daily rentals in Impasugong</p>
            </div>
        </section>
        
        <!-- Property Categories -->
        <section class="section">
            @if($canManageOwnStays)
            <div class="section-header">
                <h2><i class="fas fa-chart-line"></i>My Booking Snapshot</h2>
                <a href="{{ route('bookings.index') }}" class="view-all"><i class="fas fa-arrow-right"></i> Manage Bookings</a>
            </div>

            <div class="snapshot-grid">
                <div class="snapshot-card animate delay-1">
                    <h4><i class="fas fa-suitcase-rolling"></i> Upcoming Trips</h4>
                    <div class="snapshot-value">{{ $upcomingBookingsCount ?? 0 }}</div>
                    <div class="snapshot-meta">Active stays and confirmed arrivals</div>
                </div>

                <div class="snapshot-card animate delay-2">
                    <h4><i class="fas fa-hourglass-half"></i> Pending Requests</h4>
                    <div class="snapshot-value">{{ $pendingBookingsCount ?? 0 }}</div>
                    <div class="snapshot-meta">Waiting for owner confirmation</div>
                </div>

                <div class="snapshot-card animate delay-3">
                    <h4><i class="fas fa-wallet"></i> Year-to-Date Spend</h4>
                    <div class="snapshot-value">₱{{ number_format($ytdSpend ?? 0, 0) }}</div>
                    <div class="snapshot-meta">Paid and completed bookings this year</div>
                </div>

                <div class="snapshot-card next-trip animate delay-3">
                    <h4><i class="fas fa-plane-departure"></i> Next Trip</h4>
                    @if($nextUpcomingBooking)
                        <div class="snapshot-value" style="font-size: 1.2rem; margin-bottom: 6px;">{{ $nextUpcomingBooking->accommodation->name ?? 'Accommodation' }}</div>
                        <div class="snapshot-meta">
                            {{ optional($nextUpcomingBooking->check_in_date)->format('M d, Y') }} - {{ optional($nextUpcomingBooking->check_out_date)->format('M d, Y') }}
                        </div>
                    @else
                        <div class="next-trip-empty">No upcoming booking yet. Explore available stays.</div>
                    @endif
                </div>
            </div>
            @else
            <div class="section-header">
                <h2><i class="fas fa-chart-line"></i>Bookings</h2>
            </div>
            <p style="color: var(--gray-600); max-width: 42rem; margin-bottom: 1.5rem;">Your account does not have permission to create or manage bookings on this site. Contact the business if you need this enabled.</p>
            @endif

            <div class="section-header">
                <h2><i class="fas fa-th-large"></i>Browse by Type</h2>
                <a href="{{ route('accommodations.index') }}" class="view-all"><i class="fas fa-arrow-right"></i> View All</a>
            </div>
            
            <div class="categories">
                <div class="category-card animate delay-1">
                    <img src="/COMMUNAL.jpg" alt="Traveller-Inns" class="category-img">
                    <div class="category-content">
                        <span class="category-badge"><i class="fas fa-bed"></i> Traditional</span>
                        <h3>Traveller-Inns</h3>
                        <p>{{ $categoryCounts['traveller-inn'] ?? 0 }} listings available for booking</p>
                    </div>
                </div>
                
                <div class="category-card animate delay-2">
                    <img src="/1.jpg" alt="Airbnb" class="category-img">
                    <div class="category-content">
                        <span class="category-badge"><i class="fas fa-home"></i> Unique Stays</span>
                        <h3>Airbnb Rentals</h3>
                        <p>{{ $categoryCounts['airbnb'] ?? 0 }} listings available for booking</p>
                    </div>
                </div>
                
                <div class="category-card animate delay-3">
                    <img src="/2.jpg" alt="Daily Rentals" class="category-img">
                    <div class="category-content">
                        <span class="category-badge"><i class="fas fa-calendar"></i> Flexible</span>
                        <h3>Daily Rentals</h3>
                        <p>{{ $categoryCounts['daily-rental'] ?? 0 }} listings available for booking</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Featured Properties -->
        <section class="section">
            <div class="section-header">
                <h2><i class="fas fa-star"></i>Featured Accommodations</h2>
                <a href="{{ route('accommodations.index') }}" class="view-all"><i class="fas fa-arrow-right"></i> View All</a>
            </div>
            
            <div class="properties-grid">
                @forelse($featuredAccommodations as $index => $accommodation)
                    <div class="property-card animate delay-{{ ($index % 3) + 1 }}">
                        <div class="property-img-wrapper">
                            <img src="{{ $accommodation->primary_image_url }}" alt="{{ $accommodation->name }}" class="property-img">
                            <span class="property-type-badge">
                                @if($accommodation->type === 'traveller-inn')
                                    <i class="fas fa-bed"></i>
                                @elseif($accommodation->type === 'airbnb')
                                    <i class="fas fa-home"></i>
                                @else
                                    <i class="fas fa-calendar"></i>
                                @endif
                                {{ $accommodation->type_label }}
                            </span>
                            <button type="button" class="property-favorite"><i class="far fa-heart"></i></button>
                        </div>
                        <div class="property-content">
                            <div class="property-price">{{ $accommodation->formatted_price }} <span>/ night</span></div>
                            <h3 class="property-title">{{ $accommodation->name }}</h3>
                            <div class="property-location"><i class="fas fa-map-marker-alt"></i> {{ $accommodation->barangay ?: $accommodation->address }}</div>
                            <div class="property-features">
                                <span class="feature"><i class="fas fa-bed"></i> {{ $accommodation->bedrooms }} Beds</span>
                                <span class="feature"><i class="fas fa-bath"></i> {{ $accommodation->bathrooms }} Baths</span>
                                <span class="feature"><i class="fas fa-users"></i> {{ $accommodation->max_guests }} Guests</span>
                            </div>
                            <div class="property-rating">
                                <span class="stars"><i class="fas fa-star"></i></span>
                                <span class="rating-count">{{ number_format((float) $accommodation->rating, 1) }} ({{ $accommodation->total_reviews }} reviews)</span>
                            </div>
                            <a href="{{ route('accommodations.show', $accommodation) }}" class="book-btn" style="text-decoration: none;"><i class="fas fa-ticket-alt"></i> Book Now</a>
                        </div>
                    </div>
                @empty
                    <div class="snapshot-card" style="grid-column: 1 / -1;">
                        <h4><i class="fas fa-info-circle"></i>No featured accommodations yet</h4>
                        <p class="snapshot-meta">New listings will appear here once tenant properties are published and verified.</p>
                    </div>
                @endforelse
            </div>
        </section>
        
        <!-- Footer -->
        <footer class="footer">
            <p><i class="fas fa-copyright"></i> 2024 ImpaStay. Impasugong Accommodations Platform.</p>
        </footer>
    </div>
    
    <script>
        // Simple favorite toggle
        document.querySelectorAll('.property-favorite').forEach(btn => {
            btn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (icon.classList.contains('far')) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    this.style.color = '#dc3545';
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    this.style.color = '';
                }
            });
        });
    </script>
</body>
</html>
