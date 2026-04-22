<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdateLog extends Model
{
    use HasFactory;

    protected $connection = 'landlord';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'current_version',
        'latest_version',
        'release_notes',
        'download_url',
        'checksum_url',
        'download_checksum',
        'download_checksum_verified_at',
        'channel_status',
        'progress_percent',
        'current_step',
        'status_message',
        'checked_at',
        'installed_at',
        'install_started_at',
        'install_finished_at',
        'install_error',
        'backup_path',
        'backup_version',
        'app_key_backup_path',
        'app_key_rotated_at',
        'restored_from_update_log_id',
        'restored_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_at' => 'datetime',
            'installed_at' => 'datetime',
            'install_started_at' => 'datetime',
            'install_finished_at' => 'datetime',
            'restored_at' => 'datetime',
            'progress_percent' => 'integer',
            'download_checksum_verified_at' => 'datetime',
            'app_key_rotated_at' => 'datetime',
        ];
    }
}
