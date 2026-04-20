<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table): void {
            $table->string('plan', 32)->default('basic')->change();
        });

        Schema::table('tenants', function (Blueprint $table): void {
            if (! Schema::hasColumn('tenants', 'promo_max_listings')) {
                $table->unsignedInteger('promo_max_listings')->nullable()->after('plan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table): void {
            if (Schema::hasColumn('tenants', 'promo_max_listings')) {
                $table->dropColumn('promo_max_listings');
            }
        });

        Schema::table('tenants', function (Blueprint $table): void {
            $table->enum('plan', ['basic', 'plus', 'pro'])->default('basic')->change();
        });
    }
};
