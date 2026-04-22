<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantSubscriptionChangedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $tenantName,
        public string $ownerName,
        public string $plan,
        public string $subscriptionStatus,
        public mixed $periodEndsAt,
        public ?string $reason,
        public string $changedBy
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tenant subscription updated'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-subscription-changed'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
