<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Management - Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --green-dark: #1B5E20;
            --green-primary: #2E7D32;
            --green-medium: #43A047;
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

        .dashboard-layout {
            padding-top: 82px;
        }

        .main-content {
            padding: 28px 36px;
        }

        .page-header {
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 2rem;
            color: var(--green-dark);
            margin-bottom: 6px;
        }

        .page-header p {
            color: var(--gray-500);
        }

        .flash {
            background: #ECFDF5;
            border: 1px solid #86EFAC;
            color: #166534;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .card {
            background: var(--white);
            border-radius: 14px;
            border: 1px solid var(--green-soft);
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            overflow: hidden;
        }

        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--green-soft);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 1.1rem;
            color: var(--green-dark);
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        th, td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--gray-200);
            text-align: left;
            vertical-align: middle;
            font-size: 0.92rem;
        }

        th {
            background: var(--green-white);
            color: var(--gray-700);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 11px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .status-badge.active { background: #DCFCE7; color: #166534; }
        .status-badge.trialing { background: #FEF3C7; color: #92400E; }
        .status-badge.past-due { background: #FEE2E2; color: #991B1B; }
        .status-badge.cancelled { background: #F3F4F6; color: #374151; }

        .inline-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .inline-form select {
            padding: 6px 8px;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            background: #fff;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 6px 10px;
            font-weight: 700;
            cursor: pointer;
            color: #fff;
        }

        .btn.save { background: var(--green-primary); }
        .btn.disable { background: #DC2626; }
        .btn.enable { background: #2563EB; }

        .tenant-url {
            color: var(--green-primary);
            text-decoration: none;
            font-weight: 600;
        }

        .tenant-url:hover { text-decoration: underline; }

        .pagination {
            padding: 14px 20px;
        }

        @include('admin.partials.top-navbar-styles')
    </style>
</head>
<body>
    @include('admin.partials.top-navbar', ['active' => 'tenants'])

    <div class="dashboard-layout">
        <main class="main-content">
            @if(session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            <div class="page-header">
                <h1>Tenant Management</h1>
                <p>Manage tenant plans and domain availability from the central admin app.</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Tenants ({{ $tenants->total() }})</h3>
                    <span style="color: var(--gray-500); font-size: 0.85rem;">Plan + Domain controls</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Tenant</th>
                                <th>Owner</th>
                                <th>Plan</th>
                                <th>Domain</th>
                                <th>Subscription</th>
                                <th>DB Used (MB)</th>
                                <th>Period Ends</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenants as $tenant)
                                @php
                                    $domainEnabled = (bool) ($tenant->domain_enabled ?? true);
                                    $statusValue = (string) ($tenant->subscription_status ?? 'unknown');
                                    $statusClass = match ($statusValue) {
                                        'active' => 'active',
                                        'trialing' => 'trialing',
                                        'past_due' => 'past-due',
                                        'cancelled' => 'cancelled',
                                        default => 'cancelled',
                                    };

                                    $domainLabel = $tenant->app_port ? ('127.0.0.1:' . $tenant->app_port) : ($tenant->domain ?: 'Not assigned');
                                    $periodEnds = $tenant->current_period_ends_at ?? $tenant->trial_ends_at;
                                    $dbUsed = $tenant->database ? ($databaseUsageMbByDatabase[$tenant->database] ?? null) : null;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $tenant->name }}</strong>
                                        <div style="font-size:0.78rem; color: var(--gray-500); margin-top:3px;">{{ $tenant->slug }}</div>
                                    </td>
                                    <td>
                                        {{ $tenant->owner?->name ?? 'Unassigned' }}
                                        <div style="font-size:0.78rem; color: var(--gray-500); margin-top:3px;">{{ $tenant->owner?->email ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <form class="inline-form" action="{{ route('admin.tenants.update-plan', $tenant) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <select name="plan">
                                                <option value="basic" {{ $tenant->plan === 'basic' ? 'selected' : '' }}>BASIC</option>
                                                <option value="plus" {{ $tenant->plan === 'plus' ? 'selected' : '' }}>PLUS</option>
                                                <option value="pro" {{ $tenant->plan === 'pro' ? 'selected' : '' }}>PRO</option>
                                            </select>
                                            <button type="submit" class="btn save">Save</button>
                                        </form>
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px; flex-wrap: wrap;">
                                            @if($domainEnabled)
                                                <a href="{{ $tenant->publicUrl() }}" class="tenant-url" target="_blank">{{ $domainLabel }}</a>
                                            @else
                                                <span style="color:#B91C1C; font-weight:700;">{{ $domainLabel }}</span>
                                            @endif

                                            <form class="inline-form" action="{{ route('admin.tenants.toggle-domain', $tenant) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="domain_enabled" value="{{ $domainEnabled ? 0 : 1 }}">
                                                <button type="submit" class="btn {{ $domainEnabled ? 'disable' : 'enable' }}">
                                                    {{ $domainEnabled ? 'Disable' : 'Enable' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $statusValue)) }}</span>
                                    </td>
                                    <td>{{ is_null($dbUsed) ? 'N/A' : number_format((float) $dbUsed, 2) }}</td>
                                    <td>{{ $periodEnds ? $periodEnds->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align:center; color: var(--gray-500);">No tenants found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tenants->hasPages())
                    <div class="pagination">
                        {{ $tenants->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
