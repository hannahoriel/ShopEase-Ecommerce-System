<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\Announcement;
use App\Models\Admin\Order;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function apiIndex(Request $request): JsonResponse
    {
        return response()->json($this->dashboardData($request->user()));
    }

    protected function dashboardData(User $user): array
    {
        $seller = Seller::where('user_id', $user->id)->firstOrFail();
        $orders = Order::where('seller_id', $seller->id);
        $completedOrders = (clone $orders)->completed();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $totalSales = (float) $completedOrders->sum('total');
        $completedCount = (clone $completedOrders)->count();
        $todaySales = (float) (clone $completedOrders)->whereDate('created_at', $today)->sum('total');
        $yesterdaySales = (float) (clone $completedOrders)->whereDate('created_at', $yesterday)->sum('total');
        $todayOrders = (clone $orders)->whereDate('created_at', $today)->count();
        $yesterdayOrders = (clone $orders)->whereDate('created_at', $yesterday)->count();
        $todayPendingOrders = (clone $orders)->where('status', 'pending')->whereDate('created_at', $today)->count();
        $yesterdayPendingOrders = (clone $orders)->where('status', 'pending')->whereDate('created_at', $yesterday)->count();
        $lowStockQuery = $seller->products()
            ->where('is_archived', false)
            ->where('stock_quantity', '<=', 5);
        $lowStockProducts = (clone $lowStockQuery)->count();
        $statusGroups = [
            'new_orders' => ['pending', 'new'],
            'preparing' => ['preparing'],
            'to_ship' => ['to_ship', 'ready_to_ship'],
            'in_transit' => ['in_transit', 'out_for_delivery'],
            'delivered' => ['delivered', 'completed'],
        ];

        return [
            'seller' => [
                'id' => $seller->id,
                'store_name' => $seller->store_name ?: $seller->business_name ?: $user->name,
                'registration_status' => $seller->registration_status,
            ],
            'stats' => [
                'total_sales' => $totalSales,
                'total_orders' => (clone $orders)->count(),
                'pending_orders' => (clone $orders)->where('status', 'pending')->count(),
                'low_stock_products' => $lowStockProducts,
                'changes' => [
                    'total_sales' => $this->percentChange($yesterdaySales, $todaySales),
                    'total_orders' => $this->percentChange($yesterdayOrders, $todayOrders),
                    'pending_orders' => $this->percentChange($yesterdayPendingOrders, $todayPendingOrders),
                    'low_stock_products' => ['value' => '—', 'direction' => 'flat'],
                ],
            ],
            'sales_summary' => [
                'gross_sales' => $totalSales,
                'completed_orders' => $completedCount,
                'average_order_value' => $completedCount > 0 ? round($totalSales / $completedCount, 2) : 0.0,
                'today_sales' => $todaySales,
            ],
            'orders_by_status' => (clone $orders)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->map(fn ($total) => (int) $total)
                ->all(),
            'orders_by_status_period' => $this->ordersByStatusPeriod($seller->id, $statusGroups),
            'sales_chart' => $this->salesChart($seller->id),
            'sales_overview' => $this->salesOverview($seller->id),
            'recent_orders' => (clone $orders)
                ->with('buyer:id,name')
                ->latest()
                ->limit(5)
                ->get(['id', 'buyer_id', 'total', 'status', 'order_number', 'payment_method', 'created_at'])
                ->map(fn (Order $order) => [
                    'id' => $order->id,
                    'order_number' => $order->order_number ?: 'ORD-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
                    'buyer_id' => $order->buyer_id,
                    'buyer_name' => $order->buyer?->name ?? 'Buyer',
                    'total' => (float) $order->total,
                    'status' => $order->status,
                    'payment_method' => $order->payment_method,
                    'created_at' => $order->created_at?->toISOString(),
                ])
                ->values()
                ->all(),
            'low_stock_products' => (clone $lowStockQuery)
                ->orderBy('stock_quantity')
                ->limit(5)
                ->get(['id', 'name', 'photos', 'stock_quantity'])
                ->map(fn (Product $product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'photo' => $this->productPhotoUrl($product->photos[0] ?? null),
                    'stock' => $product->stock_quantity,
                ])
                ->values()
                ->all(),
            'announcement' => Announcement::query()
                ->where('is_active', true)
                ->latest()
                ->first(['title', 'body', 'badge_label']),
        ];
    }

    private function percentChange(float|int $previous, float|int $current): array
    {
        if ($previous == 0) {
            $percent = $current > 0 ? 100 : 0;
        } else {
            $percent = (($current - $previous) / $previous) * 100;
        }

        return [
            'value' => number_format(abs($percent), 0) . '%',
            'direction' => $percent >= 0 ? 'up' : 'down',
        ];
    }

    private function ordersByStatusPeriod(int $sellerId, array $statusGroups): array
    {
        $now = Carbon::now();

        return [
            'week' => $this->periodStatusCounts(
                $sellerId,
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek(),
                $statusGroups
            ),
            'month' => $this->periodStatusCounts(
                $sellerId,
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
                $statusGroups
            ),
            'year' => $this->periodStatusCounts(
                $sellerId,
                $now->copy()->startOfYear(),
                $now->copy()->endOfYear(),
                $statusGroups
            ),
        ];
    }

    private function periodStatusCounts(int $sellerId, Carbon $start, Carbon $end, array $statusGroups): array
    {
        $counts = Order::where('seller_id', $sellerId)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect($statusGroups)
            ->map(fn (array $statuses) => collect($statuses)->sum(fn (string $status) => (int) $counts->get($status, 0)))
            ->all();
    }

    private function salesChart(int $sellerId): array
    {
        $now = Carbon::now();

        $weekStart = $now->copy()->startOfWeek();
        $monthStart = $now->copy()->startOfMonth();
        $yearStart = $now->copy()->startOfYear();

        $weekCurrent = $this->completedSalesByDate($sellerId, $weekStart, $now->copy()->endOfWeek());
        $weekPreviousStart = $weekStart->copy()->subWeek();
        $weekPrevious = $this->completedSalesByDate($sellerId, $weekPreviousStart, $weekStart->copy()->subDay()->endOfDay());

        $monthCurrent = $this->completedSalesByDate($sellerId, $monthStart, $now->copy()->endOfMonth());
        $monthPreviousStart = $monthStart->copy()->subMonthNoOverflow();
        $monthPrevious = $this->completedSalesByDate($sellerId, $monthPreviousStart, $monthStart->copy()->subDay()->endOfDay());

        $yearCurrent = $this->completedSalesByDate($sellerId, $yearStart, $now->copy()->endOfYear());
        $yearPreviousStart = $yearStart->copy()->subYear();
        $yearPrevious = $this->completedSalesByDate($sellerId, $yearPreviousStart, $yearStart->copy()->subDay()->endOfDay());

        $weekLabels = [];
        $weekCurrentValues = [];
        $weekPreviousValues = [];
        foreach (CarbonPeriod::create($weekStart, $weekStart->copy()->addDays(6)) as $date) {
            $weekLabels[] = $date->format('D');
            $weekCurrentValues[] = $weekCurrent[$date->toDateString()] ?? 0;
            $weekPreviousValues[] = $weekPrevious[$date->copy()->subWeek()->toDateString()] ?? 0;
        }

        [$monthLabels, $monthCurrentValues] = $this->monthlySalesBuckets($monthCurrent, $monthStart);
        [, $monthPreviousValues] = $this->monthlySalesBuckets($monthPrevious, $monthPreviousStart);

        $yearLabels = [];
        $yearCurrentValues = [];
        $yearPreviousValues = [];
        for ($month = 1; $month <= 11; $month += 2) {
            $currentPeriodStart = $yearStart->copy()->month($month)->startOfMonth();
            $previousPeriodStart = $yearPreviousStart->copy()->month($month)->startOfMonth();
            $yearLabels[] = $currentPeriodStart->format('M');
            $yearCurrentValues[] = $this->sumSalesBetweenDates(
                $yearCurrent,
                $currentPeriodStart,
                $currentPeriodStart->copy()->addMonth()->endOfMonth()
            );
            $yearPreviousValues[] = $this->sumSalesBetweenDates(
                $yearPrevious,
                $previousPeriodStart,
                $previousPeriodStart->copy()->addMonth()->endOfMonth()
            );
        }

        return [
            'week' => ['labels' => $weekLabels, 'current' => $weekCurrentValues, 'previous' => $weekPreviousValues],
            'month' => ['labels' => $monthLabels, 'current' => $monthCurrentValues, 'previous' => $monthPreviousValues],
            'year' => ['labels' => $yearLabels, 'current' => $yearCurrentValues, 'previous' => $yearPreviousValues],
        ];
    }

    private function completedSalesByDate(int $sellerId, Carbon $start, Carbon $end): array
    {
        return Order::where('seller_id', $sellerId)
            ->completed()
            ->whereBetween('created_at', [$start, $end])
            ->get(['total', 'created_at'])
            ->groupBy(fn (Order $order) => $order->created_at->toDateString())
            ->map(fn ($orders) => round((float) $orders->sum('total'), 2))
            ->all();
    }

    private function monthlySalesBuckets(array $sales, Carbon $monthStart): array
    {
        $daysInMonth = $monthStart->daysInMonth;
        $labels = [];
        $values = [];
        $bucketStarts = [1, 6, 11, 16, 21, 26, $daysInMonth];

        foreach ($bucketStarts as $index => $day) {
            $bucketStart = $monthStart->copy()->day($day)->startOfDay();
            $bucketEndDay = match ($index) {
                5 => $daysInMonth - 1,
                6 => $daysInMonth,
                default => min($day + 4, $daysInMonth - 1),
            };
            $bucketEnd = $monthStart->copy()->day(max($day, $bucketEndDay))->endOfDay();

            $labels[] = $bucketStart->format('M j');
            $values[] = $this->sumSalesBetweenDates($sales, $bucketStart, $bucketEnd);
        }

        return [$labels, $values];
    }

    private function sumSalesBetweenDates(array $sales, Carbon $start, Carbon $end): float
    {
        return round((float) collect(CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay()))
            ->sum(fn (Carbon $date) => $sales[$date->toDateString()] ?? 0), 2);
    }

    private function productPhotoUrl(?string $photo): ?string
    {
        if (!$photo) {
            return null;
        }

        if (str_starts_with($photo, 'data:') || str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) {
            return $photo;
        }

        return Storage::disk('public')->url(preg_replace('/^storage\//', '', ltrim($photo, '/')));
    }

    private function salesOverview(int $sellerId): array
    {
        $start = Carbon::today()->subDays(6);
        $sales = Order::where('seller_id', $sellerId)
            ->completed()
            ->whereDate('created_at', '>=', $start)
            ->get(['total', 'created_at'])
            ->groupBy(fn (Order $order) => $order->created_at->toDateString());

        return collect(range(0, 6))->map(function (int $offset) use ($start, $sales): array {
            $date = $start->copy()->addDays($offset);

            return [
                'date' => $date->toDateString(),
                'label' => $date->format('M j'),
                'sales' => round((float) $sales->get($date->toDateString(), collect())->sum('total'), 2),
            ];
        })->all();
    }
}
