<?php

namespace App\Services;

use App\Models\Buyer\BuyerNotificationPreference;
use App\Models\User;

class BuyerNotificationPreferences
{
    public const DEFAULTS = [
        'order_updates' => true,
        'delivery_alerts' => true,
        'returns_refunds' => true,
        'complaints' => true,
        'messages' => true,
        'email_alerts' => true,
        'vouchers' => true,
        'announcements' => true,
        'recommendations' => false,
    ];

    public function forUser(User $user): array
    {
        $stored = BuyerNotificationPreference::query()->where('user_id', $user->id)->first()?->preferences ?? [];

        return array_replace(self::DEFAULTS, array_intersect_key(
            is_array($stored) ? $stored : [],
            self::DEFAULTS,
        ));
    }

    public function preferenceFor(string $type, array $data = []): ?string
    {
        $type = strtolower($type);
        $status = strtolower((string) ($data['status'] ?? ''));

        if (str_contains($type, 'recommendation')) return 'recommendations';
        if (str_contains($type, 'voucher') || str_contains($type, 'promotion') || str_contains($type, 'coupon')) return 'vouchers';
        if (str_contains($type, 'complaint') || str_contains($type, 'dispute')) return 'complaints';
        if (str_contains($type, 'message') || str_contains($type, 'chat')) return 'messages';
        if (str_contains($type, 'return') || str_contains($type, 'refund')) return 'returns_refunds';
        if ($status === 'out_for_delivery' || str_contains($type, 'delivery_reminder') || str_contains($type, 'delivery_attempt')) return 'delivery_alerts';
        if (str_contains($type, 'announcement') || str_contains($type, 'policy')) return 'announcements';
        if (in_array($type, ['order', 'shipping', 'delivered', 'completed', 'cancellation_rejected'], true)
            || in_array($status, ['pending', 'new', 'preparing', 'to_ship', 'in_transit', 'delivered', 'completed', 'cancelled'], true)) {
            return 'order_updates';
        }

        return null;
    }

    public function allows(User $user, string $type, array $data = []): bool
    {
        $preference = $this->preferenceFor($type, $data);

        return $preference === null || ($this->forUser($user)[$preference] ?? true);
    }

    public function save(User $user, array $preferences): array
    {
        $saved = array_intersect_key($preferences, self::DEFAULTS);
        BuyerNotificationPreference::query()->updateOrCreate(
            ['user_id' => $user->id],
            ['preferences' => $saved],
        );

        return array_replace(self::DEFAULTS, $saved);
    }
}
