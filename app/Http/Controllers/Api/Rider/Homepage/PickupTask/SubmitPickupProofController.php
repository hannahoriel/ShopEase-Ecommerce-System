<?php

namespace App\Http\Controllers\Api\Rider\Homepage\PickupTask;

use App\Http\Controllers\Api\Rider\Homepage\DeliveryTask\SubmitDeliveryProofController;
use App\Http\Controllers\Controller;
use App\Http\Services\RiderDeliveryService;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Rider\Rider;
use App\Models\Rider\DeliveryProof;
use App\Services\ReturnPickupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SubmitPickupProofController extends Controller
{
    public function __invoke(
        Request $request,
        int $order,
        RiderDeliveryService $deliveries,
        RiderOperationsService $operations,
        SubmitDeliveryProofController $proofs,
        ReturnPickupService $returnPickups
    ): JsonResponse {
        $rider = $deliveries->activeRiderFor($request->user());
        abort_unless($operations->supportsOperation($rider, 'pickup'), 403, 'Only pickup riders can submit pickup proof.');

        // Mobile pickup tasks use OrderReturn.id for return pickup routes and
        // Order.id for ordinary seller collections. Resolve the return first;
        // otherwise the numeric return id may bind to an unrelated order and
        // fail while looking for a Shipment assigned to this rider.
        $return = OrderReturn::query()
            ->where('pickup_rider_id', $rider->id)
            ->where('status', 'accepted')
            ->where('return_type', 'return')
            ->where('shipping_status', 'scheduled')
            ->where(fn ($query) => $query->whereKey($order)->orWhere('order_id', $order))
            ->with('order')
            ->get()
            ->first(fn (OrderReturn $candidate): bool => $this->scannedCodeMatches($request->input('scanned_code'), $candidate));

        if ($return) {
            return $this->submitReturnPickup($request, $return, $rider, $returnPickups);
        }

        if ($request->input('status') !== 'in_transit') {
            throw ValidationException::withMessages([
                'status' => ['Pickup proof must move the order into transit.'],
            ]);
        }

        return $proofs($request, Order::query()->findOrFail($order), $deliveries, true);
    }

    private function submitReturnPickup(Request $request, OrderReturn $return, Rider $rider, ReturnPickupService $returnPickups): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['in_transit'])],
            'scanned_code' => ['required', 'string', 'max:2048'],
            'proof_photo' => ['required', 'image', 'max:10240'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $photoPath = Storage::disk('local')->putFile('delivery-proofs', $validated['proof_photo']);
        if (! $photoPath) {
            throw new \RuntimeException('The pickup proof photo could not be stored.');
        }

        try {
            DB::transaction(function () use ($request, $return, $rider, $returnPickups, $validated, $photoPath): void {
                $locked = OrderReturn::query()->whereKey($return->id)->lockForUpdate()->firstOrFail();
                abort_unless((int) $locked->pickup_rider_id === (int) $rider->id, 404);

                $returnPickups->recordOutcome(
                    $locked,
                    $request->user(),
                    ['attempt' => (int) $locked->failed_pickup_attempts + 1, 'outcome' => 'picked_up'],
                    rider: $rider,
                );

                DeliveryProof::query()->create([
                    'order_id' => $locked->order_id,
                    'rider_id' => $rider->id,
                    'status' => 'in_transit',
                    'scanned_code' => $validated['scanned_code'],
                    'photo_path' => $photoPath,
                    'notes' => trim('Buyer return pickup proof. '.($validated['notes'] ?? '')),
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        return response()->json([
            'message' => 'Return pickup completed and proof photo saved.',
            'id' => $return->order_id,
            'return_id' => $return->id,
            'order_number' => $return->order?->order_number,
            'status' => 'in_transit',
            'return_status' => 'picked_up',
        ]);
    }

    private function scannedCodeMatches(?string $code, OrderReturn $return): bool
    {
        $raw = trim((string) $code);
        if ($raw === '') return false;

        $path = parse_url($raw, PHP_URL_PATH);
        $identifier = is_string($path) && $path !== ''
            ? rawurldecode(basename($path))
            : $raw;

        return in_array($identifier, array_filter([
            $return->order?->order_number,
            $return->tracking_number,
        ]), true);
    }
}
