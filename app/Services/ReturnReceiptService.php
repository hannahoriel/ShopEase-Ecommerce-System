<?php

namespace App\Services;

use App\Models\Admin\OrderReturn;
use App\Models\Buyer\BuyerNotification;
use Illuminate\Support\Facades\DB;

class ReturnReceiptService
{
    public function __construct(private CodSettlementService $settlement) {}

    public function confirm(OrderReturn $return, ?string $sellerName = null): ?OrderReturn
    {
        return DB::transaction(function () use ($return, $sellerName) {
            $locked = OrderReturn::query()->lockForUpdate()->findOrFail($return->id);
            if ($locked->return_type !== 'return' || ! in_array($locked->shipping_status, ['delivered', 'returned'], true)) {
                return null;
            }
            if ($locked->returned_at) {
                return $locked;
            }
            if ($locked->status !== 'accepted') {
                return null;
            }

            $deadline = $locked->events()->where('status', 'delivered')->first()?->scanned_at?->addHours(72);
            $expired = $deadline && ! $deadline->isFuture();
            if ($sellerName === null && ! $expired) {
                return null;
            }

            $locked->forceFill(['returned_at' => $expired ? $deadline : now()])->save();
            $locked->events()->create([
                'status' => $expired ? 'seller_receipt_auto_confirmed' : 'seller_confirmed_received',
                'location' => $expired ? 'Receipt automatically confirmed 3 days after delivery.' : 'Receipt confirmed by '.$sellerName,
                'scanned_at' => now(),
            ]);

            $locked->loadMissing('order.seller');
            if ($locked->order?->seller) {
                app(SellerWalletService::class)->recordReturnShipping($locked->order->seller, $locked);
            }

            $released = $this->settlement->releaseHeldReturnRefund($locked);

            // A seller's return receipt confirmation also completes the buyer
            // refund when the refund is not waiting on rider COD remittance.
            if (! $released && ! DB::table('rider_cod_collections')->where('order_return_id', $locked->id)->exists()) {
                $locked->loadMissing('order.seller', 'order.buyer');
                $paidMerchandise = max(0, round((float) $locked->order->total - (float) $locked->order->shipping_fee, 2));
                $alreadyRefunded = (float) OrderReturn::query()->where('order_id', $locked->order_id)
                    ->whereNotNull('refunded_at')->whereKeyNot($locked->id)->sum('refund_amount');
                abort_if((float) $locked->refund_amount <= 0
                    || round($alreadyRefunded + (float) $locked->refund_amount, 2) > $paidMerchandise,
                    422, 'Refund exceeds the remaining paid merchandise amount.');

                $locked->forceFill(['status' => 'refunded', 'refunded_at' => now()])->save();
                app(BuyerWalletService::class)->move(
                    (int) $locked->order->buyer_id,
                    'refund_credit',
                    $locked->refund_amount,
                    'order_return',
                    (int) $locked->id,
                    'Return & Refund for order '.$locked->order->order_number,
                );
                $this->settlement->reverseSellerEarningsForRefund($locked);
                app(SellerWalletService::class)->recordRefund($locked->order->seller, $locked);
                $locked->events()->create([
                    'status' => 'refunded',
                    'location' => 'Return receipt confirmed and refund completed',
                    'scanned_at' => now(),
                ]);
                BuyerNotification::createForBuyer(
                    $locked->order->buyer,
                    'refund_update',
                    'Return refund sent to your wallet',
                    'Receipt of your returned product was confirmed. ₱'.number_format((float) $locked->refund_amount, 2).' was credited to your ShopEase Wallet.',
                    ['return_id' => $locked->id, 'status' => 'refunded', 'url' => '/buyer/my-purchases?tab=returns'],
                    $locked->order,
                );
            }
            $confirmed = $locked->fresh(['order.buyer', 'events']);
            if (! $released && $confirmed->status !== 'refunded' && $confirmed->order?->buyer) {
                BuyerNotification::createForBuyer(
                    $confirmed->order->buyer,
                    'return_update',
                    'Returned parcel received',
                    'Receipt of your returned parcel is confirmed. Your refund can now proceed.',
                    ['return_id' => $confirmed->id, 'status' => $confirmed->status, 'shipping_status' => $confirmed->shipping_status, 'url' => '/buyer/my-purchases?tab=returns'],
                    $confirmed->order,
                );
            }

            return $confirmed;
        }, 3);
    }
}
