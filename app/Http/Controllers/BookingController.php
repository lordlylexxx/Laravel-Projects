<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display user's bookings.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->isOwner()) {
            $bookings = Booking::forOwner($user->id)
                ->with(['client', 'accommodation'])
                ->latest()
                ->paginate(10);
        } else {
            $bookings = Booking::forClient($user->id)
                ->with(['accommodation', 'accommodation.owner'])
                ->latest()
                ->paginate(10);
        }

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'accommodation_id' => 'required|exists:accommodations,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
            'client_message' => 'nullable|string',
        ]);

        $accommodation = Accommodation::findOrFail($validated['accommodation_id']);

        // Validate guests
        if ($validated['number_of_guests'] > $accommodation->max_guests) {
            return back()->withErrors(['number_of_guests' => 'Maximum guests allowed is ' . $accommodation->max_guests]);
        }

        // Check availability
        if ($accommodation->isBooked($validated['check_in_date'], $validated['check_out_date'])) {
            return back()->withErrors(['check_in_date' => 'Selected dates are not available.']);
        }

        // Calculate total price
        $checkIn = \Carbon\Carbon::parse($validated['check_in_date']);
        $checkOut = \Carbon\Carbon::parse($validated['check_out_date']);
        $totalPrice = $accommodation->calculateTotalPrice($checkIn, $checkOut, $validated['number_of_guests']);

        $validated['client_id'] = $request->user()->id;
        $validated['total_price'] = $totalPrice;
        $validated['status'] = Booking::STATUS_PENDING;

        $booking = Booking::create($validated);

        // Send message to owner
        Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $accommodation->owner_id,
            'booking_id' => $booking->id,
            'subject' => 'New Booking Request: ' . $accommodation->name,
            'content' => $validated['client_message'] ?? 'I would like to book this accommodation.',
            'type' => Message::TYPE_BOOKING_INQUIRY
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking request sent successfully! The owner will review your request.');
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

        if (!empty($validated['owner_response'])) {
            $booking->update(['owner_response' => $validated['owner_response']]);

            // Send message to client
            Message::create([
                'sender_id' => $request->user()->id,
                'receiver_id' => $booking->client_id,
                'booking_id' => $booking->id,
                'subject' => 'Booking Update: ' . $booking->accommodation->name,
                'content' => $validated['owner_response'],
                'type' => Message::TYPE_BOOKING_RESPONSE
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

        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->update([
            'status' => Booking::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'owner_response' => $request->reason ?? 'Cancelled by client'
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
            'content' => $validated['message'],
            'type' => Message::TYPE_GENERAL
        ]);

        return back()->with('success', 'Message sent successfully.');
    }
}

