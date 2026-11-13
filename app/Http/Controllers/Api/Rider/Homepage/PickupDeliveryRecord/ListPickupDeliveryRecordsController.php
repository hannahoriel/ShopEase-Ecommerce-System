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
        $requestedType = mb_strtolower(trim((string) $request->query('type', 'delivery')));
        $requestedType = str_replace([' ', '-', '_'], '', $requestedType);
        $requestedType = match ($requestedType) {
            'pickup', 'pickups', 'pickuprecord' => 'pickup',
            'delivery', 'deliveries', 'delivered', 'deliveryrecord' => 'delivery',
            default => $request->query('type'),
        };
        $request->merge(['type' => $requestedType ?: 'delivery']);
        $validated = $request->validate([
            'type' => ['required', Rule::in(['pickup', 'delivery'])],
        ]);
        $rider = $operations->rider($request->user());
        // History belongs to the rider who handled the task, even if their
        // currently configured operation type later changes.
        $ordersQuery = Order::query()->with($deliveries->deliveryRelations());
        if ($validated['type'] === 'pickup') {
            $ordersQuery->whereHas('shipment', fn ($query) => $query->where('rider_id', $rider->id))
                ->whereIn('status', ['in_transit', 'out_for_delivery', 'delivered']);
        } else {
            // Use the completed proof as the historical owner of a delivery.
            // The shipment assignment can later change, but the rider who
            // completed the delivery must still see it in their record.
            $ordersQuery->where(function ($query) use ($rider): void {
                $query->whereHas('deliveryProofs', fn ($proofs) => $proofs
                    ->where('rider_id', $rider->id)->where('status', 'delivered'))
                    ->orWhere(fn ($legacy) => $legacy->where('status', 'delivered')
                        ->whereHas('shipment', fn ($shipment) => $shipment->where('rider_id', $rider->id)));
            });
        }
        $orders = $ordersQuery->latest('updated_at')->limit(100)->get();
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

        $records = $orders->map(
                fn (Order $order): array => $deliveries->serializeDelivery($order)
            )->concat($returnDeliveries)->values();

        return response()->json([
            'data' => $records,
            'records' => $records,
            'total' => $records->count(),
        ]);
    }
}
