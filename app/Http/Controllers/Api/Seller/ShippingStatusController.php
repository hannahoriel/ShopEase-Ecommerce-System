<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\Shipment;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ShippingStatusController extends Controller
{
    private const TRANSITIONS = [
        'to_ship' => ['in_transit'],
        'in_transit' => ['out_for_delivery'],
        'out_for_delivery' => ['delivered'],
        'delivered' => [],
    ];

    public function index(Request $request): JsonResponse
    {
        $seller = $this->sellerFor($request->user());
        $query = $this->shippingQuery($seller->id);

        if ($request->filled('status')) {
            $query->whereIn('status', (array) $request->input('status'));
        } else {
            $query->whereIn('status', array_keys(self::TRANSITIONS));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($orderQuery) use ($search): void {
                $orderQuery->where('id', 'like', "%{$search}%")
                    ->orWhereHas('buyer', fn ($buyerQuery) => $buyerQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('shipment', fn ($shipmentQuery) => $shipmentQuery->where('tracking_number', 'like', "%{$search}%"));
            });
        }

        return response()->json($query->latest()->paginate($request->integer('per_page', 10)));
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $order = $this->ownedOrder($request->user(), $order);
        abort_unless(array_key_exists($order->status, self::TRANSITIONS), 404);

        return response()->json($order->load(['buyer:id,name,email,contact_no', 'shipment', 'statusHistory.changedBy:id,name']));
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $order = $this->ownedOrder($request->user(), $order);
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_merge(...array_values(self::TRANSITIONS)))],
            'courier' => ['nullable', 'string', 'max:100'],
            'estimated_delivery' => ['nullable', 'date', 'after_or_equal:today'],
            'shipping_fee' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $newStatus = $validated['status'];
        abort_unless(in_array($newStatus, self::TRANSITIONS[$order->status] ?? [], true), 422, "Order cannot move from {$order->status} to {$newStatus}.");

        $shipment = DB::transaction(function () use ($order, $newStatus, $validated, $request): Shipment {
            $shipment = Shipment::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'tracking_number' => 'SE-' . Str::upper(Str::random(10)),
                    'courier' => $validated['courier'] ?? 'Ease Express',
                    'estimated_delivery' => $validated['estimated_delivery'] ?? now()->addDays(3)->toDateString(),
                    'shipping_fee' => $validated['shipping_fee'] ?? 0,
                ]
            );

            $shipment->fill(array_filter([
                'courier' => $validated['courier'] ?? null,
                'estimated_delivery' => $validated['estimated_delivery'] ?? null,
                'shipping_fee' => $validated['shipping_fee'] ?? null,
            ], fn ($value) => $value !== null));

            if ($newStatus === 'in_transit' && ! $shipment->picked_up_at) {
                $shipment->picked_up_at = now();
            }
            if ($newStatus === 'delivered') {
                $shipment->delivered_at = now();
            }
            $shipment->save();

            $fromStatus = $order->status;
            $order->update(['status' => $newStatus]);
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => $fromStatus,
                'to_status' => $newStatus,
                'changed_by' => $request->user()->id,
                'notes' => $validated['notes'] ?? null,
            ]);

            return $shipment;
        });

        return response()->json($order->fresh(['shipment', 'statusHistory']));
    }

    protected function shippingQuery(int $sellerId)
    {
        return Order::query()
            ->where('seller_id', $sellerId)
            ->with(['buyer:id,name,email,contact_no', 'shipment', 'statusHistory']);
    }

    protected function sellerFor(User $user): Seller
    {
        return Seller::where('user_id', $user->id)->firstOrFail();
    }

    private function ownedOrder(User $user, Order $order): Order
    {
        abort_unless($order->seller_id === $this->sellerFor($user)->id, 404);

        return $order;
    }
}
