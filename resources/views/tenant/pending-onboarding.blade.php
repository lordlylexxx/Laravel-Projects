<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not yet available</title>
    <style>
        :root {
            --bg: #f5f7fb;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #14532d;
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
        h1 { margin: 0 0 12px; color: var(--primary); font-size: 26px; }
        p { margin: 0; color: var(--muted); line-height: 1.6; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ $tenant->name }}</h1>
        <p>{{ $message ?? 'This space is not available yet.' }}</p>
    </div>
</body>
</html>
