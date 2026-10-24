<?php

namespace App\Http\Controllers\Api\Rider\Homepage\VerifyOrder;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use App\Models\Rider\OrderArchive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArchiveVerifyOrderController extends Controller
{
    public function __invoke(
        Request $request,
        Order $order,
        RiderOperationsService $operations
    ): JsonResponse {
        $rider = $operations->rider($request->user());
        abort_unless($order->shipment()->where('rider_id', $rider->id)->exists(), 404);

        $archive = OrderArchive::query()->firstOrNew([
            'rider_id' => $rider->id,
            'order_id' => $order->id,
        ]);
        $archive->archived_at = now();
        $archive->dismissed_at = null;
        $archive->save();

        return response()->json(['data' => ['order_id' => $order->id, 'archived' => true]]);
    }
}
