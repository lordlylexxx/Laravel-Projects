<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Impasugong Accommodations</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --green-dark: #1B5E20;
            --green-primary: #2E7D32;
            --green-medium: #43A047;
            --green-soft: #C8E6C9;
            --green-white: #E8F5E9;
            --white: #FFFFFF;
            --gray-50: #F9FAFB;
            --gray-200: #E5E7EB;
            --gray-500: #6B7280;
            --gray-700: #374151;
            --danger: #DC2626;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--white) 45%, #EAF7EA 100%);
            color: var(--gray-700);
        }

        .branding {
            flex: 1;
            background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-primary) 100%);
            color: #fff;
            padding: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .branding::before {
            content: "";
            position: absolute;
            inset: 0;
            background: url('/COMMUNAL.jpg') no-repeat center center / cover;
            opacity: 0.14;
        }

        .branding-inner {
            position: relative;
            z-index: 1;
            width: min(100%, 560px);
        }

        .logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 26px;
        }

        .brand-logo {
            width: 112px;
            height: 112px;
            object-fit: contain;
            border-radius: 10px;
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.25));
        }

        .branding h1 {
            font-size: 2.1rem;
            line-height: 1.2;
            margin-bottom: 10px;
            text-align: center;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.35);
        }

        .branding p {
            text-align: center;
            font-size: 1.03rem;
            opacity: 0.95;
            margin-bottom: 22px;
        }

        .benefits { list-style: none; display: grid; gap: 10px; }
        .benefits li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.95rem;
        }
        .benefits li::before {
            content: "✓";
            width: 22px;
            height: 22px;
            border-radius: 999px;
            background: rgba(255,255,255,0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .auth-pane {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 36px;
        }

        .auth-card {
            width: 100%;
            max-width: 520px;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 18px;
            box-shadow: 0 14px 36px rgba(27, 94, 32, 0.14);
            padding: 34px 30px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 22px;
        }

        .auth-header h2 {
            color: var(--green-dark);
            font-size: 1.8rem;
            margin-bottom: 6px;
        }

        .auth-header p {
            color: var(--gray-500);
            font-size: 0.95rem;
        }

        .role-hint {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 18px;
            color: #166534;
            font-size: 0.9rem;
            text-align: center;
        }

        .global-errors {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 16px;
            font-size: 0.9rem;
        }

        .global-errors ul { margin-left: 18px; }

        .form-group { margin-bottom: 14px; }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: var(--green-dark);
            font-weight: 600;
            font-size: 0.92rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #CFE7CF;
            border-radius: 10px;
            font-size: 0.95rem;
            background: #fff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--green-primary);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.14);
        }

        .error-message {
            margin-top: 5px;
            font-size: 0.82rem;
            color: var(--danger);
        }

        .submit-btn {
            width: 100%;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            padding: 12px;
            cursor: pointer;
            margin-top: 6px;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(46, 125, 50, 0.28);
        }

        .auth-links {
            text-align: center;
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid var(--gray-200);
            color: var(--gray-500);
            font-size: 0.92rem;
        }

        .auth-links a {
            color: var(--green-primary);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-links a:hover { text-decoration: underline; }

        @media (max-width: 992px) {
            .branding { display: none; }
            .auth-pane { padding: 18px; }
            .auth-card { max-width: 620px; }
        }

        @media (max-width: 640px) {
            .auth-card { padding: 24px 18px; border-radius: 14px; }
            .auth-header h2 { font-size: 1.55rem; }
        }
    </style>
</head>
<body>
    <section class="branding" aria-hidden="true">
        <div class="branding-inner">
            <div class="logo-row">
                <img src="{{ asset('Love Impasugong.png') }}" alt="Love Impasugong" class="brand-logo" width="112" height="112">
                <img src="{{ asset('SYSTEMLOGO.png') }}" alt="ImpaStay Logo" class="brand-logo" width="112" height="112">
                <img src="{{ asset('Lgu Socmed Template-02.png') }}" alt="LGU Impasugong" class="brand-logo" width="112" height="112">
            </div>

            <h1>Join Impasugong Accommodations</h1>
            <p>Create your host account and start listing your property.</p>

            <ul class="benefits">
                <li>List accommodations with photos and complete details</li>
                <li>Receive booking requests and manage availability</li>
                <li>Coordinate directly with guests through messaging</li>
                <li>Track bookings and guest activity in one dashboard</li>
            </ul>
        </div>
    </section>

    <main class="auth-pane">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Create Account</h2>
                <p>Fill in your details to continue.</p>
            </div>

            <div class="role-hint">
                Account type: <strong>Property Owner</strong>
            </div>

            @if ($errors->any())
                <div class="global-errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="role" value="owner">

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Enter your full name"
                    >
                    @error('name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username"
                        placeholder="Enter your email"
                    >
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number (Optional)</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        autocomplete="tel"
                        placeholder="Enter your phone number"
                    >
                    @error('phone')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Create a password"
                    >
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm your password"
                    >
                    @error('password_confirmation')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                @error('role')
                    <p class="error-message" style="margin-bottom: 10px;">{{ $message }}</p>
                @enderror

                <button type="submit" class="submit-btn">Create Account</button>

                <div class="auth-links">
                    <p>Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
                    <p style="margin-top: 8px;"><a href="{{ route('landing') }}">← Back to Home</a></p>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

