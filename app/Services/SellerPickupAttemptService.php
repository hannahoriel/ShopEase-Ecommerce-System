<?php

namespace App\Services;

use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\SellerPickupAttempt;
use App\Models\Admin\Shipment;
use App\Models\Buyer\BuyerNotification;
use App\Models\Rider\EarningEntry;
use Illuminate\Support\Facades\DB;

class SellerPickupAttemptService
{
    public const MAX_ATTEMPTS = 3;
    public const RIDER_NO_SHOW_DEDUCTION = 20.00;
    public const CUTOFF_TIME = '18:00';

    public function __construct(
        private readonly OrderInventoryService $inventory,
        private readonly BuyerWalletService $buyerWallet,
    ) {}

    public function schedule(Order $order, Shipment $shipment): SellerPickupAttempt
    {
        $attemptNumber = (int) $order->sellerPickupAttempts()->max('attempt_number') + 1;

        return $order->sellerPickupAttempts()->create([
            'rider_id' => $shipment->rider_id,
            'attempt_number' => $attemptNumber,
            'pickup_date' => $order->pickup_date,
            'pickup_time' => $order->pickup_time,
            'status' => 'scheduled',
        ]);
    }

    public function processMissedPickups(): array
    {
        $now = now('Asia/Manila');
        if ($now->format('H:i') < self::CUTOFF_TIME) {
            return ['missed' => 0, 'cancelled' => 0, 'deducted' => 0];
        }

        $counts = ['missed' => 0, 'cancelled' => 0, 'deducted' => 0];
        SellerPickupAttempt::query()
            ->where('status', 'scheduled')
            ->whereDate('pickup_date', '<=', $now->toDateString())
            ->orderBy('id')
            ->chunkById(100, function ($attempts) use ($now, &$counts): void {
                foreach ($attempts as $attempt) {
                    DB::transaction(function () use ($attempt, $now, &$counts): void {
                        $lockedAttempt = SellerPickupAttempt::query()
                            ->whereKey($attempt->id)
                            ->lockForUpdate()
                            ->first();
                        if (! $lockedAttempt || $lockedAttempt->status !== 'scheduled') {
                            return;
                        }

                        $order = Order::query()->whereKey($lockedAttempt->order_id)->lockForUpdate()->first();
                        if (! $order || $order->status !== 'to_ship') {
                            $lockedAttempt->update(['status' => 'cancelled']);
                            return;
                        }

                        if (
                            $order->pickup_date?->toDateString() !== $lockedAttempt->pickup_date->toDateString()
                            || $order->pickup_time?->format('H:i') !== $lockedAttempt->pickup_time->format('H:i')
                        ) {
                            $lockedAttempt->update(['status' => 'cancelled']);
                            return;
                        }

                        $shipment = Shipment::query()
                            ->where('order_id', $order->id)
                            ->lockForUpdate()
                            ->first();
                        if ($shipment?->picked_up_at) {
                            $lockedAttempt->update([
                                'status' => 'picked_up',
                                'picked_up_at' => $shipment->picked_up_at,
                                'rider_id' => $shipment->rider_id ?: $lockedAttempt->rider_id,
                            ]);
                            return;
                        }

                        $lockedAttempt->update(['status' => 'missed', 'missed_at' => $now]);
                        $counts['missed']++;

                        if ($lockedAttempt->attempt_number < self::MAX_ATTEMPTS) {
                            $order->update(['pickup_date' => null, 'pickup_time' => null]);
                            return;
                        }

                        $previousStatus = $order->status;
                        $order->update([
                            'status' => 'cancelled',
                            'pickup_date' => null,
                            'pickup_time' => null,
                        ]);
                        $this->inventory->restoreOrder($order);
                        $shipment?->update(['rider_id' => null]);
                        if ($order->payment_method === 'ShopEase Wallet' && (float) $order->total > 0) {
                            $this->buyerWallet->move(
                                (int) $order->buyer_id,
                                'payment_reversal',
                                $order->total,
                                'order',
                                (int) $order->id,
                                'Cancelled order '.$order->order_number.' because the pickup rider did not collect it.',
                            );
                        }

                        OrderStatusHistory::query()->create([
                            'order_id' => $order->id,
                            'from_status' => $previousStatus,
                            'to_status' => 'cancelled',
                            'changed_by' => null,
                            'notes' => 'Automatically cancelled after the pickup rider did not collect the order for three seller-scheduled attempts.',
                        ]);
                        BuyerNotification::createForOrder(
                            $order,
                            'order',
                            'Order cancelled: pickup rider did not collect it',
                            'Your order '.$order->order_number.' was cancelled because the pickup rider did not collect it after three scheduled pickup attempts.',
                            [
                                'status' => 'cancelled',
                                'cancellation_reason' => 'Pickup rider did not collect the order after three scheduled attempts.',
                                'pickup_attempts' => self::MAX_ATTEMPTS,
                            ],
                        );

                        if ($lockedAttempt->rider_id) {
                            $existingDeduction = EarningEntry::query()
                                ->where('rider_id', $lockedAttempt->rider_id)
                                ->where('order_id', $order->id)
                                ->where('type', 'pickup_no_show_deduction')
                                ->exists();
                            if (! $existingDeduction) {
                                EarningEntry::query()->create([
                                    'rider_id' => $lockedAttempt->rider_id,
                                    'order_id' => $order->id,
                                    'posted_by' => null,
                                    'type' => 'pickup_no_show_deduction',
                                    'description' => 'Earning deduction: pickup not completed after three scheduled attempts for order '.$order->order_number,
                                    'amount' => -self::RIDER_NO_SHOW_DEDUCTION,
                                    'status' => 'posted',
                                    'posted_at' => $now,
                                ]);
                                $counts['deducted']++;
                            }
                        }

                        $counts['cancelled']++;
                    }, 3);
                }
            });

        return $counts;
    }
}
