<?php

namespace App\Http\Controllers\Api\Rider;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateRiderLocationController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);
        $rider = $operations->rider($request->user());
        $updated = Shipment::query()
            ->where('rider_id', $rider->id)
            ->whereHas('order', fn ($query) => $query->whereIn(
                'status',
                ['to_ship', 'in_transit', 'out_for_delivery']
            ))
            ->update([
                'current_latitude' => $validated['latitude'],
                'current_longitude' => $validated['longitude'],
                'location_updated_at' => now(),
            ]);

        return response()->json([
            'updated_shipments' => $updated,
            'location_updated_at' => now()->toISOString(),
        ]);
    }
}
