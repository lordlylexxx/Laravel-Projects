<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('tenants', 'onboarding_status')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('onboarding_status', 32)->default('approved')->after('subscription_status');
            });
        }

        if (! Schema::hasColumn('tenants', 'payment_reference')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('payment_reference', 64)->nullable();
            });
        }

        if (! Schema::hasColumn('tenants', 'payment_submitted_at')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->timestamp('payment_submitted_at')->nullable();
            });
        }

        if (! Schema::hasColumn('tenants', 'onboarding_approved_at')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->timestamp('onboarding_approved_at')->nullable();
            });
        }

        if (! Schema::hasColumn('tenants', 'onboarding_approved_by')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->foreignId('onboarding_approved_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        DB::table('tenants')->whereNull('onboarding_status')->update(['onboarding_status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'onboarding_approved_by')) {
                $table->dropForeign(['onboarding_approved_by']);
            }
        });

        foreach ([
            'onboarding_status',
            'payment_reference',
            'payment_submitted_at',
            'onboarding_approved_at',
            'onboarding_approved_by',
        ] as $column) {
            if (Schema::hasColumn('tenants', $column)) {
                Schema::table('tenants', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
