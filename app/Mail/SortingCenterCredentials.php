<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SortingCenterCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $branchName,
        public readonly string $email,
        public readonly string $password,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your ShopEase Sorting Center Login Credentials');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.sorting-center-credentials');
    }
}
