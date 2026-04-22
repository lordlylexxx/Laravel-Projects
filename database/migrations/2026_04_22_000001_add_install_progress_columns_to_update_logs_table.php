<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('update_logs', function (Blueprint $table): void {
            $table->unsignedTinyInteger('progress_percent')->default(0)->after('channel_status');
            $table->string('current_step', 80)->nullable()->after('progress_percent');
        });
    }

    public function down(): void
    {
        Schema::table('update_logs', function (Blueprint $table): void {
            $table->dropColumn([
                'progress_percent',
                'current_step',
            ]);
        });
    }
};