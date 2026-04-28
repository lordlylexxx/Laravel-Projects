<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Monthly Report - ImpaStay</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            @include('partials.tenant-theme-css-vars')
            --gray-200: #E5E7EB;
            --gray-500: #6B7280;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }

        .main-content {
            width: min(1600px, 100%);
            margin: 0 auto;
            padding: var(--owner-content-offset) clamp(12px, 2vw, 32px) 32px;
            min-height: calc(100vh - var(--owner-content-offset));
        }

        .page-header {
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 1.8rem;
            color: var(--green-dark);
            margin-bottom: 6px;
        }

        .page-header h1 i {
            color: var(--green-primary);
            margin-right: 6px;
        }

        .page-header p {
            color: var(--gray-500);
        }

        .card {
            background: var(--white);
            border: 1px solid var(--green-soft);
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(27, 94, 32, 0.08);
            padding: 16px;
            margin-bottom: 16px;
        }

        .layout-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 160px;
        }

        .field label {
            font-size: 0.82rem;
            color: var(--gray-700);
            font-weight: 600;
        }

        .field select {
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            padding: 9px 10px;
            font-size: 0.9rem;
            background: #fff;
        }

        .field select:focus {
            outline: none;
            border-color: var(--green-primary);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.12);
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 0.88rem;
        }

        .btn.primary {
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            color: #fff;
        }

        .btn.primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(46, 125, 50, 0.35);
        }

        .btn.secondary {
            background: linear-gradient(135deg, var(--green-soft), var(--green-white));
            color: var(--green-dark);
            border: 1px solid var(--green-soft);
        }

        .btn.secondary:hover {
            background: linear-gradient(135deg, var(--green-pale), var(--green-soft));
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .kpi {
            background: linear-gradient(135deg, #fff, var(--green-white));
            border: 1px solid var(--green-soft);
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 4px 15px rgba(27, 94, 32, 0.08);
            border-left: 4px solid var(--green-primary);
        }

        .kpi-title {
            color: var(--gray-500);
            font-size: 0.82rem;
            margin-bottom: 6px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .kpi-value {
            color: var(--green-dark);
            font-size: 1.6rem;
            font-weight: 800;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 680px;
        }

        th, td {
            padding: 11px 12px;
            border-bottom: 1px solid var(--gray-200);
            text-align: left;
            font-size: 0.9rem;
        }

        th {
            background: var(--cream);
            color: var(--gray-600);
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        tbody tr:hover {
            background: var(--green-white);
        }

        td.num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .empty {
            text-align: center;
            color: var(--gray-500);
            padding: 24px;
        }

        @include('owner.partials.top-navbar-styles')

        @media (min-width: 1280px) {
            .layout-grid {
                grid-template-columns: minmax(320px, 420px) 1fr;
                align-items: start;
            }

            .filters-card,
            .kpi-panel {
                position: sticky;
                top: calc(var(--owner-topbar-height) + 12px);
            }

            .kpi-panel .kpi-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="owner-nav-page">
    @include('owner.partials.top-navbar', ['active' => 'reports'])

    <main class="main-content with-owner-nav">
        <div class="page-header">
            <h1><i class="fas fa-chart-column"></i> Monthly Tenant Report</h1>
            <p>Track monthly sales, total guests catered, and booking activity.</p>
        </div>

        <div class="layout-grid">
            <aside>
                <section class="card filters-card">
                    <form method="GET" action="/owner/reports/monthly" class="filters">
                        <div class="field">
                            <label for="year">Year</label>
                            <select name="year" id="year">
                                @for($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}" @selected($year === $y)>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="field">
                            <label for="month">Month</label>
                            <select name="month" id="month">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @selected($month === $m)>{{ \Carbon\Carbon::create(2000, $m, 1)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>

                        <button type="submit" class="btn primary"><i class="fas fa-filter"></i> Apply</button>
                        <a href="/owner/reports/monthly/download-sales?year={{ $year }}&month={{ $month }}" class="btn secondary">
                            <i class="fas fa-file-invoice-dollar"></i> Sales PDF
                        </a>
                        <a href="/owner/reports/monthly/download-guests?year={{ $year }}&month={{ $month }}" class="btn secondary">
                            <i class="fas fa-users"></i> Guests PDF
                        </a>
                    </form>
                </section>

                <section class="kpi-panel">
                    <div class="kpi-grid">
                        <div class="kpi">
                            <div class="kpi-title">Reporting Month</div>
                            <div class="kpi-value" style="font-size: 1.2rem;">{{ $monthName }}</div>
                        </div>
                        <div class="kpi">
                            <div class="kpi-title">Monthly Sales</div>
                            <div class="kpi-value">PHP {{ number_format((float) $monthlySales, 2) }}</div>
                        </div>
                        <div class="kpi">
                            <div class="kpi-title">People Catered</div>
                            <div class="kpi-value">{{ number_format((int) $monthlyGuests) }}</div>
                        </div>
                        <div class="kpi">
                            <div class="kpi-title">Total Bookings</div>
                            <div class="kpi-value">{{ number_format((int) $monthlyBookings) }}</div>
                        </div>
                    </div>
                </section>
            </aside>

            <section class="card">
                <h2 style="margin-bottom: 10px; color: var(--green-dark);">Daily Breakdown</h2>
                <div class="table-wrap">
                    @if($dailyBreakdown->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="num">Bookings</th>
                                    <th class="num">Guests</th>
                                    <th class="num">Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dailyBreakdown as $row)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($row->report_date)->format('M d, Y') }}</td>
                                        <td class="num">{{ (int) $row->booking_count }}</td>
                                        <td class="num">{{ (int) $row->total_guests }}</td>
                                        <td class="num">PHP {{ number_format((float) $row->total_sales, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty">No qualified bookings found for {{ $monthName }}.</div>
                    @endif
                </div>
            </section>
        </div>
    </main>
</body>
</html>
