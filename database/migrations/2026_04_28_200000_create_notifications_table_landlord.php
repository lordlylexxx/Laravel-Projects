<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = (string) config('multitenancy.landlord_database_connection_name', 'landlord');

        Schema::connection($connection)->create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $connection = (string) config('multitenancy.landlord_database_connection_name', 'landlord');

        Schema::connection($connection)->dropIfExists('notifications');
    }
};
