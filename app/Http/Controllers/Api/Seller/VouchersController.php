<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller\SellerVoucher;
use App\Models\Seller\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VouchersController extends Controller
{
    private function sellerId(Request $request): int
    {
        return (int) $request->user()->sellerProfile()->value('id');
    }

    public function index(Request $request): JsonResponse
    {
        $sellerId = $this->sellerId($request);
        return response()->json(['data' => SellerVoucher::where('seller_id', $sellerId)->latest()->get()->map(fn ($voucher) => $this->present($voucher))]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $voucher = SellerVoucher::create([...$data, 'seller_id' => $this->sellerId($request), 'code' => strtoupper($data['code'])]);
        return response()->json(['data' => $this->present($voucher), 'message' => 'Voucher created.'], 201);
    }

    public function update(Request $request, SellerVoucher $voucher): JsonResponse
    {
        abort_unless($voucher->seller_id === $this->sellerId($request), 404);
        $data = $request->validate(['enabled' => ['required', 'boolean']]);
        $voucher->update($data);
        return response()->json(['data' => $this->present($voucher->fresh())]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'code' => ['required', 'regex:/^[A-Za-z0-9_-]{4,20}$/', Rule::unique('seller_vouchers', 'code')->where('seller_id', $this->sellerId($request))],
            'title' => ['required', 'string', 'max:65'],
            'discountType' => ['required', Rule::in(['fixed', 'percent'])],
            'discountValue' => ['required', 'numeric', 'gt:0'],
            'minimumSpend' => ['required', 'numeric', 'min:0'],
            'maximumDiscount' => ['required', 'numeric', 'gt:0'],
            'eligibleItems' => ['required', Rule::in(['all', 'selected'])],
            'productIds' => ['nullable', 'string', 'max:2000'],
            'eligibleBuyers' => ['required', Rule::in(['all', 'first', 'returning'])],
            'stacking' => ['required', Rule::in(['none', 'shipping'])],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'perBuyer' => ['required', 'integer', 'min:1', 'lte:quantity'],
            'starts' => ['required', 'date'],
            'ends' => ['required', 'date', 'after:starts'],
            'terms' => ['nullable', 'string', 'max:650'],
            'enabled' => ['required', 'boolean'],
        ]);
        if ($data['discountType'] === 'percent' && $data['discountValue'] > 100) {
            abort(422, 'Percentage discount cannot exceed 100%.');
        }
        if (\Illuminate\Support\Carbon::parse($data['starts'])->lt(now()->startOfMinute())) {
            abort(422, 'Voucher start date/time cannot be in the past.');
        }
        if ($data['discountType'] === 'fixed' && $data['maximumDiscount'] < $data['discountValue']) {
            abort(422, 'Maximum discount cannot be below the fixed discount amount.');
        }
        $productIds = collect(explode(',', $data['productIds'] ?? ''))->map(fn ($id) => trim($id))->filter()->unique()->values();
        if ($data['eligibleItems'] === 'selected' && $productIds->isEmpty()) {
            abort(422, 'Enter at least one product ID for selected products.');
        }
        if ($data['eligibleItems'] === 'selected') {
            $products = Product::where('seller_id', $this->sellerId($request))
                ->where(function ($query) use ($productIds): void {
                    $numericIds = $productIds->filter(fn ($id) => ctype_digit($id));
                    if ($numericIds->isNotEmpty()) $query->whereIn('id', $numericIds->all());
                    $skus = $productIds->reject(fn ($id) => ctype_digit($id));
                    if ($skus->isNotEmpty()) {
                        $method = $numericIds->isNotEmpty() ? 'orWhereIn' : 'whereIn';
                        $query->{$method}('sku', $skus->all());
                    }
                })->get(['id', 'sku']);
            if ($products->count() !== $productIds->count()) {
                abort(422, 'One or more selected products do not belong to your shop.');
            }
            $productIds = $products->pluck('id')->map(fn ($id) => (string) $id)->values();
        }
        return [
            'code' => strtoupper($data['code']), 'title' => $data['title'],
            'discount_type' => $data['discountType'], 'discount_value' => $data['discountValue'],
            'minimum_spend' => $data['minimumSpend'], 'maximum_discount' => $data['maximumDiscount'],
            'product_ids' => $data['eligibleItems'] === 'selected' ? $productIds->all() : null,
            'eligible_buyers' => $data['eligibleBuyers'], 'stacking' => $data['stacking'],
            'quantity' => $data['quantity'], 'per_buyer' => $data['perBuyer'],
            'starts_at' => $data['starts'], 'ends_at' => $data['ends'], 'terms' => $data['terms'] ?? null,
            'enabled' => $data['enabled'],
        ];
    }

    private function present(SellerVoucher $voucher): array
    {
        $status = ! $voucher->enabled ? 'paused' : ($voucher->claimed >= $voucher->quantity ? 'exhausted' : (now()->lt($voucher->starts_at) ? 'upcoming' : (now()->gt($voucher->ends_at) ? 'expired' : 'active')));
        return [
            'id' => $voucher->code, 'recordId' => $voucher->id, 'title' => $voucher->title,
            'purpose' => 'Store-exclusive promotional discount on eligible COD orders.',
            'discountType' => $voucher->discount_type, 'discountValue' => (float) $voucher->discount_value,
            'minimumSpend' => (float) $voucher->minimum_spend, 'maximumDiscount' => (float) $voucher->maximum_discount,
            'starts' => $voucher->starts_at->format('Y-m-d\TH:i'), 'ends' => $voucher->ends_at->format('Y-m-d\TH:i'),
            'eligibleItems' => $voucher->product_ids ? 'Selected shop products: '.implode(', ', $voucher->product_ids) : 'All products in my shop',
            'eligibleBuyers' => ['all' => 'All buyers', 'first' => 'First-time buyers at my shop', 'returning' => 'Returning buyers'][$voucher->eligible_buyers],
            'productIds' => $voucher->product_ids, 'quantity' => $voucher->quantity, 'claimed' => $voucher->claimed,
            'perBuyer' => $voucher->per_buyer, 'stacking' => $voucher->stacking === 'shipping' ? 'Can combine with shipping voucher only' : 'Cannot combine with other vouchers',
            'terms' => $voucher->terms, 'enabled' => $voucher->enabled, 'scope' => 'This shop only', 'status' => $status,
        ];
    }
}
