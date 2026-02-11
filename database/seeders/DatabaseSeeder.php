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
            $this->command->info('Admin user created: admin@impasugong.gov.ph / password');
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
                    'address' => 'Impasugong, Bukidnon',
                ];
                $ownerUsers[] = User::create($ownerData);
                $this->command->info("Owner user created: {$email}");
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
                $this->command->info("Client user created: {$email}");
            } else {
                $clientUsers[] = User::where('email', $email)->first();
            }
        }

        // Check if accommodations already exist
        if (Accommodation::count() === 0) {
            $this->call(AccommodationSeeder::class);
        }

        // Get all accommodations
        $allAccommodations = Accommodation::all();
        
        if ($allAccommodations->isEmpty()) {
            // Create Accommodations if Seeder didn't run
            $this->command->warn('No accommodations found. Please run: php artisan db:seed --class=AccommodationSeeder');
        }

        // Create Sample Bookings
        $bookings = [
            [
                'client_id' => $clientUsers[0]->id ?? 1,
                'accommodation_id' => $allAccommodations[0]->id ?? 1,
                'check_in_date' => now()->addDays(5)->format('Y-m-d'),
                'check_out_date' => now()->addDays(8)->format('Y-m-d'),
                'number_of_guests' => 2,
                'total_price' => 4500,
                'status' => 'pending',
                'client_message' => 'Hi, I would like to book this property for 3 nights.',
            ],
            [
                'client_id' => $clientUsers[1]->id ?? 2,
                'accommodation_id' => $allAccommodations[1]->id ?? 2,
                'check_in_date' => now()->addDays(10)->format('Y-m-d'),
                'check_out_date' => now()->addDays(15)->format('Y-m-d'),
                'number_of_guests' => 4,
                'total_price' => 16800,
                'status' => 'confirmed',
                'client_message' => 'Planning a family vacation.',
            ],
            [
                'client_id' => $clientUsers[2]->id ?? 3,
                'accommodation_id' => $allAccommodations[2]->id ?? 3,
                'check_in_date' => now()->subDays(3)->format('Y-m-d'),
                'check_out_date' => now()->subDays(1)->format('Y-m-d'),
                'number_of_guests' => 1,
                'total_price' => 2400,
                'status' => 'completed',
                'client_message' => 'Business trip.',
            ],
            [
                'client_id' => $clientUsers[0]->id ?? 1,
                'accommodation_id' => $allAccommodations[3]->id ?? 4,
                'check_in_date' => now()->addDays(20)->format('Y-m-d'),
                'check_out_date' => now()->addDays(25)->format('Y-m-d'),
                'number_of_guests' => 6,
                'total_price' => 17500,
                'status' => 'pending',
                'client_message' => 'Group adventure trip!',
            ],
            [
                'client_id' => $clientUsers[1]->id ?? 2,
                'accommodation_id' => $allAccommodations[4]->id ?? 5,
                'check_in_date' => now()->subDays(10)->format('Y-m-d'),
                'check_out_date' => now()->subDays(8)->format('Y-m-d'),
                'number_of_guests' => 2,
                'total_price' => 3200,
                'status' => 'completed',
                'client_message' => 'Weekend getaway.',
            ],
        ];

        foreach ($bookings as $booking) {
            if (!Booking::where('client_id', $booking['client_id'])
                ->where('accommodation_id', $booking['accommodation_id'])
                ->exists()) {
                Booking::create($booking);
            }
        }

        $this->command->info('Sample bookings created.');
        $this->command->info('========================================');
        $this->command->info('Database seeding completed!');
        $this->command->info('Test accounts:');
        $this->command->info('  - Admin: admin@impasugong.gov.ph / password');
        $this->command->info('  - Owner: sarah.chen@email.com / password');
        $this->command->info('  - Client: juan.miguel@email.com / password');
    }
}

