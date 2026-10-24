<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\Logistics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListRiderRemittancesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $company = Logistics::query()
            ->where('user_id', $request->user()->id)
            ->where('registration_status', 'active')
            ->firstOrFail();
        $query = \App\Models\Rider\Remittance::query()
            ->with('rider.user:id,name')
            ->whereHas('rider.logisticsBranch', fn ($branch) => $branch->where('logistics_id', $company->id))
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $rows = $query->paginate($request->integer('per_page', 30));

        return response()->json([
            'data' => $rows->getCollection()->map(function ($remittance): array {
                $data = $remittance->makeHidden(['receipt_path'])->toArray();
                $data['rider_name'] = $remittance->rider?->user?->name;
                $data['receipt_url'] = $remittance->receipt_path
                    ? route('logistics.api.rider-remittances.receipt', ['remittance' => $remittance->id])
                    : null;

                return $data;
            })->values(),
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'total' => $rows->total(),
            ],
        ]);
    }
}
