<?php

namespace App\Mail;

use App\Models\Admin\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Registration $registration)
    {
    }

    public function build(): self
    {
        return $this->subject('Update on your ShopEase registration')
            ->markdown('emails.registrations.rejected', [
                'registration' => $this->registration,
            ]);
    }
}
