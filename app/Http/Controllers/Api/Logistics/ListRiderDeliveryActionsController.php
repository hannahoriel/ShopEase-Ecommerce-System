<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\Logistics;
use App\Models\Rider\DeliveryAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListRiderDeliveryActionsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $company = Logistics::query()
            ->where('user_id', $request->user()->id)
            ->where('registration_status', 'active')
            ->firstOrFail();
        $actions = DeliveryAction::query()
            ->with(['rider.user:id,name', 'order:id,order_number'])
            ->whereHas('rider.logisticsBranch', fn ($branch) => $branch->where('logistics_id', $company->id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate($request->integer('per_page', 30));

        return response()->json([
            'data' => $actions->getCollection()->map(fn (DeliveryAction $action): array => [
                ...$action->toArray(),
                'rider_name' => $action->rider?->user?->name,
                'order_number' => $action->order?->order_number,
            ])->values(),
            'meta' => [
                'current_page' => $actions->currentPage(),
                'last_page' => $actions->lastPage(),
                'total' => $actions->total(),
            ],
        ]);
    }
}
