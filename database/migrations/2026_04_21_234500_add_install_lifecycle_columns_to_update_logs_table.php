<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('update_logs', function (Blueprint $table): void {
            $table->timestamp('install_started_at')->nullable()->after('installed_at');
            $table->timestamp('install_finished_at')->nullable()->after('install_started_at');
            $table->text('install_error')->nullable()->after('install_finished_at');
            $table->text('backup_path')->nullable()->after('install_error');
            $table->string('backup_version', 40)->nullable()->after('backup_path');
            $table->unsignedBigInteger('restored_from_update_log_id')->nullable()->after('backup_version');
            $table->timestamp('restored_at')->nullable()->after('restored_from_update_log_id');

            $table->index(['tenant_id', 'channel_status', 'checked_at'], 'update_logs_tenant_status_checked_idx');
        });
    }

    public function down(): void
    {
        Schema::table('update_logs', function (Blueprint $table): void {
            $table->dropIndex('update_logs_tenant_status_checked_idx');
            $table->dropColumn([
                'install_started_at',
                'install_finished_at',
                'install_error',
                'backup_path',
                'backup_version',
                'restored_from_update_log_id',
                'restored_at',
            ]);
        });
    }
};
