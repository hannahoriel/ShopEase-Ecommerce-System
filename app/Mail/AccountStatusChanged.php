<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $status,
        public ?string $reason,
        public ?string $details,
        public ?int $duration,
    ) {
    }

    public function build(): self
    {
        return $this->subject('Your ShopEase account has been ' . $this->status)
            ->markdown('emails.account-status-changed', [
                'user' => $this->user,
                'status' => $this->status,
                'reason' => $this->reason,
                'details' => $this->details,
                'duration' => $this->duration,
            ]);
    }
}
