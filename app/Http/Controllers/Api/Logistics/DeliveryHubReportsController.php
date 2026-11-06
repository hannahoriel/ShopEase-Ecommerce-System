<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\Shipment;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\Rider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DeliveryHubReportsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $dates = $request->validate(['from' => ['required', 'date_format:Y-m-d'], 'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from']]);
        $today = Carbon::now('Asia/Manila')->toDateString();
        abort_if($dates['to'] > $today, 422, 'Report dates cannot be in the future.');
        $hub = LogisticsDeliveryHub::query()->where('user_id', $request->user()->id)->whereNull('archived_at')->firstOrFail();
        $shipments = Shipment::query()->where('logistics_delivery_hub_id', $hub->id)
            ->with(['order', 'rider.user', 'scans'])
            ->get();
        $orderIds = $shipments->pluck('order_id')->all();
        $collections = DB::table('rider_cod_collections')->whereIn('order_id', $orderIds)->get()->keyBy('order_id');
        $remitIds = $collections->pluck('id')->all();
        $cashIns = DB::table('rider_cod_remittance_items as items')
            ->join('rider_remittances as remittances', 'remittances.id', '=', 'items.remittance_id')
            ->whereIn('items.cod_collection_id', $remitIds ?: [0])->whereNotNull('remittances.cash_in_at')
            ->pluck('items.cod_collection_id')->flip();
        $settlements = DB::table('cod_settlements')->whereIn('order_id', $orderIds)->where('delivery_hub_id', $hub->id)->get()->keyBy('order_id');
        $assignments = Rider::query()->with('user:id,name')
            ->where('registration_status', 'active')
            ->where('logistics_branch_id', $hub->logistics_branch_id)
            ->where(function ($query) use ($hub): void {
                $query->whereRaw('LOWER(municipal_hub) = ?', [mb_strtolower($hub->hub_name)])
                    ->orWhereRaw('LOWER(municipal_hub) = ?', [mb_strtolower($hub->municipality)]);
            })
            ->get()
            ->flatMap(function (Rider $rider): array {
                $operation = mb_strtolower((string) $rider->logistics_operation);
                $name = trim($rider->first_name.' '.$rider->last_name) ?: $rider->user?->name ?: 'Assigned rider';
                $assignments = [];
                foreach (['pickup', 'delivery'] as $role) {
                    if (str_contains($operation, $role)) {
                        $assignments[] = ['name' => $name, 'barangay' => $rider->barangay ?: $rider->assignment_barangay ?: 'Unspecified', 'role' => $role];
                    }
                }
                return $assignments;
            })->values();

        $rows = $shipments->map(function (Shipment $shipment) use ($collections, $cashIns, $settlements) {
            $order = $shipment->order;
            $scan = $shipment->scans->sortByDesc('scanned_at')->first();
            $scanStatus = strtolower((string) $scan?->status);
            $orderStatus = strtolower((string) $order?->status);
            $issue = str_contains($scanStatus, 'lost') ? 'lost' : (str_contains($scanStatus, 'damaged') ? 'damaged' : (str_contains($scanStatus, 'exception') || str_contains($orderStatus, 'exception') ? 'lost' : null));
            $status = $issue ? 'exception' : (in_array($orderStatus, ['delivered', 'completed'], true) || $shipment->delivered_at ? 'delivered' : ($orderStatus === 'out_for_delivery' ? 'out_for_delivery' : 'at_hub'));
            $eventAt = $scan?->scanned_at ?? $shipment->delivered_at ?? $shipment->picked_up_at ?? $shipment->created_at;
            $collection = $collections->get($shipment->order_id);
            $settlement = $settlements->get($shipment->order_id);
            $rider = $shipment->rider;
            $riderName = $rider ? (trim($rider->first_name.' '.$rider->last_name) ?: $rider->user?->name) : null;
            $hours = $shipment->picked_up_at && $shipment->delivered_at ? round($shipment->picked_up_at->diffInMinutes($shipment->delivered_at) / 60, 1) : null;
            $onTime = $shipment->delivered_at && $shipment->estimated_delivery
                ? $shipment->delivered_at->toDateString() <= $shipment->estimated_delivery->toDateString()
                : ($status === 'delivered' && $hours !== null && $hours <= 48);

            return [
                'date' => Carbon::parse($eventAt)->timezone('Asia/Manila')->toDateString(),
                'time' => Carbon::parse($eventAt)->timezone('Asia/Manila')->format('H:i'),
                'reference' => $shipment->tracking_number ?: ($order?->order_number ?: 'ORDER-'.$shipment->order_id),
                'status' => $status,
                'issue' => $issue,
                'destination' => $order?->delivery_address ?: 'Delivery address not recorded',
                'rider' => $riderName ?: 'Unassigned',
                'hours' => $hours,
                'on_time' => (bool) $onTime,
                'cod' => (float) ($collection?->amount ?? 0),
                'cash_in' => $collection ? $cashIns->has($collection->id) : false,
                'distributed' => $settlement?->seller_released_at !== null,
                'distribution_amount' => $settlement?->seller_released_at !== null ? (float) $settlement->seller_amount : 0,
                'shipping' => (float) $shipment->shipping_fee,
            ];
        })->filter(fn ($row) => $row['date'] >= $dates['from'] && $row['date'] <= $dates['to'])->values();

        return response()->json(['data' => ['today' => $today, 'hub' => $hub->hub_name, 'hub_id' => (string) $hub->id, 'rows' => $rows, 'assignments' => $assignments]]);
    }
}
