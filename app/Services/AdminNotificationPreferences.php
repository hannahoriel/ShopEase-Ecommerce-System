<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class AdminNotificationPreferences
{
    private const MODULE_CATEGORIES = [
        'Registrations' => 'registrations',
        'Seller Compliance' => 'seller_compliance',
        'Complaints and Disputes' => 'complaints',
        'Commission' => 'commissions',
        'Logistics Management' => 'logistics',
        'Messages' => 'messages',
        'Platform Settings' => 'platform',
        'Reports' => 'reports',
    ];

    private const CATEGORIES = [
        'registration' => 'registrations',
        'seller_compliance' => 'seller_compliance',
        'compliance' => 'seller_compliance',
        'category' => 'seller_compliance',
        'document' => 'seller_compliance',
        'permit' => 'seller_compliance',
        'complaint' => 'complaints',
        'dispute' => 'complaints',
        'commission' => 'commissions',
        'logistic' => 'logistics',
        'delivery' => 'logistics',
        'message' => 'messages',
        'conversation' => 'messages',
        'platform' => 'platform',
        'announcement' => 'platform',
        'report' => 'reports',
    ];

    public function allows(User $user, Notification $notification): bool
    {
        if ($user->role !== User::ROLE_ADMIN) {
            return true;
        }

        $data = method_exists($notification, 'toDatabase')
            ? $notification->toDatabase($user)
            : $notification->toArray($user);

        if (! is_array($data)) {
            return true;
        }

        $preference = $this->preferenceFor($data);
        if ($preference === null) {
            return true;
        }

        $profile = DB::table('admin_account_profiles')
            ->where('user_id', $user->id)
            ->first(['preferences', 'permissions']);
        $preferences = is_string($profile?->preferences ?? null)
            ? json_decode($profile->preferences, true)
            : null;

        if (! $this->isCategoryAssigned($profile, $preference)) {
            return false;
        }

        return ! is_array($preferences) || ! array_key_exists($preference, $preferences)
            ? true
            : (bool) $preferences[$preference];
    }

    private function isCategoryAssigned(?object $profile, string $preference): bool
    {
        if (! $profile || $profile->permissions === null) {
            // Legacy/full admin profiles have no module restriction.
            return true;
        }

        $permissions = is_string($profile->permissions)
            ? json_decode($profile->permissions, true)
            : null;
        if (! is_array($permissions)) {
            return true;
        }

        $assignedCategories = array_values(array_filter(array_map(
            fn ($module) => self::MODULE_CATEGORIES[$module] ?? null,
            $permissions,
        )));

        return in_array($preference, $assignedCategories, true);
    }

    private function preferenceFor(array $data): ?string
    {
        $explicitPreference = $data['preference'] ?? null;
        if (is_string($explicitPreference) && in_array($explicitPreference, self::CATEGORIES, true)) {
            return $explicitPreference;
        }

        foreach (['type', 'category'] as $key) {
            $token = $data[$key] ?? null;
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
