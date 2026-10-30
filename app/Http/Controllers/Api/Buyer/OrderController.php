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
use App\Notifications\NewOrderNotification;
use App\Services\OrderInventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderInventoryService $inventory
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->where('buyer_id', $request->user()->id)
            ->with([
                'seller:id,store_name,province,municipality,barangay,street,house_number,building,subdivision',
                'items.product',
                'complaints',
                'latestCancellationRequest',
                'statusHistory',
                'shipment.scans',
                'shipment.rider.logisticsBranch',
            ])
            ->latest()
            ->paginate(50);
        $statusCounts = Order::query()
            ->where('buyer_id', $request->user()->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'data' => $orders->getCollection()->map(fn (Order $order): array => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'qr_payload' => url('/api/v1/orders/verify/'.rawurlencode($order->order_number)),
                'shop' => $order->seller->store_name,
                'pickup_address' => collect([
                    $order->seller->house_number,
                    $order->seller->street,
                    $order->seller->building,
                    $order->seller->subdivision,
                    $order->seller->barangay,
                    $order->seller->municipality,
                    $order->seller->province,
                ])->filter()->implode(', '),
                'delivery_address' => $order->delivery_address,
                'total' => (float) $order->total,
                'status' => $order->status,
                'can_cancel' => in_array($order->status, ['pending', 'new', 'preparing', 'to_ship'], true)
                    && $order->latestCancellationRequest?->status !== 'pending',
                'cancellation_request' => $order->latestCancellationRequest,
                'created_at' => $order->created_at?->toISOString(),
                'shipment' => $order->shipment ? [
                    'tracking_number' => $order->shipment->tracking_number,
                    'courier' => $order->shipment->courier,
                    'estimated_delivery' => $order->shipment->estimated_delivery?->toDateString(),
                    'current_location' => $order->shipment->current_location,
                    'current_latitude' => $order->shipment->current_latitude,
                    'current_longitude' => $order->shipment->current_longitude,
                    'location_updated_at' => $order->shipment->location_updated_at?->toISOString(),
                    'logistics_stop' => $order->shipment->rider?->logisticsBranch?->address,
                    'logistics_stop_name' => $order->shipment->rider?->logisticsBranch?->name,
                    'rider' => $order->shipment->rider ? [
                        'name' => trim($order->shipment->rider->first_name.' '.$order->shipment->rider->last_name),
                        'contact_no' => $order->shipment->rider->contact_no,
                    ] : null,
                ] : null,
                'tracking_events' => $order->statusHistory
                    ->map(fn ($event): array => [
                        'status' => $event->to_status,
                        'description' => str_replace('_', ' ', ucfirst($event->to_status)),
                        'created_at' => $event->created_at?->toISOString(),
                    ])
                    ->concat($order->shipment?->scans->map(fn ($scan): array => [
                        'status' => $scan->status,
                        'description' => $scan->status.' · '.$scan->location,
                        'created_at' => $scan->scanned_at?->toISOString(),
                    ]) ?? collect())
                    ->sortByDesc('created_at')
                    ->values(),
                'complaint' => $order->complaints->first() ? [
                    'id' => $order->complaints->first()->id,
                    'reference' => 'CMP-'.str_pad((string) $order->complaints->first()->id, 4, '0', STR_PAD_LEFT),
                    'type' => $order->complaints->first()->type,
                    'status' => str_replace('_', '-', $order->complaints->first()->status),
                ] : null,
                'items' => $order->items->map(fn ($item): array => [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'image_url' => $this->productImageUrl($item->product?->photos[0] ?? null),
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'variation' => $item->variation,
                    'color' => $item->color,
                    'size' => $item->size,
                ])->all(),
            ])->values(),
            'total' => $orders->total(),
            'status_counts' => $statusCounts,
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
            'cart_item_ids' => ['sometimes', 'array', 'min:1'],
            'cart_item_ids.*' => ['integer', 'distinct'],
            'checkout_items' => ['sometimes', 'array', 'min:1'],
            'checkout_items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'checkout_items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'checkout_items.*.variation' => ['nullable', 'string', 'max:100'],
            'checkout_items.*.color' => ['nullable', 'string', 'max:100'],
            'checkout_items.*.size' => ['nullable', 'string', 'max:100'],
        ]);

        $orders = DB::transaction(function () use ($request, $validated) {
            $checkoutCartItemIds = $validated['cart_item_ids'] ?? [];
            foreach ($validated['checkout_items'] ?? [] as $checkoutItem) {
                $cartItem = CartItem::create([
                    'user_id' => $request->user()->id,
                    'product_id' => $checkoutItem['product_id'],
                    'quantity' => $checkoutItem['quantity'],
                    'variation' => $checkoutItem['variation'] ?? null,
                    'color' => $checkoutItem['color'] ?? null,
                    'size' => $checkoutItem['size'] ?? null,
                ]);
                $checkoutCartItemIds[] = $cartItem->id;
            }

            $itemsQuery = CartItem::query()
                ->where('user_id', $request->user()->id)
                ->with(['product.seller']);
            if ($checkoutCartItemIds !== []) {
                $itemsQuery->whereIn('id', $checkoutCartItemIds);
            }
            $items = $itemsQuery
                ->orderBy('product_id')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($items->isEmpty()) {
                throw new HttpException(422, 'Your cart is empty. Add items before placing an order.');
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
                    throw new HttpException(422, 'One or more cart items are no longer available. Refresh your cart and try again.');
                }
            }

            $orders = $items->groupBy(fn (CartItem $item) => $item->product->seller_id)
                ->map(function ($sellerItems) use ($validated, $request): Order {
                    $seller = $sellerItems->first()->product->seller;
                    $total = $sellerItems->sum(fn (CartItem $item) => (float) $item->product->price * $item->quantity);

                    $order = Order::create([
                        'buyer_id' => $request->user()->id,
                        'seller_id' => $seller->id,
                        'order_number' => null,
                        'total' => $total,
                        'commission_amount' => round($total * (CommissionTransaction::DEFAULT_RATE / 100), 2),
                        'status' => 'pending',
                        'delivery_name' => $validated['delivery_name'],
                        'delivery_phone' => $validated['delivery_phone'],
                        'delivery_address' => $validated['delivery_address'],
                        'payment_method' => $validated['payment_method'],
                    ]);
                    $order->forceFill([
                        'order_number' => $this->orderNumberFor($order),
                    ])->saveQuietly();
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

                    $order->load(['seller:id,store_name', 'items.product']);

                    // Notify the seller — fires after items are persisted so
                    // the notification can include an accurate item count.
                    $seller->user->notify(new NewOrderNotification($order));

                    return $order;
                })
                ->values();

            CartItem::where('user_id', $request->user()->id)
                ->whereIn('id', $items->pluck('id'))
                ->delete();

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
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'image_url' => $this->productImageUrl($item->product?->photos[0] ?? null),
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                ])->all(),
            ])->all(),
        ], 201);
    }

    private function productImageUrl(?string $photo): ?string
    {
        if ($photo === null || trim($photo) === '') {
            return null;
        }

        $photo = trim($photo);
        if (
            str_starts_with($photo, 'data:') ||
            str_starts_with($photo, 'http://') ||
            str_starts_with($photo, 'https://')
        ) {
            return $photo;
        }

        $path = ltrim(preg_replace('/^storage\//', '', $photo), '/');

        return $path !== '' ? Storage::disk('public')->url($path) : null;
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
        $order->update([
            'status' => 'cancelled',
            'pickup_date' => null,
            'pickup_time' => null,
        ]);
        $order->shipment()->update(['rider_id' => null]);

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

    private function orderNumberFor(Order $order): string
    {
        return 'SE-'.now()->format('Ymd').'-'.str_pad((string) $order->id, 8, '0', STR_PAD_LEFT);
    }
}
