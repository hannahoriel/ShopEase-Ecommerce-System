<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Seller\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $dashboard = $this->dashboardData($request->user());

        return view('pages.seller.dashboard', compact('dashboard'));
    }

    public function apiIndex(Request $request): JsonResponse
    {
        return response()->json($this->dashboardData($request->user()));
    }

    private function dashboardData(User $user): array
    {
        $seller = Seller::where('user_id', $user->id)->firstOrFail();
        $orders = Order::where('seller_id', $seller->id);
        $completedOrders = (clone $orders)->completed();
        $today = Carbon::today();

        $totalSales = (float) $completedOrders->sum('total');
        $completedCount = (clone $completedOrders)->count();

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
                'low_stock_products' => $seller->products()->where('stock_quantity', '<=', 5)->count(),
            ],
            'sales_summary' => [
                'gross_sales' => $totalSales,
                'completed_orders' => $completedCount,
                'average_order_value' => $completedCount > 0 ? round($totalSales / $completedCount, 2) : 0.0,
                'today_sales' => (float) (clone $completedOrders)->whereDate('created_at', $today)->sum('total'),
            ],
            'orders_by_status' => (clone $orders)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->map(fn ($total) => (int) $total)
                ->all(),
            'sales_overview' => $this->salesOverview($seller->id),
            'recent_orders' => (clone $orders)
                ->latest()
                ->limit(5)
                ->get(['id', 'buyer_id', 'total', 'status', 'created_at'])
                ->map(fn (Order $order) => [
                    'id' => $order->id,
                    'buyer_id' => $order->buyer_id,
                    'total' => (float) $order->total,
                    'status' => $order->status,
                    'created_at' => $order->created_at?->toISOString(),
                ])
                ->values()
                ->all(),
        ];
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
