<?php

namespace App\Http\Controllers\Api\Rider\User\Support;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Rider\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreSupportTicketMessageController extends Controller
{
    public function __invoke(
        Request $request,
        SupportTicket $ticket,
        RiderOperationsService $operations
    ): JsonResponse {
        $rider = $operations->rider($request->user());
        abort_unless((int) $ticket->rider_id === (int) $rider->id, 404);
        abort_unless(! in_array($ticket->status, ['resolved', 'closed'], true), 409, 'This support conversation is closed.');
        $validated = $request->validate(['body' => ['required', 'string', 'max:2000']]);
        $message = $ticket->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => trim($validated['body']),
        ]);
        $ticket->touch();

        return response()->json(['data' => $message], 201);
    }
}
