<?php

namespace App\Http\Controllers\Api\Rider\Homepage\MoneyReport;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\OrderReturn;
use App\Models\Rider\CodCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowRiderFinanceController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        // Reconcile return parcels delivered before return refunds were connected
        // to rider COD remittances. The unique order_return_id keeps this safe on refresh.
        OrderReturn::query()
            ->where('delivery_rider_id', $rider->id)
            ->where('return_type', 'return')
            ->whereIn('status', ['accepted', 'refunded'])
            ->whereIn('shipping_status', ['delivered', 'returned'])
            ->where('refund_amount', '>', 0)
            ->with(['events' => fn ($query) => $query->reorder()->where('status', 'delivered')->orderByDesc('scanned_at')])
            ->get()
            ->each(function (OrderReturn $return) use ($rider): void {
                CodCollection::query()->firstOrCreate(
                    ['order_return_id' => $return->id],
                    [
                        'rider_id' => $rider->id,
                        'order_id' => null,
                        'amount' => $return->refund_amount,
                        'collected_at' => $return->events->first()?->scanned_at ?? $return->updated_at,
                    ],
                );
            });

        $collections = $rider->codCollections()->with([
            'order:id,order_number',
            'orderReturn.order:id,order_number,buyer_id',
            'orderReturn.order.buyer:id,name',
            'remittances:id,status',
        ])->latest('collected_at')->get();
        $remittances = $rider->remittances()->latest()->get();
        $available = (float) $collections->filter(fn ($collection) => $collection->remittances->isEmpty())->sum('amount');
        $pending = (float) $collections->filter(fn ($collection) => $collection->remittances->contains(fn ($remittance) => in_array($remittance->status, ['pending', 'discrepancy'], true)))->sum('amount');
        $verified = (float) $collections->filter(fn ($collection) => $collection->remittances->contains(fn ($remittance) => in_array($remittance->status, ['verified', 'cashed_in', 'distributed'], true)))->sum('amount');
        $collections->each(function ($collection): void {
            $collection->setAttribute('remittance_status', $collection->remittances->first()?->status);
            if ($collection->order_return_id) {
                $collection->setAttribute('collection_type', 'return_refund');
                $collection->setAttribute('order_number', $collection->orderReturn?->order?->order_number);
                $collection->setAttribute('buyer_name', $collection->orderReturn?->order?->buyer?->name);
            }
        });

        return response()->json([
            'data' => [
                'cod_collected' => $collections->sum('amount'),
                'cod_remitted' => $verified,
                'cod_pending_remittance' => round($pending, 2),
                'cod_available_to_remit' => round($available, 2),
                'cod_collections' => $collections,
                'remittances' => $remittances,
                'earnings' => $rider->earningEntries()->where('status', 'posted')->latest('posted_at')->get(),
                'earnings_total' => $rider->earningEntries()->where('status', 'posted')->sum('amount'),
                'delivered_parcels' => $rider->shipments()
                    ->whereNotNull('delivered_at')
                    ->latest('delivered_at')
                    ->get(['id', 'order_id', 'delivered_at']),
            ],
        ]);
    }
}
