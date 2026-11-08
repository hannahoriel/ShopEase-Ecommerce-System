<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeliveryHubCashInOtp extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $hubName,
        public readonly string $remittanceReference,
        public readonly string $amount,
        public readonly string $code,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'ShopEase Delivery Hub cash-in verification code');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.delivery-hub-cash-in-otp');
    }
}
