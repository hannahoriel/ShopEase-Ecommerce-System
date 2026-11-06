<?php

namespace App\Services;

use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Seller\Seller;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Idempotent accounting projection of completed orders and completed returns.
 * Run this on wallet reads. New completed/refunded events are captured even if
 * their status changed from another page or a background workflow.
 */
class SellerWalletService
{
    private const RETURN_SHIPPING_CHARGE = 50.00;

    public function sync(Seller $seller): void
    {
        Order::query()->where('seller_id', $seller->id)
            ->where(function ($q) {
                $q->where('status', 'completed')->orWhereHas('statusHistory', fn ($history) => $history->where('to_status', 'completed'));
            })->orderBy('id')->chunkById(200, function ($orders) use ($seller) {
                foreach ($orders as $order) {
                    $this->recordOrder($seller, $order);
                }
            });

        OrderReturn::query()->whereHas('order', fn ($q) => $q->where('seller_id', $seller->id))
            ->where('status', 'refunded')->whereNotNull('refunded_at')
            ->with('order')->orderBy('id')->chunkById(200, function ($returns) use ($seller) {
                foreach ($returns as $return) {
                    $this->recordRefund($seller, $return);
                }
            });
    }

    public function recordOrder(Seller $seller, Order $order): void
    {
        if ((int) $order->seller_id !== (int) $seller->id) return;
        if ($order->status !== 'completed' && ! $order->statusHistory()->where('to_status', 'completed')->exists()) return;

        DB::transaction(function () use ($seller, $order) {
            $codCollection = DB::table('rider_cod_collections')->where('order_id', $order->id)->exists();
            $codSettlement = $codCollection
                ? DB::table('cod_settlements')->where('order_id', $order->id)->first()
                : null;
            if ($codCollection && (! $codSettlement || ! in_array($codSettlement->status, ['released', 'partially_refunded'], true))) return;

            $voucher = $codCollection ? 0 : max(0, (float) $order->voucher_discount);
            $codRefunds = $codCollection ? abs((float) DB::table('cod_wallet_transactions as t')
                ->join('cod_wallet_accounts as a', 'a.id', '=', 't.wallet_account_id')
                ->where('a.owner_key', 'seller:'.$seller->id.':'.($codSettlement->seller_released_at ? 'available' : 'on_hold'))
                ->whereIn('t.entry_type', ['seller_refund_hold_reversal', 'seller_refund_reversal'])
                ->where('t.order_id', $order->id)->sum('t.amount')) : 0;
            $productGross = $codCollection
                ? max(0, round((float) $codSettlement->seller_amount - $codRefunds, 2))
                : max(0, round((float) $order->total - (float) $order->shipping_fee + $voucher, 2));
            $date = $order->buyer_received_at ?? $order->updated_at;
            $ref = $order->order_number ?: '#'.$order->id;

            $this->entry($seller->id, 'order_income', 'order', $order->id, $productGross, $ref, 'Completed order · products', $date);

            if ($voucher > 0) {
                $this->entry($seller->id, 'voucher_discount', 'order', $order->id, -$voucher, $ref, 'Seller-funded voucher · '.($order->voucher_code ?: 'discount'), $date);
            }
        });
    }

    public function recordRefund(Seller $seller, OrderReturn $return): void
    {
        $order = $return->order;
        if (! $order || (int) $order->seller_id !== (int) $seller->id || $return->status !== 'refunded' || ! $return->refunded_at) return;

        DB::transaction(function () use ($seller, $return, $order) {
            $refund = max(0, round((float) ($return->refund_amount ?? 0), 2));
            $ref = $order->order_number ?: '#'.$order->id;
            if ($refund > 0) {
                $this->entry($seller->id, 'refund', 'order_return', $return->id, -$refund, $ref, 'Completed buyer refund', $return->refunded_at);
            }

            if ($return->return_type !== 'refund') {
                $this->entry($seller->id, 'return_shipping', 'order_return', $return->id, -self::RETURN_SHIPPING_CHARGE, $ref, 'Seller-paid return shipping (₱50)', $return->refunded_at);
            }
        });
    }

    private function entry(int $sellerId, string $type, string $source, int $sourceId, float $amount, string $ref, string $description, $at): void
    {
        DB::table('seller_wallet_transactions')->insertOrIgnore([
            'seller_id' => $sellerId,
            'entry_type' => $type,
            'source_type' => $source,
            'source_id' => $sourceId,
            'amount' => number_format($amount, 2, '.', ''),
            'reference' => $ref,
            'description' => $description,
            'occurred_at' => $at,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function summary(Seller $seller, array $filters = []): array
    {
        $base = DB::table('seller_wallet_transactions')->where('seller_id', $seller->id);
        $totals = (clone $base)->selectRaw('entry_type, SUM(amount) as sum_amount')
            ->groupBy('entry_type')
            ->pluck('sum_amount', 'entry_type');

        $income = (float) ($totals['order_income'] ?? 0);
        $refunds = abs((float) ($totals['refund'] ?? 0));
        $shipping = abs((float) ($totals['return_shipping'] ?? 0));
        $vouchers = abs((float) ($totals['voucher_discount'] ?? 0));

        $transactions = $this->applyFilters(clone $base, $filters)
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->appends(array_filter([
                'search' => $filters['search'] ?? null,
                'type' => ($filters['type'] ?? 'all') !== 'all' ? $filters['type'] : null,
                'from' => $filters['from'] ?? null,
                'to' => $filters['to'] ?? null,
            ], fn ($value) => filled($value)));

        return [
            'balance' => round($income - $refunds - $shipping - $vouchers, 2),
            'income' => $income,
            'refunds' => $refunds,
            'shipping' => $shipping,
            'vouchers' => $vouchers,
            'transactions' => $transactions,
        ];
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function ($inner) use ($search) {
                $inner->where('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('entry_type', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['type']) && $filters['type'] !== 'all') {
            $allowed = ['order_income', 'refund', 'return_shipping', 'voucher_discount'];
            if (in_array($filters['type'], $allowed, true)) {
                $query->where('entry_type', $filters['type']);
            }
        }

        if (! empty($filters['from'])) {
            $query->where('occurred_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->where('occurred_at', '<=', $filters['to']);
        }

        return $query;
    }
}
