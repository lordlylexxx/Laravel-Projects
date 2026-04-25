<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'payment_channel')) {
                $table->string('payment_channel', 20)->nullable()->after('payment_reference');
            }

            if (! Schema::hasColumn('bookings', 'gcash_payment_proof_path')) {
                $table->string('gcash_payment_proof_path')->nullable()->after('payment_channel');
            }

            if (! Schema::hasColumn('bookings', 'gcash_payment_submitted_at')) {
                $table->timestamp('gcash_payment_submitted_at')->nullable()->after('gcash_payment_proof_path');
            }

            if (! Schema::hasColumn('bookings', 'gcash_payment_reviewed_at')) {
                $table->timestamp('gcash_payment_reviewed_at')->nullable()->after('gcash_payment_submitted_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            foreach ([
                'gcash_payment_reviewed_at',
                'gcash_payment_submitted_at',
                'gcash_payment_proof_path',
                'payment_channel',
            ] as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
