<?php

namespace App\Http\Controllers\Api\Rider\Homepage\DeliveryTask;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\Shipment;
use App\Models\Logistics\LogisticsTransferManifest;
use App\Models\Logistics\LogisticsTransferManifestEvent;
use App\Models\Rider\DeliveryProof;
use App\Models\Rider\Rider;
use App\Services\OrderInventoryService;
use App\Services\SellerNotificationService;
use App\Services\SellerWalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SubmitFailedDeliveryReturnProofController extends Controller
{
    public function __invoke(
        Request $request,
        Order $order,
        Rider $rider,
        RiderDeliveryService $deliveries,
    ): JsonResponse {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['delivered'])],
            'scanned_code' => ['required', 'string', 'max:2048'],
            'proof_photo' => ['required', 'image', 'max:10240'],
            'delivery_method' => ['nullable', Rule::in(['handed_to_buyer', 'left_at_door'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $shipment = Shipment::query()->where('order_id', $order->id)->firstOrFail();
        abort_unless((int) $shipment->rider_id === (int) $rider->id, 409,
            'This return delivery is no longer assigned to your rider account.');
        abort_unless($order->status === 'cancelled'
            && $shipment->return_to_seller_status === 'out_for_delivery_to_seller',
            409, 'This failed-delivery return is not out for delivery to the seller.');
        $this->validateScannedCode($validated['scanned_code'], $order, $shipment);

        $photoPath = Storage::disk('local')->putFile('delivery-proofs', $validated['proof_photo']);
        if (! $photoPath) {
            throw new \RuntimeException('The seller return proof photo could not be stored.');
        }

        try {
            DB::transaction(function () use ($order, $rider, $validated, $photoPath, $request): void {
                $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
                $lockedShipment = Shipment::query()
                    ->where('order_id', $lockedOrder->id)
                    ->lockForUpdate()
                    ->firstOrFail();
                abort_unless((int) $lockedShipment->rider_id === (int) $rider->id
                    && $lockedOrder->status === 'cancelled'
                    && $lockedShipment->return_to_seller_status === 'out_for_delivery_to_seller',
                    409, 'This failed-delivery return is no longer assigned to you.');

                $lockedShipment->forceFill([
                    'return_to_seller_status' => 'completed',
                    'returned_to_seller_at' => now(),
                    'current_location' => 'Returned to seller',
                    'location_updated_at' => now(),
                ])->save();
                $lockedShipment->scans()->create([
                    'status' => 'Returned to Seller',
                    'location' => 'Seller handoff · '.$lockedOrder->seller?->municipality.', '.$lockedOrder->seller?->province,
                    'scanned_at' => now(),
                ]);
                $finalManifest = LogisticsTransferManifest::query()
                    ->where('destination_type', 'delivery_hub')
                    ->where('destination_id', $lockedShipment->logistics_delivery_hub_id)
                    ->where('status', 'out_for_delivery')
                    ->whereHas('shipments', fn ($query) => $query->whereKey($lockedShipment->id))
                    ->latest('id')
                    ->first();
                if ($finalManifest) {
                    $finalManifest->update(['status' => 'arrived', 'arrived_at' => now()]);
                    LogisticsTransferManifestEvent::query()->create([
                        'transfer_manifest_id' => $finalManifest->id,
                        'shipment_id' => $lockedShipment->id,
                        'actor_user_id' => $request->user()->id,
                        'event_type' => 'failed_delivery_return_seller_handoff',
                        'occurred_at' => now(),
                    ]);
                }

                DeliveryProof::query()->create([
                    'order_id' => $lockedOrder->id,
                    'rider_id' => $rider->id,
                    'status' => 'delivered',
                    'scanned_code' => $validated['scanned_code'],
                    'photo_path' => $photoPath,
                    'delivery_method' => 'handed_to_seller',
                    'notes' => $validated['notes'] ?? null,
                ]);
                OrderStatusHistory::query()->create([
                    'order_id' => $lockedOrder->id,
                    'from_status' => 'cancelled',
                    'to_status' => 'cancelled',
                    'changed_by' => $request->user()->id,
                    'notes' => 'Failed-delivery return parcel handed to the seller. No refund or COD collection was created.',
                ]);

                app(OrderInventoryService::class)->restoreOrder($lockedOrder);
                $seller = $lockedOrder->seller()->with('user')->first();
                if ($seller) {
                    app(SellerWalletService::class)->recordFailedDeliveryShipping($seller, $lockedOrder, $lockedShipment);
                }
                if ($seller?->user) {
                    app(SellerNotificationService::class)->send(
                        $seller->user,
                        'order_updates',
                        'Returned parcel received',
                        'The parcel for order '.$lockedOrder->order_number.' has been returned to your store. Product stock is restored, and the ₱'.number_format((float) $lockedOrder->shipping_fee, 2).' failed-delivery return shipping fee was recorded in your wallet.',
                        '/seller/order-status',
                    );
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        $freshOrder = $order->fresh()->load($deliveries->deliveryRelations());

        return response()->json(array_replace(
            $deliveries->serializeDelivery($freshOrder),
            [
                'task_type' => 'seller_return',
                'task_label' => 'Failed Delivery Return',
                'cod_collection_required' => false,
                'amount_to_collect' => 0,
            ],
        ));
    }

    private function validateScannedCode(string $code, Order $order, Shipment $shipment): void
    {
        $code = trim($code);
        $path = parse_url($code, PHP_URL_PATH);
        if (is_string($path) && $path !== '') {
            $code = rawurldecode(basename($path));
        }

        abort_unless(in_array($code, [$order->order_number, $shipment->tracking_number], true),
            422, 'The scanned code does not match this return parcel.');
    }
}
