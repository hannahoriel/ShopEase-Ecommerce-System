<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BuyerVerificationCode extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code)
    {
    }

    public function build(): self
    {
        return $this->subject('Your ShopEase buyer verification code')
            ->view('emails.buyer.verification-code');
    }
}
