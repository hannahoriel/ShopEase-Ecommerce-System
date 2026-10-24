<?php

namespace App\Http\Controllers\Api\Rider\User\Support;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Rider\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArchiveSupportTicketController extends Controller
{
    public function __invoke(Request $request, SupportTicket $ticket, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        abort_unless($ticket->rider_id === $rider->id, 404);
        $ticket->update(['archived_at' => now()]);

        return response()->json(['message' => 'Support ticket archived.']);
    }
}
