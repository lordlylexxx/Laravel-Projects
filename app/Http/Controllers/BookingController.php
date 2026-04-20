<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Tenant;
use Illuminate\Http\Request;

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
     * Display mock payment page for a booking.
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

        if ($booking->status !== Booking::STATUS_CONFIRMED) {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Payment is available after your booking has been approved.');
        }

        $booking->load(['accommodation', 'accommodation.owner']);

        return view('bookings.payment', compact('booking'));
    }

    /**
     * Confirm mock payment and mark booking as paid.
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

        if ($booking->status === Booking::STATUS_PENDING) {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Please wait for the host to approve your booking before paying.');
        }

        if ($booking->status === Booking::STATUS_PAID || $booking->status === Booking::STATUS_COMPLETED) {
            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking is already paid.');
        }

        if ($booking->status === Booking::STATUS_CANCELLED) {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Cancelled bookings cannot be paid.');
        }

        $validated = $request->validate([
            'card_number' => ['required', 'string', 'min:12', 'max:25'],
            'card_name' => ['required', 'string', 'max:120'],
            'expiry' => ['required', 'string', 'max:10'],
            'cvv' => ['required', 'digits_between:3,4'],
        ]);

        $last4 = substr(preg_replace('/\D+/', '', $validated['card_number']) ?: '0000', -4);

        $booking->markAsPaid('mock_card', 'MOCK-'.now()->format('YmdHis').'-'.$last4);

        Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $booking->accommodation->owner_id,
            'booking_id' => $booking->id,
            'tenant_id' => $booking->tenant_id,
            'subject' => 'Payment Completed: '.($booking->accommodation->name ?? 'Booking #'.$booking->id),
            'content' => 'Client payment has been completed for booking #'.$booking->id.'.',
            'type' => Message::TYPE_BOOKING_RESPONSE,
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Mock payment successful! Your booking is now marked as paid.');
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
            $booking->confirm();
        } elseif ($validated['status'] === 'cancelled') {
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

        return back()->with('success', 'Booking status updated successfully!');
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
