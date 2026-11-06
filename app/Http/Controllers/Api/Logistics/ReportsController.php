<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\Shipment;
use App\Models\Logistics\Logistics;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['sometimes', 'required', 'date_format:Y-m-d'],
            'to' => ['sometimes', 'required', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);

        $today = CarbonImmutable::now('Asia/Manila')->toDateString();
        $from = $validated['from'] ?? CarbonImmutable::now('Asia/Manila')->startOfMonth()->toDateString();
        $to = $validated['to'] ?? $today;
        abort_if($from > $today || $to > $today, 422, 'Report dates cannot be in the future.');

        $logistics = Logistics::query()
            ->where('user_id', $request->user()->id)
            ->where('registration_status', 'active')
            ->firstOrFail();

        $branches = $logistics->branches()
            ->whereNull('archived_at')
            ->with(['deliveryHubs' => fn ($query) => $query->whereNull('archived_at')->orderBy('hub_name')])
            ->orderBy('name')
            ->get();
        $branchIds = $branches->modelKeys();
        $centers = $branches;
        $hubIds = $branches->flatMap(fn ($branch) => $branch->deliveryHubs->pluck('id'))->all();

        $shipments = Shipment::query()
            ->with(['order:id,logistics_id,order_number,status', 'logisticsBranch:id,name', 'logisticsBranch.deliveryHubs', 'deliveryHub:id,hub_name', 'rider.logisticsBranch.deliveryHubs', 'scans'])
            ->where(function ($scope) use ($logistics, $branchIds, $hubIds): void {
                $hasFacilityScope = false;
                if ($branchIds !== []) {
                    $scope->whereIn('logistics_branch_id', $branchIds);
                    $hasFacilityScope = true;
                }
                if ($hubIds !== []) {
                    $method = $hasFacilityScope ? 'orWhereIn' : 'whereIn';
                    $scope->{$method}('logistics_delivery_hub_id', $hubIds);
                    $hasFacilityScope = true;
                }

                $method = $hasFacilityScope ? 'orWhereHas' : 'whereHas';
                $scope->{$method}('rider.logisticsBranch', fn ($branch) => $branch
                    ->where('logistics_id', $logistics->id)
                    ->whereNull('archived_at'));
            })
            ->where(function ($query) use ($from, $to): void {
                $query->whereDate('picked_up_at', '>=', $from)->whereDate('picked_up_at', '<=', $to)
                    ->orWhere(function ($fallback) use ($from, $to): void {
                        $fallback->whereNull('picked_up_at')
                            ->whereDate('shipments.created_at', '>=', $from)
                            ->whereDate('shipments.created_at', '<=', $to);
                    });
            })
            ->get();

        $parcels = $shipments->map(function (Shipment $shipment): array {
            $order = $shipment->order;
            $branch = $shipment->logisticsBranch ?? $shipment->rider?->logisticsBranch;
            $hub = $shipment->deliveryHub;
            if (! $hub && $branch && $shipment->rider?->municipal_hub) {
                $hub = $branch->deliveryHubs->first(fn ($candidate) => mb_strtolower($candidate->hub_name) === mb_strtolower($shipment->rider->municipal_hub));
            }
            $status = strtolower((string) $order?->status);
            $latestScan = $shipment->scans->sortByDesc('scanned_at')->first();
            $latestScanStatus = strtolower((string) $latestScan?->status);
            $deliveredAt = $shipment->delivered_at;
            $delivered = in_array($status, ['delivered', 'completed'], true) || $deliveredAt !== null;
            $exception = in_array($status, ['exception', 'lost', 'damaged'], true)
                || in_array($latestScanStatus, ['exception', 'lost', 'damaged'], true);
            $stage = $exception ? 'exception' : ($delivered ? 'delivered' : ($shipment->logistics_delivery_hub_id ? 'at_hub' : 'in_transit'));
            $duration = $shipment->picked_up_at && $deliveredAt
                ? round($shipment->picked_up_at->diffInMinutes($deliveredAt) / 60, 1)
                : null;

            return [
                'date' => ($shipment->picked_up_at ?? $shipment->created_at)->timezone('Asia/Manila')->toDateString(),
                'ref' => $shipment->tracking_number ?: (string) $order?->order_number,
                'center' => $branch?->name ?? 'Unassigned branch',
                'hub' => $hub?->hub_name ?? 'Unassigned hub',
                'status' => $stage,
                'issue' => in_array($latestScanStatus, ['lost', 'damaged'], true) ? $latestScanStatus : (in_array($status, ['lost', 'damaged'], true) ? $status : null),
                'hours' => $duration,
                'on_time' => $duration !== null && $duration <= 48,
                'fee' => (float) ($shipment->shipping_fee ?? 0),
            ];
        })->values();

        return response()->json([
            'data' => [
                'from' => $from,
                'to' => $to,
                'today' => $today,
                'parcels' => $parcels,
                'centers' => $centers->map(fn ($center): array => [
                    'name' => $center->name,
                    'hubs' => $center->deliveryHubs->pluck('hub_name')->values(),
                ])->values(),
            ],
        ]);
    }
}
