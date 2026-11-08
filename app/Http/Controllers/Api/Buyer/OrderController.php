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
use App\Models\Seller\ProductReview;
use App\Models\Seller\Seller;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use Illuminate\Support\Collection;
use App\Notifications\NewOrderNotification;
use App\Services\OrderInventoryService;
use App\Services\SellerVoucherService;
use App\Services\BuyerWalletService;
use App\Services\CodSettlementService;
use App\Services\LogisticsNotificationService;
use App\Services\SellerNotificationPreferences;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderController extends Controller
{
    public const SHIPPING_FEE_PER_STORE = 50.00;

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
                'logisticsCompany',
            ])
            ->latest()
            ->paginate(50);
        $statusCounts = Order::query()
            ->where('buyer_id', $request->user()->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        $reviewedProductIds = ProductReview::query()
            ->where('buyer_id', $request->user()->id)
            ->pluck('product_id')->map(fn ($id): string => (string) $id)->all();

        return response()->json([
            'data' => $orders->getCollection()->map(fn (Order $order) => $this->presentOrder($order, $reviewedProductIds))->values(),
            'total' => $orders->total(),
            'status_counts' => $statusCounts,
        ]);
    }

    public function presentOrder(Order $order, array $reviewedProductIds = []): array
    {
                return [
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
                'delivery_name' => $order->delivery_name,
                'delivery_phone' => $order->delivery_phone,
                'payment_method' => $order->payment_method,
                'total' => (float) $order->total,
                'shipping_fee' => (float) $order->shipping_fee,
                'voucher_discount' => (float) $order->voucher_discount,
                'voucher_code' => $order->voucher_code,
                'status' => $order->status,
                'buyer_received_at' => $order->buyer_received_at?->toISOString(),
                'buyer_received_automatically' => $order->buyer_received_automatically,
                'can_confirm_received' => $order->status === 'delivered',
                'can_cancel' => in_array($order->status, ['pending', 'new', 'preparing', 'to_ship'], true)
                    && $order->latestCancellationRequest?->status !== 'pending',
                'cancellation_request' => $order->latestCancellationRequest,
                'created_at' => $order->created_at?->toISOString(),
                'shipment' => ($order->shipment || $order->logisticsCompany) ? [
                    'tracking_number' => $order->shipment?->tracking_number,
                    'courier' => $order->logisticsCompany?->business_name ?: $order->shipment?->courier,
                    'estimated_delivery' => $order->shipment?->estimated_delivery?->toDateString(),
                    'current_location' => in_array($order->status, ['delivered', 'completed'], true)
                        ? 'Delivered'
                        : $order->shipment?->current_location,
                    'current_latitude' => $order->shipment?->current_latitude,
                    'current_longitude' => $order->shipment?->current_longitude,
                    'location_updated_at' => $order->shipment?->location_updated_at?->toISOString(),
                    'logistics_stop' => $order->shipment?->rider?->logisticsBranch?->address,
                    'logistics_stop_name' => $order->shipment?->rider?->logisticsBranch?->name,
                    'rider' => $order->shipment?->rider ? [
                        'name' => trim($order->shipment->rider->first_name.' '.$order->shipment->rider->last_name),
                        'contact_no' => $order->shipment->rider->contact_no,
                    ] : null,
                ] : null,
                'tracking_events' => $order->statusHistory
                    ->map(fn ($event): array => [
                        'status' => $event->to_status,
                        'description' => str_replace('_', ' ', ucfirst($event->to_status)),
                        'location' => null,
                        'created_at' => $event->created_at?->toISOString(),
                    ])
                    ->concat($order->shipment?->scans->map(fn ($scan): array => [
                        'status' => $scan->status,
                        'location' => $scan->location,
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
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'is_fragile' => (bool) ($item->is_fragile || $item->product?->is_fragile),
                    'image_url' => ($item->product?->selectedImageForCart($item->variation, $item->color, $item->size) ?: $this->productImageUrl($item->product?->photos[0] ?? null)),
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'variation' => $item->variation,
                    'color' => $item->color,
                    'size' => $item->size,
                    'can_review' => $order->status === 'completed'
                        && $item->product_id !== null
                        && ! in_array((string) $item->product_id, $reviewedProductIds, true),
                ])->all(),
                ];
    }

    public function confirmReceived(Request $request, Order $order): JsonResponse
    {
        abort_unless((int) $order->buyer_id === (int) $request->user()->id, 404);

        $received = DB::transaction(function () use ($request, $order): Order {
            $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            abort_unless((int) $lockedOrder->buyer_id === (int) $request->user()->id, 404);
            abort_unless($lockedOrder->status === 'delivered', 422, 'Only delivered orders can be confirmed as received.');

            $shipment = $lockedOrder->shipment()->first();
            $deliveredAt = $shipment?->delivered_at ?: OrderStatusHistory::query()
                ->where('order_id', $lockedOrder->id)
                ->where('to_status', 'delivered')
                ->latest('created_at')
                ->value('created_at');
            abort_unless($deliveredAt, 422, 'The delivery timestamp is not available for this order yet.');

            $lockedOrder->update([
                'status' => 'completed',
                'buyer_received_at' => now(),
                'buyer_received_automatically' => false,
            ]);
            OrderStatusHistory::query()->create([
                'order_id' => $lockedOrder->id,
                'from_status' => 'delivered',
                'to_status' => 'completed',
                'changed_by' => $request->user()->id,
                'notes' => 'Buyer confirmed receipt of the order.',
            ]);
            BuyerNotification::createForOrderStatus($lockedOrder, 'completed');
            app(CodSettlementService::class)->releaseSellerEarnings((int) $lockedOrder->id);

            return $lockedOrder->fresh();
        });

        return response()->json([
            'message' => 'Order marked as received. Thank you for confirming!',
            'order' => [
                'id' => $received->id,
                'order_number' => $received->order_number,
                'status' => $received->status,
                'buyer_received_at' => $received->buyer_received_at?->toISOString(),
                'buyer_received_automatically' => $received->buyer_received_automatically,
            ],
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
            'evidence' => ['sometimes', 'array', 'max:5'],
            'evidence.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $evidencePaths = collect($request->file('evidence', []))
            ->map(fn ($file): string => $file->store('buyer-complaints', 'public'))
            ->all();

        $complaint = DB::transaction(function () use ($request, $order, $validated, $evidencePaths): Complaint {
            $complaint = Complaint::create([
                'user_id' => $request->user()->id,
                'order_id' => $order->id,
                'subject' => $validated['type'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'evidence' => $evidencePaths,
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

    public function shippingOptions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'province' => ['required', 'string', 'max:150'],
            'municipality' => ['required', 'string', 'max:150'],
            'seller_ids' => ['sometimes', 'array', 'min:1'],
            'seller_ids.*' => ['required', 'integer', 'distinct', 'exists:sellers,id'],
        ]);
        $cartItems = CartItem::query()
            ->where('user_id', $request->user()->id)
            ->with('product.seller')
            ->get();

        $sellerIds = $cartItems
            ->filter(fn (CartItem $item) => $item->product?->seller)
            ->map(fn (CartItem $item) => (int) $item->product->seller_id)
            ->merge(array_map('intval', $validated['seller_ids'] ?? []))
            ->unique()
            ->values();
        $sellers = Seller::query()
            ->where('registration_status', 'active')
            ->whereIn('id', $sellerIds)
            ->get();
        $providers = $this->approvedLogisticsProviders();
        $data = $sellers
            ->map(function (Seller $seller) use ($providers, $validated): array {
                $options = $providers
                    ->filter(fn (Logistics $provider) => $this->coversLocations(
                        $provider,
                        [
                            ['province' => $validated['province'], 'municipality' => $validated['municipality']],
                            ['province' => $seller->province, 'municipality' => $seller->municipality],
                        ]
                    ))
                    ->map(fn (Logistics $provider): array => [
                        'id' => $provider->id,
                        'name' => $provider->business_name ?: 'Logistics company',
                    ])
                    ->values();

                return [
                    'seller_id' => (int) $seller->id,
                    'seller_name' => $seller->store_name,
                    'options' => $options,
                ];
            })
            ->values();

        return response()->json(['data' => $data]);
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
            'delivery_province' => ['required', 'string', 'max:150'],
            'delivery_municipality' => ['required', 'string', 'max:150'],
            'payment_method' => ['required', 'in:Cash on Delivery,ShopEase Wallet'],
            'logistics_selections' => ['required', 'array', 'min:1'],
            'logistics_selections.*.seller_id' => ['required', 'integer', 'distinct', 'exists:sellers,id'],
            'logistics_selections.*.logistics_id' => ['required', 'integer', 'exists:logistics,id'],
            'voucher_code' => ['nullable', 'string', 'max:20'],
            'voucher_seller_id' => ['nullable', 'integer', 'exists:sellers,id'],
            'cart_item_ids' => ['sometimes', 'array', 'min:1'],
            'cart_item_ids.*' => ['integer', 'distinct'],
            'checkout_items' => ['sometimes', 'array', 'min:1'],
            'checkout_items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'checkout_items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'checkout_items.*.variation' => ['nullable', 'string', 'max:100'],
            'checkout_items.*.color' => ['nullable', 'string', 'max:100'],
            'checkout_items.*.size' => ['nullable', 'string', 'max:100'],
        ]);

        // Ensure a lockable wallet record exists before transaction begins.
        if ($validated['payment_method'] === 'ShopEase Wallet') {
            app(BuyerWalletService::class)->account((int)$request->user()->id);
        }
        $orders = DB::transaction(function () use ($request, $validated) {
            $checkoutCartItemIds = $validated['cart_item_ids'] ?? [];
            $itemsQuery = CartItem::query()
                ->where('user_id', $request->user()->id)
                ->with(['product.seller']);
            // Only use explicitly selected cart rows. Buy Now items are added as
            // in-memory rows below so they cannot collide with a matching cart row.
            $itemsQuery->whereIn('id', $checkoutCartItemIds);
            $items = $itemsQuery
                ->orderBy('product_id')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($validated['checkout_items'] ?? [] as $checkoutItem) {
                $items->push(new CartItem([
                    'user_id' => $request->user()->id,
                    'product_id' => $checkoutItem['product_id'],
                    'quantity' => $checkoutItem['quantity'],
                    'variation' => $checkoutItem['variation'] ?? null,
                    'color' => $checkoutItem['color'] ?? null,
                    'size' => $checkoutItem['size'] ?? null,
                ]));
            }

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

            $cartSellerIds = $items
                ->map(fn (CartItem $item): int => (int) $item->product->seller_id)
                ->unique()
                ->sort()
                ->values()
                ->all();
            $selectedSellerIds = collect($validated['logistics_selections'])
                ->pluck('seller_id')
                ->map(fn ($id): int => (int) $id)
                ->sort()
                ->values()
                ->all();
            if ($cartSellerIds !== $selectedSellerIds) {
                throw ValidationException::withMessages([
                    'logistics_selections' => ['Choose one logistics company for every shop in your order.'],
                ]);
            }

            if ($validated['payment_method'] === 'ShopEase Wallet') {
                $walletBalance = DB::table('buyer_wallets')
                    ->where('user_id', $request->user()->id)
                    ->lockForUpdate()
                    ->value('balance');
                if ((float) $walletBalance <= 0) {
                    throw ValidationException::withMessages([
                        'payment_method' => ['Your ShopEase Wallet balance is zero. Choose Cash on Delivery.'],
                    ]);
                }
            }

            $voucherSellerId = null;
            if (! empty($validated['voucher_code'])) {
                if ($validated['payment_method'] !== 'Cash on Delivery') {
                    throw ValidationException::withMessages([
                        'payment_method' => ['Voucher codes are currently valid for Cash on Delivery orders only.'],
                    ]);
                }
                $voucherSellerId = app(SellerVoucherService::class)->sellerFor(
                    $validated['voucher_code'], $request->user(), $items,
                    !empty($validated['voucher_seller_id']) ? (int) $validated['voucher_seller_id'] : null,
                );
            }

            $approvedProviders = $this->approvedLogisticsProviders();
            $orders = $items->groupBy(fn (CartItem $item) => $item->product->seller_id)
                ->map(function ($sellerItems, $sellerId) use ($validated, $request, $voucherSellerId, $approvedProviders): Order {
                    $seller = $sellerItems->first()->product->seller;
                    $providerSelection = collect($validated['logistics_selections'] ?? [])
                        ->first(fn (array $selection) => (int) $selection['seller_id'] === (int) $sellerId);
                    if (! $providerSelection) {
                        throw ValidationException::withMessages([
                            'logistics_selections' => ['Choose a logistics company for every shop in your order.'],
                        ]);
                    }
                    $logistics = $approvedProviders->first(fn (Logistics $provider) =>
                            (int) $provider->id === (int) $providerSelection['logistics_id']
                            && $this->coversLocations($provider, [
                                ['province' => $validated['delivery_province'], 'municipality' => $validated['delivery_municipality']],
                                ['province' => $seller->province, 'municipality' => $seller->municipality],
                            ])
                        );
                    if (! $logistics) {
                        throw ValidationException::withMessages([
                            'logistics_selections' => ['Choose an approved logistics company with an active hub or sorting center at both delivery locations.'],
                        ]);
                    }
                    $subtotal = $sellerItems->sum(fn (CartItem $item) => $item->product->salePriceForCart($item->variation, $item->color, $item->size) * $item->quantity);
                    $shippingFee = self::SHIPPING_FEE_PER_STORE;
                    $voucherResult = null;
                    if (! empty($validated['voucher_code']) && (int) $sellerId === (int) $voucherSellerId) {
                        $voucherResult = app(SellerVoucherService::class)->evaluate($validated['voucher_code'], $request->user(), (int) $sellerId, $sellerItems, true);
                    }
                    $voucherDiscount = $voucherResult['discount'] ?? 0;
                    $total = max(0, $subtotal + $shippingFee - $voucherDiscount);
                    if ($validated['payment_method'] === 'ShopEase Wallet' && $total <= 0) {
                        throw ValidationException::withMessages([
                            'payment_method' => ['ShopEase Wallet can only be used for an order with a positive total.'],
                        ]);
                    }

                    $order = Order::create([
                        'buyer_id' => $request->user()->id,
                        'seller_id' => $seller->id,
                        // Buyers can place the order before a delivery provider is chosen.
                        // The seller's To Ship flow creates the pickup shipment and assigns
                        // a rider from the seller's dispatch area.
                        'logistics_id' => $logistics?->id,
                        'order_number' => null,
                        'total' => $total,
                        'shipping_fee' => $shippingFee,
                        'voucher_discount' => $voucherDiscount,
                        'voucher_code' => $voucherResult['voucher']->code ?? null,
                        'voucher_type' => $voucherResult ? ($voucherResult['voucher']->voucher_type ?? 'merchandise') : null,
                        'voucher_funding_source' => $voucherResult ? ($voucherResult['voucher']->funding_source ?? ($voucherResult['voucher']->seller_id === null ? 'platform' : 'seller')) : null,
                        'commission_amount' => round($total * (CommissionTransaction::DEFAULT_RATE / 100), 2),
                        'status' => 'pending',
                        'delivery_name' => $validated['delivery_name'],
                        'delivery_phone' => $validated['delivery_phone'],
                        'delivery_address' => $validated['delivery_address'],
                        'payment_method' => $validated['payment_method'],
                    ]);
                    if ($validated['payment_method'] === 'ShopEase Wallet') {
                        app(BuyerWalletService::class)->move((int)$request->user()->id, 'order_payment',
                            $total, 'order', (int)$order->id, 'Wallet payment for order '.$order->id);
                    }
                    $order->forceFill([
                        'order_number' => $this->orderNumberFor($order),
                    ])->saveQuietly();
                    if ($voucherResult) {
                        $voucher = $voucherResult['voucher'];
                        $claimed = \App\Models\Seller\SellerVoucher::query()
                            ->whereKey($voucher->id)
                            ->whereColumn('claimed', '<', 'quantity')
                            ->increment('claimed');
                        if (! $claimed) {
                            throw ValidationException::withMessages([
                                'voucher_code' => ['This voucher has reached its allocation limit.'],
                            ]);
                        }
                        $voucher->redemptions()->create([
                            'buyer_id' => $request->user()->id,
                            'order_id' => $order->id,
                            'discount_amount' => $voucherDiscount,
                        ]);
                    }
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
                            'is_fragile' => (bool) $item->product->is_fragile,
                            'variation' => $item->variation,
                            'color' => $item->color,
                            'size' => $item->size,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->product->salePriceForCart($item->variation, $item->color, $item->size),
                        ]);

                        $this->inventory->reserve($orderItem);
                    }

                    $order->load(['seller:id,store_name', 'items.product']);

                    // Notify the seller — fires after items are persisted so
                    // the notification can include an accurate item count.
                    app(SellerNotificationPreferences::class)->notify(
                        $seller->user,
                        new NewOrderNotification($order),
                    );

                    return $order;
                })
                ->values();

            $purchasedCartItemIds = $items
                ->filter(fn (CartItem $item): bool => $item->exists)
                ->pluck('id');
            if ($purchasedCartItemIds->isNotEmpty()) {
                CartItem::where('user_id', $request->user()->id)
                    ->whereIn('id', $purchasedCartItemIds)
                    ->delete();
            }

            return $orders;
        });

        foreach ($orders as $order) {
            $logisticsUser = $order->logisticsCompany?->user;
            if ($logisticsUser) {
                app(LogisticsNotificationService::class)->send(
                    $logisticsUser,
                    'order_updates',
                    'New order assigned',
                    "Order {$order->order_number} has been assigned to your logistics company.",
                    '/logistics/dashboard',
                );
            }
        }

        return response()->json([
            'message' => 'Order placed successfully.',
            'orders' => $orders->map(fn (Order $order): array => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'qr_payload' => url('/api/v1/orders/verify/'.rawurlencode($order->order_number)),
                'shop' => $order->seller->store_name,
                'total' => (float) $order->total,
                'shipping_fee' => (float) $order->shipping_fee,
                'logistics_company' => $order->logisticsCompany?->business_name,
                'voucher_discount' => (float) $order->voucher_discount,
                'voucher_code' => $order->voucher_code,
                'status' => $order->status,
                'created_at' => $order->created_at?->toISOString(),
                'items' => $order->items->map(fn ($item): array => [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'is_fragile' => (bool) ($item->is_fragile || $item->product?->is_fragile),
                    'image_url' => ($item->product?->selectedImageForCart($item->variation, $item->color, $item->size) ?: $this->productImageUrl($item->product?->photos[0] ?? null)),
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

    private function approvedLogisticsProviders(): Collection
    {
        return Logistics::query()
            ->where('registration_status', 'active')
            ->whereHas('user', fn ($query) => $query->where('registration_status', 'active'))
            ->with(['branches' => function ($query): void {
                $query->where('status', 'active')->whereNull('archived_at')
                    ->with(['deliveryHubs' => fn ($hubs) => $hubs->where('status', 'active')->whereNull('archived_at')]);
            }])
            ->orderBy('business_name')
            ->get();
    }

    private function coversLocations(Logistics $provider, array $locations): bool
    {
        foreach ($locations as $location) {
            if (blank($location['province'] ?? null) || blank($location['municipality'] ?? null)) return false;

            $covered = $provider->branches->contains(function (LogisticsBranch $branch) use ($location): bool {
                if ($this->sameLocation($branch->province, $branch->municipality, $location)) return true;

                return $branch->deliveryHubs->contains(fn (LogisticsDeliveryHub $hub) =>
                    $this->sameLocation($hub->province, $hub->municipality, $location)
                );
            });

            if (! $covered) return false;
        }

        return true;
    }

    private function sameLocation(?string $province, ?string $municipality, array $location): bool
    {
        return $this->normalizeLocation($province) === $this->normalizeLocation($location['province'] ?? null)
            && $this->normalizeLocation($municipality) === $this->normalizeLocation($location['municipality'] ?? null);
    }

    private function normalizeLocation(?string $value): string
    {
        return mb_strtolower(preg_replace('/[^\pL\pN]+/u', '', trim((string) $value)) ?? '');
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

        if (! $alreadyCancelled) {
            $sellerUser = $result['order']->seller?->user;
            if ($sellerUser) {
                app(\App\Services\SellerNotificationService::class)->send(
                    $sellerUser,
                    'order_updates',
                    $cancelled ? 'Order cancelled' : 'Cancellation request received',
                    $cancelled
                        ? "Order {$result['order']->order_number} was cancelled by the buyer."
                        : "The buyer requested cancellation of order {$result['order']->order_number}.",
                    '/seller/order-status',
                );
            }
        }

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
        // An approved cancellation restores wallet-paid funds exactly once.
        if ($order->payment_method === 'ShopEase Wallet') {
            app(BuyerWalletService::class)->move((int)$order->buyer_id, 'payment_reversal',
                $order->total, 'order', (int)$order->id, 'Cancelled order '.$order->order_number);
        }

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
