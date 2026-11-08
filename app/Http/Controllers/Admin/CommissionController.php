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
            $search = ltrim(trim($validated['search']), '#');
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

    /** Admin-only, read-only order details for the commission modal. */
    public function show(CommissionTransaction $commissionTransaction): JsonResponse
    {
        abort_unless($commissionTransaction->status === 'earned', 404);

        $commissionTransaction->load([
            'seller:id,store_name,user_id',
            'order.buyer:id,name',
            'order.items',
            'order.shipment.scans',
            'order.statusHistory',
        ]);
        $order = $commissionTransaction->order;
        abort_unless($order, 404);
        $shipment = $order->shipment;

        return response()->json([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'ordered_at' => $order->created_at?->toIso8601String(),
            'delivery_name' => $order->delivery_name ?: $order->buyer?->name,
            'delivery_phone' => $order->delivery_phone,
            'delivery_address' => $order->delivery_address,
            'payment_method' => $order->payment_method,
            'customer_notes' => $order->customer_notes ?? null,
            'total' => (float) $order->total,
            'shipping_fee' => (float) $order->shipping_fee,
            'voucher_discount' => (float) $order->voucher_discount,
            'items' => $order->items->map(fn ($item) => [
                'name' => $item->product_name,
                'is_fragile' => (bool) ($item->is_fragile || $item->product?->is_fragile),
                'variation' => $item->variation,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
            ]),
            'shipment' => $shipment ? [
                'estimated_delivery' => $shipment->estimated_delivery?->toDateString(),
                'tracking_number' => $shipment->tracking_number,
                'current_location' => $shipment->current_location,
                'scans' => $shipment->scans->map(fn ($scan) => [
                    'location' => $scan->location ?? $scan->scan_location ?? '',
                    'status' => $scan->status ?? $scan->scan_type ?? 'Parcel updated',
                    'scanned_at' => $scan->scanned_at,
                ]),
            ] : null,
            'commission' => [
                'order_amount' => (float) $commissionTransaction->order_amount,
                'rate' => (float) $commissionTransaction->commission_rate,
                'amount' => (float) $commissionTransaction->commission_amount,
                'earned_at' => $commissionTransaction->earned_at?->toIso8601String(),
                'seller_after_commission' => (float) $commissionTransaction->order_amount - (float) $commissionTransaction->commission_amount,
            ],
        ]);
    }

    private function commissionData(): array
    {
        return [
            // The browser loads the searchable/paginated ledger from index().
            // Keep the page response limited to summary data instead of loading
            // every commission row twice on the initial page request.
            'commissionRows' => collect(),
            'commissionStats' => $this->stats(),
            'commissionTotalEntries' => CommissionTransaction::query()->where('status', 'earned')->count(),
        ];
    }

    private function earnedTransactions(): Builder
    {
        return CommissionTransaction::query()
            ->where('status', 'earned')
            ->with(['order:id,order_number', 'seller:id,store_name,user_id,profile_photo_path', 'seller.user:id,name,profile_photo_path'])
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
            'transaction_id' => $item->id,
            'date' => $earnedAt?->toDateString() ?? '',
            'date_label' => $earnedAt?->format('F d, Y') ?? '',
            'time' => $earnedAt?->format('g:i A') ?? '',
            'order_id' => $item->order?->order_number
                ? '#'.$item->order->order_number
                : '#ORD-'.$item->order_id,
            'seller' => $item->seller?->store_name ?: 'Seller',
            'seller_owner' => $item->seller?->user?->name ?: '—',
            'seller_photo_url' => $item->seller?->user?->profile_photo_path
                ? \Illuminate\Support\Facades\Storage::disk('public')->url($item->seller->user->profile_photo_path)
                : ($item->seller?->profile_photo_path
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($item->seller->profile_photo_path)
                    : null),
            'order_amount' => (float) $item->order_amount,
            'commission' => (float) $item->commission_amount,
        ];
    }
}
