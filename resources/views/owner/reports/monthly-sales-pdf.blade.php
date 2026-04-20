<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    @include('partials.tenant-favicon')
    <title>Monthly Sales Report - {{ $monthName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: #1F2937; }
        .container { padding: 18px; }
        .header { border-bottom: 2px solid #2E7D32; padding-bottom: 8px; margin-bottom: 12px; }
        .header h1 { color: #1B5E20; font-size: 20px; margin-bottom: 3px; }
        .header p { color: #6B7280; font-size: 12px; }
        .kpi { display: flex; gap: 8px; margin-bottom: 12px; }
        .kpi-card {
            flex: 1;
            border: 1px solid #C8E6C9;
            background: #F1F8E9;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
        }
        .kpi-title { font-size: 10px; color: #4B5563; text-transform: uppercase; margin-bottom: 4px; }
        .kpi-value { font-size: 16px; color: #1B5E20; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border-bottom: 1px solid #E5E7EB; padding: 8px; text-align: left; }
        th { background: #E8F5E9; font-size: 10px; color: #374151; text-transform: uppercase; }
        td.num { text-align: right; }
        .empty { text-align: center; color: #6B7280; padding: 20px; }
        .footer { margin-top: 12px; border-top: 1px solid #E5E7EB; padding-top: 8px; font-size: 10px; color: #6B7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Monthly Sales Report</h1>
            <p>Reporting month: {{ $monthName }}</p>
        </div>

        <div class="kpi">
            <div class="kpi-card">
                <div class="kpi-title">Monthly Sales</div>
                <div class="kpi-value">PHP {{ number_format((float) $monthlySales, 2) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Total Bookings</div>
                <div class="kpi-value">{{ number_format((int) $monthlyBookings) }}</div>
            </div>
        </div>

        @if($dailyBreakdown->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="num">Bookings</th>
                        <th class="num">Sales</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyBreakdown as $row)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($row->report_date)->format('M d, Y') }}</td>
                            <td class="num">{{ (int) $row->booking_count }}</td>
                            <td class="num">PHP {{ number_format((float) $row->total_sales, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No qualified bookings found for this month.</div>
        @endif

        <div class="footer">Generated at {{ now()->format('Y-m-d H:i:s') }}</div>
    </div>
</body>
</html>
