<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('domain_enabled')->default(true)->after('domain');
            $table->timestamp('domain_disabled_at')->nullable()->after('domain_enabled');
            $table->index(['domain_enabled', 'app_port']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex('tenants_domain_enabled_app_port_index');
            $table->dropColumn(['domain_enabled', 'domain_disabled_at']);
        });
    }
};
