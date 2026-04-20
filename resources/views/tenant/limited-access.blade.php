<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Limited Access</title>
    <style>
        :root {
            --bg: #f5f7fb;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #14532d;
            --border: #e5e7eb;
            --soft: #ecfdf3;
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
            width: min(720px, 100%);
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
            color: var(--primary);
            background: var(--soft);
            border: 1px solid #c8e6c9;
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
            line-height: 1.6;
            font-size: 16px;
        }

        .actions {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 600;
            border: 1px solid var(--border);
        }

        .btn.primary {
            background: #2e7d32;
            color: #fff;
            border-color: #2e7d32;
        }

        .btn.muted {
            background: #fff;
            color: #374151;
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
        <span class="badge">Limited access</span>
        <h1>You do not have access to this page</h1>
        <p>{{ $message ?? 'Your account does not currently have permission to view this area.' }}</p>
        <p>If this seems incorrect, ask your business owner or tenant admin to review your role permissions.</p>

        <div class="actions">
            <a href="/dashboard" class="btn primary">Go to dashboard</a>
            <a href="/" class="btn muted">Back to home</a>
        </div>

        <p class="tenant">{{ $tenant->name ?? 'Tenant Portal' }}</p>
    </main>
</body>
</html>
