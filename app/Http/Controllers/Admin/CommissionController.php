<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\CommissionTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function page()
    {
        return view('pages.admin.commission', $this->commissionData());
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        $query = $this->earnedTransactions();

        if (! empty($validated['search'])) {
            $search = trim($validated['search']);
            $query->where(function (Builder $builder) use ($search): void {
                $builder->whereHas('order', fn (Builder $orders) => $orders
                    ->where('order_number', 'like', "%{$search}%"))
                    ->orWhereHas('seller', fn (Builder $sellers) => $sellers
                        ->where('store_name', 'like', "%{$search}%")
                        ->orWhereHas('user', fn (Builder $users) => $users
                            ->where('name', 'like', "%{$search}%")));
            });
        }

        if (! empty($validated['from'])) {
            $query->whereDate('earned_at', '>=', $validated['from']);
        }

        if (! empty($validated['to'])) {
            $query->whereDate('earned_at', '<=', $validated['to']);
        }

        $transactions = $query->paginate($validated['per_page'] ?? 10);

        return response()->json([
            'data' => $transactions->getCollection()->map(fn (CommissionTransaction $item): array => $this->serialize($item)),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
            'stats' => $this->stats(),
        ]);
    }

    private function commissionData(): array
    {
        $rows = $this->earnedTransactions()
            ->get()
            ->map(fn (CommissionTransaction $item): array => $this->serialize($item));

        return [
            'commissionRows' => $rows,
            'commissionStats' => $this->stats(),
            'commissionTotalEntries' => $rows->count(),
        ];
    }

    private function earnedTransactions(): Builder
    {
        return CommissionTransaction::query()
            ->where('status', 'earned')
            ->with(['order:id,order_number', 'seller:id,store_name,user_id', 'seller.user:id,name'])
            ->orderByDesc('earned_at')
            ->orderByDesc('id');
    }

    private function stats(): array
    {
        $aggregate = CommissionTransaction::query()
            ->where('status', 'earned')
            ->selectRaw('COALESCE(SUM(order_amount), 0) as order_total')
            ->selectRaw('COALESCE(SUM(commission_amount), 0) as commission_total')
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('COUNT(DISTINCT seller_id) as seller_count')
            ->first();

        $orderTotal = (float) $aggregate->order_total;
        $commissionTotal = (float) $aggregate->commission_total;

        return [
            'rate' => CommissionTransaction::DEFAULT_RATE,
            'total_commission' => $commissionTotal,
            'total_orders' => (int) $aggregate->order_count,
            'total_sellers_charged' => (int) $aggregate->seller_count,
        ];
    }

    private function serialize(CommissionTransaction $item): array
    {
        $earnedAt = $item->earned_at;

        return [
            'date' => $earnedAt?->toDateString() ?? '',
            'date_label' => $earnedAt?->format('F d, Y') ?? '',
            'time' => $earnedAt?->format('g:i A') ?? '',
            'order_id' => $item->order?->order_number
                ? '#'.$item->order->order_number
                : '#ORD-'.$item->order_id,
            'seller' => $item->seller?->store_name ?: 'Seller',
            'seller_owner' => $item->seller?->user?->name ?: '—',
            'order_amount' => (float) $item->order_amount,
            'commission' => (float) $item->commission_amount,
        ];
    }
}
