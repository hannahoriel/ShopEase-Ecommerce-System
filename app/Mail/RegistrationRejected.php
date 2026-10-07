<?php

namespace App\Mail;

use App\Models\Admin\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Registration $registration) {}

    public function build(): self
    {
        return $this->subject('An update to your ShopEase registration')
            ->view('email.registrations.rejected', [
                'registration' => $this->registration,
            ]);
    }
}
