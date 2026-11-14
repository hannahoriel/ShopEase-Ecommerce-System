<?php

namespace App\Services;

use App\Models\Admin\Complaint;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;

class SellerComplianceService
{
    public const AUTOMATIC_SUSPENSION_REASON = 'seller_compliance_score';
    public const SUSPENSION_THRESHOLD = 75.0;
    public const AUTOMATIC_SUSPENSION_DAYS = 3;

    public static function scoreFromCounts(int $reviewCount, int $atFaultComplaintCount): float
    {
        return max(0, 100 - ($reviewCount * 5) - ($atFaultComplaintCount * 0.5));
    }

    public function scoreFor(Seller $seller): float
    {
        $reviewCount = Product::query()
            ->where('seller_id', $seller->id)
            ->where('is_archived', false)
            ->whereRaw('LOWER(status) IN (?, ?, ?)', ['pending', 'under_review', 'review'])
            ->count();

        $atFaultComplaintCount = Complaint::query()
            ->where('seller_at_fault', true)
            ->whereHas('order', fn ($query) => $query->where('seller_id', $seller->id))
            ->count();

        return self::scoreFromCounts($reviewCount, $atFaultComplaintCount);
    }

    public function applyAutomaticSuspension(Seller $seller, float $score): array
    {
        $lockedSeller = Seller::query()->whereKey($seller->id)->lockForUpdate()->firstOrFail();
        $user = User::query()->whereKey($lockedSeller->user_id)->lockForUpdate()->first();

        if (! $user) {
            return ['suspended' => false, 'reactivated' => false, 'suspended_until' => null];
        }

        if ($score < self::SUSPENSION_THRESHOLD
            && $lockedSeller->registration_status === 'active'
            && $user->registration_status === 'active') {
            $suspendedUntil = now()->addDays(self::AUTOMATIC_SUSPENSION_DAYS);
            $details = 'Automatically suspended because the seller compliance score fell below 75%.';

            $lockedSeller->forceFill(['registration_status' => 'suspended'])->save();
            $user->forceFill([
                'registration_status' => 'suspended',
                'suspended_until' => $suspendedUntil,
                'account_action_reason' => self::AUTOMATIC_SUSPENSION_REASON,
                'account_action_details' => $details,
            ])->save();

            return [
                'suspended' => true,
                'reactivated' => false,
                'suspended_until' => $suspendedUntil,
            ];
        }

        if ($score >= self::SUSPENSION_THRESHOLD
            && $lockedSeller->registration_status === 'suspended'
            && $user->account_action_reason === self::AUTOMATIC_SUSPENSION_REASON) {
            $lockedSeller->forceFill(['registration_status' => 'active'])->save();
            $user->forceFill([
                'registration_status' => 'active',
                'suspended_until' => null,
                'account_action_reason' => null,
                'account_action_details' => null,
            ])->save();

            return ['suspended' => false, 'reactivated' => true, 'suspended_until' => null];
        }

        return [
            'suspended' => false,
            'reactivated' => false,
            'suspended_until' => $user->suspended_until,
        ];
    }
}
