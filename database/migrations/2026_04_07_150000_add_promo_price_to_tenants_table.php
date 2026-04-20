<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table): void {
            if (! Schema::hasColumn('tenants', 'promo_price')) {
                $table->decimal('promo_price', 12, 2)->nullable()->after('promo_max_listings');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table): void {
            if (Schema::hasColumn('tenants', 'promo_price')) {
                $table->dropColumn('promo_price');
            }
        });
    }
};
