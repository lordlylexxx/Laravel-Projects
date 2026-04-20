<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('central_landing_plans', function (Blueprint $table) {
            $table->boolean('aggregate_catalog_features')->default(false)->after('features');
        });
    }

    public function down(): void
    {
        Schema::table('central_landing_plans', function (Blueprint $table) {
            $table->dropColumn('aggregate_catalog_features');
        });
    }
};
