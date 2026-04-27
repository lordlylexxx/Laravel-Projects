<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('admin.partials.favicon')
    <title>System Updates - Release Registry</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @include('admin.partials.admin-shell-styles')

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header-row {
            align-items: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-tile {
            background: var(--white);
            border: 1px solid var(--green-soft);
            border-radius: 12px;
            padding: 14px;
        }

        .stat-label {
            font-size: 0.78rem;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .stat-value {
            font-size: 1.25rem;
            color: var(--green-dark);
            font-weight: 700;
        }

        .release-list {
            display: grid;
            gap: 14px;
        }

        .release-card {
            background: var(--white);
            border: 1px solid var(--green-soft);
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(27, 94, 32, 0.08);
            padding: 16px;
        }

        .release-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .release-tag {
            font-size: 1rem;
            font-weight: 700;
            color: var(--green-dark);
        }

        .release-title {
            color: var(--gray-700);
            margin-top: 2px;
            font-weight: 600;
        }

        .release-meta {
            color: var(--gray-500);
            font-size: 0.86rem;
            margin-bottom: 12px;
        }

        .badges {
            display: inline-flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .badge {
            border-radius: 999px;
            padding: 3px 9px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-required {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .badge-prerelease {
            background: #e2e8f0;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .actions-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .inline-form {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .grace-input {
            width: 82px;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            padding: 7px 8px;
            font-size: 0.84rem;
            color: var(--gray-700);
        }

        .btn-admin-sm-blue {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #93c5fd;
        }

        .empty-state {
            color: var(--gray-500);
            text-align: center;
            padding: 28px 10px;
        }

        .pagination-wrap {
            margin-top: 16px;
        }

        @media (max-width: 768px) {
            .release-head {
                flex-direction: column;
                align-items: flex-start;
            }
            .inline-form {
                width: 100%;
                justify-content: flex-start;
            }
            .grace-input {
                width: 72px;
            }
        }
    </style>
</head>
<body class="admin-shell-page">
    @include('admin.partials.top-navbar', ['active' => 'updates'])
    <div class="dashboard-layout">
        <main class="main-content">
            <div class="page-header-row">
                <div class="page-header">
                    <h1>Global Release Registry</h1>
                    <p>Track releases from GitHub and tenant adoption in one place.</p>
                </div>
                <form method="POST" action="{{ route('admin.releases.sync') }}">
                    @csrf
                    <button class="btn-admin-primary" type="submit">
                        <i class="fas fa-rotate"></i>
                        Sync from GitHub
                    </button>
                </form>
            </div>

            @if(session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash" style="background:#fef2f2;border-color:#fecaca;color:#991b1b;">{{ session('error') }}</div>
            @endif

            <section class="card card-padded">
                <div class="stats-grid">
                    <div class="stat-tile">
                        <div class="stat-label">Total Tenants</div>
                        <div class="stat-value">{{ $stats['total_tenants'] ?? 0 }}</div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">On Latest</div>
                        <div class="stat-value">{{ $stats['tenants_on_latest'] ?? 0 }}</div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">Pending Latest</div>
                        <div class="stat-value">{{ $stats['tenants_pending_latest'] ?? 0 }}</div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">Required Overdue</div>
                        <div class="stat-value">{{ $stats['tenants_required_overdue'] ?? 0 }}</div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">Failed Updates</div>
                        <div class="stat-value">{{ $stats['tenants_with_failed_updates'] ?? 0 }}</div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">Latest Tag</div>
                        <div class="stat-value">{{ $stats['latest_release_tag'] ?? 'N/A' }}</div>
                    </div>
                </div>
            </section>

            <section class="release-list">
                @forelse($releases as $release)
                    <article class="release-card">
                        <div class="release-head">
                            <div>
                                <div class="release-tag">{{ $release->tag }}</div>
                                <div class="release-title">{{ $release->title }}</div>
                            </div>
                            <div class="badges">
                                @if($release->is_required)
                                    <span class="badge badge-required">Required</span>
                                @endif
                                @if(! $release->is_stable)
                                    <span class="badge badge-prerelease">Pre-release</span>
                                @endif
                            </div>
                        </div>

                        <div class="release-meta">
                            Published: {{ optional($release->published_at)->format('M d, Y h:i A') ?: 'N/A' }}
                        </div>

                        <div class="actions-row">
                            <form class="inline-form" method="POST" action="{{ route('admin.releases.required', $release) }}">
                                @csrf
                                <input
                                    class="grace-input"
                                    type="number"
                                    name="grace_days"
                                    min="0"
                                    max="60"
                                    value="7"
                                    aria-label="Grace days"
                                >
                                <button class="btn-admin-sm btn-admin-sm-amber" type="submit">Mark Required</button>
                            </form>

                            <form method="POST" action="{{ route('admin.releases.notify-all', $release) }}">
                                @csrf
                                <button class="btn-admin-sm btn-admin-sm-emerald" type="submit">Notify All</button>
                            </form>

                            <form method="POST" action="{{ route('admin.releases.force-mark-all-updated', $release) }}" onsubmit="return confirm('Force mark all tenants as updated to this release?');">
                                @csrf
                                <button class="btn-admin-sm btn-admin-sm-danger" type="submit">Force Mark All Updated</button>
                            </form>

                            @if($release->release_url)
                                <a href="{{ $release->release_url }}" target="_blank" class="btn-admin-sm btn-admin-sm-blue">
                                    Open GitHub Release
                                </a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="card card-padded empty-state">
                        No releases synced yet. Click <strong>Sync from GitHub</strong> to populate this page.
                    </div>
                @endforelse
            </section>

            <div class="pagination-wrap">
                {{ $releases->links() }}
            </div>
        </main>
    </div>
</body>
</html>
