<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the owner dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $stats = [
            'total_properties' => $user->accommodations()->count(),
            'active_properties' => $user->accommodations()->where('is_available', true)->count(),
            'total_bookings' => Booking::forOwner($user->id)->count(),
            'pending_bookings' => Booking::forOwner($user->id)->pending()->count(),
            'confirmed_bookings' => Booking::forOwner($user->id)->confirmed()->count(),
            'total_earnings' => Booking::forOwner($user->id)->whereIn('status', ['confirmed', 'paid', 'completed'])->sum('total_price'),
        ];

        $properties = $user->accommodations()->withCount('bookings')->latest()->take(5)->get();
        $recent_bookings = Booking::forOwner($user->id)->with(['client', 'accommodation'])->latest()->take(5)->get();
        $unread_messages = Message::where('receiver_id', $user->id)->unread()->count();

        return view('owner.dashboard', compact(
            'stats',
            'properties',
            'recent_bookings',
            'unread_messages'
        ));
    }
}

