<?php

namespace App\Http\Controllers\Api\Rider\Homepage\DeliveryTask;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\Shipment;
use App\Models\Buyer\BuyerNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateDeliveryStatusController extends Controller
{
    private const TRANSITIONS = [
        'in_transit' => 'out_for_delivery',
    ];

    public function __invoke(
        Request $request,
        Order $order,
        RiderDeliveryService $deliveries
    ): JsonResponse {
        $rider = $deliveries->activeRiderFor($request->user());
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_values(self::TRANSITIONS))],
            'current_location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($order, $rider, $validated, $request): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $shipment = Shipment::query()
                ->where('order_id', $lockedOrder->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($shipment->rider_id === $rider->id, 404);
            abort_unless(
                (self::TRANSITIONS[$lockedOrder->status] ?? null) === $validated['status'],
                422,
                "Order cannot move from {$lockedOrder->status} to {$validated['status']}."
            );
            abort_unless(
                ! $lockedOrder->latestCancellationRequest()->where('status', 'pending')->exists(),
                422,
                'Review the pending buyer cancellation request before updating this delivery.'
            );

            $fromStatus = $lockedOrder->status;
            $lockedOrder->update(['status' => $validated['status']]);

            if (array_key_exists('current_location', $validated)) {
                $shipment->current_location = $validated['current_location'];
            }
            $shipment->save();

            OrderStatusHistory::create([
                'order_id' => $lockedOrder->id,
                'from_status' => $fromStatus,
                'to_status' => $validated['status'],
                'changed_by' => $request->user()->id,
                'notes' => $validated['notes'] ?? null,
            ]);
            BuyerNotification::createForOrderStatus($lockedOrder, $validated['status']);
        });

        $delivery = $order->fresh()->load($deliveries->deliveryRelations());

        return response()->json($deliveries->serializeDelivery($delivery));
    }
}
