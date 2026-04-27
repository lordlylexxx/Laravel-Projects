<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_updates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('app_release_id');
            $table->enum('status', ['update_available', 'updated', 'failed'])->default('update_available');
            $table->boolean('is_current')->default(false);
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('required_at')->nullable();
            $table->timestamp('grace_until')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'app_release_id']);
            $table->index(['tenant_id', 'is_current']);
            $table->index(['tenant_id', 'required_at', 'grace_until']);
            $table->foreign('app_release_id')->references('id')->on('app_releases')->cascadeOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_updates');
    }
};
