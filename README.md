# Laravel-Projects

A Laravel 12 accommodation booking and messaging platform with role-based dashboards for clients, owners, and admins.

## Product Specification

### 1. System Overview

Project ImpaStay is a multi-tenant SaaS platform designed to manage property rentals, bookings, and user interactions through a centralized system.

The platform supports three main user roles:

- Admin (Central App)
- Unit Owners (multi-tenant accounts with subscriptions)
- Tenants (Tenant App users)

The system operates on a subscription-based model (Basic, Plus, Pro) and includes:

- Centralized administration
- Role-based access control
- Wireless system updates
- Customization capabilities

### 2. System Architecture Concept

#### Multi-Tenant Architecture

- One shared platform serves multiple Unit Owners.
- Each Unit Owner has isolated data for:
- Listings
- Bookings
- Reports
- Tenant communications
- Admin has full system-wide visibility.
- Tenants interact through a separate Tenant App.

#### Core Components

1. Central App (Admin System)
2. Unit Owner Accounts (Subscription-Based)
3. Tenant App
4. Support and Wireless Update System

### 3. User Roles and Functionalities

#### 3.1 Admin (Central App)

##### Role Description

The Admin manages the entire system and acts as a central controller and intermediary between Unit Owners and Tenants.

##### Core Functions

- Manage all users (Unit Owners and Tenants)
- Monitor system-wide activity 
- View analytics and insights
- Identify most profitable units
- Identify top-performing units
- Compare performance across Unit Owners
- Act as a middleman for communication
- Send and receive messages
- Generate system reports
- Monitor system health
- Manage subscriptions
- View update logs
- Configure system customization

##### Dashboard Features

- Total users (owners + tenants)
- Total bookings
- Monthly revenue analytics
- Subscription breakdown (Basic / Plus / Pro)
- Top-performing units
- Occupancy rate trends
- Reports panel
- Messaging inbox
- System status and health
- Update status and logs

#### 3.2 Unit Owners (Multi-Tenant Users)

##### Role Description

Unit Owners are independent users who manage their own property listings, bookings, and tenant interactions.

Each Unit Owner operates within an isolated environment and is restricted to their own data.

##### Common Functionalities (All Plans)

- Create unit listings
- Edit and update listings
- Archive listings
- Manage bookings
- Cancel bookings (based on conditions)
- Communicate with tenants

##### Subscription Plans

###### A. Basic Plan

- Limited number of unit listings
- Basic booking management
- Basic messaging functionality
- Limited analytics access

###### B. Plus Plan

- Increased listing capacity
- Full booking management
- Messaging with tenants
- Access to insights and reports
- Moderate analytics dashboard

###### C. Pro Plan

- High or unlimited listing capacity
- Advanced booking controls
- Priority communication tools
- Advanced analytics and insights
- Full reporting system
- Performance tracking (best units, best months)

##### Unit Owner Dashboard Features

- Subscription plan indicator (Basic / Plus / Pro)
- Upgrade subscription option
- Listing management panel
- Booking calendar
- Occupancy rate chart
- Revenue summary
- Analytics dashboard (varies by plan)
- Reports section
- Tenant messaging system

#### 3.3 Tenants (Tenant App Users)

##### Role Description

Tenants are end-users who interact with the system through the Tenant App to find and book rental units.

##### Core Functionalities

- Browse available units
- Search and filter listings
- View unit details
- Communicate with Unit Owners
- Book rental units
- Cancel bookings
- View booking history
- Receive notifications

##### Tenant App Features

- Home dashboard
- Property search and filters
- Listing cards and details page
- Booking form and confirmation
- Cancellation option
- Messaging/chat system
- Notifications panel
- Booking history

### 4. Support and Wireless Update System

#### Role Description

This module ensures the system remains updated, customizable, and maintainable.

#### Core Functionalities

- Check for system updates wirelessly
- Receive updates from system provider
- View update logs and release notes
- Install system updates
- Manage system customization
- Configure system settings

#### Key Features

- Update availability detection
- Version control tracking
- Release notes display
- Update installation control
- Customization settings (UI, features, configurations)
- System maintenance tools

### 5. System Workflow

#### Booking Flow

1. Tenant browses available units
2. Tenant sends booking request
3. Unit Owner reviews and manages booking
4. Booking is confirmed or declined

#### Communication Flow

- Tenant and Unit Owner (direct messaging)
- Admin and Unit Owner (management/support)
- Admin and Tenant (support)

#### Administration Flow

- Admin monitors system analytics
- Admin generates reports
- Admin manages users and subscriptions

#### Update Flow

1. System checks for updates wirelessly
2. Provider releases updates and logs
3. Admin/system applies updates
4. System configuration and customization is adjusted if needed

### 6. Key System Characteristics

- Multi-tenant architecture with shared platform and isolated user data
- Subscription-based access with features by plan
- Centralized administration across the ecosystem
- Role-based access control by user type
- Scalability for multiple Unit Owners
- Customizable settings and configurable features
- Wireless update capability for continuous improvement

## Core Features

- Multi-role authentication and authorization (client, owner, admin)
- Multi-tenant architecture (Spatie multitenancy) with tenant isolation
- Port-based tenant app routing and central app routing split
- Public landing page and guest auth routes (register, login, password reset)
- Accommodation browsing for authenticated users
- Owner property management for listings
- Booking flow with status updates (pending, confirmed, paid, completed, cancelled)
- In-app user messaging with reply, read, and archive actions
- Profile management with additional user details and avatar upload
- Admin dashboards for tenants, bookings, messages, and monitoring
- Admin tenant management: update tenant plan (Basic/Plus/Pro)
- Admin tenant domain control: enable/disable tenant domain access
- Central update channel with tenant owner/admin update checks and downloads
- Persistent update history logs (checked/installed timestamps + status)

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

### 5) Multi-tenant local ports (optional)

The project supports running central and tenant app instances on different ports.

- Central app default: `127.0.0.1:8000`
- Tenant apps: `127.0.0.1:8001+` (resolved by tenant `app_port`)

If needed, run multiple `php artisan serve` processes on different ports for local tenant testing.

## Test Accounts

After seeding, you can log in with:

- Admin: admin@impasugong.gov.ph / password
- Owner: sarah.chen@email.com / password
- Client: juan.miguel@email.com / password

Tenant-scoped seeded accounts are also created per tenant:

- `tenant{n}.admin@impastay.local` / password
- `tenant{n}.user@impastay.local` / password

## Useful Commands

~~~bash
# run tests
php artisan test

# migrate database
php artisan migrate --force

# code style (if needed)
./vendor/bin/pint

# clear caches
php artisan optimize:clear
~~~

## Project Structure (High Level)

- app/Models: User, Tenant, Accommodation, Booking, Message, UpdateLog
- app/Http/Controllers: auth, booking, messaging, dashboards
- app/Http/Middleware: role and access middleware
- app/Multitenancy: tenant finder and tenant DB switching tasks
- database/migrations and database/seeders: schema and sample data
- resources/views: blade templates for guest/client/owner/admin
- routes/web.php: application routes

## Admin Tenant Management

Central admin tenant controls are available in the Tenants page:

- `GET /admin/tenants`
- `PUT /admin/tenants/{tenant}/plan`
- `PUT /admin/tenants/{tenant}/domain-status`

Changing plan updates subscription lifecycle values, and disabling a domain blocks tenant resolution in tenant routing.

## Update System

Central update endpoints:

- `GET /system-updates/check`
- `GET /system-updates/download`

Tenant owner/admin update page:

- `GET /owner/system-updates`
- `GET /admin/system-updates`

Persistent update logs are stored in `update_logs` (landlord connection), including check status and install acknowledgement timestamps.

## Deployment Notes

- Set APP_ENV=production and APP_DEBUG=false in .env
- Run php artisan config:cache and php artisan route:cache
- Ensure storage and bootstrap/cache are writable
- Use a process manager for queue workers if queue processing is enabled

## License

This project uses the MIT License.
