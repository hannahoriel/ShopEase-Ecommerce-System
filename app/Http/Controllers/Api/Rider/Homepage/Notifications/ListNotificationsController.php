<?php

namespace App\Http\Controllers\Api\Rider\Homepage\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListNotificationsController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $operations->rider($request->user());
        $notifications = $request->user()->notifications()->latest()->paginate($request->integer('per_page', 30));

        return response()->json([
            'data' => $notifications->getCollection()->map(fn ($notification): array => [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'ShopEase update',
                'message' => $notification->data['message'] ?? '',
                'type' => $notification->data['type'] ?? 'system',
                'order_id' => $notification->data['order_id'] ?? null,
                'created_at' => $notification->created_at?->toISOString(),
                'read_at' => $notification->read_at?->toISOString(),
            ])->values(),
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }
}
