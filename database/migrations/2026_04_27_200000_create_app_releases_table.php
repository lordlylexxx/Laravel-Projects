<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_releases', function (Blueprint $table): void {
            $table->id();
            $table->string('tag')->unique();
            $table->string('title')->nullable();
            $table->longText('changelog')->nullable();
            $table->string('release_url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_stable')->default(true);
            $table->boolean('is_required')->default(false);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_releases');
    }
};
