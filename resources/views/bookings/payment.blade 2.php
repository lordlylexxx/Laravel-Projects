<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Checkout Payment - ImpaStay</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-200: #E5E7EB; --gray-500: #6B7280; --gray-700: #374151; --gray-800: #1F2937;
            --red-500: #EF4444;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }

        .wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 35px 20px;
        }

        .header {
            margin-bottom: 18px;
        }

        .header h1 {
            color: var(--green-dark);
            font-size: 1.8rem;
            margin-bottom: 6px;
        }

        .header p {
            color: var(--gray-500);
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card {
            background: var(--white);
            border: 1px solid var(--green-soft);
            border-radius: 16px;
            box-shadow: 0 10px 28px rgba(27, 94, 32, 0.1);
            padding: 22px;
        }

        .card h2 {
            color: var(--green-dark);
            margin-bottom: 16px;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: var(--gray-700);
            font-size: 0.95rem;
        }

        .summary-total {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid var(--gray-200);
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--green-dark);
            display: flex;
            justify-content: space-between;
        }

        .notice {
            margin-top: 14px;
            background: #FEF3C7;
            border: 1px solid #FDE68A;
            border-radius: 10px;
            padding: 10px 12px;
            color: #92400E;
            font-size: 0.85rem;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-size: 0.88rem;
            color: var(--gray-700);
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 11px 12px;
            border-radius: 10px;
            border: 1px solid var(--gray-200);
            font-size: 0.95rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--green-primary);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.14);
        }

        .row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .error-list {
            margin-bottom: 14px;
            padding: 10px 12px;
            border: 1px solid #FECACA;
            background: #FEF2F2;
            color: #991B1B;
            border-radius: 10px;
            font-size: 0.86rem;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 6px;
        }

        .btn {
            border: none;
            border-radius: 10px;
            padding: 12px 16px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            color: #fff;
            flex: 1;
        }

        .btn-secondary {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .status-paid {
            border-left: 4px solid var(--green-primary);
            background: #F0FDF4;
            color: #166534;
            padding: 11px 12px;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-top: 12px;
        }

        .status-cancelled {
            border-left: 4px solid var(--red-500);
            background: #FEF2F2;
            color: #991B1B;
            padding: 11px 12px;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-top: 12px;
        }

        @media (max-width: 900px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1><i class="fas fa-credit-card"></i> Checkout Payment</h1>
            <p>Complete your reservation payment for booking #{{ $booking->id }}</p>
        </div>

        <div class="checkout-grid">
            <section class="card">
                <h2><i class="fas fa-receipt"></i> Booking Summary</h2>

                <div class="summary-item">
                    <span>Accommodation</span>
                    <strong>{{ $booking->accommodation->name ?? 'N/A' }}</strong>
                </div>
                <div class="summary-item">
                    <span>Check-in</span>
                    <strong>{{ optional($booking->check_in_date)->format('M d, Y') }}</strong>
                </div>
                <div class="summary-item">
                    <span>Check-out</span>
                    <strong>{{ optional($booking->check_out_date)->format('M d, Y') }}</strong>
                </div>
                <div class="summary-item">
                    <span>Guests</span>
                    <strong>{{ $booking->number_of_guests }}</strong>
                </div>
                <div class="summary-item">
                    <span>Status</span>
                    <strong>{{ ucfirst($booking->status) }}</strong>
                </div>

                <div class="summary-total">
                    <span>Total Amount</span>
                    <span>₱{{ number_format((float) $booking->total_price, 2) }}</span>
                </div>

                <div class="notice">
                    Mock payment mode is enabled for MVP. No real payment gateway is charged in this flow.
                </div>

                @if($booking->status === 'paid' || $booking->status === 'completed')
                    <div class="status-paid">
                        This booking is already paid. Payment reference: {{ $booking->payment_reference ?? 'N/A' }}
                    </div>
                @elseif($booking->status === 'cancelled')
                    <div class="status-cancelled">
                        This booking was cancelled and can no longer be paid.
                    </div>
                @endif
            </section>

            <section class="card">
                <h2><i class="fas fa-lock"></i> Payment Details</h2>

                @if($errors->any())
                    <div class="error-list">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('bookings.payment.confirm', $booking) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="card_name">Cardholder Name</label>
                        <input id="card_name" type="text" name="card_name" value="{{ old('card_name', auth()->user()->name ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <input id="card_number" type="text" name="card_number" value="{{ old('card_number') }}" placeholder="4242 4242 4242 4242" required>
                    </div>

                    <div class="row-2">
                        <div class="form-group">
                            <label for="expiry">Expiry (MM/YY)</label>
                            <input id="expiry" type="text" name="expiry" value="{{ old('expiry') }}" placeholder="12/29" required>
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input id="cvv" type="password" name="cvv" value="{{ old('cvv') }}" placeholder="123" required>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary" @disabled(in_array($booking->status, ['paid', 'completed', 'cancelled']))>
                            <i class="fas fa-check-circle"></i> Confirm Mock Payment
                        </button>
                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">
                            Back
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</body>
</html>
