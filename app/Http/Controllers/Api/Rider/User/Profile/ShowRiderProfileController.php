<?php

namespace App\Http\Controllers\Api\Rider\User\Profile;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowRiderProfileController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        return response()->json(['data' => $operations->profile($operations->rider($request->user()))]);
    }
}
