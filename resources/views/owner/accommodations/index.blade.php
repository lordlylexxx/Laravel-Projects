<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>My Properties - Impasugong Accommodations</title>
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
        .nav-btn.primary:hover { background: var(--green-dark); }
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
            width: min(1800px, 100%);
            margin: 0 auto; 
            padding: var(--owner-content-offset) clamp(12px, 2vw, 34px) 28px;
            min-height: calc(100vh - var(--owner-content-offset));
        }
        
        /* Page Header */
        .page-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            margin-bottom: 35px;
        }
        .page-header h1 { 
            font-size: 2rem; 
            color: var(--green-dark); 
            font-weight: 700;
        }
        .page-header p { color: var(--gray-500); }
        
        .add-btn {
            padding: 14px 28px;
            background: var(--green-primary);
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .add-btn:hover { background: var(--green-dark); transform: translateY(-2px); }
        .add-btn-disabled, .add-btn.add-btn-disabled {
            opacity: 0.55;
            cursor: not-allowed;
            pointer-events: none;
            background: var(--gray-400);
        }
        .plan-usage-line {
            margin-top: 10px;
            font-size: 0.95rem;
            color: var(--gray-700);
            max-width: 520px;
            line-height: 1.45;
        }
        .plan-usage-line strong { color: var(--green-dark); }
        
        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 18px;
        }
        
        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stat-icon.green { background: var(--green-soft); }
        .stat-icon.blue { background: #E3F2FD; }
        .stat-icon.orange { background: #FFF3E0; }
        .stat-icon.purple { background: #F3E5F5; }
        
        .stat-info h3 { font-size: 1.6rem; color: var(--green-dark); margin-bottom: 3px; }
        .stat-info p { color: var(--gray-500); font-size: 0.85rem; }
        
        /* Properties Table */
        .properties-table-wrapper {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            min-height: calc(100vh - var(--owner-content-offset) - 280px);
        }
        
        .table-header {
            padding: 25px 30px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .table-header h3 { font-size: 1.2rem; color: var(--gray-800); font-weight: 600; }
        
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 18px 25px; text-align: left; }
        .data-table th { 
            background: var(--cream); 
            font-weight: 600; 
            color: var(--gray-600); 
            font-size: 0.85rem; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
        }
        .data-table tr { border-bottom: 1px solid var(--gray-100); transition: background 0.2s; }
        .data-table tbody tr:hover { background: var(--green-white); }
        .data-table td { color: var(--gray-700); font-size: 0.95rem; }
        
        .property-cell { display: flex; align-items: center; gap: 15px; }
        .property-thumb { 
            width: 70px; 
            height: 55px; 
            border-radius: 10px; 
            object-fit: cover;
        }
        .property-cell-info h4 { color: var(--gray-800); font-weight: 600; margin-bottom: 3px; }
        .property-cell-info p { color: var(--gray-500); font-size: 0.85rem; }
        
        .status-badge { 
            display: inline-block; 
            padding: 6px 14px; 
            border-radius: 50px; 
            font-size: 0.8rem; 
            font-weight: 600; 
        }
        .status-badge.active { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.pending { background: #FFF3E0; color: #E65100; }
        .status-badge.inactive { background: var(--gray-200); color: var(--gray-600); }
        .status-badge.verified { background: #E8F5E9; color: #2E7D32; }
        
        .action-btns { display: flex; gap: 10px; }
        .action-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            font-size: 1rem;
        }
        .action-btn.view { background: var(--green-soft); color: var(--green-primary); }
        .action-btn.edit { background: #E3F2FD; color: #1976D2; }
        .action-btn.delete { background: #FFEBEE; color: #C62828; }
        .action-btn:hover { transform: scale(1.1); }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 40px;
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
        .empty-state p { color: var(--gray-500); margin-bottom: 25px; }
        
        /* Pagination */
        .pagination-wrapper {
            padding: 20px 30px;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: center;
        }

        .pagination-wrapper .pagination {
            display: flex;
            list-style: none;
            gap: 8px;
            margin: 0;
            padding: 0;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }

        .pagination-wrapper .page-item { list-style: none; }

        .pagination-wrapper .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1px solid var(--gray-200);
            background: #fff;
            color: var(--gray-700);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .pagination-wrapper .page-item.active .page-link {
            background: var(--green-primary);
            border-color: var(--green-primary);
            color: #fff;
        }

        .pagination-wrapper .page-item.disabled .page-link {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .pagination-wrapper p.small.text-muted {
            margin-top: 10px;
            width: 100%;
            text-align: center;
            color: var(--gray-500);
            font-size: 0.82rem;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .data-table { display: block; overflow-x: auto; }
        }
        
        @media (max-width: 768px) {
            .navbar { padding: 0 20px; height: 60px; }
            .nav-logo img { width: 38px; height: 38px; }
            .nav-logo span { font-size: 1.1rem; }
            .nav-links { display: none; }
            .main-content { padding: calc(var(--owner-content-offset) - 8px) 14px 24px; min-height: auto; }
            .page-header { flex-direction: column; gap: 15px; align-items: flex-start; }
            .page-header h1 { font-size: 1.6rem; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }

        @include('owner.partials.top-navbar-styles')
    </style>
</head>
<body class="owner-nav-page">
    @include('owner.partials.top-navbar')
    
    <!-- Main Content -->
    <main class="main-content with-owner-nav">
        <!-- Page Header -->
        <div class="page-header rounded-2xl border border-emerald-100 bg-white/90 p-4 shadow-sm">
            <div>
                <h1>My Properties</h1>
                <p>Manage your accommodations and listings</p>
                @if(isset($listingUsage))
                    <p class="plan-usage-line">
                        <strong>{{ $listingUsage['plan_label'] }} plan</strong>
                        @if($listingUsage['max'] === null)
                            — {{ $listingUsage['used'] }} active listing(s) (unlimited on Premium).
                        @else
                            — {{ $listingUsage['used'] }} / {{ $listingUsage['max'] }} listings
                            @if(($listingUsage['remaining'] ?? 0) > 0)
                                <span style="color: var(--gray-500);">({{ $listingUsage['remaining'] }} remaining)</span>
                            @elseif(($listingUsage['remaining'] ?? 0) === 0)
                                <span style="color: #B45309; font-weight: 600;">(limit reached)</span>
                            @endif
                        @endif
                    </p>
                @endif
            </div>
            @if($canCreateListing ?? false)
                <a href="/owner/accommodations/create" class="add-btn inline-flex items-center justify-center">
                    ➕ Add Property
                </a>
            @else
                <span class="add-btn add-btn-disabled inline-flex items-center justify-center" title="You’ve reached your plan limit or your subscription isn’t active. Upgrade or remove a listing to add more.">
                    ➕ Add Property
                </span>
            @endif
        </div>
        
        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon green">🏠</div>
                <div class="stat-info">
                    <h3>{{ number_format($accommodations->total() ?? 0) }}</h3>
                    <p>Total Properties</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">✅</div>
                <div class="stat-info">
                    <h3>{{ number_format($accommodations->where('is_verified', true)->count() ?? 0) }}</h3>
                    <p>Verified</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">📅</div>
                <div class="stat-info">
                    <h3>{{ number_format($accommodations->sum('bookings_count') ?? 0) }}</h3>
                    <p>Total Bookings</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">⭐</div>
                <div class="stat-info">
                    <h3>{{ number_format($accommodations->avg('rating') ?? 0, 1) }}</h3>
                    <p>Avg Rating</p>
                </div>
            </div>
        </div>
        
        <!-- Properties Table -->
        <div class="properties-table-wrapper">
            <div class="table-header">
                <h3>All Properties</h3>
            </div>
            
            @if(isset($accommodations) && count($accommodations) > 0)
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Bookings</th>
                            <th>Rating</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accommodations as $accommodation)
                            <tr>
                                <td>
                                    <div class="property-cell">
                                        @if($accommodation->primary_image)
                                            <img src="{{ $accommodation->primary_image_url }}" alt="{{ $accommodation->name }}" class="property-thumb">
                                        @else
                                            <img src="/COMMUNAL.jpg" alt="{{ $accommodation->name }}" class="property-thumb">
                                        @endif
                                        <div class="property-cell-info">
                                            <h4>{{ $accommodation->name }}</h4>
                                            <p>Brgy. {{ $accommodation->barangay }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="text-transform: capitalize;">{{ str_replace('-', ' ', $accommodation->type) }}</span>
                                </td>
                                <td>₱{{ number_format($accommodation->price_per_night, 0, '.', ',') }}</td>
                                <td>
                                    @if($accommodation->is_verified)
                                        <span class="status-badge verified">Verified</span>
                                    @elseif($accommodation->is_available)
                                        <span class="status-badge active">Active</span>
                                    @else
                                        <span class="status-badge inactive">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $accommodation->bookings_count ?? 0 }}</td>
                                <td>
                                    <span style="color: #F59E0B;">★</span> {{ number_format($accommodation->rating ?? 0, 1) }}
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="/owner/accommodations/{{ $accommodation->id }}" class="action-btn view" title="View">👁️</a>
                                        <a href="/owner/accommodations/{{ $accommodation->id }}/edit" class="action-btn edit" title="Edit">✏️</a>
                                        <form action="/owner/accommodations/{{ $accommodation->id }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this property?')">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $accommodations->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3>No Properties Yet</h3>
                    <p>Start by adding your first property to the platform.</p>
                    @if($canCreateListing ?? false)
                        <a href="/owner/accommodations/create" class="add-btn inline-flex items-center justify-center">➕ Add Your First Property</a>
                    @else
                        <span class="add-btn add-btn-disabled inline-flex items-center justify-center" title="You’ve reached your plan limit or your subscription isn’t active.">➕ Add Your First Property</span>
                    @endif
                </div>
            @endif
        </div>
    </main>
</body>
</html>
