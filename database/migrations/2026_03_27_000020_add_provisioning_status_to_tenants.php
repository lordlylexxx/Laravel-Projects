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
            $table->boolean('database_provisioned')->default(false)->after('db_password')->comment('Whether the tenant database has been provisioned');
            $table->timestamp('database_provisioned_at')->nullable()->after('database_provisioned')->comment('When the database was provisioned');
            $table->text('provisioning_error')->nullable()->after('database_provisioned_at')->comment('Error message if provisioning failed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['database_provisioned', 'database_provisioned_at', 'provisioning_error']);
        });
    }
};
