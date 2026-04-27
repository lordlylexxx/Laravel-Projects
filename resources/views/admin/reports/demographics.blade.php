<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('admin.partials.favicon')
    <title>Demographics Report - IMPASUGONG TOURISM</title>
    <style>
        @include('admin.partials.admin-shell-styles')

        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; }
        .report-page { background: #F3F4F6; color: #1F2937; min-height: 100vh; }
        .report-page .report-container { max-width: 1180px; margin: 24px auto; padding: 0 20px 30px; }
        .report-page .card { background: #fff; border: 1px solid #E5E7EB; border-radius: 14px; padding: 18px; margin-bottom: 16px; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05); }
        .report-page .title { color: #166534; margin-bottom: 6px; font-size: 1.25rem; }
        .report-page .section-title { color: #166534; margin: 0 0 8px; font-size: 1rem; }
        .report-page .muted { color: #6B7280; font-size: 0.9rem; }
        .report-page .summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; margin-top: 14px; }
        .report-page .pill { border: 1px solid #D1FAE5; background: linear-gradient(135deg, #ECFDF5, #F0FDF4); border-radius: 10px; padding: 10px; text-align: center; }
        .report-page .pill .value { font-size: 1.1rem; font-weight: 700; color: #166534; line-height: 1.2; }
        .report-page .pill .label { font-size: 0.72rem; text-transform: uppercase; color: #4B5563; letter-spacing: 0.2px; }
        .report-page .filters-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; align-items: end; }
        .report-page .actions-row { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
        .report-page .field label { display: block; font-size: 0.75rem; text-transform: uppercase; color: #6B7280; margin-bottom: 5px; font-weight: 600; }
        .report-page .field input, .report-page .field select { width: 100%; padding: 9px 10px; border-radius: 8px; border: 1px solid #D1D5DB; background: #fff; }
        .report-page .field input:focus, .report-page .field select:focus { outline: none; border-color: #16A34A; box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.15); }
        .report-page .btn { border: 1px solid transparent; border-radius: 8px; padding: 10px 12px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; font-size: 0.86rem; min-height: 40px; }
        .report-page .btn.primary { background: #16A34A; color: white; }
        .report-page .btn.primary:hover { background: #15803D; }
        .report-page .btn.secondary { background: #F9FAFB; color: #1F2937; border-color: #D1D5DB; }
        .report-page .btn.secondary:hover { background: #F3F4F6; }
        .report-page .btn.export { background: #ECFDF5; color: #166534; border-color: #BBF7D0; }
        .report-page .btn.export:hover { background: #DCFCE7; }
        .report-page table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .report-page th, .report-page td { border-bottom: 1px solid #E5E7EB; padding: 8px 10px; text-align: left; font-size: 0.88rem; }
        .report-page th { background: #F9FAFB; color: #374151; font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.2px; position: sticky; top: 0; }
        .report-page .cols { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .report-page .table-wrap { max-height: 360px; overflow: auto; border: 1px solid #EEF2F7; border-radius: 10px; }
        @media (max-width: 900px) {
            .report-page .summary, .report-page .filters-grid, .report-page .cols { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @include('admin.partials.top-navbar', ['active' => 'dashboard'])
    <div class="report-page">
    <div class="report-container" style="padding-top: 90px;">
        <div class="card">
            <h1 class="title">Demographics Report</h1>
            <p class="muted">{{ $demographics['scope_label'] }} | {{ $demographics['start_date']->toFormattedDateString() }} - {{ $demographics['end_date']->toFormattedDateString() }}</p>
            <form method="GET" action="{{ route('admin.reports.demographics', [], false) }}" class="filters-grid" style="margin-top:12px;">
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
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button class="btn primary" type="submit">Apply</button>
                    <a class="btn secondary" href="{{ route('admin.dashboard', ['tenant_id' => $selectedTenantId, 'start_date' => optional($demographicsStartDate)->toDateString(), 'end_date' => optional($demographicsEndDate)->toDateString()], false) }}">Back</a>
                </div>
            </form>
            <div class="actions-row">
                <form method="POST" action="{{ route('admin.reports.demographics.export', [], false) }}">
                    @csrf
                    <input type="hidden" name="format" value="pdf">
                    <input type="hidden" name="tenant_id" value="{{ $selectedTenantId }}">
                    <input type="hidden" name="start_date" value="{{ optional($demographicsStartDate)->toDateString() }}">
                    <input type="hidden" name="end_date" value="{{ optional($demographicsEndDate)->toDateString() }}">
                    <button class="btn export" type="submit">Export PDF</button>
                </form>
                <form method="POST" action="{{ route('admin.reports.demographics.export', [], false) }}">
                    @csrf
                    <input type="hidden" name="format" value="csv">
                    <input type="hidden" name="tenant_id" value="{{ $selectedTenantId }}">
                    <input type="hidden" name="start_date" value="{{ optional($demographicsStartDate)->toDateString() }}">
                    <input type="hidden" name="end_date" value="{{ optional($demographicsEndDate)->toDateString() }}">
                    <button class="btn export" type="submit">Export CSV</button>
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
                <h3 class="section-title">Gender Distribution</h3>
                <div class="table-wrap">
                <table>
                    <thead><tr><th>Gender</th><th>Bookings</th></tr></thead>
                    <tbody>
                    @foreach($demographics['gender']['raw'] as $label => $count)
                        <tr><td>{{ ucfirst($label) }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            <div class="card">
                <h3 class="section-title">Age Distribution</h3>
                <div class="table-wrap">
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
        </div>

        <div class="cols">
            <div class="card">
                <h3 class="section-title">Location Totals</h3>
                <div class="table-wrap">
                <table>
                    <thead><tr><th>Location Type</th><th>Bookings</th></tr></thead>
                    <tbody>
                    @foreach($demographics['location']['raw'] as $label => $count)
                        <tr><td>{{ ucfirst($label) }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            <div class="card">
                <h3 class="section-title">Local / Foreign Breakdown</h3>
                <div class="table-wrap">
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
    </div>
    </div>
</body>
</html>
