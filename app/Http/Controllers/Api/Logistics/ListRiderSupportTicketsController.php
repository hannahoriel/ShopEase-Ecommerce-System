<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\Logistics;
use App\Models\Rider\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListRiderSupportTicketsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $company = Logistics::query()
            ->where('user_id', $request->user()->id)
            ->where('registration_status', 'active')
            ->firstOrFail();
        $tickets = SupportTicket::query()
            ->with(['rider.user:id,name', 'order:id,order_number', 'messages.sender:id,name'])
            ->whereNull('archived_at')
            ->whereHas('rider.logisticsBranch', fn ($branch) => $branch->where('logistics_id', $company->id))
            ->latest()
            ->paginate($request->integer('per_page', 30));

        return response()->json([
            'data' => $tickets->getCollection()->map(fn (SupportTicket $ticket): array => [
                ...$ticket->toArray(),
                'rider_name' => $ticket->rider?->user?->name,
                'order_number' => $ticket->order?->order_number,
                'messages' => $ticket->messages->map(fn ($message): array => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_name' => $message->sender?->name,
                    'created_at' => $message->created_at?->toISOString(),
                ])->values(),
            ])->values(),
            'meta' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'total' => $tickets->total(),
            ],
        ]);
    }
}
