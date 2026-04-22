<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('update_logs', function (Blueprint $table): void {
            $table->text('checksum_url')->nullable()->after('download_url');
            $table->text('download_checksum')->nullable()->after('checksum_url');
            $table->timestamp('download_checksum_verified_at')->nullable()->after('download_checksum');
            $table->text('app_key_backup_path')->nullable()->after('download_checksum_verified_at');
            $table->timestamp('app_key_rotated_at')->nullable()->after('app_key_backup_path');
        });
    }

    public function down(): void
    {
        Schema::table('update_logs', function (Blueprint $table): void {
            $table->dropColumn([
                'checksum_url',
                'download_checksum',
                'download_checksum_verified_at',
                'app_key_backup_path',
                'app_key_rotated_at',
            ]);
        });
    }
};