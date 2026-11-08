<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SellerPreferenceNotification;

class SellerNotificationService
{
    public function send(
        User $recipient,
        string $preference,
        string $title,
        string $message,
        string $url,
    ): bool {
        if ($recipient->role !== User::ROLE_SELLER) {
            return false;
        }

        return app(SellerNotificationPreferences::class)->notify(
            $recipient,
            new SellerPreferenceNotification($title, $message, $preference, $url),
        );
    }
}
