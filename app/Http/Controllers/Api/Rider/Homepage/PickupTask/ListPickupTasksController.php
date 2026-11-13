<?php

namespace App\Http\Controllers\Api\Rider\Homepage\PickupTask;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Http\Services\RiderOperationsService;
use App\Http\Services\AssignScheduledPickups;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Logistics\DeliveryHubRiderRegistration;
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
                ->where('batch_key', 'like', 'pickup-dropoff-%')
                // Keep the pickup task visible after seller collection so the
                // rider can still open the hub drop-off screen. The handoff
                // is complete only after its manifest leaves preparing.
                ->whereIn('status', ['loaded', 'arrived']))
            ->whereIn('status', ['to_ship', 'in_transit'])
            ->with($deliveries->deliveryRelations())
            ->latest('updated_at')
            ->limit(100)
            ->get();

        $returnPickups = OrderReturn::query()
            ->where('pickup_rider_id', $rider->id)
            ->where('status', 'accepted')->where('return_type', 'return')->where('shipping_status', 'scheduled')
            ->where(function ($query) use ($rider): void {
                $hubIds = DeliveryHubRiderRegistration::where('user_id', $rider->user_id)->where('status', 'approved')->select('logistics_delivery_hub_id');
                $query->whereIn('return_delivery_hub_id', $hubIds)
                    ->orWhere(function ($legacy) use ($hubIds): void {
                        $legacy->whereNull('return_delivery_hub_id')->whereHas('order.shipment', fn ($shipment) => $shipment->whereIn('logistics_delivery_hub_id', $hubIds));
                    });
            })
            ->with('order:id,order_number,delivery_name,delivery_phone,delivery_address,total')
            ->orderBy('pickup_date')->orderBy('pickup_time')->get()
            ->map(fn (OrderReturn $return) => [
                'id' => $return->id,
                'task_type' => 'return_pickup',
                'task_label' => 'Buyer Return Pickup',
                'order_number' => $return->order->order_number,
                'title' => 'Buyer Return Pickup',
                'status' => 'to_ship',
                'total' => (float) $return->refund_amount,
                'contact_name' => $return->order->delivery_name,
                'contact_phone' => $return->order->delivery_phone,
                'pickup_address' => $return->order->delivery_address,
                'delivery_address' => $return->order->delivery_address,
                'items' => $return->return_items ?: [],
                'pickup_date' => $return->pickup_date?->toDateString(),
                'pickup_time' => $return->pickup_time,
                'tracking_number' => $return->tracking_number,
                'attempt' => (int) $return->failed_pickup_attempts + 1,
                'outcome_url' => route('rider.api.return-pickups.outcome', $return),
            ]);

        $pickupTasks = $orders->map(
            fn (Order $order): array => [...$deliveries->serializeDelivery($order),
                'task_type' => 'pickup',
                'task_label' => 'For Pickup',
                'handoff_hub' => $hubs->get(mb_strtolower(trim((string) $order->seller?->municipality)))?->only(['id', 'hub_name', 'municipality', 'province']),
                'hub_dropoff_url' => route('rider.api.pickup-tasks.hub-dropoff', ['order' => $order->id]),
            ]
        );
        $data = $pickupTasks->concat($returnPickups)->values();

        return response()->json([
            'return_pickups' => $returnPickups,
            'task_type' => 'pickup',
            'task_label' => 'For Pickup',
            'data' => $data,
        ]);
    }
}
