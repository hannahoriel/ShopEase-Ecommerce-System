<?php

namespace App\Services;

use App\Models\Logistics\LogisticsAccountSetting;
use App\Models\User;
use App\Notifications\LogisticsPreferenceNotification;

class LogisticsNotificationService
{
    private const DEFAULTS = [
        'parcel_updates' => true,
        'order_updates' => true,
        'messages' => true,
        'delivery_exceptions' => true,
        'compliance' => true,
        'email_alerts' => true,
        'announcements' => true,
        'promotions' => false,
    ];

    public function send(
        User $recipient,
        string $preference,
        string $title,
        string $message,
        ?string $url = null,
    ): bool {
        if ($recipient->role !== User::ROLE_LOGISTICS || ! array_key_exists($preference, self::DEFAULTS)) {
            return false;
        }

        $preferences = LogisticsAccountSetting::query()
            ->where('user_id', $recipient->id)
            ->first()?->notification_preferences ?? [];

        if (! (bool) ($preferences[$preference] ?? self::DEFAULTS[$preference])) {
            return false;
        }

        $recipient->notify(new LogisticsPreferenceNotification(
            $title,
            $message,
            $preference,
            $url ?? '/logistics/notifications',
        ));

        return true;
    }
}
