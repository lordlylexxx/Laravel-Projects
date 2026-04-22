<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantOnboardingSummaryMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $ownerName,
        public string $tenantName,
        public string $businessUrl,
        public string $tenantAdminEmail,
        public string $resetPasswordUrl,
        public string $reason,
        public string $changedBy
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tenant onboarding details resent'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-onboarding-summary'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
