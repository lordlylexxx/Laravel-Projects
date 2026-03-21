<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('role')->constrained('tenants')->nullOnDelete();
        });

        Schema::table('accommodations', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('owner_id')->constrained('tenants')->nullOnDelete();
            $table->index(['tenant_id', 'is_available']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('accommodation_id')->constrained('tenants')->nullOnDelete();
            $table->index(['tenant_id', 'status']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('booking_id')->constrained('tenants')->nullOnDelete();
            $table->index(['tenant_id', 'created_at']);
        });

        $this->backfillTenantData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_tenant_id_created_at_index');
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_tenant_id_status_index');
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropIndex('accommodations_tenant_id_is_available_index');
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });
    }

    private function backfillTenantData(): void
    {
        $owners = DB::table('users')
            ->where('role', 'owner')
            ->select(['id', 'name'])
            ->get();

        foreach ($owners as $owner) {
            $existingTenantId = DB::table('tenants')
                ->where('owner_user_id', $owner->id)
                ->value('id');

            $tenantId = $existingTenantId;

            if (! $tenantId) {
                $tenantId = DB::table('tenants')->insertGetId([
                    'name' => $owner->name . "'s Space",
                    'slug' => Str::slug($owner->name . '-' . $owner->id . '-' . Str::random(6)),
                    'owner_user_id' => $owner->id,
                    'plan' => 'basic',
                    'subscription_status' => 'trialing',
                    'trial_ends_at' => now()->addDays(14),
                    'current_period_starts_at' => now(),
                    'current_period_ends_at' => now()->addMonth(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('users')
                ->where('id', $owner->id)
                ->update(['tenant_id' => $tenantId]);
        }

        $ownerTenantMap = DB::table('users')
            ->whereNotNull('tenant_id')
            ->pluck('tenant_id', 'id');

        DB::table('accommodations')
            ->select(['id', 'owner_id'])
            ->orderBy('id')
            ->chunkById(200, function ($accommodations) use ($ownerTenantMap) {
                foreach ($accommodations as $accommodation) {
                    $tenantId = $ownerTenantMap[$accommodation->owner_id] ?? null;

                    if ($tenantId) {
                        DB::table('accommodations')
                            ->where('id', $accommodation->id)
                            ->update(['tenant_id' => $tenantId]);
                    }
                }
            });

        $accommodationTenantMap = DB::table('accommodations')->pluck('tenant_id', 'id');

        DB::table('bookings')
            ->select(['id', 'accommodation_id'])
            ->orderBy('id')
            ->chunkById(200, function ($bookings) use ($accommodationTenantMap) {
                foreach ($bookings as $booking) {
                    $tenantId = $accommodationTenantMap[$booking->accommodation_id] ?? null;

                    if ($tenantId) {
                        DB::table('bookings')
                            ->where('id', $booking->id)
                            ->update(['tenant_id' => $tenantId]);
                    }
                }
            });

        $bookingTenantMap = DB::table('bookings')->pluck('tenant_id', 'id');
        $userTenantMap = DB::table('users')->pluck('tenant_id', 'id');

        DB::table('messages')
            ->select(['id', 'booking_id', 'sender_id'])
            ->orderBy('id')
            ->chunkById(200, function ($messages) use ($bookingTenantMap, $userTenantMap) {
                foreach ($messages as $message) {
                    $tenantId = null;

                    if (! is_null($message->booking_id)) {
                        $tenantId = $bookingTenantMap[$message->booking_id] ?? null;
                    }

                    if (! $tenantId) {
                        $tenantId = $userTenantMap[$message->sender_id] ?? null;
                    }

                    if ($tenantId) {
                        DB::table('messages')
                            ->where('id', $message->id)
                            ->update(['tenant_id' => $tenantId]);
                    }
                }
            });
    }
};
