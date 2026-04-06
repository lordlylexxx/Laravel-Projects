<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock payment — {{ $tenant->name }}</title>
    <style>
        :root {
            --primary: #14532d;
            --accent: #16a34a;
            --ink: #111827;
            --muted: #6b7280;
            --line: #e5e7eb;
            --card: #fff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(145deg, #fff 0%, #f8fafc 100%);
            color: var(--ink);
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wrap { width: 100%; max-width: 520px; }
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 18px 40px rgba(0,0,0,0.08);
        }
        h1 { font-size: 1.35rem; margin: 0 0 8px; }
        .muted { color: var(--muted); font-size: 0.95rem; margin-bottom: 20px; line-height: 1.5; }
        .amount {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 8px;
        }
        .ref {
            font-family: ui-monospace, monospace;
            font-size: 0.9rem;
            background: #f1f5f9;
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            word-break: break-all;
        }
        .qr {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            padding: 16px;
            background: #fff;
            border: 1px dashed var(--line);
            border-radius: 12px;
        }
        .qr svg { max-width: 220px; height: auto; }
        .btn {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            color: #fff;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            cursor: pointer;
            margin-top: 8px;
        }
        .btn:hover { opacity: 0.95; }
        .flash {
            background: #ecfdf5;
            border: 1px solid #86efac;
            color: #166534;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .note {
            font-size: 0.82rem;
            color: var(--muted);
            margin-top: 14px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            @if(session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif
            <h1>Mock subscription payment</h1>
            <p class="muted">Demo only: scan the QR with any reader, then confirm below. A central admin must still approve your space before it goes live.</p>
            <div class="amount">{{ $currency }}{{ number_format($amount, 0) }}</div>
            <div><strong>Reference</strong></div>
            <div class="ref">{{ $reference }}</div>
            <div class="qr">{!! $qrSvg !!}</div>
            <p class="note">Payload: <code style="font-size:0.75rem;">{{ $payload }}</code></p>
            <form method="POST" action="{{ route('owner.onboarding.payment.submit') }}">
                @csrf
                <button type="submit" class="btn">I have completed payment (mock)</button>
            </form>
        </div>
    </div>
</body>
</html>
