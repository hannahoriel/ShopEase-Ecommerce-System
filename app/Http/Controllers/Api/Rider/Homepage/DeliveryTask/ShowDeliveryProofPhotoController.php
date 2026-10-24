<?php

namespace App\Http\Controllers\Api\Rider\Homepage\DeliveryTask;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Models\Admin\Order;
use App\Models\Rider\DeliveryProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShowDeliveryProofPhotoController extends Controller
{
    public function __invoke(
        Request $request,
        Order $order,
        DeliveryProof $proof,
        RiderDeliveryService $deliveries
    ) {
        $rider = $deliveries->activeRiderFor($request->user());
        abort_unless($order->shipment()->where('rider_id', $rider->id)->exists(), 404);
        abort_unless($proof->order_id === $order->id, 404);
        abort_unless($proof->rider_id === $rider->id, 404);
        abort_unless(Storage::disk('local')->exists($proof->photo_path), 404);

        return response()->file(Storage::disk('local')->path($proof->photo_path));
    }
}
