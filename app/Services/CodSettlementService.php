<?php

namespace App\Services;

use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Rider\Remittance;
use App\Models\Seller\Seller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CodSettlementService
{
    public const COMMISSION_RATE = 10.00;
    public const LOGISTICS_SHIPPING_FEE = 50.00;

    public function cashIn(Remittance $remittance, int $hubId, int $userId): array
    {
        return DB::transaction(function () use ($remittance, $hubId, $userId): array {
            $locked = Remittance::query()->lockForUpdate()->findOrFail($remittance->id);
            abort_unless((int) $locked->delivery_hub_id === $hubId, 404);
            abort_unless($locked->status === 'verified', 409, 'Only verified remittances can be cashed in.');
            abort_if((float) $locked->discrepancy_amount !== 0.0, 409, 'Resolve the cash discrepancy before cash-in.');

            $cashInId = DB::table('hub_cod_cash_ins')->insertGetId([
                'delivery_hub_id' => $hubId,
                'remittance_id' => $locked->id,
                'received_by' => $userId,
                'amount' => $locked->actual_amount,
                'reference' => 'HCI-'.str_pad((string) $locked->id, 8, '0', STR_PAD_LEFT),
                'received_at' => now(), 'created_at' => now(), 'updated_at' => now(),
            ]);

            $this->post('delivery_hub', $hubId, 'available', 'cash_in', 'hub_cash_in', $cashInId,
                (float) $locked->actual_amount, null, $locked->id, 'Verified rider COD cash-in');
            $locked->update(['status' => 'cashed_in', 'cash_in_at' => now(), 'cash_in_by' => $userId]);

            return ['id' => $cashInId, 'reference' => 'HCI-'.str_pad((string) $locked->id, 8, '0', STR_PAD_LEFT),
                'amount' => (float) $locked->actual_amount, 'remittance_id' => $locked->id];
        });
    }

    public function distribute(Remittance $remittance, int $hubId): array
    {
        return DB::transaction(function () use ($remittance, $hubId): array {
            $locked = Remittance::query()->lockForUpdate()->findOrFail($remittance->id);
            abort_unless((int) $locked->delivery_hub_id === $hubId, 404);
            abort_unless($locked->status === 'cashed_in', 409, 'Cash in this remittance before distributing it.');

            $cashIn = DB::table('hub_cod_cash_ins')->where('remittance_id', $locked->id)->lockForUpdate()->first();
            abort_unless($cashIn, 409, 'The verified cash-in record is missing.');
            $collections = $locked->collections()->with('order.logisticsCompany')->get();
            abort_if($collections->isEmpty(), 409, 'This remittance has no COD orders attached.');

            $expected = round((float) $collections->sum('amount'), 2);
            abort_if(abs($expected - (float) $cashIn->amount) > 0.009, 409, 'The cash-in total does not match the COD order total.');

            $distributed = 0.0;
            foreach ($collections as $collection) {
                $order = Order::query()->whereKey($collection->order_id)->lockForUpdate()->firstOrFail();
                abort_if(DB::table('cod_settlements')->where('order_id', $order->id)->exists(), 409,
                    'An order in this remittance has already been settled.');

                $cod = round((float) $collection->amount, 2);
                $shippingVoucher = $order->voucher_type === 'shipping' ? max(0, (float) $order->voucher_discount) : 0;
                $buyerShipping = min($cod, max(0, round((float) $order->shipping_fee - $shippingVoucher, 2)));
                $agreedShipping = min(max(0, round((float) $order->shipping_fee, 2)), self::LOGISTICS_SHIPPING_FEE);
                $shipping = $order->voucher_type === 'shipping' && $order->voucher_funding_source === 'logistics'
                    ? max(0, $agreedShipping - $shippingVoucher) : $agreedShipping;
                $shippingMargin = round(max(0, $buyerShipping - $agreedShipping), 2);
                $collectedProductAmount = max(0, round($cod - $buyerShipping, 2));
                $commissionBase = max(0, round($collectedProductAmount + ($order->voucher_type === 'shipping' ? 0 : (float) $order->voucher_discount), 2));
                $commission = min($collectedProductAmount, round($commissionBase * self::COMMISSION_RATE / 100, 2));
                $sellerShare = round($collectedProductAmount - $commission - ($order->voucher_type === 'shipping' && $order->voucher_funding_source === 'seller' ? $shippingVoucher : 0), 2);
                $logisticsId = $order->logistics_id ?: $order->logisticsCompany?->id;
                abort_if($shipping > 0 && ! $logisticsId, 409, 'The shipment has no logistics company assigned, so its shipping fee cannot be settled.');
                $settlementId = DB::table('cod_settlements')->insertGetId([
                    'order_id' => $order->id, 'remittance_id' => $locked->id,
                    'delivery_hub_id' => $hubId, 'logistics_id' => $logisticsId,
                    'cod_amount' => number_format($cod, 2, '.', ''),
                    'shipping_fee' => number_format($shipping, 2, '.', ''),
                    'platform_shipping_margin' => number_format($shippingMargin, 2, '.', ''),
                    'commission_base' => number_format($commissionBase, 2, '.', ''),
                    'commission_rate' => self::COMMISSION_RATE,
                    'commission_amount' => number_format($commission, 2, '.', ''),
                    'seller_amount' => number_format($sellerShare, 2, '.', ''),
                    'status' => 'held', 'distributed_at' => now(), 'created_at' => now(), 'updated_at' => now(),
                ]);

                $this->post('delivery_hub', $hubId, 'available', 'distribution', 'cod_settlement', $settlementId,
                    -$cod, $order->id, $locked->id, 'COD distributed from '.$order->order_number);
                if ($shipping > 0 && $logisticsId) {
                    $this->post('logistics', $logisticsId, 'available', 'shipping_fee', 'cod_settlement', $settlementId,
                        $shipping, $order->id, $locked->id, 'COD shipping fee for '.$order->order_number);
                }
                if ($shippingMargin > 0) {
                    $this->post('platform', null, 'available', 'shipping_margin', 'cod_settlement', $settlementId,
                        $shippingMargin, $order->id, $locked->id, 'Shipping fee balance for '.$order->order_number);
                }
                if ($commission > 0) {
                    $this->post('platform', null, 'available', 'commission', 'cod_settlement', $settlementId,
                        $commission, $order->id, $locked->id, '10% COD commission for '.$order->order_number);
                }
                if ($order->voucher_type === 'shipping' && $order->voucher_funding_source === 'platform' && $shippingVoucher > 0) {
                    $this->post('platform', null, 'available', 'shipping_voucher_subsidy', 'cod_settlement', $settlementId,
                        -$shippingVoucher, $order->id, $locked->id, 'ShopEase-funded shipping voucher '.$order->voucher_code);
                }
                if ($sellerShare > 0) {
                    $this->post('seller', $order->seller_id, 'on_hold', 'seller_hold', 'cod_settlement', $settlementId,
                        $sellerShare, $order->id, $locked->id, 'Held until buyer confirms '.$order->order_number);
                }

                // Receipt can be confirmed before the hub distributes this remittance.
                // Release immediately when the settlement is finally created.
                if ($order->status === 'completed' && $order->buyer_received_at) {
                    $this->releaseSellerEarnings((int) $order->id);
                }

                $distributed += $cod;
            }

            $locked->update(['status' => 'distributed']);

            return ['remittance_id' => $locked->id, 'orders' => $collections->count(), 'amount' => round($distributed, 2)];
        });
    }

    public function walletBalance(string $ownerType, ?int $ownerId, string $accountType): float
    {
        $key = $this->ownerKey($ownerType, $ownerId, $accountType);
        return (float) (DB::table('cod_wallet_accounts')->where('owner_key', $key)->value('balance') ?? 0);
    }

    public function releaseSellerEarnings(int $orderId): bool
    {
        return DB::transaction(function () use ($orderId): bool {
            $order = Order::query()->whereKey($orderId)->lockForUpdate()->first();
            if (! $order || $order->status !== 'completed' || ! $order->buyer_received_at) return false;

            $openComplaint = $order->complaints()->whereNotIn('status', ['resolved', 'closed', 'rejected', 'dismissed'])->exists();
            $openReturn = $order->returns()->whereNotIn('status', ['declined', 'rejected', 'refunded', 'resolved', 'closed'])->exists();
            if ($openComplaint || $openReturn || $order->returns()->where('status', 'refunded')->exists()) return false;

            $settlement = DB::table('cod_settlements')->where('order_id', $order->id)->lockForUpdate()->first();
            if (! $settlement || ! in_array($settlement->status, ['held', 'partially_refunded'], true)
                || $settlement->seller_released_at) return false;

            $alreadyReversed = abs((float) DB::table('cod_wallet_transactions as t')
                ->join('cod_wallet_accounts as a', 'a.id', '=', 't.wallet_account_id')
                ->where('a.owner_key', 'seller:'.$order->seller_id.':on_hold')
                ->where('t.entry_type', 'seller_refund_hold_reversal')->where('t.order_id', $order->id)->sum('t.amount'));
            $amount = max(0, round((float) $settlement->seller_amount - $alreadyReversed, 2));
            if ($amount > 0) {
                $this->post('seller', $order->seller_id, 'on_hold', 'seller_hold_release', 'cod_settlement', $settlement->id,
                    -$amount, (int) $order->id, (int) $settlement->remittance_id, 'Seller hold released for '.$order->order_number);
                $this->post('seller', $order->seller_id, 'available', 'seller_payout', 'cod_settlement', $settlement->id,
                    $amount, (int) $order->id, (int) $settlement->remittance_id, 'Buyer-confirmed COD earnings for '.$order->order_number);
            }
            DB::table('cod_settlements')->where('id', $settlement->id)->update([
                'status' => $alreadyReversed > 0 ? 'partially_refunded' : 'released',
                'seller_released_at' => now(), 'updated_at' => now(),
            ]);

            $seller = Seller::query()->find($order->seller_id);
            if ($seller) app(SellerWalletService::class)->recordOrder($seller, $order->fresh());
            return true;
        });
    }

    public function releaseEligibleSellerEarnings(): int
    {
        $released = 0;
        DB::table('cod_settlements')->whereIn('status', ['held', 'partially_refunded'])
            ->whereNull('seller_released_at')->orderBy('id')->chunkById(100, function ($settlements) use (&$released): void {
            foreach ($settlements as $settlement) {
                if ($this->releaseSellerEarnings((int) $settlement->order_id)) $released++;
            }
        });
        return $released;
    }

    public function reverseSellerEarningsForRefund(OrderReturn $return): void
    {
        DB::transaction(function () use ($return): void {
            $order = Order::query()->whereKey($return->order_id)->lockForUpdate()->first();
            if (! $order) return;
            $settlement = DB::table('cod_settlements')->where('order_id', $order->id)->lockForUpdate()->first();
            if (! $settlement || ! in_array($settlement->status, ['held', 'released', 'partially_refunded'], true)) return;

            $refund = max(0, round((float) ($return->refund_amount ?? 0), 2));
            $released = ! is_null($settlement->seller_released_at);
            $accountType = $released ? 'available' : 'on_hold';
            $reversalEntryType = $released ? 'seller_refund_reversal' : 'seller_refund_hold_reversal';
            $alreadyReversed = abs((float) DB::table('cod_wallet_transactions as t')
                ->join('cod_wallet_accounts as a', 'a.id', '=', 't.wallet_account_id')
                ->where('a.owner_key', 'seller:'.$order->seller_id.':'.$accountType)
                ->where('t.entry_type', $reversalEntryType)->where('t.order_id', $order->id)->sum('t.amount'));
            $remainingSellerAmount = max(0, round((float) $settlement->seller_amount - $alreadyReversed, 2));
            $reversal = min($refund, $remainingSellerAmount);
            if ($reversal > 0) {
                $this->post('seller', (int) $order->seller_id, $accountType, $reversalEntryType, 'order_return', (int) $return->id,
                    -$reversal, (int) $order->id, (int) $settlement->remittance_id,
                    'Seller earnings reversed for refund '.$order->order_number);
            }
            DB::table('cod_settlements')->where('id', $settlement->id)->update([
                'status' => ($alreadyReversed + $reversal) >= (float) $settlement->seller_amount ? 'refunded' : 'partially_refunded',
                'updated_at' => now(),
            ]);
        });
    }

    private function post(string $ownerType, ?int $ownerId, string $accountType, string $entryType,
        string $sourceType, int $sourceId, float $amount, ?int $orderId, ?int $referenceId, string $description): void
    {
        $ownerKey = $this->ownerKey($ownerType, $ownerId, $accountType);
        DB::table('cod_wallet_accounts')->insertOrIgnore([
            'owner_key' => $ownerKey, 'owner_type' => $ownerType, 'owner_id' => $ownerId,
            'account_type' => $accountType, 'balance' => '0.00', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $wallet = DB::table('cod_wallet_accounts')->where('owner_key', $ownerKey)->lockForUpdate()->first();
        $existing = DB::table('cod_wallet_transactions')->where('wallet_account_id', $wallet->id)
            ->where('entry_type', $entryType)->where('source_type', $sourceType)->where('source_id', $sourceId)->exists();
        if ($existing) {
            return;
        }

        $next = round((float) $wallet->balance + $amount, 2);
        if ($next < -0.009) {
            throw ValidationException::withMessages(['amount' => ['The wallet does not have enough available COD funds.']]);
        }
        DB::table('cod_wallet_accounts')->where('id', $wallet->id)->update([
            'balance' => number_format(max(0, $next), 2, '.', ''), 'updated_at' => now(),
        ]);
        DB::table('cod_wallet_transactions')->insert([
            'wallet_account_id' => $wallet->id, 'entry_type' => $entryType,
            'source_type' => $sourceType, 'source_id' => $sourceId, 'order_id' => $orderId,
            'amount' => number_format($amount, 2, '.', ''), 'balance_after' => number_format(max(0, $next), 2, '.', ''),
            'reference' => $referenceId ? 'COD-'.$referenceId : null, 'description' => $description,
            'occurred_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function ownerKey(string $ownerType, ?int $ownerId, string $accountType): string
    {
        return $ownerType.':'.($ownerId ?? 'platform').':'.$accountType;
    }
}
