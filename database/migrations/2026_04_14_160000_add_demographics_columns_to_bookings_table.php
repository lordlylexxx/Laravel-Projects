<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'guest_gender')) {
                $table->string('guest_gender', 20)->nullable()->after('number_of_guests');
            }
            if (! Schema::hasColumn('bookings', 'guest_age')) {
                $table->unsignedTinyInteger('guest_age')->nullable()->after('guest_gender');
            }
            if (! Schema::hasColumn('bookings', 'guest_is_local')) {
                $table->boolean('guest_is_local')->nullable()->after('guest_age');
            }
            if (! Schema::hasColumn('bookings', 'guest_local_place')) {
                $table->string('guest_local_place')->nullable()->after('guest_is_local');
            }
            if (! Schema::hasColumn('bookings', 'guest_country')) {
                $table->string('guest_country')->nullable()->after('guest_local_place');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            foreach (['guest_country', 'guest_local_place', 'guest_is_local', 'guest_age', 'guest_gender'] as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
