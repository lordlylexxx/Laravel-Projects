<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (! Schema::hasColumn('tenants', 'bandwidth_usage_bytes')) {
                $table->unsignedBigInteger('bandwidth_usage_bytes')->default(0)->after('provisioning_error');
            }
            if (! Schema::hasColumn('tenants', 'bandwidth_quota_bytes')) {
                $table->unsignedBigInteger('bandwidth_quota_bytes')->nullable()->after('bandwidth_usage_bytes');
            }
            if (! Schema::hasColumn('tenants', 'bandwidth_last_recorded_at')) {
                $table->timestamp('bandwidth_last_recorded_at')->nullable()->after('bandwidth_quota_bytes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            foreach (['bandwidth_last_recorded_at', 'bandwidth_quota_bytes', 'bandwidth_usage_bytes'] as $col) {
                if (Schema::hasColumn('tenants', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
