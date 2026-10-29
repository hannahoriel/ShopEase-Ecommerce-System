<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeliveryHubCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $hubName,
        public readonly string $email,
        public readonly string $password,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your ShopEase Delivery Hub Login Credentials');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.delivery-hub-credentials');
    }
}
