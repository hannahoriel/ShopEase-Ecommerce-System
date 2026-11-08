<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\Shipment;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\LogisticsTransferManifest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SortingCenterReportsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        [$from, $to] = $this->period($request);
        $center = LogisticsBranch::query()->where('user_id', $request->user()->id)->whereNull('archived_at')->firstOrFail();
        $hubs = $center->deliveryHubs()->whereNull('archived_at')->orderBy('hub_name')->pluck('hub_name')->values();

        $outbound = LogisticsTransferManifest::query()
            ->where('origin_type', 'sorting_center')->where('origin_id', $center->id)
            ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->with(['shipments.order', 'shipments.rider.user', 'shipments.scans'])
            ->get();
        $rows = collect();
        foreach ($outbound as $manifest) {
            $destinationType = $manifest->destination_type === 'delivery_hub' ? 'Delivery Hub' : 'Sorting Center';
            $destination = $manifest->destination_type === 'delivery_hub'
                ? $center->deliveryHubs()->whereKey($manifest->destination_id)->value('hub_name')
                : LogisticsBranch::query()->whereKey($manifest->destination_id)->value('name');
            $destination ??= 'Unknown destination';
            foreach ($manifest->shipments as $shipment) {
                $scan = $shipment->scans->sortByDesc('scanned_at')->first();
                $issue = $this->issue($shipment, $scan);
                $rows->push($this->row($shipment, $manifest->created_at, $manifest->batch_key, $destination, $destinationType, $issue ? 'dispute' : ($destinationType === 'Delivery Hub' ? 'hub_dispatch' : 'center_transfer'), $issue, $manifest->courier_name ?: $shipment->rider?->first_name.' '.$shipment->rider?->last_name));
            }
        }

        $inbound = LogisticsTransferManifest::query()
            ->where('destination_type', 'sorting_center')->where('destination_id', $center->id)
            ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->with(['shipments.order', 'shipments.scans'])
            ->get();
        foreach ($inbound as $manifest) {
            foreach ($manifest->shipments as $shipment) {
                $scan = $shipment->scans->sortByDesc('scanned_at')->first();
                $issue = $this->issue($shipment, $scan);
                $received = $shipment->pivot?->received_at !== null;
                $rows->push($this->row($shipment, $shipment->pivot?->received_at ?? $manifest->created_at, $manifest->batch_key, $manifest->origin_type === 'sorting_center' ? (LogisticsBranch::query()->whereKey($manifest->origin_id)->value('name') ?: 'Sorting Center') : 'Seller Collection', 'Sorting Center', $issue ? 'dispute' : ($received ? 'received' : 'incoming'), $issue, null));
            }
        }

        // Directly assigned shipments cover older records that predate transfer manifests.
        $manifestShipmentIds = $rows->pluck('shipment_id')->all();
        $direct = Shipment::query()->where('logistics_branch_id', $center->id)
            ->whereNotIn('id', $manifestShipmentIds ?: [0])
            ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->with(['order', 'rider.user', 'scans'])
            ->get();
        foreach ($direct as $shipment) {
            $scan = $shipment->scans->sortByDesc('scanned_at')->first();
            $status = $this->issue($shipment, $scan) ? 'dispute' : (str_contains(strtolower((string) $scan?->status), 'received') ? 'received' : 'incoming');
            $issue = $this->issue($shipment, $scan);
            $rows->push($this->row($shipment, $scan?->scanned_at ?? $shipment->created_at, null, $shipment->current_location ?: 'Sorting Center', 'Sorting Center', $status, $issue, null));
        }

        return response()->json(['data' => [
            'center' => $center->name,
            'today' => Carbon::now('Asia/Manila')->toDateString(),
            'hubs' => $hubs,
            'couriers' => CourierRegistration::query()->where('sorting_center_id', $center->id)->where('status', 'approved')->orderBy('first_name')->get()->map(fn ($courier) => trim($courier->first_name.' '.$courier->last_name))->filter()->unique()->values(),
            'records' => $rows->unique(fn ($row) => $row['shipment_id'].'|'.$row['status'].'|'.$row['batch'])->map(function ($row) { unset($row['shipment_id']); return $row; })->values(),
        ]]);
    }

    private function period(Request $request): array
    {
        $date = $request->validate(['from' => ['required', 'date_format:Y-m-d'], 'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from']]);
        abort_if($date['to'] > Carbon::now('Asia/Manila')->toDateString(), 422, 'Report dates cannot be in the future.');
        return [$date['from'], $date['to']];
    }

    private function issue(Shipment $shipment, $scan): ?string
    {
        $status = strtolower((string) ($scan?->status ?? $shipment->order?->status));
        return str_contains($status, 'lost') ? 'Lost' : (str_contains($status, 'damaged') ? 'Damaged' : (str_contains($status, 'dispute') || str_contains($status, 'exception') ? 'Lost' : null));
    }

    private function row(Shipment $shipment, $at, ?string $batch, string $destination, string $destinationType, string $status, ?string $issue, ?string $courier): array
    {
        return ['shipment_id' => $shipment->id, 'ref' => $shipment->tracking_number ?: ($shipment->order?->order_number ?: 'ORDER-'.$shipment->order_id), 'date' => Carbon::parse($at)->timezone('Asia/Manila')->toDateString(), 'time' => Carbon::parse($at)->timezone('Asia/Manila')->format('H:i'), 'batch' => $batch ?: 'Unbatched', 'destination' => $destination, 'destination_type' => $destinationType, 'status' => $status, 'issue' => $issue, 'courier' => trim((string) $courier) ?: null];
    }
}
