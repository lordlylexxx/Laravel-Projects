<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>{{ $ticket->subject }} — Ticket</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            @include('partials.tenant-theme-css-vars')
            --gray-200: #E5E7EB; --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151; --gray-800: #1F2937;
        }
        @include('client.partials.top-navbar-styles')
        body {
            font-family: var(--client-nav-font, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif);
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }
        .page-shell { padding: calc(var(--client-nav-offset, 108px) + 24px) 24px 40px; max-width: 960px; margin: 0 auto; }
        .card {
            background: var(--white);
            border: 1px solid var(--green-soft);
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 5px 20px rgba(27, 94, 32, 0.08);
        }
        h1 { font-size: 1.35rem; color: var(--green-dark); margin-bottom: 12px; }
        .status-row { margin-bottom: 14px; }
        .pill { display: inline-flex; padding: 4px 12px; border-radius: 999px; font-size: 0.85rem; font-weight: 600; }
        .pill.open { background: #DCFCE7; color: #166534; }
        .pill.resolved { background: #DBEAFE; color: #1D4ED8; }
        .body { white-space: pre-wrap; line-height: 1.55; color: var(--gray-700); margin-bottom: 0; }
        .ticket-layout { display: grid; grid-template-columns: 1.3fr 1fr; gap: 16px; align-items: start; margin-top: 8px; }
        .meta-grid { display: grid; grid-template-columns: 110px 1fr; gap: 8px 10px; font-size: 0.9rem; margin-bottom: 14px; }
        .meta-key { color: var(--gray-500); }
        .meta-value { color: var(--gray-800); font-weight: 600; word-break: break-word; }
        .media-card { border: 1px solid var(--gray-200); border-radius: 10px; background: #F9FAFB; padding: 12px; }
        .media-preview {
            width: 100%;
            max-height: 360px;
            object-fit: contain;
            display: block;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            background: var(--white);
        }
        .resolution { background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; padding: 14px; margin-top: 16px; }
        .resolution h2 { font-size: 0.95rem; color: var(--green-dark); margin-bottom: 8px; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 9px;
            border: 1px solid var(--gray-200); background: var(--white); font-weight: 600; text-decoration: none; color: var(--gray-800);
        }
        @media (max-width: 900px) {
            .ticket-layout { grid-template-columns: 1fr; }
            .media-preview { max-height: 260px; }
        }
    </style>
</head>
<body>
    @include('client.partials.top-navbar', ['active' => 'update-tickets'])

    <main class="page-shell">
        <p style="margin-bottom:12px;"><a href="/update-tickets" class="btn"><i class="fas fa-arrow-left"></i> All tickets</a></p>
        <div class="card">
            <h1>{{ $ticket->subject }}</h1>
            <div class="status-row">
                @if($ticket->status === \App\Models\UpdateTicket::STATUS_RESOLVED)
                    <span class="pill resolved"><i class="fas fa-check"></i> Resolved</span>
                @else
                    <span class="pill open"><i class="fas fa-inbox"></i> Open</span>
                @endif
            </div>

            <div class="ticket-layout">
                <div>
                    <div class="meta-grid">
                        <div class="meta-key">Submitted</div>
                        <div class="meta-value">{{ $ticket->created_at?->format('M j, Y g:i A') }}</div>
                    </div>
                    <div class="body">{{ $ticket->body }}</div>
                </div>
                <div>
                    @if($ticket->attachment_url)
                        <div class="media-card">
                            <p style="font-weight:600; color:var(--gray-700); margin-bottom:8px;">Attachment</p>
                            <img src="{{ $ticket->attachment_url }}" alt="Ticket attachment" class="media-preview">
                            <p style="margin-top:10px;">
                                <a href="{{ $ticket->attachment_url }}" target="_blank" rel="noopener" class="btn">
                                    <i class="fas fa-up-right-from-square"></i> Open full image
                                </a>
                            </p>
                        </div>
                    @else
                        <div class="media-card" style="color:var(--gray-500); font-size:0.9rem;">
                            <i class="fas fa-image"></i> No attachment
                        </div>
                    @endif
                </div>
            </div>

            @if($ticket->status === \App\Models\UpdateTicket::STATUS_RESOLVED && $ticket->resolution_notes)
                <div class="resolution">
                    <h2><i class="fas fa-reply"></i> Central admin</h2>
                    <div style="white-space:pre-wrap;">{{ $ticket->resolution_notes }}</div>
                </div>
            @endif
        </div>
    </main>
</body>
</html>
