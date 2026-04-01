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
        Schema::table('tenant_lifecycle_logs', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
        });

        Schema::table('tenant_lifecycle_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->change();
        });

        Schema::table('tenant_lifecycle_logs', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tenant_lifecycle_logs')->whereNull('tenant_id')->delete();

        Schema::table('tenant_lifecycle_logs', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
        });

        Schema::table('tenant_lifecycle_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable(false)->change();
        });

        Schema::table('tenant_lifecycle_logs', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }
};
