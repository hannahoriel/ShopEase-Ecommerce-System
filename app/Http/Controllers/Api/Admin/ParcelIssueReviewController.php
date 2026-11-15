<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Shipment;
use App\Models\Logistics\DeliveryHubParcelIssue;
use App\Models\Logistics\SortingCenterParcelIssue;
use App\Models\Seller\Seller;
use App\Services\SellerComplianceService;
use App\Services\CodSettlementService;
use App\Services\BuyerWalletService;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Buyer\BuyerNotification;
use App\Notifications\SellerPreferenceNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ParcelIssueReviewController extends Controller
{
    public function index(): JsonResponse
    {
        $sorting = SortingCenterParcelIssue::query()->latest()->get()->map(fn ($report) => $this->serialize($report, 'sorting_center'));
        $hubs = DeliveryHubParcelIssue::query()->latest()->get()->map(fn ($report) => $this->serialize($report, 'delivery_hub'));

        return response()->json(['data' => $sorting->concat($hubs)->sortByDesc('at')->values()]);
    }

    public function decide(Request $request, string $facility, int $reportId, SellerComplianceService $compliance): JsonResponse
    {
        abort_unless(in_array($facility, ['sorting_center', 'delivery_hub'], true), 404);
        $data = $request->validate([
            'decision' => ['required', Rule::in(['logistics_fault', 'seller_fault', 'shared'])],
            'notes' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $result = DB::transaction(function () use ($request, $facility, $reportId, $data, $compliance): array {
            $model = $facility === 'sorting_center' ? SortingCenterParcelIssue::class : DeliveryHubParcelIssue::class;
            $report = $model::query()->lockForUpdate()->findOrFail($reportId);
            abort_if($report->admin_decision, 409, 'This parcel report already has a final decision.');

            $shipmentQuery = Shipment::query()->with('order.seller.user')
                ->where('tracking_number', $report->tracking_number);
            $shipmentQuery->when($facility === 'sorting_center', fn ($query) => $query->where('logistics_branch_id', $report->sorting_center_id));
            $shipmentQuery->when($facility === 'delivery_hub', fn ($query) => $query->where('logistics_delivery_hub_id', $report->delivery_hub_id));
            $shipment = $shipmentQuery->lockForUpdate()->first();
            $order = $shipment?->order;
            $seller = $order?->seller;
            $ledgerSource = $facility === 'sorting_center' ? 'parcel_issue_sorting_center' : 'parcel_issue_delivery_hub';

            if ($data['decision'] === 'seller_fault') {
                abort_unless($seller, 422, 'The parcel report is not linked to a seller order.');
                $seller = Seller::query()->whereKey($seller->id)->lockForUpdate()->firstOrFail();
            }

            $report->forceFill([
                'admin_decision' => $data['decision'],
                'decision_notes' => trim($data['notes']),
                'decided_by' => $request->user()->id,
                'decided_at' => now(),
                'status' => 'Resolved',
            ])->save();

            if ($order && in_array($data['decision'], ['logistics_fault', 'seller_fault', 'shared'], true)
                && ! in_array($order->status, ['cancelled', 'refunded', 'delivered', 'completed'], true)) {
                $previousStatus = $order->status;
                $order->forceFill(['status' => 'cancelled'])->save();
                OrderStatusHistory::query()->create([
                    'order_id' => $order->id,
                    'from_status' => $previousStatus,
                    'to_status' => 'cancelled',
                    'changed_by' => $request->user()->id,
                    'notes' => 'Order cancelled after admin parcel issue decision '.$data['decision'].': '.trim($data['notes']),
                ]);
                if ($order->payment_method === 'ShopEase Wallet' && (float) $order->total > 0) {
                    app(BuyerWalletService::class)->move(
                        (int) $order->buyer_id, 'payment_reversal', (float) $order->total,
                        'order', (int) $order->id, 'Refund for admin-reviewed parcel issue '.$order->order_number,
                    );
                    BuyerNotification::createForOrder($order, 'order', 'Order refunded after parcel review',
                        'Your order was cancelled after the parcel issue review. The paid amount was returned to your ShopEase Wallet.',
                        ['status' => 'cancelled']);
                }
            }

            $settlementAmount = 0.0;
            $verifiedProductValue = $order ? round((float) $order->items()->sum(DB::raw('quantity * unit_price')), 2) : 0.0;
            if ($order && $seller && $data['decision'] === 'logistics_fault') {
                $priorSellerIncome = (float) DB::table('seller_wallet_transactions')
                    ->where('seller_id', $seller->id)->where('entry_type', 'order_income')
                    ->where('source_type', 'order')->where('source_id', $order->id)->sum('amount');
                $priorSellerReimbursement = (float) DB::table('seller_wallet_transactions')
                    ->where('seller_id', $seller->id)->where('entry_type', 'logistics_reimbursement')
                    ->where('reference', $order->order_number)->sum('amount');
                $settlementAmount = max(0, round($verifiedProductValue - max(0, $priorSellerIncome) - max(0, $priorSellerReimbursement), 2));
                if ($settlementAmount > 0) {
                    DB::table('seller_wallet_transactions')->insertOrIgnore([
                        'seller_id' => $seller->id,
                        'entry_type' => 'logistics_reimbursement',
                        'source_type' => $ledgerSource,
                        'source_id' => $report->id,
                        'amount' => number_format($settlementAmount, 2, '.', ''),
                        'reference' => $order->order_number,
                        'description' => 'Admin-approved logistics reimbursement for '.$report->issue_type.' · shipping excluded',
                        'occurred_at' => now(), 'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }

            if ($order && $seller && $data['decision'] === 'seller_fault') {
                DB::table('seller_wallet_transactions')->insertOrIgnore([
                    'seller_id' => $seller->id,
                    'entry_type' => 'return_shipping',
                    'source_type' => $ledgerSource,
                    'source_id' => $report->id,
                    'amount' => '-'.number_format(CodSettlementService::LOGISTICS_SHIPPING_FEE, 2, '.', ''),
                    'reference' => $order->order_number,
                    'description' => 'Seller-paid parcel issue shipping charge (₱'.number_format(CodSettlementService::LOGISTICS_SHIPPING_FEE, 2).')',
                    'occurred_at' => now(), 'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            if ($seller && $data['decision'] === 'seller_fault') {
                $score = $compliance->scoreFor($seller);
                $suspension = $compliance->applyAutomaticSuspension($seller, $score);
            } else {
                $score = $seller ? $compliance->scoreFor($seller) : null;
                $suspension = ['suspended' => false, 'reactivated' => false];
            }

            $report->accountability = array_merge($report->accountability ?? [], [
                'status' => match ($data['decision']) {
                    'logistics_fault' => 'Logistics at Fault',
                    'seller_fault' => 'Seller at Fault',
                    'shared' => 'Shared Responsibility',
                },
                'compensation' => $settlementAmount,
                'parcelAmount' => $verifiedProductValue,
                'shippingCharge' => $data['decision'] === 'seller_fault' ? CodSettlementService::LOGISTICS_SHIPPING_FEE : 0,
                'notes' => trim($data['notes']),
                'decidedAt' => $report->decided_at?->toIso8601String(),
            ]);
            $report->save();

            if ($seller?->user) {
                $decisionLabel = match ($data['decision']) {
                    'logistics_fault' => 'Logistics at Fault',
                    'seller_fault' => 'Seller at Fault',
                    'shared' => 'Shared Responsibility',
                };
                $seller->user->notify(new SellerPreferenceNotification(
                    'Parcel issue review completed',
                    'Admin decision for tracking '.$report->tracking_number.': '.$decisionLabel.'. '.trim($data['notes']),
                    'order_activity',
                    '/seller/wallet?search='.rawurlencode((string) ($order?->order_number ?? $report->tracking_number)),
                ));
            }

            return ['report' => $report, 'score' => $score, 'suspension' => $suspension];
        }, 3);

        return response()->json(['data' => [
            'id' => $result['report']->id,
            'decision' => $result['report']->admin_decision,
            'status' => $result['report']->status,
            'compensation' => (float) data_get($result['report']->accountability, 'compensation', 0),
            'compliance_score' => $result['score'],
            'auto_suspended' => $result['suspension']['suspended'],
            'auto_reactivated' => $result['suspension']['reactivated'],
        ]]);
    }

    private function serialize($report, string $facility): array
    {
        $hub = $facility === 'sorting_center';
        $shipment = Shipment::query()->with(['order.seller.user', 'order.items.product'])
            ->where('tracking_number', $report->tracking_number)
            ->when($hub, fn ($query) => $query->where('logistics_branch_id', $report->sorting_center_id))
            ->when(! $hub, fn ($query) => $query->where('logistics_delivery_hub_id', $report->delivery_hub_id))
            ->first();
        $order = $shipment?->order;
        $seller = $order?->seller;
        $item = $order?->items?->first();

        return [
            'id' => strtoupper($hub ? 'SC' : 'DH').'-'.$report->id,
            'databaseId' => $report->id,
            'parcelIssue' => true,
            'facility' => $facility,
            'party1' => $hub ? 'Sorting Center' : 'Delivery Hub',
            'party1Email' => '—', 'role1' => 'Logistics',
            'party2' => $seller?->store_name ?? 'Seller unavailable',
            'party2Email' => $seller?->user?->email ?? '—', 'role2' => 'Seller',
            'type' => 'Parcel Dispute · '.$report->issue_type,
            'status' => $report->admin_decision ? 'resolved' : 'under-review',
            'date' => $report->created_at?->format('M d, Y') ?? '',
            'time' => $report->created_at?->format('g:i A') ?? '',
            'createdAt' => $report->created_at?->toIso8601String(),
            'lastUpdated' => $report->updated_at?->format('M d, Y g:i A') ?? '',
            'description' => $report->description,
            'evidence' => $report->evidence_path ? [Storage::disk('public')->url($report->evidence_path)] : [],
            'orderId' => $order?->order_number ?? '—',
            'orderDate' => $order?->created_at?->format('M d, Y g:i A') ?? '—',
            'paymentMethod' => $order?->payment_method ?? '—',
            'paymentStatus' => $order?->payment_method === 'ShopEase Wallet' ? 'Paid' : ($order ? 'Cash on Delivery' : '—'),
            'shippingMethod' => $shipment?->courier ?? '—',
            'orderStatus' => $order?->status ? str($order->status)->replace('_', ' ')->title()->toString() : '—',
            'orderTotal' => $order ? '₱'.number_format((float) $order->total, 2) : '—',
            'productName' => $item?->product_name ?? '—',
            'is_fragile' => (bool) ($item?->is_fragile || $item?->product?->is_fragile),
            'productVariant' => collect([$item?->variation, $item?->color, $item?->size])->filter()->implode(' · ') ?: '—',
            'productPrice' => $item ? '₱'.number_format((float) $item->unit_price, 2) : '—',
            'productQuantity' => $item ? 'x'.$item->quantity : '—',
            'productCategory' => $item?->product?->category ?? '—',
            'productImage' => $this->productImage($item?->product?->photos ?? []),
            'shopName' => $seller?->store_name ?? '—',
            'shopOwner' => $seller?->user?->name ?? '—',
            'tracking' => $report->tracking_number,
            'decision' => $report->admin_decision,
            'decisionNotes' => $report->decision_notes,
            'sellerAtFault' => $report->admin_decision === 'seller_fault' ? true : ($report->admin_decision === 'logistics_fault' ? false : null),
            'sellerFaultNotes' => $report->decision_notes,
        ];
    }

    private function productImage(array $photos): ?string
    {
        $photo = collect($photos)->first(fn ($value): bool => is_string($value) && trim($value) !== '');
        if (! $photo) {
            return null;
        }

        if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://') || str_starts_with($photo, 'data:')) {
            return $photo;
        }

        return Storage::disk('public')->url(ltrim(preg_replace('/^storage\//', '', $photo), '/'));
    }
}
