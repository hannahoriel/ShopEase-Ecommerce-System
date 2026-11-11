<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\DeliveryHubScannerState;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\LogisticsTransferManifest;
use App\Models\Logistics\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DeliveryHubParcelDisputesController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tracking' => ['required', 'string', 'max:90'],
            'type' => ['required', 'string', Rule::in([
                'Lost Parcel', 'Damaged Parcel',
                'Pickup Unsuccessful — Sender Not Available',
                'Pickup Unsuccessful — Parcel Not Ready',
                'Pickup Unsuccessful — Incorrect Address',
                'Pickup Unsuccessful — Customer Cancelled',
            ])],
            'description' => ['required', 'string', 'min:1', 'max:1500'],
            'evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $hub = LogisticsDeliveryHub::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('archived_at')
            ->firstOrFail();
        $trackingKey = mb_strtolower(trim($data['tracking']));
        $stateRow = DeliveryHubScannerState::query()
            ->where('logistics_delivery_hub_id', $hub->id)
            ->first();
        $state = $stateRow?->state ?: ['parcels' => [], 'manifests' => [], 'events' => [], 'issues' => []];
        $parcelIndex = collect($state['parcels'] ?? [])->search(fn ($parcel) =>
            is_array($parcel) && mb_strtolower(trim((string) ($parcel['tracking'] ?? ''))) === $trackingKey
        );
        if ($parcelIndex === false) {
            $shipment = Shipment::query()
                ->with('order')
                ->where('tracking_number', trim($data['tracking']))
                ->where('logistics_delivery_hub_id', $hub->id)
                ->first();
            abort_if(! $shipment, 404, 'This parcel is not assigned to the current Delivery Hub.');
            $isSellerPickup = LogisticsTransferManifest::query()
                ->where('destination_type', 'delivery_hub')
                ->where('destination_id', $hub->id)
                ->where('origin_type', 'seller_collection')
                ->whereHas('shipments', fn ($query) => $query->whereKey($shipment->id))
                ->exists();
            $state['parcels'] = array_values($state['parcels'] ?? []);
            $parcelIndex = count($state['parcels']);
            $state['parcels'][] = [
                'id' => 'shipment-'.$shipment->id,
                'shipment_id' => $shipment->id,
                'order_id' => $shipment->order_id,
                'tracking' => $shipment->tracking_number,
                'kind' => $isSellerPickup ? 'pickup' : 'sorting',
                'status' => 'incoming',
            ];
        }

        $parcel = $state['parcels'][$parcelIndex];
        $pickupFailure = str_starts_with($data['type'], 'Pickup Unsuccessful');
        if ($pickupFailure) {
            abort_unless(in_array(mb_strtolower((string) ($parcel['kind'] ?? '')), ['pickup', 'return'], true), 422, 'Pickup failure reports are only available for seller pickups and buyer returns.');
            abort_unless(($parcel['status'] ?? null) === 'incoming', 422, 'Only an incoming pickup can be marked unsuccessful.');
        } else {
            abort_if(($parcel['reportedIssue'] ?? false) === true, 409, 'An issue has already been reported for this parcel.');
        }

        $evidencePath = $request->file('evidence')?->store('delivery-hub/issues');
        $saved = DB::transaction(function () use ($request, $hub, $state, $parcelIndex, $data, $pickupFailure, $evidencePath): array {
            $row = DeliveryHubScannerState::query()
                ->where('logistics_delivery_hub_id', $hub->id)
                ->lockForUpdate()
                ->first();
            $current = $row?->state ?: $state;
            $current['parcels'] = array_values($current['parcels'] ?? []);
            $currentParcelIndex = collect($current['parcels'])->search(fn ($candidate) =>
                is_array($candidate) && mb_strtolower(trim((string) ($candidate['tracking'] ?? ''))) === mb_strtolower(trim($data['tracking']))
            );
            if ($currentParcelIndex === false) {
                $currentParcelIndex = count($current['parcels']);
                $current['parcels'][] = $state['parcels'][$parcelIndex];
            }
            $currentParcel = $current['parcels'][$currentParcelIndex] ?? null;
            abort_unless(is_array($currentParcel) && mb_strtolower(trim((string) ($currentParcel['tracking'] ?? ''))) === mb_strtolower(trim($data['tracking'])), 409, 'The parcel state changed. Refresh and try again.');
            if ($pickupFailure) {
                abort_unless(($currentParcel['status'] ?? null) === 'incoming', 409, 'This pickup has already been processed.');
            } else {
                abort_if(($currentParcel['reportedIssue'] ?? false) === true, 409, 'An issue has already been reported for this parcel.');
            }

            $at = now()->toIso8601String();
            $current['issues'] = array_values($current['issues'] ?? []);
            $current['events'] = array_values($current['events'] ?? []);
            if ($pickupFailure) {
                $currentParcel['status'] = 'voided';
                $currentParcel['voidedAt'] = $at;
                $currentParcel['rescheduleRequired'] = true;
                $currentParcel['pickupFailureReason'] = $data['type'];
                $currentParcel['pickupFailureDescription'] = trim($data['description']);
            } else {
                $id = 'DH-ISS-'.Str::upper(Str::random(12));
                $entry = [
                    'id' => $id,
                    'tracking' => trim($data['tracking']),
                    'type' => $data['type'],
                    'description' => trim($data['description']),
                    'evidenceName' => $request->file('evidence')?->getClientOriginalName() ?: '',
                    'evidencePath' => $evidencePath,
                    'at' => $at,
                    'hubId' => $hub->id,
                    'status' => 'Open',
                ];
                $current['issues'] = array_values(array_filter($current['issues'], fn ($report) =>
                    ! is_array($report) || mb_strtolower(trim((string) ($report['tracking'] ?? ''))) !== mb_strtolower(trim($data['tracking']))
                ));
                array_unshift($current['issues'], $entry);
                $currentParcel['reportedIssue'] = true;
                $currentParcel['reportedAt'] = $at;
                $currentParcel['reportId'] = $id;
                $currentParcel['status'] = 'reported';
                $currentParcel['nextStop'] = null;
            }

            $currentParcel['history'] = array_values($currentParcel['history'] ?? []);
            $currentParcel['history'][] = [
                'status' => $pickupFailure ? 'Pickup Voided — Reschedule Required' : 'Issue reported',
                'date' => $at,
                'location' => $hub->hub_name,
                'details' => $data['type'].' — '.trim($data['description']),
            ];
            $current['parcels'][$currentParcelIndex] = $currentParcel;
            array_unshift($current['events'], [
                'at' => $at,
                'action' => $pickupFailure ? 'Pickup voided' : 'Issue reported',
                'tracking' => trim($data['tracking']),
                'desc' => $data['type'],
            ]);
            $current['events'] = array_slice($current['events'], 0, 100);
            DeliveryHubScannerState::query()->updateOrCreate(
                ['logistics_delivery_hub_id' => $hub->id],
                ['updated_by' => $request->user()->id, 'state' => $current],
            );

            return ['state' => $current, 'report' => $pickupFailure ? null : $entry ?? null, 'parcel' => $currentParcel];
        });

        return response()->json([
            'message' => $pickupFailure ? 'Pickup failure recorded; reschedule is required.' : 'Parcel issue report saved.',
            'data' => $saved,
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $hub = LogisticsDeliveryHub::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('archived_at')
            ->firstOrFail();

        $state = DeliveryHubScannerState::query()
            ->where('logistics_delivery_hub_id', $hub->id)
            ->first()?->state ?: [];
        $parcels = collect(data_get($state, 'parcels', []))
            ->filter(fn ($parcel) => is_array($parcel) && ! empty($parcel['tracking']))
            ->keyBy(fn ($parcel) => mb_strtolower(trim((string) $parcel['tracking'])));

        $reports = collect(data_get($state, 'issues', []))
            ->filter(fn ($report) => is_array($report) && ! empty($report['tracking']))
            ->map(function (array $report) use ($parcels, $hub): array {
                $tracking = trim((string) $report['tracking']);
                unset($report['evidencePath']);
                return [
                    ...$report,
                    'id' => $report['id'] ?? 'DH-ISSUE-'.$tracking,
                    'tracking' => $tracking,
                    'hubId' => $hub->id,
                    'parcel' => $parcels->get(mb_strtolower($tracking)),
                ];
            });

        // A failed seller or buyer-return collection is also a dispute record,
        // persisted in the scanner state as a voided parcel requiring reschedule.
        $failedPickups = $parcels->filter(fn ($parcel) =>
            ($parcel['status'] ?? null) === 'voided'
            && in_array(mb_strtolower((string) ($parcel['kind'] ?? '')), ['pickup', 'return'], true)
            && ($parcel['rescheduleRequired'] ?? true) !== false
        )->map(function (array $parcel) use ($hub): array {
            $kind = mb_strtolower((string) ($parcel['kind'] ?? 'pickup'));
            $reason = $parcel['pickupFailureReason'] ?? 'Pickup unsuccessful; a new collection date is required.';
            return [
                'id' => 'DH-FAILED-'.($parcel['tracking'] ?? $parcel['id'] ?? uniqid()),
                'tracking' => $parcel['tracking'],
                'type' => $kind === 'return' ? 'Failed Buyer Return Pickup' : 'Failed Seller Pickup',
                'description' => $parcel['pickupFailureDescription'] ?? $reason,
                'failureReason' => $reason,
                'evidenceName' => '',
                'at' => $parcel['voidedAt'] ?? $parcel['updatedAt'] ?? null,
                'hubId' => $hub->id,
                'status' => 'Awaiting Reschedule',
                'isFailedPickup' => true,
                'pickupKind' => $kind,
                'barangay' => $parcel['barangay'] ?? null,
                'scheduledDate' => $parcel['scheduledDate'] ?? $parcel['pickupDate'] ?? null,
                'assignedRider' => $parcel['courierName'] ?? null,
                'rescheduleRequired' => true,
                'parcel' => $parcel,
            ];
        });

        $reports = $reports->concat($failedPickups)
            ->unique(fn ($report) => (string) $report['id'])
            ->sortByDesc(fn ($report) => $report['at'] ?? '')
            ->values();

        return response()->json([
            'data' => [
                'hub' => ['id' => $hub->id, 'name' => $hub->hub_name],
                'reports' => $reports,
                'parcels' => $parcels->values(),
            ],
        ]);
    }
}
