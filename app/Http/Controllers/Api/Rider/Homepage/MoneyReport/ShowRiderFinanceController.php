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
        $collections = $rider->codCollections()->with('order:id,order_number')->latest('collected_at')->get();
        $remittances = $rider->remittances()->latest()->get();
        $verified = (float) $remittances->where('status', 'verified')->sum('amount');
        $pending = (float) $remittances->where('status', 'pending')->sum('amount');

        return response()->json([
            'data' => [
                'cod_collected' => $collections->sum('amount'),
                'cod_remitted' => $verified,
                'cod_pending_remittance' => max(0, (float) $collections->sum('amount') - $verified),
                'cod_available_to_remit' => max(0, (float) $collections->sum('amount') - $verified - $pending),
                'cod_collections' => $collections,
                'remittances' => $remittances,
                'earnings' => $rider->earningEntries()->where('status', 'posted')->latest('posted_at')->get(),
                'earnings_total' => $rider->earningEntries()->where('status', 'posted')->sum('amount'),
            ],
        ]);
    }
}
