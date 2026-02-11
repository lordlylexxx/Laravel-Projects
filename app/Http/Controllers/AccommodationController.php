<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AccommodationController extends Controller
{
    /**
     * Display a listing of accommodations for clients.
     */
    public function index(Request $request)
    {
        $query = Accommodation::available()->with('owner');

        // Apply filters
        if ($request->has('type') && $request->type) {
            $query->ofType($request->type);
        }

        if ($request->has('min_price') && $request->min_price) {
            $query->where('price_per_night', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        if ($request->has('guests') && $request->guests) {
            $query->where('max_guests', '>=', $request->guests);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Featured accommodations
        $featured = $query->featured()->limit(6)->get();

        // All accommodations with pagination
        $accommodations = $query->latest()->paginate(12);

        return view('client.accommodations.index', compact('accommodations', 'featured'));
    }

    /**
     * Display accommodation details.
     */
    public function show(Accommodation $accommodation)
    {
        $accommodation->load(['owner', 'bookings' => function ($query) {
            $query->whereIn('status', ['pending', 'confirmed', 'paid'])
                  ->where('check_out_date', '>=', now()->toDateString());
        }]);

        $amenities = is_array($accommodation->amenities) ? $accommodation->amenities : [];
        $images = is_array($accommodation->images) ? $accommodation->images : [];

        return view('client.accommodations.show', compact('accommodation', 'amenities', 'images'));
    }

    /**
     * Show the form for creating a new accommodation (Owner only).
     */
    public function create()
    {
        return view('owner.accommodations.create');
    }

    /**
     * Store a newly created accommodation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:traveller-inn,airbnb,daily-rental',
            'description' => 'required|string',
            'address' => 'required|string',
            'barangay' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'price_per_day' => 'nullable|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'max_guests' => 'nullable|integer|min:1',
            'amenities' => 'nullable|array',
            'house_rules' => 'nullable|string',
            'check_in_instructions' => 'nullable|string',
        ]);

        $validated['owner_id'] = $request->user()->id;
        $validated['amenities'] = $request->amenities ?? [];
        
        // Handle image upload
        if ($request->hasFile('primary_image')) {
            $validated['primary_image'] = $request->file('primary_image')->store('accommodations', 'public');
        }

        $accommodation = Accommodation::create($validated);

        return redirect()->route('owner.accommodations.index')
            ->with('success', 'Accommodation listed successfully! It will be visible after verification.');
    }

    /**
     * Show the form for editing an accommodation.
     */
    public function edit(Accommodation $accommodation)
    {
        $this->authorize('update', $accommodation);
        
        return view('owner.accommodations.edit', compact('accommodation'));
    }

    /**
     * Update an accommodation.
     */
    public function update(Request $request, Accommodation $accommodation)
    {
        $this->authorize('update', $accommodation);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:traveller-inn,airbnb,daily-rental',
            'description' => 'required|string',
            'address' => 'required|string',
            'barangay' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'price_per_day' => 'nullable|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'max_guests' => 'nullable|integer|min:1',
            'amenities' => 'nullable|array',
            'house_rules' => 'nullable|string',
            'check_in_instructions' => 'nullable|string',
            'is_available' => 'nullable|boolean',
        ]);

        $validated['amenities'] = $request->amenities ?? [];
        $validated['is_available'] = $request->has('is_available');

        // Handle image upload
        if ($request->hasFile('primary_image')) {
            // Delete old image if exists
            if ($accommodation->primary_image) {
                Storage::disk('public')->delete($accommodation->primary_image);
            }
            $validated['primary_image'] = $request->file('primary_image')->store('accommodations', 'public');
        }

        $accommodation->update($validated);

        return redirect()->route('owner.accommodations.index')
            ->with('success', 'Accommodation updated successfully!');
    }

    /**
     * Remove an accommodation.
     */
    public function destroy(Accommodation $accommodation)
    {
        $this->authorize('delete', $accommodation);

        // Delete images
        if ($accommodation->primary_image) {
            Storage::disk('public')->delete($accommodation->primary_image);
        }

        $accommodation->delete();

        return redirect()->route('owner.accommodations.index')
            ->with('success', 'Accommodation deleted successfully!');
    }

    /**
     * Display owner's accommodations.
     */
    public function ownerIndex(Request $request)
    {
        $accommodations = $request->user()
            ->accommodations()
            ->withCount('bookings')
            ->latest()
            ->paginate(10);

        return view('owner.accommodations.index', compact('accommodations'));
    }

    /**
     * Toggle accommodation availability.
     */
    public function toggleAvailability(Accommodation $accommodation)
    {
        $this->authorize('update', $accommodation);

        $accommodation->update(['is_available' => !$accommodation->is_available]);

        $status = $accommodation->is_available ? 'available' : 'unavailable';
        return back()->with('success', "Accommodation is now {$status}.");
    }
}

