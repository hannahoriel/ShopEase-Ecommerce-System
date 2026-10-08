<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\OrderCancellationRequest;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\Shipment;
use App\Models\Buyer\BuyerNotification;
use App\Models\Seller\Seller;
use App\Models\User;
use App\Services\OrderInventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrderStatusController extends Controller
{
    public function __construct(
        private readonly OrderInventoryService $inventory
    ) {}

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

    public function index(Request $request): JsonResponse
    {
        $seller = $this->sellerFor($request->user());
        $query = Order::query()
            ->where('seller_id', $seller->id)
            ->with([
                'buyer:id,name,email,contact_no',
                'items.product:id,name,photos',
                'latestCancellationRequest.buyer:id,name',
                'latestCancellationRequest.decidedBy:id,name',
            ]);

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
        $this->ownedOrder($request->user(), $order)->load([
            'buyer:id,name,email,contact_no',
            'items.product:id,name,photos',
            'statusHistory.changedBy:id,name',
            'latestCancellationRequest.buyer:id,name',
            'latestCancellationRequest.decidedBy:id,name',
        ]);

        return response()->json($order);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $order = $this->ownedOrder($request->user(), $order);
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::TRANSITIONS))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($order, $validated, $request): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $this->changeStatus($lockedOrder, $validated['status'], $request->user(), $validated['notes'] ?? null);
        });

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

        DB::transaction(function () use ($order, $validated, $request): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless($lockedOrder->seller_id === $this->sellerFor($request->user())->id, 404);
            abort_unless($lockedOrder->status === 'preparing', 422, 'Prepare the order before scheduling pickup.');

            $lockedOrder->update([
                'pickup_date' => $validated['pickup_date'],
                'pickup_time' => $validated['pickup_time'],
            ]);
            $this->changeStatus($lockedOrder, 'to_ship', $request->user(), $validated['notes'] ?? null);
        });

        return response()->json($order->fresh(['statusHistory']));
    }

    public function cancelScheduledShipment(Request $request, Order $order): JsonResponse
    {
        $order = $this->ownedOrder($request->user(), $order);
        $validated = $request->validate([
            'reason' => ['required', Rule::in([
                'Buyer requested cancellation',
                'Item is out of stock',
                'Unable to fulfill the order',
                'Pickup schedule unavailable',
                'Other',
            ])],
            'other_reason' => ['required_if:reason,Other', 'nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($order, $validated, $request): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless($lockedOrder->seller_id === $this->sellerFor($request->user())->id, 404);
            abort_unless($lockedOrder->status === 'to_ship', 422, 'Only a scheduled shipment can be cancelled here.');

            $reason = $validated['reason'] === 'Other'
                ? 'Other: '.trim($validated['other_reason'])
                : $validated['reason'];

            $this->inventory->restoreOrder($lockedOrder);

            $lockedOrder->update(['status' => 'cancelled']);
            OrderStatusHistory::create([
                'order_id' => $lockedOrder->id,
                'from_status' => 'to_ship',
                'to_status' => 'cancelled',
                'changed_by' => $request->user()->id,
                'notes' => 'Scheduled shipment cancelled by seller. Reason: '.$reason,
            ]);
            BuyerNotification::createForOrderStatus($lockedOrder, 'cancelled');
        });

        return response()->json($order->fresh(['statusHistory']));
    }

    public function approveCancellationRequest(Request $request, Order $order, OrderCancellationRequest $cancellationRequest): JsonResponse
    {
        $order = $this->ownedOrder($request->user(), $order);
        abort_unless($cancellationRequest->order_id === $order->id, 404);

        DB::transaction(function () use ($order, $cancellationRequest, $request): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $lockedRequest = OrderCancellationRequest::query()->lockForUpdate()->findOrFail($cancellationRequest->id);
            abort_unless($lockedRequest->order_id === $lockedOrder->id, 404);
            abort_unless($lockedRequest->status === 'pending', 422, 'This cancellation request has already been reviewed.');
            abort_unless(
                in_array($lockedOrder->status, ['pending', 'new', 'preparing', 'to_ship'], true),
                422,
                'This order can no longer be cancelled.'
            );

            $this->inventory->restoreOrder($lockedOrder);
            $previousStatus = $lockedOrder->status;
            $lockedOrder->update(['status' => 'cancelled']);
            $lockedRequest->update([
                'status' => 'approved',
                'decided_by' => $request->user()->id,
                'decided_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id' => $lockedOrder->id,
                'from_status' => $previousStatus,
                'to_status' => 'cancelled',
                'changed_by' => $request->user()->id,
                'notes' => 'Buyer cancellation request approved by seller.',
            ]);
            BuyerNotification::createForOrderStatus($lockedOrder, 'cancelled');
        });

        return response()->json($order->fresh(['latestCancellationRequest', 'statusHistory']));
    }

    public function rejectCancellationRequest(Request $request, Order $order, OrderCancellationRequest $cancellationRequest): JsonResponse
    {
        $order = $this->ownedOrder($request->user(), $order);
        abort_unless($cancellationRequest->order_id === $order->id, 404);

        $validated = $request->validate([
            'reason' => ['required', Rule::in(OrderCancellationRequest::SELLER_REJECTION_REASONS)],
            'other_reason' => ['required_if:reason,other', 'nullable', 'string', 'max:500'],
        ]);

        $reviewedRequest = DB::transaction(function () use ($order, $cancellationRequest, $request, $validated): OrderCancellationRequest {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $lockedRequest = OrderCancellationRequest::query()->lockForUpdate()->findOrFail($cancellationRequest->id);
            abort_unless($lockedRequest->order_id === $lockedOrder->id, 404);
            abort_unless($lockedRequest->status === 'pending', 422, 'This cancellation request has already been reviewed.');
            abort_unless(
                in_array($lockedOrder->status, ['pending', 'new', 'preparing', 'to_ship'], true),
                422,
                'This cancellation request can no longer be reviewed.'
            );

            $lockedRequest->update([
                'status' => 'rejected',
                'decided_by' => $request->user()->id,
                'seller_reason' => $validated['reason'],
                'seller_other_reason' => $validated['reason'] === 'other'
                    ? trim($validated['other_reason'])
                    : null,
                'decided_at' => now(),
            ]);

            $reason = $validated['reason'] === 'other'
                ? trim($validated['other_reason'])
                : str_replace('_', ' ', $validated['reason']);
            BuyerNotification::createForOrder(
                $lockedOrder,
                'cancellation_rejected',
                'Cancellation request declined',
                'The seller declined your cancellation request. Reason: '.$reason,
                [
                    'cancellation_request_id' => $lockedRequest->id,
                    'seller_reason' => $validated['reason'],
                    'seller_other_reason' => $validated['reason'] === 'other' ? trim($validated['other_reason']) : null,
                ]
            );

            return $lockedRequest->fresh();
        });

        return response()->json([
            'message' => 'Cancellation request rejected.',
            'data' => $reviewedRequest,
        ]);
    }

    public function waybill(Request $request, Order $order): JsonResponse
    {
        $order = $this->ownedOrder($request->user(), $order);
        abort_unless(
            in_array($order->status, ['new', 'pending'], true),
            422,
            'A waybill can only be printed while the order is new.'
        );

        $shipment = Shipment::firstOrCreate(
            ['order_id' => $order->id],
            [
                'tracking_number' => 'SE-'.Str::upper(Str::random(10)),
                'scan_token' => Str::random(64),
                'courier' => 'Ease Express',
                'estimated_delivery' => $order->pickup_date?->copy()->addDays(3) ?? now()->addDays(3)->toDateString(),
            ]
        );

        if (! $shipment->scan_token) {
            $shipment->update(['scan_token' => Str::random(64)]);
        }

        $order->load([
            'buyer:id,name,email,contact_no',
            'seller:id,store_name,province,municipality,barangay,street,house_number',
            'items.product:id,name,photos',
        ]);

        return response()->json([
            'scan_url' => route('parcel.scan.show', ['token' => $shipment->scan_token]),
            'order' => $order,
            'shipment' => $shipment->fresh(),
        ]);
    }

    protected function sellerFor(User $user): Seller
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
        abort_if(
            $order->latestCancellationRequest()->where('status', 'pending')->exists(),
            422,
            'Review the pending buyer cancellation request before changing this order.'
        );

        $allowedStatuses = self::TRANSITIONS[$order->status] ?? [];
        abort_unless(in_array($newStatus, $allowedStatuses, true), 422, "Order cannot move from {$order->status} to {$newStatus}.");

        if ($newStatus === 'cancelled') {
            $this->inventory->restoreOrder($order);
        }

        $fromStatus = $order->status;
        $order->update(['status' => $newStatus]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $fromStatus,
            'to_status' => $newStatus,
            'changed_by' => $user->id,
            'notes' => $notes,
        ]);

        BuyerNotification::createForOrderStatus($order, $newStatus);
    }
}
