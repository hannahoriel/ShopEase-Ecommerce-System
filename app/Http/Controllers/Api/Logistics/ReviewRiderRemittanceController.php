<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\Logistics;
use App\Models\Rider\Remittance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReviewRiderRemittanceController extends Controller
{
    public function __invoke(Request $request, Remittance $remittance): JsonResponse
    {
        $company = Logistics::query()
            ->where('user_id', $request->user()->id)
            ->where('registration_status', 'active')
            ->firstOrFail();
        abort_unless(
            $remittance->rider()->whereHas('logisticsBranch', fn ($branch) => $branch->where('logistics_id', $company->id))->exists(),
            404
        );
        $validated = $request->validate([
            'status' => ['required', Rule::in(['verified', 'rejected'])],
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $remittance, $validated): void {
            $locked = Remittance::query()->lockForUpdate()->findOrFail($remittance->id);
            abort_unless($locked->status === 'pending', 409, 'Only pending remittances can be reviewed.');
            $locked->update([
                ...$validated,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        return response()->json(['data' => $remittance->fresh()]);
    }
}
