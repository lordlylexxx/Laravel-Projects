<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_custom_role_id')
                ->nullable()
                ->after('tenant_id')
                ->constrained('tenant_custom_roles')
                ->nullOnDelete();
            $table->json('tenant_permission_grants')->nullable()->after('notification_preferences');
            $table->json('tenant_permission_revokes')->nullable()->after('tenant_permission_grants');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_custom_role_id');
            $table->dropColumn(['tenant_permission_grants', 'tenant_permission_revokes']);
        });
    }
};
