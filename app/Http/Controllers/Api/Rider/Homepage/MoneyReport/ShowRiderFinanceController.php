<?php

namespace App\Http\Controllers\Api\Rider\Homepage\MoneyReport;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowRiderFinanceController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        $collections = $rider->codCollections()->with(['order:id,order_number', 'remittances:id,status'])->latest('collected_at')->get();
        $remittances = $rider->remittances()->latest()->get();
        $available = (float) $collections->filter(fn ($collection) => $collection->remittances->isEmpty())->sum('amount');
        $pending = (float) $collections->filter(fn ($collection) => $collection->remittances->contains(fn ($remittance) => in_array($remittance->status, ['pending', 'discrepancy'], true)))->sum('amount');
        $verified = (float) $collections->filter(fn ($collection) => $collection->remittances->contains(fn ($remittance) => in_array($remittance->status, ['verified', 'cashed_in', 'distributed'], true)))->sum('amount');
        $collections->each(function ($collection): void {
            $collection->setAttribute('remittance_status', $collection->remittances->first()?->status);
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
            ],
        ]);
    }
}
