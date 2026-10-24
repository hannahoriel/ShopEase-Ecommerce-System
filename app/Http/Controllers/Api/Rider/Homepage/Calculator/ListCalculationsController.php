<?php

namespace App\Http\Controllers\Api\Rider\Homepage\Calculator;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListCalculationsController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $calculations = $operations->rider($request->user())
            ->calculations()
            ->latest()
            ->limit(100)
            ->get(['id', 'expression', 'result', 'category', 'created_at']);

        return response()->json(['data' => $calculations]);
    }
}
