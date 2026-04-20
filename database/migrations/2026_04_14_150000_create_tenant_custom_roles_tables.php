<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_custom_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('tenant_custom_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_custom_role_id')->constrained('tenant_custom_roles')->cascadeOnDelete();
            $table->string('permission_name');
            $table->timestamps();

            $table->unique(['tenant_custom_role_id', 'permission_name'], 'tenant_custom_role_permission_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_custom_role_permissions');
        Schema::dropIfExists('tenant_custom_roles');
    }
};
