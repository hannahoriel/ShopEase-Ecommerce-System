<?php

namespace App\Http\Controllers\Api\Rider\Homepage\DeliveryTask;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\Shipment;
use App\Models\Buyer\BuyerNotification;
use App\Models\Rider\DeliveryAttempt;
use App\Services\BuyerWalletService;
use App\Services\OrderInventoryService;
use App\Services\SellerNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RecordFailedDeliveryAttemptController extends Controller
{
    public function store(Request $request, Order $order, RiderDeliveryService $deliveries): JsonResponse
    {
        $rider = $deliveries->activeRiderFor($request->user());
        $validated = $request->validate([
            'reason' => ['required', Rule::in([
                'Buyer unavailable', 'Buyer did not answer', 'Address inaccessible',
                'Buyer refused parcel', 'Other delivery issue',
            ])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'proof_photo' => ['required', 'image', 'max:10240'],
        ]);
        $photoPath = Storage::disk('local')->putFile('delivery-attempts', $validated['proof_photo']);
        if (! $photoPath) {
            throw new \RuntimeException('The failed delivery proof photo could not be stored.');
        }

        try {
            $result = DB::transaction(function () use ($order, $rider, $validated, $photoPath, $request): array {
                $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
                $shipment = Shipment::query()->where('order_id', $lockedOrder->id)->lockForUpdate()->firstOrFail();
                abort_unless($shipment->rider_id === $rider->id, 404);
                abort_unless($lockedOrder->status === 'out_for_delivery' && ! $shipment->return_to_seller_status,
                    409, 'This parcel is no longer awaiting a delivery attempt.');

                $count = (int) $lockedOrder->deliveryAttempts()->count();
                abort_if($count >= 3, 409, 'The maximum number of delivery attempts has already been reached.');
                $attemptNumber = $count + 1;
                DeliveryAttempt::query()->create([
                    'order_id' => $lockedOrder->id,
                    'rider_id' => $rider->id,
                    'attempt_number' => $attemptNumber,
                    'reason' => $validated['reason'],
                    'notes' => $validated['notes'] ?? null,
                    'evidence_path' => $photoPath,
                ]);

                $message = "Delivery attempt {$attemptNumber} of 3 failed: {$validated['reason']}".
                    (filled($validated['notes'] ?? null) ? ' '.$validated['notes'] : '');
                $previousStatus = $lockedOrder->status;
                $cancelled = $attemptNumber >= 3;
                if ($cancelled) {
                    $lockedOrder->forceFill(['status' => 'cancelled', 'pickup_date' => null, 'pickup_time' => null])->save();
                    $shipment->forceFill(['return_to_seller_status' => 'pending'])->save();
                    $message = 'Automatically cancelled after three unsuccessful delivery attempts. Parcel must be returned to the seller. '.$message;
                    OrderStatusHistory::query()->create([
                        'order_id' => $lockedOrder->id,
                        'from_status' => $previousStatus,
                        'to_status' => 'cancelled',
                        'changed_by' => $request->user()->id,
                        'notes' => $message,
                    ]);
                    if ($lockedOrder->payment_method === 'ShopEase Wallet' && (float) $lockedOrder->total > 0) {
                        app(BuyerWalletService::class)->move(
                            (int) $lockedOrder->buyer_id, 'payment_reversal', (float) $lockedOrder->total,
                            'order', (int) $lockedOrder->id, 'Automatic cancellation after three failed delivery attempts for '.$lockedOrder->order_number,
                        );
                    }
                    BuyerNotification::createForOrder(
                        $lockedOrder, 'order', 'Order cancelled after failed deliveries',
                        'Order '.$lockedOrder->order_number.' was cancelled after three unsuccessful delivery attempts. The parcel is being returned to the seller.',
                        ['status' => 'cancelled', 'cancellation_reason' => 'Three unsuccessful delivery attempts'],
                    );
                    $seller = $lockedOrder->seller()->with('user')->first();
                    if ($seller?->user) {
                        app(SellerNotificationService::class)->send(
                            $seller->user, 'order_updates', 'Parcel returning to your store',
                            'Order '.$lockedOrder->order_number.' was cancelled after three unsuccessful delivery attempts. The parcel is being returned to you.',
                            '/seller/order-status',
                        );
                    }
                }

                return ['attempt' => $attemptNumber, 'cancelled' => $cancelled, 'message' => $message];
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        return response()->json(['data' => $result], $result['cancelled'] ? 200 : 201);
    }

    public function completeReturn(Request $request, Order $order, RiderDeliveryService $deliveries, OrderInventoryService $inventory): JsonResponse
    {
        $rider = $deliveries->activeRiderFor($request->user());
        $validated = $request->validate([
            'scanned_code' => ['required', 'string', 'max:2048'],
            'proof_photo' => ['required', 'image', 'max:10240'],
        ]);
        $path = parse_url(trim($validated['scanned_code']), PHP_URL_PATH);
        $code = is_string($path) && $path !== '' ? rawurldecode(basename($path)) : trim($validated['scanned_code']);
        $photoPath = Storage::disk('local')->putFile('seller-return-proofs', $validated['proof_photo']);
        if (! $photoPath) {
            throw new \RuntimeException('The seller handover proof photo could not be stored.');
        }

        try {
            DB::transaction(function () use ($order, $rider, $code, $photoPath, $request, $inventory): void {
                $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
                $shipment = Shipment::query()->where('order_id', $lockedOrder->id)->lockForUpdate()->firstOrFail();
                abort_unless($shipment->rider_id === $rider->id, 404);
                abort_unless($lockedOrder->status === 'cancelled' && $shipment->return_to_seller_status === 'pending',
                    409, 'This parcel is not awaiting seller return.');
                abort_unless(in_array($code, [$lockedOrder->order_number, $shipment->tracking_number], true),
                    422, 'The scanned code does not match this parcel.');

                $shipment->forceFill([
                    'return_to_seller_status' => 'completed',
                    'returned_to_seller_at' => now(),
                    'return_to_seller_proof_path' => $photoPath,
                    'current_location' => 'Returned to seller',
                    'location_updated_at' => now(),
                ])->save();
                $inventory->restoreOrder($lockedOrder);
                OrderStatusHistory::query()->create([
                    'order_id' => $lockedOrder->id,
                    'from_status' => 'cancelled',
                    'to_status' => 'cancelled',
                    'changed_by' => $request->user()->id,
                    'notes' => 'Parcel returned to seller after three unsuccessful delivery attempts.',
                ]);
                $seller = $lockedOrder->seller()->with('user')->first();
                if ($seller?->user) {
                    app(SellerNotificationService::class)->send(
                        $seller->user, 'order_updates', 'Returned parcel received',
                        'The parcel for order '.$lockedOrder->order_number.' has been returned to your store. The product stock is restored.',
                        '/seller/order-status',
                    );
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        return response()->json(['message' => 'Parcel return to seller confirmed.']);
    }
}
