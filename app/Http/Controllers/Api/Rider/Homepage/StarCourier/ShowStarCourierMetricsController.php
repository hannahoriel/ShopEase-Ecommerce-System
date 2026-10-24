<?php

namespace App\Http\Controllers\Api\Rider\Homepage\StarCourier;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowStarCourierMetricsController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $validated = $request->validate([
            'period' => ['required', 'in:month,year'],
            'year' => ['required', 'integer', 'between:2000,2100'],
            'month' => ['required_if:period,month', 'nullable', 'integer', 'between:1,12'],
        ]);
        $rider = $operations->rider($request->user());
        $start = Carbon::create(
            (int) $validated['year'],
            (int) ($validated['month'] ?? 1),
            1,
            0,
            0,
            0,
            'Asia/Manila'
        )->startOfDay();
        $end = $validated['period'] === 'month'
            ? $start->copy()->endOfMonth()
            : $start->copy()->endOfYear();

        return response()->json([
            'data' => [
                'profile' => $operations->profile($rider),
                'period' => $validated['period'],
                'year' => $start->year,
                'month' => $validated['period'] === 'month' ? $start->month : null,
                'completed_deliveries' => Order::query()
                    ->whereHas('shipment', fn ($shipments) => $shipments->where('rider_id', $rider->id))
                    ->where('status', 'delivered')
                    ->whereBetween('updated_at', [$start, $end])
                    ->count(),
                'posted_earnings' => number_format(
                    (float) $rider->earningEntries()
                        ->where('status', 'posted')
                        ->whereBetween('posted_at', [$start, $end])
                        ->sum('amount'),
                    2,
                    '.',
                    ''
                ),
            ],
        ]);
    }
}
