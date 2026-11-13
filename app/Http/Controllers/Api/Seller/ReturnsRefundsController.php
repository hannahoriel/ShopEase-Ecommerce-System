<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Buyer\BuyerNotification;
use App\Models\Seller\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\BuyerWalletService;
use App\Services\CodSettlementService;

class ReturnsRefundsController extends Controller
{
    public function page(Request $request)
    {
        $seller = Seller::query()->where('user_id', $request->user()->id)->firstOrFail();
        $records = OrderReturn::query()->whereHas('order', fn ($q) => $q->where('seller_id', $seller->id))
            ->with(['order.buyer:id,name,email', 'order.items', 'events'])->latest('requested_at')->get();
        $present = fn (OrderReturn $return) => [
            'complaint_id' => $return->complaint_id,
            'admin_managed' => (bool)$return->complaint_id,
            'id' => (string) $return->id,
            'order_id' => $return->order->order_number,
            'buyer_name' => $return->order->buyer?->name ?? 'Buyer',
            'buyer_email' => $return->order->buyer?->email,
            'buyer_phone' => $return->order->delivery_phone,
            'delivery_address' => $return->order->delivery_address,
            'return_items' => $return->return_items ?: [],
            'type' => $return->return_type === 'refund' ? 'Refund Only' : 'Return & Refund',
            'refund_type' => $return->return_type === 'refund' ? 'Refund Only' : 'Return & Refund',
            'status' => $return->status,
            'shipping_status' => $return->shipping_status,
            'returned_at' => $return->returned_at?->toIso8601String(),
            'date' => $return->requested_at,
            'updated_at' => $return->updated_at,
            'description' => $return->reason,
            'solution' => $return->solution,
            'amount' => $return->refund_amount ?? $return->order->total,
            'rider_collected' => DB::table('rider_cod_collections')->where('order_return_id', $return->id)->exists(),
            'evidence' => $return->evidence ?? [],
            'refunded_at' => $return->refunded_at,
            'approved_at' => $return->reviewed_at,
            'note' => $return->solution,
        ];

        $shipping = $records->filter(fn (OrderReturn $return) => filled($return->shipping_status) || filled($return->tracking_number))
            ->map(fn (OrderReturn $return) => [
                'return_id' => $return->id,
                'return_type' => $return->return_type,
                'request_status' => $return->status,
                'return_shipping_status' => $return->shipping_status,
                'returned_at' => $return->returned_at?->toIso8601String(),
                'order_id' => $return->order->order_number,
                'buyer_name' => $return->order->buyer?->name ?? 'Buyer',
                'buyer_email' => $return->order->buyer?->email,
                'buyer_phone' => $return->order->delivery_phone,
                'address' => $return->order->delivery_address,
                'shipping_status' => match (str_replace('_', '-', (string) $return->shipping_status)) {
                    'return-requested', 'scheduled', 'processing' => 'processing',
                    'picked-up', 'in-transit' => 'in-transit',
                    'out-for-delivery' => 'out-for-delivery',
                    'returned', 'delivered' => 'delivered',
                    default => $return->shipping_status,
                },
                'tracking_number' => $return->tracking_number ?? 'Awaiting return pickup',
                'courier' => $return->courier ?? 'Not assigned',
                'date' => $return->requested_at,
                'estimated_delivery' => null,
                'price' => $return->refund_amount ?? $return->order->total,
                'return_items' => $return->return_items ?: [],
                'events' => $return->events->map(fn ($event) => [$event->status, $event->scanned_at, $event->location])->values(),
            ])->values();

        return view('pages.seller.returns-refunds', [
            'returnRefunds' => $records->map($present)->values(),
            'approvedRefunds' => $records->whereNotNull('refunded_at')->map($present)->values(),
            'shippingOrders' => $shipping,
        ]);
    }

    public function index(Request $request)
    {
        $seller = Seller::query()->where('user_id', $request->user()->id)->firstOrFail();
        $query = OrderReturn::query()->whereHas('order', fn ($q) => $q->where('seller_id', $seller->id))
            ->with(['order.buyer:id,name,email', 'order.items', 'events']);

        if ($request->string('status')->isNotEmpty()) {
            $query->where('status', $request->string('status')->toString());
        }

        $requests = $query->latest('requested_at')->paginate(min(100, max(1, $request->integer('per_page', 20))));
        return response()->json($requests);
    }

    public function decide(Request $request, OrderReturn $return)
    {
        abort_if($return->complaint_id,403,'This complaint is reviewed by ShopEase admin.');
        $seller = Seller::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($return->order()->where('seller_id', $seller->id)->exists(), 404);

        $data = $request->validate([
            'decision' => ['required', Rule::in(['accepted', 'declined'])],
            'solution' => ['nullable', 'string', 'max:2000'],
            'refund_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);

        abort_unless($return->status === 'pending', 409, 'Only pending requests can be reviewed.');

        if ($data['decision'] === 'accepted' && isset($data['refund_amount'])) {
            abort_if((float) $data['refund_amount'] > (float) $return->order->total, 422, 'Refund cannot exceed the order total.');
        }

        return DB::transaction(function () use ($request, $return, $data) {
            $locked = OrderReturn::query()->whereKey($return->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'pending', 409, 'This request has already been reviewed.');
            $locked->forceFill([
                'status' => $data['decision'],
                'solution' => $data['solution'] ?? $locked->solution,
                'refund_amount' => $data['refund_amount'] ?? ($data['decision'] === 'accepted' ? $locked->order->total : null),
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ])->save();
            $locked->events()->create(['status' => $data['decision'], 'location' => 'Seller review', 'scanned_at' => now()]);
            $reviewed = $locked->fresh(['order.buyer:id,name,email', 'order.items', 'events']);
            if ($reviewed->order?->buyer) {
                BuyerNotification::createForBuyer(
                    $reviewed->order->buyer,
                    'return_update',
                    $data['decision'] === 'accepted' ? 'Return request accepted' : 'Return request declined',
                    'The seller '.($data['decision'] === 'accepted' ? 'accepted' : 'declined')
                        .' your '.($reviewed->return_type === 'refund' ? 'refund' : 'return').' request.',
                    ['return_id' => $reviewed->id, 'status' => $reviewed->status, 'url' => '/buyer/my-purchases?tab=returns'],
                    $reviewed->order,
                );
            }
            return response()->json(['data' => $reviewed]);
        });
    }

    public function completeRefund(Request $request, OrderReturn $return)
    {
        abort_if($return->complaint_id,403,'Admin controls refund completion for this complaint.');
        abort_if($return->return_type === 'return'
            && DB::table('rider_cod_collections')->where('order_return_id', $return->id)->exists(),
            409, 'The delivery hub is holding the rider-collected return funds. The buyer wallet is credited automatically after seller receipt confirmation and hub distribution.');
        $seller = Seller::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($return->order()->where('seller_id', $seller->id)->exists(), 404);
        abort_unless($return->return_type === 'refund' || $return->returned_at || $return->shipping_status === 'returned', 409, 'Confirm receipt of the returned parcel before completing its refund.');

        return DB::transaction(function () use ($return) {
            $locked = OrderReturn::query()->whereKey($return->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'accepted', 409, 'Only accepted requests can be refunded.');
            abort_unless($locked->return_type === 'refund' || $locked->returned_at || $locked->shipping_status === 'returned', 409, 'Confirm receipt of the returned parcel before completing its refund.');
            $paidMerchandise = max(0, round((float) $locked->order->total - (float) $locked->order->shipping_fee, 2));
            $alreadyRefunded = (float) OrderReturn::query()->where('order_id', $locked->order_id)
                ->whereNotNull('refunded_at')->whereKeyNot($locked->id)->sum('refund_amount');
            abort_if((float) $locked->refund_amount <= 0
                || round($alreadyRefunded + (float) $locked->refund_amount, 2) > $paidMerchandise,
                422, 'Refund exceeds the remaining paid merchandise amount.');
            $locked->forceFill(['status' => 'refunded', 'refunded_at' => now()])->save();
            // Credit the buyer only after the refund is actually completed, not when merely approved.
            $refundIsRiderCollected = DB::table('rider_cod_collections')
                ->where('order_return_id', $locked->id)->exists();
            if ((float) $locked->refund_amount > 0 && ! $refundIsRiderCollected) {
                app(BuyerWalletService::class)->move((int)$locked->order->buyer_id, 'refund_credit',
                    $locked->refund_amount, 'order_return', (int)$locked->id, 'Refund for order '.$locked->order->order_number);
            }
            app(CodSettlementService::class)->reverseSellerEarningsForRefund($locked);
            app(\App\Services\SellerWalletService::class)->recordRefund($locked->order->seller, $locked);
            $locked->events()->create(['status' => 'refunded', 'location' => 'Seller refund processing', 'scanned_at' => now()]);
            $refunded = $locked->fresh(['order.buyer:id,name,email', 'order', 'events']);
            if ($refunded->order?->buyer) {
                BuyerNotification::createForBuyer(
                    $refunded->order->buyer,
                    'refund_update',
                    $refundIsRiderCollected ? 'Refund awaiting hub distribution' : 'Refund completed',
                    $refundIsRiderCollected
                        ? 'The seller confirmed your return. Your refund will reach your ShopEase Wallet after the delivery hub distributes the rider remittance.'
                        : 'Your refund for order '.$refunded->order->order_number.' has been completed.',
                    ['return_id' => $refunded->id, 'status' => 'refunded', 'url' => '/buyer/my-purchases?tab=returns'],
                    $refunded->order,
                );
            }
            return response()->json(['data' => $refunded]);
        });
    }

    public function confirmReceipt(Request $request, OrderReturn $return)
    {
        $seller = Seller::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($return->order()->where('seller_id', $seller->id)->exists(), 404);

        return DB::transaction(function () use ($request, $return): \Illuminate\Http\JsonResponse {
            $locked = OrderReturn::query()->whereKey($return->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'accepted' && $locked->return_type === 'return', 409, 'Only an accepted return can be receipt-confirmed.');
            abort_unless($locked->shipping_status === 'delivered' || $locked->shipping_status === 'returned', 409, 'The rider must mark the return delivered to the seller first.');

            if (! $locked->returned_at) {
                $locked->forceFill(['returned_at' => now()])->save();
                $locked->events()->create([
                    'status' => 'seller_confirmed_received',
                    'location' => 'Receipt confirmed by '.$request->user()->name,
                    'scanned_at' => now(),
                ]);
            }

            $refundReleased = app(CodSettlementService::class)->releaseHeldReturnRefund($locked);

            $confirmed = $locked->fresh(['order.buyer:id,name,email', 'order', 'events']);
            if (! $refundReleased && $confirmed->order?->buyer) {
                BuyerNotification::createForBuyer(
                    $confirmed->order->buyer,
                    'return_update',
                    'Returned parcel received',
                    'The seller confirmed receipt of your returned parcel. Your refund will be credited to your ShopEase Wallet after the delivery hub distributes the held return funds.',
                    ['return_id' => $confirmed->id, 'status' => $confirmed->status, 'shipping_status' => $confirmed->shipping_status, 'url' => '/buyer/my-purchases?tab=returns'],
                    $confirmed->order,
                );
            }

            return response()->json(['data' => $confirmed]);
        });
    }
}
