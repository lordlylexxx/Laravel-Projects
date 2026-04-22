<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('admin.partials.favicon')
    <title>Support — Central Admin</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @include('admin.partials.admin-shell-styles')
        .filters { display: grid; grid-template-columns: minmax(180px, 320px) minmax(180px, 320px) auto; gap: 10px; align-items: end; margin-bottom: 16px; }
        .filters label { font-size: 0.8rem; font-weight: 600; color: var(--gray-700); display: block; margin-bottom: 4px; }
        .filters select { width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--gray-200); min-width: 160px; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--gray-200); background: var(--white); font-weight: 600; text-decoration: none; color: var(--gray-800); font-size: 0.88rem; }
        .btn.primary { background: var(--green-primary, #2E7D32); color: #fff; border-color: transparent; }
        .support-table-wrap { overflow: auto; }
        .support-table { width: 100%; min-width: 980px; border-collapse: collapse; table-layout: fixed; }
        th, td { padding: 10px 12px; border-bottom: 1px solid var(--gray-200); text-align: left; font-size: 0.9rem; vertical-align: middle; }
        th { background: var(--green-white, #E8F5E9); font-size: 0.75rem; text-transform: uppercase; color: var(--gray-700); }
        .cell-ellipsis { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; }
        .col-created { width: 160px; }
        .col-tenant { width: 170px; }
        .col-subject { width: 180px; }
        .col-reporter { width: 280px; }
        .col-status { width: 120px; }
        .col-action { width: 90px; }
        .pill { display: inline-flex; padding: 4px 10px; border-radius: 999px; font-size: 0.78rem; font-weight: 600; }
        .pill.open { background: #DCFCE7; color: #166534; }
        .pill.resolved { background: #DBEAFE; color: #1D4ED8; }
        .support-title { padding-left: 12px; }

        @media (max-width: 980px) {
            .filters { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @include('admin.partials.top-navbar', ['active' => 'update-tickets'])

    <div class="dashboard-layout">
        <main class="main-content">
            @if(session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            <div class="card" style="margin-bottom: 18px;">
                <div class="card-header">
                    <h3 class="support-title"><i class="fas fa-life-ring"></i> Support</h3>
                </div>
                <div style="padding: 16px 20px;">
                    <form method="GET" action="{{ route('admin.update-tickets.index') }}" class="filters">
                        <div>
                            <label for="f_status">Status</label>
                            <select id="f_status" name="status" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="open" {{ ($filters['status'] ?? '') === 'open' ? 'selected' : '' }}>Pending</option>
                                <option value="resolved" {{ ($filters['status'] ?? '') === 'resolved' ? 'selected' : '' }}>Fixed</option>
                            </select>
                        </div>
                        <div>
                            <label for="f_tenant">Tulogan</label>
                            <select id="f_tenant" name="tenant_id" onchange="this.form.submit()">
                                <option value="">All</option>
                                @foreach($tenantFilterOptions as $opt)
                                    <option value="{{ $opt->id }}" {{ (string)($filters['tenant_id'] ?? '') === (string)$opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <a href="{{ route('admin.update-tickets.index') }}" class="btn">Reset</a>
                    </form>

                    <div class="support-table-wrap">
                        <table class="support-table">
                            <colgroup>
                                <col class="col-created">
                                <col class="col-tenant">
                                <col class="col-subject">
                                <col class="col-reporter">
                                <col class="col-status">
                                <col class="col-action">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Created</th>
                                    <th>Tulogan</th>
                                    <th>Subject</th>
                                    <th>Reporter</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->created_at?->format('Y-m-d H:i') }}</td>
                                        <td><span class="cell-ellipsis">{{ $ticket->tenant?->name ?? '—' }}</span></td>
                                        <td><span class="cell-ellipsis">{{ \Illuminate\Support\Str::limit($ticket->subject, 48) }}</span></td>
                                        <td><span class="cell-ellipsis">{{ $ticket->reporter_name }} <span style="color:#6B7280;font-size:0.8rem;">({{ $ticket->reporter_role }})</span></span></td>
                                        <td>
                                            @if($ticket->status === \App\Models\UpdateTicket::STATUS_RESOLVED)
                                                <span class="pill resolved">Fixed</span>
                                            @else
                                                <span class="pill open">Pending</span>
                                            @endif
                                        </td>
                                        <td><a href="{{ route('admin.update-tickets.show', $ticket) }}" class="btn primary">Open</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6">No tickets found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div style="padding: 14px 0;">{{ $tickets->links() }}</div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
