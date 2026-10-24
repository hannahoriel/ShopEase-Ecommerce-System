<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\Logistics;
use App\Models\Rider\SupportTicket;
use App\Notifications\RiderEventNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreRiderSupportTicketMessageController extends Controller
{
    public function __invoke(Request $request, SupportTicket $ticket): JsonResponse
    {
        $company = Logistics::query()
            ->where('user_id', $request->user()->id)
            ->where('registration_status', 'active')
            ->firstOrFail();
        abort_unless(
            $ticket->rider()->whereHas('logisticsBranch', fn ($branch) => $branch->where('logistics_id', $company->id))->exists(),
            404
        );
        abort_unless(! in_array($ticket->status, ['resolved', 'closed'], true), 409, 'This support conversation is closed.');
        $validated = $request->validate(['body' => ['required', 'string', 'max:2000']]);
        $message = $ticket->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => trim($validated['body']),
        ]);
        $ticket->touch();
        $ticket->update(['status' => 'in_progress']);
        $ticket->rider->user->notify(new RiderEventNotification(
            'Support replied',
            "Logistics replied to support ticket: {$ticket->subject}.",
            'support_ticket'
        ));

        return response()->json(['data' => $message], 201);
    }
}
