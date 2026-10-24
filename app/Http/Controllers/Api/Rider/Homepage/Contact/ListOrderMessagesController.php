<?php

namespace App\Http\Controllers\Api\Rider\Homepage\Contact;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use App\Models\Rider\OrderMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListOrderMessagesController extends Controller
{
    public function __invoke(
        Request $request,
        Order $order,
        RiderDeliveryService $deliveries,
        RiderOperationsService $operations
    ): JsonResponse {
        $rider = $operations->rider($request->user());
        $deliveries->assignedOrder($request->user(), $order);

        $messages = OrderMessage::query()
            ->where('rider_id', $rider->id)
            ->where('order_id', $order->id)
            ->with('sender:id,name')
            ->oldest()
            ->limit(200)
            ->get()
            ->map(fn (OrderMessage $message): array => [
                'id' => $message->id,
                'body' => $message->body,
                'sender_name' => $message->sender?->name,
                'is_rider' => (int) $message->sender_id === (int) $request->user()->id,
                'created_at' => $message->created_at?->toISOString(),
            ])
            ->values();

        return response()->json(['data' => $messages]);
    }
}
