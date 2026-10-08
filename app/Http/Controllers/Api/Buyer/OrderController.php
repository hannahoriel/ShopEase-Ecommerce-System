<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\CommissionTransaction;
use App\Models\Admin\Complaint;
use App\Models\Admin\ComplaintUpdate;
use App\Models\Admin\Order;
use App\Models\Admin\OrderCancellationRequest;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Buyer\BuyerNotification;
use App\Models\Buyer\CartItem;
use App\Models\Seller\Product;
use App\Services\OrderInventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderInventoryService $inventory
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->where('buyer_id', $request->user()->id)
            ->with(['seller:id,store_name', 'items', 'complaints', 'latestCancellationRequest'])
            ->latest()
            ->paginate(50);

        return response()->json([
            'data' => $orders->getCollection()->map(fn (Order $order): array => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'qr_payload' => url('/api/v1/orders/verify/'.rawurlencode($order->order_number)),
                'shop' => $order->seller->store_name,
                'total' => (float) $order->total,
                'status' => $order->status,
                'can_cancel' => in_array($order->status, ['pending', 'new', 'preparing', 'to_ship'], true)
                    && $order->latestCancellationRequest?->status !== 'pending',
                'cancellation_request' => $order->latestCancellationRequest,
                'created_at' => $order->created_at?->toISOString(),
                'complaint' => $order->complaints->first() ? [
                    'reference' => 'CMP-'.str_pad((string) $order->complaints->first()->id, 4, '0', STR_PAD_LEFT),
                    'type' => $order->complaints->first()->type,
                    'status' => str_replace('_', '-', $order->complaints->first()->status),
                ] : null,
                'items' => $order->items->map(fn ($item): array => [
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'variation' => $item->variation,
                    'color' => $item->color,
                    'size' => $item->size,
                ])->all(),
            ])->values(),
            'total' => $orders->total(),
        ]);
    }

    public function storeComplaint(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->buyer_id === $request->user()->id, 404);

        $validated = $request->validate([
            'type' => ['required', Rule::in([
                'Wrong Item',
                'Missing Item',
                'Damaged Item',
                'Late Delivery',
                'Order Not Received',
                'Seller Conduct',
                'Other',
            ])],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $complaint = DB::transaction(function () use ($request, $order, $validated): Complaint {
            $complaint = Complaint::create([
                'user_id' => $request->user()->id,
                'order_id' => $order->id,
                'subject' => $validated['type'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'status' => 'open',
            ]);

            ComplaintUpdate::create([
                'complaint_id' => $complaint->id,
                'user_id' => $request->user()->id,
                'type' => 'submitted',
                'message' => 'Complaint submitted by '.$request->user()->name.'.',
            ]);

            return $complaint;
        });

        return response()->json([
            'message' => 'Your complaint has been submitted to ShopEase Support.',
            'data' => [
                'id' => $complaint->id,
                'reference' => 'CMP-'.str_pad((string) $complaint->id, 4, '0', STR_PAD_LEFT),
                'type' => $complaint->type,
                'status' => 'open',
                'created_at' => $complaint->created_at?->toISOString(),
            ],
        ], 201);
    }

    public function verify(string $orderNumber): JsonResponse
    {
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->first();

        abort_unless($order, 404);

        return response()->json([
            'valid' => true,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'placed_at' => $order->created_at?->toISOString(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'delivery_name' => ['required', 'string', 'max:255'],
            'delivery_phone' => ['required', 'string', 'max:30'],
            'delivery_address' => ['required', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:Cash on Delivery'],
        ]);

        $orders = DB::transaction(function () use ($request, $validated) {
            $items = CartItem::query()
                ->where('user_id', $request->user()->id)
                ->with(['product.seller'])
                ->orderBy('product_id')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => ['Your cart is empty. Add items before placing an order.'],
                ]);
            }

            foreach ($items as $item) {
                $product = Product::query()
                    ->with('seller')
                    ->lockForUpdate()
                    ->find($item->product_id);
                $item->setRelation('product', $product);

                if (
                    ! $product ||
                    $product->status !== 'active' ||
                    $product->is_archived ||
                    ! $product->seller ||
                    $product->seller->registration_status !== 'active'
                ) {
                    throw ValidationException::withMessages([
                        'cart' => ['One or more cart items are no longer available. Refresh your cart and try again.'],
                    ]);
                }
            }

            $orders = $items->groupBy(fn (CartItem $item) => $item->product->seller_id)
                ->map(function ($sellerItems) use ($validated, $request): Order {
                    $seller = $sellerItems->first()->product->seller;
                    $total = $sellerItems->sum(fn (CartItem $item) => (float) $item->product->price * $item->quantity);

                    $order = Order::create([
                        'buyer_id' => $request->user()->id,
                        'seller_id' => $seller->id,
                        'order_number' => $this->newOrderNumber(),
                        'total' => $total,
                        'commission_amount' => round($total * (CommissionTransaction::DEFAULT_RATE / 100), 2),
                        'status' => 'pending',
                        'delivery_name' => $validated['delivery_name'],
                        'delivery_phone' => $validated['delivery_phone'],
                        'delivery_address' => $validated['delivery_address'],
                        'payment_method' => $validated['payment_method'],
                    ]);
                    BuyerNotification::createForOrder(
                        $order,
                        'order',
                        'Order placed successfully',
                        'Your order '.$order->order_number.' has been placed.',
                        ['status' => $order->status]
                    );

                    foreach ($sellerItems as $item) {
                        $orderItem = $order->items()->create([
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name,
                            'variation' => $item->variation,
                            'color' => $item->color,
                            'size' => $item->size,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->product->price,
                        ]);

                        $this->inventory->reserve($orderItem);
                    }

                    return $order->load(['seller:id,store_name', 'items']);
                })
                ->values();

            CartItem::where('user_id', $request->user()->id)->delete();

            return $orders;
        });

        return response()->json([
            'message' => 'Order placed successfully.',
            'orders' => $orders->map(fn (Order $order): array => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'qr_payload' => url('/api/v1/orders/verify/'.rawurlencode($order->order_number)),
                'shop' => $order->seller->store_name,
                'total' => (float) $order->total,
                'status' => $order->status,
                'created_at' => $order->created_at?->toISOString(),
                'items' => $order->items->map(fn ($item): array => [
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                ])->all(),
            ])->all(),
        ], 201);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->buyer_id === $request->user()->id, 404);

        $validated = $request->validate([
            'reason' => ['required', Rule::in(OrderCancellationRequest::BUYER_REASONS)],
            'other_reason' => ['required_if:reason,other', 'nullable', 'string', 'max:500'],
        ]);

        $result = DB::transaction(function () use ($request, $order, $validated): array {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            abort_unless($lockedOrder->buyer_id === $request->user()->id, 404);

            if ($lockedOrder->status === 'cancelled') {
                return [
                    'order' => $lockedOrder,
                    'request' => $lockedOrder->latestCancellationRequest,
                    'auto_cancelled' => false,
                    'already_cancelled' => true,
                ];
            }

            abort_unless(
                in_array($lockedOrder->status, ['pending', 'new', 'preparing', 'to_ship'], true),
                422,
                'This order can no longer be cancelled.'
            );

            $pendingRequest = $lockedOrder->cancellationRequests()
                ->where('status', 'pending')
                ->latest('id')
                ->first();

            if ($pendingRequest) {
                return [
                    'order' => $lockedOrder,
                    'request' => $pendingRequest,
                    'auto_cancelled' => false,
                    'already_cancelled' => false,
                ];
            }

            $autoCancelled = $lockedOrder->created_at !== null
                && now()->lessThanOrEqualTo($lockedOrder->created_at->copy()->addHours(5));
            $cancellationRequest = OrderCancellationRequest::create([
                'order_id' => $lockedOrder->id,
                'buyer_id' => $lockedOrder->buyer_id,
                'status' => $autoCancelled ? 'approved' : 'pending',
                'buyer_reason' => $validated['reason'],
                'buyer_other_reason' => $validated['reason'] === 'other'
                    ? trim($validated['other_reason'])
                    : null,
                'auto_approved' => $autoCancelled,
                'requested_at' => now(),
                'decided_at' => $autoCancelled ? now() : null,
            ]);

            if ($autoCancelled) {
                $this->finalizeBuyerCancellation($lockedOrder, $cancellationRequest);
            }

            return [
                'order' => $lockedOrder,
                'request' => $cancellationRequest,
                'auto_cancelled' => $autoCancelled,
                'already_cancelled' => false,
            ];
        });

        $cancelled = $result['auto_cancelled'];
        $alreadyCancelled = $result['already_cancelled'];

        return response()->json([
            'message' => $alreadyCancelled
                ? 'This order has already been cancelled.'
                : ($cancelled
                        ? 'Order cancelled and stock restored.'
                        : 'Cancellation request sent to seller.'),
            'data' => [
                'id' => $result['order']->id,
                'order_number' => $result['order']->order_number,
                'status' => $result['order']->status,
                'can_cancel' => false,
                'auto_cancelled' => $cancelled,
                'cancellation_request' => $result['request'],
            ],
        ], $cancelled || $alreadyCancelled ? 200 : 202);
    }

    private function finalizeBuyerCancellation(Order $order, OrderCancellationRequest $cancellationRequest): void
    {
        $this->inventory->restoreOrder($order);

        $previousStatus = $order->status;
        $order->update(['status' => 'cancelled']);

        $reason = $cancellationRequest->buyer_reason === 'other'
            ? 'Other: '.$cancellationRequest->buyer_other_reason
            : $cancellationRequest->buyer_reason;

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $previousStatus,
            'to_status' => 'cancelled',
            'changed_by' => $cancellationRequest->buyer_id,
            'notes' => 'Cancelled by buyer within five hours of placement. Reason: '.$reason,
        ]);
    }

    private function newOrderNumber(): string
    {
        do {
            $number = 'SE-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
