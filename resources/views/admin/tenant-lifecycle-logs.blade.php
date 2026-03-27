<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Lifecycle Logs - Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --green-dark: #1B5E20;
            --green-primary: #2E7D32;
            --green-soft: #C8E6C9;
            --green-white: #E8F5E9;
            --cream: #F1F8E9;
            --white: #FFFFFF;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-500: #6B7280;
            --gray-700: #374151;
            --gray-800: #1F2937;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }
        .dashboard-layout { padding-top: 82px; }
        .main-content { padding: 28px 36px; }
        .page-header { margin-bottom: 18px; }
        .page-header h1 { font-size: 1.9rem; color: var(--green-dark); margin-bottom: 6px; }
        .page-header p { color: var(--gray-500); }
        .card {
            background: var(--white);
            border-radius: 14px;
            border: 1px solid var(--green-soft);
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            overflow: hidden;
        }
        .filters {
            padding: 16px 20px;
            border-bottom: 1px solid var(--green-soft);
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filters input {
            padding: 8px 10px;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            min-width: 180px;
        }
        .filters button, .filters a {
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-primary { background: var(--green-primary); color: #fff; }
        .btn-muted { background: #6B7280; color: #fff; }
        .table-wrap { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }
        th, td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--gray-200);
            text-align: left;
            vertical-align: top;
            font-size: 0.9rem;
        }
        th {
            background: var(--green-white);
            color: var(--gray-700);
            font-size: 0.78rem;
            text-transform: uppercase;
        }
        code {
            background: #f3f4f6;
            border-radius: 6px;
            padding: 2px 6px;
            font-size: 0.78rem;
        }
        .pagination { padding: 14px 20px; }
        @include('admin.partials.top-navbar-styles')
    </style>
</head>
<body>
    @include('admin.partials.top-navbar', ['active' => 'tenants'])

    <div class="dashboard-layout">
        <main class="main-content">
            <div class="page-header">
                <h1>Tenant Lifecycle Logs</h1>
                <p>Audit trail for tenant lifecycle updates in Central App.</p>
            </div>

            <div class="card">
                <form class="filters" method="GET" action="{{ route('admin.tenants.lifecycle-logs') }}">
                    <input type="text" name="tenant" value="{{ request('tenant') }}" placeholder="Filter by tenant name/slug">
                    <input type="text" name="action" value="{{ request('action') }}" placeholder="Filter by action">
                    <button type="submit" class="btn-primary">Apply</button>
                    <a class="btn-muted" href="{{ route('admin.tenants.lifecycle-logs') }}">Reset</a>
                </form>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>When</th>
                                <th>Tenant</th>
                                <th>Action</th>
                                <th>Actor</th>
                                <th>Reason</th>
                                <th>Before</th>
                                <th>After</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at?->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <strong>{{ $log->tenant?->name ?? 'N/A' }}</strong><br>
                                        <span style="color:#6B7280; font-size:0.8rem;">{{ $log->tenant?->slug ?? 'N/A' }}</span>
                                    </td>
                                    <td><code>{{ $log->action }}</code></td>
                                    <td>{{ $log->actor?->name ?? 'System' }}</td>
                                    <td>{{ $log->reason ?? '-' }}</td>
                                    <td><code>{{ json_encode($log->before_state ?? []) }}</code></td>
                                    <td><code>{{ json_encode($log->after_state ?? []) }}</code></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align:center; color:#6B7280;">No lifecycle logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="pagination">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
