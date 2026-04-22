<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    @include('admin.partials.favicon')
    <title>Demographics Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; }
        h1, h2 { margin: 0 0 8px 0; color: #166534; }
        .meta { margin-bottom: 10px; color: #4b5563; }
        .summary { width: 100%; border-collapse: collapse; margin: 10px 0 14px; }
        .summary td { border: 1px solid #d1d5db; padding: 8px; text-align: center; }
        .label { font-size: 10px; text-transform: uppercase; color: #6b7280; }
        .value { font-size: 16px; font-weight: 700; color: #166534; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; font-size: 10px; text-transform: uppercase; color: #374151; }
        .columns { width: 100%; }
        .columns td { vertical-align: top; width: 50%; padding-right: 8px; }
    </style>
</head>
<body>
    <h1>Booking Demographics Report</h1>
    <p class="meta">{{ $demographics['scope_label'] }} | {{ $demographics['start_date']->toDateString() }} to {{ $demographics['end_date']->toDateString() }}</p>

    <table class="summary">
        <tr>
            <td><div class="value">{{ $demographics['total_bookings'] }}</div><div class="label">Bookings</div></td>
            <td><div class="value">{{ $demographics['total_guests'] }}</div><div class="label">Guests</div></td>
            <td><div class="value">{{ $demographics['profiled_bookings'] }}</div><div class="label">Profiled Bookings</div></td>
            <td><div class="value">{{ $demographics['average_age'] ?? 'N/A' }}</div><div class="label">Average Age</div></td>
        </tr>
    </table>

    <table class="columns">
        <tr>
            <td>
                <h2>Gender Distribution</h2>
                <table>
                    <thead><tr><th>Gender</th><th>Bookings</th></tr></thead>
                    <tbody>
                    @foreach($demographics['gender']['raw'] as $label => $count)
                        <tr><td>{{ ucfirst($label) }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                    </tbody>
                </table>

                <h2>Location Totals</h2>
                <table>
                    <thead><tr><th>Type</th><th>Bookings</th></tr></thead>
                    <tbody>
                    @foreach($demographics['location']['raw'] as $label => $count)
                        <tr><td>{{ ucfirst($label) }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </td>
            <td>
                <h2>Age Distribution</h2>
                <table>
                    <thead><tr><th>Bucket</th><th>Bookings</th></tr></thead>
                    <tbody>
                    @foreach($demographics['age']['raw'] as $bucket => $count)
                        <tr><td>{{ $bucket }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                    </tbody>
                </table>

                <h2>Location Breakdown</h2>
                <table>
                    <thead><tr><th>Area</th><th>Bookings</th></tr></thead>
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
            </td>
        </tr>
    </table>
</body>
</html>
