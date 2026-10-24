<?php

namespace App\Http\Controllers\Api\Rider\Homepage\PickupTask;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListPickupTasksController extends Controller
{
    public function __invoke(
        Request $request,
        RiderOperationsService $operations,
        RiderDeliveryService $deliveries
    ): JsonResponse {
        $rider = $operations->rider($request->user());
        $orders = Order::query()
            ->whereHas('shipment', fn ($query) => $query->where('rider_id', $rider->id))
            ->whereIn('status', ['to_ship', 'in_transit'])
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
