<?php

namespace App\Http\Controllers\Api\Rider\User\Recipients;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreRecipientController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:1000'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        return response()->json(['data' => $rider->recipients()->create($validated)], 201);
    }
}
