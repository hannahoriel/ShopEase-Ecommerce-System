<?php

namespace App\Http\Controllers\Api\Rider\User\Recipients;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListRecipientsController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());

        return response()->json(['data' => $rider->recipients()->latest()->get()]);
    }
}
