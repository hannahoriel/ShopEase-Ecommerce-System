<?php

namespace App\Services;

use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\User;

class AccountLoginEligibility
{
    private const DISABLED_STATUSES = ['suspended', 'deactivated', 'disabled', 'inactive'];

    public function disabledReason(User $user): ?string
    {
        if ($user->role === User::ROLE_ADMIN) {
            return null;
        }

        if (in_array(mb_strtolower((string) $user->registration_status), self::DISABLED_STATUSES, true)) {
            return 'This account is disabled. Contact the administrator for assistance.';
        }

        if ($user->role !== User::ROLE_DELIVERY_HUB) {
            return null;
        }

        $hub = LogisticsDeliveryHub::query()
            ->with(['logisticsBranch.logistics.user'])
            ->where('user_id', $user->id)
            ->first();

        // Some provisioned delivery-hub accounts are created before their
        // facility profile is attached. Preserve first-login password setup
        // for those accounts; when a facility exists, its active state and
        // its parent network determine whether it is disabled.
        if (! $hub) {
            return null;
        }

        $branch = $hub->logisticsBranch;
        $company = $branch?->logistics;

        if ($hub->status !== 'active'
            || $hub->archived_at !== null
            || ! $branch
            || $branch->status !== 'active'
            || $branch->archived_at !== null
            || ! $company
            || $company->registration_status !== 'active'
            || $company->user?->registration_status !== 'active') {
            return 'This delivery hub is disabled. Contact the logistics administrator for assistance.';
        }

        return null;
    }
}
