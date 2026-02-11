<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accommodation extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'type',
        'description',
        'address',
        'barangay',
        'price_per_night',
        'price_per_day',
        'bedrooms',
        'bathrooms',
        'max_guests',
        'amenities',
        'images',
        'primary_image',
        'latitude',
        'longitude',
        'house_rules',
        'check_in_instructions',
        'rating',
        'total_reviews',
        'is_available',
        'is_verified',
        'is_featured',
        'available_from'
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'price_per_night' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
        'is_available' => 'boolean',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'available_from' => 'datetime'
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('is_verified', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->available();
    }

    public function scopeInBarangay($query, $barangay)
    {
        return $query->where('barangay', 'like', '%' . $barangay . '%');
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->price_per_night, 0, '.', ',');
    }

    public function getPrimaryImageUrlAttribute()
    {
        if ($this->primary_image) {
            return asset('storage/' . $this->primary_image);
        }
        
        $images = $this->images;
        if (is_array($images) && count($images) > 0) {
            return asset('storage/' . $images[0]);
        }
        
        return asset('/COMMUNAL.jpg');
    }

    public function getTypeLabelAttribute()
    {
        $labels = [
            'traveller-inn' => 'Traveller-Inn',
            'airbnb' => 'Airbnb',
            'daily-rental' => 'Daily Rental'
        ];
        
        return $labels[$this->type] ?? $this->type;
    }

    // Methods
    public function calculateTotalPrice($checkIn, $checkOut, $guests = 1)
    {
        $nights = $checkIn->diffInDays($checkOut);
        $basePrice = $this->price_per_night * $nights;
        
        // Add guest surcharge if超过max_guests
        $extraGuests = max(0, $guests - $this->max_guests);
        $guestSurcharge = $extraGuests * 200; // ₱200 per extra guest
        
        return $basePrice + $guestSurcharge;
    }

    public function isBooked($checkIn, $checkOut)
    {
        return $this->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
            })
            ->exists();
    }
}

