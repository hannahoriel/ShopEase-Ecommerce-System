<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\DeliveryHubScannerState;
use App\Models\Logistics\LogisticsDeliveryHub;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryHubParcelDisputesController extends Controller
{
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
