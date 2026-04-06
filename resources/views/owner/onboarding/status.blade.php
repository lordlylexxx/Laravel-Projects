<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration status — {{ $tenant->name }}</title>
    <style>
        :root {
            --primary: #14532d;
            --muted: #6b7280;
            --line: #e5e7eb;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            max-width: 520px;
            width: 100%;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.06);
        }
        h1 { font-size: 1.35rem; margin: 0 0 10px; color: var(--primary); }
        p { color: var(--muted); line-height: 1.6; margin: 0 0 16px; }
        .flash {
            background: #ecfdf5;
            border: 1px solid #86efac;
            color: #166534;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-weight: 600;
        }
        .rejected { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
        a { color: var(--primary); font-weight: 700; }
    </style>
</head>
<body>
    <div class="card">
        @if(session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif

        @if($state === 'pending')
            <h1>Under review</h1>
            <p>Thank you. Your mock payment was submitted on {{ $tenant->payment_submitted_at?->format('M j, Y g:i A') ?? '—' }}. A central administrator will approve your space and you will receive tenant admin credentials by email.</p>
            <p>Reference: <strong>{{ $tenant->payment_reference ?? '—' }}</strong></p>
            <p><a href="{{ url('/logout') }}">Sign out</a></p>
        @elseif($state === 'rejected')
            <h1>Not approved</h1>
            <p class="flash rejected">Your registration was not approved. Please contact support if you believe this is an error.</p>
            <p><a href="{{ url('/logout') }}">Sign out</a></p>
        @endif
    </div>
</body>
</html>
