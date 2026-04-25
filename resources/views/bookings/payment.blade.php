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
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            border-radius: 10px;
            padding: 10px 12px;
            color: #1E40AF;
            font-size: 0.85rem;
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
    @php
        $stripeConfigured = filled(config('services.stripe.secret'));
        $currentTenant = \App\Models\Tenant::current();
        $gcashQrUrl = $currentTenant?->getGcashQrUrl();
    @endphp
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

                @if($booking->status === 'pending')
                    <div class="notice">
                        Client payment must be submitted first. Tenant admin reviews the payment and then approves the booking.
                    </div>
                @endif

                <div class="summary-total">
                    <span>Total Amount</span>
                    <span>₱{{ number_format((float) $booking->total_price, 2) }}</span>
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

                @if(session('error'))
                    <div class="error-list" style="margin-bottom:12px;">
                        {{ session('error') }}
                    </div>
                @endif

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

                    <div class="actions">
                        <button
                            type="submit"
                            class="btn btn-primary"
                            @disabled(! $stripeConfigured || in_array($booking->status, ['paid', 'completed', 'cancelled']))
                        >
                            <i class="fab fa-stripe"></i> Pay with Stripe
                        </button>
                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">
                            Back
                        </a>
                    </div>
                </form>

                <div class="notice" style="margin-top: 14px;">
                    <strong>GCash (manual review)</strong>
                    @if($gcashQrUrl)
                        <div style="margin-top:10px;">
                            <a href="{{ $gcashQrUrl }}" target="_blank">
                                <img src="{{ $gcashQrUrl }}" alt="GCash QR" style="max-width:180px; width:100%; border-radius:10px; border:1px solid var(--gray-200);">
                            </a>
                        </div>
                    @else
                        <p style="margin-top:8px;">Tenant admin has not uploaded a GCash QR photo yet.</p>
                    @endif

                    <form action="{{ route('bookings.payment-proof.upload', $booking) }}" method="POST" enctype="multipart/form-data" style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                        @csrf
                        <input type="file" name="gcash_payment_proof" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
                        <button type="submit" class="btn btn-secondary">Upload Proof Screenshot</button>
                    </form>

                    @if($booking->gcash_payment_proof_url)
                        <div style="margin-top:10px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                            <a href="{{ $booking->gcash_payment_proof_url }}" target="_blank" class="btn btn-secondary">View Uploaded Proof</a>
                            <form action="{{ route('bookings.payment-proof.remove', $booking) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary">Remove Proof</button>
                            </form>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</body>
</html>
