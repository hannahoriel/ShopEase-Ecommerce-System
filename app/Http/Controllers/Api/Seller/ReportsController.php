<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Seller\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ReportsController extends Controller
{
    public function page(Request $request)
    {
        $seller = $this->sellerFor($request->user());
        $filters = [
            'start_date' => null,
            'end_date' => null,
            'period' => 'month',
            'page' => 1,
            'per_page' => 7,
        ];

        return view('pages.seller.reports', [
            'report' => $this->reportData($seller, $filters),
            'reportDataUrl' => route('seller.reports.data'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'period' => ['nullable', Rule::in(['week', 'month', 'year'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', Rule::in([7, 10, 20])],
        ]);

        return response()->json($this->reportData(
            $this->sellerFor($request->user()),
            array_merge([
                'period' => 'month',
                'page' => 1,
                'per_page' => 7,
            ], $filters)
        ));
    }

    private function reportData(Seller $seller, array $filters): array
    {
        $orders = Order::query()
            ->where('seller_id', $seller->id)
            ->completed();

        if (! empty($filters['start_date'])) {
            $orders->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $orders->whereDate('created_at', '<=', $filters['end_date']);
        }

        $rangeStart = ! empty($filters['start_date'])
            ? Carbon::parse($filters['start_date'])->startOfDay()
            : null;
        $rangeEnd = ! empty($filters['end_date'])
            ? Carbon::parse($filters['end_date'])->endOfDay()
            : null;
        $totalSales = (float) (clone $orders)->sum('total');
        $commission = (float) (clone $orders)->sum('commission_amount');
        $orderCount = (clone $orders)->count();
        $profit = round($totalSales - $commission, 2);
        if ($rangeStart || $rangeEnd) {
            $effectiveStart = $rangeStart ?? $rangeEnd->copy()->startOfDay();
            $effectiveEnd = $rangeEnd ?? $rangeStart->copy()->endOfDay();
            $comparisonEnd = $effectiveStart->copy()->subDay()->endOfDay();
            $comparisonStart = $comparisonEnd
                ->copy()
                ->subDays((int) $effectiveStart->copy()->startOfDay()->diffInDays($effectiveEnd->copy()->startOfDay()))
                ->startOfDay();
        } else {
            $comparisonStart = Carbon::yesterday()->startOfDay();
            $comparisonEnd = Carbon::yesterday()->endOfDay();
        }
        $comparisonOrders = Order::query()
            ->where('seller_id', $seller->id)
            ->completed()
            ->whereBetween('created_at', [$comparisonStart, $comparisonEnd]);
        $comparisonSales = (float) (clone $comparisonOrders)->sum('total');
        $comparisonCommission = (float) (clone $comparisonOrders)->sum('commission_amount');
        $comparisonProfit = round($comparisonSales - $comparisonCommission, 2);
        $comparisonCount = (clone $comparisonOrders)->count();
        $comparisonMargin = $comparisonSales > 0
            ? round(($comparisonProfit / $comparisonSales) * 100, 1)
            : 0.0;

        $chart = $this->salesChart(
            $seller->id,
            $filters['period'] ?? 'month',
            $rangeStart,
            $rangeEnd
        );

        $page = (clone $orders)
            ->latest('created_at')
            ->paginate(
                $filters['per_page'] ?? 7,
                ['id', 'order_number', 'total', 'commission_amount', 'payment_method', 'created_at'],
                'page',
                $filters['page'] ?? 1
            );
        $page->setCollection($page->getCollection()->map(fn (Order $order): array => [
            'id' => $order->order_number ?: 'ORD-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
            'date' => $order->created_at?->toDateString(),
            'payment_method' => $order->payment_method ?: '—',
            'sales' => (float) $order->total,
            'commission' => (float) $order->commission_amount,
            'profit' => round((float) $order->total - (float) $order->commission_amount, 2),
        ]));

        return [
            'filters' => [
                'start_date' => $filters['start_date'] ?? null,
                'end_date' => $filters['end_date'] ?? null,
                'period' => $filters['period'] ?? 'month',
            ],
            'stats' => [
                'total_sales' => round($totalSales, 2),
                'total_orders' => $orderCount,
                'total_profit' => $profit,
                'gross_profit_margin' => $totalSales > 0
                    ? round(($profit / $totalSales) * 100, 1)
                    : 0.0,
                'changes' => [
                    'total_sales' => $this->percentChange($comparisonSales, $totalSales),
                    'total_orders' => $this->percentChange($comparisonCount, $orderCount),
                    'total_profit' => $this->percentChange($comparisonProfit, $profit),
                    'gross_profit_margin' => $this->percentChange($comparisonMargin, $totalSales > 0
                        ? round(($profit / $totalSales) * 100, 1)
                        : 0.0),
                ],
                'comparison_label' => $rangeStart || $rangeEnd ? 'from previous period' : 'from yesterday',
            ],
            'performance' => [
                'total_orders' => $orderCount,
                'gross_sales' => round($totalSales, 2),
                'admin_commission' => round($commission, 2),
                'profit' => $profit,
            ],
            'sales_chart' => $chart,
            'top_products' => $this->topProducts($seller->id, $rangeStart, $rangeEnd),
            'orders' => [
                'data' => $page->items(),
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'from' => $page->firstItem(),
                'to' => $page->lastItem(),
            ],
        ];
    }

    private function salesChart(
        int $sellerId,
        string $period,
        ?Carbon $rangeStart,
        ?Carbon $rangeEnd
    ): array {
        $now = Carbon::now();
        [$periodStart, $periodEnd, $periodPreviousStart, $periodPreviousEnd] = match ($period) {
            'week' => [
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek(),
                $now->copy()->startOfWeek()->subWeek(),
                $now->copy()->startOfWeek()->subDay()->endOfDay(),
            ],
            'year' => [
                $now->copy()->startOfYear(),
                $now->copy()->endOfYear(),
                $now->copy()->startOfYear()->subYear(),
                $now->copy()->startOfYear()->subDay()->endOfDay(),
            ],
            default => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
                $now->copy()->startOfMonth()->subMonthNoOverflow(),
                $now->copy()->startOfMonth()->subDay()->endOfDay(),
            ],
        };

        if ($rangeStart || $rangeEnd) {
            $currentStart = $rangeStart ?? $periodStart;
            $currentEnd = $rangeEnd ?? $periodEnd;
            $days = (int) $currentStart->copy()->startOfDay()->diffInDays($currentEnd->copy()->startOfDay()) + 1;
            $previousEnd = $currentStart->copy()->subDay()->endOfDay();
            $previousStart = $previousEnd->copy()->subDays($days - 1)->startOfDay();
        } else {
            [$currentStart, $currentEnd, $previousStart, $previousEnd] = [
                $periodStart,
                $periodEnd,
                $periodPreviousStart,
                $periodPreviousEnd,
            ];
            $days = (int) $currentStart->copy()->startOfDay()->diffInDays($currentEnd->copy()->startOfDay()) + 1;
        }

        $bucketCount = match ($period) {
            'week' => min(7, $days),
            'year' => min(6, $days),
            default => min(7, $days),
        };
        $currentSales = $this->salesByDate($sellerId, $currentStart, $currentEnd);
        $previousSales = $this->salesByDate($sellerId, $previousStart, $previousEnd);
        $labels = [];
        $current = [];
        $previous = [];

        for ($index = 0; $index < $bucketCount; $index++) {
            $fromOffset = (int) floor($index * $days / $bucketCount);
            $toOffset = (int) floor(($index + 1) * $days / $bucketCount) - 1;
            $bucketStart = $currentStart->copy()->addDays($fromOffset)->startOfDay();
            $bucketEnd = $currentStart->copy()->addDays(max($fromOffset, $toOffset))->endOfDay();
            $previousBucketStart = $previousStart->copy()->addDays($fromOffset)->startOfDay();
            $previousBucketEnd = $previousStart->copy()->addDays(max($fromOffset, $toOffset))->endOfDay();

            $labels[] = match ($period) {
                'week' => $bucketStart->format('D'),
                'year' => $bucketStart->format('M'),
                default => $bucketStart->format('M j'),
            };
            $current[] = $this->sumSalesBetween($currentSales, $bucketStart, $bucketEnd);
            $previous[] = $this->sumSalesBetween($previousSales, $previousBucketStart, $previousBucketEnd);
        }

        return [
            'labels' => $labels,
            'current' => $current,
            'previous' => $previous,
        ];
    }

    private function salesByDate(int $sellerId, Carbon $start, Carbon $end): array
    {
        return DB::table('orders')
            ->where('seller_id', $sellerId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as sale_date, SUM(total) as sales')
            ->groupBy('sale_date')
            ->pluck('sales', 'sale_date')
            ->map(fn ($sales): float => (float) $sales)
            ->all();
    }

    private function sumSalesBetween(array $sales, Carbon $start, Carbon $end): float
    {
        $sum = 0.0;
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $sum += $sales[$date->toDateString()] ?? 0;
        }

        return round($sum, 2);
    }

    private function percentChange(float|int $previous, float|int $current): array
    {
        $change = $previous == 0
            ? ($current > 0 ? 100 : 0)
            : (($current - $previous) / abs($previous)) * 100;

        return [
            'value' => number_format(abs($change), 0) . '%',
            'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'flat'),
        ];
    }

    private function topProducts(int $sellerId, ?Carbon $start, ?Carbon $end): array
    {
        $query = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', function ($join) use ($sellerId): void {
                $join->on('products.id', '=', 'order_items.product_id')
                    ->where('products.seller_id', '=', $sellerId);
            })
            ->where('orders.seller_id', $sellerId)
            ->where('orders.status', 'completed')
            ->selectRaw('order_items.product_id, order_items.product_name, SUM(order_items.quantity) as quantity_sold, MAX(products.photos) as photos')
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('quantity_sold')
            ->limit(6);

        if ($start) {
            $query->where('orders.created_at', '>=', $start);
        }
        if ($end) {
            $query->where('orders.created_at', '<=', $end);
        }

        return $query->get()->map(function ($product): array {
            $photos = is_string($product->photos)
                ? json_decode($product->photos, true)
                : [];
            $photo = is_array($photos) ? ($photos[0] ?? null) : null;
            if (is_string($photo) && ! preg_match('/^https?:\/\//i', $photo)) {
                $photo = Storage::disk('public')->url(preg_replace('/^storage\//', '', ltrim($photo, '/')));
            }

            return [
                'name' => $product->product_name,
                'quantity' => (int) $product->quantity_sold,
                'photo' => $photo,
            ];
        })->all();
    }

    private function sellerFor(User $user): Seller
    {
        return Seller::query()->where('user_id', $user->id)->firstOrFail();
    }
}
