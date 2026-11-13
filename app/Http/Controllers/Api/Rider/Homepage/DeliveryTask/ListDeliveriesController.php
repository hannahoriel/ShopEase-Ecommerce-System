<?php

namespace App\Http\Controllers\Api\Rider\Homepage\DeliveryTask;

use App\Http\Controllers\Controller;
use App\Http\Services\AssignScheduledPickups;
use App\Http\Services\RiderDeliveryService;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListDeliveriesController extends Controller
{
    public function __invoke(Request $request, RiderDeliveryService $deliveries, AssignScheduledPickups $assignPickups): JsonResponse
    {
        $rider = $deliveries->activeRiderFor($request->user());
        $isPickup = str_contains(
            mb_strtolower(preg_replace('/[^a-z]/i', '', (string) $rider->logistics_operation)),
            'pickup'
        );
        if ($isPickup) {
            $assignPickups->forRider($rider);
        }

        $orders = Order::query()
            ->whereHas('shipment', fn ($query) => $query->where('rider_id', $rider->id))
            ->when($isPickup, fn ($query) => $query->whereDoesntHave('shipment.transferManifests', fn ($manifest) => $manifest
                ->where('origin_type', 'seller_collection')
                ->where('batch_key', 'like', 'pickup-dropoff-%')))
            ->whereIn('status', $isPickup
                ? ['to_ship', 'in_transit']
                : ['in_transit', 'out_for_delivery', 'delivered'])
            ->with($deliveries->deliveryRelations())
            ->latest('updated_at')
            ->limit(100)
            ->get();
        $returnDeliveries = $isPickup ? collect() : OrderReturn::query()
            ->where('delivery_rider_id', $rider->id)
            ->where('status', 'accepted')->where('return_type', 'return')
            ->where('shipping_status', 'out_for_delivery_to_seller')
            ->with(['order.seller.user', 'order.items.product', 'order.buyer.buyerProfile'])
            ->latest('updated_at')->limit(100)->get()
            ->map(function (OrderReturn $return): array {
                $order = $return->order;
                $seller = $order?->seller;
                $orderName = $order?->items->pluck('product_name')->filter()->unique()->implode(', ') ?: 'Order '.$order?->order_number;
                $address = collect([$seller?->house_number, $seller?->street, $seller?->barangay,
                    $seller?->municipality, $seller?->province])->filter()->implode(', ');
                return [
                    'id' => $return->id, 'return_id' => $return->id, 'order_id' => $order?->id,
                    'task_type' => 'return_delivery', 'task_label' => 'Buyer Return to Seller',
                    'order_number' => $order?->order_number, 'order_name' => $orderName,
                    'name' => $orderName, 'title' => $orderName,
                    'status' => 'out_for_delivery',
                    'tracking_number' => $return->tracking_number, 'total' => (float) $return->refund_amount,
                    'contact_name' => $seller?->store_name ?: trim(($seller?->first_name ?? '').' '.($seller?->last_name ?? '')),
                    'delivery_name' => $seller?->store_name ?: trim(($seller?->first_name ?? '').' '.($seller?->last_name ?? '')),
                    'contact_phone' => $seller?->contact_no, 'delivery_phone' => $seller?->contact_no,
                    'delivery_address' => $address,
                    'seller' => ['store_name' => $seller?->store_name],
                    'items' => $return->return_items ?: [],
                    'proof_url' => route('rider.api.return-deliveries.proof', $return),
                    'scanned_code' => $order?->order_number,
                ];
            });

        return response()->json([
            'task_type' => $isPickup ? 'pickup' : 'delivery',
            'task_label' => $isPickup ? 'For Pickup' : 'Delivery',
            'data' => $orders->map(function (Order $order) use ($deliveries, $isPickup): array {
                return [...$deliveries->serializeDelivery($order),
                    'task_type' => $isPickup ? 'pickup' : 'delivery',
                    'task_label' => $isPickup ? 'For Pickup' : 'Delivery',
                ];
            })->concat($returnDeliveries)->values(),
        ]);
    }
}
