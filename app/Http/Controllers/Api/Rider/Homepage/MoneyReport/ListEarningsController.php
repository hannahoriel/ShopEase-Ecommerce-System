<?php

namespace App\Http\Controllers\Api\Rider\Homepage\MoneyReport;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListEarningsController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        $entries = $rider->earningEntries()
            ->where('status', 'posted')
            ->when($request->filled('date'), fn ($query) => $query->whereDate('posted_at', $request->input('date')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('posted_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('posted_at', '<=', $request->input('to')))
            ->latest('posted_at')
            ->paginate($request->integer('per_page', 30));

        return response()->json([
            'data' => $entries,
            'total' => $rider->earningEntries()->where('status', 'posted')->sum('amount'),
        ]);
    }
}
