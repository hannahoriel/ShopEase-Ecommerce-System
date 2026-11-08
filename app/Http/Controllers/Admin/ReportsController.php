<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->range($request);
        $data = $this->reportViewData($from, $to);

        return view('pages.admin.reports', $data);
    }

    public function apiIndex(Request $request)
    {
        [$from, $to] = $this->range($request);
        $data = $this->reportViewData($from, $to);

        return response()->json([
            'data' => collect($data)->except(['from', 'to', 'dateLabel'])->all(),
            'meta' => [
                'from' => $data['from'],
                'to' => $data['to'],
                'dateLabel' => $data['dateLabel'],
            ],
            'html' => view('pages.admin.reports', $data)->render(),
        ]);
    }

    private function reportViewData(Carbon $from, Carbon $to): array
    {
        return array_merge($this->buildReport($from, $to), [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'dateLabel' => $from->format('M d, Y') . ' — ' . $to->format('M d, Y'),
        ]);
    }

    public function export(Request $request)
    {
        [$from, $to] = $this->range($request);
        $r = $this->buildReport($from, $to);
        $rows = [
            ['ShopEase Admin Reports', $from->toDateString(), $to->toDateString()],
            ['Metric', 'Value', 'Definition'],
            ['Completed merchandise value', $r['kpis']['sales'], 'Orders completed in period; excludes buyer shipping'],
            ['Earned commission', $r['kpis']['commission'], 'Commission transactions earned during period'],
            ['Refunds issued', $r['kpis']['refunds'], 'Completed refunds with refunded_at in period'],
            ['Completed orders', $r['kpis']['completed'], 'Orders completed in period'],
            ['Voucher discount redeemed', $r['voucher']['amount'], 'Seller voucher discounts on completed orders'],
            ['Voucher orders', $r['voucher']['orders'], 'Completed orders with seller voucher discounts'],
            ['Accounts created', $r['accounts']['created'], 'User accounts created in the reporting period'],
            ['Products added', $r['catalog']['created'], 'Products created in the reporting period'],
            ['Admin archived products', $r['catalog']['archived_by_admin'], 'Products added in this period that are currently archived by admin'],
            ['Products with warnings', $r['catalog']['with_warnings'], 'Products added in this period that currently have warning reasons'],
            ['Delivered shipments', $r['shipping']['delivered'], 'Shipments delivered in period'],
            ['Average delivery time (hours)', $r['shipping']['average_hours'] ?? 'N/A', 'Pickup to delivery for period deliveries with both timestamps'],
            ['Registrations submitted', $r['registration']['total'], 'New registration applications in period'],
            ['Seller approvals', $r['sellerCompliance']['approved'], 'Seller records created in period with active or approved registration status'],
            ['Pending seller verification', $r['sellerCompliance']['pending'], 'Seller records created in period with pending registration status'],
            ['Rejected seller registrations', $r['sellerCompliance']['rejected'], 'Seller records created in period with rejected registration status'],
            ['Complaints opened', $r['complaints']['total'], 'Complaint records created in period'],
            ['Returns requested', $r['returns']['total'], 'Return requests submitted in period'],
            [], ['Order cohort - current status', 'Count'],
        ];
        foreach ($r['ordersByStatus'] as $status => $count) $rows[] = [$status, $count];
        $rows[] = []; $rows[] = ['Top sellers', 'Completed order count', 'Completed merchandise value'];
        foreach ($r['sellers'] as $seller) $rows[] = [$seller->store_name, $seller->orders, $seller->sales];

        $name = 'shopease-admin-report-' . $from->toDateString() . '-to-' . $to->toDateString() . '.csv';
        return Response::streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            foreach ($rows as $row) {
                $clean = array_map(function ($v) {
                    $value = (string) $v;
                    // Neutralize formula injection when opened in spreadsheet software.
                    return preg_match('/^[\s]*[=+\-@]/u', $value) ? "'" . $value : $value;
                }, $row);
                fputcsv($out, $clean);
            }
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function range(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);
        $to = Carbon::parse($validated['to'] ?? now()->toDateString())->startOfDay();
        $from = Carbon::parse($validated['from'] ?? now()->subDays(29)->toDateString())->startOfDay();
        abort_if($to->lt($from), 422, 'End date must not be earlier than start date.');
        abort_if($to->gt(now()->startOfDay()), 422, 'Future date ranges are not supported.');
        abort_if($from->diffInDays($to) > 366, 422, 'Select a range of up to 366 days.');
        return [$from, $to];
    }

    private function buildReport(Carbon $from, Carbon $to): array
    {
        $start = $from->copy()->startOfDay();
        $end = $to->copy()->endOfDay();
        $completedDate = 'COALESCE(buyer_received_at, updated_at)';
        $completed = DB::table('orders')->where('status', 'completed')
            ->whereRaw("$completedDate >= ? AND $completedDate <= ?", [$start, $end]);

        $sales = (float) (clone $completed)->selectRaw('COALESCE(SUM(GREATEST(total - COALESCE(shipping_fee,0), 0)),0) as value')->first()->value;
        $completedCount = (int) (clone $completed)->count();
        $commission = (float) DB::table('commission_transactions')
            ->where('status', 'earned')->whereBetween('earned_at', [$start, $end])->sum('commission_amount');
        // Local demo return seeders create records that look fully refunded. Keep
        // those clearly marked fixtures out of production-facing report totals.
        $demoReturnOrderIds = DB::table('orders')
            ->where(function ($query) {
                $query->where('order_number', 'like', 'SE-DEMO-RET-%')
                    ->orWhere('order_number', 'like', 'DEMO-TRACK-%');
            })
            ->select('id');
        $reportReturns = DB::table('order_returns')->whereNotIn('order_id', $demoReturnOrderIds);
        $refunds = (float) (clone $reportReturns)->where('status', 'refunded')
            ->whereBetween('refunded_at', [$start, $end])->sum('refund_amount');

        // This is a created-order cohort (current statuses), not a count of status-change events.
        $statuses = DB::table('orders')->whereBetween('created_at', [$start, $end])
            ->select('status', DB::raw('COUNT(*) as count'))->groupBy('status')
            ->pluck('count', 'status')->map(fn ($v) => (int) $v)->all();
        $orderedStatuses = ['new', 'pending', 'confirmed', 'preparing', 'to_ship', 'shipped', 'in_transit', 'out_for_delivery', 'completed', 'cancelled', 'refunded'];
        $ordersByStatus = [];
        foreach ($orderedStatuses as $s) if (array_key_exists($s, $statuses)) $ordersByStatus[$s] = $statuses[$s];
        foreach ($statuses as $s => $n) if (!array_key_exists($s, $ordersByStatus)) $ordersByStatus[$s] = $n;

        // One row per date, aggregated in SQL, then fill gaps for a continuous chart.
        $trendRows = (clone $completed)->selectRaw("DATE($completedDate) as day, COUNT(*) as orders, COALESCE(SUM(GREATEST(total - COALESCE(shipping_fee,0),0)),0) as sales")
            ->groupBy('day')->orderBy('day')->get()->keyBy('day');
        $trend = [];
        $day = $from->copy();
        while ($day->lte($to)) {
            $key = $day->toDateString();
            $trend[] = ['date' => $key, 'label' => $day->format('M d'), 'orders' => (int) ($trendRows[$key]->orders ?? 0), 'sales' => (float) ($trendRows[$key]->sales ?? 0)];
            $day->addDay();
        }
        // For large date windows, group on the client into <= 15 readable bars.

        $sellers = DB::table('orders as o')->join('sellers as s', 's.id', '=', 'o.seller_id')
            ->where('o.status', 'completed')
            ->whereRaw('COALESCE(o.buyer_received_at, o.updated_at) >= ? AND COALESCE(o.buyer_received_at, o.updated_at) <= ?', [$start, $end])
            ->select('s.store_name', DB::raw('COUNT(o.id) as orders'), DB::raw('SUM(GREATEST(o.total - COALESCE(o.shipping_fee,0),0)) as sales'))
            ->groupBy('s.id', 's.store_name')->orderByDesc('sales')->limit(6)->get();

        $reg = DB::table('registrations')->whereBetween('created_at', [$start, $end]);
        $regByType = (clone $reg)->select('user_type', DB::raw('COUNT(*) as total'))
            ->groupBy('user_type')->pluck('total', 'user_type')->all();
        $regByStatus = (clone $reg)->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->all();
        $complaints = DB::table('complaints')->whereBetween('created_at', [$start, $end]);
        $complaintStatuses = (clone $complaints)->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->all();
        $returns = (clone $reportReturns)->whereBetween('requested_at', [$start, $end]);
        $returnTypes = (clone $returns)->select('return_type', DB::raw('COUNT(*) as total'))
            ->groupBy('return_type')->pluck('total', 'return_type')->all();
        $delivered = DB::table('shipments')->whereBetween('delivered_at', [$start, $end]);
        $averageHours = (clone $delivered)->whereNotNull('picked_up_at')->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, picked_up_at, delivered_at)) / 60 AS avg_hours')->value('avg_hours');
        $voucher = (clone $completed)->where('voucher_discount', '>', 0);
        $newUsers = DB::table('users')->whereBetween('created_at', [$start, $end]);
        $newUsersByRole = (clone $newUsers)->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')->pluck('total', 'role')->all();
        $sellerStatusCounts = DB::table('sellers')->whereBetween('created_at', [$start, $end])
            ->select('registration_status', DB::raw('COUNT(*) as total'))
            ->groupBy('registration_status')->pluck('total', 'registration_status');
        $sellerApproved = (int) ($sellerStatusCounts['active'] ?? 0) + (int) ($sellerStatusCounts['approved'] ?? 0);
        $sellerCompliance = [
            'approved' => $sellerApproved,
            'pending' => (int) ($sellerStatusCounts['pending'] ?? 0),
            'rejected' => (int) ($sellerStatusCounts['rejected'] ?? 0),
        ];
        $sellerCompliance['reviewed'] = $sellerCompliance['approved'] + $sellerCompliance['rejected'];
        $newProducts = DB::table('products')->whereBetween('created_at', [$start, $end]);
        $catalog = [
            'created' => (int) (clone $newProducts)->count(),
            'archived_by_admin' => (int) (clone $newProducts)->where('archived_by_admin', true)->count(),
            'with_warnings' => (int) (clone $newProducts)->whereNotNull('warning_reason')->where('warning_reason', '<>', '')->count(),
        ];

        return [
            'kpis' => ['sales' => $sales, 'commission' => $commission, 'refunds' => $refunds, 'completed' => $completedCount],
            'ordersByStatus' => $ordersByStatus,
            'orderCohort' => array_sum($ordersByStatus),
            'trend' => $trend,
            'sellers' => $sellers,
            'registration' => ['total' => (int) (clone $reg)->count(), 'types' => $regByType, 'statuses' => $regByStatus],
            'complaints' => ['total' => (int) (clone $complaints)->count(), 'statuses' => $complaintStatuses],
            'returns' => ['total' => (int) (clone $returns)->count(), 'types' => $returnTypes],
            'shipping' => ['delivered' => (int) (clone $delivered)->count(), 'average_hours' => $averageHours === null ? null : round((float) $averageHours, 1)],
            'voucher' => ['orders' => (int) (clone $voucher)->count(), 'amount' => (float) (clone $voucher)->sum('voucher_discount')],
            'accounts' => ['created' => (int) (clone $newUsers)->count(), 'roles' => $newUsersByRole],
            'sellerCompliance' => $sellerCompliance,
            'catalog' => $catalog,
        ];
    }
}
