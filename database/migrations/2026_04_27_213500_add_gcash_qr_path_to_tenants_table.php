<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tenants', 'gcash_qr_path')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('gcash_qr_path')->nullable()->after('onboarding_gcash_proof_path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tenants', 'gcash_qr_path')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('gcash_qr_path');
            });
        }
    }
};
