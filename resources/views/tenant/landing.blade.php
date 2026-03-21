<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['hero_title'] }} - {{ $tenant->name }}</title>
    <style>
        :root {
            --primary: {{ $settings['primary_color'] }};
            --accent: {{ $settings['accent_color'] }};
            --ink: #111827;
            --paper: #f8fafc;
            --muted: #6b7280;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Trebuchet MS", "Lucida Sans Unicode", "Lucida Grande", "Lucida Sans", Arial, sans-serif;
            color: var(--ink);
            background: radial-gradient(circle at 10% 10%, color-mix(in srgb, var(--accent) 18%, #ffffff), #ffffff 40%),
                        linear-gradient(160deg, #ffffff 0%, var(--paper) 65%, color-mix(in srgb, var(--primary) 10%, #ffffff) 100%);
            min-height: 100vh;
        }

        .shell {
            max-width: 1100px;
            margin: 0 auto;
            padding: 28px 20px 56px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 36px;
        }

        .brand {
            font-weight: 800;
            letter-spacing: 0.02em;
            font-size: 1.1rem;
        }

        .badge {
            background: color-mix(in srgb, var(--primary) 12%, #ffffff);
            color: var(--primary);
            border: 1px solid color-mix(in srgb, var(--primary) 40%, #ffffff);
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 28px;
            align-items: stretch;
        }

        .hero-copy {
            background: #ffffff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin: 0 0 14px;
            font-size: clamp(1.9rem, 4vw, 3rem);
            line-height: 1.08;
        }

        .subtitle {
            margin: 0 0 22px;
            color: var(--muted);
            font-size: 1.03rem;
            line-height: 1.6;
        }

        .cta {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 24px color-mix(in srgb, var(--primary) 35%, transparent);
            transition: transform 0.2s ease;
        }

        .cta:hover {
            transform: translateY(-2px);
        }

        .login-panel {
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid #e5e7eb;
        }

        .login-panel h3 {
            margin: 0;
            font-size: 1.05rem;
            color: var(--ink);
        }

        .login-panel p {
            margin: 6px 0 12px;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .login-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .login-btn {
            display: inline-block;
            text-decoration: none;
            font-weight: 700;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .login-btn.owner {
            background: color-mix(in srgb, var(--primary) 15%, #ffffff);
            color: var(--primary);
            border-color: color-mix(in srgb, var(--primary) 35%, #ffffff);
        }

        .login-btn.user {
            background: color-mix(in srgb, var(--accent) 18%, #ffffff);
            color: #065f46;
            border-color: color-mix(in srgb, var(--accent) 45%, #ffffff);
        }

        .login-btn.signup {
            background: #ffffff;
            color: var(--ink);
            border-color: #d1d5db;
        }

        .login-btn:hover {
            transform: translateY(-1px);
        }

        .hero-media {
            border-radius: 20px;
            overflow: hidden;
            min-height: 320px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.08);
            background: linear-gradient(140deg, color-mix(in srgb, var(--primary) 30%, #ffffff), color-mix(in srgb, var(--accent) 26%, #ffffff));
        }

        .hero-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .about {
            margin-top: 30px;
            background: #ffffff;
            border-radius: 20px;
            padding: 26px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.06);
        }

        .about h2 {
            margin: 0 0 10px;
            font-size: 1.35rem;
        }

        .about p {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .foot {
            margin-top: 32px;
            color: var(--muted);
            font-size: 0.9rem;
            text-align: center;
        }

        @media (max-width: 900px) {
            .hero {
                grid-template-columns: 1fr;
            }

            .hero-media {
                min-height: 240px;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="topbar">
            <div class="brand">{{ $tenant->name }}</div>
            <div class="badge">{{ strtoupper($tenant->plan) }} PLAN</div>
        </div>

        <section class="hero">
            <div class="hero-copy">
                <h1>{{ $settings['hero_title'] }}</h1>
                <p class="subtitle">{{ $settings['hero_subtitle'] }}</p>
                <a class="cta" href="{{ $settings['cta_url'] }}">{{ $settings['cta_text'] }}</a>

                <div class="login-panel">
                    <h3>{{ $settings['login_section_title'] }}</h3>
                    <p>{{ $settings['login_section_subtitle'] }}</p>
                    <div class="login-actions">
                        <a class="login-btn owner" href="{{ route('login') }}">{{ $settings['login_text'] }}</a>
                        <a class="login-btn signup" href="{{ route('register') }}">{{ $settings['signup_text'] }}</a>
                    </div>
                </div>
            </div>

            <div class="hero-media">
                @if(!empty($settings['hero_image_url']))
                    <img src="{{ $settings['hero_image_url'] }}" alt="{{ $tenant->name }}">
                @endif
            </div>
        </section>

        <section class="about">
            <h2>{{ $settings['about_title'] }}</h2>
            <p>{{ $settings['about_text'] }}</p>
        </section>

        <div class="foot">
            {{ $tenant->domain }}
        </div>
    </div>
</body>
</html>
