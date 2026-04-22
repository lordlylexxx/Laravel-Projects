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
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --cream: #F1F8E9; --white: #FFFFFF;
            --gray-200: #E5E7EB; --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151; --gray-800: #1F2937;
        }
        @include('client.partials.top-navbar-styles')
        body {
            font-family: var(--client-nav-font, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif);
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }
        .page-shell { padding: calc(var(--client-nav-offset, 108px) + 24px) 24px 40px; max-width: 800px; margin: 0 auto; }
        .card {
            background: var(--white);
            border: 1px solid var(--green-soft);
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 5px 20px rgba(27, 94, 32, 0.08);
        }
        h1 { font-size: 1.35rem; color: var(--green-dark); margin-bottom: 12px; }
        .meta { color: var(--gray-500); font-size: 0.88rem; margin-bottom: 16px; }
        .body { white-space: pre-wrap; line-height: 1.55; color: var(--gray-700); margin-bottom: 20px; }
        .resolution { background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; padding: 14px; margin-top: 12px; }
        .resolution h2 { font-size: 0.95rem; color: var(--green-dark); margin-bottom: 8px; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 9px;
            border: 1px solid var(--gray-200); background: var(--white); font-weight: 600; text-decoration: none; color: var(--gray-800);
        }
    </style>
</head>
<body>
    @include('client.partials.top-navbar', ['active' => 'update-tickets'])

    <main class="page-shell">
        <p style="margin-bottom:12px;"><a href="/update-tickets" class="btn"><i class="fas fa-arrow-left"></i> All tickets</a></p>
        <div class="card">
            <h1>{{ $ticket->subject }}</h1>
            <p class="meta">
                Submitted {{ $ticket->created_at?->format('M j, Y g:i A') }}
                · Status:
                @if($ticket->status === \App\Models\UpdateTicket::STATUS_RESOLVED)
                    <strong>Resolved</strong>
                @else
                    <strong>Open</strong>
                @endif
            </p>
            <div class="body">{{ $ticket->body }}</div>
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
