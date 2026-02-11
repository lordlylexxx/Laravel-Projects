<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display user's messages.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $messages = Message::where(function ($query) use ($user) {
                $query->where('receiver_id', $user->id)
                      ->orWhere('sender_id', $user->id);
            })
            ->with(['sender', 'receiver', 'booking.accommodation'])
            ->latest()
            ->paginate(20);

        // Get unread count
        $unreadCount = Message::where('receiver_id', $user->id)
            ->unread()
            ->count();

        return view('messages.index', compact('messages', 'unreadCount'));
    }

    /**
     * Display a specific message.
     */
    public function show(Message $message)
    {
        $user = Auth::user();
        
        // Check if user is sender or receiver
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403);
        }

        // Mark as read if user is receiver
        if ($message->receiver_id === $user->id && $message->is_unread) {
            $message->markAsRead();
        }

        // Get conversation thread
        $thread = Message::where(function ($query) use ($message, $user) {
                $query->where('booking_id', $message->booking_id)
                      ->where(function ($q) use ($user) {
                          $q->where('sender_id', $user->id)
                            ->orWhere('receiver_id', $user->id);
                      });
            })
            ->where('id', '!=', $message->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('messages.show', compact('message', 'thread'));
    }

    /**
     * Send a new message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'booking_id' => 'nullable|exists:bookings,id',
            'type' => 'nullable|in:general,booking_inquiry,booking_response,complaint,feedback',
        ]);

        $validated['sender_id'] = $request->user()->id;
        $validated['type'] = $validated['type'] ?? Message::TYPE_GENERAL;

        $message = Message::create($validated);

        return redirect()->route('messages.show', $message)
            ->with('success', 'Message sent successfully!');
    }

    /**
     * Reply to a message.
     */
    public function reply(Request $request, Message $message)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        // Create reply
        $reply = $message->reply($validated['content'], $request->user());

        return redirect()->route('messages.show', $reply)
            ->with('success', 'Reply sent successfully!');
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(Message $message)
    {
        if (Auth::id() === $message->receiver_id) {
            $message->markAsRead();
        }

        return back();
    }

    /**
     * Archive a message.
     */
    public function archive(Message $message)
    {
        if (Auth::id() === $message->receiver_id || Auth::id() === $message->sender_id) {
            $message->archive();
        }

        return redirect()->route('messages.index')
            ->with('success', 'Message archived.');
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message)
    {
        if (Auth::id() === $message->sender_id || Auth::id() === $message->receiver_id) {
            $message->delete();
        }

        return redirect()->route('messages.index')
            ->with('success', 'Message deleted.');
    }

    /**
     * Get unread messages count (API).
     */
    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Admin: View all messages.
     */
    public function adminIndex(Request $request)
    {
        $messages = Message::with(['sender', 'receiver', 'booking.accommodation'])
            ->latest()
            ->paginate(30);

        return view('admin.messages.index', compact('messages'));
    }

    /**
     * Admin: Contact a user.
     */
    public function adminContactUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $validated['sender_id'] = Auth::id();
        $validated['type'] = Message::TYPE_GENERAL;

        Message::create($validated);

        return back()->with('success', 'Message sent to user.');
    }
}

