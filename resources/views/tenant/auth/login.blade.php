<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $tenant->name ?? 'Tenant' }}</title>
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
            max-width: 520px;
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

        .portal-switch {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }

        .portal-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: 1px solid var(--line);
            color: var(--ink);
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 0.82rem;
            font-weight: 700;
            background: #ffffff;
        }

        .portal-pill.active {
            border-color: color-mix(in srgb, var(--primary) 65%, #ffffff);
            background: color-mix(in srgb, var(--primary) 12%, #ffffff);
            color: var(--primary);
        }

        .status {
            margin-bottom: 14px;
            background: color-mix(in srgb, var(--primary) 12%, #ffffff);
            border: 1px solid color-mix(in srgb, var(--primary) 35%, #ffffff);
            border-radius: 10px;
            padding: 10px;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .field { margin-bottom: 14px; }

        label {
            display: block;
            font-size: 0.88rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--ink);
        }

        input[type="email"],
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

        .row a { 
            color: var(--primary); 
            text-decoration: none; 
            font-weight: 600;
        }
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

        @media (max-width: 640px) {
            .tenant-name { font-size: 1.5rem; }
            .card { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="header">
            @if($tenant->logo_path)
                <img src="{{ asset('storage/' . $tenant->logo_path) }}" alt="{{ $tenant->name }}" class="logo">
            @else
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.8rem; margin-bottom: 14px;">
                    {{ substr($tenant->name, 0, 1) }}
                </div>
            @endif
            <h2 class="tenant-name">{{ $tenant->name }}</h2>
            <p class="tagline">Property management portal</p>
        </div>

        <div class="card">
            @php
                $selectedPortal = $portal ?? request('portal');
                if (!in_array($selectedPortal, ['admin', 'client'], true)) {
                    $selectedPortal = null;
                }

                $isAdminPortal = $selectedPortal === 'admin';
                $isClientPortal = $selectedPortal === 'client';
                $kickerText = $isAdminPortal
                    ? 'Tenant admin sign in'
                    : ($isClientPortal ? 'Client sign in' : 'Tenant sign in');
                $headingText = $isAdminPortal
                    ? 'Tenant Admin Login'
                    : ($isClientPortal ? 'Client Login' : 'Tenant Login');
                $subtitleText = $isAdminPortal
                    ? 'Sign in with your tenant admin credentials to manage properties, bookings, and tenant operations.'
                    : ($isClientPortal
                        ? 'Sign in to browse properties, book accommodations, and manage your reservations.'
                        : 'Use one login page for both tenant admin and client accounts.');
            @endphp

            <span class="kicker">{{ $kickerText }}</span>
            <h1>{{ $headingText }}</h1>
            <p class="subtitle">{{ $subtitleText }}</p>

            <div class="portal-switch">
                <a href="/login?portal=admin" class="portal-pill {{ $isAdminPortal ? 'active' : '' }}">Tenant Admin</a>
                <a href="/login?portal=client" class="portal-pill {{ $isClientPortal ? 'active' : '' }}">Client</a>
            </div>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <input type="hidden" name="portal" value="{{ $selectedPortal ?? '' }}">

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
                    <a href="/forgot-password">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn">
                Sign In{{ $isAdminPortal ? ' to Tenant Admin Portal' : ($isClientPortal ? ' to Client Portal' : '') }}
            </button>

            <div class="links">
                @if ($isAdminPortal)
                    <div>Tenant admins are provisioned by the system owner.</div>
                @elseif ($isClientPortal)
                    <div>No account yet? <a href="/register">Create client account</a></div>
                @else
                    <div>Client account? <a href="/register">Create one here</a></div>
                    <div>Tenant admins are provisioned by the system owner.</div>
                @endif
                <div><a href="/">Back to Tenant Landing</a></div>
            </div>
        </form>
        </div>
    </div>
</body>
</html>
