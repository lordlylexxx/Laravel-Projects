<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\Tenant\ClientImportantNotification;
use App\Notifications\Tenant\StaffImportantNotification;
use App\Services\StripeRefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Stripe\Exception\AuthenticationException;
use Stripe\StripeClient;

class BookingController extends Controller
{
    /**
     * Display user's bookings.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $this->authorize('viewAny', Booking::class);

        $tenantId = $user->tenant_id;
        $currentTenant = Tenant::current();
        $status = $request->query('status');
        $allowedStatuses = [
            Booking::STATUS_PENDING,
            Booking::STATUS_CONFIRMED,
            Booking::STATUS_COMPLETED,
            Booking::STATUS_CANCELLED,
            Booking::STATUS_PAID,
        ];
        $statusFilter = in_array($status, $allowedStatuses, true) ? $status : null;

        if ($user->isOwner()) {
            $bookings = Booking::forOwner($user->id)
                ->when($tenantId, fn ($query) => $query->forTenant($tenantId))
                ->when($statusFilter, fn ($query) => $query->where('status', $statusFilter))
                ->with(['client', 'accommodation'])
                ->latest()
                ->paginate(10);
        } elseif ($user->isAdmin() && $currentTenant && (int) $tenantId === (int) $currentTenant->id) {
            $bookings = Booking::forTenant($currentTenant->id)
                ->when($statusFilter, fn ($query) => $query->where('status', $statusFilter))
                ->with(['client', 'accommodation'])
                ->latest()
                ->paginate(10);
        } else {
            $bookings = Booking::forClient($user->id)
                ->when($currentTenant, fn ($query) => $query->forTenant($currentTenant->id))
                ->when($statusFilter, fn ($query) => $query->where('status', $statusFilter))
                ->with(['accommodation', 'accommodation.owner'])
                ->latest()
                ->paginate(10);
        }

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request, Accommodation $accommodation)
    {
        $user = $request->user();

        if (! $user || ! $user->isClient()) {
            return back()->with('error', 'Only client accounts can book accommodations.');
        }

        $this->authorize('create', Booking::class);

        // Prevent self-booking of own listings.
        if ((int) $accommodation->owner_id === (int) $user->id) {
            return back()->with('error', 'You cannot book your own accommodation.');
        }

        $currentTenant = Tenant::current();

        if ($currentTenant && (int) $accommodation->tenant_id !== (int) $currentTenant->id) {
            abort(404);
        }

        $validated = $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
            'client_message' => 'nullable|string',
            'guest_gender' => 'nullable|in:male,female,unspecified',
            'guest_age' => 'nullable|integer|min:0|max:120',
            'guest_is_local' => 'nullable|boolean',
            'guest_local_place' => 'nullable|string|max:120|required_if:guest_is_local,1',
            'guest_country' => 'nullable|string|max:120|required_if:guest_is_local,0',
        ]);

        if (! array_key_exists('guest_is_local', $validated)) {
            $validated['guest_is_local'] = null;
        }

        if ($validated['guest_is_local'] === null) {
            $validated['guest_local_place'] = null;
            $validated['guest_country'] = null;
        } elseif ((bool) $validated['guest_is_local']) {
            $validated['guest_country'] = null;
        } else {
            $validated['guest_local_place'] = null;
        }

        // Validate guests
        if ($validated['number_of_guests'] > $accommodation->max_guests) {
            return back()->withErrors(['number_of_guests' => 'Maximum guests allowed is '.$accommodation->max_guests]);
        }

        // Check availability
        if ($accommodation->isBooked($validated['check_in_date'], $validated['check_out_date'])) {
            return back()->withErrors(['check_in_date' => 'Selected dates are not available.']);
        }

        // Calculate total price
        $checkIn = \Carbon\Carbon::parse($validated['check_in_date']);
        $checkOut = \Carbon\Carbon::parse($validated['check_out_date']);
        $totalPrice = $accommodation->calculateTotalPrice($checkIn, $checkOut, $validated['number_of_guests']);

        $validated['client_id'] = $user->id;
        $validated['accommodation_id'] = $accommodation->id;
        $validated['tenant_id'] = $currentTenant?->id
            ?? $accommodation->tenant_id
            ?? $accommodation->owner?->tenant_id;
        $validated['total_price'] = $totalPrice;
        $validated['status'] = Booking::STATUS_PENDING;

        $booking = Booking::create($validated);

        // Send message to owner
        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $accommodation->owner_id,
            'booking_id' => $booking->id,
            'tenant_id' => $booking->tenant_id,
            'subject' => 'New Booking Request: '.$accommodation->name,
            'content' => $validated['client_message'] ?? 'I would like to book this accommodation.',
            'type' => Message::TYPE_BOOKING_INQUIRY,
        ]);

        $owner = $accommodation->owner;

        $notifyStaff = static function (User $recipient) use ($accommodation, $booking): void {
            try {
                $recipient->notify(new StaffImportantNotification(
                    title: 'New booking request',
                    body: 'A guest requested '.$accommodation->name.'. Review and confirm when payment is ready.',
                    actionUrl: '/owner/bookings/'.$booking->id,
                    actionLabel: 'View booking',
                ));
            } catch (\Throwable $exception) {
                Log::warning('Staff in-app notification failed for new booking.', [
                    'booking_id' => $booking->id,
                    'user_id' => $recipient->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        };

        if ($owner) {
            $notifyStaff($owner);
        }

        User::query()
            ->where('tenant_id', (int) $booking->tenant_id)
            ->where('role', User::ROLE_ADMIN)
            ->where('is_active', true)
            ->when($owner, fn ($query) => $query->whereKeyNot($owner->id))
            ->each(fn (User $tenantAdmin) => $notifyStaff($tenantAdmin));

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking request submitted. The host will review and approve or decline it. You can complete payment after approval.');
    }

    /**
     * Display booking details.
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['accommodation', 'accommodation.owner', 'client', 'messages' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        return view('bookings.show', compact('booking'));
    }

    /**
     * Display Stripe checkout page for a booking.
     */
    public function payment(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $currentTenant = Tenant::current();
        if ($currentTenant && (int) $booking->tenant_id !== (int) $currentTenant->id) {
            abort(404);
        }

        if (! $request->user()->isClient() || (int) $booking->client_id !== (int) $request->user()->id) {
            abort(403);
        }

        if (! in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED], true)) {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Payment page is only available for pending or confirmed bookings.');
        }

        $booking->load(['accommodation', 'accommodation.owner']);

        return view('bookings.payment', compact('booking'));
    }

    /**
     * Create Stripe Checkout session for a booking payment.
     */
    public function confirmPayment(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $currentTenant = Tenant::current();
        if ($currentTenant && (int) $booking->tenant_id !== (int) $currentTenant->id) {
            abort(404);
        }

        if (! $request->user()->isClient() || (int) $booking->client_id !== (int) $request->user()->id) {
            abort(403);
        }

        if ($booking->status === Booking::STATUS_PAID || $booking->status === Booking::STATUS_COMPLETED) {
            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking is already paid.');
        }

        if ($booking->status === Booking::STATUS_CANCELLED) {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Cancelled bookings cannot be paid.');
        }

        $amountInCentavos = (int) round(((float) $booking->total_price) * 100);
        if ($amountInCentavos < 100) {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Booking amount is too low for Stripe checkout.');
        }

        $stripeSecret = (string) config('services.stripe.secret');
        if ($stripeSecret === '') {
            Log::error('Stripe secret key is missing while creating booking checkout.', [
                'booking_id' => $booking->id,
            ]);

            return back()->with('error', 'Stripe is not configured. Please contact support.');
        }

        try {
            $booking->loadMissing(['accommodation']);
            if ($booking->payment_channel !== 'stripe') {
                $booking->update(['payment_channel' => 'stripe']);
            }
            $stripe = new StripeClient($stripeSecret);

            $session = $stripe->checkout->sessions->create([
                'mode' => 'payment',
                'success_url' => route('bookings.payment.success', $booking).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('bookings.payment.cancel', $booking),
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'php',
                        'product_data' => [
                            'name' => ($booking->accommodation->name ?? 'Booking').' (#'.$booking->id.')',
                        ],
                        'unit_amount' => $amountInCentavos,
                    ],
                    'quantity' => 1,
                ]],
                'client_reference_id' => (string) $booking->id,
                'metadata' => [
                    'payment_type' => 'booking',
                    'booking_id' => (string) $booking->id,
                    'tenant_id' => (string) ($booking->tenant_id ?? ''),
                    'client_id' => (string) $booking->client_id,
                ],
            ]);

            return redirect()->away((string) $session->url);
        } catch (\Throwable $exception) {
            Log::error('Failed to create Stripe checkout for booking.', [
                'booking_id' => $booking->id,
                'error' => $exception->getMessage(),
            ]);

            if ($exception instanceof AuthenticationException) {
                return back()->with('error', 'Stripe authentication failed. Please re-check STRIPE_SECRET in .env.');
            }

            return back()->with('error', 'Unable to start Stripe checkout at the moment.');
        }
    }

    public function paymentSuccess(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $validated = $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        $stripeSecret = (string) config('services.stripe.secret');
        if ($stripeSecret === '') {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Stripe is not configured. Webhook confirmation could not be checked.');
        }

        try {
            $stripe = new StripeClient($stripeSecret);
            $session = $stripe->checkout->sessions->retrieve($validated['session_id']);

            $sessionBookingId = (string) ($session->metadata->booking_id ?? '');
            if ($sessionBookingId !== (string) $booking->id) {
                Log::warning('Stripe session booking mismatch on success redirect.', [
                    'booking_id' => $booking->id,
                    'session_id' => $validated['session_id'],
                    'session_booking_id' => $sessionBookingId,
                ]);

                return redirect()->route('bookings.show', $booking)
                    ->with('error', 'Payment session does not match this booking.');
            }

            $paymentStatus = (string) ($session->payment_status ?? '');
            $alreadyRecordedAsStripePaid = $booking->payment_channel === 'stripe'
                && in_array((string) $booking->payment_method, ['stripe_checkout'], true)
                && $booking->paid_at !== null;

            // Fallback for environments where webhook is delayed/missed:
            // trust a verified paid checkout session for this booking.
            if ($paymentStatus === 'paid' && ! $alreadyRecordedAsStripePaid) {
                $booking->update([
                    'payment_channel' => 'stripe',
                    'payment_method' => 'stripe_checkout',
                    'payment_reference' => (string) ($session->payment_intent ?? $session->id ?? ''),
                    'paid_at' => now(),
                ]);
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to retrieve Stripe session on booking success redirect.', [
                'booking_id' => $booking->id,
                'session_id' => $validated['session_id'],
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Payment completed but verification failed. Please check again shortly.');
        }

        return redirect()->route('bookings.show', $booking)->with(
            'success',
            'Stripe checkout completed. Payment has been recorded and now waits for tenant admin approval.'
        );
    }

    public function paymentCancel(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        return redirect()->route('bookings.show', $booking)
            ->with('error', 'Stripe checkout was canceled.');
    }

    public function uploadTenantGcashQr(Request $request)
    {
        $user = $request->user();
        $currentTenant = Tenant::current();

        if (! $user || ! $currentTenant || ! ($user->isOwner() || $user->isAdmin())) {
            abort(403);
        }

        if ((int) ($user->tenant_id ?? 0) !== (int) $currentTenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'gcash_qr' => ['required', 'image', 'max:5120', 'mimes:jpeg,jpg,png,webp'],
        ]);

        if ($currentTenant->gcash_qr_path) {
            Storage::disk('public')->delete($currentTenant->gcash_qr_path);
        }

        $currentTenant->gcash_qr_path = $request->file('gcash_qr')->store('tenant-gcash-qr', 'public');
        $currentTenant->save();

        return back()->with('success', 'GCash QR code photo uploaded.');
    }

    public function removeTenantGcashQr(Request $request)
    {
        $user = $request->user();
        $currentTenant = Tenant::current();

        if (! $user || ! $currentTenant || ! ($user->isOwner() || $user->isAdmin())) {
            abort(403);
        }

        if ((int) ($user->tenant_id ?? 0) !== (int) $currentTenant->id) {
            abort(403);
        }

        if ($currentTenant->gcash_qr_path) {
            Storage::disk('public')->delete($currentTenant->gcash_qr_path);
            $currentTenant->gcash_qr_path = null;
            $currentTenant->save();
        }

        return back()->with('success', 'GCash QR code photo removed.');
    }

    public function uploadPaymentProof(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $currentTenant = Tenant::current();
        if ($currentTenant && (int) $booking->tenant_id !== (int) $currentTenant->id) {
            abort(404);
        }

        $user = $request->user();
        if (! $user || ! $user->isClient() || (int) $booking->client_id !== (int) $user->id) {
            abort(403);
        }

        if (! in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED], true)) {
            return back()->with('error', 'Payment proof can only be uploaded for pending or confirmed bookings.');
        }

        $request->validate([
            'gcash_payment_proof' => ['required', 'image', 'max:5120', 'mimes:jpeg,jpg,png,webp'],
        ]);

        if ($booking->gcash_payment_proof_path) {
            Storage::disk('public')->delete($booking->gcash_payment_proof_path);
        }

        $booking->update([
            'payment_channel' => 'gcash',
            'gcash_payment_proof_path' => $request->file('gcash_payment_proof')->store('booking-payment-proofs', 'public'),
            'gcash_payment_submitted_at' => now(),
            'gcash_payment_reviewed_at' => null,
        ]);

        return back()->with('success', 'Payment proof screenshot uploaded. Waiting for host review.');
    }

    public function removePaymentProof(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $currentTenant = Tenant::current();
        if ($currentTenant && (int) $booking->tenant_id !== (int) $currentTenant->id) {
            abort(404);
        }

        $user = $request->user();
        if (! $user || ! $user->isClient() || (int) $booking->client_id !== (int) $user->id) {
            abort(403);
        }

        if ($booking->status !== Booking::STATUS_PENDING) {
            return back()->with('error', 'Payment proof can only be removed while booking is pending.');
        }

        if ($booking->gcash_payment_proof_path) {
            Storage::disk('public')->delete($booking->gcash_payment_proof_path);
        }

        $booking->update([
            'gcash_payment_proof_path' => null,
            'gcash_payment_submitted_at' => null,
            'gcash_payment_reviewed_at' => null,
            'payment_channel' => null,
        ]);

        return back()->with('success', 'Payment proof removed.');
    }

    /**
     * Update booking status (Owner only).
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'status' => 'required|in:confirmed,cancelled',
            'owner_response' => 'nullable|string',
        ]);

        if ($validated['status'] === 'confirmed') {
            $hasStripePayment = $booking->payment_channel === 'stripe'
                && $booking->paid_at !== null
                && in_array((string) $booking->payment_method, ['stripe_checkout'], true);
            $hasGcashProof = ! empty($booking->gcash_payment_proof_path);

            if (! $hasStripePayment && ! $hasGcashProof) {
                return back()->with('error', 'Cannot approve this booking yet. Client must submit payment first (Stripe or GCash proof).');
            }

            $booking->confirm();

            if ($hasGcashProof) {
                $booking->update([
                    'gcash_payment_reviewed_at' => now(),
                    'payment_channel' => $booking->payment_channel ?: 'gcash',
                    'paid_at' => $booking->paid_at ?: now(),
                ]);
            }
        } elseif ($validated['status'] === 'cancelled') {
            $refundResult = $this->refundStripePaymentIfNeeded($booking);
            if (! $refundResult['ok']) {
                return back()->with('error', (string) ($refundResult['message'] ?? 'Unable to process Stripe refund.'));
            }

            $booking->cancel();
        }

        if (! empty($validated['owner_response'])) {
            $booking->update(['owner_response' => $validated['owner_response']]);

            // Send message to client
            Message::create([
                'sender_id' => $request->user()->id,
                'receiver_id' => $booking->client_id,
                'booking_id' => $booking->id,
                'tenant_id' => $booking->tenant_id,
                'subject' => 'Booking Update: '.$booking->accommodation->name,
                'content' => $validated['owner_response'],
                'type' => Message::TYPE_BOOKING_RESPONSE,
            ]);
        }

        $booking->loadMissing('accommodation', 'client');
        $client = $booking->client;
        if ($client) {
            try {
                if ($validated['status'] === 'confirmed') {
                    $client->notify(new ClientImportantNotification(
                        title: 'Booking confirmed',
                        body: 'Your stay at '.$booking->accommodation->name.' is confirmed. Complete payment if you have not already.',
                        actionUrl: '/bookings/'.$booking->id,
                        actionLabel: 'View booking',
                    ));
                } elseif ($validated['status'] === 'cancelled') {
                    $client->notify(new ClientImportantNotification(
                        title: 'Booking cancelled',
                        body: 'Your booking for '.$booking->accommodation->name.' was cancelled by the host.',
                        actionUrl: '/bookings/'.$booking->id,
                        actionLabel: 'View booking',
                    ));
                }
            } catch (\Throwable $exception) {
                Log::warning('Client in-app notification failed for booking status change.', [
                    'booking_id' => $booking->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return back()->with('success', 'Booking status updated successfully!');
    }

    private function refundStripePaymentIfNeeded(Booking $booking): array
    {
        $isStripePayment = $booking->payment_channel === 'stripe'
            && in_array((string) $booking->payment_method, ['stripe_checkout'], true)
            && $booking->paid_at !== null;

        if (! $isStripePayment) {
            return ['ok' => true];
        }

        $paymentIntentId = trim((string) ($booking->payment_reference ?? ''));
        if ($paymentIntentId === '') {
            return ['ok' => false, 'message' => 'Cannot reject this booking yet because Stripe payment reference is missing.'];
        }

        $stripeSecret = (string) config('services.stripe.secret');
        if ($stripeSecret === '') {
            return ['ok' => false, 'message' => 'Stripe refund failed because Stripe is not configured.'];
        }

        try {
            app(StripeRefundService::class)->refundBookingPaymentIntent($stripeSecret, $booking, $paymentIntentId);

            return ['ok' => true];
        } catch (\Throwable $exception) {
            Log::error('Stripe refund failed after booking rejection.', [
                'booking_id' => $booking->id,
                'payment_reference' => $paymentIntentId,
                'error' => $exception->getMessage(),
            ]);

            return ['ok' => false, 'message' => 'Stripe refund failed. Booking was not rejected. Please try again.'];
        }
    }

    /**
     * Cancel booking (Client only).
     */
    public function cancel(Request $request, Booking $booking)
    {
        $this->authorize('cancel', $booking);

        if (! $booking->canBeCancelled()) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->update([
            'status' => Booking::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'owner_response' => $request->reason ?? 'Cancelled by client',
        ]);

        return back()->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Mark booking as paid (Owner/Admin only).
     */
    public function markAsPaid(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string',
        ]);

        $booking->markAsPaid(
            $validated['payment_method'] ?? null,
            $validated['payment_reference'] ?? null
        );

        return back()->with('success', 'Booking marked as paid.');
    }

    /**
     * Complete booking (Owner/Admin only).
     */
    public function complete(Booking $booking)
    {
        $this->authorize('update', $booking);

        $booking->complete();

        return back()->with('success', 'Booking marked as completed.');
    }

    /**
     * Send message about booking.
     */
    public function sendMessage(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $sender = $request->user();
        $receiver = $sender->id === $booking->client_id
            ? $booking->accommodation->owner_id
            : $booking->client_id;

        Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver,
            'booking_id' => $booking->id,
            'tenant_id' => $booking->tenant_id,
            'content' => $validated['message'],
            'type' => Message::TYPE_GENERAL,
        ]);

        return back()->with('success', 'Message sent successfully.');
    }
}
