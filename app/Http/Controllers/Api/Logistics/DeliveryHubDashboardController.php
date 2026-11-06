<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\Announcement;
use App\Models\Admin\Shipment;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\LogisticsTransferManifest;
use App\Models\Rider\Rider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DeliveryHubDashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $hub = LogisticsDeliveryHub::query()->where('user_id', $request->user()->id)->whereNull('archived_at')->firstOrFail();
        if ($request->filled('tracking')) {
            $tracking = $request->validate(['tracking' => ['required', 'string', 'max:80']])['tracking'];
            $shipment = Shipment::query()->where('tracking_number', $tracking)
                ->where(fn ($query) => $query->where('logistics_delivery_hub_id', $hub->id)
                    ->orWhereHas('rider', fn ($rider) => $rider->where('logistics_branch_id', $hub->logistics_branch_id)->whereRaw('LOWER(municipal_hub) = ?', [mb_strtolower($hub->hub_name)])))
                ->with(['order:id,status', 'scans'])->first();
            abort_unless($shipment, 404, 'No parcel with that tracking number is assigned to this delivery hub.');
            return response()->json(['data' => ['tracking' => $shipment->tracking_number, 'status' => $shipment->scans->first()?->status ?: $shipment->order?->status ?: 'In transit', 'location' => $shipment->current_location, 'updated_at' => $shipment->location_updated_at?->timezone('Asia/Manila')->format('M d, Y g:i A')]]);
        }

        $dates = $request->validate(['from' => ['required', 'date_format:Y-m-d'], 'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from']]);
        $today = Carbon::now('Asia/Manila')->toDateString();
        abort_if($dates['to'] > $today, 422, 'Dashboard dates cannot be in the future.');
        $inbound = LogisticsTransferManifest::query()->where('destination_type', 'delivery_hub')->where('destination_id', $hub->id)
            ->with(['shipments.order', 'shipments.scans'])->latest('created_at')->get();
        $outbound = LogisticsTransferManifest::query()->where('origin_type', 'delivery_hub')->where('origin_id', $hub->id)
            ->with(['shipments.order', 'shipments.scans'])->latest('created_at')->get();

        $incomingBatches = $inbound->map(function ($manifest) use ($hub): array {
            $total = $manifest->shipments->count();
            $received = $manifest->shipments->filter(fn (Shipment $shipment) => $shipment->pivot?->received_at !== null)->count();
            $origin = $manifest->origin_type === 'sorting_center'
                ? LogisticsBranch::query()->whereKey($manifest->origin_id)->value('name')
                : ($manifest->origin_type === 'delivery_hub' ? LogisticsDeliveryHub::query()->whereKey($manifest->origin_id)->value('hub_name') : 'Seller collection');
            return ['id' => $manifest->batch_key, 'origin' => $origin ?: 'Transfer origin', 'total' => $total, 'received' => $received,
                'pending' => max(0, $total - $received), 'arrival_at' => ($manifest->arrived_at ?? $manifest->created_at)?->toIso8601String(),
                'courier' => $manifest->courier_name, 'truck' => $manifest->vehicle_reference, 'status' => $manifest->status];
        })->filter(fn ($batch) => $batch['pending'] > 0 && $this->inPeriod($batch['arrival_at'], $dates))->values();

        $receivedBatches = $inbound->map(function ($manifest) use ($hub): array {
            $receivedShipments = $manifest->shipments->filter(fn (Shipment $shipment) => $shipment->pivot?->received_at !== null);
            $onSite = $receivedShipments->filter(fn (Shipment $shipment) => $shipment->logistics_delivery_hub_id === $hub->id
                && ! in_array($shipment->order?->status, ['out_for_delivery', 'delivered', 'completed', 'cancelled'], true))->count();
            $lastReceived = $receivedShipments->max(fn (Shipment $shipment) => $shipment->pivot?->received_at);
            return ['id' => $manifest->batch_key, 'origin' => $manifest->origin_type === 'sorting_center' ? (LogisticsBranch::query()->whereKey($manifest->origin_id)->value('name') ?: 'Sorting Center') : 'Transfer origin',
                'total' => $manifest->shipments->count(), 'received' => $receivedShipments->count(), 'on_site_count' => $onSite,
                'received_at' => $lastReceived ? Carbon::parse($lastReceived)->toIso8601String() : null];
        })->filter(fn ($batch) => $batch['received'] > 0 && $this->inPeriod($batch['received_at'], $dates))->values();

        $departedBatches = $outbound->filter(fn ($manifest) => $manifest->departed_at && $this->inPeriod($manifest->departed_at, $dates))
            ->map(function ($manifest) {
                $destination = $manifest->destination_type === 'sorting_center'
                    ? LogisticsBranch::query()->whereKey($manifest->destination_id)->value('name')
                    : LogisticsDeliveryHub::query()->whereKey($manifest->destination_id)->value('hub_name');
                return ['id' => $manifest->batch_key, 'destination' => $destination ?: 'Transfer destination', 'total' => $manifest->shipments->count(),
                    'departed_at' => $manifest->departed_at->toIso8601String(), 'courier' => $manifest->courier_name, 'truck' => $manifest->vehicle_reference];
            })->values();

        $shipments = Shipment::query()->where('logistics_delivery_hub_id', $hub->id)->with(['order', 'rider.user', 'scans'])->get();
        $assignments = $shipments->filter(fn (Shipment $shipment) => $shipment->order?->status === 'out_for_delivery')
            ->map(function (Shipment $shipment) {
                $scan = $shipment->scans->first(fn ($item) => str_contains(mb_strtolower($item->status), 'out for delivery')) ?: $shipment->scans->first();
                $rider = $shipment->rider;
                return ['id' => $shipment->tracking_number ?: $shipment->order?->order_number, 'name' => trim(($rider?->first_name ?? '').' '.($rider?->last_name ?? '')) ?: $rider?->user?->name ?: 'Unassigned rider',
                    'role' => 'Rider', 'vehicle' => trim(($rider?->vehicle ?? '').' '.($rider?->plate_number ?? '')) ?: 'Vehicle not recorded', 'count' => 1,
                    'left_at' => ($scan?->scanned_at ?? $shipment->picked_up_at ?? $shipment->updated_at)?->toIso8601String()];
            })->filter(fn ($row) => $this->inPeriod($row['left_at'], $dates))->values();

        $delivered = $shipments->filter(fn (Shipment $shipment) => ($shipment->delivered_at || in_array($shipment->order?->status, ['delivered', 'completed'], true))
                && $this->inPeriod($shipment->delivered_at ?? $shipment->order?->updated_at, $dates))
            ->map(fn (Shipment $shipment) => ['id' => $shipment->tracking_number ?: $shipment->order?->order_number, 'count' => 1,
                'delivered_at' => ($shipment->delivered_at ?? $shipment->order?->updated_at)?->toIso8601String(), 'staff' => $shipment->rider?->user?->name]);
        $disputes = $shipments->filter(function (Shipment $shipment) use ($dates): bool {
            $scan = $shipment->scans->first();
            $status = mb_strtolower((string) $scan?->status);
            return (str_contains($status, 'lost') || str_contains($status, 'damaged') || str_contains($status, 'exception'))
                && $this->inPeriod($scan?->scanned_at, $dates);
        })->count();

        $riders = Rider::query()->where('logistics_branch_id', $hub->logistics_branch_id)->where('registration_status', 'active')
            ->whereRaw('LOWER(municipal_hub) = ?', [mb_strtolower($hub->hub_name)])->get();
        $distributions = DB::table('cod_settlements as settlements')
            ->join('rider_remittances as remittances', 'remittances.id', '=', 'settlements.remittance_id')
            ->join('hub_cod_cash_ins as cash_ins', 'cash_ins.remittance_id', '=', 'remittances.id')
            ->where('settlements.delivery_hub_id', $hub->id)->whereNotNull('cash_ins.received_at')->whereNotNull('settlements.distributed_at')
            ->whereDate('settlements.distributed_at', '>=', $dates['from'])->whereDate('settlements.distributed_at', '<=', $dates['to'])
            ->get(['settlements.cod_amount', 'settlements.distributed_at', 'cash_ins.received_at']);
        $announcements = Announcement::publishedForAudience(['All Users', 'Logistics'])->latest('published_at')->take(8)
            ->get()->map(fn ($item) => ['badge_label' => 'ANNOUNCEMENT', 'title' => $item->title, 'body' => $item->body])->values();

        return response()->json(['data' => [
            'today' => $today, 'hub' => ['id' => $hub->id, 'name' => $hub->hub_name],
            'stats' => ['parcel_disputes' => $disputes, 'vehicles' => $riders->pluck('vehicle')->filter()->unique()->count(), 'drivers' => $riders->count(),
                'verified_distributions' => $distributions->map(fn ($row) => ['status' => 'completed', 'verified_at' => $row->received_at, 'distributed_at' => $row->distributed_at, 'total_distributed' => (float) $row->cod_amount])->values()],
            'incoming' => $incomingBatches, 'received' => $receivedBatches, 'departed' => $departedBatches,
            'assignments' => $assignments, 'vehicles' => $riders->pluck('vehicle')->filter()->unique()->values(), 'drivers' => $riders->pluck('id')->values(),
            'delivered' => $delivered->values(), 'announcements' => $announcements,
        ]]);
    }

    private function inPeriod($value, array $dates): bool
    {
        if (! $value) return false;
        $date = Carbon::parse($value)->timezone('Asia/Manila')->toDateString();
        return $date >= $dates['from'] && $date <= $dates['to'];
    }
}
