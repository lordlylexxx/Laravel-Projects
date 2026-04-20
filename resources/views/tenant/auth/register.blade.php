<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Sign Up - {{ $tenant->name ?? 'Tenant' }}</title>
    <style>
        :root {
            --primary: {{ $tenant->primary_color ?? '#14532d' }};
            --accent: {{ $tenant->accent_color ?? '#16a34a' }};
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

        .shell {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 560px;
        }

        .header {
            text-align: center;
            margin-bottom: 28px;
        }

        .logo {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: contain;
            margin-bottom: 14px;
        }

        .tenant-name {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--ink);
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .tagline {
            font-size: 0.95rem;
            color: var(--muted);
        }

        .card {
            width: 100%;
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
            background: color-mix(in srgb, var(--primary) 12%, #ffffff);
            border: 1px solid color-mix(in srgb, var(--primary) 35%, #ffffff);
            border-radius: 999px;
            padding: 6px 10px;
            margin-bottom: 12px;
        }

        h1 {
            font-size: 1.5rem;
            margin-bottom: 6px;
            color: var(--ink);
        }

        .subtitle {
            color: var(--muted);
            margin-bottom: 20px;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        .field { margin-bottom: 14px; }

        label {
            display: block;
            font-size: 0.88rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--ink);
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 12px;
            font-size: 0.95rem;
            color: var(--ink);
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 20%, transparent);
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .error {
            margin-top: 5px;
            color: #b91c1c;
            font-size: 0.82rem;
        }

        .btn {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            cursor: pointer;
            margin-top: 4px;
            font-size: 0.95rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px color-mix(in srgb, var(--primary) 35%, transparent);
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

        @media (max-width: 680px) {
            .grid { grid-template-columns: 1fr; }
            .tenant-name { font-size: 1.5rem; }
            .card { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="header">
            @if($tenant->getLogoUrl())
                <img src="{{ $tenant->getLogoUrl() }}" alt="{{ $tenant->name }}" class="logo" onerror="this.onerror=null;this.src='{{ asset('SYSTEMLOGO.png') }}';">
            @else
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.8rem; margin-bottom: 14px;">
                    {{ substr($tenant->name, 0, 1) }}
                </div>
            @endif
            <h2 class="tenant-name">{{ $tenant->name }}</h2>
            <p class="tagline">Property management portal</p>
        </div>

        <div class="card">
            <span class="kicker">Create user account</span>
            <h1>Join {{ $tenant->name }}</h1>
            <p class="subtitle">Book accommodations, manage reservations, and connect with property owners.</p>

            <form method="POST" action="/register">
                @csrf
                <input type="hidden" name="role" value="client">

            <div class="field">
                <label for="name">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="email">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="phone">Phone Number (Optional)</label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel">
                @error('phone')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="grid">
                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password">
                    @error('password')<div class="error">{{ $message }}</div>@enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <button type="submit" class="btn">Create Account</button>

            <div class="links">
                <div>Already registered? <a href="/login">Sign In</a></div>
                <div><a href="/">Back to Tenant Landing</a></div>
            </div>
            </form>
        </div>
    </div>
</body>
</html>
