<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('admin.partials.favicon')
    <title>Demographics Report - IMPASUGONG TOURISM</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #F8FAFC; color: #1F2937; margin: 0; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px 30px; }
        .card { background: #fff; border: 1px solid #E5E7EB; border-radius: 14px; padding: 18px; margin-bottom: 16px; }
        .title { color: #166534; margin-bottom: 6px; }
        .muted { color: #6B7280; font-size: 0.9rem; }
        .summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-top: 12px; }
        .pill { border: 1px solid #D1FAE5; background: #ECFDF5; border-radius: 10px; padding: 10px; text-align: center; }
        .pill .value { font-size: 1.2rem; font-weight: 700; color: #166534; }
        .pill .label { font-size: 0.75rem; text-transform: uppercase; color: #4B5563; }
        .filters-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; align-items: end; }
        .field label { display: block; font-size: 0.75rem; text-transform: uppercase; color: #6B7280; margin-bottom: 5px; }
        .field input, .field select { width: 100%; padding: 9px 10px; border-radius: 8px; border: 1px solid #D1D5DB; }
        .btn { border: none; border-radius: 8px; padding: 10px 12px; font-weight: 600; cursor: pointer; }
        .btn.primary { background: #16A34A; color: white; }
        .btn.link { background: #E5E7EB; color: #1F2937; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border-bottom: 1px solid #E5E7EB; padding: 8px 10px; text-align: left; font-size: 0.9rem; }
        th { background: #F9FAFB; color: #374151; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.2px; }
        .cols { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        @media (max-width: 900px) {
            .summary, .filters-grid, .cols { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @include('admin.partials.top-navbar', ['active' => 'dashboard'])
    <div class="container" style="padding-top: 90px;">
        <div class="card">
            <h1 class="title">Demographics Report</h1>
            <p class="muted">{{ $demographics['scope_label'] }} | {{ $demographics['start_date']->toFormattedDateString() }} - {{ $demographics['end_date']->toFormattedDateString() }}</p>
            <form method="GET" action="{{ route('admin.reports.demographics') }}" class="filters-grid" style="margin-top:12px;">
                <div class="field">
                    <label for="tenant_id">Tenant Scope</label>
                    <select id="tenant_id" name="tenant_id">
                        <option value="">All tenants</option>
                        @foreach($tenantFilterOptions as $tenantOption)
                            <option value="{{ $tenantOption->id }}" @selected((int) ($selectedTenantId ?? 0) === (int) $tenantOption->id)>{{ $tenantOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="start_date">Start Date</label>
                    <input id="start_date" type="date" name="start_date" value="{{ optional($demographicsStartDate)->toDateString() }}">
                </div>
                <div class="field">
                    <label for="end_date">End Date</label>
                    <input id="end_date" type="date" name="end_date" value="{{ optional($demographicsEndDate)->toDateString() }}">
                </div>
                <div style="display:flex; gap:8px;">
                    <button class="btn primary" type="submit">Apply</button>
                    <a class="btn link" href="{{ route('admin.dashboard', ['tenant_id' => $selectedTenantId, 'start_date' => optional($demographicsStartDate)->toDateString(), 'end_date' => optional($demographicsEndDate)->toDateString()]) }}">Back</a>
                </div>
            </form>
            <div style="display:flex; gap:8px; margin-top:10px;">
                <form method="POST" action="{{ route('admin.reports.demographics.export') }}">
                    @csrf
                    <input type="hidden" name="format" value="pdf">
                    <input type="hidden" name="tenant_id" value="{{ $selectedTenantId }}">
                    <input type="hidden" name="start_date" value="{{ optional($demographicsStartDate)->toDateString() }}">
                    <input type="hidden" name="end_date" value="{{ optional($demographicsEndDate)->toDateString() }}">
                    <button class="btn link" type="submit">Export PDF</button>
                </form>
                <form method="POST" action="{{ route('admin.reports.demographics.export') }}">
                    @csrf
                    <input type="hidden" name="format" value="csv">
                    <input type="hidden" name="tenant_id" value="{{ $selectedTenantId }}">
                    <input type="hidden" name="start_date" value="{{ optional($demographicsStartDate)->toDateString() }}">
                    <input type="hidden" name="end_date" value="{{ optional($demographicsEndDate)->toDateString() }}">
                    <button class="btn link" type="submit">Export CSV</button>
                </form>
            </div>

            <div class="summary">
                <div class="pill"><div class="value">{{ $demographics['total_bookings'] }}</div><div class="label">Bookings</div></div>
                <div class="pill"><div class="value">{{ $demographics['total_guests'] }}</div><div class="label">Guests</div></div>
                <div class="pill"><div class="value">{{ $demographics['profiled_bookings'] }}</div><div class="label">Profiled</div></div>
                <div class="pill"><div class="value">{{ $demographics['average_age'] ?? 'N/A' }}</div><div class="label">Average Age</div></div>
            </div>
        </div>

        <div class="cols">
            <div class="card">
                <h3 class="title">Gender Distribution</h3>
                <table>
                    <thead><tr><th>Gender</th><th>Bookings</th></tr></thead>
                    <tbody>
                    @foreach($demographics['gender']['raw'] as $label => $count)
                        <tr><td>{{ ucfirst($label) }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card">
                <h3 class="title">Age Distribution</h3>
                <table>
                    <thead><tr><th>Age Bucket</th><th>Bookings</th></tr></thead>
                    <tbody>
                    @foreach($demographics['age']['raw'] as $bucket => $count)
                        <tr><td>{{ $bucket }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="cols">
            <div class="card">
                <h3 class="title">Location Totals</h3>
                <table>
                    <thead><tr><th>Location Type</th><th>Bookings</th></tr></thead>
                    <tbody>
                    @foreach($demographics['location']['raw'] as $label => $count)
                        <tr><td>{{ ucfirst($label) }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card">
                <h3 class="title">Local / Foreign Breakdown</h3>
                <table>
                    <thead><tr><th>Area</th><th>Count</th></tr></thead>
                    <tbody>
                    @forelse($demographics['location']['breakdown']['local_labels'] as $i => $place)
                        <tr><td>Local: {{ $place }}</td><td>{{ $demographics['location']['breakdown']['local_counts'][$i] ?? 0 }}</td></tr>
                    @empty
                        <tr><td colspan="2">No local place data</td></tr>
                    @endforelse
                    @forelse($demographics['location']['breakdown']['foreign_labels'] as $i => $country)
                        <tr><td>Foreign: {{ $country }}</td><td>{{ $demographics['location']['breakdown']['foreign_counts'][$i] ?? 0 }}</td></tr>
                    @empty
                        <tr><td colspan="2">No foreign country data</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
