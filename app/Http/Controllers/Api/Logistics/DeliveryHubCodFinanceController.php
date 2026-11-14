<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\Remittance;
use App\Services\CodSettlementService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

        // Reconcile completed orders that were confirmed before their remittance
        // was distributed, so the finance view reflects the seller wallet release.
        DB::table('cod_settlements')->where('delivery_hub_id', $hub->id)
            ->whereIn('status', ['held', 'partially_refunded'])->whereNull('seller_released_at')
            ->orderBy('id')->pluck('order_id')
            ->each(fn ($orderId) => $settlements->releaseSellerEarnings((int) $orderId));

        $remittances = $this->remittanceQuery($hub)->with([
            'rider.user', 'collections.order.buyer', 'collections.orderReturn.order.buyer',
        ])->latest()->get();
        $today = now()->toDateString();
        $todaySettlements = DB::table('cod_settlements')->where('delivery_hub_id', $hub->id)
            ->whereDate('distributed_at', $today)->get();
        $todayReturnDistributions = $this->returnDistributionQuery($hub)
            ->whereDate('remittance.updated_at', $today)->get();
        $todayCollections = DB::table('rider_cod_collections as c')->join('riders as r', 'r.id', '=', 'c.rider_id')
            ->where('r.logistics_branch_id', $hub->logistics_branch_id)->whereDate('c.collected_at', $today)->get(['c.amount']);
        $todayOutstanding = DB::table('rider_cod_collections as c')->join('riders as r', 'r.id', '=', 'c.rider_id')
            ->where('r.logistics_branch_id', $hub->logistics_branch_id)->whereDate('c.collected_at', $today)
            ->whereNotExists(fn ($q) => $q->selectRaw('1')->from('cod_settlements as cs')->whereColumn('cs.order_id', 'c.order_id'))
            ->whereNotExists(fn ($q) => $q->selectRaw('1')->from('rider_cod_remittance_items as ri')
                ->join('rider_remittances as rr', 'rr.id', '=', 'ri.remittance_id')
                ->whereColumn('ri.cod_collection_id', 'c.id')->where('rr.status', 'distributed'))
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
            ])->map(function ($row): object { $row->type = 'cod_order'; return $row; });
        $returnHistory = $this->returnDistributionQuery($hub)
            ->when(! empty($filters['from']), fn ($q) => $q->whereDate('remittance.updated_at', '>=', $filters['from']))
            ->when(! empty($filters['to']), fn ($q) => $q->whereDate('remittance.updated_at', '<=', $filters['to']))
            ->when(! empty($filters['search']), function ($q) use ($filters): void {
                $term = '%'.$filters['search'].'%';
                $q->where(fn ($nested) => $nested->where('orders.order_number', 'like', $term)->orWhere('users.name', 'like', $term));
            })->get();
        $history = $history->concat($returnHistory)->sortByDesc('distributed_at')->values();
        $wallet = $settlements->walletBalance('delivery_hub', $hub->id, 'available');
        $todayTotal = (float) $todayCollections->sum('amount');
        $todayDistributed = (float) $todaySettlements->sum('cod_amount') + (float) $todayReturnDistributions->sum('cod_amount');

        return response()->json(['data' => [
            'hub' => ['id' => $hub->id, 'name' => $hub->hub_name],
            'wallet_pin_configured' => DB::table('hub_wallet_pins')->where('delivery_hub_id', $hub->id)->exists(),
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
                    'amount' => (float) $row->cod_amount,
                    'type' => 'cod_order', 'seller_amount' => (float) $row->seller_amount,
                    'status' => $row->seller_released_at ? 'released' : 'held', 'time' => $row->distributed_at];
            })->concat($todayReturnDistributions->map(fn ($row): array => [
                'id' => 'return-'.$row->return_id, 'ref' => $row->order_number, 'buyer' => $row->buyer_name,
                'amount' => (float) $row->cod_amount, 'type' => 'return_refund', 'seller_amount' => 0,
                'status' => $row->return_status === 'refunded' ? 'refund_to_buyer' : 'refund_held', 'time' => $row->distributed_at,
            ]))->sortByDesc('time')->values(),
            'history' => $history->map(fn ($row): array => [
                'id' => $row->id, 'ref' => $row->order_number, 'buyer' => $row->buyer_name,
                'amount' => (float) $row->cod_amount, 'shipping_fee' => (float) $row->shipping_fee,
                'platform_shipping_margin' => (float) $row->platform_shipping_margin,
                'seller_amount' => (float) $row->seller_amount,
                'type' => $row->type,
                'status' => $row->type === 'return_refund'
                    ? ($row->return_status === 'refunded' ? 'refund_to_buyer' : 'refund_held')
                    : ($row->seller_released_at ? 'released' : 'held'),
                'distributed_at' => $row->distributed_at, 'seller_released_at' => $row->seller_released_at,
            ])->values(),
            'cash_ins' => DB::table('hub_cod_cash_ins')->where('delivery_hub_id', $hub->id)->latest('received_at')->get(),
        ]]);
    }

    public function setupWalletPin(Request $request): JsonResponse
    {
        $data = $request->validate(['pin' => ['required', 'digits:6']]);
        $hub = $this->hub($request);

        $result = DB::transaction(function () use ($hub, $request, $data): array {
            $existing = DB::table('hub_wallet_pins')->where('delivery_hub_id', $hub->id)->lockForUpdate()->first();
            abort_if($existing, 409, 'A wallet PIN is already set. Verify your existing PIN to continue.');

            DB::table('hub_wallet_pins')->insert([
                'delivery_hub_id' => $hub->id,
                'pin_hash' => Hash::make($data['pin']),
                'set_by' => $request->user()->id,
                'failed_attempts' => 0,
                'created_at' => now(), 'updated_at' => now(),
            ]);

            return $this->issueWalletPinToken($hub, (int) $request->user()->id);
        });

        return response()->json(['data' => $result + ['configured' => true], 'message' => 'Wallet PIN saved securely.']);
    }

    public function verifyWalletPin(Request $request): JsonResponse
    {
        $data = $request->validate(['pin' => ['required', 'digits:6']]);
        $hub = $this->hub($request);
        $result = DB::transaction(function () use ($hub, $request, $data): array {
            $pin = DB::table('hub_wallet_pins')->where('delivery_hub_id', $hub->id)->lockForUpdate()->first();
            abort_unless($pin, 409, 'Set up a wallet PIN before using Cash In.');

            if ($pin->locked_until && now()->lt($pin->locked_until)) {
                return ['error' => 'Wallet PIN is temporarily locked. Try again later.', 'status' => 429];
            }
            if (! Hash::check($data['pin'], $pin->pin_hash)) {
                $attempts = (int) $pin->failed_attempts + 1;
                $lockedUntil = $attempts >= 5 ? now()->addMinutes(15) : null;
                DB::table('hub_wallet_pins')->where('id', $pin->id)->update([
                    'failed_attempts' => $lockedUntil ? 0 : $attempts,
                    'locked_until' => $lockedUntil,
                    'updated_at' => now(),
                ]);
                return ['error' => $lockedUntil
                    ? 'Too many incorrect PIN attempts. Try again in 15 minutes.'
                    : 'Incorrect wallet PIN. '.(5 - $attempts).' attempt(s) remaining.', 'status' => 422];
            }

            DB::table('hub_wallet_pins')->where('id', $pin->id)->update([
                'failed_attempts' => 0, 'locked_until' => null, 'updated_at' => now(),
            ]);
            return $this->issueWalletPinToken($hub, (int) $request->user()->id);
        });

        if (isset($result['error'])) return response()->json(['message' => $result['error']], $result['status']);
        return response()->json(['data' => $result + ['configured' => true], 'message' => 'Wallet PIN verified.']);
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

        return response()->json(['data' => $this->presentRemittance($remittance->fresh([
            'rider.user', 'collections.order', 'collections.orderReturn.order.buyer',
        ]))]);
    }

    public function cashIn(Request $request, Remittance $remittance, CodSettlementService $settlements): JsonResponse
    {
        $hub = $this->hub($request);
        abort_unless($this->belongsToHub($remittance, $hub), 404);
        abort_unless($remittance->status === 'verified', 409, 'Only verified remittances can be cashed in.');
        $result = DB::transaction(function () use ($request, $remittance, $hub, $settlements): array {
            $pinVerification = $this->walletPinVerification($request, $hub, true);
            $result = $settlements->cashIn($remittance, $hub->id, (int) $request->user()->id);
            DB::table('hub_wallet_pins')->where('delivery_hub_id', $hub->id)
                ->where('verification_token_hash', $pinVerification->verification_token_hash)
                ->update(['token_consumed_at' => now(), 'updated_at' => now()]);
            return ['data' => $result];
        });
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }
        $result = $result['data'];
        return response()->json(['data' => $result, 'message' => 'Verified remittance cashed in.']);
    }

    public function distribute(Request $request, Remittance $remittance, CodSettlementService $settlements): JsonResponse
    {
        $hub = $this->hub($request);
        abort_unless($this->belongsToHub($remittance, $hub), 404);
        $result = $settlements->distribute($remittance, $hub->id);
        return response()->json(['data' => $result, 'message' => 'COD distribution recorded.']);
    }

    private function issueWalletPinToken(LogisticsDeliveryHub $hub, int $userId): array
    {
        $token = bin2hex(random_bytes(32));
        DB::table('hub_wallet_pins')->where('delivery_hub_id', $hub->id)->update([
            'verification_token_hash' => hash('sha256', $token),
            'verified_by' => $userId,
            'token_expires_at' => now()->addMinutes(15),
            'token_consumed_at' => null,
            'updated_at' => now(),
        ]);

        return ['verification_token' => $token, 'expires_in_seconds' => 900];
    }

    private function walletPinVerification(Request $request, LogisticsDeliveryHub $hub, bool $lock = false): object
    {
        $token = (string) $request->input('wallet_pin_token', '');
        abort_unless($token !== '', 403, 'Verify your delivery hub wallet PIN before cash-in.');
        $query = DB::table('hub_wallet_pins')->where('delivery_hub_id', $hub->id)
            ->where('verified_by', $request->user()->id)
            ->where('verification_token_hash', hash('sha256', $token))
            ->whereNull('token_consumed_at')->where('token_expires_at', '>', now());
        if ($lock) $query->lockForUpdate();
        $verification = $query->first();
        abort_unless($verification, 403, 'Wallet PIN verification expired. Enter your PIN again to continue.');

        return $verification;
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
                if ($collection->order_return_id) {
                    $return = $collection->orderReturn;
                    $order = $return?->order;
                    return [
                        'id' => $order?->id,
                        'return_id' => $return?->id,
                        'type' => 'return_refund',
                        'ref' => $order?->order_number,
                        'buyer' => $order?->buyer?->name,
                        'amount' => $amount,
                        'status' => $return?->status,
                        'shipping_status' => $return?->shipping_status,
                        'returned_at' => $return?->returned_at?->toIso8601String(),
                        'shipping_fee' => 0,
                        'platform_shipping_margin' => 0,
                        'commission_amount' => 0,
                        'seller_amount' => 0,
                    ];
                }
                $order = $collection->order;
                $shippingVoucher = $order?->voucher_type === 'shipping' ? max(0, (float) $order->voucher_discount) : 0;
                $buyerShipping = min($amount, max(0, (float) ($order?->shipping_fee ?? 0) - $shippingVoucher));
                $agreedShipping = min(max(0, (float) ($order?->shipping_fee ?? 0)), CodSettlementService::LOGISTICS_SHIPPING_FEE);
                $shipping = $order?->voucher_type === 'shipping' && $order?->voucher_funding_source === 'logistics' ? max(0, $agreedShipping - $shippingVoucher) : $agreedShipping;
                $shippingMargin = max(0, round($buyerShipping - $agreedShipping, 2));
                $collectedProductAmount = max(0, $amount - $buyerShipping);
                $base = round($collectedProductAmount + ($order?->voucher_type === 'shipping' ? 0 : (float) ($order?->voucher_discount ?? 0)), 2);
                $commission = round($base * CodSettlementService::COMMISSION_RATE / 100, 2);
                return [
                    'id' => $collection->order_id, 'ref' => $collection->order?->order_number,
                    'buyer' => $collection->order?->buyer?->name, 'amount' => $amount, 'shipping_fee' => $shipping,
                    'platform_shipping_margin' => $shippingMargin,
                    'commission_amount' => $commission, 'seller_amount' => CodSettlementService::sellerAmount($order, $collectedProductAmount, $commission, $shippingVoucher),
                ];
            })->values(),
        ];
    }

    private function returnDistributionQuery(LogisticsDeliveryHub $hub)
    {
        return DB::table('rider_cod_remittance_items as items')
            ->join('rider_cod_collections as collections', 'collections.id', '=', 'items.cod_collection_id')
            ->join('rider_remittances as remittance', 'remittance.id', '=', 'items.remittance_id')
            ->join('order_returns as returns', 'returns.id', '=', 'collections.order_return_id')
            ->join('orders', 'orders.id', '=', 'returns.order_id')
            ->leftJoin('users', 'users.id', '=', 'orders.buyer_id')
            ->where('remittance.delivery_hub_id', $hub->id)
            ->where('remittance.status', 'distributed')
            ->select([
                'returns.id as id', 'returns.id as return_id', 'orders.id as order_id', 'orders.order_number',
                'users.name as buyer_name', 'collections.amount as cod_amount',
                'returns.status as return_status', 'returns.shipping_status', 'returns.returned_at',
                'remittance.updated_at as distributed_at',
            ])
            ->selectRaw('? as type, 0 as shipping_fee, 0 as platform_shipping_margin, 0 as commission_amount, 0 as seller_amount, null as seller_released_at', ['return_refund']);
    }
}
