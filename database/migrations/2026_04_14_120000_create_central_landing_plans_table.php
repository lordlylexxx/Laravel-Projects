<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('central_landing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_plan_key', 32);
            $table->string('title')->nullable();
            $table->decimal('price_amount', 12, 2)->nullable();
            $table->json('features')->nullable();
            $table->string('button_label')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        foreach ([
            ['tenant_plan_key' => 'basic', 'sort_order' => 0, 'is_featured' => false],
            ['tenant_plan_key' => 'plus', 'sort_order' => 1, 'is_featured' => true],
            ['tenant_plan_key' => 'pro', 'sort_order' => 2, 'is_featured' => false],
        ] as $row) {
            DB::table('central_landing_plans')->insert([
                'tenant_plan_key' => $row['tenant_plan_key'],
                'title' => null,
                'price_amount' => null,
                'features' => null,
                'button_label' => null,
                'is_visible' => true,
                'is_featured' => $row['is_featured'],
                'sort_order' => $row['sort_order'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('central_landing_plans');
    }
};
