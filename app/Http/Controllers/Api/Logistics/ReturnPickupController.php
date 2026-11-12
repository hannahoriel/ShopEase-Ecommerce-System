<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\OrderReturn;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Services\ReturnPickupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReturnPickupController extends Controller
{
    public function schedule(Request $request, OrderReturn $return, ReturnPickupService $pickups): JsonResponse
    {
        $hub = $this->hub($request);
        $data = $request->validate([
            'pickup_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'pickup_time' => ['required', Rule::in(OrderReturn::PICKUP_TIMES)],
            'rider_id' => ['required', 'integer'],
            'attempt' => ['required', 'integer', 'between:1,3'],
        ]);

        return response()->json(['data' => $pickups->schedule($return, $hub, $request->user(), $data)]);
    }

    public function hubOutcome(Request $request, OrderReturn $return, ReturnPickupService $pickups): JsonResponse
    {
        return response()->json(['data' => $pickups->recordOutcome(
            $return, $request->user(), $this->outcome($request), hub: $this->hub($request),
        )]);
    }

    public function riderOutcome(Request $request, OrderReturn $return, ReturnPickupService $pickups, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        abort_unless($operations->supportsOperation($rider, 'pickup'), 403);

        return response()->json(['data' => $pickups->recordOutcome(
            $return, $request->user(), $this->outcome($request), rider: $rider,
        )]);
    }

    private function outcome(Request $request): array
    {
        return $request->validate([
            'attempt' => ['required', 'integer', 'between:1,3'],
            'outcome' => ['required', 'in:picked_up,failed'],
            'reason' => ['required_if:outcome,failed', 'nullable', 'string', 'max:500'],
        ]);
    }

    private function hub(Request $request): LogisticsDeliveryHub
    {
        return LogisticsDeliveryHub::where('user_id', $request->user()->id)
            ->where('status', 'active')->whereNull('archived_at')->firstOrFail();
    }
}
