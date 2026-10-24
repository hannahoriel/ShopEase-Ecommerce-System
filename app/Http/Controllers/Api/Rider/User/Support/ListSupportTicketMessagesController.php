<?php

namespace App\Http\Controllers\Api\Rider\User\Support;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Rider\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListSupportTicketMessagesController extends Controller
{
    public function __invoke(
        Request $request,
        SupportTicket $ticket,
        RiderOperationsService $operations
    ): JsonResponse {
        $rider = $operations->rider($request->user());
        abort_unless((int) $ticket->rider_id === (int) $rider->id, 404);

        return response()->json([
            'data' => $ticket->messages()->with('sender:id,name')->get()->map(
                fn ($message): array => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_name' => $message->sender?->name,
                    'is_rider' => (int) $message->sender_id === (int) $request->user()->id,
                    'created_at' => $message->created_at?->toISOString(),
                ]
            )->values(),
        ]);
    }
}
