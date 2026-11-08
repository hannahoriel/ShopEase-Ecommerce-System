<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller\Seller;
use App\Services\SellerWalletService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    public function index(Request $request, SellerWalletService $wallet): JsonResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', 'string', Rule::in(['all', 'order_income', 'refund', 'return_shipping', 'voucher_discount'])],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        // datetime-local values are local wall time in the app timezone.
        if (! empty($filters['from'])) {
            $filters['from'] = Carbon::parse($filters['from'])->startOfMinute()->toDateTimeString();
        }
        if (! empty($filters['to'])) {
            $filters['to'] = Carbon::parse($filters['to'])->endOfMinute()->toDateTimeString();
        }

        $seller = Seller::query()->where('user_id', $request->user()->id)->firstOrFail();
        $wallet->sync($seller);
        $summary = $wallet->summary($seller, $filters);
        $transactions = $summary['transactions'];

        return response()->json([
            'summary' => [
                'balance' => $summary['balance'],
                'income' => $summary['income'],
                'refunds' => $summary['refunds'],
                'shipping' => $summary['shipping'],
                'vouchers' => $summary['vouchers'],
            ],
            'transactions' => [
                'data' => $transactions->getCollection()->map(fn ($row) => [
                    'id' => $row->id,
                    'entry_type' => $row->entry_type,
                    'reference' => $row->reference,
                    'description' => $row->description,
                    'amount' => (float) $row->amount,
                    'occurred_at' => $row->occurred_at,
                ])->values(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'from' => $transactions->firstItem(),
                'to' => $transactions->lastItem(),
            ],
            'filters' => [
                'search' => $filters['search'] ?? '',
                'type' => $filters['type'] ?? 'all',
                'from' => $request->query('from', ''),
                'to' => $request->query('to', ''),
            ],
        ]);
    }
}
