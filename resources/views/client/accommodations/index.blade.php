<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Properties - Impasugong Accommodations</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind = {
            config: {
                corePlugins: {
                    preflight: false,
                },
            },
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            @include('partials.tenant-theme-css-vars')
            --gray-200: #E5E7EB; --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151; --gray-800: #1F2937;
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
        .nav-btn.secondary:hover { background: var(--green-white); }
        @endunless

        @if(auth()->user()?->isClient())
            @include('client.partials.top-navbar-styles')
        @endif
        
        /* Responsive */
        @media (max-width: 768px) {
            @unless(auth()->user()?->isClient())
            .navbar { padding: 0 20px; height: 60px; }
            .nav-logo img { width: 38px; height: 38px; }
            .nav-logo span { font-size: 1.1rem; }
            .nav-links { display: none; }
            @endunless
        }

    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-50 via-lime-50 to-white text-gray-800">
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
            <li><a href="{{ route('messages.index', [], false) }}">Messages</a></li>
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
    <main class="mx-auto min-h-screen w-full max-w-[1800px] px-4 pb-10 sm:px-6 lg:px-10" style="padding-top: calc(var(--client-nav-offset, 108px) + 24px);">
        <!-- Page Header -->
        <div class="mb-6 rounded-2xl border border-green-100 bg-white/85 p-6 text-center shadow-sm backdrop-blur-sm">
            <div class="mb-3 flex items-center justify-center gap-3">
                <img src="/Love%20Impasugong.png" alt="Love Impasugong Logo" class="h-14 w-14 rounded-xl bg-white p-1.5 shadow-sm sm:h-16 sm:w-16">
                <img src="/SYSTEMLOGO.png" alt="System Logo" class="h-14 w-14 rounded-xl bg-white p-1.5 shadow-sm sm:h-16 sm:w-16">
            </div>
            <h1 class="mb-1 text-2xl font-bold text-green-900 sm:text-3xl">Find Your Perfect Stay</h1>
            <p class="text-sm text-gray-600 sm:text-base">Discover traveller-inns, Airbnb stays, and daily rentals in Impasugong</p>
        </div>
        
        <!-- Filter Bar -->
        <form action="{{ route('accommodations.index') }}" method="GET" class="mb-6 grid gap-3 rounded-2xl border border-green-100 bg-white p-4 shadow-sm lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-600">Type</label>
                <select name="type" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                    <option value="">All Types</option>
                    <option value="traveller-inn" {{ request('type') == 'traveller-inn' ? 'selected' : '' }}>Traveller-Inn</option>
                    <option value="airbnb" {{ request('type') == 'airbnb' ? 'selected' : '' }}>Airbnb</option>
                    <option value="daily-rental" {{ request('type') == 'daily-rental' ? 'selected' : '' }}>Daily Rental</option>
                </select>
            </div>
            
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-600">Min Price</label>
                <input type="number" name="min_price" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100" placeholder="PHP 0" value="{{ request('min_price') }}">
            </div>
            
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-600">Max Price</label>
                <input type="number" name="max_price" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100" placeholder="PHP 10000" value="{{ request('max_price') }}">
            </div>
            
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-600">Guests</label>
                <select name="guests" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                    <option value="">Any</option>
                    <option value="1" {{ request('guests') == '1' ? 'selected' : '' }}>1 Guest</option>
                    <option value="2" {{ request('guests') == '2' ? 'selected' : '' }}>2 Guests</option>
                    <option value="3" {{ request('guests') == '3' ? 'selected' : '' }}>3 Guests</option>
                    <option value="4" {{ request('guests') == '4' ? 'selected' : '' }}>4 Guests</option>
                    <option value="5" {{ request('guests') == '5' ? 'selected' : '' }}>5+ Guests</option>
                </select>
            </div>
            
            <div class="lg:col-span-3">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-600">Search</label>
                <input type="text" name="search" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100" placeholder="Search properties..." value="{{ request('search') }}" aria-label="Search properties">
            </div>
            
            <div class="lg:col-span-1">
                <button type="submit" class="w-full rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">Search</button>
            </div>
        </form>
        
        <!-- Properties Grid -->
        @if(isset($accommodations) && count($accommodations) > 0)
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                @foreach($accommodations as $accommodation)
                    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="relative h-44 overflow-hidden">
                            @if($accommodation->primary_image)
                                <img src="{{ $accommodation->primary_image_url }}" alt="{{ $accommodation->name }}" class="h-full w-full object-cover transition duration-300 hover:scale-105">
                            @else
                                <img src="/COMMUNAL.jpg" alt="{{ $accommodation->name }}" class="h-full w-full object-cover transition duration-300 hover:scale-105">
                            @endif
                            <span class="absolute left-3 top-3 rounded-full bg-green-700 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white">{{ str_replace('-', ' ', $accommodation->type) }}</span>
                            <button class="property-favorite absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-white text-base shadow-sm transition hover:scale-110 hover:bg-green-50" title="Add to favorites">♡</button>
                        </div>
                        
                        <div class="p-4">
                            <div class="mb-2 text-xl font-bold text-green-700">₱{{ number_format($accommodation->price_per_night, 0, '.', ',') }} <span class="text-sm font-normal text-gray-500">/ night</span></div>
                            <h3 class="mb-2 text-lg font-semibold text-gray-800">{{ $accommodation->name }}</h3>
                            <div class="mb-3 flex items-center gap-2 text-sm text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $accommodation->address }}, Brgy. {{ $accommodation->barangay }}
                            </div>
                            
                            <p class="mb-3 line-clamp-2 text-sm text-gray-600">{{ Str::limit($accommodation->description, 100) }}</p>
                            
                            <div class="mb-3 flex flex-wrap gap-3 border-t border-gray-200 pt-3">
                                <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                    </svg>
                                    {{ $accommodation->bedrooms ?? 1 }} Bed
                                </div>
                                <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                    </svg>
                                    {{ $accommodation->bathrooms ?? 1 }} Bath
                                </div>
                                <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    {{ $accommodation->max_guests ?? 2 }} Guests
                                </div>
                            </div>
                            
                            <div class="mb-3 flex items-center gap-2">
                                <span class="text-sm tracking-wide text-amber-500">★★★★★</span>
                                <span class="text-xs text-gray-500">({{ $accommodation->total_reviews ?? 0 }} reviews)</span>
                            </div>
                            
                            <a href="{{ route('accommodations.show', $accommodation) }}" class="inline-flex w-full items-center justify-center rounded-lg bg-gradient-to-r from-green-700 to-green-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:from-green-800 hover:to-green-700">View Details</a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if(isset($accommodations) && method_exists($accommodations, 'links'))
                <div class="mt-8">
                    {{ $accommodations->links() }}
                </div>
            @endif
        @else
            <div class="rounded-2xl border border-gray-200 bg-white px-6 py-14 text-center shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-5 h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mb-2 text-2xl font-bold text-gray-700">No Properties Found</h3>
                <p class="text-gray-500">Try adjusting your filters or search criteria.</p>
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
