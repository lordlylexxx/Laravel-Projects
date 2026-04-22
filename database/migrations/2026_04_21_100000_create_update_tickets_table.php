<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $landlord = (string) config('multitenancy.landlord_database_connection_name', 'landlord');

        Schema::connection($landlord)->create('update_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('reporter_landlord_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reporter_role', 32);
            $table->string('reporter_name');
            $table->string('reporter_email');
            $table->string('subject', 255);
            $table->text('body');
            $table->string('status', 32)->default('open');
            $table->text('resolution_notes')->nullable();
            $table->text('reopen_note')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by_landlord_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        $landlord = (string) config('multitenancy.landlord_database_connection_name', 'landlord');

        Schema::connection($landlord)->dropIfExists('update_tickets');
    }
};
