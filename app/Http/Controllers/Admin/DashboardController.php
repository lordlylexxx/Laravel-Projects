<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(Request $request)
    {
        $stats = [
            'total_users' => User::count(),
            'total_properties' => Accommodation::count(),
            'total_bookings' => Booking::count(),
            'pending_verifications' => Accommodation::where('is_verified', false)->count(),
            'active_users' => User::where('is_active', true)->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        $recent_users = User::latest()->take(5)->get();
        $recent_bookings = Booking::with(['client', 'accommodation'])->latest()->take(5)->get();
        $unread_messages = Message::where('receiver_id', Auth::id())->unread()->count();
        $recent_messages = Message::with(['sender', 'receiver'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_users',
            'recent_bookings',
            'recent_messages',
            'unread_messages'
        ));
    }
}

