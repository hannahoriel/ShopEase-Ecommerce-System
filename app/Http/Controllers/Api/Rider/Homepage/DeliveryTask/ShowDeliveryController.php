<?php

namespace App\Http\Controllers\Api\Rider\Homepage\DeliveryTask;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Models\Admin\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowDeliveryController extends Controller
{
    public function __invoke(Request $request, Order $order, RiderDeliveryService $deliveries): JsonResponse
    {
        $delivery = $deliveries
            ->assignedOrder($request->user(), $order)
            ->load($deliveries->deliveryRelations());

        return response()->json($deliveries->serializeDelivery($delivery));
    }
}
