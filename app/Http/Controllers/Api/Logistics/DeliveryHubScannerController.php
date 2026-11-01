<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\DeliveryHubScannerState;
use App\Models\Logistics\LogisticsDeliveryHub;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryHubScannerController extends Controller
{
    private function hub(Request $request): LogisticsDeliveryHub
    {
        return LogisticsDeliveryHub::query()->where('user_id', $request->user()->id)->firstOrFail();
    }

    public function show(Request $request): JsonResponse
    {
        $hub = $this->hub($request);
        $state = DeliveryHubScannerState::query()->where('logistics_delivery_hub_id', $hub->id)->first()?->state;

        return response()->json([
            'hub_id' => $hub->id,
            'state' => $state ?: ['parcels' => [], 'manifests' => [], 'events' => [], 'issues' => []],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $hub = $this->hub($request);
        $data = $request->validate([
            'state' => ['required', 'array'],
            'state.parcels' => ['present', 'array', 'max:2000'],
            'state.manifests' => ['present', 'array', 'max:1000'],
            'state.events' => ['present', 'array', 'max:2000'],
            'state.issues' => ['sometimes', 'array', 'max:2000'],
            'state.parcels.*.tracking' => ['required', 'string', 'max:90'],
            'state.parcels.*.status' => ['required', 'string', 'max:40'],
            'state.manifests.*.id' => ['required', 'string', 'max:120'],
        ]);
        // Laravel's validated() output includes only explicitly validated nested
        // keys; keep the rest of the scanner's evolving UI record fields too.
        $data['state'] = $request->input('state');

        $row = DB::transaction(function () use ($request, $hub, $data): DeliveryHubScannerState {
            return DeliveryHubScannerState::query()->updateOrCreate(
                ['logistics_delivery_hub_id' => $hub->id],
                ['updated_by' => $request->user()->id, 'state' => $data['state']],
            );
        });

        return response()->json(['message' => 'Scanner state saved.', 'hub_id' => $hub->id, 'state' => $row->state]);
    }
}
