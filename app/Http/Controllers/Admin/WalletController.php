<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', Rule::in(['all', 'commission_income', 'platform_voucher', 'commission_refund'])],
            'from' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'to' => ['nullable', 'date_format:Y-m-d\TH:i', 'after_or_equal:from'],
        ]);

        $commissions = DB::table('commission_transactions as commission')
            ->join('orders', 'orders.id', '=', 'commission.order_id')
            ->where('commission.status', 'earned')
            ->whereNotNull('commission.earned_at')
            ->where('commission.commission_amount', '>', 0)
            ->selectRaw("orders.id as order_id, orders.order_number as reference, 'commission_income' as entry_type,
                commission.commission_amount as amount, commission.earned_at as occurred_at,
                'Earned order commission' as description");

        $vouchers = DB::table('orders')
            ->where('orders.status', 'completed')
            ->where('orders.voucher_discount', '>', 0)
            ->where(function (Builder $query): void {
                $query->where('orders.voucher_funding_source', 'platform')
                    ->orWhere(function (Builder $legacy): void {
                        $legacy->whereNull('orders.voucher_funding_source')
                            ->whereExists(function (Builder $redemptions): void {
                                $redemptions->selectRaw('1')->from('seller_voucher_redemptions as redemption')
                                    ->join('seller_vouchers as voucher', 'voucher.id', '=', 'redemption.seller_voucher_id')
                                    ->whereColumn('redemption.order_id', 'orders.id')
                                    ->whereNull('voucher.seller_id');
                            });
                    });
            })
            ->selectRaw("orders.id as order_id, orders.order_number as reference, 'platform_voucher' as entry_type,
                -orders.voucher_discount as amount, COALESCE(orders.buyer_received_at, orders.updated_at) as occurred_at,
                COALESCE(orders.voucher_code, 'ShopEase voucher') as description");

        $completedRefunds = DB::table('order_returns as refunds')
            ->join('commission_transactions as commission', 'commission.order_id', '=', 'refunds.order_id')
            ->join('orders', 'orders.id', '=', 'refunds.order_id')
            ->where('commission.status', 'earned')
            ->whereColumn('refunds.refunded_at', '>=', 'commission.earned_at')
            ->where('refunds.status', 'refunded')
            ->where('refunds.refund_amount', '>', 0)
            ->selectRaw("orders.id as order_id, orders.order_number as reference, refunds.refunded_at as occurred_at,
                commission.commission_amount, commission.commission_rate, refunds.refund_amount,
                SUM(refunds.refund_amount) OVER (
                    PARTITION BY refunds.order_id ORDER BY refunds.refunded_at, refunds.id ROWS UNBOUNDED PRECEDING
                ) as refunded_total");

        // Cap the cumulative deduction at the original earned commission.
        $currentShare = 'ROUND(refunded_total * commission_rate / 100.0, 2)';
        $previousShare = 'ROUND((refunded_total - refund_amount) * commission_rate / 100.0, 2)';
        $refunds = DB::query()->fromSub($completedRefunds, 'completed_refunds')
            ->selectRaw("order_id, reference, 'commission_refund' as entry_type,
                (CASE WHEN $previousShare > commission_amount THEN commission_amount ELSE $previousShare END)
                - (CASE WHEN $currentShare > commission_amount THEN commission_amount ELSE $currentShare END) as amount,
                occurred_at, 'Commission reversed on completed refund' as description");
        $refundEntries = DB::query()->fromSub($refunds, 'refund_entries')->where('amount', '<', 0);

        $history = DB::query()->fromSub($commissions->unionAll($vouchers)->unionAll($refundEntries), 'wallet_entries');
        $totals = (clone $history)->selectRaw('entry_type, SUM(amount) as total')
            ->groupBy('entry_type')->pluck('total', 'entry_type');

        if (filled($filters['search'] ?? null)) {
            $search = '%'.trim($filters['search']).'%';
            $history->where(fn (Builder $query) => $query
                ->where('reference', 'like', $search)->orWhere('description', 'like', $search));
        }
        if (in_array($filters['type'] ?? 'all', ['commission_income', 'platform_voucher', 'commission_refund'], true)) {
            $history->where('entry_type', $filters['type']);
        }
        if (filled($filters['from'] ?? null)) {
            $history->where('occurred_at', '>=', Carbon::parse($filters['from'])->startOfMinute());
        }
        if (filled($filters['to'] ?? null)) {
            $history->where('occurred_at', '<=', Carbon::parse($filters['to'])->endOfMinute());
        }

        $income = (float) ($totals['commission_income'] ?? 0);
        $deductions = abs((float) ($totals['platform_voucher'] ?? 0));
        $refundDeductions = abs((float) ($totals['commission_refund'] ?? 0));
        $wallet = [
            'balance' => round($income - $deductions - $refundDeductions, 2),
            'refunds' => $refundDeductions,
            'income' => $income,
            'vouchers' => $deductions,
            'transactions' => $history->orderByDesc('occurred_at')->orderByDesc('order_id')
                ->orderBy('entry_type')->paginate(12)->appends($filters),
        ];

        if ($request->expectsJson()) {
            return response()->json($wallet);
        }

        return view('pages.admin.wallet', compact('wallet', 'filters'));
    }
}
