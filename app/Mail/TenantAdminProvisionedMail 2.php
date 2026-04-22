<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantAdminProvisionedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $ownerName,
        public string $businessName,
        public string $businessUrl,
        public string $adminEmail,
        public string $adminPassword
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your business admin account is ready'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-admin-provisioned'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
