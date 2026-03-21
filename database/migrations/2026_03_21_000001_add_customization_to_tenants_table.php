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
        Schema::table('tenants', function (Blueprint $table) {
            // Customization fields
            $table->string('app_title')->nullable()->after('name')->comment('Custom app/business name set by owner');
            $table->string('primary_color')->default('#2E7D32')->after('app_title')->comment('Primary theme color (hex)');
            $table->string('accent_color')->default('#43A047')->after('primary_color')->comment('Accent theme color (hex)');
            $table->string('logo_path')->nullable()->after('accent_color')->comment('Path to uploaded logo');
            $table->string('locale')->default('en')->after('logo_path')->comment('Language/locale preference');
            
            // Features flags
            $table->boolean('feature_bookings')->default(true)->after('locale')->comment('Enable/disable booking system');
            $table->boolean('feature_messaging')->default(true)->after('feature_bookings')->comment('Enable/disable messaging');
            $table->boolean('feature_reviews')->default(true)->after('feature_messaging')->comment('Enable/disable reviews');
            $table->boolean('feature_payments')->default(true)->after('feature_reviews')->comment('Enable/disable online payments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'app_title',
                'primary_color',
                'accent_color',
                'logo_path',
                'locale',
                'feature_bookings',
                'feature_messaging',
                'feature_reviews',
                'feature_payments',
            ]);
        });
    }
};
