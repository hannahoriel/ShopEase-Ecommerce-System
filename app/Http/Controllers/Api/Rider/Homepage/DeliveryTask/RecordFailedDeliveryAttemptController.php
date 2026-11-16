<?php

namespace App\Http\Controllers\Api\Rider\Homepage\DeliveryTask;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\Shipment;
use App\Models\Buyer\BuyerNotification;
use App\Models\Rider\DeliveryAttempt;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\LogisticsTransferManifest;
use App\Models\Logistics\LogisticsTransferManifestEvent;
use App\Services\BuyerWalletService;
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
            'proof_photo' => ['required', 'image', 'max:15360'],
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
                            'Order '.$lockedOrder->order_number.' was cancelled after three unsuccessful delivery attempts. The parcel is being returned to you. A ₱'.number_format((float) $lockedOrder->shipping_fee, 2).' failed-delivery return shipping fee will be recorded in your wallet once you receive the parcel.',
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

    public function completeReturn(Request $request, Order $order, RiderDeliveryService $deliveries): JsonResponse
    {
        $rider = $deliveries->activeRiderFor($request->user());
        $validated = $request->validate([
            'scanned_code' => ['required', 'string', 'max:2048'],
            'proof_photo' => ['required', 'image', 'max:15360'],
        ]);
        $path = parse_url(trim($validated['scanned_code']), PHP_URL_PATH);
        $code = is_string($path) && $path !== '' ? rawurldecode(basename($path)) : trim($validated['scanned_code']);
        $photoPath = Storage::disk('local')->putFile('seller-return-proofs', $validated['proof_photo']);
        if (! $photoPath) {
            throw new \RuntimeException('The seller handover proof photo could not be stored.');
        }

        try {
            DB::transaction(function () use ($order, $rider, $code, $photoPath, $request): void {
                $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
                $shipment = Shipment::query()->where('order_id', $lockedOrder->id)->lockForUpdate()->firstOrFail();
                abort_unless($shipment->rider_id === $rider->id, 404);
                abort_unless($lockedOrder->status === 'cancelled' && $shipment->return_to_seller_status === 'pending',
                    409, 'This parcel is not awaiting seller return.');
                abort_unless(in_array($code, [$lockedOrder->order_number, $shipment->tracking_number], true),
                    422, 'The scanned code does not match this parcel.');
                abort_unless($rider->logistics_branch_id, 422,
                    'This rider is not assigned to a logistics branch with a delivery hub.');

                $hubQuery = LogisticsDeliveryHub::query()
                    ->where('status', 'active')
                    ->whereNull('archived_at')
                    ->where('logistics_branch_id', $rider->logistics_branch_id);
                if ($shipment->logistics_delivery_hub_id) {
                    $hubQuery->whereKey($shipment->logistics_delivery_hub_id);
                } else {
                    $municipalHub = mb_strtolower(trim((string) $rider->municipal_hub));
                    $municipality = mb_strtolower(trim((string) $rider->municipality));
                    $hubQuery->where(function ($query) use ($municipalHub, $municipality): void {
                        if ($municipalHub !== '') {
                            $query->whereRaw('LOWER(hub_name) = ?', [$municipalHub])
                                ->orWhereRaw('LOWER(municipality) = ?', [$municipalHub]);
                        }
                        if ($municipality !== '') {
                            $query->orWhereRaw('LOWER(municipality) = ?', [$municipality]);
                        }
                    });
                }
                $hub = $hubQuery->first();
                abort_unless($hub, 422, 'No active delivery hub is assigned for this rider return drop-off.');
                abort_unless(! $rider->logistics_branch_id
                    || (int) $hub->logistics_branch_id === (int) $rider->logistics_branch_id,
                    409, 'This return parcel is assigned to a different logistics branch.');

                $hubLocation = $hub->hub_name.' · '.$hub->municipality.', '.$hub->province;
                $manifest = LogisticsTransferManifest::query()->create([
                    'batch_key' => 'failed-delivery-return-'.$lockedOrder->id,
                    'origin_type' => 'rider_return',
                    'origin_id' => $rider->id,
                    'destination_type' => 'delivery_hub',
                    'destination_id' => $hub->id,
                    'courier_name' => trim($rider->first_name.' '.$rider->last_name),
                    'vehicle_reference' => trim(collect([$rider->vehicle, $rider->plate_number])->filter()->implode(' · ')),
                    'status' => 'loaded',
                    'loaded_at' => now(),
                    'departed_at' => now(),
                ]);
                $manifest->shipments()->attach($shipment->id, [
                    'load_status' => 'loaded',
                    'loaded_at' => now(),
                    'loaded_by' => $request->user()->id,
                ]);
                $shipment->forceFill([
                    'logistics_branch_id' => $hub->logistics_branch_id,
                    'logistics_delivery_hub_id' => $hub->id,
                    'return_to_seller_status' => 'return_to_hub',
                    'return_to_seller_proof_path' => $photoPath,
                    'current_location' => 'Rider drop-off · '.$hubLocation,
                    'location_updated_at' => now(),
                ])->save();
                $shipment->scans()->create([
                    'status' => 'Rider Returned Parcel to Delivery Hub',
                    'location' => $hubLocation,
                    'scanned_at' => now(),
                ]);
                LogisticsTransferManifestEvent::query()->create([
                    'transfer_manifest_id' => $manifest->id,
                    'shipment_id' => $shipment->id,
                    'actor_user_id' => $request->user()->id,
                    'event_type' => 'failed_delivery_return_dropped_at_hub',
                    'occurred_at' => now(),
                ]);
                OrderStatusHistory::query()->create([
                    'order_id' => $lockedOrder->id,
                    'from_status' => 'cancelled',
                    'to_status' => 'cancelled',
                    'changed_by' => $request->user()->id,
                    'notes' => 'Rider dropped off the parcel at '.$hub->hub_name.' for hub-to-hub return routing to the seller.',
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        return response()->json([
            'message' => 'Rider drop-off at the delivery hub is confirmed. The parcel will be routed to the seller; no cash is collected.',
            'data' => ['return_to_seller' => 'return_to_hub'],
        ]);
    }
}
