<?php

namespace App\Http\Controllers\Api\Rider\Homepage\PickupTask;

use App\Http\Controllers\Api\Rider\Homepage\DeliveryTask\ShowDeliveryProofPhotoController;
use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Models\Admin\Order;
use App\Models\Rider\DeliveryProof;
use Illuminate\Http\Request;

class ShowPickupProofPhotoController extends Controller
{
    public function __invoke(
        Request $request,
        Order $order,
        DeliveryProof $proof,
        RiderDeliveryService $deliveries,
        ShowDeliveryProofPhotoController $photos
    ) {
        abort_unless($proof->status === 'in_transit', 404);

        return $photos($request, $order, $proof, $deliveries);
    }
}
