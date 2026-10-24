<?php

namespace App\Http\Controllers\Api\Rider\Homepage\Contact;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use App\Models\Rider\OrderMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreOrderMessageController extends Controller
{
    public function __invoke(
        Request $request,
        Order $order,
        RiderDeliveryService $deliveries,
        RiderOperationsService $operations
    ): JsonResponse {
        $rider = $operations->rider($request->user());
        $deliveries->assignedOrder($request->user(), $order);
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'is_pickup' => ['required', 'boolean'],
        ]);

        $recipientId = $validated['is_pickup']
            ? $order->seller()->value('user_id')
            : $order->buyer_id;
        abort_if(! $recipientId, 422, 'The order contact is not available.');

        $message = OrderMessage::query()->create([
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'sender_id' => $request->user()->id,
            'recipient_id' => $recipientId,
            'body' => trim($validated['body']),
        ]);

        return response()->json([
            'data' => $message->load('sender:id,name')->only([
                'id',
                'order_id',
                'sender_id',
                'body',
                'created_at',
                'sender',
            ]),
        ], 201);
    }
}
