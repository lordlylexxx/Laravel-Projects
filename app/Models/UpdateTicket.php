<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class UpdateTicket extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_RESOLVED = 'resolved';

    protected $connection = 'landlord';

    protected $fillable = [
        'tenant_id',
        'reporter_landlord_user_id',
        'reporter_role',
        'reporter_name',
        'reporter_email',
        'subject',
        'body',
        'attachment_path',
        'status',
        'resolution_notes',
        'reopen_note',
        'resolved_at',
        'resolved_by_landlord_user_id',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        $path = (string) ($this->attachment_path ?? '');

        if ($path === '') {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
