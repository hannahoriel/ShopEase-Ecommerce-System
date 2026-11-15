<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Models\Admin\Order;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use Illuminate\Http\Request;

class EarningsController
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);

        $company = Logistics::query()->where('user_id', $request->user()->id)->firstOrFail();
        $orders = Order::query()
            ->with(['shipment.logisticsBranch', 'shipment.deliveryHub.logisticsBranch'])
            ->where('logistics_id', $company->id)
            ->where('status', 'completed')
            ->whereHas('shipment', fn ($query) => $query->whereNotNull('delivered_at'))
            ->whereHas('shipment', fn ($query) => $query->whereDate('delivered_at', '>=', $validated['from'])->whereDate('delivered_at', '<=', $validated['to']))
            ->get();

        $deliveredRows = $orders->map(function (Order $order): array {
            $shipment = $order->shipment;
            // Some existing shipments only have the destination hub saved. The
            // hub already belongs to a sorting center, so use that relationship
            // to place the parcel under the correct network unit.
            $branch = $shipment->logisticsBranch ?: $shipment->deliveryHub?->logisticsBranch;
            $center = $branch?->name ?: 'Unassigned sorting center';
            $hub = $shipment->deliveryHub?->hub_name ?: 'Unassigned delivery hub';
            $fee = max(0, (float) ($order->shipping_fee ?: $shipment?->shipping_fee ?: 0));

            return [
                'date' => $shipment->delivered_at->timezone('Asia/Manila')->format('Y-m-d H:i'),
                'reference' => $order->order_number ?: 'ORDER-'.$order->id,
                'route' => $center.' → '.$hub,
                'center' => $center,
                'hub' => $hub,
                'center_id' => $branch?->id,
                'hub_id' => $shipment->logistics_delivery_hub_id,
                'status' => 'Credited',
                'amount' => $fee,
            ];
        })->sortByDesc('date')->values();

        $returnedOrders = Order::query()
            ->with(['shipment.logisticsBranch', 'shipment.deliveryHub.logisticsBranch'])
            ->where('status', 'cancelled')
            ->where(function ($query) use ($company): void {
                $query->where('logistics_id', $company->id)
                    ->orWhereHas('shipment.logisticsBranch', fn ($branch) => $branch->where('logistics_id', $company->id))
                    ->orWhereHas('shipment.deliveryHub.logisticsBranch', fn ($branch) => $branch->where('logistics_id', $company->id));
            })
            ->whereHas('shipment', fn ($query) => $query
                ->where('return_to_seller_status', 'completed')
                ->whereDate('returned_to_seller_at', '>=', $validated['from'])
                ->whereDate('returned_to_seller_at', '<=', $validated['to']))
            ->whereHas('deliveryAttempts', fn ($query) => $query->where('attempt_number', 3))
            ->get();

        $rtoRows = $returnedOrders->map(function (Order $order): array {
            $shipment = $order->shipment;
            $branch = $shipment->logisticsBranch ?: $shipment->deliveryHub?->logisticsBranch;

            return [
                'date' => $shipment->returned_to_seller_at->timezone('Asia/Manila')->format('Y-m-d H:i'),
                'reference' => $order->order_number ?: 'ORDER-'.$order->id,
                'route' => ($branch?->name ?: 'Unassigned sorting center').' → '.($shipment->deliveryHub?->hub_name ?: 'Unassigned delivery hub'),
                'center' => $branch?->name ?: 'Unassigned sorting center',
                'hub' => $shipment->deliveryHub?->hub_name ?: 'Unassigned delivery hub',
                'center_id' => $branch?->id,
                'hub_id' => $shipment->logistics_delivery_hub_id,
                'status' => 'Seller-paid RTO',
                'amount' => max(0, (float) ($order->shipping_fee ?: $shipment?->shipping_fee ?: 0)),
            ];
        })->sortByDesc('date')->values();

        $rows = $deliveredRows->concat($rtoRows)->sortByDesc('date')->values();
        $daily = $rows->groupBy(fn ($row) => substr($row['date'], 0, 10))
            ->map(fn ($dayRows, $date) => ['date' => $date, 'amount' => round($dayRows->sum('amount'), 2), 'parcels' => $dayRows->count()])
            ->values();
        // Include every registered unit even when its selected-period earnings are zero.
        $branches = LogisticsBranch::query()
            ->with('deliveryHubs')
            ->where('logistics_id', $company->id)
            ->orderBy('name')
            ->get();
        $units = $branches->map(function (LogisticsBranch $branch) use ($rows) {
            $centerRows = $rows->where('center_id', $branch->id);
            return [
                'name' => $branch->name,
                'hubs' => $branch->deliveryHubs->sortBy('hub_name')->map(function ($hub) use ($centerRows) {
                    $hubRows = $centerRows->where('hub_id', $hub->id);
                    return ['name' => $hub->hub_name, 'parcels' => $hubRows->count(), 'amount' => round($hubRows->sum('amount'), 2)];
                })->values(),
                'parcels' => $centerRows->count(),
                'amount' => round($centerRows->sum('amount'), 2),
            ];
        })->values();
        $gross = round($rows->sum('amount'), 2);

        return response()->json([
            'summary' => ['gross' => $gross, 'company_income' => $gross, 'parcels' => $rows->count(), 'shipping_fee' => $rows->count() ? round($gross / $rows->count(), 2) : 0],
            'transactions' => $rows,
            'daily' => $daily,
            'units' => $units,
        ]);
    }
}
