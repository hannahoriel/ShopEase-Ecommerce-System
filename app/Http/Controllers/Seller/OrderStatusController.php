<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderStatusController extends Controller
{
    private const TRANSITIONS = [
        'pending' => ['preparing', 'cancelled'],
        'new' => ['preparing', 'cancelled'],
        'preparing' => ['to_ship', 'cancelled'],
        'to_ship' => ['in_transit', 'cancelled'],
        'in_transit' => ['out_for_delivery'],
        'out_for_delivery' => ['delivered'],
        'delivered' => [],
        'completed' => [],
        'cancelled' => [],
        'refunded' => [],
    ];

    public function page(Request $request): View
    {
        $seller = $this->sellerFor($request->user());
        $orders = Order::where('seller_id', $seller->id)
            ->with('buyer:id,name,email,contact_no')
            ->latest()
            ->get();

        return view('pages.seller.order-status', compact('orders'));
    }

    public function index(Request $request): JsonResponse
    {
        $seller = $this->sellerFor($request->user());
        $query = Order::query()
            ->where('seller_id', $seller->id)
            ->with('buyer:id,name,email,contact_no');

        if ($request->filled('status')) {
            $query->whereIn('status', (array) $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($orderQuery) use ($search): void {
                $orderQuery->where('id', 'like', "%{$search}%")
                    ->orWhereHas('buyer', function ($buyerQuery) use ($search): void {
                        $buyerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->latest()->paginate($request->integer('per_page', 10));

        return response()->json($orders);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $this->ownedOrder($request->user(), $order)->load(['buyer:id,name,email,contact_no', 'statusHistory.changedBy:id,name']);

        return response()->json($order);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $order = $this->ownedOrder($request->user(), $order);
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::TRANSITIONS))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->changeStatus($order, $validated['status'], $request->user(), $validated['notes'] ?? null);

        return response()->json($order->fresh(['statusHistory']));
    }

    public function schedule(Request $request, Order $order): JsonResponse
    {
        $order = $this->ownedOrder($request->user(), $order);
        $validated = $request->validate([
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! in_array($order->status, ['preparing', 'pending', 'new'], true)) {
            abort(422, 'Only preparing or new orders can be scheduled for pickup.');
        }

        $order->update([
            'pickup_date' => $validated['pickup_date'],
            'pickup_time' => $validated['pickup_time'],
        ]);
        $this->changeStatus($order, 'to_ship', $request->user(), $validated['notes'] ?? null);

        return response()->json($order->fresh(['statusHistory']));
    }

    private function sellerFor(User $user): Seller
    {
        return Seller::where('user_id', $user->id)->firstOrFail();
    }

    private function ownedOrder(User $user, Order $order): Order
    {
        abort_unless($order->seller_id === $this->sellerFor($user)->id, 404);

        return $order;
    }

    private function changeStatus(Order $order, string $newStatus, User $user, ?string $notes): void
    {
        $allowedStatuses = self::TRANSITIONS[$order->status] ?? [];
        abort_unless(in_array($newStatus, $allowedStatuses, true), 422, "Order cannot move from {$order->status} to {$newStatus}.");

        $fromStatus = $order->status;
        $order->update(['status' => $newStatus]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $fromStatus,
            'to_status' => $newStatus,
            'changed_by' => $user->id,
            'notes' => $notes,
        ]);
    }
}
