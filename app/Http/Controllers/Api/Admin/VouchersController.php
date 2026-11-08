<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Seller\SellerVoucher;
use Illuminate\Http\Request;

class VouchersController extends \App\Http\Controllers\Api\Seller\VouchersController
{
    protected function validated(Request $request): array
    {
        $category = $request->input('eligibleItems');
        $isShipping = $request->input('voucherType') === 'shipping';
        $request->validate(['eligibleItems' => ['required', \Illuminate\Validation\Rule::in([
            'all', 'selected', ...array_keys(config('admin.seller_compliance.categories', [])),
        ])]]);
        $isCategory = ! $isShipping && array_key_exists($category, config('admin.seller_compliance.categories', []));
        if ($isShipping || $isCategory) $request->merge(['eligibleItems' => 'all', 'productIds' => null]);
        return [...parent::validated($request), 'eligible_category' => $isCategory ? $category : null];
    }

    protected function sellerId(Request $request): ?int
    {
        // A null seller identifies platform-issued vouchers, never shop records.
        return null;
    }

    protected function present(SellerVoucher $voucher): array
    {
        return [...parent::present($voucher),
            'purpose' => ($voucher->voucher_type ?? 'merchandise') === 'shipping' ? 'ShopEase or logistics-funded shipping voucher; funding rules follow the selected promotion.' : 'ShopEase platform discount on eligible COD orders.',
            'eligibleItems' => ($voucher->voucher_type ?? 'merchandise') === 'shipping' ? 'Shipping fee' : ($voucher->eligible_category ? config('admin.seller_compliance.categories.'.$voucher->eligible_category) : ($voucher->product_ids ? 'Selected platform products: '.implode(', ', $voucher->product_ids) : 'All eligible ShopEase products')),
            'eligibleBuyers' => ['all' => 'All buyers', 'first' => 'First-time ShopEase buyers', 'returning' => 'Returning ShopEase buyers'][$voucher->eligible_buyers],
            'scope' => 'Across ShopEase shops; one qualifying shop order per checkout',
        ];
    }
}
