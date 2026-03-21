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
        'channel_status',
        'status_message',
        'checked_at',
        'installed_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_at' => 'datetime',
            'installed_at' => 'datetime',
        ];
    }
}
