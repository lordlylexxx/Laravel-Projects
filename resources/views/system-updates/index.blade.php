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
    <script>
        tailwind = {
            config: {
                corePlugins: {
                    preflight: false,
                },
            },
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
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
        .status-pill.restoring { background: #FEF3C7; color: #92400E; }
        .status-pill.failed { background: #FEE2E2; color: #B91C1C; }

        .progress-card {
            margin-bottom: 18px;
            display: grid;
            gap: 12px;
        }

        .progress-track {
            width: 100%;
            height: 12px;
            border-radius: 999px;
            background: #E5E7EB;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 0;
            border-radius: inherit;
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            transition: width 0.25s ease;
        }

        .progress-meta {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            color: var(--gray-500);
            font-size: 0.9rem;
        }

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

        .flash-error {
            margin: 0 0 16px;
            border-radius: 10px;
            padding: 10px 12px;
            border: 1px solid #FECACA;
            background: #FEF2F2;
            color: #991B1B;
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
        @if(session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif

        <div class="header">
            <h1><i class="fas fa-cloud-arrow-down"></i> System Updates</h1>
            <p>Check version status, read release notes, and install updates from the central app in one click.</p>
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

            @if($latestInstallActivity)
                <div class="card progress-card" style="margin-top: 16px;">
                    <h2>Live Install Progress</h2>
                    <div class="progress-track" aria-label="Install progress">
                        <div class="progress-fill" id="install-progress-fill" style="width: {{ (int) ($latestInstallActivity->progress_percent ?? 0) }}%;"></div>
                    </div>
                    <div class="progress-meta">
                        <span id="install-step-text">{{ $latestInstallActivity->current_step ?: 'queued' }}</span>
                        <span id="install-progress-text">{{ (int) ($latestInstallActivity->progress_percent ?? 0) }}%</span>
                    </div>
                    <div class="sub" id="install-status-text">{{ $latestInstallActivity->status_message ?: 'Waiting for the installer...' }}</div>
                </div>
            @endif

            <div class="actions" style="margin-top: 16px;">
                <form method="POST" action="{{ $installRoute }}" style="display:inline-flex;">
                    @csrf
                    <button type="submit" class="btn primary" {{ ($downloadUrl === '' || $installInProgress) ? 'disabled' : '' }}>
                        <i class="fas fa-cloud-arrow-down"></i>
                        Install Update
                    </button>
                </form>

                @if($restoreAvailable)
                    <form method="POST" action="{{ $restoreRoute }}" style="display:inline-flex;">
                        @csrf
                        <button type="submit" class="btn ghost" {{ $installInProgress ? 'disabled' : '' }}>
                            <i class="fas fa-rotate-left"></i>
                            Restore Previous Version
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ $markInstalledRoute }}" style="display:inline-flex;">
                    @csrf
                    <button type="submit" class="btn ghost" {{ $installInProgress ? 'disabled' : '' }}>
                        <i class="fas fa-circle-check"></i>
                        Mark as Installed
                    </button>
                </form>
            </div>
        </section>

        @php
            $showStaffTicketUi = $tenantId && ($navType === 'owner' || ($navType === 'admin' && \App\Models\Tenant::checkCurrent()));
        @endphp

        @if($showStaffTicketUi)
            <section class="card" style="margin-top: 18px;">
                <h2>Support</h2>
                <p class="sub" style="margin-bottom: 14px;">Report an issue with the update channel or installation. Central admin will respond and mark tickets resolved.</p>

                <form method="POST" action="{{ $ownerUpdateTicketStoreRoute }}" enctype="multipart/form-data" style="margin-bottom: 20px;">
                    @csrf
                    <div style="margin-bottom: 12px;">
                        <label for="ticket_subject" style="display:block; font-weight:600; margin-bottom:6px; color: var(--gray-700);">Subject</label>
                        <input id="ticket_subject" name="subject" type="text" value="{{ old('subject') }}" required maxlength="255"
                            style="width:100%; max-width:520px; padding:10px 12px; border:1px solid var(--gray-200); border-radius:8px;">
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label for="ticket_body" style="display:block; font-weight:600; margin-bottom:6px; color: var(--gray-700);">Details</label>
                        <textarea id="ticket_body" name="body" rows="4" required maxlength="10000"
                            style="width:100%; max-width:720px; padding:10px 12px; border:1px solid var(--gray-200); border-radius:8px;">{{ old('body') }}</textarea>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label for="ticket_attachment" style="display:block; font-weight:600; margin-bottom:6px; color: var(--gray-700);">Photo attachment <span style="color: var(--gray-500); font-weight:400;">(optional, JPG/PNG/WEBP up to 5MB)</span></label>
                        <input id="ticket_attachment" name="attachment" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            style="width:100%; max-width:520px; padding:10px 12px; border:1px solid var(--gray-200); border-radius:8px; background:#fff;">
                    </div>
                    @if ($errors->any())
                        <div class="flash-error" style="margin-bottom:12px;">{{ $errors->first() }}</div>
                    @endif
                    <button type="submit" class="btn primary"><i class="fas fa-paper-plane"></i> Submit ticket</button>
                </form>

                <h3 style="font-size:0.95rem; margin-bottom:10px; color: var(--gray-700);">Recent tickets (this business)</h3>
                <div class="table-wrap">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Submitted</th>
                                <th>Subject</th>
                                <th>From</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($updateTickets as $t)
                                <tr>
                                    <td>{{ $t->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($t->subject, 60) }}</td>
                                    <td>{{ $t->reporter_name }} <span class="mono" style="color:var(--gray-500);">({{ $t->reporter_role }})</span></td>
                                    <td>
                                        @if($t->status === \App\Models\UpdateTicket::STATUS_RESOLVED)
                                            <span class="status-pill current"><i class="fas fa-check"></i> Fixed</span>
                                        @else
                                            <span class="status-pill update"><i class="fas fa-inbox"></i> Pending</span>
                                        @endif
                                    </td>
                                    <td><a href="{{ $updateTicketShowPathPrefix.'/'.$t->id }}" class="btn ghost" style="padding:6px 10px;font-size:0.85rem;">View</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5">No tickets yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

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
                                    @elseif($entry->channel_status === 'restored')
                                        <span class="status-pill restoring"><i class="fas fa-rotate-left"></i> Restored</span>
                                    @elseif($entry->channel_status === 'installing')
                                        <span class="status-pill update"><i class="fas fa-spinner"></i> Installing</span>
                                    @elseif($entry->channel_status === 'restoring')
                                        <span class="status-pill restoring"><i class="fas fa-spinner"></i> Restoring</span>
                                    @elseif($entry->channel_status === 'failed')
                                        <span class="status-pill failed"><i class="fas fa-circle-xmark"></i> Failed</span>
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
            <div class="mt-3">{{ $history->links('pagination::tailwind') }}</div>
        </section>
    </main>

    @if($latestInstallActivity)
        <script>
            (function () {
                const statusUrl = @json($installStatusRoute);
                const activeUpdateLogId = @json($activeUpdateLogId);
                const progressFill = document.getElementById('install-progress-fill');
                const progressText = document.getElementById('install-progress-text');
                const stepText = document.getElementById('install-step-text');
                const statusText = document.getElementById('install-status-text');
                if (!progressFill || !progressText || !stepText || !statusText) {
                    return;
                }

                const updateLiveStatus = async () => {
                    try {
                        const url = activeUpdateLogId
                            ? `${statusUrl}?update_log_id=${encodeURIComponent(activeUpdateLogId)}`
                            : statusUrl;

                        const response = await fetch(url, {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin',
                        });

                        if (!response.ok) {
                            return;
                        }

                        const payload = await response.json();

                        if (typeof payload.progress_percent === 'number') {
                            const percent = Math.max(0, Math.min(100, payload.progress_percent));
                            progressFill.style.width = percent + '%';
                            progressText.textContent = percent + '%';
                        }

                        if (payload.current_step) {
                            stepText.textContent = payload.current_step;
                        }

                        if (payload.message) {
                            statusText.textContent = payload.message;
                        }

                        if (payload.status === 'installed' || payload.status === 'failed') {
                            clearInterval(timer);
                        }
                    } catch (error) {
                        // Ignore transient polling errors while the installer runs.
                    }
                };

                updateLiveStatus();
                const timer = setInterval(updateLiveStatus, 3000);
            })();
        </script>
    @endif
</body>
</html>
