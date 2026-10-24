<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\Logistics;
use App\Models\Rider\EarningEntry;
use App\Models\Rider\Rider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreRiderEarningController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $company = Logistics::query()
            ->where('user_id', $request->user()->id)
            ->where('registration_status', 'active')
            ->firstOrFail();
        $validated = $request->validate([
            'rider_id' => ['required', 'integer', 'exists:riders,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'type' => ['required', Rule::in(['base_pay', 'bonus', 'tip', 'adjustment'])],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
        ]);
        $rider = Rider::query()
            ->whereKey($validated['rider_id'])
            ->whereHas('logisticsBranch', fn ($branch) => $branch->where('logistics_id', $company->id))
            ->firstOrFail();
        if (! empty($validated['order_id'])) {
            abort_unless($rider->shipments()->where('order_id', $validated['order_id'])->exists(), 404);
        }

        $entry = EarningEntry::create([
            ...$validated,
            'rider_id' => $rider->id,
            'posted_by' => $request->user()->id,
            'status' => 'posted',
            'posted_at' => now('Asia/Manila'),
        ]);

        return response()->json(['data' => $entry], 201);
    }
}
