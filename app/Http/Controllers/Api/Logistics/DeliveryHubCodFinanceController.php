<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Mail\DeliveryHubCashInOtp;
use App\Models\Admin\Order;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\Remittance;
use App\Services\CodSettlementService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class DeliveryHubCodFinanceController extends Controller
{
    public function index(Request $request, CodSettlementService $settlements): JsonResponse
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);
        $hub = $this->hub($request);
        $remittances = $this->remittanceQuery($hub)->with(['rider.user', 'collections.order.buyer'])->latest()->get();
        $today = now()->toDateString();
        $todaySettlements = DB::table('cod_settlements')->where('delivery_hub_id', $hub->id)
            ->whereDate('distributed_at', $today)->get();
        $todayCollections = DB::table('rider_cod_collections as c')->join('riders as r', 'r.id', '=', 'c.rider_id')
            ->where('r.logistics_branch_id', $hub->logistics_branch_id)->whereDate('c.collected_at', $today)->get(['c.amount']);
        $todayOutstanding = DB::table('rider_cod_collections as c')->join('riders as r', 'r.id', '=', 'c.rider_id')
            ->where('r.logistics_branch_id', $hub->logistics_branch_id)->whereDate('c.collected_at', $today)
            ->whereNotExists(fn ($q) => $q->selectRaw('1')->from('cod_settlements as cs')->whereColumn('cs.order_id', 'c.order_id'))
            ->get(['c.amount']);
        $history = DB::table('cod_settlements as cs')->join('orders as o', 'o.id', '=', 'cs.order_id')
            ->leftJoin('users as u', 'u.id', '=', 'o.buyer_id')->where('cs.delivery_hub_id', $hub->id)
            ->when(! empty($filters['from']), fn ($q) => $q->whereDate('cs.distributed_at', '>=', $filters['from']))
            ->when(! empty($filters['to']), fn ($q) => $q->whereDate('cs.distributed_at', '<=', $filters['to']))
            ->when(! empty($filters['search']), function ($q) use ($filters): void {
                $term = '%'.$filters['search'].'%';
                $q->where(fn ($nested) => $nested->where('o.order_number', 'like', $term)->orWhere('u.name', 'like', $term));
            })->orderByDesc('cs.distributed_at')->get([
                'cs.id', 'cs.order_id', 'cs.cod_amount', 'cs.shipping_fee', 'cs.platform_shipping_margin', 'cs.commission_amount', 'cs.seller_amount',
                'cs.status', 'cs.distributed_at', 'cs.seller_released_at', 'o.order_number', 'u.name as buyer_name',
            ]);
        $wallet = $settlements->walletBalance('delivery_hub', $hub->id, 'available');
        $todayTotal = (float) $todayCollections->sum('amount');
        $todayDistributed = (float) $todaySettlements->sum('cod_amount');

        return response()->json(['data' => [
            'hub' => ['id' => $hub->id, 'name' => $hub->hub_name],
            'summary' => [
                'wallet_balance' => $wallet,
                'today_orders' => $todayCollections->count(),
                'awaiting_orders' => $todayOutstanding->count(),
                'today_due' => $todayTotal,
                'today_distributed' => $todayDistributed,
                'today_remaining' => (float) $todayOutstanding->sum('amount'),
                'pending_review' => $remittances->where('status', 'pending')->count(),
                'verified_remittances' => $remittances->where('status', 'verified')->count(),
            ],
            'remittances' => $remittances->map(fn (Remittance $remittance): array => $this->presentRemittance($remittance))->values(),
            'today' => $todaySettlements->map(function ($row): array {
                $order = Order::query()->with('buyer')->find($row->order_id);
                return ['id' => $row->id, 'ref' => $order?->order_number, 'buyer' => $order?->buyer?->name,
                    'amount' => (float) $row->cod_amount, 'status' => $row->status, 'time' => $row->distributed_at];
            })->values(),
            'history' => $history->map(fn ($row): array => [
                'id' => $row->id, 'ref' => $row->order_number, 'buyer' => $row->buyer_name,
                'amount' => (float) $row->cod_amount, 'shipping_fee' => (float) $row->shipping_fee,
                'platform_shipping_margin' => (float) $row->platform_shipping_margin,
                'seller_amount' => (float) $row->seller_amount, 'status' => $row->status,
                'distributed_at' => $row->distributed_at, 'seller_released_at' => $row->seller_released_at,
            ])->values(),
            'cash_ins' => DB::table('hub_cod_cash_ins')->where('delivery_hub_id', $hub->id)->latest('received_at')->get(),
        ]]);
    }

    public function review(Request $request, Remittance $remittance): JsonResponse
    {
        $hub = $this->hub($request);
        $data = $request->validate([
            'status' => ['required', 'in:verified,rejected'],
            'actual_amount' => ['required_if:status,verified', 'nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'rejection_reason' => ['required_if:status,rejected', 'nullable', 'in:amount_mismatch,missing_cod_records,invalid_details,duplicate,other'],
            'review_notes' => ['required_if:rejection_reason,other', 'nullable', 'string', 'max:2000'],
        ]);

        if ($data['status'] === 'rejected') {
            $reasonLabels = [
                'amount_mismatch' => 'Declared amount does not match counted cash',
                'missing_cod_records' => 'Missing or mismatched COD records',
                'invalid_details' => 'Invalid or incomplete remittance details',
                'duplicate' => 'Suspected duplicate remittance',
                'other' => 'Other',
            ];
            $reason = $data['rejection_reason'];
            $details = $reason === 'other' ? trim((string) $data['review_notes']) : '';
            $data['review_notes'] = 'Rejection reason: '.$reasonLabels[$reason]
                .($details !== '' ? ' — '.$details : '');
        }

        DB::transaction(function () use ($request, $hub, $remittance, $data): void {
            $locked = Remittance::query()->lockForUpdate()->findOrFail($remittance->id);
            abort_unless($this->belongsToHub($locked, $hub), 404);
            abort_unless(in_array($locked->status, ['pending', 'discrepancy'], true), 409, 'Only pending or discrepant remittances can be reviewed.');
            $status = $data['status'];
            $actual = isset($data['actual_amount']) ? round((float) $data['actual_amount'], 2) : null;
            $discrepancy = $actual === null ? null : round($actual - (float) $locked->amount, 2);
            if ($status === 'verified') {
                $collections = $locked->collections()->lockForUpdate()->get();
                $collectionTotal = round((float) $collections->sum('amount'), 2);
                if ($collections->isEmpty() || abs($collectionTotal - (float) $locked->amount) > 0.009) {
                    $status = 'discrepancy';
                    $data['review_notes'] = trim(($data['review_notes'] ?? '').' Linked COD collections do not match the remittance amount.');
                } elseif (abs((float) $discrepancy) > 0.009 || abs((float) $actual - $collectionTotal) > 0.009) {
                    $status = 'discrepancy';
                    $data['review_notes'] = trim(($data['review_notes'] ?? '').' Counted cash differs from the declared amount.');
                }
            }
            $locked->update([
                'delivery_hub_id' => $hub->id,
                'actual_amount' => $actual,
                'discrepancy_amount' => $discrepancy,
                'status' => $status,
                'review_notes' => $data['review_notes'] ?? null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
            if ($status === 'rejected') {
                $locked->collections()->detach();
            }
        });

        return response()->json(['data' => $this->presentRemittance($remittance->fresh(['rider.user', 'collections.order']))]);
    }

    public function cashIn(Request $request, Remittance $remittance, CodSettlementService $settlements): JsonResponse
    {
        $hub = $this->hub($request);
        abort_unless($this->belongsToHub($remittance, $hub), 404);
        abort_unless($remittance->status === 'verified', 409, 'Only verified remittances can be cashed in.');
        $data = $request->validate(['otp_code' => ['required', 'digits:6']]);
        $result = DB::transaction(function () use ($request, $remittance, $hub, $settlements, $data): array {
            $otp = DB::table('hub_cash_in_otps')->where('delivery_hub_id', $hub->id)
                ->where('remittance_id', $remittance->id)->where('requested_by', $request->user()->id)
                ->whereNull('consumed_at')->latest('id')->lockForUpdate()->first();
            if (! $otp || now()->greaterThan($otp->expires_at)) {
                return ['error' => 'The cash-in code has expired. Request a new email code.', 'status' => 410];
            }
            if ((int) $otp->attempts >= 5) {
                DB::table('hub_cash_in_otps')->where('id', $otp->id)->update(['consumed_at' => now(), 'updated_at' => now()]);
                return ['error' => 'Too many incorrect code attempts. Request a new email code.', 'status' => 429];
            }
            if (! Hash::check($data['otp_code'], $otp->code_hash)) {
                DB::table('hub_cash_in_otps')->where('id', $otp->id)->update([
                    'attempts' => (int) $otp->attempts + 1, 'updated_at' => now(),
                ]);
                return ['error' => 'The email verification code is incorrect.', 'status' => 422];
            }

            $result = $settlements->cashIn($remittance, $hub->id, (int) $request->user()->id);
            DB::table('hub_cash_in_otps')->where('id', $otp->id)->update(['consumed_at' => now(), 'updated_at' => now()]);
            return ['data' => $result];
        });
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }
        $result = $result['data'];
        return response()->json(['data' => $result, 'message' => 'Verified remittance cashed in.']);
    }

    public function requestCashInOtp(Request $request, Remittance $remittance): JsonResponse
    {
        $hub = $this->hub($request);
        abort_unless($this->belongsToHub($remittance, $hub), 404);
        abort_unless($remittance->status === 'verified' && abs((float) $remittance->discrepancy_amount) < 0.009, 409,
            'Only verified remittances without discrepancies can be cashed in.');
        $recipient = trim((string) $hub->manager_email);
        abort_if($recipient === '' || ! filter_var($recipient, FILTER_VALIDATE_EMAIL), 422,
            'Set a valid manager email on this delivery hub before requesting a cash-in code.');
        $recent = DB::table('hub_cash_in_otps')->where('delivery_hub_id', $hub->id)
            ->where('remittance_id', $remittance->id)->where('requested_by', $request->user()->id)
            ->where('created_at', '>=', now()->subMinute())->exists();
        abort_if($recent, 429, 'Wait one minute before requesting another code for this remittance.');

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpId = DB::table('hub_cash_in_otps')->insertGetId([
            'delivery_hub_id' => $hub->id, 'remittance_id' => $remittance->id,
            'requested_by' => $request->user()->id, 'recipient_email' => $recipient,
            'code_hash' => Hash::make($code), 'expires_at' => now()->addMinutes(10),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('hub_cash_in_otps')->where('delivery_hub_id', $hub->id)
            ->where('remittance_id', $remittance->id)->where('requested_by', $request->user()->id)
            ->whereNull('consumed_at')->where('id', '!=', $otpId)
            ->update(['consumed_at' => now(), 'updated_at' => now()]);

        try {
            Mail::to($recipient)->send(new DeliveryHubCashInOtp(
                $hub->hub_name,
                'HUB-REM-'.str_pad((string) $remittance->id, 8, '0', STR_PAD_LEFT),
                number_format((float) $remittance->amount, 2),
                $code,
            ));
        } catch (\Throwable $exception) {
            DB::table('hub_cash_in_otps')->where('id', $otpId)->update(['consumed_at' => now(), 'updated_at' => now()]);
            report($exception);
            return response()->json(['message' => 'The verification email could not be sent. Check mail configuration and try again.'], 503);
        }

        return response()->json(['message' => 'A cash-in verification code was sent to the delivery hub manager email.',
            'expires_in_seconds' => 600, 'resend_after_seconds' => 60]);
    }

    public function distribute(Request $request, Remittance $remittance, CodSettlementService $settlements): JsonResponse
    {
        $hub = $this->hub($request);
        abort_unless($this->belongsToHub($remittance, $hub), 404);
        $result = $settlements->distribute($remittance, $hub->id);
        return response()->json(['data' => $result, 'message' => 'COD distribution recorded.']);
    }

    private function hub(Request $request): LogisticsDeliveryHub
    {
        return LogisticsDeliveryHub::query()->where('user_id', $request->user()->id)->firstOrFail();
    }

    private function remittanceQuery(LogisticsDeliveryHub $hub): Builder
    {
        return Remittance::query()->where(function (Builder $query) use ($hub): void {
            $query->where('delivery_hub_id', $hub->id)->orWhere(function (Builder $unassigned) use ($hub): void {
                $unassigned->whereNull('delivery_hub_id')->where('status', 'pending')
                    ->whereHas('rider', fn (Builder $rider) => $rider->where('logistics_branch_id', $hub->logistics_branch_id));
            });
        });
    }

    private function belongsToHub(Remittance $remittance, LogisticsDeliveryHub $hub): bool
    {
        if ((int) $remittance->delivery_hub_id === (int) $hub->id) return true;
        return $remittance->delivery_hub_id === null && $remittance->status === 'pending' && $remittance->rider()
            ->where('logistics_branch_id', $hub->logistics_branch_id)->exists();
    }

    private function presentRemittance(Remittance $remittance): array
    {
        return [
            'id' => $remittance->id, 'rider' => $remittance->rider?->user?->name ?? 'Rider',
            'amount' => (float) $remittance->amount, 'actual_amount' => $remittance->actual_amount === null ? null : (float) $remittance->actual_amount,
            'discrepancy_amount' => $remittance->discrepancy_amount === null ? null : (float) $remittance->discrepancy_amount,
            'method' => $remittance->method, 'reference_number' => $remittance->reference_number,
            'status' => $remittance->status, 'review_notes' => $remittance->review_notes,
            'created_at' => $remittance->created_at?->toIso8601String(), 'cash_in_at' => $remittance->cash_in_at?->toIso8601String(),
            'orders' => $remittance->collections->map(function ($collection): array {
                $amount = (float) $collection->amount;
                $buyerShipping = min($amount, max(0, (float) ($collection->order?->shipping_fee ?? 0)));
                $shipping = min($buyerShipping, CodSettlementService::LOGISTICS_SHIPPING_FEE);
                $shippingMargin = round($buyerShipping - $shipping, 2);
                $collectedProductAmount = max(0, $amount - $buyerShipping);
                $base = round($collectedProductAmount + (float) ($collection->order?->voucher_discount ?? 0), 2);
                $commission = min($collectedProductAmount, round($base * CodSettlementService::COMMISSION_RATE / 100, 2));
                return [
                    'id' => $collection->order_id, 'ref' => $collection->order?->order_number,
                    'buyer' => $collection->order?->buyer?->name, 'amount' => $amount, 'shipping_fee' => $shipping,
                    'platform_shipping_margin' => $shippingMargin,
                    'commission_amount' => $commission, 'seller_amount' => round($collectedProductAmount - $commission, 2),
                ];
            })->values(),
        ];
    }
}
