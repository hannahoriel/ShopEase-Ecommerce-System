<?php

namespace App\Services\Logistics;

use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\Shipment;
use App\Models\Buyer\BuyerNotification;
use App\Notifications\SellerPreferenceNotification;
use App\Services\BuyerWalletService;
use Illuminate\Support\Facades\DB;
class CancelOrderForParcelIssue
{
    /** Cancel an active buyer order and retain the reported issue as its reason. */
    public function handle(
        Shipment $shipment,
        int $reportedBy,
        string $issueType,
        string $description,
    ): array
    {
        $order = $shipment->order;
        if (! $order) {
            return ['cancelled' => false, 'reimbursed' => false];
        }

        $order = $order->newQuery()->whereKey($order->id)->lockForUpdate()->first();
        if (! $order || in_array($order->status, ['refunded', 'delivered', 'completed'], true)) {
            return ['cancelled' => false, 'reimbursed' => false];
        }

        $cancelled = false;
        if ($order->status !== 'cancelled') {
            $previousStatus = $order->status;
            $reason = 'Automatically cancelled after a parcel issue was reported. Reason: '.$issueType.' — '.trim($description);
            $order->forceFill([
                'status' => 'cancelled',
                'pickup_date' => null,
                'pickup_time' => null,
            ])->save();

            OrderStatusHistory::query()->create([
                'order_id' => $order->id,
                'from_status' => $previousStatus,
                'to_status' => 'cancelled',
                'changed_by' => $reportedBy,
                'notes' => $reason,
            ]);

            BuyerNotification::createForOrder(
                $order,
                'order',
                'Order cancelled due to a parcel issue',
                'Your order '.$order->order_number.' was cancelled. Reason: '.$issueType.'. '.$description,
                ['status' => 'cancelled', 'cancellation_reason' => $issueType.'. '.trim($description)],
            );
            if ($order->payment_method === 'ShopEase Wallet' && (float) $order->total > 0) {
                app(BuyerWalletService::class)->move(
                    (int) $order->buyer_id,
                    'payment_reversal',
                    $order->total,
                    'order',
                    (int) $order->id,
                    'Logistics cancelled order '.$order->order_number.' due to '.$issueType,
                );
            }
            $cancelled = true;
        }

        $seller = $order->seller()->with('user')->first();
        $amount = round((float) $order->total, 2);
        $reimbursed = false;
        if ($seller && $amount > 0) {
            $credited = DB::table('seller_wallet_transactions')->insertOrIgnore([
                'seller_id' => $seller->id,
                'entry_type' => 'logistics_reimbursement',
                'source_type' => 'parcel_issue_reimbursement',
                'source_id' => $order->id,
                'amount' => number_format($amount, 2, '.', ''),
                'reference' => $order->order_number,
                'description' => 'Logistics reimbursement for '.$issueType.' parcel · '.$order->order_number.' · Tracking '.$shipment->tracking_number,
                'occurred_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $reimbursed = $credited > 0 || DB::table('seller_wallet_transactions')
                ->where('seller_id', $seller->id)
                ->where('entry_type', 'logistics_reimbursement')
                ->where('source_type', 'parcel_issue_reimbursement')
                ->where('source_id', $order->id)
                ->exists();

            if ($credited > 0 && $seller->user) {
                $seller->user->notify(new SellerPreferenceNotification(
                    'Logistics reimbursement credited',
                    'A ₱'.number_format($amount, 2).' reimbursement for '.$issueType.' parcel '.$order->order_number.' has been credited to your seller wallet. Report details: '.trim($description),
                    'order_activity',
                    '/seller/wallet?search='.rawurlencode($order->order_number),
                ));
            }
        }

        return ['cancelled' => $cancelled, 'reimbursed' => $reimbursed];
    }
}
