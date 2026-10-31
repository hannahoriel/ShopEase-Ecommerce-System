<?php

namespace App\Http\Controllers\Api\Rider\Homepage\PickupTask;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Http\Services\RiderOperationsService;
use App\Http\Services\AssignScheduledPickups;
use App\Models\Admin\Order;
use App\Models\Logistics\LogisticsDeliveryHub;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListPickupTasksController extends Controller
{
    public function __invoke(
        Request $request,
        RiderOperationsService $operations,
        RiderDeliveryService $deliveries,
        AssignScheduledPickups $assignPickups
    ): JsonResponse {
        $rider = $operations->rider($request->user());
        if (! $operations->supportsOperation($rider, 'pickup')) {
            return response()->json(['task_type' => 'pickup', 'task_label' => 'For Pickup', 'data' => []]);
        }
        $assignPickups->forRider($rider);
        $hubs = LogisticsDeliveryHub::query()
            ->where('logistics_branch_id', $rider->logistics_branch_id)
            ->where('status', 'active')->whereNull('archived_at')
            ->get(['id', 'hub_name', 'municipality', 'province'])
            ->keyBy(fn ($hub) => mb_strtolower(trim((string) $hub->municipality)));

        $orders = Order::query()
            ->whereHas('shipment', fn ($query) => $query->where('rider_id', $rider->id))
            ->whereDoesntHave('shipment.transferManifests', fn ($query) => $query
                ->where('origin_type', 'seller_collection')
                ->where('batch_key', 'like', 'pickup-dropoff-%'))
            ->whereIn('status', ['to_ship', 'in_transit'])
            ->with($deliveries->deliveryRelations())
            ->latest('updated_at')
            ->limit(100)
            ->get();

        return response()->json([
            'task_type' => 'pickup',
            'task_label' => 'For Pickup',
            'data' => $orders->map(
                fn (Order $order): array => [...$deliveries->serializeDelivery($order),
                    'task_type' => 'pickup',
                    'task_label' => 'For Pickup',
                    'handoff_hub' => $hubs->get(mb_strtolower(trim((string) $order->seller?->municipality)))?->only(['id', 'hub_name', 'municipality', 'province']),
                    'hub_dropoff_url' => route('rider.api.pickup-tasks.hub-dropoff', ['order' => $order->id]),
                ]
            )->values(),
        ]);
    }
}
