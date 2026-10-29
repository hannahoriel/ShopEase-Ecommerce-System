<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Seller\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReturnsRefundsController extends Controller
{
    public function page(Request $request)
    {
        $seller = Seller::query()->where('user_id', $request->user()->id)->firstOrFail();
        $records = OrderReturn::query()->whereHas('order', fn ($q) => $q->where('seller_id', $seller->id))
            ->with(['order.buyer:id,name,email', 'order.items', 'events'])->latest('requested_at')->get();
        $present = fn (OrderReturn $return) => [
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
            'date' => $return->requested_at,
            'updated_at' => $return->updated_at,
            'description' => $return->reason,
            'solution' => $return->solution,
            'amount' => $return->refund_amount ?? $return->order->total,
            'evidence' => $return->evidence ?? [],
            'refunded_at' => $return->refunded_at,
            'approved_at' => $return->reviewed_at,
            'note' => $return->solution,
        ];

        $shipping = $records->filter(fn (OrderReturn $return) => filled($return->shipping_status) || filled($return->tracking_number))
            ->map(fn (OrderReturn $return) => [
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
            return response()->json(['data' => $locked->fresh(['order.buyer:id,name,email', 'order.items', 'events'])]);
        });
    }

    public function completeRefund(Request $request, OrderReturn $return)
    {
        $seller = Seller::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($return->order()->where('seller_id', $seller->id)->exists(), 404);
        abort_unless($return->return_type === 'refund' || in_array($return->shipping_status, ['delivered', 'returned'], true), 409, 'A return must be received before refund completion.');

        return DB::transaction(function () use ($return) {
            $locked = OrderReturn::query()->whereKey($return->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'accepted', 409, 'Only accepted requests can be refunded.');
            $locked->forceFill(['status' => 'refunded', 'refunded_at' => now()])->save();
            $locked->events()->create(['status' => 'refunded', 'location' => 'Seller refund processing', 'scanned_at' => now()]);
            return response()->json(['data' => $locked->fresh(['events'])]);
        });
    }
}
