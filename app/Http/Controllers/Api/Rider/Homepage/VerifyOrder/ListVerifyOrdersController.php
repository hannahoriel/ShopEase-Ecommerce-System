<?php

namespace App\Http\Controllers\Api\Rider\Homepage\VerifyOrder;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use App\Models\Rider\OrderArchive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListVerifyOrdersController extends Controller
{
    public function __invoke(
        Request $request,
        RiderOperationsService $operations,
        RiderDeliveryService $deliveries
    ): JsonResponse {
        $rider = $operations->rider($request->user());
        $archives = OrderArchive::query()->where('rider_id', $rider->id);
        $archivedOrderIds = (clone $archives)->whereNotNull('archived_at')->select('order_id');
        $dismissedOrderIds = (clone $archives)->whereNotNull('dismissed_at')->select('order_id');

        $orders = Order::query()
            ->whereHas('shipment', fn ($query) => $query->where('rider_id', $rider->id))
            ->whereIn('status', ['to_ship', 'in_transit', 'out_for_delivery', 'delivered'])
            ->whereNotIn('orders.id', $dismissedOrderIds)
            ->when(
                $request->boolean('archived'),
                fn ($query) => $query->whereIn('orders.id', $archivedOrderIds),
                fn ($query) => $query->whereNotIn('orders.id', $archivedOrderIds)
            )
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
