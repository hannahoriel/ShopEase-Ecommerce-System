<?php

namespace App\Http\Controllers\Api\Rider\Homepage\MoneyReport;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreRemittanceController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'method' => ['required', Rule::in(['gcash', 'hub_turn_in', 'bank_transfer'])],
            'reference_number' => ['nullable', 'string', 'max:120'],
            'receipt' => ['nullable', 'image', 'max:10240'],
        ]);
        if (in_array($validated['method'], ['gcash', 'bank_transfer'], true)
            && empty($validated['reference_number'])) {
            throw ValidationException::withMessages([
                'reference_number' => ['A transfer reference is required for this remittance method.'],
            ]);
        }

        $collected = (float) $rider->codCollections()->sum('amount');
        $held = (float) $rider->remittances()->whereIn('status', ['pending', 'verified'])->sum('amount');
        if ((float) $validated['amount'] > max(0, $collected - $held)) {
            throw ValidationException::withMessages([
                'amount' => ['The remittance amount exceeds your available COD balance.'],
            ]);
        }

        $receiptPath = isset($validated['receipt'])
            ? $operations->storePrivatePhoto('rider-remittances', $validated['receipt'])
            : null;
        try {
            $remittance = $rider->remittances()->create([
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'receipt_path' => $receiptPath,
                'status' => 'pending',
            ]);
        } catch (\Throwable $exception) {
            if ($receiptPath) {
                Storage::disk('local')->delete($receiptPath);
            }
            throw $exception;
        }

        return response()->json(['data' => $remittance], 201);
    }
}
