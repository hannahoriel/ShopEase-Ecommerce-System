<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Notifications\RiderEventNotification;
use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\Shipment;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Rider\Rider;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeliveryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $logistics = $this->logisticsFor($request->user());
        $branchIds = $logistics->branches()->whereNull('archived_at')->pluck('id');
        $orders = Order::query()
            ->whereHas('shipment.rider', fn ($query) => $query->whereIn('logistics_branch_id', $branchIds))
            ->whereIn('status', ['to_ship', 'in_transit', 'out_for_delivery', 'delivered'])
            ->with($this->deliveryRelations())
            ->latest('updated_at')
            ->paginate($request->integer('per_page', 20));

        return response()->json($orders);
    }

    public function available(Request $request): JsonResponse
    {
        $this->logisticsFor($request->user());
        $orders = Order::query()
            ->where('status', 'to_ship')
            ->whereHas('shipment', fn ($query) => $query->whereNull('rider_id'))
            ->with(['seller:id,store_name', 'shipment:id,order_id,tracking_number,courier'])
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return response()->json([
            'data' => $orders->getCollection()->map(fn (Order $order): array => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'seller' => ['store_name' => $order->seller?->store_name],
                'shipment' => [
                    'tracking_number' => $order->shipment?->tracking_number,
                    'courier' => $order->shipment?->courier,
                ],
                'created_at' => $order->created_at?->toISOString(),
            ])->values(),
            'total' => $orders->total(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
        ]);
    }

    public function riders(Request $request): JsonResponse
    {
        $logistics = $this->logisticsFor($request->user());
        $branchIds = $logistics->branches()->whereNull('archived_at')->pluck('id');
        $riders = Rider::query()
            ->where('registration_status', 'active')
            ->whereHas('user', fn ($query) => $query->where('registration_status', 'active'))
            ->where(fn ($query) => $query
                ->whereNull('logistics_branch_id')
                ->orWhereIn('logistics_branch_id', $branchIds))
            ->with(['user:id,name', 'logisticsBranch:id,name'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(fn (Rider $rider): array => [
                'id' => $rider->id,
                'name' => trim($rider->first_name.' '.$rider->last_name),
                'vehicle' => $rider->vehicle,
                'plate_number' => $rider->plate_number,
                'contact_no' => $rider->contact_no,
                'logistics_branch_id' => $rider->logistics_branch_id,
                'branch' => $rider->logisticsBranch?->name,
                'available' => $rider->logistics_branch_id === null,
            ]);

        return response()->json(['data' => $riders->values()]);
    }

    public function branches(Request $request): JsonResponse
    {
        $branches = $this->logisticsFor($request->user())
            ->branches()
            ->whereNull('archived_at')
            ->orderBy('name')
            ->get(['id', 'name', 'address']);

        return response()->json(['data' => $branches]);
    }

    public function assignBranch(Request $request, Rider $rider): JsonResponse
    {
        $logistics = $this->logisticsFor($request->user());
        $validated = $request->validate([
            'logistics_branch_id' => ['present', 'nullable', 'integer'],
        ]);
        $branchId = $validated['logistics_branch_id'];

        DB::transaction(function () use ($logistics, $rider, $branchId): void {
            $lockedRider = Rider::query()->lockForUpdate()->findOrFail($rider->id);

            if ($branchId !== null) {
                $branch = LogisticsBranch::query()
                    ->where('logistics_id', $logistics->id)
                    ->whereNull('archived_at')
                    ->findOrFail($branchId);
            }

            $currentBranch = $lockedRider->logistics_branch_id
                ? LogisticsBranch::query()->find($lockedRider->logistics_branch_id)
                : null;
            abort_unless(
                $currentBranch === null || $currentBranch->logistics_id === $logistics->id,
                404
            );
            abort_unless(
                $lockedRider->registration_status === 'active'
                    && $lockedRider->user()->where('registration_status', 'active')->exists(),
                422,
                'Only an approved, active rider can be assigned to a branch.'
            );

            if ($branchId === null && $lockedRider->shipments()
                ->whereHas('order', fn ($query) => $query->whereIn('status', ['to_ship', 'in_transit', 'out_for_delivery']))
                ->exists()) {
                throw ValidationException::withMessages([
                    'logistics_branch_id' => ['A rider with active deliveries cannot be removed from the branch.'],
                ]);
            }

            $lockedRider->update(['logistics_branch_id' => $branchId]);
        });

        return response()->json(['message' => 'Rider branch assignment updated.']);
    }

    public function assign(Request $request, Order $order): JsonResponse
    {
        $logistics = $this->logisticsFor($request->user());
        $validated = $request->validate([
            'rider_id' => ['required', 'integer', 'exists:riders,id'],
        ]);

        DB::transaction(function () use ($logistics, $order, $validated): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless($lockedOrder->status === 'to_ship', 422, 'Only orders awaiting pickup can be assigned.');

            $shipment = Shipment::query()
                ->where('order_id', $lockedOrder->id)
                ->lockForUpdate()
                ->firstOrFail();
            if ($shipment->rider_id !== null) {
                $assignedToCompany = Rider::query()
                    ->whereKey($shipment->rider_id)
                    ->whereHas('logisticsBranch', fn ($query) => $query->where('logistics_id', $logistics->id))
                    ->exists();
                abort_unless($assignedToCompany, 404);
                abort(409, 'This delivery has already been assigned.');
            }

            $rider = Rider::query()
                ->where('registration_status', 'active')
                ->whereHas('user', fn ($query) => $query->where('registration_status', 'active'))
                ->whereHas('logisticsBranch', fn ($query) => $query->where('logistics_id', $logistics->id))
                ->lockForUpdate()
                ->findOrFail($validated['rider_id']);

            $shipment->update([
                'rider_id' => $rider->id,
                'courier' => $logistics->business_name ?: $shipment->courier,
            ]);
        });

        $riderUser = Rider::query()->with('user')->findOrFail($validated['rider_id'])->user;
        $riderUser->notify(new RiderEventNotification(
            'Delivery task assigned',
            "Order {$order->order_number} has been assigned to you.",
            'delivery_task',
            $order->id,
        ));

        return response()->json([
            'message' => 'Delivery assigned to rider.',
            'data' => $order->fresh()->load($this->deliveryRelations()),
        ], 200);
    }

    private function logisticsFor(User $user): Logistics
    {
        return Logistics::query()
            ->where('user_id', $user->id)
            ->where('registration_status', 'active')
            ->firstOrFail();
    }

    private function deliveryRelations(): array
    {
        return [
            'buyer:id,name',
            'buyer.buyerProfile:id,user_id,contact_no',
            'seller:id,store_name',
            'items:id,order_id,product_name,quantity',
            'shipment.rider.user:id,name',
            'shipment.rider.logisticsBranch:id,name,logistics_id',
        ];
    }
}
