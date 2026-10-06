<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Buyer\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->where('buyer_id', $request->user()->id)
            ->with(['seller:id,store_name', 'items'])
            ->latest()
            ->paginate(50);

        return response()->json([
            'data' => $orders->getCollection()->map(fn (Order $order): array => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'qr_payload' => url('/api/v1/orders/verify/' . rawurlencode($order->order_number)),
                'shop' => $order->seller->store_name,
                'total' => (float) $order->total,
                'status' => $order->status,
                'created_at' => $order->created_at?->toISOString(),
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
                ->lockForUpdate()
                ->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => ['Your cart is empty. Add items before placing an order.'],
                ]);
            }

            foreach ($items as $item) {
                $product = $item->product;
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

            return $items->groupBy(fn (CartItem $item) => $item->product->seller_id)
                ->map(function ($sellerItems) use ($validated, $request): Order {
                    $seller = $sellerItems->first()->product->seller;
                    $total = $sellerItems->sum(fn (CartItem $item) => (float) $item->product->price * $item->quantity);

                    $order = Order::create([
                        'buyer_id' => $request->user()->id,
                        'seller_id' => $seller->id,
                        'order_number' => $this->newOrderNumber(),
                        'total' => $total,
                        'status' => 'pending',
                        'delivery_name' => $validated['delivery_name'],
                        'delivery_phone' => $validated['delivery_phone'],
                        'delivery_address' => $validated['delivery_address'],
                        'payment_method' => $validated['payment_method'],
                    ]);

                    foreach ($sellerItems as $item) {
                        $order->items()->create([
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name,
                            'variation' => $item->variation,
                            'color' => $item->color,
                            'size' => $item->size,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->product->price,
                        ]);
                    }

                    return $order->load(['seller:id,store_name', 'items']);
                })
                ->values();
        });

        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'message' => 'Order placed successfully.',
            'orders' => $orders->map(fn (Order $order): array => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'qr_payload' => url('/api/v1/orders/verify/' . rawurlencode($order->order_number)),
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

    private function newOrderNumber(): string
    {
        do {
            $number = 'SE-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
