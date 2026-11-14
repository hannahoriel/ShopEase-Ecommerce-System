<?php

namespace App\Http\Controllers\Api\Rider\Homepage\DeliveryTask;

use App\Http\Controllers\Controller;
use App\Models\Admin\OrderReturn;
use App\Models\Rider\CodCollection;
use App\Models\Rider\DeliveryProof;
use App\Http\Services\RiderDeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SubmitReturnDeliveryProofController extends Controller
{
    public function __invoke(Request $request, OrderReturn $return, RiderDeliveryService $deliveries): JsonResponse
    {
        $rider = $deliveries->activeRiderFor($request->user());
        $data = $request->validate([
            'status' => ['required', Rule::in(['delivered'])],
            'scanned_code' => ['required', 'string', 'max:2048'],
            'proof_photo' => ['required', 'image', 'max:10240'],
            'delivery_method' => ['nullable', Rule::in(['handed_to_buyer', 'left_at_door'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        abort_unless((int) $return->delivery_rider_id === (int) $rider->id
            && $return->shipping_status === 'out_for_delivery_to_seller', 404,
            'This return parcel is not assigned to you for delivery.');
        $order = $return->order;
        $scan = trim($data['scanned_code']);
        $path = parse_url($scan, PHP_URL_PATH);
        $token = is_string($path) && $path !== '' ? rawurldecode(basename($path)) : $scan;
        abort_unless(in_array($scan, [$return->tracking_number, $order?->order_number], true)
            || $token === $order?->order_number, 422, 'The scanned code does not match this return parcel.');

        $photoPath = Storage::disk('local')->putFile('delivery-proofs', $data['proof_photo']);
        abort_if(! $photoPath, 500, 'The delivery proof photo could not be stored.');
        try {
            DB::transaction(function () use ($return, $rider, $request, $data, $scan, $photoPath): void {
                $locked = OrderReturn::query()->whereKey($return->id)->lockForUpdate()->firstOrFail();
                abort_unless((int) $locked->delivery_rider_id === (int) $rider->id
                    && $locked->shipping_status === 'out_for_delivery_to_seller', 409,
                    'This return parcel is no longer assigned for delivery.');
                $order = $locked->order;
                $seller = $order?->seller;
                $location = collect([$seller?->store_name, $seller?->municipality, $seller?->province])->filter()->implode(' · ');
                DeliveryProof::query()->create([
                    'order_id' => $order->id, 'rider_id' => $rider->id, 'status' => 'delivered',
                    'scanned_code' => $scan, 'photo_path' => $photoPath,
                    'delivery_method' => $data['delivery_method'] ?? null, 'notes' => $data['notes'] ?? 'Return parcel delivered to seller.',
                ]);
                $locked->forceFill(['shipping_status' => 'returned'])->save();
                if ((float) $locked->refund_amount > 0) {
                    CodCollection::query()->firstOrCreate(
                        ['order_return_id' => $locked->id],
                        [
                            'rider_id' => $rider->id,
                            'order_id' => null,
                            'amount' => $locked->refund_amount,
                            'collected_at' => now(),
                        ],
                    );
                }
                $locked->events()->create([
                    'status' => 'delivered',
                    'location' => $location.' · Delivered by '.$rider->first_name.' '.$rider->last_name,
                    'scanned_at' => now(),
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        return response()->json([
            'message' => 'Return parcel delivered to the seller.',
            'return_id' => $return->id,
            'return_status' => $return->fresh()->shipping_status,
            'returned_at' => $return->fresh()->returned_at?->toISOString(),
        ]);
    }
}
