<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\Shipment;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\LogisticsTransferManifest;
use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\SortingCenterFleetArchive;
use App\Models\Logistics\SortingCenterVehicle;
use App\Models\Rider\Rider;
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
        ]);

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

            if ($facility === 'delivery_hub') {
                $hub = LogisticsDeliveryHub::query()->where('user_id', $request->user()->id)->whereNull('archived_at')->firstOrFail();
                $pickupDropoffAtThisHub = $order->status === 'in_transit'
                    && $this->same($hub->province, $sellerProvince)
                    && $this->same($hub->municipality, $sellerCity)
                    && (int) $shipment->logistics_delivery_hub_id === (int) $hub->id;
                if ($input['action'] === 'receive' && ($order->status === 'to_ship' || $pickupDropoffAtThisHub)) {
                    $awaitingDropoffScan = LogisticsTransferManifest::query()
                        ->where('destination_type', 'delivery_hub')
                        ->where('destination_id', $hub->id)
                        ->where('origin_type', 'seller_collection')
                        ->where('status', 'loaded')
                        ->where('batch_key', 'pickup-dropoff-'.$shipment->id)
                        ->whereHas('shipments', fn ($query) => $query
                            ->whereKey($shipment->id)
                            ->whereNull('logistics_transfer_manifest_shipments.received_at'))
                        ->exists();
                    abort_unless($awaitingDropoffScan, 409, 'The rider must confirm drop-off before this parcel can be receive-scanned.');
                }
                $this->scanAtHub($shipment, $hub, $input['action'], $sellerProvince, $sellerCity, $seller?->barangay, $buyerProvince, $buyerCity, $buyer?->barangay);
                $location = $hub->hub_name.' · '.$hub->municipality.', '.$hub->province;
            } else {
                $center = LogisticsBranch::query()->where('user_id', $request->user()->id)->whereNull('archived_at')->firstOrFail();
                $this->scanAtCenter($shipment, $center, $input['action'], $buyerProvince, $buyerCity);
                $location = $center->name.' · '.$center->province;
            }

            $facilityId = $facility === 'delivery_hub' ? $hub->id : $center->id;
            if ($input['action'] === 'receive') {
                $inbound = LogisticsTransferManifest::query()
                    ->where('destination_type', $facility)->where('destination_id', $facilityId)
                    ->where('status', 'loaded')->whereHas('shipments', fn ($query) => $query->whereKey($shipment->id))
                    ->lockForUpdate()->first();
                if ($inbound) {
                    $inbound->shipments()->updateExistingPivot($shipment->id, ['received_at' => now()]);
                    if (! $inbound->shipments()->wherePivotNull('received_at')->exists()) {
                        $inbound->update(['status' => 'arrived', 'arrived_at' => now()]);
                    }
                }
            if ($facility === 'delivery_hub' && $order->status === 'to_ship' && ! $inbound) {
                    $collection = LogisticsTransferManifest::query()->firstOrCreate(
                        ['batch_key' => 'seller-collection-'.$shipment->id],
                        [
                            'origin_type' => 'seller_collection', 'origin_id' => $hub->id,
                            'destination_type' => 'delivery_hub', 'destination_id' => $hub->id,
                            'courier_name' => $shipment->rider?->first_name.' '.$shipment->rider?->last_name,
                            'status' => 'arrived', 'loaded_at' => now(), 'arrived_at' => now(),
                        ],
                    );
                    $collection->shipments()->syncWithoutDetaching([$shipment->id => ['received_at' => now()]]);
                }
            } elseif ($shipment->order->status === 'in_transit') {
                $destinationType = $shipment->logistics_delivery_hub_id ? 'delivery_hub' : 'sorting_center';
                $destinationId = $shipment->logistics_delivery_hub_id ?: $shipment->logistics_branch_id;
                abort_unless($destinationId, 422, 'The next destination facility could not be resolved.');
                $batchKey = $input['batch_key'] ?: 'scan-'.$shipment->id.'-'.now()->format('YmdHisv');
                $trip = null;
                if (! empty($input['returning_manifest_id'])) {
                    $trip = LogisticsTransferManifest::query()->lockForUpdate()->findOrFail($input['returning_manifest_id']);
                    abort_unless($trip->status === 'arrived' && $trip->destination_type === $facility && (int) $trip->destination_id === (int) $facilityId, 422, 'The selected inbound truck has not arrived at this facility.');
                    abort_unless($trip->origin_type === $destinationType && (int) $trip->origin_id === (int) $destinationId, 422, 'Return cargo must be routed back to the truck’s originating facility.');
                    $existingReturn = LogisticsTransferManifest::query()->where('returning_manifest_id', $trip->id)->first();
                    abort_if($existingReturn && $existingReturn->batch_key !== $batchKey, 409, 'This truck already has a return cargo assignment.');
                } else {
                    if ($facility === 'sorting_center' && $destinationType === 'sorting_center') {
                        $arrivedTruck = LogisticsTransferManifest::query()
                            ->where('origin_type', 'sorting_center')->where('origin_id', $destinationId)
                            ->where('destination_type', 'sorting_center')->where('destination_id', $facilityId)
                            ->where('status', 'arrived')
                            ->whereNotIn('id', LogisticsTransferManifest::query()->whereNotNull('returning_manifest_id')->select('returning_manifest_id'))
                            ->exists();
                        abort_unless(! $arrivedTruck, 422, 'This sorting-center truck must be assigned return cargo to its origin center.');
                    }
                    $courierId = $input['courier_registration_id'] ?? null;
                    $vehicleId = $input['vehicle_id'] ?? null;
                    if ($facility === 'sorting_center') {
                        $centerId = $facilityId;
                        $courier = CourierRegistration::query()->where('sorting_center_id', $centerId)->where('status', 'approved')->find($courierId);
                        $vehicle = SortingCenterVehicle::query()->where('sorting_center_id', $centerId)->find($vehicleId);
                        abort_unless($courier && $vehicle && mb_strtolower($courier->plate_number) === mb_strtolower($vehicle->plate_number), 422, 'Select an approved courier and the vehicle registered to that courier.');
                        abort_unless(mb_strtolower((string) $vehicle->availability) === 'available', 422, 'The assigned truck is not available.');
                        abort_if(LogisticsTransferManifest::query()->where('status', 'loaded')->where(function ($query) use ($courierId, $vehicleId): void {
                            $query->where('courier_registration_id', $courierId)->orWhere('vehicle_id', $vehicleId);
                        })->exists(), 409, 'This courier or vehicle is already on an active trip.');
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
                        'status' => 'loaded', 'loaded_at' => now(),
                    ],
                );
                abort_unless(
                    $outbound->origin_type === $facility
                    && (int) $outbound->origin_id === (int) $facilityId
                    && $outbound->destination_type === $destinationType
                    && (int) $outbound->destination_id === (int) $destinationId
                    && (int) $outbound->returning_manifest_id === (int) ($trip?->id),
                    409,
                    'This batch is already assigned to another route.',
                );
                $outbound->shipments()->syncWithoutDetaching([$shipment->id]);
            }

            $shipment->current_location = $location;
            $shipment->location_updated_at = now();
            $shipment->save();

            $label = $input['action'] === 'receive' ? 'Received at '.($facility === 'delivery_hub' ? 'Delivery Hub' : 'Sorting Center') : ($order->status === 'out_for_delivery' ? 'Out for Delivery' : 'Departed');
            $shipment->scans()->create(['status' => $label, 'location' => $location, 'scanned_at' => now()]);

            return $shipment->fresh(['order', 'scans']);
        });

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

    public function queue(Request $request): JsonResponse
    {
        $facility = $request->user()->role;
        abort_unless(in_array($facility, ['sorting_center', 'delivery_hub'], true), 403);
        $node = $facility === 'delivery_hub'
            ? LogisticsDeliveryHub::query()->where('user_id', $request->user()->id)->whereNull('archived_at')->firstOrFail()
            : LogisticsBranch::query()->where('user_id', $request->user()->id)->whereNull('archived_at')->firstOrFail();
        $id = $node->id;
        $transfers = LogisticsTransferManifest::query()
            ->where('destination_type', $facility)->where('destination_id', $id)
            ->whereIn('status', ['loaded', 'arrived'])
            ->with(['shipments.order.seller.user', 'shipments.order.buyer.buyerProfile', 'shipments.order.items.product', 'returnTrip'])
            ->latest('loaded_at')->get();

        $parcels = [];
        $manifests = [];
        foreach ($transfers as $transfer) {
            $members = [];
            foreach ($transfer->shipments as $shipment) {
                $isAtFacility = $facility === 'delivery_hub'
                    ? (int) $shipment->logistics_delivery_hub_id === (int) $id
                    : (int) $shipment->logistics_branch_id === (int) $id && $shipment->logistics_delivery_hub_id === null;
                if (! $isAtFacility) continue;
                $received = $shipment->pivot->received_at !== null;
                $parcelId = 'shipment-'.$shipment->id;
                $members[] = $parcelId;
                $parcelStatus = $shipment->order?->status === 'out_for_delivery' ? 'out-for-delivery' : ($received ? 'received' : 'incoming');
                $payload = $this->parcelPayload($shipment, $parcelId, $parcelStatus, $facility === 'sorting_center' ? $id : null, $transfer->id);
                if ($transfer->origin_type === 'seller_collection') {
                    $payload['kind'] = 'pickup';
                    if (str_starts_with($transfer->batch_key, 'pickup-dropoff-')) {
                        $payload['dropoffConfirmed'] = true;
                        $payload['scheduledDate'] = now('Asia/Manila')->toDateString();
                    }
                }
                $parcels[] = $payload;
            }
            if ($members) {
                $manifests[] = [
                    'id' => (string) $transfer->id,
                    'sourceType' => $transfer->origin_type === 'sorting_center' ? 'sorting' : 'collection',
                    'originName' => $this->facilityName($transfer->origin_type, $transfer->origin_id),
                    'destination' => $this->facilityName($facility, $id),
                    'courierName' => $transfer->courier_name,
                    'truckId' => $transfer->vehicle_reference,
                    'courierRegistrationId' => $transfer->courier_registration_id,
                    'vehicleId' => $transfer->vehicle_id,
                    'status' => $transfer->status,
                    'arrivedAt' => $transfer->arrived_at?->toISOString(),
                    'originCenterId' => $transfer->origin_type === 'sorting_center' ? $transfer->origin_id : null,
                    'originType' => $transfer->origin_type,
                    'originId' => $transfer->origin_id,
                    'returnManifestId' => $transfer->returning_manifest_id,
                    'parcelIds' => $members,
                ];
            }
        }

        $couriers = $vehicles = [];
        $activeDispatches = LogisticsTransferManifest::query()->where('origin_type', $facility)->where('origin_id', $id)->where('status', 'loaded')->get(['courier_registration_id', 'vehicle_id', 'courier_name', 'vehicle_reference']);
        if ($facility === 'sorting_center') {
            $archivedCourierIds = SortingCenterFleetArchive::query()->where('sorting_center_id', $id)->where('record_type', 'couriers')->pluck('record_id');
            $archivedVehicleIds = SortingCenterFleetArchive::query()->where('sorting_center_id', $id)->where('record_type', 'vehicles')->pluck('record_id');
            $couriers = CourierRegistration::query()->where('sorting_center_id', $id)->where('status', 'approved')->whereNotIn('id', $archivedCourierIds)->get(['id', 'first_name', 'middle_name', 'last_name', 'phone', 'plate_number', 'vehicle'])->values();
            $vehicles = SortingCenterVehicle::query()->where('sorting_center_id', $id)->whereNotIn('id', $archivedVehicleIds)->get(['id', 'name', 'type', 'plate_number', 'availability'])->values();
        }

        $assignedReturnIds = LogisticsTransferManifest::query()
            ->whereNotNull('returning_manifest_id')->pluck('returning_manifest_id')->all();

        return response()->json([
            'facility_type' => $facility,
            'facility_id' => $id,
            'parcels' => $parcels,
            'manifests' => $manifests,
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
            'couriers' => $couriers,
            'vehicles' => $vehicles,
            'active_dispatches' => $activeDispatches,
        ]);
    }

    private function parcelPayload(Shipment $shipment, string $id, string $status, ?int $centerId, int|string $transferId): array
    {
        $order = $shipment->order;
        $seller = $order?->seller;
        $buyer = $order?->buyer?->buyerProfile;
        return [
            'id' => $id, 'shipment_id' => $shipment->id, 'order_id' => $order?->id,
            'tracking' => $shipment->tracking_number, 'order_number' => $order?->order_number,
            'status' => $status, 'kind' => str_starts_with((string) $transferId, 'collection-') ? 'pickup' : 'sorting', 'transferId' => (string) $transferId,
            'pickupDate' => $order?->pickup_date?->toDateString(),
            'currentCenterId' => $centerId, 'nextCenterId' => null,
            'buyer_name' => $order?->delivery_name ?: trim(($buyer?->first_name ?? '').' '.($buyer?->last_name ?? '')),
            'buyer_email' => $order?->buyer?->email, 'buyer_phone' => $order?->delivery_phone ?: $buyer?->contact_no,
            'buyer_province' => $buyer?->province, 'buyer_city' => $buyer?->municipality,
            'buyer_barangay' => $buyer?->barangay, 'buyer_street' => $buyer?->street,
            'buyer_house_no' => $buyer?->house_number, 'buyer_postal_code' => $buyer?->zip_code,
            'seller_name' => $seller?->store_name ?: trim(($seller?->first_name ?? '').' '.($seller?->last_name ?? '')),
            'seller_email' => $seller?->user?->email, 'seller_phone' => $seller?->contact_no,
            'seller_province' => $seller?->province, 'seller_city' => $seller?->municipality,
            'seller_barangay' => $seller?->barangay, 'seller_street' => $seller?->street,
            'seller_house_no' => $seller?->house_number, 'seller_postal_code' => $seller?->zip_code,
            'destProvince' => $buyer?->province, 'destCity' => $buyer?->municipality, 'destBarangay' => $buyer?->barangay,
            'total_amount' => (float) ($order?->total ?? 0), 'payment_method' => $order?->payment_method,
            'items' => $order?->items?->map(fn ($item) => ['name' => $item->product?->name ?? $item->product_name ?? 'Item', 'quantity' => $item->quantity, 'price' => $item->price])->values() ?? [],
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

    private function scanAtHub(Shipment $shipment, LogisticsDeliveryHub $hub, string $action, string $sellerProvince, string $sellerCity, ?string $sellerBarangay, string $buyerProvince, string $buyerCity, ?string $buyerBarangay): void
    {
        if ($action === 'receive' && $shipment->order->status === 'to_ship') {
            abort_unless($this->same($hub->province, $sellerProvince) && $this->same($hub->municipality, $sellerCity), 422, 'Seller pickup parcels must first arrive at the delivery hub for the seller municipality.');
            abort_if($shipment->scans()->latest('scanned_at')->value('status') === 'Received at Delivery Hub', 409, 'This parcel has already been received at the seller hub.');
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
                $branch = $this->activeBranch($sellerProvince);
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
                $deliveryRider = $this->riderFor($hub, (string) $buyerBarangay, 'delivery');
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

        if ($action === 'receive') {
            abort_unless($shipment->logistics_branch_id === $center->id && $shipment->logistics_delivery_hub_id === null, 409, 'Parcel is not in this sorting center’s incoming transfers.');
            abort_if($shipment->scans()->latest('scanned_at')->value('status') === 'Received at Sorting Center', 409, 'This parcel has already been received at this sorting center.');
            return;
        }

        abort_unless($shipment->logistics_branch_id === $center->id, 409, 'Parcel must be received at this sorting center before departure.');
        abort_unless($shipment->scans()->latest('scanned_at')->value('status') === 'Received at Sorting Center', 409, 'Receive-scan this parcel before dispatch.');
        abort_unless($buyerProvince !== '' && $buyerCity !== '', 422, 'Buyer province and municipality are required to route this parcel.');
        if ($this->same($center->province, $buyerProvince)) {
            $hub = LogisticsDeliveryHub::query()->where('logistics_branch_id', $center->id)->whereNull('archived_at')
                ->whereRaw('LOWER(municipality) = ?', [mb_strtolower($buyerCity)])->first();
            abort_unless($hub, 422, 'No active delivery hub is configured for the buyer municipality.');
            $shipment->update(['logistics_delivery_hub_id' => $hub->id]);
            return;
        }

        $next = $this->activeBranch($buyerProvince);
        abort_unless($next, 422, 'No active sorting center is configured for the buyer province.');
        abort_unless($next->id !== $center->id, 422, 'The buyer province sorting-center route is not configured.');
        $shipment->update(['logistics_branch_id' => $next->id]);
    }

    private function activeBranch(string $province): ?LogisticsBranch
    {
        return LogisticsBranch::query()->whereNull('archived_at')->where('status', 'active')
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

    private function same(?string $left, ?string $right): bool
    {
        return mb_strtolower(trim((string) $left)) === mb_strtolower(trim((string) $right));
    }
}
