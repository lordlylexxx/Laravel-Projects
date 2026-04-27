<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (! Schema::hasColumn('tenants', 'onboarding_payment_channel')) {
                $table->string('onboarding_payment_channel', 20)->nullable()->after('payment_reference');
            }

            if (! Schema::hasColumn('tenants', 'onboarding_gcash_proof_path')) {
                $table->string('onboarding_gcash_proof_path')->nullable()->after('onboarding_payment_channel');
            }

            if (! Schema::hasColumn('tenants', 'onboarding_gcash_submitted_at')) {
                $table->timestamp('onboarding_gcash_submitted_at')->nullable()->after('onboarding_gcash_proof_path');
            }

            if (! Schema::hasColumn('tenants', 'onboarding_stripe_session_id')) {
                $table->string('onboarding_stripe_session_id', 255)->nullable()->after('onboarding_gcash_submitted_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            foreach ([
                'onboarding_stripe_session_id',
                'onboarding_gcash_submitted_at',
                'onboarding_gcash_proof_path',
                'onboarding_payment_channel',
            ] as $column) {
                if (Schema::hasColumn('tenants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
