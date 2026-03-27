<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Required</title>
    <style>
        :root {
            --bg: #f5f7fb;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #14532d;
            --warning: #b45309;
            --border: #e5e7eb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            background: radial-gradient(circle at top, #e8f5e9 0%, var(--bg) 45%);
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .card {
            width: min(680px, 100%);
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 12px 30px rgba(20, 83, 45, 0.12);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--warning);
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 600;
        }

        h1 {
            margin: 14px 0 10px;
            color: var(--primary);
            font-size: 28px;
            line-height: 1.2;
        }

        p {
            margin: 0 0 12px;
            color: var(--muted);
            line-height: 1.55;
            font-size: 16px;
        }

        .tenant {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px dashed var(--border);
            color: #111827;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <main class="card">
        <span class="badge">Subscription required</span>
        <h1>Service temporarily unavailable</h1>
        <p>{{ $message ?? 'Pay your subscription to continue using our services.' }}</p>
        <p>Please contact support or your account owner to reactivate this tenant.</p>

        <p class="tenant">
            {{ $tenant->name ?? 'Tenant Portal' }}
        </p>
    </main>
</body>
</html>
