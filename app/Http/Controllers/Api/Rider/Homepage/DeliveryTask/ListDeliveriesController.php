<?php

namespace App\Http\Controllers\Api\Rider\Homepage\DeliveryTask;

use App\Http\Controllers\Controller;
use App\Http\Services\AssignScheduledPickups;
use App\Http\Services\RiderDeliveryService;
use App\Models\Admin\Order;
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
            ->where(function ($query) use ($isPickup): void {
                $query->whereIn('status', $isPickup
                    ? ['to_ship', 'in_transit']
                    : ['in_transit', 'out_for_delivery', 'delivered']);
                $query->orWhere(fn ($returned) => $returned->where('status', 'cancelled')
                    ->whereHas('shipment', fn ($shipment) => $shipment->where('return_to_seller_status', 'pending')));
            })
            ->withCount('deliveryAttempts')
            ->with($deliveries->deliveryRelations())
            ->latest('updated_at')
            ->limit(100)
            ->get();

        return response()->json([
            'task_type' => $isPickup ? 'pickup' : 'delivery',
            'task_label' => $isPickup ? 'For Pickup' : 'Delivery',
            'data' => $orders->map(function (Order $order) use ($deliveries, $isPickup): array {
                return [...$deliveries->serializeDelivery($order),
                    'task_type' => $isPickup ? 'pickup' : 'delivery',
                    'task_label' => $isPickup ? 'For Pickup' : 'Delivery',
                ];
            })->values(),
        ]);
    }
}
