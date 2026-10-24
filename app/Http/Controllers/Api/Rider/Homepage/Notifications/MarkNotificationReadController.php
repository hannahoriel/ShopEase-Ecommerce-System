<?php

namespace App\Http\Controllers\Api\Rider\Homepage\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarkNotificationReadController extends Controller
{
    public function __invoke(Request $request, string $notification, RiderOperationsService $operations): JsonResponse
    {
        $operations->rider($request->user());
        $record = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        $record->markAsRead();

        return response()->json(['message' => 'Notification marked as read.']);
    }
}
