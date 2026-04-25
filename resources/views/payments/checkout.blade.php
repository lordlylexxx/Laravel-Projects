<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Checkout (Test Mode)</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f7fb;
            color: #1f2937;
        }
        .container {
            max-width: 560px;
            margin: 48px auto;
            padding: 24px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }
        h1 {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 1.5rem;
        }
        p {
            margin-top: 0;
            margin-bottom: 20px;
            color: #4b5563;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }
        input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            margin-bottom: 14px;
            font-size: 0.95rem;
        }
        button {
            border: 0;
            background: #2563eb;
            color: #ffffff;
            font-weight: 700;
            padding: 10px 14px;
            border-radius: 8px;
            cursor: pointer;
        }
        .alert {
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }
        .alert.error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .hint {
            margin-top: 14px;
            font-size: 0.85rem;
            color: #6b7280;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Stripe Checkout (Test Mode)</h1>
    <p>Enter payment details in pesos (PHP). You will be redirected to Stripe-hosted checkout.</p>

    @if ($errors->any())
        <div class="alert error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('payments.checkout') }}">
        @csrf
        <label for="product_name">Product name</label>
        <input
            id="product_name"
            name="product_name"
            type="text"
            required
            maxlength="255"
            value="{{ old('product_name') }}"
        >

        <label for="amount">Amount (PHP)</label>
        <input
            id="amount"
            name="amount"
            type="number"
            min="1"
            step="0.01"
            required
            value="{{ old('amount') }}"
        >

        <button type="submit">Pay with Stripe</button>
    </form>

    <div class="hint">
        Test card: 4242 4242 4242 4242, any future expiry, any CVC, any ZIP.
    </div>
</div>
</body>
</html>
