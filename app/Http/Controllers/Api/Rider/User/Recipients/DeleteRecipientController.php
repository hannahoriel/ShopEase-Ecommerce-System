<?php

namespace App\Http\Controllers\Api\Rider\User\Recipients;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Rider\Recipient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteRecipientController extends Controller
{
    public function __invoke(Request $request, Recipient $recipient, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        abort_unless($recipient->rider_id === $rider->id, 404);
        $recipient->delete();

        return response()->json(['message' => 'Recipient deleted.']);
    }
}
