<?php

namespace App\Http\Controllers\Api\Rider\Homepage;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Http\Services\AssignScheduledPickups;
use App\Models\Admin\Order;
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
                    ->count(),
                'active_deliveries' => $isPickup
                    ? (clone $orders)->whereIn('status', ['to_ship', 'in_transit'])
                        ->whereDoesntHave('shipment.transferManifests', fn ($manifest) => $manifest
                            ->where('origin_type', 'seller_collection')
                            ->where('batch_key', 'like', 'pickup-dropoff-%'))->count()
                    : (clone $orders)->whereIn('status', ['to_ship', 'in_transit', 'out_for_delivery'])->count(),
                'completed_deliveries' => $isPickup
                    ? (clone $orders)->whereHas('shipment.transferManifests', fn ($manifest) => $manifest
                        ->where('origin_type', 'seller_collection')
                        ->where('batch_key', 'like', 'pickup-dropoff-%'))->count()
                    : (clone $orders)->where('status', 'delivered')->count(),
                'unread_notifications' => $request->user()->unreadNotifications()->count(),
                'attendance' => $rider->attendanceRecords()->whereDate('attendance_date', now('Asia/Manila')->toDateString())->first(),
                'unremitted_cod' => number_format(
                    (float) $rider->codCollections()->sum('amount')
                    - (float) $rider->remittances()->where('status', 'verified')->sum('amount'),
                    2,
                    '.',
                    ''
                ),
            ],
        ]);
    }
}
