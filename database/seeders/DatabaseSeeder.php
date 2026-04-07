<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // Create Admin User (only if doesn't exist)
        if (! User::where('email', 'admin@impasugong.gov.ph')->exists()) {
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

        foreach ($ownerEmails as $email) {
            if (! User::where('email', $email)->exists()) {
                $ownerData = [
                    'name' => match ($email) {
                        'sarah.chen@email.com' => 'Sarah Chen',
                        'maria.lopez@email.com' => 'Maria Lopez',
                        'john.davis@email.com' => 'John Davis',
                    },
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'owner',
                    'phone' => match ($email) {
                        'sarah.chen@email.com' => '+63 912 345 6789',
                        'maria.lopez@email.com' => '+63 923 456 7890',
                        'john.davis@email.com' => '+63 934 567 8901',
                    },
                    'address' => 'Impasugong, Bukidnon',
                ];
                User::create($ownerData);
            }
        }

        // Link landlord Tenant rows to existing MySQL tenant DBs only when needed:
        // php artisan db:seed --class=ExistingTenantDatabasesSeeder

        // Create Clients (only if doesn't exist)
        $clientEmails = [
            'juan.miguel@email.com',
            'robert.perez@email.com',
            'emily.santos@email.com',
        ];

        foreach ($clientEmails as $email) {
            if (! User::where('email', $email)->exists()) {
                $clientData = [
                    'name' => match ($email) {
                        'juan.miguel@email.com' => 'Juan Miguel',
                        'robert.perez@email.com' => 'Robert Perez',
                        'emily.santos@email.com' => 'Emily Santos',
                    },
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'client',
                    'phone' => match ($email) {
                        'juan.miguel@email.com' => '+63 945 678 9012',
                        'robert.perez@email.com' => '+63 956 789 0123',
                        'emily.santos@email.com' => '+63 967 890 1234',
                    },
                ];
                User::create($clientData);
            }
        }

        // Accommodations and bookings live in tenant databases only — not seeded on the central (landlord) DB.
        // Per-tenant demo data: make tenant current, then e.g. php artisan db:seed --class=AccommodationSeeder

        $this->command->info('========================================');
        $this->command->info('Database seeding completed!');
        $this->command->info('Test accounts:');
        $this->command->info('  - Admin: admin@impasugong.gov.ph / password');

        // Ensure newly created users remain synchronized with RBAC roles.
        User::query()->select(['id', 'role'])->chunkById(200, function ($users): void {
            foreach ($users as $user) {
                if (method_exists($user, 'syncRbacFromLegacyRole')) {
                    $user->syncRbacFromLegacyRole();
                }
            }
        });
    }
}
