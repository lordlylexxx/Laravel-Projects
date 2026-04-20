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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            padding: 20px;
            max-width: 900px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #2E7D32;
            padding-bottom: 8px;
        }
        
        .header h1 {
            color: #1B5E20;
            font-size: 20px;
            margin-bottom: 3px;
        }
        
        .header p {
            color: #666;
            font-size: 12px;
            margin: 2px 0;
        }
        
        .report-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding: 8px 10px;
            background: #E8F5E9;
            border-radius: 4px;
            font-size: 11px;
        }
        
        .meta-item {
            font-size: 11px;
        }
        
        .meta-item strong {
            color: #2E7D32;
            display: block;
            margin-bottom: 1px;
            font-size: 10px;
        }
        
        /* Summary Cards */
        .summary {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .summary-card {
            flex: 1;
            padding: 10px;
            background: #F1F8E9;
            border-left: 3px solid #2E7D32;
            border-radius: 4px;
            text-align: center;
        }
        
        .summary-card h4 {
            color: #666;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
            font-weight: 600;
        }
        
        .summary-card .value {
            font-size: 20px;
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
            font-size: 13px;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #E8F5E9;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }
        
        table th {
            background: #2E7D32;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        
        table td {
            padding: 8px;
            border-bottom: 1px solid #E0E0E0;
            font-size: 11px;
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
        
        .revenue {
            color: #1B5E20;
            font-weight: 700;
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
            margin-top: 20px;
            padding-top: 12px;
            border-top: 2px solid #2E7D32;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .footer-detail {
            margin: 3px 0;
            line-height: 1.4;
        }
        
        .footer-divider {
            display: flex;
            justify-content: space-around;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #E0E0E0;
            font-size: 9px;
        }
        
        .footer-item {
            text-align: center;
            flex: 1;
        }
        
        .footer-item-label {
            font-size: 8px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        
        .footer-item-value {
            font-size: 11px;
            font-weight: 600;
            color: #2E7D32;
        }
        
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
        <!-- Header -->
        <div class="header">
            <h1>📊 Monthly Booking Report</h1>
            <p>{{ $monthName }} - Tenant Guest Analytics</p>
        </div>
        
        <!-- Report Meta - Compact -->
        <div class="report-meta">
            <div class="meta-item">
                <strong>Period:</strong> {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}
            </div>
            <div class="meta-item">
                <strong>Generated:</strong> {{ now()->format('M d, Y') }}
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
                <h4>Revenue</h4>
                <div class="value" style="font-size: 18px;">₱{{ number_format($summary['total_revenue'], 0) }}</div>
            </div>
            <div class="summary-card">
                <h4>Avg/Booking</h4>
                <div class="value">{{ $summary['average_guests_per_booking'] }}</div>
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
                            <th style="text-align: right;">Total Revenue</th>
                            <th style="text-align: right;">Avg/Booking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenantBookings as $booking)
                            <tr>
                                <td class="tenant-name">{{ $booking->name }}</td>
                                <td class="number">{{ $booking->booking_count }}</td>
                                <td class="number">{{ $booking->total_guests }}</td>
                                <td class="number revenue">₱{{ number_format($booking->total_revenue, 0) }}</td>
                                <td class="number">₱{{ number_format($booking->avg_booking_value, 0) }}</td>
                            </tr>
                        @endforeach
                        <tr class="summary-row">
                            <td><strong>TOTAL</strong></td>
                            <td class="number"><strong>{{ $summary['total_bookings'] }}</strong></td>
                            <td class="number"><strong>{{ $summary['total_guests'] }}</strong></td>
                            <td class="number"><strong>₱{{ number_format($summary['total_revenue'], 0) }}</strong></td>
                            <td class="number"><strong>₱{{ number_format($summary['total_revenue'] / max($summary['total_bookings'], 1), 0) }}</strong></td>
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
            <div class="footer-detail">
                <strong>ImpaStay Management System</strong> | Monthly Booking Report
            </div>
            
            <div class="footer-divider">
                <div class="footer-item">
                    <div class="footer-item-label">Report Date</div>
                    <div class="footer-item-value">{{ now()->format('M d, Y') }}</div>
                </div>
                <div class="footer-item">
                    <div class="footer-item-label">Time Generated</div>
                    <div class="footer-item-value">{{ now()->format('H:i A') }}</div>
                </div>
                <div class="footer-item">
                    <div class="footer-item-label">Tracking Number</div>
                    <div class="footer-item-value">BR-{{ $year }}{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}-{{ strtoupper(substr(md5(now()->toString()), 0, 6)) }}</div>
                </div>
            </div>
            
            <div class="footer-detail" style="margin-top: 6px; font-size: 8px; color: #bbb;">
                Confidential - For authorized personnel only
            </div>
        </div>
    </div>
</body>
</html>
