<?php

namespace App\Mail;

use App\Models\Admin\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Registration $registration)
    {
    }

    public function build(): self
    {
        return $this->subject('Your ShopEase registration has been approved')
            ->view('email.registrations.approved', [
                'registration' => $this->registration,
            ]);
    }
}
