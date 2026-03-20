# Laravel-Projects

A Laravel 12 accommodation booking and messaging platform with role-based dashboards for clients, owners, and admins.

## Core Features

- Multi-role authentication and authorization (client, owner, admin)
- Public landing page and guest auth routes (register, login, password reset)
- Accommodation browsing for authenticated users
- Owner property management for listings
- Booking flow with status updates (pending, confirmed, paid, completed, cancelled)
- In-app user messaging with reply, read, and archive actions
- Profile management with additional user details and avatar upload
- Admin dashboards for users, bookings, messages, and monitoring

## Tech Stack

- Laravel 12
- PHP 8.2+
- MySQL or SQLite
- Vite + Tailwind CSS + Alpine.js
- Pest / PHPUnit for testing

## Requirements

- PHP 8.2 or newer
- Composer
- Node.js 18+ and npm
- Database server (MySQL recommended) or SQLite

## Quick Start

### 1) Clone and install

~~~bash
git clone https://github.com/lordlylexxx/Laravel-Projects.git
cd Laravel-Projects

composer install
npm install
~~~

### 2) Environment setup

~~~bash
cp .env.example .env
php artisan key:generate
~~~

Update your .env database values before migrating.

### 3) Migrate and seed

~~~bash
php artisan migrate --seed
php artisan storage:link
~~~

### 4) Run in development

Option A (single command, recommended):

~~~bash
composer run dev
~~~

Option B (separate terminals):

~~~bash
php artisan serve
npm run dev
~~~

## Test Accounts

After seeding, you can log in with:

- Admin: admin@impasugong.gov.ph / password
- Owner: sarah.chen@email.com / password
- Client: juan.miguel@email.com / password

## Useful Commands

~~~bash
# run tests
php artisan test

# code style (if needed)
./vendor/bin/pint

# clear caches
php artisan optimize:clear
~~~

## Project Structure (High Level)

- app/Models: User, Accommodation, Booking, Message
- app/Http/Controllers: auth, booking, messaging, dashboards
- app/Http/Middleware: role and access middleware
- database/migrations and database/seeders: schema and sample data
- resources/views: blade templates for guest/client/owner/admin
- routes/web.php: application routes

## Deployment Notes

- Set APP_ENV=production and APP_DEBUG=false in .env
- Run php artisan config:cache and php artisan route:cache
- Ensure storage and bootstrap/cache are writable
- Use a process manager for queue workers if queue processing is enabled

## License

This project uses the MIT License.
