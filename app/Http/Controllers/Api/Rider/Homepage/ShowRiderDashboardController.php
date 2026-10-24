<?php

namespace App\Http\Controllers\Api\Rider\Homepage;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowRiderDashboardController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        $orders = Order::query()->whereHas('shipment', fn ($query) => $query->where('rider_id', $rider->id));

        return response()->json([
            'data' => [
                'profile' => $operations->profile($rider),
                'active_deliveries' => (clone $orders)->whereIn('status', ['to_ship', 'in_transit', 'out_for_delivery'])->count(),
                'completed_deliveries' => (clone $orders)->where('status', 'delivered')->count(),
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
