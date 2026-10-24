<?php

namespace App\Http\Controllers\Api\Rider\Homepage\Calculator;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreCalculationController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $validated = $request->validate([
            'expression' => ['required', 'string', 'max:255', 'regex:/^[0-9.+\\-*\\/×÷ ]+$/u'],
            'result' => ['required', 'string', 'max:32'],
            'category' => ['required', Rule::in(['COD Change', 'Tally', 'General'])],
        ]);

        $calculation = $operations->rider($request->user())
            ->calculations()
            ->create($validated);

        return response()->json([
            'data' => $calculation->only(['id', 'expression', 'result', 'category', 'created_at']),
        ], 201);
    }
}
