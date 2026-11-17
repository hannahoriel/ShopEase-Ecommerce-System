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

            $released = $this->settlement->releaseHeldReturnRefund($locked);
            $confirmed = $locked->fresh(['order.buyer', 'events']);
            if (! $released && $confirmed->order?->buyer) {
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
