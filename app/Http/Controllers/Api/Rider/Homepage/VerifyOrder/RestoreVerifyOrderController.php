<?php

namespace App\Http\Controllers\Api\Rider\Homepage\VerifyOrder;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestoreVerifyOrderController extends Controller
{
    public function __invoke(
        Request $request,
        Order $order,
        RiderOperationsService $operations
    ): JsonResponse {
        $rider = $operations->rider($request->user());
        abort_unless($order->shipment()->where('rider_id', $rider->id)->exists(), 404);

        $archive = $rider->orderArchives()->where('order_id', $order->id)->firstOrFail();
        $archive->update(['archived_at' => null, 'dismissed_at' => null]);

        return response()->json(['data' => ['order_id' => $order->id, 'archived' => false]]);
    }
}
