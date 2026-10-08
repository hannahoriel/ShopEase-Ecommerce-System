<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Buyer\BuyerNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = BuyerNotification::query()
            ->where('buyer_id', $request->user()->id)
            ->latest()
            ->paginate(50);

        return response()->json([
            'data' => $notifications->getCollection()->map(fn (BuyerNotification $notification): array => [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'data' => $notification->data ?? [],
                'read_at' => $notification->read_at?->toISOString(),
                'created_at' => $notification->created_at?->toISOString(),
            ])->values(),
            'unread_count' => BuyerNotification::query()
                ->where('buyer_id', $request->user()->id)
                ->whereNull('read_at')
                ->count(),
            'total' => $notifications->total(),
        ]);
    }

    public function markRead(Request $request, BuyerNotification $notification): JsonResponse
    {
        abort_unless($notification->buyer_id === $request->user()->id, 404);

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json([
            'message' => 'Notification marked as read.',
            'data' => [
                'id' => $notification->id,
                'read_at' => $notification->fresh()->read_at?->toISOString(),
            ],
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $updated = BuyerNotification::query()
            ->where('buyer_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now(), 'updated_at' => now()]);

        return response()->json([
            'message' => 'All notifications marked as read.',
            'updated_count' => $updated,
        ]);
    }
}
