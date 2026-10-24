<?php

namespace App\Http\Controllers\Api\Rider\Homepage\PickupDeliveryRecord;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
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

        return response()->json([
            'data' => $orders->map(
                fn (Order $order): array => $deliveries->serializeDelivery($order)
            )->values(),
        ]);
    }
}
