<?php

namespace App\Services;

use App\Models\Booking;
use Stripe\StripeClient;

class StripeRefundService
{
    public function refundBookingPaymentIntent(string $stripeSecret, Booking $booking, string $paymentIntentId): void
    {
        $stripe = new StripeClient($stripeSecret);

        $stripe->refunds->create([
            'payment_intent' => $paymentIntentId,
            'reason' => 'requested_by_customer',
            'metadata' => [
                'booking_id' => (string) $booking->id,
                'tenant_id' => (string) ($booking->tenant_id ?? ''),
                'source' => 'booking_owner_rejection',
            ],
        ]);
    }
}
