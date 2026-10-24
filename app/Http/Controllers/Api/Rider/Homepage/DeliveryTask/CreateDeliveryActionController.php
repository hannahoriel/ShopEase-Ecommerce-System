<?php

namespace App\Http\Controllers\Api\Rider\Homepage\DeliveryTask;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CreateDeliveryActionController extends Controller
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
            'action' => ['required', Rule::in(['issue', 'reschedule', 'return'])],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'reschedule_date' => [
                Rule::requiredIf($request->input('action') === 'reschedule'),
                'nullable',
                'date',
                'after:today',
                'before_or_equal:'.now('Asia/Manila')->addDays(14)->toDateString(),
            ],
            'reschedule_slot' => [
                Rule::requiredIf($request->input('action') === 'reschedule'),
                'nullable',
                Rule::in(['morning', 'afternoon', 'evening']),
            ],
        ]);
        abort_unless($order->status === 'out_for_delivery', 422, 'Only an out-for-delivery order can receive a delivery issue or reschedule request.');
        abort_unless(
            ! $rider->deliveryActions()->where('order_id', $order->id)->where('status', 'pending')->exists(),
            409,
            'A delivery action is already awaiting review for this order.'
        );

        $action = $rider->deliveryActions()->create([
            ...$validated,
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        return response()->json(['data' => $action], 201);
    }
}
