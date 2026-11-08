<?php

namespace App\Services;

use App\Models\Admin\Order;
use App\Models\Seller\SellerVoucher;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class SellerVoucherService
{
    public function sellerFor(string $code, User $buyer, Collection $items, ?int $preferred = null): int
    {
        $groups = $items->groupBy(fn ($item) => $item->product?->seller_id);
        $voucher = SellerVoucher::where('code', strtoupper(trim($code)))
            ->where(fn ($query) => $query->whereNull('seller_id')->orWhereIn('seller_id', $groups->keys()))
            ->when($preferred, fn ($query) => $query->where(fn ($scope) => $scope->whereNull('seller_id')->orWhere('seller_id', $preferred)))
            ->first();
        if (!$voucher) throw ValidationException::withMessages(['voucher_code' => 'This voucher is unavailable for the selected products.']);
        $lastError = null;
        foreach ($groups as $sellerId => $sellerItems) {
            if ($preferred && (int) $sellerId !== $preferred) continue;
            if ($voucher->seller_id !== null && (int) $voucher->seller_id !== (int) $sellerId) continue;
            try {
                $this->evaluate($code, $buyer, (int) $sellerId, $sellerItems);
                return (int) $sellerId;
            } catch (ValidationException $error) { $lastError = $error; }
        }
        throw $lastError ?? ValidationException::withMessages(['voucher_code' => 'No shop order qualifies for this voucher.']);
    }

    public function evaluate(string $code, User $buyer, int $sellerId, Collection $items, bool $lock = false): array
    {
        $query = SellerVoucher::query()->where(fn ($scope) => $scope->where('seller_id', $sellerId)->orWhereNull('seller_id'))->where('code', strtoupper(trim($code)));
        if ($lock) $query->lockForUpdate();
        $voucher = $query->first();
        $fail = fn (string $message) => throw ValidationException::withMessages(['voucher_code' => [$message]]);
        if (! $voucher || ! $voucher->enabled) $fail('This voucher is unavailable.');
        if (now()->lt($voucher->starts_at) || now()->gt($voucher->ends_at)) $fail('This voucher is outside its validity period.');
        if ($voucher->claimed >= $voucher->quantity) $fail('This voucher has reached its allocation limit.');
        $uses = $voucher->redemptions()->where('buyer_id', $buyer->id)->count();
        if ($uses >= $voucher->per_buyer) $fail('You have reached the use limit for this voucher.');
        $hasPreviousOrders = Order::where('buyer_id', $buyer->id)
            ->when($voucher->seller_id !== null, fn ($query) => $query->where('seller_id', $sellerId))->exists();
        $audience = $voucher->seller_id === null ? 'ShopEase' : 'this shop';
        if ($voucher->eligible_buyers === 'first' && $hasPreviousOrders) $fail('This voucher is for first-time buyers at '.$audience.'.');
        if ($voucher->eligible_buyers === 'returning' && ! $hasPreviousOrders) $fail('This voucher is for returning buyers at '.$audience.'.');

        $eligibleIds = $voucher->product_ids;
        $eligibleItems = $eligibleIds
            ? $items->filter(fn ($item) => in_array((string) $item->product_id, array_map('strval', $eligibleIds), true))
            : $items;
        if ($voucher->eligible_category) {
            $eligibleItems = $eligibleItems->filter(fn ($item) => \App\Support\VoucherCategories::matches(
                (string) $item->product?->category, $voucher->eligible_category,
            ));
        }
        if ($eligibleItems->isEmpty()) $fail('No products in this order qualify for the voucher.');
        $subtotal = (float) $eligibleItems->sum(fn ($item) => $item->product->salePriceForCart($item->variation, $item->color, $item->size) * $item->quantity);
        if ($subtotal < (float) $voucher->minimum_spend) $fail('This order does not meet the voucher minimum spend.');
        $discount = $voucher->discount_type === 'percent'
            ? min($subtotal * (float) $voucher->discount_value / 100, (float) $voucher->maximum_discount)
            : min($subtotal, (float) $voucher->discount_value, (float) $voucher->maximum_discount);
        return ['voucher' => $voucher, 'discount' => round($discount, 2), 'eligible_subtotal' => round($subtotal, 2)];
    }
}
