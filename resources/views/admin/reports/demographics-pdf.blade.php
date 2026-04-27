<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    @include('admin.partials.favicon')
    <title>Demographics Report</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; color: #1f2937; font-size: 12px; }
        .header { margin-bottom: 3px; border-bottom: 2px solid #2E7D32; padding-bottom: 0; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        .header-table td { border: none; vertical-align: middle; }
        .header-left { width: 170px; text-align: left; }
        .header-center { text-align: center; }
        .header-right { width: 170px; text-align: right; }
        .header-side-logo { width: 74px; height: 74px; display: inline-block; object-fit: contain; }
        .header-right-logo { width: 170px; height: 150px; object-fit: cover; }
        .header-topline { font-size: 12px; color: #4B5563; margin-bottom: 3px; }
        .header-main { font-size: 20px; font-weight: 700; color: #1F2937; letter-spacing: 0.2px; margin-bottom: 2px; }
        .header-office { font-size: 14px; font-weight: 700; color: #1F2937; margin-bottom: 2px; }
        .header-report-line { font-size: 12px; color: #4B5563; margin-bottom: 1px; }
        h1, h2 { margin: 0 0 6px 0; color: #166534; }
        h1 { font-size: 16px; }
        h2 { font-size: 13px; }
        .meta { margin-bottom: 5px; color: #4b5563; font-size: 11px; }
        .summary { width: 100%; border-collapse: collapse; margin: 6px 0 8px; }
        .summary td { border: 1px solid #d1d5db; padding: 3px 4px; text-align: center; }
        .label { font-size: 9px; text-transform: uppercase; color: #6b7280; line-height: 1.2; }
        .value { font-size: 11px; font-weight: 700; color: #166534; line-height: 1.2; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #e5e7eb; padding: 5px 6px; text-align: left; font-size: 12px; }
        th { background: #f3f4f6; font-size: 12px; text-transform: uppercase; color: #374151; }
        .columns { width: 100%; }
        .columns td { vertical-align: top; width: 50%; padding-right: 8px; }
        .footer { margin-top: 10px; border-top: 1px solid #d1d5db; padding-top: 6px; }
        .footer-row { width: 100%; border-collapse: collapse; }
        .footer-row td { border: none; font-size: 12px; color: #374151; padding: 2px 0; }
        .footer-left { text-align: left; }
        .footer-center { text-align: center; }
        .footer-right { text-align: right; }
        .pagenum:before { content: counter(page); }
        .pagecount:before { content: counter(pages); }
    </style>
</head>
<body>
    @php
        $leftLogoCandidates = [
            public_path('report-headers/ca-left-logo.png'),
            '/Users/yanreyestrada/.cursor/projects/Users-yanreyestrada-Documents-Systems-Laravel-Projects/assets/logo-07b7d8b6-dfca-41f3-a0f6-cd597cd88a1e.png',
        ];
        $leftLogoData = null;
        foreach ($leftLogoCandidates as $candidatePath) {
            if (is_string($candidatePath) && $candidatePath !== '' && file_exists($candidatePath)) {
                $mime = function_exists('mime_content_type') ? (mime_content_type($candidatePath) ?: 'image/png') : 'image/png';
                $leftLogoData = 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($candidatePath));
                break;
            }
        }

        $rightLogoCandidates = [
            public_path('report-headers/ca-right-logo.png'),
            '/Users/yanreyestrada/.cursor/projects/Users-yanreyestrada-Documents-Systems-Laravel-Projects/assets/515313979_729318819730861_9203702352099745495_n-d3bbf38f-364b-44b4-8ce0-e6c997db9063.png',
        ];
        $rightLogoData = null;
        foreach ($rightLogoCandidates as $candidatePath) {
            if (is_string($candidatePath) && $candidatePath !== '' && file_exists($candidatePath)) {
                $mime = function_exists('mime_content_type') ? (mime_content_type($candidatePath) ?: 'image/png') : 'image/png';
                $rightLogoData = 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($candidatePath));
                break;
            }
        }
    @endphp
    @php
        $docTracking = 'DR-'
            .$demographics['start_date']->format('Ymd')
            .$demographics['end_date']->format('Ymd')
            .'-'
            .strtoupper(substr(md5((string) ($demographics['scope_slug'] ?? 'all-tenants').$demographics['start_date']->toDateString().$demographics['end_date']->toDateString()), 0, 6));
    @endphp
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    @if($leftLogoData)
                        <img src="{{ $leftLogoData }}" alt="Municipality Logo" class="header-side-logo">
                    @endif
                </td>
                <td class="header-center">
                    <div class="header-topline">Republic of the Philippines</div>
                    <div class="header-main">Municipality of Impasug-ong, Bukidnon</div>
                    <div class="header-office">Tourism Management Office</div>
                    <div class="header-report-line">Tulogan Monthly Report</div>
                </td>
                <td class="header-right">
                    @if($rightLogoData)
                        <img src="{{ $rightLogoData }}" alt="Impasug-ong Logo" class="header-side-logo header-right-logo">
                    @endif
                </td>
            </tr>
        </table>
    </div>
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
    <div class="footer">
        <table class="footer-row">
            <tr>
                <td class="footer-left"><strong>Date:</strong> {{ now('Asia/Manila')->format('M d, Y h:i A') }}</td>
                <td class="footer-center"><strong>Doc Tracking:</strong> {{ $docTracking }}</td>
                <td class="footer-right"><strong>Page:</strong> <span class="pagenum"></span> / <span class="pagecount"></span></td>
            </tr>
        </table>
    </div>
</body>
</html>
