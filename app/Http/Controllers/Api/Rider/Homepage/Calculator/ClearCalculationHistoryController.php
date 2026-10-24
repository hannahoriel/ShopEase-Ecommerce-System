<?php

namespace App\Http\Controllers\Api\Rider\Homepage\Calculator;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClearCalculationHistoryController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $operations->rider($request->user())->calculations()->delete();

        return response()->json(['data' => ['cleared' => true]]);
    }
}
