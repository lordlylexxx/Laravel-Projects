<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('admin.partials.favicon')
    <title>Ticket #{{ $ticket->id }} — Central Admin</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @include('admin.partials.admin-shell-styles')
        .card { background: #fff; border: 1px solid var(--green-soft, #C8E6C9); border-radius: 12px; margin-bottom: 16px; }
        .card-inner { padding: 20px; }
        .body { white-space: pre-wrap; line-height: 1.55; color: #374151; margin: 14px 0; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 14px; border-radius: 8px; border: 1px solid #E5E7EB; background: #fff; font-weight: 600; cursor: pointer; text-decoration: none; color: #1F2937; }
        .btn.primary { background: #2E7D32; color: #fff; border-color: transparent; }
        .btn.warn { background: #F59E0B; color: #fff; border-color: transparent; }
        textarea { width: 100%; max-width: 720px; padding: 10px 12px; border-radius: 8px; border: 1px solid #E5E7EB; margin-top: 6px; min-height: 100px; }
        label { font-weight: 600; color: #374151; display: block; margin-top: 12px; }
        .flash-error { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 10px 12px; border-radius: 10px; margin-bottom: 12px; font-weight: 600; }
    </style>
</head>
<body>
    @include('admin.partials.top-navbar', ['active' => 'update-tickets'])

    <div class="dashboard-layout">
        <main class="main-content">
            @if(session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            <p style="margin-bottom:12px;"><a href="{{ route('admin.update-tickets.index') }}" class="btn"><i class="fas fa-arrow-left"></i> All tickets</a></p>

            <div class="card">
                <div class="card-inner">
                    <h1 style="font-size:1.2rem;color:#1B5E20;margin-bottom:8px;">{{ $ticket->subject }}</h1>
                    <p style="color:#6B7280;font-size:0.9rem;">
                        Tulogan: <strong>{{ $ticket->tenant?->name ?? '—' }}</strong>
                        · Reporter: {{ $ticket->reporter_name }} ({{ $ticket->reporter_role }}) · {{ $ticket->reporter_email }}
                        · {{ $ticket->created_at?->format('M j, Y g:i A') }}
                    </p>
                    <p style="margin:10px 0;">
                        @if($ticket->status === \App\Models\UpdateTicket::STATUS_RESOLVED)
                            <span style="background:#DBEAFE;color:#1D4ED8;padding:4px 12px;border-radius:999px;font-weight:600;font-size:0.85rem;">Fixed</span>
                        @else
                            <span style="background:#DCFCE7;color:#166534;padding:4px 12px;border-radius:999px;font-weight:600;font-size:0.85rem;">Pending</span>
                        @endif
                    </p>
                    <div class="body">{{ $ticket->body }}</div>

                    @if($ticket->resolution_notes)
                        <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:10px;padding:14px;margin-top:12px;">
                            <strong style="color:#166534;">Resolution notes</strong>
                            <div class="body" style="margin:8px 0 0;">{{ $ticket->resolution_notes }}</div>
                        </div>
                    @endif
                    @if($ticket->reopen_note)
                        <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:10px;padding:14px;margin-top:12px;">
                            <strong>Reopen note</strong>
                            <div class="body" style="margin:8px 0 0;">{{ $ticket->reopen_note }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-inner">
                    <h2 style="font-size:1rem;color:#374151;margin-bottom:12px;">Update status</h2>

                    @if ($errors->any())
                        <div class="flash-error">{{ $errors->first() }}</div>
                    @endif

                    @if($ticket->status === \App\Models\UpdateTicket::STATUS_OPEN)
                        <form method="POST" action="{{ route('admin.update-tickets.update', $ticket) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="resolve">
                            <label for="resolution_notes">Resolution notes (required)</label>
                            <textarea id="resolution_notes" name="resolution_notes" required maxlength="10000">{{ old('resolution_notes') }}</textarea>
                            <div style="margin-top:14px;">
                                <button type="submit" class="btn primary"><i class="fas fa-check"></i> Mark fixed</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('admin.update-tickets.update', $ticket) }}" style="margin-top:12px;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="unresolve">
                            <div style="margin-top:14px;">
                                <button type="submit" class="btn"><i class="fas fa-rotate-left"></i> Unresolve ticket</button>
                            </div>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.update-tickets.update', $ticket) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="reopen">
                            <label for="reopen_note">Reopen note (optional)</label>
                            <textarea id="reopen_note" name="reopen_note" maxlength="5000">{{ old('reopen_note') }}</textarea>
                            <div style="margin-top:14px;">
                                <button type="submit" class="btn warn"><i class="fas fa-undo"></i> Reopen ticket</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('admin.update-tickets.update', $ticket) }}" style="margin-top:12px;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="unresolve">
                            <div style="margin-top:14px;">
                                <button type="submit" class="btn"><i class="fas fa-rotate-left"></i> Unresolve ticket</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>
