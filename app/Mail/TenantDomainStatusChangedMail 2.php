<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantDomainStatusChangedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $tenantName,
        public string $ownerName,
        public string $businessUrl,
        public bool $enabled,
        public ?string $reason,
        public string $changedBy
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tenant domain status updated'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-domain-status-changed'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
