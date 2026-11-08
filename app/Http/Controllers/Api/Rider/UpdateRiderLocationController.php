<?php

namespace App\Http\Controllers\Api\Rider;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Shipment;
use App\Models\Logistics\CourierRegistration;
use App\Models\User;
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
        $shipments = Shipment::query()
            ->whereHas('order', fn ($query) => $query->whereIn('status', ['to_ship', 'in_transit', 'out_for_delivery']))
            ->where(function ($query) use ($request, $rider): void {
                $query->where('rider_id', $rider->id);
                if ($request->user()->role === User::ROLE_COURIER) {
                    $courier = CourierRegistration::query()->where('user_id', $request->user()->id)
                        ->where('status', 'approved')->first();
                    if ($courier) {
                        $query->orWhereHas('transferManifests', fn ($manifest) => $manifest
                            ->where('courier_registration_id', $courier->id)
                            ->where('status', 'loaded')->whereNotNull('departed_at'));
                    }
                }
            });
        $updated = $shipments
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
