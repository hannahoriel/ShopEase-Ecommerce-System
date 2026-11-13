<?php

namespace App\Http\Controllers\Api\Rider\Homepage\PickupDeliveryRecord;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListPickupDeliveryRecordsController extends Controller
{
    public function __invoke(
        Request $request,
        RiderOperationsService $operations,
        RiderDeliveryService $deliveries
    ): JsonResponse {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['pickup', 'delivery'])],
        ]);
        $rider = $operations->rider($request->user());
        if (! $operations->supportsOperation($rider, $validated['type'])) {
            return response()->json(['data' => []]);
        }
        $statuses = $validated['type'] === 'pickup'
            ? ['in_transit', 'out_for_delivery', 'delivered']
            : ['delivered'];
        $orders = Order::query()
            ->whereHas('shipment', fn ($query) => $query->where('rider_id', $rider->id))
            ->whereIn('status', $statuses)
            ->with($deliveries->deliveryRelations())
            ->latest('updated_at')
            ->limit(100)
            ->get();
        $returnDeliveries = $validated['type'] === 'delivery'
            ? OrderReturn::query()
                ->where('delivery_rider_id', $rider->id)
                ->whereIn('status', ['accepted', 'refunded'])
                ->where('return_type', 'return')
                ->whereIn('shipping_status', ['delivered', 'returned'])
                ->with(['order.seller', 'order.items'])
                ->latest('updated_at')
                ->limit(100)
                ->get()
                ->map(function (OrderReturn $return): array {
                    $order = $return->order;
                    $seller = $order?->seller;
                    $orderName = $order?->items->pluck('product_name')->filter()->unique()->implode(', ')
                        ?: 'Order '.$order?->order_number;
                    $address = collect([
                        $seller?->house_number,
                        $seller?->street,
                        $seller?->barangay,
                        $seller?->municipality,
                        $seller?->province,
                    ])->filter()->implode(', ');

                    return [
                        'id' => $return->id,
                        'return_id' => $return->id,
                        'order_id' => $order?->id,
                        'order_number' => $order?->order_number,
                        'order_name' => $orderName,
                        'name' => $orderName,
                        'title' => $orderName,
                        'task_type' => 'return_delivery',
                        'task_label' => 'Buyer Return to Seller',
                        'status' => 'delivered',
                        'return_shipping_status' => $return->shipping_status,
                        'tracking_number' => $return->tracking_number,
                        'total' => (float) $return->refund_amount,
                        'delivery_name' => $seller?->store_name,
                        'delivery_phone' => $seller?->contact_no,
                        'delivery_address' => $address,
                        'seller' => ['store_name' => $seller?->store_name],
                        'items' => $return->return_items ?: [],
                        'delivered_at' => $return->events()->where('status', 'delivered')->latest('scanned_at')->value('scanned_at'),
                    ];
                })
            : collect();

        return response()->json([
            'data' => $orders->map(
                fn (Order $order): array => $deliveries->serializeDelivery($order)
            )->concat($returnDeliveries)->values(),
        ]);
    }
}
