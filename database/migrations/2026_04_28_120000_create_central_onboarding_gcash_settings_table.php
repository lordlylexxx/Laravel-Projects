<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('central_onboarding_gcash_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gcash_account_name')->nullable();
            $table->string('gcash_number', 64)->nullable();
            $table->string('gcash_qr_path', 500)->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('central_onboarding_gcash_settings')->insert([
            'id' => 1,
            'gcash_account_name' => null,
            'gcash_number' => null,
            'gcash_qr_path' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('central_onboarding_gcash_settings');
    }
};
