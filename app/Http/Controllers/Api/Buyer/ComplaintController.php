<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $complaints = Complaint::query()
            ->where('user_id', $request->user()->id)
            ->with([
                'user:id,name,email,profile_photo_path',
                'order.seller.user:id,name,email,profile_photo_path',
                'order.items.product',
                'order.shipment',
                'order.logisticsCompany',
                'updates.user:id,name,profile_photo_path',
                'returnRequest.events',
            ])
            ->latest()
            ->get()
            ->map(fn (Complaint $complaint): array => $this->serialize($complaint));

        return response()->json([
            'data' => $complaints,
            'counts' => [
                'open' => $complaints->where('status', 'open')->count(),
                'in_progress' => $complaints->where('status', 'in-progress')->count(),
                'resolved' => $complaints->where('status', 'resolved')->count(),
                'total' => $complaints->count(),
            ],
        ]);
    }

    private function serialize(Complaint $complaint): array
    {
        $order = $complaint->order;
        $buyer = $complaint->user;
        $seller = $order?->seller;
        $sellerUser = $seller?->user;
        $item = $complaint->selectedOrderItem();

        return [
            'databaseId' => $complaint->id,
            'returnRequest' => $complaint->returnRequest?->toArray(),
            'id' => 'CMP-'.str_pad((string) $complaint->id, 4, '0', STR_PAD_LEFT),
            'party1' => $buyer?->name ?? 'Buyer',
            'party1Photo' => $buyer?->profile_photo_path ? Storage::disk('public')->url($buyer->profile_photo_path) : null,
            'party1Email' => $buyer?->email ?? '—',
            'role1' => 'Buyer',
            'party2' => $seller?->store_name ?? 'ShopEase Support',
            'party2Photo' => ($sellerUser?->profile_photo_path ?: $seller?->profile_photo_path) ? Storage::disk('public')->url($sellerUser?->profile_photo_path ?: $seller?->profile_photo_path) : null,
            'party2Email' => $sellerUser?->email ?? '—',
            'role2' => 'Seller',
            'type' => $complaint->type ?: $complaint->subject,
            'status' => str_replace('_', '-', $complaint->status),
            'filed_at' => $complaint->created_at?->toISOString(),
            'updated_at' => $complaint->updated_at?->toISOString(),
            'description' => $complaint->description ?: $complaint->subject,
            'order_id' => $order?->order_number ?? '—',
            'orderDate' => $order?->created_at?->toISOString(),
            'paymentMethod' => $order?->payment_method ?? '—',
            'paymentStatus' => $order?->status === 'refunded' ? 'Refunded' : '—',
            'shippingMethod' => $order?->logisticsCompany?->business_name ?? $order?->shipment?->courier ?? '—',
            'orderStatus' => $order?->status ? str($order->status)->replace('_', ' ')->title()->toString() : '—',
            'orderTotal' => $order ? '₱'.number_format((float) $order->total, 2) : '—',
            'productName' => $item?->product_name ?? '—',
            'is_fragile' => (bool) ($item?->is_fragile || $item?->product?->is_fragile),
            'productVariant' => collect([$item?->variation, $item?->color, $item?->size])->filter()->implode(' · ') ?: '—',
            'productPrice' => $item ? '₱'.number_format((float) $item->unit_price, 2) : '—',
            'productQuantity' => $item ? 'x'.$item->quantity : '—',
            'productCategory' => $item?->product?->category ?? '—',
            'shopName' => $seller?->store_name ?? 'ShopEase Support',
            'productImage' => $this->productImage($item?->product?->photos ?? []),
            'evidence' => collect($complaint->evidence ?? [])->map(fn ($path): string => Storage::disk('public')->url($path))->values(),
            'updates' => $complaint->updates->map(fn ($update): array => [
                'id' => (string) $update->id,
                'type' => $update->type,
                'status' => $update->type === 'status' ? str_replace('_', '-', $complaint->status) : 'open',
                'message' => $update->message,
                'actor' => $update->user?->name ?? 'ShopEase Support',
                'timestamp' => $update->created_at?->toISOString(),
            ])->values(),
        ];
    }

    private function productImage(array $photos): ?string
    {
        $photo = collect($photos)->first(fn ($value): bool => is_string($value) && trim($value) !== '');
        if (! $photo) {
            return null;
        }

        if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://') || str_starts_with($photo, 'data:')) {
            return $photo;
        }

        return Storage::disk('public')->url(ltrim(preg_replace('/^storage\//', '', $photo), '/'));
    }
}
