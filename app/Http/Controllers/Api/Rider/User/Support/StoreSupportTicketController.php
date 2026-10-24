<?php

namespace App\Http\Controllers\Api\Rider\User\Support;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreSupportTicketController extends Controller
{
    public function __invoke(
        Request $request,
        RiderOperationsService $operations,
        RiderDeliveryService $deliveries
    ): JsonResponse {
        $rider = $operations->rider($request->user());
        $validated = $request->validate([
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
        ]);
        if (! empty($validated['order_id'])) {
            $deliveries->assignedOrder($request->user(), Order::findOrFail($validated['order_id']));
        }
        $ticket = $rider->supportTickets()->create([
            ...$validated,
            'priority' => $validated['priority'] ?? 'medium',
            'status' => 'open',
        ]);
        $ticket->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => $validated['description'],
        ]);

        return response()->json(['data' => $ticket->load('messages')], 201);
    }
}
