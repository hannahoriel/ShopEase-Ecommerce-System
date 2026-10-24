<?php

namespace App\Http\Controllers\Api\Rider\Homepage\DeliveryTask;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\Shipment;
use App\Models\Buyer\BuyerNotification;
use App\Models\Rider\CodCollection;
use App\Models\Rider\DeliveryProof;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SubmitDeliveryProofController extends Controller
{
    private const TRANSITIONS = [
        'to_ship' => 'in_transit',
        'out_for_delivery' => 'delivered',
    ];

    public function __invoke(
        Request $request,
        Order $order,
        RiderDeliveryService $deliveries
    ): JsonResponse {
        $rider = $deliveries->activeRiderFor($request->user());
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_values(self::TRANSITIONS))],
            'scanned_code' => ['required', 'string', 'max:2048'],
            'proof_photo' => ['required', 'image', 'max:10240'],
            'delivery_method' => ['nullable', Rule::in(['handed_to_buyer', 'left_at_door'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $shipment = $order->shipment()->where('rider_id', $rider->id)->firstOrFail();
        $this->validateScannedCode($validated['scanned_code'], $order, $shipment);
        $this->validateTransition($order, $validated['status']);

        $photoPath = Storage::disk('local')->putFile('delivery-proofs', $validated['proof_photo']);
        if (! $photoPath) {
            throw new \RuntimeException('The delivery proof photo could not be stored.');
        }

        try {
            DB::transaction(function () use ($order, $rider, $validated, $photoPath, $request): void {
                $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
                $lockedShipment = Shipment::query()
                    ->where('order_id', $lockedOrder->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_unless($lockedShipment->rider_id === $rider->id, 404);
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
                if ($validated['status'] === 'delivered' && empty($validated['delivery_method'])) {
                    throw ValidationException::withMessages([
                        'delivery_method' => ['Choose how the parcel was delivered.'],
                    ]);
                }

                $fromStatus = $lockedOrder->status;
                $lockedOrder->update(['status' => $validated['status']]);
                if ($validated['status'] === 'in_transit' && ! $lockedShipment->picked_up_at) {
                    $lockedShipment->picked_up_at = now();
                }
                if ($validated['status'] === 'delivered') {
                    $lockedShipment->delivered_at = now();
                }
                $lockedShipment->save();

                if (
                    $validated['status'] === 'delivered'
                    && $lockedOrder->payment_method === 'Cash on Delivery'
                ) {
                    CodCollection::create([
                        'rider_id' => $rider->id,
                        'order_id' => $lockedOrder->id,
                        'amount' => $lockedOrder->total,
                        'collected_at' => now('Asia/Manila'),
                    ]);
                }

                DeliveryProof::create([
                    'order_id' => $lockedOrder->id,
                    'rider_id' => $rider->id,
                    'status' => $validated['status'],
                    'scanned_code' => $validated['scanned_code'],
                    'photo_path' => $photoPath,
                    'delivery_method' => $validated['delivery_method'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);
                OrderStatusHistory::create([
                    'order_id' => $lockedOrder->id,
                    'from_status' => $fromStatus,
                    'to_status' => $validated['status'],
                    'changed_by' => $request->user()->id,
                    'notes' => $validated['notes'] ?? null,
                ]);
                BuyerNotification::createForOrderStatus($lockedOrder, $validated['status']);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        $delivery = $order->fresh()->load($deliveries->deliveryRelations());

        return response()->json($deliveries->serializeDelivery($delivery));
    }

    private function validateScannedCode(string $code, Order $order, Shipment $shipment): void
    {
        $code = trim($code);
        $path = parse_url($code, PHP_URL_PATH);
        if (is_string($path) && $path !== '') {
            $code = rawurldecode(basename($path));
        }

        if (! in_array($code, [$order->order_number, $shipment->tracking_number], true)) {
            throw ValidationException::withMessages([
                'scanned_code' => ['The scanned code does not match this order or shipment.'],
            ]);
        }
    }

    private function validateTransition(Order $order, string $status): void
    {
        if ((self::TRANSITIONS[$order->status] ?? null) !== $status) {
            throw ValidationException::withMessages([
                'status' => ["Order cannot move from {$order->status} to {$status}."],
            ]);
        }
    }
}
