<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedSmallInteger('app_port')->nullable()->after('domain');
            $table->unique('app_port');
        });

        $startPort = (int) env('TENANT_PORT_START', 8001);
        $usedPorts = DB::table('tenants')
            ->whereNotNull('app_port')
            ->pluck('app_port')
            ->map(fn ($port) => (int) $port)
            ->all();

        $nextPort = $startPort;

        DB::table('tenants')
            ->whereNull('app_port')
            ->orderBy('id')
            ->get(['id'])
            ->each(function ($tenant) use (&$nextPort, &$usedPorts) {
                while (in_array($nextPort, $usedPorts, true)) {
                    $nextPort++;
                }

                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update(['app_port' => $nextPort]);

                $usedPorts[] = $nextPort;
                $nextPort++;
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropUnique('tenants_app_port_unique');
            $table->dropColumn('app_port');
        });
    }
};
