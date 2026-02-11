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
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // traveller-inn, airbnb, daily-rental
            $table->text('description');
            $table->text('address');
            $table->string('barangay');
            $table->decimal('price_per_night', 10, 2);
            $table->decimal('price_per_day', 10, 2)->nullable();
            $table->integer('bedrooms')->default(1);
            $table->integer('bathrooms')->default(1);
            $table->integer('max_guests')->default(2);
            $table->json('amenities')->nullable();
            $table->json('images')->nullable();
            $table->string('primary_image')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('house_rules')->nullable();
            $table->text('check_in_instructions')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('available_from')->nullable();
            $table->timestamps();
            
            $table->index(['owner_id', 'type']);
            $table->index(['is_available', 'is_verified']);
            $table->index(['price_per_night', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};

