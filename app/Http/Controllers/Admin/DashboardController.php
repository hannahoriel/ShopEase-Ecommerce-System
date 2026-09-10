<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Announcement;
use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Admin\Registration;
use App\Models\Seller\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // ---------------------------------------------------------------
        // STAT CARDS
        // ---------------------------------------------------------------
        $pendingRegistrationsToday = Registration::pending()->whereDate('created_at', $today)->count();
        $pendingRegistrationsYesterday = Registration::pending()->whereDate('created_at', $yesterday)->count();

        $activeUsersToday = User::active()->whereDate('approved_at', $today)->count();
        $activeUsersYesterday = User::active()->whereDate('approved_at', $yesterday)->count();

        $activeSellersToday = Seller::active()->whereDate('updated_at', $today)->count();
        $activeSellersYesterday = Seller::active()->whereDate('updated_at', $yesterday)->count();

        $commissionToday = Order::completed()->whereDate('created_at', $today)->sum('commission_amount');
        $commissionYesterday = Order::completed()->whereDate('created_at', $yesterday)->sum('commission_amount');

        $stats = [
            [
                'icon' => 'pending.png',
                'value' => number_format(Registration::pending()->count()),
                'label' => 'Pending Registrations',
                'change' => $this->percentChange($pendingRegistrationsYesterday, $pendingRegistrationsToday),
            ],
            [
                'icon' => 'active-users.png',
                'value' => number_format(User::active()->count()),
                'label' => 'Active Users',
                'change' => $this->percentChange($activeUsersYesterday, $activeUsersToday),
            ],
            [
                'icon' => 'active-sellers.png',
                'value' => number_format(Seller::active()->count()),
                'label' => 'Active Sellers',
                'change' => $this->percentChange($activeSellersYesterday, $activeSellersToday),
            ],
            [
                'icon' => 'total-commision.png',
                'value' => '₱' . number_format(Order::completed()->sum('commission_amount'), 2),
                'label' => 'Total Commission',
                'change' => $this->percentChange($commissionYesterday, $commissionToday),
            ],
        ];

        // ---------------------------------------------------------------
        // SALES SUMMARY
        // ---------------------------------------------------------------
        $grossSales = Order::completed()->sum('total');
        $totalOrders = Order::count();
        $completedOrders = Order::completed()->count();
        $refunds = Order::refunded()->count();
        $averageOrderValue = $completedOrders > 0 ? $grossSales / $completedOrders : 0;

        $salesSummary = [
            'gross_sales' => '₱' . number_format($grossSales, 2),
            'total_orders' => number_format($totalOrders),
            'average_order_value' => '₱' . number_format($averageOrderValue, 2),
            'completed_orders' => number_format($completedOrders),
            'returns_refunds' => number_format($refunds),
        ];

        // ---------------------------------------------------------------
        // COMPLAINTS & DISPUTES (doughnut chart)
        // ---------------------------------------------------------------
        $complaintCounts = Complaint::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $complaints = [
            'open' => $complaintCounts->get('open', 0),
            'in_progress' => $complaintCounts->get('in_progress', 0),
            'resolved' => $complaintCounts->get('resolved', 0),
        ];

        // ---------------------------------------------------------------
        // PENDING REGISTRATIONS BREAKDOWN
        // ---------------------------------------------------------------
        $pendingBreakdown = [
            'sellers' => Registration::pending()->where('user_type', 'seller')->count(),
            'buyers' => Registration::pending()->where('user_type', 'buyer')->count(),
        ];

        // ---------------------------------------------------------------
        // ANNOUNCEMENT
        // ---------------------------------------------------------------
        $announcement = Announcement::where('is_active', true)->latest()->first();
        $announcementMonth = Carbon::now()->format('F Y');

        // ---------------------------------------------------------------
        // PLATFORM OVERVIEW LINE CHART (last 30 days, bucketed every 5 days)
        // ---------------------------------------------------------------
        $overviewChart = $this->buildOverviewChart();

        return view('pages.admin.Dashboard', compact(
            'stats',
            'salesSummary',
            'complaints',
            'pendingBreakdown',
            'announcement',
            'announcementMonth',
            'overviewChart'
        ));
    }

    /**
     * Percentage change from $previous to $current, formatted for display.
     * Returns something like "12%" and lets the view decide the up/down arrow
     * based on sign (positive/negative), which we also expose.
     */
    private function percentChange($previous, $current): array
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

    /**
     * Builds label + 3 dataset arrays (registrations, active users, active sellers)
     * bucketed into 5-day windows over the last 30 days, matching the
     * existing Chart.js line chart on the dashboard.
     */
    private function buildOverviewChart(): array
    {
        $labels = [];
        $registrations = [];
        $activeUsers = [];
        $activeSellers = [];

        $start = Carbon::today()->subDays(29);

        for ($i = 0; $i < 30; $i += 5) {
            $bucketStart = $start->copy()->addDays($i);
            $bucketEnd = $bucketStart->copy()->addDays(4)->endOfDay();

            $labels[] = $bucketStart->format('M j');

            $registrations[] = User::whereBetween('created_at', [$bucketStart, $bucketEnd])->count();

            $activeUsers[] = User::active()
                ->whereBetween('approved_at', [$bucketStart, $bucketEnd])
                ->count();

            $activeSellers[] = Seller::active()
                ->whereBetween('updated_at', [$bucketStart, $bucketEnd])
                ->count();
        }

        return [
            'labels' => $labels,
            'registrations' => $registrations,
            'active_users' => $activeUsers,
            'active_sellers' => $activeSellers,
        ];
    }
}
