<?php

namespace App\Http\Controllers\Api\Rider\Homepage;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Http\Services\AssignScheduledPickups;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Logistics\DeliveryHubRiderRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowRiderDashboardController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations, AssignScheduledPickups $assignPickups): JsonResponse
    {
        $rider = $operations->rider($request->user());
        $assignPickups->forRider($rider);
        $isPickup = str_contains(
            mb_strtolower(preg_replace('/[^a-z]/i', '', (string) $rider->logistics_operation)),
            'pickup'
        );
        $orders = Order::query()->whereHas('shipment', fn ($query) => $query->where('rider_id', $rider->id));
        $hubIds = DeliveryHubRiderRegistration::where('user_id', $rider->user_id)->where('status', 'approved')->select('logistics_delivery_hub_id');
        $returnPickups = OrderReturn::query()->where('pickup_rider_id', $rider->id)
            ->where('status', 'accepted')->where('return_type', 'return')->where('shipping_status', 'scheduled')
            ->where(function ($query) use ($hubIds): void {
                $query->whereIn('return_delivery_hub_id', $hubIds)
                    ->orWhere(fn ($legacy) => $legacy->whereNull('return_delivery_hub_id')
                        ->whereHas('order.shipment', fn ($shipment) => $shipment->whereIn('logistics_delivery_hub_id', $hubIds)));
            });
        $nextReturnPickup = (clone $returnPickups)->with('order:id,order_number,delivery_address,delivery_name,delivery_phone')
            ->orderBy('pickup_date')->orderBy('pickup_time')->first();
        $activeReturnDeliveries = $isPickup ? 0 : OrderReturn::query()
            ->where('delivery_rider_id', $rider->id)
            ->where('status', 'accepted')->where('return_type', 'return')
            ->where('shipping_status', 'out_for_delivery_to_seller')
            ->count();
        $completedReturnDeliveries = $isPickup ? 0 : OrderReturn::query()
            ->where('delivery_rider_id', $rider->id)
            ->whereIn('status', ['accepted', 'refunded'])->where('return_type', 'return')
            ->whereIn('shipping_status', ['delivered', 'returned'])
            ->count();

        return response()->json([
            'data' => [
                'profile' => $operations->profile($rider),
                'task_type' => $isPickup ? 'pickup' : 'delivery',
                'task_label' => $isPickup ? 'For Pickup' : 'Delivery',
                'active_tasks' => (clone $orders)->whereIn('status', $isPickup
                    ? ['to_ship', 'in_transit']
                    : ['in_transit', 'out_for_delivery'])
                    ->when($isPickup, fn ($query) => $query->whereDoesntHave('shipment.transferManifests', fn ($manifest) => $manifest
                        ->where('origin_type', 'seller_collection')
                        ->where('batch_key', 'like', 'pickup-dropoff-%')))
                    ->count() + ($isPickup ? (clone $returnPickups)->count() : $activeReturnDeliveries),
                'active_deliveries' => $isPickup
                    ? (clone $orders)->whereIn('status', ['to_ship', 'in_transit'])
                        ->whereDoesntHave('shipment.transferManifests', fn ($manifest) => $manifest
                            ->where('origin_type', 'seller_collection')
                            ->where('batch_key', 'like', 'pickup-dropoff-%'))->count() + (clone $returnPickups)->count()
                    : (clone $orders)->whereIn('status', ['to_ship', 'in_transit', 'out_for_delivery'])->count() + $activeReturnDeliveries,
                'next_pickup' => $nextReturnPickup ? [
                    'id' => $nextReturnPickup->id,
                    'task_type' => 'return_pickup',
                    'title' => 'Buyer Return Pickup',
                    'order_number' => $nextReturnPickup->order?->order_number,
                    'pickup_address' => $nextReturnPickup->pickup_address ?: $nextReturnPickup->order?->delivery_address,
                    'pickup_date' => $nextReturnPickup->pickup_date?->toDateString(),
                    'pickup_time' => $nextReturnPickup->pickup_time,
                    'total' => (float) $nextReturnPickup->refund_amount,
                ] : null,
                'completed_deliveries' => $isPickup
                    ? (clone $orders)->whereHas('shipment.transferManifests', fn ($manifest) => $manifest
                        ->where('origin_type', 'seller_collection')
                        ->where('batch_key', 'like', 'pickup-dropoff-%'))->count()
                    : (clone $orders)->where('status', 'delivered')->count() + $completedReturnDeliveries,
                'unread_notifications' => $request->user()->unreadNotifications()->count(),
                'attendance' => $rider->attendanceRecords()->whereDate('attendance_date', now('Asia/Manila')->toDateString())->first(),
                'unremitted_cod' => number_format(
                    (float) $rider->codCollections()->sum('amount')
                    - (float) $rider->remittances()->whereIn('status', ['verified', 'cashed_in', 'distributed'])->sum('amount'),
                    2,
                    '.',
                    ''
                ),
            ],
        ]);
    }
}
