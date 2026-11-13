<?php

namespace App\Http\Controllers\Api\Rider\Homepage\MoneyReport;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Logistics\LogisticsDeliveryHub;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $hubName = trim((string) $rider->municipal_hub);
        $hub = LogisticsDeliveryHub::query()->where('logistics_branch_id', $rider->logistics_branch_id)
            ->where('status', 'active')->where('hub_name', $hubName)->first();
        if (! $hub) {
            throw ValidationException::withMessages([
                'hub' => ['Your active delivery hub assignment is missing. Contact your logistics manager.'],
            ]);
        }

        $receiptPath = isset($validated['receipt'])
            ? $operations->storePrivatePhoto('rider-remittances', $validated['receipt'])
            : null;
        try {
            [$remittance, $reconciled] = DB::transaction(function () use ($rider, $hub, $validated, $receiptPath) {
                $lockedRider = $rider->newQuery()->lockForUpdate()->findOrFail($rider->id);
                $collections = $lockedRider->codCollections()->whereDoesntHave(
                    'remittances', fn ($query) => $query->where('status', '<>', 'rejected'),
                )
                    ->lockForUpdate()->get();
                $due = round((float) $collections->sum('amount'), 2);
                $submitted = round((float) $validated['amount'], 2);
                if ($due <= 0 || abs($due - $submitted) > 0.009) {
                    throw ValidationException::withMessages([
                        'amount' => ['Remit all available COD collections. The current amount due is ₱'.number_format($due, 2).'.'],
                    ]);
                }

                // Reconcile an exact-amount legacy row left unlinked by an older app.
                $legacyPending = $lockedRider->remittances()->where('status', 'pending')
                    ->whereDoesntHave('collections')->orderBy('id')->lockForUpdate()->get();
                $remittance = $legacyPending->first(fn ($candidate) => abs((float) $candidate->amount - $due) < 0.009);
                $reconciled = $remittance !== null;
                if ($remittance) {
                    $remittance->update([
                        'delivery_hub_id' => $hub->id,
                        'method' => $validated['method'],
                        'reference_number' => $validated['reference_number'] ?? null,
                        'receipt_path' => $receiptPath ?: $remittance->receipt_path,
                    ]);
                } else {
                    $collectionIds = $collections->modelKeys();
                    $collectionIds = array_map('intval', $collectionIds);
                    sort($collectionIds);
                    $rejected = $lockedRider->remittances()
                        ->where('status', 'rejected')
                        ->with('collections')
                        ->lockForUpdate()
                        ->get()
                        ->first(function ($candidate) use ($collectionIds): bool {
                            $previousCollectionIds = array_map('intval', $candidate->collections->modelKeys());
                            sort($previousCollectionIds);

                            return $previousCollectionIds === $collectionIds;
                        });

                    if ($rejected) {
                        $rejected->update([
                            'delivery_hub_id' => $hub->id,
                            'amount' => number_format($due, 2, '.', ''),
                            'actual_amount' => null,
                            'discrepancy_amount' => null,
                            'method' => $validated['method'],
                            'reference_number' => $validated['reference_number'] ?? null,
                            'receipt_path' => $receiptPath,
                            'status' => 'pending',
                            'reviewed_by' => null,
                            'reviewed_at' => null,
                            'review_notes' => null,
                            'cash_in_at' => null,
                            'cash_in_by' => null,
                        ]);
                        $remittance = $rejected;
                    } else {
                        $remittance = $lockedRider->remittances()->create([
                            'delivery_hub_id' => $hub->id,
                            'amount' => number_format($due, 2, '.', ''),
                            'method' => $validated['method'],
                            'reference_number' => $validated['reference_number'] ?? null,
                            'receipt_path' => $receiptPath,
                            'status' => 'pending',
                        ]);
                    }
                }
                $remittance->collections()->sync($collections->modelKeys());
                return [$remittance, $reconciled];
            });
        } catch (\Throwable $exception) {
            if ($receiptPath) {
                Storage::disk('local')->delete($receiptPath);
            }
            throw $exception;
        }

        return response()->json([
            'data' => $remittance,
            'message' => $reconciled
                ? 'The existing pending remittance was matched to these COD collections and sent to the hub for review.'
                : 'COD remittance submitted to the delivery hub for review.',
        ], $reconciled ? 200 : 201);
    }
}
