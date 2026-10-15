<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class SellerNotificationPreferences
{
    private const DEFAULTS = [
        'new_orders' => true,
        'order_updates' => true,
        'messages' => true,
        'reviews' => true,
        'compliance' => true,
        'email_alerts' => true,
        'announcements' => true,
        'promotions' => false,
    ];

    private const CATEGORIES = [
        'new_order' => 'new_orders',
        'shipping' => 'order_updates',
        'delivery' => 'order_updates',
        'cancellation' => 'order_updates',
        'cancelled' => 'order_updates',
        'return' => 'order_updates',
        'order_update' => 'order_updates',
        'update' => 'order_updates',
        'order' => 'new_orders',
        'message' => 'messages',
        'conversation' => 'messages',
        'review' => 'reviews',
        'rating' => 'reviews',
        'feedback' => 'reviews',
        'compliance' => 'compliance',
        'permit' => 'compliance',
        'approval' => 'compliance',
        'category' => 'compliance',
        'document' => 'compliance',
        'announcement' => 'announcements',
        'promotion' => 'promotions',
        'marketing' => 'promotions',
    ];

    public function forUser(User $user): array
    {
        $stored = DB::table('seller_notification_preferences')
            ->where('user_id', $user->id)
            ->value('preferences');

        if (! is_string($stored)) {
            return self::DEFAULTS;
        }

        $preferences = json_decode($stored, true);
        if (! is_array($preferences)) {
            return self::DEFAULTS;
        }

        foreach (self::DEFAULTS as $key => $default) {
            if (! is_bool($preferences[$key] ?? null)) {
                $preferences[$key] = $default;
            }
        }

        return array_intersect_key($preferences, self::DEFAULTS);
    }

    public function allows(User $user, Notification $notification): bool
    {
        if ($user->role !== User::ROLE_SELLER) {
            return true;
        }

        $data = method_exists($notification, 'toDatabase')
            ? $notification->toDatabase($user)
            : $notification->toArray($user);

        $data = is_array($data) ? $data : [];
        $preference = $this->preferenceFor($data);

        return $preference === null || $this->forUser($user)[$preference];
    }

    private function preferenceFor(array $data): ?string
    {
        $explicitPreference = $data['preference'] ?? null;
        if (is_string($explicitPreference) && array_key_exists($explicitPreference, self::DEFAULTS)) {
            return $explicitPreference;
        }

        $tokens = [
            $data['type'] ?? '',
            $data['category'] ?? '',
        ];

        foreach ($tokens as $token) {
            if (! is_string($token)) {
                continue;
            }

            $normalized = strtolower($token);
            foreach (self::CATEGORIES as $needle => $preference) {
                if (str_contains($normalized, $needle)) {
                    return $preference;
                }
            }
        }

        return null;
    }
}
