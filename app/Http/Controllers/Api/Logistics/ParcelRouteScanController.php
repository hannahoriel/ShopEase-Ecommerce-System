<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\OrderReturn;
use App\Models\Admin\Shipment;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsBranchRoute;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\LogisticsTransferManifest;
use App\Models\Logistics\LogisticsTransferManifestEvent;
use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\DeliveryHubParcelIssue;
use App\Models\Logistics\SortingCenterFleetArchive;
use App\Models\Logistics\SortingCenterVehicle;
use App\Models\Logistics\SortingCenterParcelIssue;
use App\Models\Rider\Rider;
use App\Services\LogisticsNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** Applies QR scans to the parcel's real shipment and order records. */
class ParcelRouteScanController extends Controller
{
    public function scan(Request $request): JsonResponse
    {
        $facility = $request->user()->role;
        abort_unless(in_array($facility, ['sorting_center', 'delivery_hub'], true), 403);

        $input = $request->validate([
            'tracking' => ['required', 'string', 'max:2048'],
            'action' => ['required', Rule::in(['receive', 'depart'])],
            'batch_key' => ['nullable', 'string', 'max:100'],
            'courier_registration_id' => ['nullable', 'integer'],
            'vehicle_id' => ['nullable', 'integer'],
            'courier_name' => ['nullable', 'string', 'max:150'],
            'vehicle_reference' => ['nullable', 'string', 'max:80'],
            'returning_manifest_id' => ['nullable', 'integer'],
            'scheduled_at' => ['nullable', 'date'],
            'rider_id' => ['nullable', 'integer'],
        ]);

        $return = $this->returnForScan($input['tracking']);
        if ($return) {
            return $this->scanReturn($request, $facility, $input, $return);
        }

        $shipment = DB::transaction(function () use ($request, $facility, $input): Shipment {
            $scannedCode = trim($input['tracking']);
            $path = parse_url($scannedCode, PHP_URL_PATH);
            $qrIdentifier = is_string($path) && $path !== ''
                ? rawurldecode(basename($path))
                : $scannedCode;

            $shipment = Shipment::query()
                ->where(fn ($query) => $query
                    ->where('tracking_number', $scannedCode)
                    ->orWhere('tracking_number', $qrIdentifier)
                    ->orWhere('scan_token', $scannedCode)
                    ->orWhere('scan_token', $qrIdentifier)
                    ->orWhereHas('order', fn ($order) => $order->where('order_number', $qrIdentifier)))
                ->lockForUpdate()
                ->first();
            abort_unless($shipment, 422, 'The QR code does not match this order or shipment.');
            $shipment->loadMissing(['order.seller', 'order.buyer.buyerProfile']);
            $order = $shipment->order;
            abort_unless($order && ! in_array($order->status, ['cancelled', 'refunded', 'delivered'], true), 409, 'This shipment is no longer active.');

            $seller = $order->seller;
            $buyer = $order->buyer?->buyerProfile;
            $sellerProvince = trim((string) $seller?->province);
            $sellerCity = trim((string) $seller?->municipality);
            $buyerProvince = trim((string) $buyer?->province);
            $buyerCity = trim((string) $buyer?->municipality);
            $stagedOutbound = $input['action'] === 'depart' && $facility === 'sorting_center' && ! empty($input['batch_key'])
                ? LogisticsTransferManifest::query()
                    ->where('batch_key', $input['batch_key'])
                    ->where('origin_type', 'sorting_center')
                    ->where('origin_id', LogisticsBranch::query()->where('user_id', $request->user()->id)->value('id'))
                    ->where('status', 'preparing')
                    ->whereHas('shipments', fn ($query) => $query->whereKey($shipment->id))
                    ->first()
                : null;

            if ($facility === 'delivery_hub') {
                $hub = LogisticsDeliveryHub::query()->where('user_id', $request->user()->id)->whereNull('archived_at')->firstOrFail();
                $pickupDropoffAtThisHub = $order->status === 'in_transit'
                    && $this->same($hub->province, $sellerProvince)
                    && $this->same($hub->municipality, $sellerCity)
                    && (int) $shipment->logistics_delivery_hub_id === (int) $hub->id;
                if ($input['action'] === 'receive') {
                    $hubLocation = $hub->hub_name.' · '.$hub->municipality.', '.$hub->province;
                    $latestScan = $shipment->scans()->orderByDesc('scanned_at')->orderByDesc('id')->first();
                    abort_if(
                        $latestScan?->status === 'Received at Delivery Hub' && $latestScan->location === $hubLocation,
                        409,
                        'Already scanned: this parcel has already been received at this delivery hub.'
                    );
                }
                if ($input['action'] === 'receive' && $pickupDropoffAtThisHub) {
                    $awaitingDropoffScan = LogisticsTransferManifest::query()
                        ->where('destination_type', 'delivery_hub')
                        ->where('destination_id', $hub->id)
                        ->where('origin_type', 'seller_collection')
                        ->where('status', 'loaded')
                        ->whereHas('shipments', fn ($query) => $query
                            ->whereKey($shipment->id)
                            ->whereNull('logistics_transfer_manifest_shipments.received_at'))
                        ->exists();
                    abort_unless($awaitingDropoffScan, 409, 'The rider must confirm drop-off before this parcel can be receive-scanned.');
                }
                $this->scanAtHub($shipment, $hub, $input['action'], $sellerProvince, $sellerCity, $seller?->barangay, $buyerProvince, $buyerCity, $buyer?->barangay, $input['rider_id'] ?? null);
                $location = $hub->hub_name.' · '.$hub->municipality.', '.$hub->province;
            } else {
                $center = LogisticsBranch::query()->where('user_id', $request->user()->id)->whereNull('archived_at')->firstOrFail();
                if (! $stagedOutbound) {
                    $this->scanAtCenter($shipment, $center, $input['action'], $buyerProvince, $buyerCity);
                }
                $location = $center->name.' · '.$center->province;
            }

            $facilityId = $facility === 'delivery_hub' ? $hub->id : $center->id;
            if ($input['action'] === 'receive' && $shipment->order->status === 'in_transit') {
                $inbound = LogisticsTransferManifest::query()
                    ->where('destination_type', $facility)->where('destination_id', $facilityId)
                    ->where('status', 'loaded')
                    ->where(function ($manifestQuery) use ($shipment): void {
                        $manifestQuery->whereHas('shipments', fn ($query) => $query
                            ->whereKey($shipment->id)
                            ->where('logistics_transfer_manifest_shipments.load_status', 'loaded')
                            ->whereNull('logistics_transfer_manifest_shipments.received_at'))
                            // Earlier rider drop-offs created a loaded manifest but
                            // left the parcel pivot at its default pending state.
                            ->orWhere(function ($legacyPickup) use ($shipment): void {
                                $legacyPickup->where('origin_type', 'seller_collection')
                                    ->where('batch_key', 'pickup-dropoff-'.$shipment->id)
                                    ->whereHas('shipments', fn ($query) => $query
                                        ->whereKey($shipment->id)
                                        ->where('logistics_transfer_manifest_shipments.load_status', 'pending')
                                        ->whereNull('logistics_transfer_manifest_shipments.received_at'));
                            });
                    })
                    ->lockForUpdate()->first();
                abort_unless($inbound, 409, 'This parcel is not part of an active incoming manifest for this facility.');
                abort_unless(
                    ! $inbound->courier_registration_id || $inbound->arrival_confirmed_at,
                    409,
                    'The courier must confirm arrival before receiving scans begin.',
                );
                $inbound->shipments()->updateExistingPivot($shipment->id, [
                    'load_status' => 'loaded',
                    'loaded_at' => $inbound->loaded_at ?? now(),
                    'received_at' => now(),
                    'received_by' => $request->user()->id,
                ]);
                LogisticsTransferManifestEvent::query()->create([
                    'transfer_manifest_id' => $inbound->id,
                    'shipment_id' => $shipment->id,
                    'actor_user_id' => $request->user()->id,
                    'event_type' => 'parcel_received',
                    'occurred_at' => now(),
                ]);
                if (! $inbound->shipments()
                    ->wherePivot('load_status', 'loaded')
                    ->wherePivotNull('received_at')
                    ->exists()) {
                    $inbound->update(['status' => 'arrived', 'arrived_at' => now()]);
                    LogisticsTransferManifestEvent::query()->create([
                        'transfer_manifest_id' => $inbound->id,
                        'actor_user_id' => $request->user()->id,
                        'event_type' => 'receiving_reconciled',
                        'occurred_at' => now(),
                    ]);
                }
            } elseif ($shipment->order->status === 'in_transit') {
                $destinationType = $shipment->logistics_delivery_hub_id ? 'delivery_hub' : 'sorting_center';
                $destinationId = $shipment->logistics_delivery_hub_id ?: $shipment->logistics_branch_id;
                abort_unless($destinationId, 422, 'The next destination facility could not be resolved.');
                $courierId = $input['courier_registration_id'] ?? null;
                $vehicleId = $input['vehicle_id'] ?? null;
                $hasCourierAssignment = $courierId !== null || $vehicleId !== null;
                if (($facility === 'sorting_center' && $hasCourierAssignment)
                    || ($facility === 'delivery_hub' && $destinationType === 'sorting_center')) {
                    abort_unless(! empty($input['scheduled_at']), 422, 'Choose a scheduled dispatch date and time.');
                }
                $batchKey = $input['batch_key'] ?: 'scan-'.$shipment->id.'-'.now()->format('YmdHisv');
                $trip = null;
                $requiresCourierDeparture = $facility === 'sorting_center';
                if (! empty($input['returning_manifest_id'])) {
                    $trip = LogisticsTransferManifest::query()->lockForUpdate()->findOrFail($input['returning_manifest_id']);
                    abort_unless($trip->status === 'arrived' && $trip->destination_type === $facility && (int) $trip->destination_id === (int) $facilityId, 422, 'The selected inbound truck has not arrived at this facility.');
                    abort_unless($trip->origin_type === $destinationType && (int) $trip->origin_id === (int) $destinationId, 422, 'Return cargo must be routed back to the truck’s originating facility.');
                    $existingReturn = LogisticsTransferManifest::query()->where('returning_manifest_id', $trip->id)->first();
                    abort_if($existingReturn && $existingReturn->batch_key !== $batchKey, 409, 'This truck already has a return cargo assignment.');
                } else {
                    if ($facility === 'sorting_center' && $destinationType === 'sorting_center' && $hasCourierAssignment) {
                        $arrivedTruck = LogisticsTransferManifest::query()
                            ->where('origin_type', 'sorting_center')->where('origin_id', $destinationId)
                            ->where('destination_type', 'sorting_center')->where('destination_id', $facilityId)
                            ->where('status', 'arrived')
                            ->whereNotIn('id', LogisticsTransferManifest::query()->whereNotNull('returning_manifest_id')->select('returning_manifest_id'))
                            ->exists();
                        abort_unless(! $arrivedTruck, 422, 'This sorting-center truck must be assigned return cargo to its origin center.');
                    }
                    if ($facility === 'sorting_center') {
                        if ($hasCourierAssignment) {
                            abort_unless($courierId !== null && $vehicleId !== null, 422, 'Select both an approved courier and their registered vehicle.');
                            $centerId = $facilityId;
                            $courier = CourierRegistration::query()->where('sorting_center_id', $centerId)->where('status', 'approved')->lockForUpdate()->find($courierId);
                            $vehicle = SortingCenterVehicle::query()->where('sorting_center_id', $centerId)->lockForUpdate()->find($vehicleId);
                            abort_unless($courier && $vehicle && mb_strtolower($courier->plate_number) === mb_strtolower($vehicle->plate_number), 422, 'Select an approved courier and the vehicle registered to that courier.');
                            abort_unless(mb_strtolower((string) $vehicle->availability) === 'available', 422, 'The assigned truck is not available.');
                            abort_if(LogisticsTransferManifest::query()
                                ->whereIn('status', ['preparing', 'loaded'])
                                ->where(function ($query) use ($batchKey): void {
                                    $query->whereNull('batch_key')->orWhere('batch_key', '!=', $batchKey);
                                })
                                ->where(function ($query) use ($courierId, $vehicleId): void {
                                    $query->where('courier_registration_id', $courierId)
                                        ->orWhere('vehicle_id', $vehicleId);
                                })
                                ->where(fn ($query) => $query->whereNull('scheduled_at')->orWhere('scheduled_at', $input['scheduled_at']))
                                ->exists(), 409, 'This courier or vehicle is already on another active trip.');
                        }
                    } elseif ($facility === 'delivery_hub' && $destinationType === 'sorting_center') {
                        abort_unless($courierId !== null && $vehicleId !== null, 422, 'Select an available Sorting Center courier and registered vehicle.');
                        $courier = CourierRegistration::query()
                            ->where('sorting_center_id', $destinationId)
                            ->where('status', 'approved')
                            ->lockForUpdate()
                            ->find($courierId);
                        $vehicle = SortingCenterVehicle::query()
                            ->where('sorting_center_id', $destinationId)
                            ->lockForUpdate()
                            ->find($vehicleId);
                        abort_unless(
                            $courier && $vehicle
                            && mb_strtolower($courier->plate_number) === mb_strtolower($vehicle->plate_number),
                            422,
                            'Select an approved Sorting Center courier and their registered vehicle.',
                        );
                        abort_unless(mb_strtolower((string) $vehicle->availability) === 'available', 422, 'The assigned truck is not available.');
                        abort_if(LogisticsTransferManifest::query()
                            ->whereIn('status', ['preparing', 'loaded'])
                            ->where(function ($query) use ($batchKey): void {
                                $query->whereNull('batch_key')->orWhere('batch_key', '!=', $batchKey);
                            })
                            ->where(function ($query) use ($courierId, $vehicleId): void {
                                $query->where('courier_registration_id', $courierId)
                                    ->orWhere('vehicle_id', $vehicleId);
                            })
                            ->where(fn ($query) => $query->whereNull('scheduled_at')->orWhere('scheduled_at', $input['scheduled_at']))
                            ->exists(), 409, 'This courier or vehicle is already on another active trip.');
                    }
                }
                $outbound = LogisticsTransferManifest::query()->firstOrCreate(
                    ['batch_key' => $batchKey],
                    [
                        'origin_type' => $facility, 'origin_id' => $facilityId,
                        'destination_type' => $destinationType, 'destination_id' => $destinationId,
                        'courier_registration_id' => $trip?->courier_registration_id ?? $input['courier_registration_id'] ?? null,
                        'vehicle_id' => $trip?->vehicle_id ?? $input['vehicle_id'] ?? null,
                        'courier_name' => $trip?->courier_name ?? $input['courier_name'] ?? null,
                        'vehicle_reference' => $trip?->vehicle_reference ?? $input['vehicle_reference'] ?? null,
                        'returning_manifest_id' => $trip?->id,
                        'status' => $requiresCourierDeparture ? 'preparing' : 'loaded',
                        'loaded_at' => $requiresCourierDeparture ? null : now(),
                        'departed_at' => $requiresCourierDeparture ? null : now(),
                        'scheduled_at' => $input['scheduled_at'] ?? null,
                    ],
                );
                abort_unless(
                    $outbound->origin_type === $facility
                    && (int) $outbound->origin_id === (int) $facilityId
                    && $outbound->destination_type === $destinationType
                    && (int) $outbound->destination_id === (int) $destinationId,
                    409,
                    'This batch is already assigned to another route.',
                );
                if ($trip && ! $outbound->returning_manifest_id) {
                    // The parcel scan creates a staged manifest before the
                    // operator chooses the returning truck. Bind that inbound
                    // truck when Confirm Loaded is submitted.
                    $outbound->update(['returning_manifest_id' => $trip->id]);
                }
                abort_unless(
                    (int) $outbound->returning_manifest_id === (int) ($trip?->id),
                    409,
                    'This batch is already assigned to another returning truck.',
                );
                if ($requiresCourierDeparture && ! empty($input['courier_registration_id'])) {
                    $outbound->update([
                        'courier_registration_id' => $input['courier_registration_id'],
                        'vehicle_id' => $input['vehicle_id'],
                        'courier_name' => $input['courier_name'],
                        'vehicle_reference' => $input['vehicle_reference'],
                        'scheduled_at' => $input['scheduled_at'],
                        'status' => 'preparing',
                        'loaded_at' => now(),
                    ]);
                }
                abort_if(
                    LogisticsTransferManifest::query()
                        ->whereIn('status', ['preparing', 'loaded'])
                        ->whereKeyNot($outbound->id)
                        ->whereHas('shipments', fn ($query) => $query->whereKey($shipment->id))
                        ->exists(),
                    409,
                    'This parcel is already assigned to another active transport manifest.',
                );
                $outbound->shipments()->syncWithoutDetaching([
                    $shipment->id => [
                        'load_status' => $requiresCourierDeparture && empty($input['courier_registration_id']) ? 'pending' : 'loaded',
                        'loaded_at' => $requiresCourierDeparture && empty($input['courier_registration_id']) ? null : now(),
                        'loaded_by' => $requiresCourierDeparture && empty($input['courier_registration_id']) ? null : $request->user()->id,
                    ],
                ]);
                if (! $requiresCourierDeparture && $outbound->wasRecentlyCreated) {
                    LogisticsTransferManifestEvent::query()->create([
                        'transfer_manifest_id' => $outbound->id,
                        'actor_user_id' => $request->user()->id,
                        'event_type' => 'departure_confirmed',
                        'occurred_at' => now(),
                    ]);
                }
                LogisticsTransferManifestEvent::query()->create([
                    'transfer_manifest_id' => $outbound->id,
                    'shipment_id' => $shipment->id,
                    'actor_user_id' => $request->user()->id,
                    'event_type' => $requiresCourierDeparture && empty($input['courier_registration_id'])
                        ? 'departure_staged'
                        : 'parcel_loaded',
                    'occurred_at' => now(),
                ]);
            }

            $shipment->current_location = $location;
            $shipment->location_updated_at = now();
            $shipment->save();

            $label = $input['action'] === 'receive'
                ? 'Received at '.($facility === 'delivery_hub' ? 'Delivery Hub' : 'Sorting Center')
                : ($order->status === 'out_for_delivery'
                    ? 'Out for Delivery'
                    : 'In Transit to '.($shipment->logistics_delivery_hub_id ? 'Delivery Hub' : 'Sorting Center'));
            $shipment->scans()->create(['status' => $label, 'location' => $location, 'scanned_at' => now()]);

            return $shipment->fresh(['order', 'scans']);
        });

        $logisticsUser = $shipment->order?->logisticsCompany?->user;
        if ($logisticsUser) {
            app(LogisticsNotificationService::class)->send(
                $logisticsUser,
                'parcel_updates',
                'Parcel movement updated',
                "Shipment {$shipment->tracking_number} was updated: {$shipment->current_location}.",
                '/logistics/parcel-tracking',
            );
        }

        return response()->json([
            'message' => 'Parcel scan recorded.',
            'shipment' => [
                'id' => $shipment->id,
                'tracking' => $shipment->tracking_number,
                'order_status' => $shipment->order->status,
                'current_location' => $shipment->current_location,
                'logistics_branch_id' => $shipment->logistics_branch_id,
                'logistics_delivery_hub_id' => $shipment->logistics_delivery_hub_id,
                'rider_id' => $shipment->rider_id,
                'history' => $shipment->scans->map(fn ($scan) => ['status' => $scan->status, 'location' => $scan->location, 'scanned_at' => $scan->scanned_at?->toISOString()]),
            ],
        ]);
    }

    private function returnForScan(string $code): ?OrderReturn
    {
        $raw = trim($code);
        $path = parse_url($raw, PHP_URL_PATH);
        $identifier = is_string($path) && $path !== '' ? rawurldecode(basename($path)) : $raw;

        return OrderReturn::query()
            ->where('return_type', 'return')
            ->whereIn('status', ['accepted', 'refunded'])
            ->whereIn('shipping_status', ['scheduled', 'picked_up', 'received_at_delivery_hub', 'staged_for_return_dispatch', 'in_transit_to_sorting_center', 'received_at_intermediate_sorting_center', 'received_at_sorting_center', 'in_transit_to_seller_delivery_hub', 'received_at_seller_delivery_hub', 'out_for_delivery_to_seller'])
            ->where(fn ($query) => $query
                ->where('tracking_number', $raw)
                ->orWhere('tracking_number', $identifier)
                ->orWhereHas('order.shipment', fn ($shipment) => $shipment
                    ->where('tracking_number', $raw)
                    ->orWhere('tracking_number', $identifier)
                    ->orWhere('scan_token', $raw)
                    ->orWhere('scan_token', $identifier))
                ->orWhereHas('order', fn ($order) => $order->where('order_number', $identifier)))
            ->with(['order.seller', 'order.shipment', 'order.buyer.buyerProfile', 'pickupRider'])
            ->first();
    }

    private function scanReturn(Request $request, string $facility, array $input, OrderReturn $return): JsonResponse
    {
        if ($facility === 'delivery_hub') {
            $result = DB::transaction(function () use ($request, $return, $input): array {
                $hub = LogisticsDeliveryHub::query()->where('user_id', $request->user()->id)
                    ->whereNull('archived_at')->firstOrFail();
                $locked = OrderReturn::query()->whereKey($return->id)->lockForUpdate()->firstOrFail();
                $order = $locked->order()->with(['seller', 'shipment', 'buyer.buyerProfile'])->firstOrFail();
                $shipment = Shipment::query()->where('order_id', $order->id)->lockForUpdate()->firstOrFail();
                $buyer = $order->buyer?->buyerProfile;
                $buyerProvince = trim((string) ($order->delivery_province ?: $buyer?->province));
                $destination = $buyerProvince !== ''
                    ? $this->activeBranch($buyerProvince, $order->logistics_id ?: $hub->logisticsBranch?->logistics_id)
                    : null;
                $destination ??= LogisticsBranch::query()->whereKey($hub->logistics_branch_id)
                    ->where('status', 'active')->whereNull('archived_at')->first();
                abort_unless($destination, 422, 'No active Sorting Center is available for this return parcel.');
                $location = $hub->hub_name.' · '.$hub->municipality.', '.$hub->province;
                if ($input['action'] === 'receive') {
                    if ($locked->shipping_status === 'picked_up') {
                        $profile = $order->buyer?->buyerProfile;
                        $province = trim((string) ($order->delivery_province ?: $profile?->province));
                        $municipality = trim((string) ($order->delivery_municipality ?: $profile?->municipality));
                        if ($locked->return_delivery_hub_id) {
                            abort_unless((int) $locked->return_delivery_hub_id === (int) $hub->id, 404);
                        } else {
                            abort_unless($this->same($hub->province, $province) && $this->same($hub->municipality, $municipality), 404);
                            $locked->return_delivery_hub_id = $hub->id;
                        }
                        $locked->forceFill(['shipping_status' => 'received_at_delivery_hub'])->save();
                        $shipment->update(['logistics_branch_id' => null, 'logistics_delivery_hub_id' => $hub->id,
                            'current_location' => $location, 'location_updated_at' => now()]);
                        $shipment->scans()->create(['status' => 'Received at Delivery Hub', 'location' => $location, 'scanned_at' => now()]);
                        $locked->events()->create(['status' => 'hub_received', 'location' => $location, 'scanned_at' => now()]);
                        return ['tracking' => $locked->tracking_number, 'status' => $locked->shipping_status];
                    }

                    abort_unless($locked->shipping_status === 'in_transit_to_seller_delivery_hub', 409,
                        'This return is not in transit to this seller delivery hub.');
                    $manifest = LogisticsTransferManifest::query()->where('order_return_id', $locked->id)
                        ->where('destination_type', 'delivery_hub')->where('destination_id', $hub->id)
                        ->where('status', 'loaded')->latest('id')->lockForUpdate()->firstOrFail();
                    $pivot = $manifest->shipments()->whereKey($shipment->id)->first();
                    abort_unless($pivot && $pivot->pivot?->received_at === null, 409, 'This return has already been received at the seller delivery hub.');
                    $manifest->shipments()->updateExistingPivot($shipment->id, ['load_status' => 'loaded', 'received_at' => now(), 'received_by' => $request->user()->id]);
                    $manifest->update(['status' => 'arrived', 'arrived_at' => now()]);
                    $locked->forceFill(['shipping_status' => 'received_at_seller_delivery_hub'])->save();
                    $shipment->update(['current_location' => $location, 'location_updated_at' => now()]);
                    $shipment->scans()->create(['status' => 'Received at Delivery Hub', 'location' => $location, 'scanned_at' => now()]);
                    $locked->events()->create(['status' => 'received_at_seller_delivery_hub', 'location' => $location, 'scanned_at' => now()]);
                    return ['tracking' => $locked->tracking_number, 'status' => $locked->shipping_status, 'manifest_id' => $manifest->id];
                }

                if ($locked->shipping_status === 'received_at_seller_delivery_hub') {
                    $seller = $order->seller;
                    abort_unless($seller && $this->same($hub->province, $seller->province)
                        && $this->same($hub->municipality, $seller->municipality), 409,
                        'This is not the delivery hub serving the seller address.');
                    $barangay = trim((string) $seller->barangay);
                    $rider = $this->riderFor($hub, $barangay, 'delivery', isset($input['rider_id']) ? (int) $input['rider_id'] : null);
                    abort_unless($rider, 422, 'No active delivery rider is assigned to the seller barangay.');
                    LogisticsTransferManifest::query()->where('order_return_id', $locked->id)
                        ->where('destination_type', 'delivery_hub')->where('destination_id', $hub->id)
                        ->where('status', 'arrived')->latest('id')->first()
                        ?->update(['status' => 'out_for_delivery']);
                    $locked->forceFill(['shipping_status' => 'out_for_delivery_to_seller', 'delivery_rider_id' => $rider->id])->save();
                    $shipment->update(['rider_id' => $rider->id, 'current_location' => $location, 'location_updated_at' => now()]);
                    $shipment->scans()->create(['status' => 'Out for Delivery to Seller', 'location' => $location, 'scanned_at' => now()]);
                    $locked->events()->create(['status' => 'out_for_delivery_to_seller', 'location' => $location.' · '.$rider->first_name.' '.$rider->last_name, 'scanned_at' => now()]);
                    return ['tracking' => $locked->tracking_number, 'status' => $locked->shipping_status, 'rider_id' => $rider->id];
                }

                abort_unless($locked->shipping_status === 'received_at_delivery_hub'
                    && (int) $locked->return_delivery_hub_id === (int) $hub->id,
                    409, 'Receive-scan this return at the buyer delivery hub before dispatch.');
                abort_unless(! empty($input['scheduled_at']), 422, 'Choose a scheduled dispatch date and time.');
                abort_unless(! empty($input['courier_name']) && ! empty($input['vehicle_reference']), 422,
                    'Select a transfer courier and vehicle before dispatch.');

                $dispatchKey = trim((string) ($input['batch_key'] ?? '')) ?: 'buyer-return-dispatch-'.$locked->id;
                $manifest = LogisticsTransferManifest::query()->create([
                    'order_return_id' => $locked->id,
                    'batch_key' => substr($dispatchKey.'-buyer-return-'.$locked->id, 0, 100),
                    'origin_type' => 'delivery_hub', 'origin_id' => $hub->id,
                    'destination_type' => 'sorting_center', 'destination_id' => $destination->id,
                    'courier_registration_id' => $input['courier_registration_id'] ?? null,
                    'vehicle_id' => $input['vehicle_id'] ?? null,
                    'courier_name' => $input['courier_name'], 'vehicle_reference' => $input['vehicle_reference'],
                    'status' => 'loaded', 'scheduled_at' => $input['scheduled_at'], 'loaded_at' => now(), 'departed_at' => now(),
                ]);
                $shipment->update(['logistics_branch_id' => $destination->id, 'logistics_delivery_hub_id' => null,
                    'current_location' => $location, 'location_updated_at' => now()]);
                $manifest->shipments()->syncWithoutDetaching([$shipment->id => ['load_status' => 'loaded', 'loaded_at' => now(),
                    'loaded_by' => $request->user()->id, 'received_at' => null, 'received_by' => null]]);
                $locked->forceFill(['shipping_status' => 'in_transit_to_sorting_center'])->save();
                $shipment->scans()->create(['status' => 'In Transit to Sorting Center', 'location' => $location, 'scanned_at' => now()]);
                $locked->events()->create(['status' => 'dispatched_to_buyer_sorting_center', 'location' => $location.' · '.$manifest->batch_key, 'scanned_at' => now()]);
                LogisticsTransferManifestEvent::query()->create(['transfer_manifest_id' => $manifest->id, 'shipment_id' => $shipment->id,
                    'actor_user_id' => $request->user()->id, 'event_type' => 'return_to_buyer_sorting_center', 'occurred_at' => now()]);
                return ['tracking' => $locked->tracking_number, 'manifest_id' => $manifest->id, 'destination' => $destination->name];
            });

            return response()->json([
                'message' => $input['action'] === 'receive'
                    ? 'Return parcel received at the buyer delivery hub and is ready for dispatch.'
                    : ((($result['status'] ?? null) === 'out_for_delivery_to_seller')
                        ? 'Return parcel is out for delivery to the seller.'
                        : 'Return parcel dispatched to the buyer Sorting Center.'),
                'return' => $result,
            ]);
        }

        $result = DB::transaction(function () use ($request, $return, $input): array {
            $center = LogisticsBranch::query()->where('user_id', $request->user()->id)
                ->whereNull('archived_at')->firstOrFail();
            $locked = OrderReturn::query()->whereKey($return->id)->lockForUpdate()->firstOrFail();
            $shipment = Shipment::query()->where('order_id', $locked->order_id)->lockForUpdate()->firstOrFail();
            $order = $locked->order()->with(['seller', 'buyer.buyerProfile'])->firstOrFail();
            $seller = $order->seller;
            $sellerProvince = trim((string) $seller?->province);
            $sellerMunicipality = trim((string) $seller?->municipality);
            abort_unless($sellerProvince !== '' && $sellerMunicipality !== '', 422, 'Seller delivery province and municipality are required for return routing.');
            $location = $center->name.' · '.$center->province;

            if ($input['action'] === 'receive') {
                abort_unless($locked->shipping_status === 'in_transit_to_sorting_center', 409,
                    'This return is not in transit to a Sorting Center.');
                $manifest = LogisticsTransferManifest::query()->where('order_return_id', $locked->id)
                    ->where('destination_type', 'sorting_center')->where('destination_id', $center->id)
                    ->where('status', 'loaded')->latest('id')->lockForUpdate()->firstOrFail();
                $pivot = $manifest->shipments()->whereKey($shipment->id)->first();
                abort_unless($pivot && $pivot->pivot?->received_at === null, 409,
                    'This return has already been receive-scanned at the Sorting Center.');
                $manifest->shipments()->updateExistingPivot($shipment->id, [
                    'load_status' => 'loaded', 'loaded_at' => $manifest->loaded_at ?? now(),
                    'received_at' => now(), 'received_by' => $request->user()->id,
                ]);
                $manifest->update(['status' => 'arrived', 'arrived_at' => now()]);
                $sellerReached = $this->same($center->province, $sellerProvince);
                $returnStatus = $sellerReached ? 'received_at_sorting_center' : 'received_at_intermediate_sorting_center';
                $locked->forceFill(['shipping_status' => $returnStatus])->save();
                $shipment->update(['current_location' => $location, 'location_updated_at' => now()]);
                $shipment->scans()->create(['status' => 'Received at Sorting Center', 'location' => $location, 'scanned_at' => now()]);
                $locked->events()->create(['status' => $returnStatus, 'location' => $location, 'scanned_at' => now()]);
                LogisticsTransferManifestEvent::query()->create([
                    'transfer_manifest_id' => $manifest->id, 'shipment_id' => $shipment->id,
                    'actor_user_id' => $request->user()->id, 'event_type' => 'return_received_at_sorting_center', 'occurred_at' => now(),
                ]);
                return ['tracking' => $locked->tracking_number, 'manifest_id' => $manifest->id, 'status' => $returnStatus];
            }

            abort_unless(in_array($locked->shipping_status, ['received_at_intermediate_sorting_center', 'received_at_sorting_center', 'staged_for_return_dispatch'], true),
                409, 'Receive-scan this return at the current Sorting Center before departure.');
            $inbound = LogisticsTransferManifest::query()->where('order_return_id', $locked->id)
                ->where('destination_type', 'sorting_center')->where('destination_id', $center->id)
                ->where('status', 'arrived')->latest('id')->first();
            abort_unless($inbound, 409, 'This return has not been received at this Sorting Center.');

            if ($this->same($center->province, $sellerProvince)) {
                $hub = LogisticsDeliveryHub::query()->where('status', 'active')->whereNull('archived_at')
                    ->where('logistics_branch_id', $center->id)
                    ->whereRaw('LOWER(municipality) = ?', [mb_strtolower($sellerMunicipality)])->first();
                abort_unless($hub, 422, 'No active seller delivery hub is configured for this municipality.');
                $destinationType = 'delivery_hub'; $destinationId = $hub->id; $destinationName = $hub->hub_name;
            } else {
                $nextCenter = $this->nextBranchToward($center, $sellerProvince);
                abort_unless($nextCenter && $nextCenter->id !== $center->id, 422, 'No next Sorting Center is configured toward the seller province.');
                $destinationType = 'sorting_center'; $destinationId = $nextCenter->id; $destinationName = $nextCenter->name;
            }

            $batchKey = trim((string) ($input['batch_key'] ?? '')) ?: 'return-sc-'.$center->id.'-'.$locked->id.'-'.now()->format('YmdHis');
            $outbound = LogisticsTransferManifest::query()->where('batch_key', $batchKey)->lockForUpdate()->first();
            if (empty($input['courier_registration_id']) || empty($input['vehicle_id'])) {
                if (! $outbound) {
                    $outbound = LogisticsTransferManifest::query()->create([
                        'order_return_id' => $locked->id, 'batch_key' => $batchKey,
                        'origin_type' => 'sorting_center', 'origin_id' => $center->id,
                        'destination_type' => $destinationType, 'destination_id' => $destinationId,
                        'status' => 'preparing',
                    ]);
                    $outbound->shipments()->syncWithoutDetaching([$shipment->id => ['load_status' => 'pending']]);
                }
                $locked->forceFill(['shipping_status' => 'staged_for_return_dispatch'])->save();
                $shipment->scans()->create(['status' => 'Return Departure Staged', 'location' => $location, 'scanned_at' => now()]);
                return ['tracking' => $locked->tracking_number, 'manifest_id' => $outbound->id, 'status' => 'staged_for_return_dispatch', 'destination' => $destinationName];
            }

            abort_unless(! empty($input['scheduled_at']), 422, 'Choose a scheduled dispatch date and time.');
            $returningTrip = null;
            $courier = null;
            $vehicle = null;
            if (! empty($input['returning_manifest_id'])) {
                $returningTrip = LogisticsTransferManifest::query()->lockForUpdate()->findOrFail($input['returning_manifest_id']);
                abort_unless(
                    $returningTrip->status === 'arrived'
                    && $returningTrip->destination_type === 'sorting_center'
                    && (int) $returningTrip->destination_id === (int) $center->id,
                    422,
                    'The selected inbound truck has not arrived at this Sorting Center.',
                );
                abort_unless(
                    $returningTrip->origin_type === $destinationType
                    && (int) $returningTrip->origin_id === (int) $destinationId,
                    422,
                    'Return cargo must be routed back to the inbound truck’s originating facility.',
                );
                $existingReturn = LogisticsTransferManifest::query()
                    ->where('returning_manifest_id', $returningTrip->id)
                    ->first();
                abort_if($existingReturn && $existingReturn->batch_key !== $batchKey, 409,
                    'This truck already has a return cargo assignment.');
            } else {
                $courier = CourierRegistration::query()->where('sorting_center_id', $center->id)->where('status', 'approved')
                    ->lockForUpdate()->find((int) $input['courier_registration_id']);
                $vehicle = SortingCenterVehicle::query()->where('sorting_center_id', $center->id)->lockForUpdate()->find((int) $input['vehicle_id']);
                abort_unless($courier && $vehicle && mb_strtolower($courier->plate_number) === mb_strtolower($vehicle->plate_number), 422,
                    'Select an approved Sorting Center courier and their registered vehicle.');
                abort_unless(mb_strtolower((string) $vehicle->availability) === 'available', 422, 'The assigned truck is not available.');
            }
            $outbound ??= LogisticsTransferManifest::query()->create([
                'order_return_id' => $locked->id, 'batch_key' => $batchKey,
                'origin_type' => 'sorting_center', 'origin_id' => $center->id,
                'destination_type' => $destinationType, 'destination_id' => $destinationId,
                'status' => 'preparing',
            ]);
            abort_unless($outbound->origin_type === 'sorting_center' && (int) $outbound->origin_id === (int) $center->id
                && $outbound->destination_type === $destinationType && (int) $outbound->destination_id === (int) $destinationId,
                409, 'This return is staged for a different route.');
            if ($returningTrip && ! $outbound->returning_manifest_id) {
                $outbound->update(['returning_manifest_id' => $returningTrip->id]);
            }
            abort_unless((int) $outbound->returning_manifest_id === (int) ($returningTrip?->id), 409,
                'This return batch is assigned to another inbound truck.');
            $outbound->update([
                'courier_registration_id' => $returningTrip?->courier_registration_id ?? $courier?->id,
                'vehicle_id' => $returningTrip?->vehicle_id ?? $vehicle?->id,
                'courier_name' => $returningTrip?->courier_name ?? $input['courier_name'] ?? $courier?->name,
                'vehicle_reference' => $returningTrip?->vehicle_reference ?? $input['vehicle_reference'] ?? $vehicle?->plate_number,
                'scheduled_at' => $input['scheduled_at'], 'status' => 'loaded', 'loaded_at' => now(), 'departed_at' => now(),
            ]);
            $outbound->shipments()->syncWithoutDetaching([$shipment->id => [
                'load_status' => 'loaded', 'loaded_at' => now(), 'loaded_by' => $request->user()->id,
                'received_at' => null, 'received_by' => null,
            ]]);
            $shipment->update([
                'logistics_branch_id' => $destinationType === 'sorting_center' ? $destinationId : $center->id,
                'logistics_delivery_hub_id' => $destinationType === 'delivery_hub' ? $destinationId : null,
                'current_location' => $location, 'location_updated_at' => now(),
            ]);
            $returnStatus = $destinationType === 'delivery_hub' ? 'in_transit_to_seller_delivery_hub' : 'in_transit_to_sorting_center';
            $locked->forceFill(['shipping_status' => $returnStatus])->save();
            $shipment->scans()->create([
                'status' => $destinationType === 'delivery_hub' ? 'In Transit to Delivery Hub' : 'In Transit to Sorting Center',
                'location' => $location, 'scanned_at' => now(),
            ]);
            $locked->events()->create(['status' => $returnStatus, 'location' => $location.' · '.$outbound->batch_key, 'scanned_at' => now()]);
            LogisticsTransferManifestEvent::query()->create([
                'transfer_manifest_id' => $outbound->id, 'shipment_id' => $shipment->id,
                'actor_user_id' => $request->user()->id, 'event_type' => 'return_loaded_and_dispatched', 'occurred_at' => now(),
            ]);
            return ['tracking' => $locked->tracking_number, 'manifest_id' => $outbound->id, 'status' => $returnStatus, 'destination' => $destinationName];
        });

        return response()->json(['message' => $input['action'] === 'receive'
            ? 'Return parcel received at the Sorting Center.'
            : (($result['status'] ?? null) === 'staged_for_return_dispatch' ? 'Return parcel staged for Loading.' : 'Return parcel loaded and dispatched toward the seller.'),
            'return' => $result]);
    }

    public function queue(Request $request): JsonResponse
    {
        $facility = $request->user()->role;
        abort_unless(in_array($facility, ['sorting_center', 'delivery_hub'], true), 403);
        $node = $facility === 'delivery_hub'
            ? LogisticsDeliveryHub::query()->where('user_id', $request->user()->id)->whereNull('archived_at')->firstOrFail()
            : LogisticsBranch::query()->where('user_id', $request->user()->id)->whereNull('archived_at')->firstOrFail();
        $id = $node->id;
        $deliveryRiders = $facility === 'delivery_hub'
            ? Rider::query()->where('logistics_branch_id', $node->logistics_branch_id)
                ->where('registration_status', 'active')
                ->whereHas('user', fn ($query) => $query->where('registration_status', 'active'))
                ->whereRaw("LOWER(REPLACE(REPLACE(logistics_operation, ' ', ''), '-', '')) LIKE ?", ['%delivery%'])
                ->where(function ($query) use ($node): void {
                    $query->whereRaw('LOWER(municipal_hub) = ?', [mb_strtolower($node->hub_name)])
                        ->orWhereRaw('LOWER(municipal_hub) = ?', [mb_strtolower($node->municipality)]);
                })->orderBy('id')->get()->map(fn (Rider $rider): array => [
                    'id' => $rider->id,
                    'name' => trim($rider->first_name.' '.$rider->last_name),
                    'barangay' => $rider->assignment_barangay ?: $rider->barangay,
                    'role' => $rider->logistics_operation,
                    'vehicle' => trim(collect([$rider->vehicle, $rider->plate_number])->filter()->implode(' · ')),
                    'phone' => $rider->contact_no,
                    'load' => $rider->shipments()->whereHas('order', fn ($query) => $query->where('status', 'out_for_delivery'))->count(),
                ])->values()
            : collect();
        $transfers = LogisticsTransferManifest::query()
            ->where('destination_type', $facility)->where('destination_id', $id)
            ->where(function ($query): void {
                $query->whereIn('status', ['loaded', 'arrived'])
                    ->orWhere(fn ($pickup) => $pickup
                        ->where('origin_type', 'seller_collection')
                        ->where('status', 'preparing'));
            })
            ->with(['shipments.order.seller.user', 'shipments.order.buyer.buyerProfile', 'shipments.order.items.product', 'shipments.rider.user', 'returnTrip', 'orderReturn.order.seller.user', 'orderReturn.order.buyer.buyerProfile', 'orderReturn.order.items.product', 'courierRegistration'])
            ->latest('loaded_at')->get();

        $parcels = [];
        $manifests = [];
        foreach ($transfers as $transfer) {
            $members = [];
            foreach ($transfer->shipments as $shipment) {
                // Facility ownership is updated when the courier confirms
                // departure, so the destination only lists dispatched parcels.
                $isAtFacility = $facility === 'delivery_hub'
                    ? (int) $shipment->logistics_delivery_hub_id === (int) $id
                    : (int) $shipment->logistics_branch_id === (int) $id && $shipment->logistics_delivery_hub_id === null;
                if (! $isAtFacility) continue;
                $received = $shipment->pivot->received_at !== null;
                $parcelId = $transfer->order_return_id ? 'return-'.$transfer->order_return_id : 'shipment-'.$shipment->id;
                $members[] = $parcelId;
                $parcelStatus = $shipment->order?->status === 'out_for_delivery' ? 'out-for-delivery' : ($received ? 'received' : 'incoming');
                $payload = $this->parcelPayload($shipment, $parcelId, $parcelStatus, $facility === 'sorting_center' ? $id : null, $transfer->id);
                $payload['receivedAt'] = $shipment->pivot->received_at
                    ? \Illuminate\Support\Carbon::parse($shipment->pivot->received_at)->toISOString()
                    : null;
                $payload['receivedFrom'] = $transfer->origin_type === 'seller_collection'
                    ? (collect([
                        $shipment->order?->seller?->store_name
                            ?: trim(($shipment->order?->seller?->first_name ?? '').' '.($shipment->order?->seller?->last_name ?? '')),
                        $shipment->order?->seller?->barangay,
                        $shipment->order?->seller?->municipality,
                    ])->filter()->implode(' · ') ?: 'Seller pickup')
                    : $this->facilityName($transfer->origin_type, (int) $transfer->origin_id);
                if ($transfer->origin_type !== 'seller_collection') {
                    // This incoming row represents the immediately previous
                    // facility in the route, not the seller's original address.
                    $payload['kind'] = 'sorting';
                    $payload['loadedFromName'] = $payload['receivedFrom'];
                }
                // The UI uses nextCenterId to decide which parcels belong in
                // Incoming Transfers. Keep an unreceived parcel in transit to
                // this center, then move it to currentCenterId after a scan.
                if ($facility === 'sorting_center') {
                    $payload['currentCenterId'] = $received ? $id : null;
                    $payload['nextCenterId'] = $received ? null : $id;
                }
                $payload['routeStatus'] = $received ? 'received' : 'in_transit';
                $payload['inTransitSince'] = ($transfer->departed_at ?? $transfer->loaded_at ?? $transfer->created_at)?->toISOString();
                if ($transfer->origin_type === 'seller_collection') {
                    $pickupRider = $shipment->rider;
                    if (! $pickupRider && $facility === 'delivery_hub') {
                        $pickupRider = $this->riderFor(
                            $node,
                            (string) ($shipment->order?->seller?->barangay ?? ''),
                            'pickup',
                        );
                    }
                    $payload['kind'] = 'pickup';
                    $payload['pickupCollected'] = true;
                    $payload['dropoffConfirmed'] = $transfer->status !== 'preparing';
                    $payload['scheduledDate'] = $shipment->order?->pickup_date?->toDateString()
                        ?? now('Asia/Manila')->toDateString();
                    $pickupBarangay = trim((string) ($shipment->order?->seller?->barangay
                        ?: $shipment->rider?->assignment_barangay
                        ?: $shipment->rider?->barangay)) ?: 'Seller barangay unavailable';
                    $payload['barangay'] = $pickupBarangay;
                    $sellerAddress = collect([
                        $pickupBarangay,
                        $shipment->order?->seller?->municipality,
                        $shipment->order?->seller?->province,
                    ])->filter()->implode(', ');
                    $payload['loadedFromName'] = trim('Seller: '.($payload['seller_name'] ?: 'Seller').($sellerAddress !== '' ? ' · '.$sellerAddress : ''));
                    $manifestRider = trim((string) $transfer->courier_name);
                    if (in_array(mb_strtolower($manifestRider), ['', 'unassigned', 'not recorded'], true)) {
                        $manifestRider = '';
                    }
                    $payload['courierName'] = $pickupRider
                        ? (trim(implode(' ', array_filter([$pickupRider->first_name, $pickupRider->last_name]))) ?: $pickupRider->user?->name)
                        : ($payload['rider_name'] ?: $manifestRider ?: 'Pickup rider unavailable');
                    $payload['pickupRider'] = $payload['courierName'];
                    $payload['courierPhone'] = $pickupRider?->contact_no ?: $pickupRider?->user?->phone ?: $payload['rider_phone'];
                    $payload['riderPhone'] = $payload['courierPhone'];
                    $payload['riderVehicle'] = collect([$pickupRider?->vehicle, $pickupRider?->plate_number])->filter()->implode(' · ')
                        ?: $payload['rider_vehicle'];
                    $payload['truckId'] = $transfer->vehicle_reference ?: $pickupRider?->plate_number ?: $payload['rider_plate_number'];
                }
                if ($transfer->order_return_id && $transfer->orderReturn) {
                    $payload = array_replace($payload, $this->returnTransferPayload(
                        $transfer->orderReturn,
                        $shipment,
                        $transfer,
                        $received,
                    ));
                    $payload['returnAtSellerHub'] = $facility === 'delivery_hub'
                        && $transfer->destination_type === 'delivery_hub';
                }
                $parcels[] = $payload;
            }
            if ($members) {
                $manifests[] = [
                    'id' => (string) $transfer->id,
                    'sourceType' => $transfer->origin_type === 'sorting_center' ? 'sorting' : 'collection',
                    'originName' => $transfer->origin_type === 'seller_collection'
                        ? (collect([
                            $transfer->shipments->first()?->order?->seller?->store_name
                                ?: trim(($transfer->shipments->first()?->order?->seller?->first_name ?? '').' '.($transfer->shipments->first()?->order?->seller?->last_name ?? '')),
                            $transfer->shipments->first()?->order?->seller?->barangay,
                            $transfer->shipments->first()?->order?->seller?->municipality,
                        ])->filter()->implode(' · ') ?: 'Seller pickup')
                        : $this->facilityName($transfer->origin_type, $transfer->origin_id),
                    'receivedFrom' => $transfer->origin_type === 'seller_collection'
                        ? (collect([
                            $transfer->shipments->first()?->order?->seller?->store_name
                                ?: trim(($transfer->shipments->first()?->order?->seller?->first_name ?? '').' '.($transfer->shipments->first()?->order?->seller?->last_name ?? '')),
                            $transfer->shipments->first()?->order?->seller?->barangay,
                            $transfer->shipments->first()?->order?->seller?->municipality,
                        ])->filter()->implode(' · ') ?: 'Seller pickup')
                        : $this->facilityName($transfer->origin_type, $transfer->origin_id),
                    'barangay' => $transfer->origin_type === 'seller_collection'
                        ? ($transfer->shipments->first()?->order?->seller?->barangay
                            ?: $transfer->shipments->first()?->rider?->assignment_barangay
                            ?: $transfer->shipments->first()?->rider?->barangay
                            ?: 'Seller barangay unavailable')
                        : null,
                    'destination' => $this->facilityName($facility, $id),
                    'courierName' => $transfer->origin_type === 'sorting_center'
                        ? (trim(implode(' ', array_filter([
                            $transfer->courierRegistration?->first_name,
                            $transfer->courierRegistration?->middle_name,
                            $transfer->courierRegistration?->last_name,
                        ]))) ?: $transfer->courier_name)
                        : ($transfer->courier_name ?: trim(($transfer->shipments->first()?->rider?->first_name ?? '').' '.($transfer->shipments->first()?->rider?->last_name ?? ''))),
                    'pickupRider' => trim(($transfer->shipments->first()?->rider?->first_name ?? '').' '.($transfer->shipments->first()?->rider?->last_name ?? '')),
                    'courierPhone' => $transfer->origin_type === 'sorting_center'
                        ? ($transfer->courierRegistration?->phone ?: $transfer->courierRegistration?->user?->phone)
                        : $transfer->shipments->first()?->rider?->contact_no,
                    'riderPhone' => $transfer->shipments->first()?->rider?->contact_no,
                    'vehicleType' => $transfer->shipments->first()?->rider?->vehicle,
                    'truckId' => $transfer->vehicle_reference ?: $transfer->shipments->first()?->rider?->plate_number,
                    'courierRegistrationId' => $transfer->courier_registration_id,
                    'vehicleId' => $transfer->vehicle_id,
                    'status' => $transfer->status,
                    'routeStatus' => $transfer->status === 'arrived' ? 'received' : 'in_transit',
                    'inTransitSince' => ($transfer->departed_at ?? $transfer->loaded_at ?? $transfer->created_at)?->toISOString(),
                    'arrivedAt' => $transfer->arrived_at?->toISOString(),
                    'originCenterId' => $transfer->origin_type === 'sorting_center' ? $transfer->origin_id : null,
                    'originType' => $transfer->origin_type,
                    'originId' => $transfer->origin_id,
                    'returnManifestId' => $transfer->returning_manifest_id,
                    'parcelIds' => $members,
                ];
            }
        }

        if ($facility === 'delivery_hub') {
            $pickupShipments = Shipment::query()
                ->with(['order.seller.user', 'order.buyer.buyerProfile', 'order.items.product'])
                ->where(function ($query) use ($node): void {
                    // Scheduled pickups are shown before collection. Once the
                    // rider submits pickup proof, the shipment is already
                    // routed to this hub and must remain visible even if a
                    // manifest write was missed or an older app version sent
                    // the proof through the legacy endpoint.
                    $query->where(function ($scheduled) use ($node): void {
                    $scheduled->where(function ($hubScope) use ($node): void {
                        $hubScope->where('logistics_delivery_hub_id', $node->id)
                            ->orWhere(function ($unassigned) use ($node): void {
                                $unassigned->whereNull('logistics_delivery_hub_id')
                                    ->whereHas('order', fn ($order) => $order
                                            ->where(fn ($network) => $network
                                                ->where('logistics_id', $node->logisticsBranch?->logistics_id)
                                                ->orWhereNull('logistics_id'))
                                        ->whereHas('seller', fn ($seller) => $seller
                                            ->whereRaw('LOWER(province) = ?', [mb_strtolower(trim((string) $node->province))])
                                            ->whereRaw('LOWER(municipality) = ?', [mb_strtolower(trim((string) $node->municipality))])));
                            });
                    })->whereHas('order', fn ($order) => $order->whereIn('status', ['pending', 'new', 'preparing', 'to_ship']));
                    })->orWhere(function ($collected) use ($node): void {
                        $collected->where('logistics_delivery_hub_id', $node->id)
                            ->whereHas('order', fn ($order) => $order->where('status', 'in_transit'))
                            ->whereDoesntHave('transferManifests', fn ($manifest) => $manifest
                                ->where('origin_type', 'seller_collection')
                                ->where('destination_type', 'delivery_hub')
                                ->where('destination_id', $node->id));
                    });
                })
                ->oldest('id')
                ->get();

            foreach ($pickupShipments as $shipment) {
                $hubLocation = $node->hub_name.' · '.$node->municipality.', '.$node->province;
                $receivedAtHub = $shipment->scans()->where('status', 'Received at Delivery Hub')->where('location', $hubLocation)->exists();
                $payload = $this->parcelPayload(
                    $shipment,
                    'shipment-'.$shipment->id,
                    $receivedAtHub ? 'received' : 'incoming',
                    null,
                    'collection-'.$shipment->id,
                );
                // Resolve the pickup rider from the hub assignment as a
                // fallback for older shipments whose rider_id was not saved
                // when the seller scheduled the pickup.
                $pickupRider = $shipment->rider ?: $this->riderFor(
                    $node,
                    (string) ($shipment->order?->seller?->barangay ?? ''),
                    'pickup',
                );
                $pickupBarangay = trim((string) ($shipment->order?->seller?->barangay
                    ?: $shipment->rider?->assignment_barangay
                    ?: $shipment->rider?->barangay)) ?: 'Seller barangay unavailable';
                $payload['kind'] = 'pickup';
                $payload['barangay'] = $pickupBarangay;
                $payload['loadedFromName'] = trim('Seller: '.($payload['seller_name'] ?: 'Seller').' · '.implode(', ', array_filter([
                    $pickupBarangay,
                    $shipment->order?->seller?->municipality,
                    $shipment->order?->seller?->province,
                ])));
                $payload['pickupCollected'] = $shipment->order?->status === 'in_transit';
                $payload['dropoffConfirmed'] = $receivedAtHub;
                // A scheduled pickup belongs to the rider even when the order
                // remains in a seller-facing preparation status for legacy flows.
                $payload['orderAwaitingSeller'] = in_array($shipment->order?->status, ['pending', 'new', 'preparing'], true)
                    && $shipment->order?->pickup_date === null;
                $payload['scheduledDate'] = $shipment->order?->pickup_date?->toDateString()
                    ?? $shipment->order?->created_at?->toDateString();
                $payload['courierName'] = $pickupRider
                    ? (trim(implode(' ', array_filter([$pickupRider->first_name, $pickupRider->last_name]))) ?: $pickupRider->user?->name)
                    : 'Pickup rider unavailable';
                $payload['pickupRider'] = $payload['courierName'];
                $payload['courierPhone'] = $pickupRider?->contact_no ?: $pickupRider?->user?->phone;
                $payload['riderPhone'] = $payload['courierPhone'];
                $payload['riderVehicle'] = collect([$pickupRider?->vehicle, $pickupRider?->plate_number])->filter()->implode(' · ');
                $payload['truckId'] = $pickupRider?->plate_number;
                $parcels[] = $payload;
            }
        }

        // Buyer-scheduled returns are pickup collections, not shipment
        // manifests. Keep them in the receiving hub's incoming scanner queue
        // as soon as a pickup slot and rider have been assigned.
        if ($facility === 'delivery_hub') {
            $scheduledReturns = OrderReturn::query()
                ->where(function ($query) use ($id, $node): void {
                    $query->where('return_delivery_hub_id', $id)
                        // Compatibility for returns scheduled before the
                        // return hub column was populated. Resolve those by
                        // the buyer's delivery municipality, never the
                        // shipment's seller-origin hub.
                        ->orWhere(function ($legacy) use ($node): void {
                            $legacy->whereNull('return_delivery_hub_id')
                                ->whereHas('order', function ($order) use ($node): void {
                                    $order->where(function ($location) use ($node): void {
                                            $location->where(function ($delivery) use ($node): void {
                                                $delivery->whereRaw("LOWER(TRIM(COALESCE(NULLIF(delivery_province, ''), ''))) = ?", [mb_strtolower(trim((string) $node->province))])
                                                ->whereRaw("LOWER(TRIM(COALESCE(NULLIF(delivery_municipality, ''), ''))) = ?", [mb_strtolower(trim((string) $node->municipality))]);
                                        })->orWhereHas('buyer.buyerProfile', fn ($profile) => $profile
                                            ->whereRaw('LOWER(TRIM(province)) = ?', [mb_strtolower(trim((string) $node->province))])
                                            ->whereRaw('LOWER(TRIM(municipality)) = ?', [mb_strtolower(trim((string) $node->municipality))]));
                                    });
                                });
                        });
                })
                ->where('return_type', 'return')
                ->whereIn('status', ['accepted', 'refunded'])
                ->whereIn('shipping_status', ['scheduled', 'picked_up', 'received_at_delivery_hub'])
                ->whereNotNull('pickup_date')
                ->with(['order.buyer.buyerProfile', 'order.seller', 'order.items.product', 'order.shipment', 'pickupRider.user'])
                ->orderBy('pickup_date')
                ->orderBy('pickup_time')
                ->get();

            foreach ($scheduledReturns as $return) {
                $order = $return->order;
                if (! $order) continue;

                $buyer = $order->buyer?->buyerProfile;
                $rider = $return->pickupRider;
                $buyerName = trim(implode(' ', array_filter([$buyer?->first_name, $buyer?->last_name])))
                    ?: ($order->buyer?->name ?: $order->delivery_name ?: 'Buyer');
                $buyerAddress = trim((string) ($return->pickup_address ?: $order->delivery_address));
                $barangay = trim((string) ($order->delivery_barangay ?: $buyer?->barangay)) ?: 'Barangay unavailable';
                $riderName = trim(implode(' ', array_filter([$rider?->first_name, $rider?->last_name])))
                    ?: ($rider?->user?->name ?: 'Pickup rider unavailable');
                $tracking = $order->shipment?->tracking_number
                    ?: $return->tracking_number ?: 'SE-RETURN-'.str_pad((string) $return->id, 8, '0', STR_PAD_LEFT);
                $items = $order->items->map(fn ($item): array => [
                    'product_name' => $item->product_name ?: $item->product?->name ?: 'Returned item',
                    'variation' => $item->variation ?: 'Standard',
                    'color' => $item->color ?: 'N/A',
                    'size' => $item->size ?: 'N/A',
                    'quantity' => (int) $item->quantity,
                    'product_price' => (float) ($item->unit_price ?? $item->product_price ?? 0),
                ])->values()->all();

                $parcels[] = [
                    'id' => 'return-'.$return->id,
                    'return_id' => $return->id,
                    'order_id' => $order->id,
                    'tracking' => $tracking,
                    'scan_token' => $order->shipment?->scan_token,
                    'order_number' => $order->order_number,
                    'status' => $return->shipping_status === 'received_at_delivery_hub' ? 'received' : 'incoming',
                    'kind' => 'return',
                    'isReturn' => true,
                    'parcelType' => 'return',
                    'transferId' => 'return-collection-'.$return->pickup_date->toDateString().'-'.mb_strtolower($barangay).'-'.($rider?->id ?? 0),
                    'barangay' => $barangay,
                    'scheduledDate' => $return->pickup_date->toDateString(),
                    'pickupTime' => $return->pickup_time,
                    'pickupCollected' => $return->shipping_status === 'picked_up',
                    'receivedAt' => $return->shipping_status === 'received_at_delivery_hub'
                        ? $return->events()->where('status', 'hub_received')->latest('scanned_at')->value('scanned_at')
                        : null,
                    'loadedFromName' => 'FOR RETURN · Buyer pickup · '.$buyerName.($buyerAddress !== '' ? ' · '.$buyerAddress : ''),
                    'courierName' => $riderName,
                    'pickupRider' => $riderName,
                    'courierPhone' => $rider?->contact_no,
                    'riderPhone' => $rider?->contact_no,
                    'riderVehicle' => $rider?->vehicle,
                    'truckId' => $rider?->plate_number,
                    'buyer_name' => $buyerName,
                    'buyer_email' => $order->buyer?->email,
                    'buyer_phone' => $order->delivery_phone ?: $buyer?->contact_no,
                    'buyer_province' => $order->delivery_province ?: $buyer?->province,
                    'buyer_city' => $order->delivery_municipality ?: $buyer?->municipality,
                    'buyer_barangay' => $barangay,
                    'buyer_street' => $buyer?->street,
                    'buyer_house_no' => $buyer?->house_number,
                    'buyer_postal_code' => $buyer?->postal_code,
                    'delivery_address' => $buyerAddress,
                    'seller_name' => $order->seller?->store_name ?: trim(($order->seller?->first_name ?? '').' '.($order->seller?->last_name ?? '')),
                    'seller_email' => $order->seller?->user?->email,
                    'seller_phone' => $order->seller?->contact_no,
                    'seller_province' => $order->seller?->province,
                    'seller_city' => $order->seller?->municipality,
                    'seller_barangay' => $order->seller?->barangay,
                    'ordered_at' => $order->created_at?->toISOString(),
                    'estimated_delivery' => null,
                    'payment_method' => $order->payment_method,
                    'total_amount' => (float) $return->refund_amount,
                    'notes' => 'Return pickup scheduled for '.$return->pickup_date->format('M d, Y').' at '.$return->pickup_time.'.',
                    'items' => $items,
                    'history' => $return->events->map(fn ($event): array => [
                        'status' => ucfirst(str_replace('_', ' ', $event->status)),
                        'date' => $event->scanned_at?->toISOString(),
                        'location' => $event->location,
                    ])->values()->all(),
                ];
            }
        }

        $couriers = $vehicles = [];
        $activeDispatchQuery = LogisticsTransferManifest::query()->whereIn('status', ['preparing', 'loaded']);
        $sortingCenterId = $facility === 'sorting_center' ? $id : $node->logistics_branch_id;
        $networkHubIds = LogisticsDeliveryHub::query()->where('logistics_branch_id', $sortingCenterId)->pluck('id');
        $activeDispatchQuery->where(function ($query) use ($sortingCenterId, $networkHubIds): void {
            $query->where(fn ($sorting) => $sorting->where('origin_type', 'sorting_center')->where('origin_id', $sortingCenterId))
                ->orWhere(fn ($hub) => $hub->where('origin_type', 'delivery_hub')->whereIn('origin_id', $networkHubIds));
        });
        $activeDispatches = $activeDispatchQuery->get(['courier_registration_id', 'vehicle_id', 'courier_name', 'vehicle_reference', 'scheduled_at']);
        if ($facility === 'sorting_center') {
            $archivedCourierIds = SortingCenterFleetArchive::query()->where('sorting_center_id', $id)->where('record_type', 'couriers')->pluck('record_id');
            $archivedVehicleIds = SortingCenterFleetArchive::query()->where('sorting_center_id', $id)->where('record_type', 'vehicles')->pluck('record_id');
            $couriers = CourierRegistration::query()->with('user:id,email')->where('sorting_center_id', $id)->where('status', 'approved')->whereNotIn('id', $archivedCourierIds)->get(['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'phone', 'plate_number', 'vehicle'])->values();
            $vehicles = SortingCenterVehicle::query()->where('sorting_center_id', $id)->whereNotIn('id', $archivedVehicleIds)->get(['id', 'name', 'type', 'plate_number', 'availability'])->values();
        } elseif ($facility === 'delivery_hub') {
            $sortingCenterId = (int) $node->logistics_branch_id;
            $couriers = CourierRegistration::query()
                ->where('sorting_center_id', $sortingCenterId)
                ->where('status', 'approved')
                ->with('user:id,email')
                ->get(['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'phone', 'plate_number', 'vehicle'])
                ->map(function (CourierRegistration $courier) use ($sortingCenterId): ?array {
                    $vehicle = SortingCenterVehicle::query()
                        ->where('sorting_center_id', $sortingCenterId)
                        ->whereRaw('LOWER(plate_number) = ?', [mb_strtolower(trim((string) $courier->plate_number))])
                        ->whereRaw('LOWER(availability) = ?', ['available'])
                        ->first(['id', 'name', 'type', 'plate_number']);
                    if (! $vehicle) return null;

                    return [
                        'id' => $courier->id,
                        'courier_registration_id' => $courier->id,
                        'name' => trim(implode(' ', array_filter([$courier->first_name, $courier->middle_name, $courier->last_name]))),
                        'email' => $courier->user?->email,
                        'phone' => $courier->phone,
                        'vehicle' => $courier->vehicle,
                        'plate_number' => $courier->plate_number,
                        'vehicle_id' => $vehicle->id,
                        'vehicle_reference' => $vehicle->plate_number,
                    ];
                })->filter()->values();
        }

        $assignedReturnIds = LogisticsTransferManifest::query()
            ->whereNotNull('returning_manifest_id')->pluck('returning_manifest_id')->all();

        $outgoingTransfers = LogisticsTransferManifest::query()
            ->where('origin_type', $facility)
            ->where('origin_id', $id)
            ->whereIn('destination_type', ['sorting_center', 'delivery_hub'])
            // Keep loaded parcels in departure history, including a manifest
            // awaiting the courier's departure confirmation. Remove each
            // parcel only after the destination receive scan reconciles it.
            ->whereIn('status', ['preparing', 'loaded'])
            ->whereHas('shipments', fn ($query) => $query
                ->where('logistics_transfer_manifest_shipments.load_status', 'loaded')
                ->whereNull('logistics_transfer_manifest_shipments.received_at'))
            ->with('shipments')
            ->latest('departed_at')
            ->get()
            ->map(function (LogisticsTransferManifest $transfer) use ($facility, $id): array {
                $destinationName = $this->facilityName($transfer->destination_type, (int) $transfer->destination_id);
                $originName = $this->facilityName($facility, $id);

                return [
                    'id' => (string) ($transfer->batch_key ?: $transfer->id),
                    'manifestId' => (string) $transfer->id,
                    'sourceType' => 'transfer-to-sorting',
                    'originCenterId' => (string) $id,
                    'originName' => $originName,
                    'destination' => $destinationName,
                    'destinationType' => $transfer->destination_type,
                    'destinationId' => (string) $transfer->destination_id,
                    'nextStop' => [
                        'type' => $transfer->destination_type === 'delivery_hub' ? 'hub' : 'sorting',
                        'id' => (string) $transfer->destination_id,
                        'name' => $destinationName,
                    ],
                    'courierName' => $transfer->courier_name,
                    'truckId' => $transfer->vehicle_reference,
                    'createdAt' => ($transfer->departed_at ?? $transfer->loaded_at)?->toISOString(),
                    'status' => $transfer->status,
                    'arrivedAt' => $transfer->arrived_at?->toISOString(),
                    'parcelIds' => $transfer->shipments
                        ->filter(fn (Shipment $shipment): bool => $shipment->pivot->load_status === 'loaded' && $shipment->pivot->received_at === null)
                        ->map(fn (Shipment $shipment): string => $transfer->order_return_id ? 'return-'.$transfer->order_return_id : 'shipment-'.$shipment->id)->values(),
                    'trackingNumbers' => $transfer->orderReturn
                        ? collect([$transfer->orderReturn->tracking_number])->filter()->values()
                        : $transfer->shipments
                        ->filter(fn (Shipment $shipment): bool => $shipment->pivot->load_status === 'loaded' && $shipment->pivot->received_at === null)
                        ->map(fn (Shipment $shipment): string => (string) $shipment->tracking_number)->values(),
                ];
            })->values();

        // Last-mile deliveries do not use a facility-to-facility manifest.
        // Keep them in the hub's departure history from the persisted shipment
        // assignment until the buyer confirms delivery.
        $lastMileDepartures = $facility === 'delivery_hub'
            ? Shipment::query()
                ->with(['order.buyer.buyerProfile', 'rider'])
                ->where('logistics_delivery_hub_id', $id)
                ->whereHas('order', fn ($query) => $query->where('status', 'out_for_delivery'))
                ->whereHas('scans', fn ($query) => $query
                    ->where('status', 'Out for Delivery')
                    ->where('location', $node->hub_name.' · '.$node->municipality.', '.$node->province))
                ->get()
                ->map(function (Shipment $shipment) use ($node, $id): array {
                    $order = $shipment->order;
                    $buyer = $order?->buyer?->buyerProfile;
                    $recipient = trim((string) $order?->delivery_name)
                        ?: trim(implode(' ', array_filter([$buyer?->first_name, $buyer?->last_name])))
                        ?: 'Buyer';
                    $rider = $shipment->rider;
                    $departureScan = $shipment->scans()
                        ->where('status', 'Out for Delivery')
                        ->where('location', $node->hub_name.' · '.$node->municipality.', '.$node->province)
                        ->latest('scanned_at')->first();

                    return [
                        'id' => 'last-mile-'.$shipment->id,
                        'manifestId' => null,
                        'sourceType' => 'out-for-delivery',
                        'originCenterId' => (string) $id,
                        'originName' => $node->hub_name,
                        'destination' => $recipient.' · '.($order?->delivery_address ?: 'Buyer address unavailable'),
                        'destinationType' => 'buyer',
                        'destinationId' => (string) $order?->id,
                        'courierName' => trim(implode(' ', array_filter([$rider?->first_name, $rider?->last_name]))) ?: $rider?->user?->name,
                        'truckId' => collect([$rider?->vehicle, $rider?->plate_number])->filter()->implode(' · '),
                        'createdAt' => $departureScan?->scanned_at?->toISOString() ?? $shipment->location_updated_at?->toISOString(),
                        'status' => 'out_for_delivery',
                        'arrivedAt' => null,
                        'parcelIds' => ['shipment-'.$shipment->id],
                        'trackingNumbers' => [(string) $shipment->tracking_number],
                    ];
                })->values()
            : collect();
        $outgoingTransfers = $outgoingTransfers->concat($lastMileDepartures)->values();
        $deliveredTrackingNumbers = $facility === 'delivery_hub'
            ? Shipment::query()
                ->where('logistics_delivery_hub_id', $id)
                ->whereHas('order', fn ($query) => $query->where('status', 'delivered'))
                ->whereHas('scans', fn ($query) => $query
                    ->where('status', 'Out for Delivery')
                    ->where('location', $node->hub_name.' · '.$node->municipality.', '.$node->province))
                ->pluck('tracking_number')->values()
            : collect();

        $stagedDepartures = $facility === 'sorting_center'
            ? LogisticsTransferManifest::query()
                ->where('origin_type', 'sorting_center')
                ->where('origin_id', $id)
                ->where('status', 'preparing')
                ->whereHas('shipments', fn ($query) => $query->whereIn('logistics_transfer_manifest_shipments.load_status', ['pending', 'loaded']))
                ->with(['shipments.order.seller.user', 'shipments.order.buyer.buyerProfile', 'shipments.order.items.product', 'orderReturn.order.seller.user', 'orderReturn.order.buyer.buyerProfile'])
                ->latest('created_at')->get()
                ->map(function (LogisticsTransferManifest $transfer) use ($id): array {
                    $destination = $this->facilityName($transfer->destination_type, (int) $transfer->destination_id);
                    $nextStop = [
                        'type' => $transfer->destination_type === 'delivery_hub' ? 'hub' : 'sorting',
                        'id' => (string) $transfer->destination_id,
                        'name' => $destination,
                        'centerId' => (string) ($transfer->destination_type === 'sorting_center' ? $transfer->destination_id : $id),
                    ];

                    return [
                        'id' => (string) ($transfer->batch_key ?: $transfer->id),
                        'originName' => $this->facilityName('sorting_center', $id),
                        'destination' => $destination,
                        'courierName' => $transfer->courier_name,
                        'truckId' => $transfer->vehicle_reference,
                        'createdAt' => $transfer->created_at?->toISOString(),
                        'status' => $transfer->courier_registration_id ? 'loaded' : 'preparing',
                        'parcelIds' => $transfer->shipments->map(fn (Shipment $shipment): string => 'shipment-'.$shipment->id)->values(),
                        'parcels' => $transfer->shipments->map(function (Shipment $shipment) use ($id, $transfer, $nextStop): array {
                            $inboundId = $shipment->transferManifests()
                                ->where('destination_type', 'sorting_center')
                                ->where('destination_id', $id)
                                ->wherePivotNotNull('received_at')
                                ->latest('logistics_transfer_manifests.id')
                                ->value('logistics_transfer_manifests.id');
                            $payload = $transfer->order_return_id && $transfer->orderReturn
                                ? $this->returnTransferPayload($transfer->orderReturn, $shipment, $transfer, true)
                                : $this->parcelPayload($shipment, 'shipment-'.$shipment->id, $transfer->courier_registration_id ? 'loaded' : 'staged', $id, $transfer->id);
                            $payload['currentCenterId'] = (string) $id;
                            $payload['status'] = $transfer->courier_registration_id ? 'loaded' : 'staged';
                            $payload['nextCenterId'] = $transfer->destination_type === 'sorting_center' && ! $transfer->courier_registration_id
                                ? (string) $transfer->destination_id
                                : null;
                            $payload['nextStop'] = $transfer->courier_registration_id ? null : $nextStop;
                            $payload['receivedManifestId'] = $inboundId ? (string) $inboundId : null;
                            $payload['departureBatchKey'] = $transfer->batch_key;
                            $payload['loadedFromId'] = (string) $id;
                            $payload['loadedFromName'] = $this->facilityName('sorting_center', $id);
                            $payload['loadedAt'] = $transfer->loaded_at?->toISOString();
                            $payload['courierName'] = $transfer->courier_name;
                            $payload['truckId'] = $transfer->vehicle_reference;

                            return $payload;
                        })->values(),
                    ];
                })->values()
            : collect();

        if ($parcels !== []) {
            $reportedIssues = $facility === 'sorting_center'
                ? SortingCenterParcelIssue::query()
                ->where('sorting_center_id', $id)
                ->latest('created_at')
                ->get(['id', 'tracking_number', 'created_at'])
                ->keyBy(fn (SortingCenterParcelIssue $issue): string => mb_strtolower(trim($issue->tracking_number)))
                : DeliveryHubParcelIssue::query()
                    ->where('delivery_hub_id', $id)
                    ->latest('created_at')
                    ->get(['id', 'tracking_number', 'created_at'])
                    ->keyBy(fn (DeliveryHubParcelIssue $issue): string => mb_strtolower(trim($issue->tracking_number)));

            foreach ($parcels as &$parcel) {
                $issue = $reportedIssues->get(mb_strtolower(trim((string) ($parcel['tracking'] ?? ''))));
                if ($issue) {
                    $parcel['reportedIssue'] = true;
                    $parcel['reportId'] = (string) $issue->id;
                    $parcel['reportedAt'] = $issue->created_at?->toISOString();
                }
            }
            unset($parcel);
        }

        return response()->json([
            'facility_type' => $facility,
            'facility_id' => $id,
            'parcels' => $parcels,
            'manifests' => $manifests,
            'departures' => $outgoingTransfers,
            'delivered_tracking_numbers' => $deliveredTrackingNumbers,
            'staged_departures' => $stagedDepartures,
            'return_trucks' => $transfers->where('status', 'arrived')->filter(fn ($transfer) =>
                $transfer->origin_type === 'sorting_center'
                && $transfer->destination_type === 'sorting_center'
                && ! in_array($transfer->id, $assignedReturnIds, true)
            )->map(fn ($transfer) => [
                'id' => $transfer->id,
                'origin_id' => $transfer->origin_id,
                'origin_type' => $transfer->origin_type,
                'courier_name' => $transfer->courier_name,
                'courier_registration_id' => $transfer->courier_registration_id,
                'vehicle_id' => $transfer->vehicle_id,
                'vehicle_reference' => $transfer->vehicle_reference,
                'origin_name' => $this->facilityName($transfer->origin_type, $transfer->origin_id),
            ])->values(),
            'centers' => $facility === 'sorting_center'
                ? LogisticsBranch::query()->where('logistics_id', $node->logistics_id)->where('status', 'active')->whereNull('archived_at')
                    ->with(['deliveryHubs' => fn ($query) => $query->where('status', 'active')->whereNull('archived_at')])
                    ->get()->map(fn ($branch) => [
                        'id' => (string) $branch->id, 'branch_name' => $branch->name, 'province' => $branch->province, 'status' => $branch->status,
                        'hubs' => $branch->deliveryHubs->map(fn ($hub) => ['id' => (string) $hub->id, 'hub_name' => $hub->hub_name, 'municipality' => $hub->municipality, 'city' => $hub->municipality, 'status' => $hub->status])->values(),
                    ])->values()
                : [],
            'center_routes' => $facility === 'sorting_center'
                ? LogisticsBranchRoute::query()->where('status', 'active')
                    ->whereHas('origin', fn ($query) => $query->where('logistics_id', $node->logistics_id)->where('status', 'active')->whereNull('archived_at'))
                    ->whereHas('next', fn ($query) => $query->where('logistics_id', $node->logistics_id)->where('status', 'active')->whereNull('archived_at'))
                    ->get(['origin_branch_id', 'next_branch_id'])
                    ->map(fn (LogisticsBranchRoute $route): array => [
                        'origin_branch_id' => (string) $route->origin_branch_id,
                        'next_branch_id' => (string) $route->next_branch_id,
                    ])->values()
                : [],
            'couriers' => $couriers,
            'delivery_riders' => $deliveryRiders,
            'vehicles' => $vehicles,
            'active_dispatches' => $activeDispatches,
        ]);
    }

    private function returnTransferPayload(OrderReturn $return, Shipment $shipment, LogisticsTransferManifest $manifest, bool $received): array
    {
        $order = $return->order;
        $buyer = $order?->buyer?->buyerProfile;
        $rider = $return->pickupRider;
        $barangay = trim((string) ($order?->delivery_barangay ?: $buyer?->barangay)) ?: 'Barangay unavailable';
        $buyerName = trim(implode(' ', array_filter([$buyer?->first_name, $buyer?->last_name])))
            ?: ($order?->buyer?->name ?: $order?->delivery_name ?: 'Buyer');
        $buyerAddress = trim((string) ($return->pickup_address ?: $order?->delivery_address));
        $history = $return->events->map(fn ($event): array => [
            'status' => ucfirst(str_replace('_', ' ', $event->status)),
            'date' => $event->scanned_at?->toISOString(),
            'location' => $event->location,
        ])->values()->all();
        $receivedAt = $received ? $manifest->shipments->first()?->pivot?->received_at : null;

        return [
            'id' => 'return-'.$return->id,
            'return_id' => $return->id,
            'order_id' => $order?->id,
            'tracking' => $shipment->tracking_number,
            'scan_token' => $shipment->scan_token,
            'scan_url' => $order?->order_number ? url('/api/v1/orders/verify/'.rawurlencode($order->order_number)) : null,
            'order_number' => $order?->order_number,
            'status' => $received ? 'received' : 'incoming',
            'kind' => 'return',
            'isReturn' => true,
            'parcelType' => 'return',
            'transferId' => (string) $manifest->id,
            'receivedManifestId' => (string) $manifest->id,
            'currentCenterId' => $received ? (string) $manifest->destination_id : null,
            'nextCenterId' => $received ? null : (string) $manifest->destination_id,
            'receivedAt' => $receivedAt ? \Illuminate\Support\Carbon::parse($receivedAt)->toISOString() : null,
            'receivedFrom' => $this->facilityName($manifest->origin_type, (int) $manifest->origin_id),
            'barangay' => $barangay,
            'destProvince' => $order?->seller?->province,
            'destCity' => $order?->seller?->municipality,
            'destBarangay' => $order?->seller?->barangay,
            'buyer_name' => $buyerName,
            'buyer_email' => $order?->buyer?->email,
            'buyer_phone' => $order?->delivery_phone ?: $buyer?->contact_no,
            'buyer_province' => $order?->delivery_province ?: $buyer?->province,
            'buyer_city' => $order?->delivery_municipality ?: $buyer?->municipality,
            'buyer_barangay' => $barangay,
            'buyer_street' => $buyer?->street,
            'buyer_house_no' => $buyer?->house_number,
            'buyer_postal_code' => $buyer?->postal_code,
            'delivery_address' => $buyerAddress,
            'loadedFromName' => 'FOR RETURN · Buyer pickup · '.$buyerName.($buyerAddress !== '' ? ' · '.$buyerAddress : ''),
            'pickupCollected' => true,
            'scheduledDate' => $return->pickup_date?->toDateString(),
            'pickupTime' => $return->pickup_time,
            'courierName' => trim(implode(' ', array_filter([$rider?->first_name, $rider?->last_name]))) ?: $rider?->user?->name,
            'pickupRider' => trim(implode(' ', array_filter([$rider?->first_name, $rider?->last_name]))) ?: $rider?->user?->name,
            'courierPhone' => $rider?->contact_no,
            'riderPhone' => $rider?->contact_no,
            'truckId' => $rider?->plate_number,
            'seller_name' => $order?->seller?->store_name ?: trim(($order?->seller?->first_name ?? '').' '.($order?->seller?->last_name ?? '')),
            'seller_email' => $order?->seller?->user?->email,
            'seller_phone' => $order?->seller?->contact_no,
            'seller_province' => $order?->seller?->province,
            'seller_city' => $order?->seller?->municipality,
            'seller_barangay' => $order?->seller?->barangay,
            'ordered_at' => $order?->created_at?->toISOString(),
            'estimated_delivery' => null,
            'payment_method' => $order?->payment_method,
            'total_amount' => (float) $return->refund_amount,
            'notes' => 'Buyer return transfer to Sorting Center.',
            'items' => $order?->items?->map(fn ($item): array => [
                'product_name' => $item->product_name ?: $item->product?->name ?: 'Returned item',
                'variation' => $item->variation ?: 'Standard',
                'color' => $item->color ?: 'N/A',
                'size' => $item->size ?: 'N/A',
                'quantity' => (int) $item->quantity,
                'product_price' => (float) ($item->unit_price ?? 0),
            ])->values()->all() ?? [],
            'history' => $history ?: $shipment->scans()->oldest('scanned_at')->get()
                ->map(fn ($scan): array => ['status' => $scan->status, 'date' => $scan->scanned_at?->toISOString(), 'location' => $scan->location])->values()->all(),
        ];
    }

    private function parcelPayload(Shipment $shipment, string $id, string $status, ?int $centerId, int|string $transferId): array
    {
        $shipment->loadMissing('rider.user');
        $order = $shipment->order;
        $seller = $order?->seller;
        $buyer = $order?->buyer?->buyerProfile;
        $recipientName = trim((string) $order?->delivery_name);
        $profileBuyerName = trim(implode(' ', array_filter([$buyer?->first_name, $buyer?->last_name])));
        $accountBuyerName = trim((string) $order?->buyer?->name);
        $recipientName = $profileBuyerName ?: $accountBuyerName ?: $recipientName;
        $rider = $shipment->rider;
        return [
            'id' => $id, 'shipment_id' => $shipment->id, 'order_id' => $order?->id, 'scan_token' => $shipment->scan_token,
            'tracking' => $shipment->tracking_number, 'order_number' => $order?->order_number,
            'status' => $status, 'kind' => str_starts_with((string) $transferId, 'collection-') ? 'pickup' : 'sorting', 'transferId' => (string) $transferId,
            'currentCenterId' => $centerId, 'nextCenterId' => null,
            'buyer_name' => $recipientName,
            'buyer_email' => $order?->buyer?->email, 'buyer_phone' => $order?->delivery_phone ?: $buyer?->contact_no,
            'buyer_province' => $buyer?->province, 'buyer_city' => $buyer?->municipality,
            'buyer_barangay' => $buyer?->barangay, 'buyer_street' => $buyer?->street,
            'buyer_house_no' => $buyer?->house_number, 'buyer_postal_code' => $buyer?->zip_code,
            'delivery_address' => $order?->delivery_address,
            'seller_name' => $seller?->store_name ?: trim(($seller?->first_name ?? '').' '.($seller?->last_name ?? '')),
            'seller_email' => $seller?->user?->email, 'seller_phone' => $seller?->contact_no,
            'seller_province' => $seller?->province, 'seller_city' => $seller?->municipality,
            'seller_barangay' => $seller?->barangay, 'seller_street' => $seller?->street,
            'seller_house_no' => $seller?->house_number, 'seller_postal_code' => $seller?->zip_code,
            'destProvince' => $buyer?->province, 'destCity' => $buyer?->municipality, 'destBarangay' => $buyer?->barangay,
            'barangay' => $buyer?->barangay ?: $seller?->barangay ?: 'Barangay not provided',
            'loadedFromName' => $seller?->store_name ?: trim(($seller?->first_name ?? '').' '.($seller?->last_name ?? '')),
            'pickupDate' => $order?->pickup_date?->toDateString(),
            'ordered_at' => $order?->created_at?->toISOString(),
            'estimated_delivery' => $shipment->estimated_delivery?->toDateString()
                ?? $order?->pickup_date?->copy()->addDays(3)->toDateString()
                ?? $order?->created_at?->copy()->addDays(3)->toDateString(),
            'rider_name' => trim(implode(' ', array_filter([$rider?->first_name, $rider?->last_name]))) ?: $rider?->user?->name,
            'rider_phone' => $rider?->contact_no,
            'rider_vehicle' => collect([$rider?->vehicle, $rider?->plate_number])->filter()->implode(' · '),
            'rider_plate_number' => $rider?->plate_number,
            'courierName' => trim(implode(' ', array_filter([$rider?->first_name, $rider?->last_name]))) ?: $rider?->user?->name,
            'courierPhone' => $rider?->contact_no,
            'scan_url' => $shipment->scan_token
                ? route('parcel.scan.show', ['token' => $shipment->scan_token])
                : url('/api/v1/orders/verify/'.rawurlencode((string) $order?->order_number)),
            'total_amount' => (float) ($order?->total ?? 0), 'payment_method' => $order?->payment_method,
            'items' => $order?->items?->map(fn ($item) => [
                'product_name' => $item->product?->name ?: $item->product_name,
                'is_fragile' => (bool) ($item->is_fragile || $item->product?->is_fragile),
                'variation' => $item->variation,
                'color' => $item->color,
                'size' => $item->size,
                'quantity' => $item->quantity,
                'product_price' => (float) $item->unit_price,
            ])->values() ?? [],
            'history' => $shipment->scans()->oldest('scanned_at')->get()->map(fn ($scan) => ['status' => $scan->status, 'date' => $scan->scanned_at?->toISOString(), 'location' => $scan->location])->values(),
        ];
    }

    private function facilityName(string $type, int $id): string
    {
        if ($type === 'seller_collection') return 'Seller Barangay Collection';
        return $type === 'delivery_hub'
            ? (string) (LogisticsDeliveryHub::query()->whereKey($id)->value('hub_name') ?? 'Delivery Hub')
            : (string) (LogisticsBranch::query()->whereKey($id)->value('name') ?? 'Sorting Center');
    }

    private function scanAtHub(Shipment $shipment, LogisticsDeliveryHub $hub, string $action, string $sellerProvince, string $sellerCity, ?string $sellerBarangay, string $buyerProvince, string $buyerCity, ?string $buyerBarangay, ?int $assignedRiderId = null): void
    {
        if ($action === 'receive') {
            abort_unless(! in_array($shipment->order->status, ['pending', 'new', 'preparing'], true), 409, 'This order is waiting for the seller to schedule its pickup before the Delivery Hub can receive-scan it.');
        }

        $latestScan = $shipment->scans()->orderByDesc('scanned_at')->orderByDesc('id')->first();
        $hubLocation = $hub->hub_name.' · '.$hub->municipality.', '.$hub->province;
        if ($latestScan?->location === $hubLocation) {
            if ($action === 'receive' && $latestScan->status === 'Received at Delivery Hub') {
                abort(409, 'Already scanned: this parcel has already been received at this delivery hub.');
            }
            if ($action === 'depart' && in_array($latestScan->status, ['In Transit to Delivery Hub', 'In Transit to Sorting Center', 'Out for Delivery'], true)) {
                abort(409, 'Already scanned: this parcel has already been departure-scanned from this delivery hub.');
            }
        }

        if ($action === 'receive' && $shipment->order->status === 'to_ship') {
            abort_unless($this->same($hub->province, $sellerProvince) && $this->same($hub->municipality, $sellerCity), 422, 'Seller pickup parcels must first arrive at the delivery hub for the seller municipality.');
            $latestScan = $shipment->scans()->orderByDesc('scanned_at')->orderByDesc('id')->first();
            abort_if(
                $latestScan?->status === 'Received at Delivery Hub'
                    && $latestScan->location === $hub->hub_name.' · '.$hub->municipality.', '.$hub->province,
                409,
                'This parcel has already been received at the seller hub.',
            );
            abort_unless($shipment->logistics_delivery_hub_id === null || $shipment->logistics_delivery_hub_id === $hub->id, 409, 'Parcel is assigned to a different delivery hub.');
            $pickupRider = $this->riderFor($hub, (string) $sellerBarangay, 'pickup', $shipment->rider_id);
            abort_unless($pickupRider, 422, 'No active pickup rider is assigned to the seller barangay.');
            $shipment->update(['logistics_delivery_hub_id' => $hub->id, 'logistics_branch_id' => $hub->logistics_branch_id, 'rider_id' => $pickupRider->id]);
            return;
        }

        if ($action === 'depart') {
            abort_unless($shipment->logistics_delivery_hub_id === $hub->id, 409, 'Parcel must be received at this delivery hub before departure.');
            abort_unless($shipment->scans()->latest('scanned_at')->value('status') === 'Received at Delivery Hub', 409, 'Receive-scan this parcel before dispatch.');
            abort_if($shipment->order->status === 'out_for_delivery', 409, 'This parcel is already out for delivery.');
            $pickupCollection = LogisticsTransferManifest::query()
                ->where('batch_key', 'pickup-dropoff-'.$shipment->id)
                ->where('origin_type', 'seller_collection')
                ->where('destination_type', 'delivery_hub')
                ->where('destination_id', $hub->id)
                ->where('status', 'arrived')
                ->exists();
            if ($shipment->order->status === 'to_ship' || $pickupCollection) {
                abort_unless($sellerProvince !== '' && $sellerCity !== '', 422, 'Seller province and municipality are required to route this parcel to its sorting center.');
            } else {
                abort_unless($buyerProvince !== '' && $buyerCity !== '', 422, 'Buyer province and municipality are required to route this parcel for delivery.');
            }
            abort_unless(
                $shipment->order->status === 'to_ship'
                    || $pickupCollection
                    || $this->same($hub->province, $buyerProvince) && $this->same($hub->municipality, $buyerCity),
                422,
                'This parcel is not at its next logistics hub.'
            );

            if ($shipment->order->status === 'to_ship' || $pickupCollection) {
                $branch = LogisticsBranch::query()->whereKey($hub->logistics_branch_id)
                    ->where('status', 'active')->whereNull('archived_at')
                    ->whereRaw('LOWER(province) = ?', [mb_strtolower($sellerProvince)])
                    ->first();
                abort_unless($branch, 422, 'No active sorting center is configured for the seller province.');
                $shipment->update(['logistics_branch_id' => $branch->id, 'logistics_delivery_hub_id' => null]);
                if ($shipment->order->status === 'to_ship') {
                    $shipment->order->update(['status' => 'in_transit']);
                    OrderStatusHistory::query()->create([
                        'order_id' => $shipment->order_id,
                        'from_status' => 'to_ship',
                        'to_status' => 'in_transit',
                        'changed_by' => request()->user()->id,
                        'notes' => 'Seller municipality delivery hub dispatched the parcel to its provincial sorting center.',
                    ]);
                }
            } else {
                $deliveryRider = $assignedRiderId
                    ? $this->riderFor($hub, (string) $buyerBarangay, 'delivery', $assignedRiderId)
                    : $this->balancedDeliveryRider($hub, (string) $buyerBarangay);
                abort_unless($deliveryRider, 422, 'No active delivery rider is assigned to the buyer barangay.');
                $shipment->update(['rider_id' => $deliveryRider->id]);
                $shipment->order->update(['status' => 'out_for_delivery']);
                OrderStatusHistory::query()->create([
                    'order_id' => $shipment->order_id,
                    'from_status' => 'in_transit',
                    'to_status' => 'out_for_delivery',
                    'changed_by' => request()->user()->id,
                    'notes' => 'Dispatched from buyer municipality delivery hub for barangay delivery.',
                ]);
            }
            return;
        }

        abort_unless($shipment->logistics_delivery_hub_id === $hub->id && $shipment->order->status !== 'to_ship', 409, 'This parcel is not in this hub’s incoming transfers.');
    }

    private function scanAtCenter(Shipment $shipment, LogisticsBranch $center, string $action, string $buyerProvince, string $buyerCity): void
    {
        abort_unless($shipment->order->status === 'in_transit', 409, 'Parcel must be in transit between logistics facilities.');

        $latestScan = $shipment->scans()->orderByDesc('scanned_at')->orderByDesc('id')->first();
        $centerLocation = $center->name.' · '.$center->province;
        if ($latestScan?->location === $centerLocation) {
            if ($action === 'receive' && $latestScan->status === 'Received at Sorting Center') {
                abort(409, 'Already scanned: this parcel has already been received at this sorting center.');
            }
            if ($action === 'depart' && in_array($latestScan->status, ['In Transit to Delivery Hub', 'In Transit to Sorting Center', 'Out for Delivery'], true)) {
                abort(409, 'Already scanned: this parcel has already been departure-scanned from this sorting center.');
            }
        }

        if ($action === 'receive') {
            abort_unless($shipment->logistics_branch_id === $center->id && $shipment->logistics_delivery_hub_id === null, 409, 'Parcel is not in this sorting center’s incoming transfers.');
            $latestScan = $shipment->scans()->orderByDesc('scanned_at')->orderByDesc('id')->first();
            abort_if(
                $latestScan?->status === 'Received at Sorting Center'
                    && $latestScan->location === $center->name.' · '.$center->province,
                409,
                'This parcel has already been received at this sorting center.',
            );
            return;
        }

        abort_unless($shipment->logistics_branch_id === $center->id, 409, 'Parcel must be received at this sorting center before departure.');
        abort_unless($shipment->scans()->latest('scanned_at')->value('status') === 'Received at Sorting Center', 409, 'Receive-scan this parcel before dispatch.');
        abort_unless($buyerProvince !== '' && $buyerCity !== '', 422, 'Buyer province and municipality are required to route this parcel.');
        if ($this->same($center->province, $buyerProvince)) {
            $hub = LogisticsDeliveryHub::query()->where('status', 'active')->whereNull('archived_at')
                ->where('logistics_branch_id', $center->id)
                ->whereHas('logisticsBranch', fn ($query) => $query
                    ->where('logistics_id', $center->logistics_id)
                    ->where('status', 'active')->whereNull('archived_at'))
                ->whereRaw('LOWER(municipality) = ?', [mb_strtolower($buyerCity)])->first();
            abort_unless($hub, 422, 'No active delivery hub is configured for the buyer municipality.');
            $shipment->update(['logistics_delivery_hub_id' => $hub->id]);
            return;
        }

        $next = $this->nextBranchToward($center, $buyerProvince);
        abort_unless(
            $next,
            422,
            'No active sorting center is configured for the buyer province.'
        );
        abort_unless($next->id !== $center->id, 422, 'The buyer province sorting-center route is not configured.');
        $shipment->update(['logistics_branch_id' => $next->id]);
    }

    private function nextBranchToward(LogisticsBranch $origin, string $destinationProvince): ?LogisticsBranch
    {
        $destination = $this->activeBranch($destinationProvince, $origin->logistics_id);
        if (! $destination || $destination->id === $origin->id) {
            return $destination;
        }

        $routes = LogisticsBranchRoute::query()
            ->where('status', 'active')
            ->whereHas('origin', fn ($query) => $query->where('logistics_id', $origin->logistics_id)
                ->where('status', 'active')->whereNull('archived_at'))
            ->whereHas('next', fn ($query) => $query->where('logistics_id', $origin->logistics_id)
                ->where('status', 'active')->whereNull('archived_at'))
            ->get(['origin_branch_id', 'next_branch_id']);

        if ($routes->isEmpty()) {
            return $destination;
        }

        $graph = [];
        foreach ($routes as $route) {
            $graph[(int) $route->origin_branch_id][] = (int) $route->next_branch_id;
        }

        $originId = (int) $origin->id;
        $destinationId = (int) $destination->id;
        $queue = [$originId];
        $visited = [$originId => true];
        $firstHop = [];

        while ($queue !== []) {
            $currentId = array_shift($queue);
            foreach ($graph[$currentId] ?? [] as $nextId) {
                if (isset($visited[$nextId])) {
                    continue;
                }

                $visited[$nextId] = true;
                $firstHop[$nextId] = $currentId === $originId ? $nextId : $firstHop[$currentId];
                if ($nextId === $destinationId) {
                    return LogisticsBranch::query()->whereKey($firstHop[$nextId])
                        ->where('logistics_id', $origin->logistics_id)
                        ->where('status', 'active')->whereNull('archived_at')->first();
                }

                $queue[] = $nextId;
            }
        }

        // If there is no complete active route, send this transfer directly
        // to the buyer's provincial sorting center as a single fallback hop.
        return $destination;
    }

    private function activeBranch(string $province, ?int $logisticsId = null): ?LogisticsBranch
    {
        return LogisticsBranch::query()->whereNull('archived_at')->where('status', 'active')
            ->when($logisticsId !== null, fn ($query) => $query->where('logistics_id', $logisticsId))
            ->whereRaw('LOWER(province) = ?', [mb_strtolower($province)])->orderBy('id')->first();
    }

    private function riderFor(LogisticsDeliveryHub $hub, string $barangay, string $operation, ?int $riderId = null): ?Rider
    {
        if (trim($barangay) === '') {
            return null;
        }

        return Rider::query()->where('registration_status', 'active')
            ->whereHas('user', fn ($query) => $query->where('registration_status', 'active'))
            ->when($riderId !== null, fn ($query) => $query->whereKey($riderId))
            ->where('logistics_branch_id', $hub->logistics_branch_id)
            ->whereRaw('LOWER(COALESCE(assignment_barangay, barangay)) = ?', [mb_strtolower(trim($barangay))])
            ->where(function ($query) use ($hub): void {
                $query->whereRaw('LOWER(municipal_hub) = ?', [mb_strtolower($hub->hub_name)])
                    ->orWhereRaw('LOWER(municipal_hub) = ?', [mb_strtolower($hub->municipality)]);
            })
            ->whereRaw('LOWER(logistics_operation) LIKE ?', ['%'.mb_strtolower($operation).'%'])
            ->orderBy('id')
            ->first();
    }

    private function balancedDeliveryRider(LogisticsDeliveryHub $hub, string $barangay): ?Rider
    {
        return Rider::query()->where('registration_status', 'active')
            ->whereHas('user', fn ($query) => $query->where('registration_status', 'active'))
            ->where('logistics_branch_id', $hub->logistics_branch_id)
            ->whereRaw('LOWER(COALESCE(assignment_barangay, barangay)) = ?', [mb_strtolower(trim($barangay))])
            ->where(function ($query) use ($hub): void {
                $query->whereRaw('LOWER(municipal_hub) = ?', [mb_strtolower($hub->hub_name)])
                    ->orWhereRaw('LOWER(municipal_hub) = ?', [mb_strtolower($hub->municipality)]);
            })
            ->whereRaw("LOWER(REPLACE(REPLACE(logistics_operation, ' ', ''), '-', '')) LIKE ?", ['%delivery%'])
            ->orderBy('id')->get()
            ->map(fn (Rider $rider): array => [
                'rider' => $rider,
                'load' => $rider->shipments()->whereHas('order', fn ($query) => $query->where('status', 'out_for_delivery'))->count(),
            ])
            ->sort(fn (array $left, array $right): int => [$left['load'], $left['rider']->id] <=> [$right['load'], $right['rider']->id])
            ->first()['rider'] ?? null;
    }

    private function same(?string $left, ?string $right): bool
    {
        return mb_strtolower(trim((string) $left)) === mb_strtolower(trim((string) $right));
    }
}
