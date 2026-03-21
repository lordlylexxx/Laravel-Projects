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
        Schema::create('update_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('current_version', 40);
            $table->string('latest_version', 40);
            $table->text('release_notes')->nullable();
            $table->text('download_url')->nullable();
            $table->string('channel_status', 40)->default('up_to_date');
            $table->text('status_message')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamp('installed_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'checked_at']);
            $table->index(['channel_status', 'checked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_logs');
    }
};
