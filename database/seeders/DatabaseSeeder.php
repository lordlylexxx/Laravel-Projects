<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User (only if doesn't exist)
        if (!User::where('email', 'admin@impasugong.gov.ph')->exists()) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@impasugong.gov.ph',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+63 900 000 0000',
                'is_active' => true,
            ]);
        }

        // Create Accommodation Owners (only if doesn't exist)
        $ownerEmails = [
            'sarah.chen@email.com',
            'maria.lopez@email.com',
            'john.davis@email.com',
        ];
        
        $ownerUsers = [];
        foreach ($ownerEmails as $email) {
            if (!User::where('email', $email)->exists()) {
                $ownerData = [
                    'name' => match($email) {
                        'sarah.chen@email.com' => 'Sarah Chen',
                        'maria.lopez@email.com' => 'Maria Lopez',
                        'john.davis@email.com' => 'John Davis',
                    },
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'owner',
                    'phone' => match($email) {
                        'sarah.chen@email.com' => '+63 912 345 6789',
                        'maria.lopez@email.com' => '+63 923 456 7890',
                        'john.davis@email.com' => '+63 934 567 8901',
                    },
                ];
                $ownerUsers[] = User::create($ownerData);
            } else {
                $ownerUsers[] = User::where('email', $email)->first();
            }
        }

        // Create Clients (only if doesn't exist)
        $clientEmails = [
            'juan.miguel@email.com',
            'robert.perez@email.com',
            'emily.santos@email.com',
        ];
        
        $clientUsers = [];
        foreach ($clientEmails as $email) {
            if (!User::where('email', $email)->exists()) {
                $clientData = [
                    'name' => match($email) {
                        'juan.miguel@email.com' => 'Juan Miguel',
                        'robert.perez@email.com' => 'Robert Perez',
                        'emily.santos@email.com' => 'Emily Santos',
                    },
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'client',
                    'phone' => match($email) {
                        'juan.miguel@email.com' => '+63 945 678 9012',
                        'robert.perez@email.com' => '+63 956 789 0123',
                        'emily.santos@email.com' => '+63 967 890 1234',
                    },
                ];
                $clientUsers[] = User::create($clientData);
            } else {
                $clientUsers[] = User::where('email', $email)->first();
            }
        }

        // Create Accommodations
        $accommodations = [
            [
                'owner_id' => $ownerUsers[0]->id,
                'name' => 'Mountain View Inn',
                'type' => 'traveller-inn',
                'description' => 'Cozy traveller-inn with stunning mountain views. Perfect for budget travelers seeking authentic local experiences. Features comfortable rooms, hot showers, and complimentary breakfast.',
                'address' => '123 Main Road, Poblacion',
                'barangay' => 'Poblacion',
                'price_per_night' => 1500,
                'price_per_day' => null,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'max_guests' => 4,
                'amenities' => ['WiFi', 'TV', 'Hot Shower', 'Breakfast', 'Parking'],
                'primary_image' => 'COMMUNAL.jpg',
                'rating' => 4.8,
                'total_reviews' => 12,
                'is_available' => true,
                'is_verified' => true,
                'is_featured' => true,
            ],
            [
                'owner_id' => $ownerUsers[0]->id,
                'name' => 'Cozy Garden House',
                'type' => 'airbnb',
                'description' => 'Beautiful garden house with private courtyard. Ideal for couples or small families looking for a peaceful retreat.',
                'address' => '456 Garden Lane, Kapitan',
                'barangay' => 'Kapitan',
                'price_per_night' => 2800,
                'price_per_day' => null,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'max_guests' => 6,
                'amenities' => ['WiFi', 'Kitchen', 'Garden', 'Parking', 'Air Conditioning', 'Washer'],
                'primary_image' => '1.jpg',
                'rating' => 4.9,
                'total_reviews' => 8,
                'is_available' => true,
                'is_verified' => true,
                'is_featured' => true,
            ],
            [
                'owner_id' => $ownerUsers[1]->id,
                'name' => 'Riverside Apartment',
                'type' => 'daily-rental',
                'description' => 'Modern apartment near the river. Perfect for short stays and business travelers.',
                'address' => '789 Riverside Street, Centro',
                'barangay' => 'Centro',
                'price_per_night' => 1200,
                'price_per_day' => 1000,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'max_guests' => 2,
                'amenities' => ['WiFi', 'TV', 'Air Conditioning', 'Kitchenette'],
                'primary_image' => '2.jpg',
                'rating' => 4.5,
                'total_reviews' => 15,
                'is_available' => true,
                'is_verified' => true,
                'is_featured' => false,
            ],
            [
                'owner_id' => $ownerUsers[1]->id,
                'name' => 'Forest Cabin Retreat',
                'type' => 'airbnb',
                'description' => 'Secluded cabin in the forest. Perfect for nature lovers and adventure seekers.',
                'address' => '321 Forest Path, Malitbog',
                'barangay' => 'Malitbog',
                'price_per_night' => 3500,
                'price_per_day' => null,
                'bedrooms' => 4,
                'bathrooms' => 2,
                'max_guests' => 8,
                'amenities' => ['WiFi', 'Fireplace', 'Kitchen', 'Hiking Trails', 'Mountain View'],
                'primary_image' => 'airbnb1.jpg',
                'rating' => 4.95,
                'total_reviews' => 22,
                'is_available' => true,
                'is_verified' => true,
                'is_featured' => true,
            ],
            [
                'owner_id' => $ownerUsers[2]->id,
                'name' => 'Town Inn Basic',
                'type' => 'traveller-inn',
                'description' => 'Affordable and comfortable inn in the heart of town. Great for backpackers.',
                'address' => '555 Town Center, Poblacion',
                'barangay' => 'Poblacion',
                'price_per_night' => 800,
                'price_per_day' => null,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'max_guests' => 2,
                'amenities' => ['WiFi', 'Shared Bathroom', 'Common Area'],
                'primary_image' => 'inn1.jpg',
                'rating' => 4.3,
                'total_reviews' => 35,
                'is_available' => true,
                'is_verified' => true,
                'is_featured' => false,
            ],
            [
                'owner_id' => $ownerUsers[2]->id,
                'name' => 'Villa Rosa',
                'type' => 'daily-rental',
                'description' => 'Luxurious villa with private pool. Perfect for special occasions and group gatherings.',
                'address' => '888 Estate Road, Haguit',
                'barangay' => 'Haguit',
                'price_per_night' => 4500,
                'price_per_day' => 4000,
                'bedrooms' => 5,
                'bathrooms' => 3,
                'max_guests' => 10,
                'amenities' => ['Private Pool', 'WiFi', 'Kitchen', 'Parking', 'Garden', 'BBQ Area'],
                'primary_image' => 'accommodation1.jpg',
                'rating' => 4.85,
                'total_reviews' => 9,
                'is_available' => true,
                'is_verified' => true,
                'is_featured' => true,
            ],
            [
                'owner_id' => $ownerUsers[0]->id,
                'name' => 'Lakeside Villa',
                'type' => 'airbnb',
                'description' => 'Stunning lakeside property with direct lake access. Perfect for fishing and water activities.',
                'address' => '777 Lake View, Bontoc',
                'barangay' => 'Bontoc',
                'price_per_night' => 5500,
                'price_per_day' => 5000,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'max_guests' => 8,
                'amenities' => ['Lake Access', 'WiFi', 'Kitchen', 'Kayaks', 'Fishing Equipment'],
                'primary_image' => 'airbnb2.jpg',
                'rating' => 4.9,
                'total_reviews' => 18,
                'is_available' => true,
                'is_verified' => true,
                'is_featured' => true,
            ],
            [
                'owner_id' => $ownerUsers[1]->id,
                'name' => 'Mountain Lodge',
                'type' => 'traveller-inn',
                'description' => 'Traditional mountain lodge with breathtaking views. Great for nature enthusiasts.',
                'address' => '999 Mountain Trail, Kalingag',
                'barangay' => 'Kalingag',
                'price_per_night' => 2000,
                'price_per_day' => null,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'max_guests' => 6,
                'amenities' => ['WiFi', 'Restaurant', 'Mountain View', 'Hiking Guides', 'Hot Shower'],
                'primary_image' => 'inn2.jpg',
                'rating' => 4.7,
                'total_reviews' => 25,
                'is_available' => true,
                'is_verified' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($accommodations as $accommodation) {
            Accommodation::create($accommodation);
        }

        // Get all accommodations
        $allAccommodations = Accommodation::all();

        // Create Sample Bookings
        $bookings = [
            [
                'client_id' => $clientUsers[0]->id,
                'accommodation_id' => $allAccommodations[0]->id,
                'check_in_date' => now()->addDays(5)->format('Y-m-d'),
                'check_out_date' => now()->addDays(8)->format('Y-m-d'),
                'number_of_guests' => 2,
                'total_price' => 4500,
                'status' => 'pending',
                'client_message' => 'Hi, I would like to book this property for 3 nights. Looking forward to my stay!',
            ],
            [
                'client_id' => $clientUsers[1]->id,
                'accommodation_id' => $allAccommodations[1]->id,
                'check_in_date' => now()->addDays(10)->format('Y-m-d'),
                'check_out_date' => now()->addDays(15)->format('Y-m-d'),
                'number_of_guests' => 4,
                'total_price' => 16800,
                'status' => 'confirmed',
                'client_message' => 'Planning a family vacation. Would love to know if early check-in is possible.',
            ],
            [
                'client_id' => $clientUsers[2]->id,
                'accommodation_id' => $allAccommodations[2]->id,
                'check_in_date' => now()->subDays(3)->format('Y-m-d'),
                'check_out_date' => now()->subDays(1)->format('Y-m-d'),
                'number_of_guests' => 1,
                'total_price' => 2400,
                'status' => 'completed',
                'client_message' => 'Business trip. Need a quiet place to work during the day.',
            ],
        ];

        foreach ($bookings as $booking) {
            Booking::create($booking);
        }

        // Get all bookings
        $allBookings = Booking::all();

        // Create Sample Messages
        $messages = [
            [
                'sender_id' => $clientUsers[0]->id,
                'receiver_id' => $ownerUsers[0]->id,
                'booking_id' => $allBookings[0]->id,
                'subject' => 'Booking Inquiry - Mountain View Inn',
                'content' => 'Hi! I came across your Mountain View Inn listing and I\'m very interested in booking it for a weekend getaway. I wanted to inquire about availability for December 15-18, 2024. There will be 2 adults and we\'re looking forward to experiencing the local hospitality. Could you please let me know if the property is available for those dates?',
                'type' => 'booking_inquiry',
                'status' => 'sent',
            ],
            [
                'sender_id' => $ownerUsers[0]->id,
                'receiver_id' => $clientUsers[0]->id,
                'booking_id' => $allBookings[0]->id,
                'subject' => 'Re: Booking Inquiry - Mountain View Inn',
                'content' => 'Hi Juan! Thank you for your interest in Mountain View Inn. Yes, we are available for those dates. I\'ll go ahead and confirm your booking. Feel free to ask if you have any other questions!',
                'type' => 'booking_response',
                'status' => 'sent',
            ],
        ];

        foreach ($messages as $message) {
            Message::create($message);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Sample users created:');
        $this->command->info('  - Admin: admin@impasugong.gov.ph / password');
        $this->command->info('  - Owner: sarah.chen@email.com / password');
        $this->command->info('  - Client: juan.miguel@email.com / password');
    }
}

