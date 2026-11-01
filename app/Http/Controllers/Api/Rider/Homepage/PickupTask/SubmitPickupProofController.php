<?php

namespace App\Http\Controllers\Api\Rider\Homepage\PickupTask;

use App\Http\Controllers\Api\Rider\Homepage\DeliveryTask\SubmitDeliveryProofController;
use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubmitPickupProofController extends Controller
{
    public function __invoke(
        Request $request,
        Order $order,
        RiderDeliveryService $deliveries,
        RiderOperationsService $operations,
        SubmitDeliveryProofController $proofs
    ) {
        $rider = $deliveries->activeRiderFor($request->user());
        abort_unless($operations->supportsOperation($rider, 'pickup'), 403, 'Only pickup riders can submit pickup proof.');

        if ($request->input('status') !== 'in_transit') {
            throw ValidationException::withMessages([
                'status' => ['Pickup proof must move the order into transit.'],
            ]);
        }

        return $proofs($request, $order, $deliveries, true);
    }
}
