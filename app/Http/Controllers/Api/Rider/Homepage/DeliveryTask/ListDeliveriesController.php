<?php

namespace App\Http\Controllers\Api\Rider\Homepage\DeliveryTask;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Models\Admin\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListDeliveriesController extends Controller
{
    public function __invoke(Request $request, RiderDeliveryService $deliveries): JsonResponse
    {
        $rider = $deliveries->activeRiderFor($request->user());
        $orders = Order::query()
            ->whereHas('shipment', fn ($query) => $query->where('rider_id', $rider->id))
            ->whereIn('status', ['to_ship', 'in_transit', 'out_for_delivery', 'delivered'])
            ->with($deliveries->deliveryRelations())
            ->latest('updated_at')
            ->limit(100)
            ->get();

        return response()->json([
            'data' => $orders
                ->map(fn (Order $order): array => $deliveries->serializeDelivery($order))
                ->values(),
        ]);
    }
}
