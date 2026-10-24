<?php

namespace App\Http\Controllers\Api\Rider\User\Recipients;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Rider\Recipient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateRecipientController extends Controller
{
    public function __invoke(Request $request, Recipient $recipient, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        abort_unless($recipient->rider_id === $rider->id, 404);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:1000'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);
        $recipient->update($validated);

        return response()->json(['data' => $recipient->fresh()]);
    }
}
