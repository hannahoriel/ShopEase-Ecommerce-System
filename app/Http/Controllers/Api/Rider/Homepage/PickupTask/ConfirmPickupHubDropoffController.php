<?php

namespace App\Http\Controllers\Api\Rider\Homepage\PickupTask;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Models\Admin\Order;
use App\Models\Admin\Shipment;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\LogisticsTransferManifest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConfirmPickupHubDropoffController extends Controller
{
    public function __invoke(Request $request, Order $order, RiderDeliveryService $deliveries): JsonResponse
    {
        $rider = $deliveries->activeRiderFor($request->user());
        abort_unless(str_contains(
            mb_strtolower(preg_replace('/[^a-z]/i', '', (string) $rider->logistics_operation)),
            'pickup'
        ), 403, 'Only pickup riders can confirm a pickup drop-off.');

        $hub = DB::transaction(function () use ($order, $rider): LogisticsDeliveryHub {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless(in_array($lockedOrder->status, ['to_ship', 'in_transit'], true) && $lockedOrder->pickup_date !== null, 422, 'Only a scheduled pickup can be dropped off at the delivery hub.');
            $shipment = Shipment::query()->where('order_id', $lockedOrder->id)->lockForUpdate()->firstOrFail();
            abort_unless((int) $shipment->rider_id === (int) $rider->id, 404);

            $seller = $lockedOrder->seller()->firstOrFail();
            $hubQuery = LogisticsDeliveryHub::query()
                ->where('status', 'active')->whereNull('archived_at')
                ->whereRaw('LOWER(province) = ?', [mb_strtolower(trim((string) $seller->province))])
                ->whereRaw('LOWER(municipality) = ?', [mb_strtolower(trim((string) $seller->municipality))]);
            if ($rider->logistics_branch_id) {
                $hubQuery->where('logistics_branch_id', $rider->logistics_branch_id);
            }
            $hub = $hubQuery->orderBy('id')->first();
            if (! $hub) {
                throw ValidationException::withMessages([
                    'delivery_hub' => ['No active delivery hub exists for the seller municipality assigned to this rider.'],
                ]);
            }

            abort_unless(
                $shipment->logistics_delivery_hub_id === null
                    || (int) $shipment->logistics_delivery_hub_id === (int) $hub->id,
                409,
                'This parcel is already routed to another delivery hub.'
            );
            abort_if($shipment->scans()->where('status', 'Received at Delivery Hub')->exists(), 409, 'The delivery hub has already received this parcel.');

            $shipment->update([
                'logistics_branch_id' => $hub->logistics_branch_id,
                'logistics_delivery_hub_id' => $hub->id,
            ]);
            $manifest = LogisticsTransferManifest::query()->firstOrCreate(
                ['batch_key' => 'pickup-dropoff-'.$shipment->id],
                [
                    'origin_type' => 'seller_collection',
                    'origin_id' => $hub->id,
                    'destination_type' => 'delivery_hub',
                    'destination_id' => $hub->id,
                    'courier_name' => trim($rider->first_name.' '.$rider->last_name),
                    'status' => 'loaded',
                    'loaded_at' => now(),
                ],
            );
            abort_unless(
                $manifest->destination_type === 'delivery_hub' && (int) $manifest->destination_id === (int) $hub->id,
                409,
                'A conflicting delivery hub manifest already exists for this parcel.'
            );
            $manifest->shipments()->syncWithoutDetaching([
                $shipment->id => [
                    'load_status' => 'loaded',
                    'loaded_at' => $manifest->loaded_at ?? now(),
                    'loaded_by' => $rider->user_id,
                ],
            ]);

            return $hub;
        });

        return response()->json([
            'message' => 'Drop-off confirmed. The parcel is now awaiting the delivery hub receive scan.',
            'data' => [
                'order_id' => $order->id,
                'tracking_number' => $order->shipment()->value('tracking_number'),
                'status' => 'awaiting_hub_receive_scan',
                'delivery_hub' => [
                    'id' => $hub->id,
                    'name' => $hub->hub_name,
                    'municipality' => $hub->municipality,
                    'province' => $hub->province,
                ],
            ],
        ]);
    }
}
