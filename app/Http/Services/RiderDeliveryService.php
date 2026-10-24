<?php

namespace App\Http\Services;

use App\Models\Admin\Order;
use App\Models\Rider\Rider;
use App\Models\User;

class RiderDeliveryService
{
    public function activeRiderFor(User $user): Rider
    {
        return Rider::query()
            ->where('user_id', $user->id)
            ->where('registration_status', 'active')
            ->firstOrFail();
    }

    public function assignedOrder(User $user, Order $order): Order
    {
        $rider = $this->activeRiderFor($user);
        abort_unless($order->shipment()->where('rider_id', $rider->id)->exists(), 404);

        return $order;
    }

    public function deliveryRelations(): array
    {
        return [
            'buyer:id,name',
            'buyer.buyerProfile:id,user_id,contact_no',
            'seller:id,store_name,contact_no,province,municipality,barangay,street,house_number,building,subdivision',
            'items:id,order_id,product_name,quantity',
            'shipment.rider.user:id,name',
            'deliveryProofs' => fn ($query) => $query->latest()->limit(10),
        ];
    }

    public function serializeDelivery(Order $order): array
    {
        $rider = $order->shipment?->rider;

        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'delivery_name' => $order->delivery_name,
            'delivery_phone' => $order->delivery_phone,
            'delivery_address' => $order->delivery_address,
            'total' => $order->total,
            'payment_method' => $order->payment_method,
            'pickup_date' => $order->pickup_date?->toDateString(),
            'pickup_time' => $order->pickup_time?->format('H:i'),
            'seller' => [
                'store_name' => $order->seller?->store_name,
                'contact_no' => $order->seller?->contact_no,
                'address' => collect([
                    $order->seller?->house_number,
                    $order->seller?->street,
                    $order->seller?->building,
                    $order->seller?->subdivision,
                    $order->seller?->barangay,
                    $order->seller?->municipality,
                    $order->seller?->province,
                ])->filter()->implode(', '),
            ],
            'buyer' => [
                'name' => $order->buyer?->name,
                'contact_no' => $order->buyer?->buyerProfile?->contact_no,
            ],
            'items' => $order->items->map(fn ($item): array => [
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
            ])->values(),
            'shipment' => $order->shipment ? [
                'tracking_number' => $order->shipment->tracking_number,
                'courier' => $order->shipment->courier,
                'estimated_delivery' => $order->shipment->estimated_delivery?->toDateString(),
                'current_location' => $order->shipment->current_location,
                'picked_up_at' => $order->shipment->picked_up_at?->toISOString(),
                'delivered_at' => $order->shipment->delivered_at?->toISOString(),
                'rider' => [
                    'name' => $rider?->user?->name,
                    'contact_no' => $rider?->contact_no,
                ],
            ] : null,
            'proofs' => $order->deliveryProofs->map(fn ($proof): array => [
                'id' => $proof->id,
                'status' => $proof->status,
                'scanned_code' => $proof->scanned_code,
                'delivery_method' => $proof->delivery_method,
                'created_at' => $proof->created_at?->toISOString(),
                'photo_url' => route('rider.api.deliveries.proofs.photo', [
                    'order' => $order->id,
                    'proof' => $proof->id,
                ]),
            ])->values(),
        ];
    }
}
