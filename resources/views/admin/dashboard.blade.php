<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('admin.partials.favicon')
    <title>Admin Dashboard - ImpaStay</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-50: #F9FAFB; --gray-100: #F3F4F6; --gray-200: #E5E7EB;
            --gray-300: #D1D5DB; --gray-400: #9CA3AF; --gray-500: #6B7280;
            --gray-600: #4B5563; --gray-700: #374151; --gray-800: #1F2937;
            --blue-500: #3B82F6; --blue-600: #2563EB;
            --red-500: #EF4444; --orange-500: #F97316;
            --purple-500: #8B5CF6; --cyan-500: #06B6D4;
            --amber-500: #F59E0B;
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
            padding: 0 40px;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.1);
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
            padding: 10px 16px; 
            border-radius: 8px; 
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-links a:hover, .nav-links a.active { 
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); 
            color: var(--white);
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
        }
        
        .nav-actions { display: flex; gap: 15px; align-items: center; }
        .user-display {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: linear-gradient(135deg, var(--green-soft), var(--green-white));
            border-radius: 10px;
            border: 1px solid var(--green-soft);
        }
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .user-info {
            text-align: left;
        }
        .user-name {
            font-weight: 700;
            color: var(--green-dark);
            font-size: 0.95rem;
            line-height: 1.2;
        }
        .user-role {
            font-size: 0.75rem;
            color: var(--green-medium);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .nav-btn { 
            padding: 10px 20px; 
            border-radius: 8px; 
            font-weight: 600; 
            text-decoration: none;
            transition: all 0.3s; 
            cursor: pointer; 
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-btn.primary { 
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary)); 
            color: var(--white); 
        }
        .nav-btn.primary:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
        }
        
        /* Main Layout */
        .dashboard-layout { display: flex; padding-top: 80px; }
        
        /* Main Content */
        .main-content { flex: 1; padding: 30px 40px; min-height: calc(100vh - 80px); }
        
        /* Page Header */
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 2rem; color: var(--green-dark); margin-bottom: 5px; font-weight: 700; }
        .page-header p { color: var(--gray-500); font-size: 0.95rem; }
        
        /* KPI Cards */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .kpi-card {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(27, 94, 32, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s;
            border: 1px solid var(--green-soft);
        }
        .kpi-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 15px 40px rgba(27, 94, 32, 0.15);
        }
        .kpi-icon { 
            width: 60px; 
            height: 60px; 
            border-radius: 14px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.5rem;
        }
        .kpi-icon.green { background: linear-gradient(135deg, var(--green-soft), var(--green-pale)); color: var(--green-dark); }
        .kpi-icon.blue { background: linear-gradient(135deg, #DBEAFE, #BFDBFE); color: var(--blue-600); }
        .kpi-icon.orange { background: linear-gradient(135deg, #FEF3C7, #FDE68A); color: var(--amber-500); }
        .kpi-icon.purple { background: linear-gradient(135deg, #EDE9FE, #DDD6FE); color: var(--purple-500); }
        .kpi-icon.red { background: linear-gradient(135deg, #FEE2E2, #FECACA); color: var(--red-500); }
        .kpi-icon.cyan { background: linear-gradient(135deg, #CFFAFE, #A5F3FC); color: var(--cyan-500); }
        
        .kpi-info h3 { font-size: 1.8rem; color: var(--green-dark); margin-bottom: 3px; font-weight: 700; }
        .kpi-info p { color: var(--gray-500); font-size: 0.85rem; }
        
        /* Dashboard Card */
        .dashboard-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(27, 94, 32, 0.08);
            padding: 18px;
            margin-bottom: 18px;
            border: 1px solid var(--green-soft);
        }
        .dashboard-card h3 { 
            font-size: 1rem; 
            color: var(--gray-800); 
            margin-bottom: 14px; 
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .dashboard-card h3 .icon { color: var(--green-primary); }

        .filter-card {
            background: var(--white);
            border: 1px solid var(--green-soft);
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(27, 94, 32, 0.08);
            padding: 18px;
            margin-bottom: 18px;
        }
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            align-items: end;
        }
        .filter-field label {
            display: block;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: var(--gray-500);
            margin-bottom: 6px;
            font-weight: 600;
        }
        .filter-field input,
        .filter-field select {
            width: 100%;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            padding: 9px 11px;
            font-size: 0.9rem;
            color: var(--gray-700);
            background: var(--white);
        }
        .btn-filter {
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .btn-filter.primary {
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: white;
        }
        .btn-filter.secondary {
            background: var(--gray-100);
            color: var(--gray-700);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .demographics-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 18px;
        }
        .demographics-meta {
            color: var(--gray-500);
            font-size: 0.85rem;
            margin-bottom: 12px;
        }
        .breakdown-list {
            margin-top: 12px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }
        .breakdown-block {
            background: var(--green-white);
            border-radius: 10px;
            padding: 10px;
        }
        .breakdown-block h4 {
            font-size: 0.78rem;
            color: var(--gray-600);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.82rem;
            color: var(--gray-700);
            margin-bottom: 4px;
        }
        .demographics-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }
        .demographics-pill {
            background: linear-gradient(135deg, var(--cream), var(--green-white));
            border: 1px solid var(--green-soft);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
        }
        .demographics-pill .value {
            display: block;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--green-dark);
        }
        .demographics-pill .label {
            display: block;
            color: var(--gray-600);
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }
        
        /* Content Grid */
        .content-grid { display: grid; grid-template-columns: 320px 1fr; gap: 20px; }
        .content-left { display: flex; flex-direction: column; gap: 15px; }
        .content-right { display: flex; flex-direction: column; gap: 15px; }
        
        /* Chart Container */
        .chart-container { position: relative; height: 280px; }
        .chart-container-sm { position: relative; height: 250px; }
        
        /* Table */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 14px; text-align: left; border-bottom: 1px solid var(--gray-200); }
        .data-table th { font-weight: 600; color: var(--gray-600); font-size: 0.8rem; text-transform: uppercase; background: var(--cream); }
        .data-table tr:hover { background: var(--green-white); }
        
        /* Status Badges */
        .status-badge { display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.active { background: linear-gradient(135deg, var(--green-soft), var(--green-pale)); color: var(--green-dark); }
        .status-badge.pending { background: linear-gradient(135deg, #FEF3C7, #FDE68A); color: #B45309; }
        .status-badge.confirmed { background: linear-gradient(135deg, #DBEAFE, #BFDBFE); color: #1D4ED8; }
        .status-badge.cancelled { background: linear-gradient(135deg, #FEE2E2, #FECACA); color: #DC2626; }
        .status-badge.completed { background: linear-gradient(135deg, var(--green-soft), var(--green-pale)); color: var(--green-dark); }
        .status-badge.past-due { background: linear-gradient(135deg, #FEE2E2, #FCA5A5); color: #B91C1C; }
        .status-badge.trialing { background: linear-gradient(135deg, #FEF3C7, #FDE68A); color: #92400E; }
        .status-badge.cancelled { background: linear-gradient(135deg, #F3F4F6, #E5E7EB); color: var(--gray-600); }

        .table-note {
            color: var(--gray-500);
            font-size: 0.85rem;
            margin-bottom: 14px;
        }
        
        /* Quick Stats Grid */
        .quick-stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .quick-stat-card {
            background: linear-gradient(135deg, var(--cream), var(--green-white));
            padding: 16px;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid var(--green-soft);
            min-height: 110px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .quick-stat-card:hover { 
            background: linear-gradient(135deg, var(--green-soft), var(--green-pale)); 
            transform: translateY(-3px);
        }
        .quick-stat-card .icon { font-size: 1.6rem; color: var(--green-primary); margin-bottom: 6px; }
        .quick-stat-card h4 { font-size: 1.3rem; color: var(--green-dark); margin-bottom: 3px; font-weight: 700; }
        .quick-stat-card p { color: var(--gray-600); font-size: 0.8rem; }
        
        /* Gear Icon (Settings) - Icon Only */
        .settings-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--green-soft), var(--green-pale));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--green-dark);
            font-size: 1.2rem;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }
        .settings-icon:hover {
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: white;
            transform: rotate(90deg);
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .content-grid { grid-template-columns: 1fr; }
            .demographics-grid { grid-template-columns: 1fr; }
            .demographics-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 768px) {
            .navbar { padding: 0 20px; height: 60px; }
            .nav-links { display: none; }
            .main-content { padding: 20px; }
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
            .demographics-summary { grid-template-columns: 1fr; }
            .breakdown-list { grid-template-columns: 1fr; }
        }
        
        /* Animations */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate { animation: fadeInUp 0.6s ease forwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }

        @include('admin.partials.top-navbar-styles')
    </style>
</head>
<body>
    <!-- Navigation -->
    @include('admin.partials.top-navbar', ['active' => 'dashboard'])
    
    <!-- Dashboard Layout -->
    <div class="dashboard-layout">
        <!-- Main Content -->
        <main class="main-content">
            @if(session('success'))
                <div style="background: #ECFDF5; border: 1px solid #86EFAC; color: #166534; padding: 10px 12px; border-radius: 10px; margin-bottom: 16px; font-weight: 600;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Page Header -->
            <div class="page-header animate">
                <h1><i class="fas fa-chart-line" style="color: var(--green-primary); margin-right: 12px;"></i>Sales Monitoring Dashboard</h1>
                <p>Business performance metrics and analytics</p>
            </div>

            <div class="filter-card animate delay-1">
                <h3 style="margin-bottom: 12px; display:flex; align-items:center; gap:8px;"><i class="fas fa-filter icon"></i>Demographics Filters & Reports</h3>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="filters-grid">
                    <div class="filter-field">
                        <label for="tenant_id">Tenant Scope</label>
                        <select id="tenant_id" name="tenant_id">
                            <option value="">All tenants</option>
                            @foreach($tenantFilterOptions as $tenantOption)
                                <option value="{{ $tenantOption->id }}" @selected((int) ($selectedTenantId ?? 0) === (int) $tenantOption->id)>{{ $tenantOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field">
                        <label for="start_date">Start Date</label>
                        <input id="start_date" type="date" name="start_date" value="{{ optional($demographicsStartDate)->toDateString() }}">
                    </div>
                    <div class="filter-field">
                        <label for="end_date">End Date</label>
                        <input id="end_date" type="date" name="end_date" value="{{ optional($demographicsEndDate)->toDateString() }}">
                    </div>
                    <div class="filter-field" style="display:flex; gap:8px;">
                        <button type="submit" class="btn-filter primary"><i class="fas fa-chart-line"></i> Apply</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn-filter secondary">Reset</a>
                    </div>
                </form>
                <div style="margin-top: 12px; display:flex; gap:8px; flex-wrap:wrap;">
                    <a class="btn-filter secondary" href="{{ route('admin.reports.demographics', ['tenant_id' => $selectedTenantId, 'start_date' => optional($demographicsStartDate)->toDateString(), 'end_date' => optional($demographicsEndDate)->toDateString()]) }}">
                        <i class="fas fa-eye"></i> View Demographics Report
                    </a>
                    <form method="POST" action="{{ route('admin.reports.demographics.export') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="format" value="pdf">
                        <input type="hidden" name="tenant_id" value="{{ $selectedTenantId }}">
                        <input type="hidden" name="start_date" value="{{ optional($demographicsStartDate)->toDateString() }}">
                        <input type="hidden" name="end_date" value="{{ optional($demographicsEndDate)->toDateString() }}">
                        <button type="submit" class="btn-filter secondary"><i class="fas fa-file-pdf"></i> Export PDF</button>
                    </form>
                    <form method="POST" action="{{ route('admin.reports.demographics.export') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="format" value="csv">
                        <input type="hidden" name="tenant_id" value="{{ $selectedTenantId }}">
                        <input type="hidden" name="start_date" value="{{ optional($demographicsStartDate)->toDateString() }}">
                        <input type="hidden" name="end_date" value="{{ optional($demographicsEndDate)->toDateString() }}">
                        <button type="submit" class="btn-filter secondary"><i class="fas fa-file-csv"></i> Export CSV</button>
                    </form>
                </div>
            </div>

            <div class="dashboard-card animate delay-1">
                <h3><i class="fas fa-people-group icon"></i>Booking Demographics</h3>
                <p class="demographics-meta">
                    {{ $demographics['scope_label'] ?? 'All tenants' }} |
                    {{ optional($demographicsStartDate)->toFormattedDateString() }} - {{ optional($demographicsEndDate)->toFormattedDateString() }}
                </p>

                @if(empty($demographics['columns_ready']))
                    <div style="background:#FFFBEB; border:1px solid #FCD34D; color:#92400E; padding:10px 12px; border-radius:10px; margin-bottom:12px;">
                        Demographic columns are not available yet in `bookings`. Run migrations first to populate this section.
                    </div>
                @endif

                <div class="demographics-summary">
                    <div class="demographics-pill">
                        <span class="value">{{ number_format($demographics['total_bookings'] ?? 0) }}</span>
                        <span class="label">Bookings in Scope</span>
                    </div>
                    <div class="demographics-pill">
                        <span class="value">{{ number_format($demographics['total_guests'] ?? 0) }}</span>
                        <span class="label">Guests in Scope</span>
                    </div>
                    <div class="demographics-pill">
                        <span class="value">{{ number_format($demographics['profiled_bookings'] ?? 0) }}</span>
                        <span class="label">Profiled Bookings</span>
                    </div>
                    <div class="demographics-pill">
                        <span class="value">{{ isset($demographics['average_age']) && $demographics['average_age'] !== null ? $demographics['average_age'] : 'N/A' }}</span>
                        <span class="label">Average Age</span>
                    </div>
                </div>

                <div class="demographics-grid">
                    <div class="dashboard-card" style="margin-bottom:0;">
                        <h3><i class="fas fa-venus-mars icon"></i>Gender Distribution</h3>
                        <div class="chart-container-sm">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                    <div class="dashboard-card" style="margin-bottom:0;">
                        <h3><i class="fas fa-map-marked-alt icon"></i>Location Distribution</h3>
                        <div class="chart-container-sm">
                            <canvas id="locationChart"></canvas>
                        </div>
                        <div class="breakdown-list">
                            <div class="breakdown-block">
                                <h4>Local Places</h4>
                                @forelse(collect($demographics['location']['breakdown']['local_labels'] ?? [])->take(5) as $i => $label)
                                    <div class="breakdown-item"><span>{{ $label }}</span><strong>{{ $demographics['location']['breakdown']['local_counts'][$i] ?? 0 }}</strong></div>
                                @empty
                                    <div class="breakdown-item"><span>No local breakdown yet</span><strong>-</strong></div>
                                @endforelse
                            </div>
                            <div class="breakdown-block">
                                <h4>Foreign Countries</h4>
                                @forelse(collect($demographics['location']['breakdown']['foreign_labels'] ?? [])->take(5) as $i => $label)
                                    <div class="breakdown-item"><span>{{ $label }}</span><strong>{{ $demographics['location']['breakdown']['foreign_counts'][$i] ?? 0 }}</strong></div>
                                @empty
                                    <div class="breakdown-item"><span>No foreign breakdown yet</span><strong>-</strong></div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-card" style="margin-bottom:0;">
                        <h3><i class="fas fa-user-clock icon"></i>Age Distribution</h3>
                        <div class="chart-container-sm">
                            <canvas id="ageChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- KPI Cards -->
            <div class="kpi-grid animate delay-1">
                <div class="kpi-card">
                    <div class="kpi-icon green"><i class="fas fa-peso-sign"></i></div>
                    <div class="kpi-info">
                        <h3>₱{{ number_format($kpis['total_revenue'] ?? 0, 0, '.', ',') }}</h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon blue"><i class="fas fa-calendar-week"></i></div>
                    <div class="kpi-info">
                        <h3>₱{{ number_format($weeklyRevenue ?? 0, 0, '.', ',') }}</h3>
                        <p>Weekly Revenue</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon orange"><i class="fas fa-calendar"></i></div>
                    <div class="kpi-info">
                        <h3>₱{{ number_format($monthlyRevenue ?? 0, 0, '.', ',') }}</h3>
                        <p>Monthly Revenue</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon purple"><i class="fas fa-calendar-year"></i></div>
                    <div class="kpi-info">
                        <h3>₱{{ number_format($yearlyRevenue ?? 0, 0, '.', ',') }}</h3>
                        <p>Yearly Revenue</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon cyan"><i class="fas fa-ticket-alt"></i></div>
                    <div class="kpi-info">
                        <h3>{{ number_format($kpis['total_bookings'] ?? 0) }}</h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon green"><i class="fas fa-users"></i></div>
                    <div class="kpi-info">
                        <h3>{{ number_format($kpis['active_clients'] ?? 0) }}</h3>
                        <p>Active Clients</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon orange"><i class="fas fa-trophy"></i></div>
                    <div class="kpi-info">
                        <h3 style="font-size: 1.2rem;">{{ $topTenantByBookings->name ?? 'N/A' }}</h3>
                        <p>Top Tenant by Bookings</p>
                    </div>
                </div>
            </div>
            
            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Left Column -->
                <div class="content-left">
                    <!-- Business KPI Overview -->
                    <div class="dashboard-card animate delay-2">
                        <h3><i class="fas fa-bullseye icon"></i>Business KPI Overview</h3>
                        <div class="quick-stats-grid">
                            <div class="quick-stat-card">
                                <div class="icon"><i class="fas fa-trending-up"></i></div>
                                <h4>{{ $growthRate ?? 0 }}%</h4>
                                <p>Growth Rate</p>
                            </div>
                            <div class="quick-stat-card">
                                <div class="icon"><i class="fas fa-star"></i></div>
                                <h4>₱{{ number_format($kpis['average_booking_value'] ?? 0, 0) }}</h4>
                                <p>Avg Booking</p>
                            </div>
                            <div class="quick-stat-card">
                                <div class="icon"><i class="fas fa-home"></i></div>
                                <h4>{{ number_format($kpis['total_accommodations'] ?? 0) }}</h4>
                                <p>Total Units</p>
                            </div>
                            <div class="quick-stat-card">
                                <div class="icon"><i class="fas fa-check-circle"></i></div>
                                <h4>{{ number_format($kpis['verified_properties'] ?? 0) }}</h4>
                                <p>Verified Units</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Revenue Distribution -->
                    <div class="dashboard-card animate delay-3">
                        <h3><i class="fas fa-chart-pie icon"></i>Revenue Distribution</h3>
                        <div class="chart-container-sm">
                            <canvas id="revenueDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="content-right">
                    <!-- Monthly Revenue Trend -->
                    <div class="dashboard-card animate delay-2">
                        <h3><i class="fas fa-chart-area icon"></i>Monthly Revenue Trend</h3>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Guests Per Month -->
                    <div class="dashboard-card animate delay-3">
                        <h3><i class="fas fa-chart-bar icon"></i>Guests Per Month</h3>
                        <div class="chart-container">
                            <canvas id="guestsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tenant Bookings Today -->
            <div class="dashboard-card animate delay-4">
                <h3><i class="fas fa-users-check icon"></i>Today's Tenant Bookings</h3>
                <p class="table-note"><i class="fas fa-info-circle"></i> Shows number of guests per tenant with active check-ins today</p>
                @if(isset($tenantBookingsToday) && count($tenantBookingsToday) > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-building"></i> Tenant Name</th>
                                <th><i class="fas fa-calendar-check"></i> Active Bookings</th>
                                <th><i class="fas fa-users"></i> Total Guests</th>
                                <th><i class="fas fa-download"></i> Monthly Report</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tenantBookingsToday as $booking)
                                <tr>
                                    <td><strong>{{ $booking->name }}</strong></td>
                                    <td>{{ $booking->booking_count }}</td>
                                    <td><span style="background: linear-gradient(135deg, var(--green-soft), var(--green-pale)); padding: 6px 12px; border-radius: 50px; color: var(--green-dark); font-weight: 600;">{{ $booking->total_guests }} guests</span></td>
                                    <td>
                                        <form action="{{ route('admin.monthly-booking-pdf') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="year" value="{{ now()->year }}">
                                            <input type="hidden" name="month" value="{{ now()->month }}">
                                            <button type="submit" style="background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: white; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; transition: all 0.3s;">
                                                <i class="fas fa-download"></i> PDF
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            <tr style="background: var(--cream); font-weight: 600;">
                                <td colspan="1"><strong>Total This Month:</strong></td>
                                <td colspan="3">
                                    <span style="color: var(--green-dark);">
                                        {{ $tenantBookingsToday->sum('booking_count') }} Bookings | 
                                        {{ $tenantBookingsToday->sum('total_guests') }} Guests
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 40px; color: var(--gray-400);">
                        <i class="fas fa-calendar-alt" style="font-size: 3rem; margin-bottom: 15px; color: var(--gray-300);"></i>
                        <p>No active bookings today</p>
                    </div>
                @endif
            </div>

        </main>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const demographics = @json($demographics ?? []);

            const genderChartEl = document.getElementById('genderChart');
            if (genderChartEl) {
                new Chart(genderChartEl.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: demographics.gender?.labels ?? ['Male', 'Female', 'Unspecified'],
                        datasets: [{
                            data: demographics.gender?.counts ?? [0, 0, 0],
                            backgroundColor: ['#2E7D32', '#8B5CF6', '#D1D5DB'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }

            const locationChartEl = document.getElementById('locationChart');
            if (locationChartEl) {
                new Chart(locationChartEl.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: demographics.location?.labels ?? ['Local', 'Foreign', 'Unspecified'],
                        datasets: [{
                            label: 'Bookings',
                            data: demographics.location?.counts ?? [0, 0, 0],
                            backgroundColor: ['#2E7D32', '#2563EB', '#9CA3AF'],
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            const ageChartEl = document.getElementById('ageChart');
            if (ageChartEl) {
                new Chart(ageChartEl.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: demographics.age?.labels ?? ['0-17', '18-24', '25-34', '35-44', '45-54', '55+', 'Unspecified'],
                        datasets: [{
                            label: 'Bookings',
                            data: demographics.age?.counts ?? [0, 0, 0, 0, 0, 0, 0],
                            backgroundColor: '#F59E0B',
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            // Monthly Revenue Line Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: [
                            {{ $monthlyRevenueData['jan'] ?? 0 }},
                            {{ $monthlyRevenueData['feb'] ?? 0 }},
                            {{ $monthlyRevenueData['mar'] ?? 0 }},
                            {{ $monthlyRevenueData['apr'] ?? 0 }},
                            {{ $monthlyRevenueData['may'] ?? 0 }},
                            {{ $monthlyRevenueData['jun'] ?? 0 }},
                            {{ $monthlyRevenueData['jul'] ?? 0 }},
                            {{ $monthlyRevenueData['aug'] ?? 0 }},
                            {{ $monthlyRevenueData['sep'] ?? 0 }},
                            {{ $monthlyRevenueData['oct'] ?? 0 }},
                            {{ $monthlyRevenueData['nov'] ?? 0 }},
                            {{ $monthlyRevenueData['dec'] ?? 0 }}
                        ],
                        borderColor: 'rgb(46, 125, 50)',
                        backgroundColor: 'rgba(46, 125, 50, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(46, 125, 50)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
            
            // Guests Per Month (sum of number_of_guests on bookings created in each month)
            const guestsCtx = document.getElementById('guestsChart').getContext('2d');
            new Chart(guestsCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Guests',
                        data: [
                            {{ $monthlyGuestsData['jan'] ?? 0 }},
                            {{ $monthlyGuestsData['feb'] ?? 0 }},
                            {{ $monthlyGuestsData['mar'] ?? 0 }},
                            {{ $monthlyGuestsData['apr'] ?? 0 }},
                            {{ $monthlyGuestsData['may'] ?? 0 }},
                            {{ $monthlyGuestsData['jun'] ?? 0 }},
                            {{ $monthlyGuestsData['jul'] ?? 0 }},
                            {{ $monthlyGuestsData['aug'] ?? 0 }},
                            {{ $monthlyGuestsData['sep'] ?? 0 }},
                            {{ $monthlyGuestsData['oct'] ?? 0 }},
                            {{ $monthlyGuestsData['nov'] ?? 0 }},
                            {{ $monthlyGuestsData['dec'] ?? 0 }}
                        ],
                        backgroundColor: 'rgba(59, 162, 246, 0.8)',
                        borderColor: 'rgb(59, 162, 246)',
                        borderWidth: 1,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Revenue Distribution Doughnut Chart
            const distributionCtx = document.getElementById('revenueDistributionChart').getContext('2d');
            new Chart(distributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Traveller-Inn', 'Airbnb', 'Daily Rental'],
                    datasets: [{
                        data: [
                            {{ $revenueByType['traveller-inn'] ?? 0 }},
                            {{ $revenueByType['airbnb'] ?? 0 }},
                            {{ $revenueByType['daily-rental'] ?? 0 }}
                        ],
                        backgroundColor: [
                            'rgb(46, 125, 50)',
                            'rgb(59, 162, 246)',
                            'rgb(249, 115, 22)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
