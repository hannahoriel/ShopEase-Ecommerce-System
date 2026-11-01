<?php

namespace App\Http\Controllers\Api\Rider;

use App\Http\Controllers\Controller;
use App\Models\Admin\Shipment;
use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\LogisticsTransferManifest;
use App\Models\Logistics\LogisticsTransferManifestEvent;
use App\Models\Logistics\SortingCenterVehicle;
use App\Models\User;
use App\Notifications\CourierArrivalNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourierTransportController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $courier = $this->courier($request);
        $active = $this->activeManifests($courier)->with('shipments.order')->get();
        $current = $active->sortByDesc('scheduled_at')->sortByDesc('created_at')->first();
        $manifestIds = $active->modelKeys();
        $loadedCount = $manifestIds
            ? DB::table('logistics_transfer_manifest_shipments')
                ->whereIn('transfer_manifest_id', $manifestIds)
                ->where('load_status', 'loaded')
                ->whereNull('received_at')
                ->count()
            : 0;
        $outboundCount = $this->assignedManifests($courier)
            ->whereNull('returning_manifest_id')
            ->whereIn('status', ['preparing', 'loaded', 'arrived'])
            ->where(fn (Builder $query) => $query
                ->whereIn('status', ['preparing', 'loaded'])
                ->orWhereNull('arrived_at'))
            ->withCount('shipments')
            ->get()
            ->sum('shipments_count');
        $returnCount = $this->assignedManifests($courier)
            ->whereNotNull('returning_manifest_id')
            ->whereIn('status', ['preparing', 'loaded', 'arrived'])
            ->where(fn (Builder $query) => $query
                ->whereIn('status', ['preparing', 'loaded'])
                ->orWhereNull('arrived_at'))
            ->withCount('shipments')
            ->get()
            ->sum('shipments_count');

        return response()->json([
            'data' => [
                'profile' => $this->profilePayload($courier),
                'summary' => [
                    'in_truck' => $loadedCount,
                    'outbound_parcels' => $outboundCount,
                    'return_parcels' => $returnCount,
                    'active_trips' => $active->count(),
                ],
                'current_trip' => $current ? $this->manifestPayload($current, true) : null,
            ],
        ]);
    }

    public function trips(Request $request): JsonResponse
    {
        $courier = $this->courier($request);
        $filters = $request->validate([
            'scope' => ['nullable', Rule::in(['active', 'history', 'all'])],
        ]);
        $scope = $filters['scope'] ?? 'active';
        $query = $this->assignedManifests($courier)->with('shipments.order');

        if ($scope === 'history') {
            $query->where('status', 'arrived')->whereNotNull('arrived_at');
        } elseif ($scope === 'active') {
            $query->whereIn('status', ['preparing', 'loaded', 'arrived'])
                ->where(fn (Builder $active) => $active
                    ->whereIn('status', ['preparing', 'loaded'])
                    ->orWhereNull('arrived_at'));
        }

        return response()->json([
            'data' => $query->latest('created_at')->get()
                ->map(fn (LogisticsTransferManifest $manifest): array => $this->manifestPayload($manifest)),
        ]);
    }

    public function manifests(Request $request): JsonResponse
    {
        $courier = $this->courier($request);
        $filters = $request->validate([
            'direction' => ['required', Rule::in(['outbound', 'return'])],
        ]);
        $query = $this->assignedManifests($courier)->with('shipments.order');

        if ($filters['direction'] === 'return') {
            $query->whereNotNull('returning_manifest_id');
        } else {
            $query->whereNull('returning_manifest_id');
        }

        return response()->json([
            'data' => $query->latest('created_at')->get()
                ->map(fn (LogisticsTransferManifest $manifest): array => $this->manifestPayload($manifest, true)),
        ]);
    }

    public function showManifest(Request $request, int $manifestId): JsonResponse
    {
        $manifest = $this->manifestForCourier($request, $manifestId);
        $manifest->load(['shipments.order', 'events']);

        return response()->json(['data' => $this->manifestPayload($manifest, true)]);
    }

    public function loadParcel(Request $request, int $manifestId): JsonResponse
    {
        $data = $request->validate([
            'tracking' => ['required', 'string', 'max:2048'],
        ]);
        $courier = $this->courier($request);
        $scanned = trim($data['tracking']);
        $path = parse_url($scanned, PHP_URL_PATH);
        $identifier = is_string($path) && $path !== ''
            ? rawurldecode(basename($path))
            : $scanned;

        $shipment = DB::transaction(function () use ($request, $courier, $manifestId, $identifier): Shipment {
            $manifest = $this->lockAssignedManifest($courier, $manifestId);
            abort_unless(in_array($manifest->status, ['preparing', 'loaded'], true), 409, 'This trip is no longer accepting parcel loads.');
            abort_if($manifest->departed_at || $manifest->arrival_confirmed_at, 409, 'This trip is no longer accepting parcel loads.');

            $shipment = $manifest->shipments()
                ->where(fn (Builder $query) => $query
                    ->where('tracking_number', $identifier)
                    ->orWhere('scan_token', $identifier)
                    ->orWhereHas('order', fn (Builder $order) => $order->where('order_number', $identifier)))
                ->lockForUpdate()
                ->first();
            abort_unless($shipment, 404, 'This parcel is not assigned to the selected manifest.');

            $origin = $this->facility($manifest->origin_type, $manifest->origin_id);
            $physicallyReceived = $shipment->scans()
                ->where('status', 'Received at Sorting Center')
                ->where('location', $origin['name'].' · '.$origin['province'])
                ->exists();
            abort_unless(
                $physicallyReceived
                    && $shipment->current_location === $origin['name'].' · '.$origin['province'],
                409,
                'Only parcels physically received at the origin sorting center can be loaded.',
            );

            $shipment->pivot->load_status = 'loaded';
            $shipment->pivot->loaded_at = now();
            $shipment->pivot->loaded_by = $request->user()->id;
            $shipment->pivot->discrepancy_note = null;
            $shipment->pivot->save();

            $this->recordEvent(
                $manifest,
                $request->user(),
                'parcel_loaded',
                $shipment,
            );
            $shipment->scans()->create([
                'status' => $manifest->returning_manifest_id ? 'Loaded onto return transport' : 'Loaded onto outbound transport',
                'location' => $origin['name'].' · '.$origin['province'],
                'scanned_at' => now(),
            ]);

            return $shipment;
        });

        return response()->json([
            'message' => 'Parcel load confirmed.',
            'data' => [
                'shipment_id' => $shipment->id,
                'tracking' => $shipment->tracking_number,
                'load_status' => 'loaded',
            ],
        ]);
    }

    public function reconcileParcel(Request $request, int $manifestId, int $shipmentId): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['missing', 'rejected'])],
            'notes' => ['required', 'string', 'max:1000'],
        ]);
        $courier = $this->courier($request);

        DB::transaction(function () use ($request, $courier, $manifestId, $shipmentId, $data): void {
            $manifest = $this->lockAssignedManifest($courier, $manifestId);
            abort_unless(in_array($manifest->status, ['preparing', 'loaded'], true), 409, 'This trip can no longer be reconciled for loading.');
            abort_if($manifest->departed_at || $manifest->arrival_confirmed_at, 409, 'This trip can no longer be reconciled for loading.');
            $shipment = $manifest->shipments()->lockForUpdate()->findOrFail($shipmentId);
            abort_if($shipment->pivot->load_status === 'loaded', 409, 'A loaded parcel cannot be marked missing or rejected.');

            $shipment->pivot->load_status = $data['status'];
            $shipment->pivot->discrepancy_note = trim($data['notes']);
            $shipment->pivot->save();
            $this->recordEvent($manifest, $request->user(), 'parcel_'.$data['status'], $shipment, $data['notes']);
        });

        return response()->json(['message' => 'Manifest discrepancy recorded.']);
    }

    public function confirmDeparture(Request $request, int $manifestId): JsonResponse
    {
        $courier = $this->courier($request);
        $manifest = DB::transaction(function () use ($request, $courier, $manifestId): LogisticsTransferManifest {
            $manifest = $this->lockAssignedManifest($courier, $manifestId);
            abort_if($manifest->departed_at, 409, 'Departure has already been confirmed.');
            abort_if($manifest->arrival_confirmed_at, 409, 'This trip has already arrived.');
            abort_unless(in_array($manifest->status, ['preparing', 'loaded'], true), 409, 'This trip is not ready for departure.');
            abort_unless($manifest->vehicle_id && filled($manifest->vehicle_reference), 409, 'An assigned truck is required before departure.');

            $parcels = DB::table('logistics_transfer_manifest_shipments')
                ->where('transfer_manifest_id', $manifest->id)
                ->lockForUpdate();
            abort_unless((clone $parcels)->exists(), 409, 'The manifest contains no parcels.');
            abort_if((clone $parcels)->where('load_status', 'pending')->exists(), 409, 'Scan or reconcile every expected parcel before departure.');
            abort_unless((clone $parcels)->where('load_status', 'loaded')->exists(), 409, 'At least one parcel must be loaded before departure.');

            $loadedShipmentIds = (clone $parcels)
                ->where('load_status', 'loaded')
                ->pluck('shipment_id');
            Shipment::query()->whereIn('id', $loadedShipmentIds)->update([
                'logistics_branch_id' => $manifest->destination_type === 'sorting_center'
                    ? $manifest->destination_id
                    : null,
                'logistics_delivery_hub_id' => $manifest->destination_type === 'delivery_hub'
                    ? $manifest->destination_id
                    : null,
            ]);

            $manifest->update([
                'status' => 'loaded',
                'loaded_at' => $manifest->loaded_at ?? now(),
                'departed_at' => now(),
            ]);
            $this->recordEvent($manifest, $request->user(), 'departure_confirmed');

            return $manifest->fresh(['shipments.order']);
        });

        return response()->json(['data' => $this->manifestPayload($manifest, true)]);
    }

    public function confirmArrival(Request $request, int $manifestId): JsonResponse
    {
        $courier = $this->courier($request);
        $manifest = DB::transaction(function () use ($request, $courier, $manifestId): LogisticsTransferManifest {
            $manifest = $this->lockAssignedManifest($courier, $manifestId);
            abort_unless($manifest->departed_at, 409, 'Confirm departure before confirming arrival.');
            abort_if($manifest->arrival_confirmed_at, 409, 'Arrival has already been confirmed.');
            abort_if($manifest->arrived_at, 409, 'This trip has already been completed.');

            $manifest->update(['arrival_confirmed_at' => now()]);
            $this->recordEvent($manifest, $request->user(), 'arrival_confirmed');
            $this->notifyReceivingFacility($manifest);

            return $manifest->fresh(['shipments.order']);
        });

        return response()->json([
            'message' => 'Arrival reported. Receiving staff must scan and reconcile each parcel.',
            'data' => $this->manifestPayload($manifest, true),
        ]);
    }

    public function returnPickup(Request $request): JsonResponse
    {
        $courier = $this->courier($request);
        $trips = $this->assignedManifests($courier)
            ->whereNull('returning_manifest_id')
            ->where('origin_type', 'sorting_center')
            ->where('origin_id', $courier->sorting_center_id)
            ->where('destination_type', 'sorting_center')
            ->where('status', 'arrived')
            ->whereNotNull('arrived_at')
            ->whereNotNull('arrival_confirmed_at')
            ->whereDoesntHave('returnTrip')
            ->latest('arrived_at')
            ->get();

        return response()->json([
            'data' => $trips->map(function (LogisticsTransferManifest $trip) use ($courier): array {
                $trip->load('shipments.order');

                return [
                    'trip' => $this->manifestPayload($trip),
                    'eligible_parcels' => $this->eligibleReturnShipments($courier, $trip)
                        ->get()
                        ->map(fn (Shipment $shipment): array => $this->shipmentPayload($shipment)),
                ];
            }),
        ]);
    }

    public function prepareReturnManifest(Request $request): JsonResponse
    {
        $data = $request->validate([
            'outbound_manifest_id' => ['required', 'integer'],
            'shipment_ids' => ['required', 'array', 'min:1', 'max:500'],
            'shipment_ids.*' => ['required', 'integer', 'distinct'],
            'scheduled_at' => ['nullable', 'date'],
        ]);
        $courier = $this->courier($request);

        $manifest = DB::transaction(function () use ($request, $courier, $data): LogisticsTransferManifest {
            $trip = $this->assignedManifests($courier)
                ->whereKey($data['outbound_manifest_id'])
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless(
                $trip->origin_type === 'sorting_center'
                && (int) $trip->origin_id === (int) $courier->sorting_center_id
                && $trip->destination_type === 'sorting_center'
                && $trip->status === 'arrived'
                && $trip->arrived_at
                && $trip->arrival_confirmed_at
                && ! $trip->returnTrip()->exists(),
                409,
                'Return cargo can only be prepared after this outbound trip is fully received at its destination.',
            );
            abort_if(
                $this->activeManifests($courier)->lockForUpdate()->exists(),
                409,
                'This courier already has an active transport trip.',
            );
            abort_if(
                LogisticsTransferManifest::query()
                    ->where('vehicle_id', $trip->vehicle_id)
                    ->whereIn('status', ['preparing', 'loaded', 'arrived'])
                    ->where(fn (Builder $active) => $active
                        ->whereIn('status', ['preparing', 'loaded'])
                        ->orWhereNull('arrived_at'))
                    ->lockForUpdate()
                    ->exists(),
                409,
                'The assigned truck is already on an active transport trip.',
            );

            $ids = array_map('intval', $data['shipment_ids']);
            $eligible = $this->eligibleReturnShipments($courier, $trip)
                ->whereIn('shipments.id', $ids)
                ->lockForUpdate()
                ->get();
            abort_unless($eligible->count() === count($ids), 409, 'One or more parcels are no longer eligible for return transport.');

            $manifest = LogisticsTransferManifest::query()->create([
                'batch_key' => 'pending-'.Str::uuid(),
                'origin_type' => 'sorting_center',
                'origin_id' => $trip->destination_id,
                'destination_type' => 'sorting_center',
                'destination_id' => $courier->sorting_center_id,
                'courier_registration_id' => $courier->id,
                'vehicle_id' => $trip->vehicle_id,
                'courier_name' => $trip->courier_name,
                'vehicle_reference' => $trip->vehicle_reference,
                'returning_manifest_id' => $trip->id,
                'status' => 'preparing',
                'scheduled_at' => $data['scheduled_at'] ?? null,
            ]);
            $manifest->update(['batch_key' => 'RTN-'.str_pad((string) $manifest->id, 8, '0', STR_PAD_LEFT)]);

            $manifest->shipments()->attach($eligible->mapWithKeys(fn (Shipment $shipment): array => [
                $shipment->id => ['load_status' => 'pending'],
            ])->all());
            $this->recordEvent($manifest, $request->user(), 'return_manifest_prepared', null, null, [
                'outbound_manifest_id' => $trip->id,
                'shipment_ids' => $ids,
            ]);

            return $manifest->fresh(['shipments.order']);
        });

        return response()->json(['data' => $this->manifestPayload($manifest, true)], 201);
    }

    private function courier(Request $request): CourierRegistration
    {
        $courier = CourierRegistration::query()
            ->with(['sortingCenter.logistics', 'user'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        abort_unless($courier->status === 'approved' && $courier->sortingCenter, 403, 'An approved sorting-center assignment is required.');

        return $courier;
    }

    private function assignedManifests(CourierRegistration $courier): Builder
    {
        $fullName = trim(implode(' ', array_filter([
            $courier->first_name,
            $courier->middle_name,
            $courier->last_name,
        ])));
        $firstLastName = trim($courier->first_name.' '.$courier->last_name);
        $nameVariants = array_values(array_unique(array_filter([$fullName, $firstLastName])));
        $plate = mb_strtolower(trim((string) $courier->plate_number));

        return LogisticsTransferManifest::query()
            ->where(function (Builder $query) use ($courier, $nameVariants, $plate): void {
                $query->where('courier_registration_id', $courier->id)
                    ->orWhere(function (Builder $legacy) use ($courier, $nameVariants, $plate): void {
                        $legacy->whereNull('courier_registration_id')
                            ->where(function (Builder $route) use ($courier): void {
                                $route->where(function (Builder $hubTransfer) use ($courier): void {
                                    $hubTransfer->where('origin_type', 'delivery_hub')
                                        ->where('destination_type', 'sorting_center')
                                        ->where('destination_id', $courier->sorting_center_id);
                                })->orWhere(function (Builder $sortingTransfer) use ($courier): void {
                                    $sortingTransfer->where('origin_type', 'sorting_center')
                                        ->where('origin_id', $courier->sorting_center_id)
                                        ->whereIn('destination_type', ['sorting_center', 'delivery_hub']);
                                });
                            })
                            ->whereIn(DB::raw('LOWER(TRIM(courier_name))'), array_map('mb_strtolower', $nameVariants));
                        if ($plate !== '') {
                            $legacy->whereRaw('LOWER(vehicle_reference) LIKE ?', ['%'.$plate.'%']);
                        }
                    });
            });
    }

    private function activeManifests(CourierRegistration $courier): Builder
    {
        return $this->assignedManifests($courier)
            ->whereIn('status', ['preparing', 'loaded', 'arrived'])
            ->where(fn (Builder $query) => $query
                ->whereIn('status', ['preparing', 'loaded'])
                ->orWhereNull('arrived_at'));
    }

    private function manifestForCourier(Request $request, int $manifestId): LogisticsTransferManifest
    {
        return $this->assignedManifests($this->courier($request))
            ->with(['shipments.order', 'events'])
            ->findOrFail($manifestId);
    }

    private function lockAssignedManifest(CourierRegistration $courier, int $manifestId): LogisticsTransferManifest
    {
        return $this->assignedManifests($courier)
            ->lockForUpdate()
            ->findOrFail($manifestId);
    }

    private function profilePayload(CourierRegistration $courier): array
    {
        $center = $courier->sortingCenter;
        return [
            'name' => trim(implode(' ', array_filter([
                $courier->first_name,
                $courier->middle_name,
                $courier->last_name,
            ]))),
            'email' => $courier->email,
            'phone' => $courier->phone,
            'courier_company' => $center->logistics?->business_name,
            'sorting_center_id' => $center->id,
            'sorting_center_name' => $center->name,
            'sorting_center_address' => $this->facilityAddress($center->address, $center->municipality, $center->province),
            'vehicle' => $courier->vehicle,
            'plate_number' => $courier->plate_number,
            'shift' => null,
        ];
    }

    private function manifestPayload(LogisticsTransferManifest $manifest, bool $includeParcels = false): array
    {
        $manifest->loadMissing('shipments.order');
        $origin = $this->facility($manifest->origin_type, $manifest->origin_id);
        $destination = $this->facility($manifest->destination_type, $manifest->destination_id);
        $parcels = $manifest->shipments;
        $loaded = $parcels->filter(fn (Shipment $shipment): bool => $shipment->pivot->load_status === 'loaded');
        $pending = $parcels->filter(fn (Shipment $shipment): bool => $shipment->pivot->load_status === 'pending');
        $status = $this->tripStatus($manifest, $pending->isEmpty());
        $truck = $manifest->vehicle_id
            ? SortingCenterVehicle::query()->find($manifest->vehicle_id)
            : null;
        $payload = [
            'id' => $manifest->id,
            'trip_reference' => $manifest->batch_key,
            'manifest_number' => $manifest->batch_key,
            'trip_type' => $manifest->returning_manifest_id ? 'RETURN' : 'OUTBOUND',
            'origin' => $origin,
            'destination' => $destination,
            'assigned_truck' => [
                'id' => $manifest->vehicle_id,
                'name' => $truck?->name,
                'type' => $truck?->type,
                'plate_number' => $truck?->plate_number ?: $manifest->vehicle_reference,
                'reference' => $manifest->vehicle_reference,
            ],
            'courier_name' => $manifest->courier_name,
            'expected_parcels' => $parcels->count(),
            'loaded_parcels' => $loaded->count(),
            'pending_parcels' => $pending->count(),
            'missing_parcels' => $parcels->filter(fn (Shipment $shipment): bool => $shipment->pivot->load_status === 'missing')->count(),
            'rejected_parcels' => $parcels->filter(fn (Shipment $shipment): bool => $shipment->pivot->load_status === 'rejected')->count(),
            'received_parcels' => $parcels->filter(fn (Shipment $shipment): bool => $shipment->pivot->received_at !== null)->count(),
            'status' => $status,
            'scheduled_at' => $manifest->scheduled_at?->toISOString(),
            'loaded_at' => $manifest->loaded_at?->toISOString(),
            'departed_at' => $manifest->departed_at?->toISOString(),
            'arrival_confirmed_at' => $manifest->arrival_confirmed_at?->toISOString(),
            'arrived_at' => $manifest->arrived_at?->toISOString(),
            'returning_manifest_id' => $manifest->returning_manifest_id,
            'can_confirm_departure' => ! $manifest->departed_at && ! $pending->count() && $loaded->isNotEmpty(),
            'can_confirm_arrival' => (bool) $manifest->departed_at && ! $manifest->arrival_confirmed_at,
            'route' => [$origin, $destination],
        ];

        if ($includeParcels) {
            $payload['parcels'] = $parcels->map(
                fn (Shipment $shipment): array => $this->shipmentPayload($shipment),
            )->values();
            $payload['events'] = $manifest->events->map(fn (LogisticsTransferManifestEvent $event): array => [
                'type' => $event->event_type,
                'notes' => $event->notes,
                'shipment_id' => $event->shipment_id,
                'occurred_at' => $event->occurred_at?->toISOString(),
            ])->values();
        }

        return $payload;
    }

    private function shipmentPayload(Shipment $shipment): array
    {
        return [
            'id' => $shipment->id,
            'tracking_number' => $shipment->tracking_number,
            'order_number' => $shipment->order?->order_number,
            'load_status' => $shipment->pivot?->load_status ?? 'pending',
            'loaded_at' => $shipment->pivot?->loaded_at,
            'received_at' => $shipment->pivot?->received_at,
            'discrepancy_note' => $shipment->pivot?->discrepancy_note,
            'current_location' => $shipment->current_location,
        ];
    }

    private function tripStatus(LogisticsTransferManifest $manifest, bool $loadsResolved): string
    {
        if ($manifest->status === 'arrived' && $manifest->arrived_at) {
            return 'completed';
        }
        if ($manifest->arrival_confirmed_at) {
            return 'awaiting_receiving';
        }
        if ($manifest->departed_at) {
            return 'in_transit';
        }

        return $loadsResolved ? 'ready_to_depart' : 'loading';
    }

    private function facility(string $type, int $id): array
    {
        if ($type === 'delivery_hub') {
            $hub = LogisticsDeliveryHub::query()->find($id);
            return [
                'id' => $id,
                'type' => $type,
                'name' => $hub?->hub_name ?? 'Delivery Hub',
                'address' => $this->facilityAddress(null, $hub?->municipality, $hub?->province),
                'province' => (string) $hub?->province,
            ];
        }

        $center = LogisticsBranch::query()->find($id);
        return [
            'id' => $id,
            'type' => $type,
            'name' => $center?->name ?? 'Sorting Center',
            'address' => $this->facilityAddress($center?->address, $center?->municipality, $center?->province),
            'province' => (string) $center?->province,
        ];
    }

    private function facilityAddress(?string $address, ?string $municipality, ?string $province): string
    {
        return collect([$address, $municipality, $province])
            ->map(fn (?string $value): string => trim((string) $value))
            ->filter()
            ->unique()
            ->implode(', ');
    }

    private function eligibleReturnShipments(CourierRegistration $courier, LogisticsTransferManifest $trip): Builder
    {
        $origin = $courier->sortingCenter;
        $destination = LogisticsBranch::query()->find($trip->destination_id);
        $routeCenterId = $destination
            ? LogisticsBranch::query()
                ->where('logistics_id', $destination->logistics_id)
                ->where('status', 'active')
                ->whereNull('archived_at')
                ->whereRaw('LOWER(province) = ?', [mb_strtolower(trim((string) $origin->province))])
                ->orderBy('id')
                ->value('id')
            : null;

        if (! $destination
            || (int) $destination->logistics_id !== (int) $origin->logistics_id
            || (int) $routeCenterId !== (int) $origin->id) {
            return Shipment::query()->whereRaw('1 = 0');
        }

        return Shipment::query()
            ->with('order')
            ->where('logistics_branch_id', $trip->destination_id)
            ->whereNull('logistics_delivery_hub_id')
            ->whereHas('order', fn (Builder $order) => $order
                ->where('status', 'in_transit')
                ->whereHas('buyer.buyerProfile', fn (Builder $buyer) => $buyer
                    ->whereRaw('LOWER(province) = ?', [mb_strtolower(trim((string) $origin->province))])))
            ->whereHas('transferManifests', fn (Builder $manifest) => $manifest
                ->where('destination_type', 'sorting_center')
                ->where('destination_id', $trip->destination_id)
                ->whereNotNull('logistics_transfer_manifest_shipments.received_at'))
            ->whereDoesntHave('transferManifests', fn (Builder $manifest) => $manifest
                ->whereIn('status', ['preparing', 'loaded', 'arrived'])
                ->where(fn (Builder $active) => $active
                    ->whereIn('status', ['preparing', 'loaded'])
                    ->orWhereNull('arrived_at')));
    }

    private function recordEvent(
        LogisticsTransferManifest $manifest,
        User $actor,
        string $type,
        ?Shipment $shipment = null,
        ?string $notes = null,
        ?array $metadata = null,
    ): void {
        LogisticsTransferManifestEvent::query()->create([
            'transfer_manifest_id' => $manifest->id,
            'shipment_id' => $shipment?->id,
            'actor_user_id' => $actor->id,
            'event_type' => $type,
            'notes' => $notes,
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }

    private function notifyReceivingFacility(LogisticsTransferManifest $manifest): void
    {
        $user = $manifest->destination_type === 'delivery_hub'
            ? LogisticsDeliveryHub::query()->whereKey($manifest->destination_id)->value('user_id')
            : LogisticsBranch::query()->whereKey($manifest->destination_id)->value('user_id');
        $user = $user ? User::query()->find($user) : null;
        $user?->notify(new CourierArrivalNotification($manifest));
    }
}
