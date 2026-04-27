<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>My Bookings - Impasugong Accommodations</title>
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
        @php
            $authUser = auth()->user();
            $currentTenant = \App\Models\Tenant::current();
            $isTenantManager = $authUser && (
                $authUser->isOwner()
                || ($authUser->isAdmin() && $currentTenant && (int) $authUser->tenant_id === (int) $currentTenant->id)
            );
            $useLegacyBookingsNav = ! $isTenantManager && ! $authUser?->isClient();
        @endphp
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            @include('partials.tenant-theme-css-vars')
            --gray-50: #F9FAFB; --gray-100: #F3F4F6; --gray-200: #E5E7EB;
            --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151;
            --gray-800: #1F2937;
        }
        
        @if($useLegacyBookingsNav)
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
        .nav-btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.3s; cursor: pointer; border: none; }
        .nav-btn.primary { background: var(--green-primary); color: var(--white); }
        .nav-btn.secondary { background: var(--green-soft); color: var(--green-dark); }
        @endif

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
        
        /* Main Layout */
        .main-content {
            width: min(1800px, 100%);
            margin: 0 auto;
            padding-top: var(--client-nav-offset, 100px);
            padding-bottom: 28px;
            padding-left: clamp(12px, 2vw, 34px);
            padding-right: clamp(12px, 2vw, 34px);
            min-height: calc(100vh - var(--client-nav-offset, 100px));
        }
        
        /* Page Header */
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 2rem; color: var(--green-dark); margin-bottom: 5px; }
        .page-header p { color: var(--gray-500); }
        
        /* Filter Tabs */
        .filter-tabs { display: flex; gap: 10px; margin-bottom: 25px; flex-wrap: wrap; }
        .filter-tab { padding: 10px 20px; border-radius: 8px; border: none; background: var(--white); color: var(--gray-600); cursor: pointer; font-weight: 500; transition: all 0.3s; text-decoration: none; }
        .filter-tab:hover { background: var(--green-soft); }
        .filter-tab.active { background: var(--green-primary); color: white; }
        
        /* Bookings Grid */
        .bookings-grid { display: grid; gap: 16px; align-items: stretch; }
        
        .booking-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
            overflow: hidden;
            transition: all 0.3s;
            align-self: start;
            height: 100%;
        }
        
        .booking-card:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(27, 94, 32, 0.15); }
        
        .booking-header { padding: 20px 25px; border-bottom: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; }
        .booking-id { font-size: 0.85rem; color: var(--gray-500); }
        .booking-date { font-size: 0.9rem; color: var(--gray-600); }
        
        .booking-body { padding: 25px; display: flex; gap: 25px; }
        
        .property-image { width: 180px; height: 130px; border-radius: 12px; object-fit: cover; flex-shrink: 0; }
        
        .booking-details { flex: 1; }
        .property-name { font-size: 1.3rem; color: var(--green-dark); margin-bottom: 8px; font-weight: 600; }
        .property-location { display: flex; align-items: center; gap: 6px; color: var(--gray-500); font-size: 0.9rem; margin-bottom: 15px; }
        
        .booking-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .info-item { background: var(--cream); padding: 12px 15px; border-radius: 10px; }
        .info-label { font-size: 0.75rem; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
        .info-value { font-weight: 600; color: var(--gray-800); }
        
        .booking-footer { padding: 20px 25px; background: var(--cream); display: flex; justify-content: space-between; align-items: center; }
        
        /* Status Badges */
        .status-badge { display: inline-block; padding: 6px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        .status-badge.pending { background: #FFF3E0; color: #E65100; }
        .status-badge.confirmed { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.cancelled { background: #FFEBEE; color: #C62828; }
        .status-badge.completed { background: #E3F2FD; color: #1565C0; }
        .status-badge.paid { background: #E8F5E9; color: #2E7D32; }
        .payment-badge { display: inline-flex; align-items: center; border-radius: 999px; padding: 6px 12px; font-size: 0.76rem; font-weight: 700; }
        .payment-badge.neutral { background: #F3F4F6; color: #374151; }
        .payment-badge.pending-review { background: #FFF7ED; color: #9A3412; }
        .payment-badge.paid { background: #ECFDF5; color: #166534; }
        
        /* Action Buttons */
        .action-btns { display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: var(--green-primary); color: white; }
        .btn-primary:hover { background: var(--green-dark); }
        .btn-danger { background: #EF4444; color: white; }
        .btn-danger:hover { background: #DC2626; }
        .btn-secondary { background: var(--gray-200); color: var(--gray-700); }
        .btn-outline { background: transparent; border: 2px solid var(--green-primary); color: var(--green-primary); }
        .btn-outline:hover { background: var(--green-primary); color: white; }
        .toggle-actions-btn {
            width: 100%;
            padding: 9px 12px;
            border-radius: 8px;
            border: 1px solid var(--green-primary);
            background: var(--white);
            color: var(--green-dark);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .toggle-actions-btn:hover {
            background: var(--green-soft);
        }

        /* Owner compact grid view (4 cards per row) */
        .owner-bookings-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 16px;
        }
        .owner-bookings-grid .booking-header {
            padding: 12px 14px;
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }
        .owner-bookings-grid .booking-body {
            padding: 14px;
            flex-direction: column;
            gap: 12px;
        }
        .owner-bookings-grid .property-image {
            width: 100%;
            height: 120px;
            border-radius: 10px;
        }
        .owner-bookings-grid .property-name {
            font-size: 1rem;
            line-height: 1.3;
            margin-bottom: 6px;
        }
        .owner-bookings-grid .property-location {
            font-size: 0.8rem;
            margin-bottom: 10px;
        }
        .owner-bookings-grid .booking-info {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            margin-bottom: 0;
        }
        .owner-bookings-grid .info-item {
            padding: 8px 10px;
            border-radius: 8px;
        }
        .owner-bookings-grid .info-label {
            font-size: 0.65rem;
        }
        .owner-bookings-grid .info-value {
            font-size: 0.85rem;
        }
        .owner-bookings-grid .booking-footer {
            padding: 12px 14px;
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }
        .owner-bookings-grid .action-btns {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }
        .owner-bookings-grid .owner-actions-panel {
            display: none;
        }
        .owner-bookings-grid .owner-actions-panel.open {
            display: grid;
        }
        .owner-bookings-grid .btn {
            width: 100%;
            justify-content: center;
            padding: 9px 12px;
            font-size: 0.85rem;
        }

        @media (max-width: 1400px) {
            .owner-bookings-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (max-width: 1100px) {
            .owner-bookings-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        
        /* Empty State */
        .empty-state { text-align: center; padding: 60px 20px; background: var(--white); border-radius: 16px; }
        .empty-icon { font-size: 4rem; margin-bottom: 20px; }
        .empty-state h3 { font-size: 1.5rem; color: var(--gray-700); margin-bottom: 10px; }
        .empty-state p { color: var(--gray-500); margin-bottom: 25px; }
        
        /* Responsive */
        @media (max-width: 768px) {
            @if($useLegacyBookingsNav)
            .navbar { padding: 0 20px; height: 60px; }
            .nav-links { display: none; }
            @endif
            .main-content {
                padding-top: calc(var(--client-nav-offset, 100px) - 10px);
                padding-left: 14px;
                padding-right: 14px;
                padding-bottom: 24px;
            }
            .booking-body { flex-direction: column; }
            .property-image { width: 100%; height: 200px; }
            .booking-footer { flex-direction: column; gap: 15px; align-items: stretch; }
            .action-btns { justify-content: center; }
            .owner-bookings-grid { grid-template-columns: 1fr; }
        }

    </style>
</head>
<body class="{{ $isTenantManager ? 'owner-nav-page' : '' }}">
    <!-- Navigation -->
    @if($isTenantManager)
        @include('owner.partials.top-navbar')
    @else
    @if(auth()->user()?->isClient())
        @include('client.partials.top-navbar', ['active' => 'bookings'])
    @else
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="nav-logo">
            <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo">
            <span>Impasugong</span>
        </a>
        
        <ul class="nav-links">
            @auth
                @if(Auth::user()->isAdmin())
                    @php
                        $adminDashboardHref = \App\Models\Tenant::checkCurrent() ? '/owner/dashboard' : '/admin/dashboard';
                    @endphp
                    <li><a href="{{ $adminDashboardHref }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                @else
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Browse</a></li>
                @endif
            @endauth
            <li><a href="{{ route(Auth::check() && $isTenantManager && \Illuminate\Support\Facades\Route::has('owner.accommodations.index') ? 'owner.accommodations.index' : (\Illuminate\Support\Facades\Route::has('accommodations.index') ? 'accommodations.index' : 'dashboard')) }}" class="{{ request()->routeIs('accommodations.*') || request()->routeIs('owner.accommodations.*') ? 'active' : '' }}">Browse</a></li>
            <li><a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}">My Bookings</a></li>
            <li><a href="{{ route('messages.index', [], false) }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">Messages</a></li>
            <li><a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">Settings</a></li>
        </ul>
        
        <div class="nav-actions">
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="nav-btn primary">Logout</button>
            </form>
        </div>
    </nav>
    @endif
    @endif
    
    <!-- Main Content -->
    <main
        class="{{ $isTenantManager ? 'main-content with-owner-nav w-full' : 'mx-auto min-h-screen w-full max-w-[1800px] px-4 pb-10 sm:px-6 lg:px-10' }}"
        @if(!$isTenantManager) style="padding-top: calc(var(--client-nav-offset, 108px) + 24px);" @endif
    >
        @php
            $isOwner = $isTenantManager;
            $bookingsIndexRoute = Auth::check() && $isTenantManager ? 'owner.bookings.index' : 'bookings.index';
            $bookingsShowRoute = Auth::check() && $isTenantManager ? 'owner.bookings.show' : 'bookings.show';
            $ownerBookingsBase = '/owner/bookings';
            $ownerAccommodationsBase = '/owner/accommodations';
        @endphp

        <div class="{{ $isOwner ? 'page-header rounded-2xl border border-emerald-100 bg-white/70 p-4 shadow-sm' : 'mb-6 rounded-2xl border border-green-100 bg-white/85 p-6 shadow-sm backdrop-blur-sm' }}">
            <h1 class="{{ $isOwner ? '' : 'mb-2 text-2xl font-bold text-green-900 sm:text-3xl' }}">
                @if(!$isOwner)<i class="fas fa-calendar-check mr-2 text-green-700"></i>@endif
                My Bookings
            </h1>
            <p class="{{ $isOwner ? '' : 'text-sm text-gray-600 sm:text-base' }}">View and manage your accommodation bookings</p>
        </div>

        @if($isOwner)
            <div class="rounded-2xl border border-emerald-100 bg-white/95 p-4 shadow-sm" style="margin-bottom: 18px;">
                <h3 style="color: var(--green-dark); margin-bottom: 10px; font-size: 1rem;">GCash QR Code</h3>
                @if(session('success'))
                    <p style="font-size:0.85rem;color:var(--green-dark);margin-bottom:8px;">{{ session('success') }}</p>
                @endif
                @if($currentTenant?->getGcashQrUrl())
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px;">
                        <img src="{{ $currentTenant->getGcashQrUrl() }}" alt="GCash QR" style="width:90px;height:90px;object-fit:cover;border-radius:10px;border:1px solid var(--gray-200);">
                        <form method="POST" action="/owner/bookings/payment-settings/gcash-qr">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Remove QR</button>
                        </form>
                    </div>
                @endif
                <form method="POST" action="/owner/bookings/payment-settings/gcash-qr" enctype="multipart/form-data" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                    @csrf
                    <input type="file" name="gcash_qr" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
                    <button type="submit" class="btn btn-primary">{{ $currentTenant?->getGcashQrUrl() ? 'Replace' : 'Upload' }} GCash QR Photo</button>
                </form>
                @error('gcash_qr')
                    <p style="font-size:0.82rem;color:#C62828;margin-top:8px;">{{ $message }}</p>
                @enderror
            </div>
        @endif
        
        <!-- Filter Tabs -->
        <div class="{{ $isOwner ? 'filter-tabs rounded-2xl border border-emerald-100 bg-white/90 p-3 shadow-sm' : 'mb-6 flex flex-wrap gap-2 rounded-2xl border border-green-100 bg-white p-3 shadow-sm' }}">
            <a href="{{ $isOwner ? $ownerBookingsBase : route($bookingsIndexRoute) }}" class="{{ $isOwner ? 'filter-tab' : 'inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold transition' }} {{ !request('status') ? ($isOwner ? 'active' : 'bg-green-700 text-white') : ($isOwner ? '' : 'bg-gray-100 text-gray-700 hover:bg-green-50 hover:text-green-700') }}">All</a>
            <a href="{{ $isOwner ? ($ownerBookingsBase . '?status=pending') : route($bookingsIndexRoute, ['status' => 'pending']) }}" class="{{ $isOwner ? 'filter-tab' : 'inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold transition' }} {{ request('status') == 'pending' ? ($isOwner ? 'active' : 'bg-green-700 text-white') : ($isOwner ? '' : 'bg-gray-100 text-gray-700 hover:bg-green-50 hover:text-green-700') }}">Pending</a>
            <a href="{{ $isOwner ? ($ownerBookingsBase . '?status=confirmed') : route($bookingsIndexRoute, ['status' => 'confirmed']) }}" class="{{ $isOwner ? 'filter-tab' : 'inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold transition' }} {{ request('status') == 'confirmed' ? ($isOwner ? 'active' : 'bg-green-700 text-white') : ($isOwner ? '' : 'bg-gray-100 text-gray-700 hover:bg-green-50 hover:text-green-700') }}">Confirmed</a>
            <a href="{{ $isOwner ? ($ownerBookingsBase . '?status=completed') : route($bookingsIndexRoute, ['status' => 'completed']) }}" class="{{ $isOwner ? 'filter-tab' : 'inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold transition' }} {{ request('status') == 'completed' ? ($isOwner ? 'active' : 'bg-green-700 text-white') : ($isOwner ? '' : 'bg-gray-100 text-gray-700 hover:bg-green-50 hover:text-green-700') }}">Completed</a>
            <a href="{{ $isOwner ? ($ownerBookingsBase . '?status=cancelled') : route($bookingsIndexRoute, ['status' => 'cancelled']) }}" class="{{ $isOwner ? 'filter-tab' : 'inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold transition' }} {{ request('status') == 'cancelled' ? ($isOwner ? 'active' : 'bg-green-700 text-white') : ($isOwner ? '' : 'bg-gray-100 text-gray-700 hover:bg-green-50 hover:text-green-700') }}">Cancelled</a>
        </div>
        
        <!-- Bookings List -->
        @if(isset($bookings) && count($bookings) > 0)
            <div class="bookings-grid {{ $isOwner ? 'owner-bookings-grid' : 'grid-cols-1 gap-4 xl:grid-cols-2 2xl:grid-cols-3' }} w-full">
                @foreach($bookings as $booking)
                    <div class="{{ $isOwner ? 'booking-card flex h-full flex-col' : 'flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md' }}">
                        <div class="{{ $isOwner ? 'booking-header' : 'flex items-center justify-between border-b border-gray-200 px-4 py-3' }}">
                            <span class="{{ $isOwner ? 'booking-id' : 'text-xs font-semibold text-gray-500' }}">Booking #{{ $booking->id }}</span>
                            <span class="{{ $isOwner ? 'booking-date' : 'text-xs text-gray-600' }}">{{ $booking->created_at->format('M d, Y') }}</span>
                        </div>
                        
                        <div class="{{ $isOwner ? 'booking-body flex-1' : 'flex flex-1 flex-col gap-3 p-4 md:flex-row md:gap-4' }}">
                            @if($booking->accommodation && $booking->accommodation->primary_image)
                                <img src="{{ $booking->accommodation->primary_image_url }}" alt="{{ $booking->accommodation->name }}" class="{{ $isOwner ? 'property-image' : 'h-28 w-full rounded-lg object-cover md:h-24 md:w-36 md:flex-shrink-0' }}">
                            @else
                                <img src="/COMMUNAL.jpg" alt="Property" class="{{ $isOwner ? 'property-image' : 'h-28 w-full rounded-lg object-cover md:h-24 md:w-36 md:flex-shrink-0' }}">
                            @endif
                            
                            <div class="{{ $isOwner ? 'booking-details' : 'flex-1' }}">
                                <h3 class="{{ $isOwner ? 'property-name' : 'mb-1 text-lg font-bold text-green-900' }}">{{ $booking->accommodation->name ?? 'N/A' }}</h3>
                                <div class="{{ $isOwner ? 'property-location' : 'mb-3 text-xs text-gray-500' }}">
                                    <i class="fas fa-location-dot mr-1"></i> {{ $booking->accommodation->address ?? 'Impasugong' }}
                                </div>
                                
                                <div class="{{ $isOwner ? 'booking-info' : 'grid grid-cols-2 gap-2' }}">
                                    <div class="{{ $isOwner ? 'info-item' : 'rounded-lg bg-green-50 p-2.5' }}">
                                        <div class="{{ $isOwner ? 'info-label' : 'mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500' }}">Check-In</div>
                                        <div class="{{ $isOwner ? 'info-value' : 'text-sm font-semibold text-gray-800' }}">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</div>
                                    </div>
                                    <div class="{{ $isOwner ? 'info-item' : 'rounded-lg bg-green-50 p-2.5' }}">
                                        <div class="{{ $isOwner ? 'info-label' : 'mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500' }}">Check-Out</div>
                                        <div class="{{ $isOwner ? 'info-value' : 'text-sm font-semibold text-gray-800' }}">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</div>
                                    </div>
                                    <div class="{{ $isOwner ? 'info-item' : 'rounded-lg bg-green-50 p-2.5' }}">
                                        <div class="{{ $isOwner ? 'info-label' : 'mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500' }}">Guests</div>
                                        <div class="{{ $isOwner ? 'info-value' : 'text-sm font-semibold text-gray-800' }}">{{ $booking->number_of_guests ?? 1 }}</div>
                                    </div>
                                    <div class="{{ $isOwner ? 'info-item' : 'rounded-lg bg-green-50 p-2.5' }}">
                                        <div class="{{ $isOwner ? 'info-label' : 'mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500' }}">Total</div>
                                        <div class="{{ $isOwner ? 'info-value' : 'text-sm font-semibold text-gray-800' }}">₱{{ number_format($booking->total_price, 2) }}</div>
                                    </div>
                                    @if($isOwner)
                                        <div class="info-item">
                                            <div class="info-label">Proof Status</div>
                                            <div class="info-value">
                                                @if($booking->gcash_payment_proof_path)
                                                    Proof Submitted (Needs Review)
                                                @else
                                                    No Proof Yet
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($isOwner && $booking->gcash_payment_proof_url)
                                    <div style="margin-top:8px;">
                                        <a href="{{ $booking->gcash_payment_proof_url }}" target="_blank" class="btn btn-outline" style="padding:8px 12px; font-size:0.8rem;">
                                            View Client Proof
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="{{ $isOwner ? 'booking-footer' : 'flex flex-col justify-between gap-2 border-t border-gray-200 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center' }}">
                            @php
                                $paymentUi = $booking->payment_ui_state;
                                $paymentToneClass = $paymentUi['tone'] === 'pending_review' ? 'pending-review' : $paymentUi['tone'];
                            @endphp
                            <div class="flex flex-col gap-2">
                                <span class="{{ $isOwner ? 'status-badge ' . $booking->status : 'inline-flex w-fit items-center rounded-full px-3 py-1 text-xs font-semibold ' . ($booking->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($booking->status === 'confirmed' ? 'bg-emerald-100 text-emerald-800' : ($booking->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'))) }}">{{ ucfirst($booking->status) }}</span>
                                <span class="{{ $isOwner ? 'payment-badge ' . $paymentToneClass : 'inline-flex w-fit items-center rounded-full px-3 py-1 text-xs font-semibold ' . ($paymentToneClass === 'pending-review' ? 'bg-orange-100 text-orange-800' : ($paymentToneClass === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700')) }}">{{ $paymentUi['label'] }}</span>
                            </div>
                            @if($isOwner)
                                <button type="button" class="toggle-actions-btn" data-target="owner-actions-{{ $booking->id }}" aria-expanded="false">
                                    Show Actions
                                </button>
                                <div class="action-btns owner-actions-panel" id="owner-actions-{{ $booking->id }}">
                                    <a href="{{ $ownerBookingsBase . '/' . $booking->id }}" class="btn btn-primary">View Details</a>
                                    @if($booking->status === 'pending')
                                        <form action="/owner/bookings/{{ $booking->id }}/status" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-primary">Approve</button>
                                        </form>
                                        <form action="/owner/bookings/{{ $booking->id }}/status" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Decline this booking request?')">Decline</button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <div class="{{ $isOwner ? 'action-btns' : 'flex flex-wrap gap-2' }}">
                                    <a href="{{ route($bookingsShowRoute, $booking) }}" class="{{ $isOwner ? 'btn btn-primary' : 'inline-flex items-center rounded-lg bg-green-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-green-800' }}">View Details</a>
                                    @if(Auth::check() && Auth::user()->isClient() && ($booking->status == 'pending' || $booking->status == 'confirmed'))
                                        @if($booking->status === 'confirmed')
                                            <a href="{{ route('bookings.payment', $booking) }}" class="inline-flex items-center rounded-lg bg-green-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-green-800">Pay Now</a>
                                        @endif
                                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-100" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if(isset($bookings) && method_exists($bookings, 'links'))
                <div class="mt-8">
                    {{ $bookings->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="{{ $isOwner ? 'empty-state' : 'rounded-2xl border border-gray-200 bg-white p-10 text-center shadow-sm' }}">
                <div class="{{ $isOwner ? 'empty-icon' : 'mb-4 text-5xl' }}">📅</div>
                <h3 class="{{ $isOwner ? '' : 'mb-2 text-2xl font-bold text-gray-800' }}">No Bookings Found</h3>
                <p class="{{ $isOwner ? '' : 'mb-6 text-gray-500' }}">You haven't made any bookings yet. Start exploring accommodations!</p>
                <a href="{{ $isOwner ? $ownerAccommodationsBase : route((\Illuminate\Support\Facades\Route::has('accommodations.index') ? 'accommodations.index' : 'dashboard')) }}" class="{{ $isOwner ? 'btn btn-primary' : 'inline-flex items-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800' }}">Browse Properties</a>
            </div>
        @endif
    </main>

    <script>
        document.querySelectorAll('.toggle-actions-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var panel = document.getElementById(button.dataset.target);
                if (!panel) return;

                var willOpen = !panel.classList.contains('open');

                document.querySelectorAll('.owner-actions-panel.open').forEach(function(openPanel) {
                    openPanel.classList.remove('open');
                });

                document.querySelectorAll('.toggle-actions-btn').forEach(function(btn) {
                    btn.textContent = 'Show Actions';
                    btn.setAttribute('aria-expanded', 'false');
                });

                if (willOpen) {
                    panel.classList.add('open');
                    button.textContent = 'Hide Actions';
                    button.setAttribute('aria-expanded', 'true');
                }
            });
        });
    </script>
</body>
</html>

