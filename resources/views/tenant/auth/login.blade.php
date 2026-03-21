<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenant->name ?? 'Tenant' }} Login</title>
    <style>
        :root {
            --primary: #14532d;
            --accent: #16a34a;
            --paper: #f8fafc;
            --ink: #111827;
            --muted: #6b7280;
            --line: #d1d5db;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "Trebuchet MS", Arial, sans-serif;
            background: linear-gradient(145deg, #ffffff 0%, var(--paper) 100%);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            width: 100%;
            max-width: 460px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.08);
        }

        .kicker {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--primary);
            background: #ecfdf5;
            border: 1px solid #86efac;
            border-radius: 999px;
            padding: 6px 10px;
            margin-bottom: 12px;
        }

        h1 {
            font-size: 1.7rem;
            margin-bottom: 6px;
        }

        .subtitle {
            color: var(--muted);
            margin-bottom: 20px;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        .status {
            margin-bottom: 14px;
            background: #ecfdf5;
            border: 1px solid #86efac;
            border-radius: 10px;
            padding: 10px;
            color: #166534;
            font-size: 0.9rem;
        }

        .field { margin-bottom: 14px; }

        label {
            display: block;
            font-size: 0.88rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 12px;
            font-size: 0.95rem;
        }

        input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.16);
        }

        .error {
            margin-top: 5px;
            color: #b91c1c;
            font-size: 0.82rem;
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            font-size: 0.9rem;
        }

        .row a { color: var(--primary); text-decoration: none; }
        .row a:hover { text-decoration: underline; }

        .btn {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            cursor: pointer;
        }

        .links {
            margin-top: 16px;
            border-top: 1px solid #e5e7eb;
            padding-top: 14px;
            font-size: 0.9rem;
            color: var(--muted);
            text-align: center;
            line-height: 1.8;
        }

        .links a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
        }

        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <span class="kicker">{{ $tenant->name ?? 'Tenant Portal' }}</span>
        <h1>Owner and User Login</h1>
        <p class="subtitle">
            Sign in to access this tenant portal. Owners can manage listings, and users can browse and book accommodations.
        </p>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="field">
                <label for="email">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
                @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn">Sign In</button>

            <div class="links">
                <div>No account yet? <a href="{{ route('register') }}">Create user account</a></div>
                <div><a href="{{ route('landing') }}">Back to Tenant Landing</a></div>
            </div>
        </form>
    </div>
</body>
</html>
