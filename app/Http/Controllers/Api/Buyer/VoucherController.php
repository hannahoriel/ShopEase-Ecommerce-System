<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Buyer\CartItem;
use App\Models\Seller\SellerVoucher;
use App\Services\SellerVoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $vouchers = SellerVoucher::query()
            ->with('seller:id,store_name')
            ->withCount(['redemptions as buyer_uses_count' => fn ($query) => $query->where('buyer_id', $request->user()->id)])
            ->where('enabled', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->whereColumn('claimed', '<', 'quantity')
            ->orderBy('ends_at')
            ->get()
            ->filter(function ($voucher) use ($request): bool {
                if ($voucher->buyer_uses_count >= $voucher->per_buyer) return false;
                $hasPreviousShopOrder = Order::where('buyer_id', $request->user()->id)
                    ->where('seller_id', $voucher->seller_id)->exists();
                return match ($voucher->eligible_buyers) {
                    'first' => ! $hasPreviousShopOrder,
                    'returning' => $hasPreviousShopOrder,
                    default => true,
                };
            })
            ->map(fn ($voucher) => [
                'id' => $voucher->code,
                'title' => $voucher->title,
                'discount_type' => $voucher->discount_type,
                'discount_value' => (float) $voucher->discount_value,
                'minimum_spend' => (float) $voucher->minimum_spend,
                'maximum_discount' => (float) $voucher->maximum_discount,
                'starts_at' => $voucher->starts_at->toISOString(),
                'ends_at' => $voucher->ends_at->toISOString(),
                'eligible_items' => $voucher->product_ids ? 'Selected shop products' : 'All products in this shop',
                'eligible_buyers' => ['all' => 'All buyers', 'first' => 'First-time buyers at this shop', 'returning' => 'Returning buyers'][$voucher->eligible_buyers],
                'quantity' => $voucher->quantity,
                'claimed' => $voucher->claimed,
                'per_buyer' => $voucher->per_buyer,
                'terms' => $voucher->terms,
                'stacking' => $voucher->stacking === 'shipping' ? 'Can combine with shipping voucher only' : 'Cannot combine with other vouchers',
                'seller_name' => $voucher->seller?->store_name ?? 'Shop',
            ])->values();

        return response()->json(['data' => $vouchers]);
    }

    public function preview(Request $request, SellerVoucherService $vouchers): JsonResponse
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:20']]);
        $items = CartItem::where('user_id', $request->user()->id)->with('product')->get();
        if ($items->isEmpty()) abort(422, 'Your cart is empty.');
        $sellerGroups = $items->groupBy(fn ($item) => $item->product?->seller_id);
        $code = strtoupper(trim($data['code']));
        $cartSellerIds = $sellerGroups->keys()->filter()->map(fn ($id) => (int) $id)->values();
        $voucher = SellerVoucher::where('code', $code)->whereIn('seller_id', $cartSellerIds)->first();
        if (! $voucher) {
            $exists = SellerVoucher::where('code', $code)->exists();
            abort(422, $exists
                ? 'This voucher belongs to a shop that has no items in your cart.'
                : 'Voucher code not found. Check the code and try again.');
        }

        $sellerItems = $sellerGroups->get((string) $voucher->seller_id)
            ?? $sellerGroups->get($voucher->seller_id);
        if (! $sellerItems) abort(422, 'Add an item from this voucher’s shop to your cart.');

        $result = $vouchers->evaluate($code, $request->user(), (int) $voucher->seller_id, $sellerItems);
        return response()->json([
            'data' => [
                'code' => $result['voucher']->code,
                'seller_id' => (int) $voucher->seller_id,
                'seller_name' => $sellerItems->first()->product->seller->store_name ?? 'Shop',
                'discount' => $result['discount'],
                'shipping_fee' => 50,
            ],
            'message' => 'Voucher applied.',
        ]);
    }
}
