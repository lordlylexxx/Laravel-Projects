<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    @include('admin.partials.favicon')
    <title>Monthly Booking Report - {{ $monthName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Times New Roman", Times, serif;
            color: #333;
            line-height: 1.6;
            font-size: 12px;
        }
        
        .container {
            padding: 20px;
            max-width: 1120px;
            margin: 0 auto;
        }
        
        @include('reports.partials.municipal-pdf-header-styles')

        .report-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 6px 8px;
            background: #E8F5E9;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .meta-item {
            font-size: 12px;
        }
        
        .meta-item strong {
            color: #2E7D32;
            display: block;
            margin-bottom: 1px;
            font-size: 12px;
        }
        
        /* Summary Cards */
        .summary {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        
        .summary-card {
            flex: 1;
            padding: 8px;
            background: #F1F8E9;
            border-left: 3px solid #2E7D32;
            border-radius: 4px;
            text-align: center;
        }
        
        .summary-card h4 {
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
            font-weight: 600;
        }
        
        .summary-card .value {
            font-size: 12px;
            color: #1B5E20;
            font-weight: 700;
        }
        
        /* Table */
        .table-section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .table-section h3 {
            color: #2E7D32;
            font-size: 12px;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #E8F5E9;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 12px;
        }
        
        table th {
            background: #2E7D32;
            color: white;
            padding: 6px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        
        table td {
            padding: 6px;
            border-bottom: 1px solid #E0E0E0;
            font-size: 12px;
        }
        
        table tr:nth-child(even) {
            background: #F9FAFB;
        }
        
        .tenant-name {
            font-weight: 600;
            color: #1B5E20;
        }
        
        .number {
            text-align: right;
            font-weight: 600;
            color: #2E7D32;
        }
        
        
        /* Summary Row */
        .summary-row {
            background: #E8F5E9;
            font-weight: 700;
            color: #1B5E20;
        }
        
        .summary-row td {
            font-weight: 700;
            color: #1B5E20;
        }
        
        /* Footer */
        .footer {
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid #d1d5db;
            font-size: 12px;
            color: #374151;
        }
        .footer-row {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-row td {
            border: none;
            font-size: 12px;
            color: #374151;
            padding: 2px 0;
        }
        .footer-left { text-align: left; }
        .footer-center { text-align: center; }
        .footer-right { text-align: right; }
        .pagenum:before { content: counter(page); }
        .pagecount:before { content: counter(pages); }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #999;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
            }
            
            .container {
                padding: 15px;
            }
            
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @include('reports.partials.municipal-pdf-header', [
            'pdfReportTitle' => 'Monthly Booking Report',
            'pdfReportSubtitle' => $monthName.' - Tenant Guest Analytics',
        ])

        <!-- Report Meta - Compact -->
        <div class="report-meta">
            <div class="meta-item">
                <strong>Period:</strong> {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}
            </div>
            <div class="meta-item">
                <strong>Generated:</strong> {{ now('Asia/Manila')->format('M d, Y') }}
            </div>
            <div class="meta-item">
                <strong>Tenants:</strong> {{ $tenantBookings->count() }}
            </div>
        </div>
        
        <!-- Summary Cards - Compact Grid -->
        <div class="summary">
            <div class="summary-card">
                <h4>Bookings</h4>
                <div class="value">{{ $summary['total_bookings'] }}</div>
            </div>
            <div class="summary-card">
                <h4>Guests</h4>
                <div class="value">{{ $summary['total_guests'] }}</div>
            </div>
            <div class="summary-card">
                <h4>Avg guests / booking</h4>
                <div class="value">{{ $summary['average_guests_per_booking'] }}</div>
            </div>
            <div class="summary-card">
                <h4>Tenants</h4>
                <div class="value">{{ $summary['tenant_count'] }}</div>
            </div>
        </div>
        
        <!-- Table Section -->

        <div class="table-section">
            <h3>Tenant Booking Details</h3>
            
            @if($tenantBookings->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Tenant Name</th>
                            <th style="text-align: right;">Bookings</th>
                            <th style="text-align: right;">Total Guests</th>
                            <th style="text-align: right;">Avg guests / booking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenantBookings as $booking)
                            <tr>
                                <td class="tenant-name">{{ $booking->name }}</td>
                                <td class="number">{{ $booking->booking_count }}</td>
                                <td class="number">{{ $booking->total_guests }}</td>
                                <td class="number">{{ number_format((float) $booking->avg_guests_per_booking, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="summary-row">
                            <td><strong>TOTAL</strong></td>
                            <td class="number"><strong>{{ $summary['total_bookings'] }}</strong></td>
                            <td class="number"><strong>{{ $summary['total_guests'] }}</strong></td>
                            <td class="number"><strong>{{ $summary['average_guests_per_booking'] }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <p>No bookings data available for {{ $monthName }}</p>
                </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <table class="footer-row">
                <tr>
                    <td class="footer-left"><strong>Date:</strong> {{ now('Asia/Manila')->format('M d, Y h:i A') }}</td>
                    <td class="footer-center"><strong>Doc Tracking:</strong> BR-{{ $year }}{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}-{{ strtoupper(substr(md5(now('Asia/Manila')->toString()), 0, 6)) }}</td>
                    <td class="footer-right"><strong>Page:</strong> <span class="pagenum"></span> / <span class="pagecount"></span></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
