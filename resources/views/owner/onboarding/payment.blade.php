<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Payment setup — {{ $tenant->name }}</title>
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
        .wrap { width: 100%; max-width: 700px; }
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 18px 40px rgba(0,0,0,0.08);
        }
        h1 { font-size: 1.35rem; margin: 0 0 8px; color: var(--primary); }
        .muted { color: var(--muted); font-size: 0.95rem; margin-bottom: 20px; line-height: 1.5; }
        .amount { font-size: 1.75rem; font-weight: 800; color: var(--primary); margin-bottom: 8px; }
        .ref { font-family: ui-monospace, monospace; font-size: 0.9rem; background: #f1f5f9; padding: 8px 12px; border-radius: 8px; margin-bottom: 16px; word-break: break-all; }
        .section {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 16px;
            margin-top: 14px;
            background: #fff;
        }
        .section h2 { margin: 0 0 8px; font-size: 1rem; color: var(--ink); }
        .section p { margin: 0 0 10px; color: var(--muted); font-size: 0.88rem; line-height: 1.5; }
        .btn {
            border: none;
            border-radius: 10px;
            padding: 12px 14px;
            font-weight: 700;
            font-size: 0.92rem;
            color: #fff;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            cursor: pointer;
            width: 100%;
        }
        .btn.secondary { background: #111827; }
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
        .flash.error {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .note { font-size: 0.82rem; color: var(--muted); margin-top: 10px; line-height: 1.5; }
        label { font-size: 0.84rem; font-weight: 700; display: block; margin-bottom: 6px; color: #374151; }
        input[type="file"] { width: 100%; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            @if(session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash error">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="flash error">{{ $errors->first() }}</div>
            @endif

            <h1>Choose payment method</h1>
            <p class="muted">Complete your subscription payment to continue onboarding. After submission, a central admin will review and approve your space.</p>

            <div class="amount">{{ $currency }}{{ number_format($amount, 0) }}</div>
            <div><strong>Reference</strong></div>
            <div class="ref">{{ $reference }}</div>

            <section class="section">
                <h2>Pay with Stripe</h2>
                <p>Instant online payment via Stripe Checkout (cards, e-wallets supported by Stripe in your region).</p>
                <form method="POST" action="{{ route('owner.onboarding.payment.stripe.checkout') }}">
                    @csrf
                    <button type="submit" class="btn">Pay now with Stripe</button>
                </form>
            </section>

            <section class="section">
                <h2>Pay with GCash</h2>
                <p>Send payment using GCash then upload your screenshot proof for admin review.</p>
                <p>
                    <strong>Account:</strong> {{ $onboardingGcashAccountName !== '' ? $onboardingGcashAccountName : 'Not configured' }}<br>
                    <strong>GCash number:</strong> {{ $onboardingGcashNumber !== '' ? $onboardingGcashNumber : 'Not configured' }}
                </p>
                <form method="POST" action="{{ route('owner.onboarding.payment.submit') }}" enctype="multipart/form-data">
                    @csrf
                    <label for="gcash_payment_proof">Upload proof screenshot</label>
                    <input id="gcash_payment_proof" name="gcash_payment_proof" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
                    <button type="submit" class="btn secondary">Submit GCash proof</button>
                </form>
                <p class="note">Accepted files: JPG, PNG, WEBP up to 5MB.</p>
            </section>
        </div>
    </div>
</body>
</html>
