<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\Logistics;
use App\Models\Rider\SupportTicket;
use App\Notifications\RiderEventNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReviewRiderSupportTicketController extends Controller
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
        $validated = $request->validate([
            'status' => ['required', Rule::in(['in_progress', 'resolved', 'closed'])],
            'resolution' => ['nullable', 'string', 'max:2000'],
        ]);
        if (in_array($validated['status'], ['resolved', 'closed'], true) && empty($validated['resolution'])) {
            return response()->json(['message' => 'Add a resolution note before closing this ticket.'], 422);
        }

        $ticket->update($validated);
        if (! empty($validated['resolution'])) {
            $ticket->messages()->create([
                'sender_id' => $request->user()->id,
                'body' => trim($validated['resolution']),
            ]);
            $ticket->touch();
            $ticket->rider->user->notify(new RiderEventNotification(
                'Support ticket updated',
                "Logistics updated support ticket: {$ticket->subject}.",
                'support_ticket'
            ));
        }

        return response()->json(['data' => $ticket->fresh()->load('messages')]);
    }
}
