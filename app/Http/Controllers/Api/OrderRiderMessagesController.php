<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Rider\OrderMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderRiderMessagesController extends Controller
{
    public function index(Request $request, Order $order): JsonResponse
    {
        $this->authorizeParticipant($request, $order);
        $rider = $order->shipment?->rider;
        abort_unless($rider && $rider->registration_status === 'active', 404);

        $messages = OrderMessage::query()
            ->where('order_id', $order->id)
            ->where('rider_id', $rider->id)
            ->where(function ($query) use ($request): void {
                $query->where('sender_id', $request->user()->id)
                    ->orWhere('recipient_id', $request->user()->id);
            })
            ->with('sender:id,name')
            ->oldest()
            ->limit(200)
            ->get()
            ->map(fn (OrderMessage $message): array => [
                'id' => $message->id,
                'body' => $message->body,
                'sender_name' => $message->sender?->name,
                'is_sender' => (int) $message->sender_id === (int) $request->user()->id,
                'created_at' => $message->created_at?->toISOString(),
            ])
            ->values();

        return response()->json(['data' => $messages]);
    }

    public function store(Request $request, Order $order): JsonResponse
    {
        $this->authorizeParticipant($request, $order);
        $rider = $order->shipment?->rider;
        abort_unless($rider && $rider->registration_status === 'active', 404);
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = OrderMessage::query()->create([
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'sender_id' => $request->user()->id,
            'recipient_id' => $rider->user_id,
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

    private function authorizeParticipant(Request $request, Order $order): void
    {
        $userId = (int) $request->user()->id;
        $isBuyer = (int) $order->buyer_id === $userId;
        $isSeller = (int) $order->seller()->value('user_id') === $userId;
        abort_unless($isBuyer || $isSeller, 404);
    }
}
