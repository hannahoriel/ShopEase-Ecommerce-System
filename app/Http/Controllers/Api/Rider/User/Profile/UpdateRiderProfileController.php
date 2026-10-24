<?php

namespace App\Http\Controllers\Api\Rider\User\Profile;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateRiderProfileController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $rider->update(['contact_no' => $validated['phone']]);
        $request->user()->update(['name' => $validated['name']]);

        return response()->json(['data' => $operations->profile($rider->fresh()->load(['user', 'logisticsBranch.logistics']))]);
    }
}
