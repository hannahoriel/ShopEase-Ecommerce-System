<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\Logistics;
use App\Models\Rider\DeliveryAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReviewRiderDeliveryActionController extends Controller
{
    public function __invoke(Request $request, DeliveryAction $action): JsonResponse
    {
        $company = Logistics::query()
            ->where('user_id', $request->user()->id)
            ->where('registration_status', 'active')
            ->firstOrFail();
        abort_unless(
            $action->rider()->whereHas('logisticsBranch', fn ($branch) => $branch->where('logistics_id', $company->id))->exists(),
            404
        );
        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        DB::transaction(function () use ($request, $action, $validated): void {
            $locked = DeliveryAction::query()->lockForUpdate()->findOrFail($action->id);
            abort_unless($locked->status === 'pending', 409, 'Only pending delivery requests can be reviewed.');
            $locked->update([
                'status' => $validated['status'],
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        return response()->json(['data' => $action->fresh()]);
    }
}
