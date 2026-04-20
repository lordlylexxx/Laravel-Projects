<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if($navType === 'admin' && ! \App\Models\Tenant::checkCurrent())
        @include('admin.partials.favicon')
    @else
        @include('partials.tenant-favicon')
    @endif
    <title>System Updates - ImpaStay</title>
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
            --gray-500: #6B7280;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --amber-600: #B45309;
            --red-700: #B91C1C;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }

        .page-shell {
            padding: 96px 24px 30px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 22px;
        }

        .header h1 {
            font-size: 2rem;
            color: var(--green-dark);
            margin-bottom: 6px;
        }

        .header p {
            color: var(--gray-500);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 18px;
            margin-bottom: 18px;
        }

        .card {
            background: var(--white);
            border: 1px solid var(--green-soft);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(27, 94, 32, 0.08);
        }

        .card h2 {
            font-size: 1rem;
            margin-bottom: 12px;
            color: var(--gray-700);
        }

        .version {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--green-dark);
        }

        .sub {
            color: var(--gray-500);
            margin-top: 4px;
            font-size: 0.9rem;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.86rem;
            font-weight: 600;
            border-radius: 999px;
            padding: 7px 12px;
        }

        .status-pill.update { background: #DCFCE7; color: var(--green-dark); }
        .status-pill.current { background: #DBEAFE; color: #1D4ED8; }
        .status-pill.offline { background: #FEE2E2; color: var(--red-700); }
        .status-pill.installed { background: #EDE9FE; color: #6D28D9; }

        .actions {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            border: none;
            border-radius: 9px;
            padding: 10px 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn.primary {
            color: var(--white);
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
        }

        .btn.ghost {
            color: var(--green-dark);
            background: var(--green-white);
            border: 1px solid var(--green-soft);
        }

        .meta-row {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .meta-row:last-child {
            border-bottom: 0;
        }

        .meta-key {
            color: var(--gray-500);
            font-size: 0.9rem;
        }

        .meta-value {
            color: var(--gray-800);
            font-weight: 600;
            word-break: break-all;
        }

        .notes {
            margin-top: 6px;
            white-space: pre-wrap;
            color: var(--gray-700);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .hint {
            margin-top: 10px;
            color: var(--amber-600);
            font-size: 0.88rem;
        }

        .flash {
            margin: 0 0 16px;
            border-radius: 10px;
            padding: 10px 12px;
            border: 1px solid #86EFAC;
            background: #ECFDF5;
            color: #166534;
            font-weight: 600;
        }

        .table-wrap {
            overflow: auto;
            border-radius: 10px;
            border: 1px solid var(--gray-200);
            margin-top: 10px;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
            background: var(--white);
        }

        .history-table th,
        .history-table td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--gray-200);
            text-align: left;
            font-size: 0.9rem;
        }

        .history-table th {
            background: var(--green-white);
            color: var(--gray-700);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 0.84rem;
        }

        @media (max-width: 760px) {
            .page-shell { padding: 88px 14px 24px; }
            .meta-row { grid-template-columns: 1fr; gap: 4px; }
        }
    </style>

    <style>
    @if($navType === 'admin')
        @include('admin.partials.top-navbar-styles')
    @else
        @include('owner.partials.top-navbar-styles')
    @endif
    </style>
</head>
<body class="owner-nav-page">
    @if($navType === 'admin')
        @include('admin.partials.top-navbar', ['active' => 'updates'])
    @else
        @include('owner.partials.top-navbar', ['active' => 'updates'])
    @endif

    <main class="page-shell">
        @if(session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif

        <div class="header">
            <h1><i class="fas fa-cloud-arrow-down"></i> System Updates</h1>
            <p>Check version status, read release notes, and download the latest package from the central app.</p>
        </div>

        <section class="grid">
            <article class="card">
                <h2>Current Installation</h2>
                <div class="version">v{{ $currentVersion }}</div>
                <div class="sub">Installed on this app instance</div>
            </article>

            <article class="card">
                <h2>Latest Available</h2>
                <div class="version">v{{ $latestVersion }}</div>
                <div class="actions">
                    @if($isUnavailable)
                        <span class="status-pill offline"><i class="fas fa-cloud-slash"></i> Channel Unavailable</span>
                    @elseif($hasUpdate)
                        <span class="status-pill update"><i class="fas fa-arrow-up"></i> Update Available</span>
                    @else
                        <span class="status-pill current"><i class="fas fa-check"></i> Up To Date</span>
                    @endif
                </div>
            </article>
        </section>

        <section class="card" style="margin-bottom: 18px;">
            <h2>Release Notes</h2>
            <div class="notes">{{ $releaseNotes }}</div>
            @if($statusMessage !== '')
                <div class="hint">{{ $statusMessage }}</div>
            @endif
        </section>

        <section class="card">
            <h2>Update Channel Details</h2>
            <div class="meta-row">
                <div class="meta-key">Published At</div>
                <div class="meta-value">{{ $publishedAt ?: 'Not specified' }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-key">Central Endpoint</div>
                <div class="meta-value">{{ $centralBaseUrl ?: 'Not configured' }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-key">Package URL</div>
                <div class="meta-value">{{ $downloadUrl ?: 'Not available' }}</div>
            </div>

            <div class="actions" style="margin-top: 16px;">
                @if($downloadUrl !== '')
                    <a href="{{ $downloadUrl }}" class="btn primary">
                        <i class="fas fa-download"></i>
                        Download Latest Package
                    </a>
                @endif
                <form method="POST" action="{{ $markInstalledRoute }}" style="display:inline-flex;">
                    @csrf
                    <button type="submit" class="btn ghost">
                        <i class="fas fa-circle-check"></i>
                        Mark as Installed
                    </button>
                </form>
                <a href="{{ ($navType === 'admin' && !\App\Models\Tenant::checkCurrent()) ? '/admin/dashboard' : '/owner/dashboard' }}" class="btn ghost">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        </section>

        <section class="card" style="margin-top: 18px;">
            <h2>Update History</h2>
            <div class="table-wrap">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Checked At</th>
                            <th>Version</th>
                            <th>Status</th>
                            <th>Installed At</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $entry)
                            <tr>
                                <td>{{ optional($entry->checked_at)->format('Y-m-d H:i:s') ?: 'N/A' }}</td>
                                <td class="mono">v{{ $entry->current_version }} -> v{{ $entry->latest_version }}</td>
                                <td>
                                    @if($entry->channel_status === 'installed')
                                        <span class="status-pill installed"><i class="fas fa-circle-check"></i> Installed</span>
                                    @elseif($entry->channel_status === 'update_available')
                                        <span class="status-pill update"><i class="fas fa-arrow-up"></i> Update Available</span>
                                    @elseif($entry->channel_status === 'unavailable')
                                        <span class="status-pill offline"><i class="fas fa-cloud-slash"></i> Unavailable</span>
                                    @else
                                        <span class="status-pill current"><i class="fas fa-check"></i> Up To Date</span>
                                    @endif
                                </td>
                                <td>{{ optional($entry->installed_at)->format('Y-m-d H:i:s') ?: '-' }}</td>
                                <td>{{ $entry->status_message ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No update checks logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
