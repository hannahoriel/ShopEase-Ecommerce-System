<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Services\ReturnPickupService;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Logistics\LogisticsDeliveryHub;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryHubParcelTrackingController extends Controller
{
    public function page(Request $request): View
    {
        $hub = LogisticsDeliveryHub::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('archived_at')
            ->firstOrFail();

        return view('pages.logistics.delivery-hub.parcel-tracking', [
            'hub' => $hub,
            'deliveryHub' => $hub,
            'deliveryHubId' => $hub->id,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $hub = LogisticsDeliveryHub::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('archived_at')
            ->with('logisticsBranch')
            ->firstOrFail();

        // Include parcels currently assigned to this hub and parcels in a transfer
        // manifest handled by it, so dispatched/delivered parcels retain history.
        $orders = Order::query()
            ->whereHas('shipment', function ($shipment) use ($hub): void {
                $shipment->where('logistics_delivery_hub_id', $hub->id)
                    ->orWhereHas('transferManifests', function ($manifest) use ($hub): void {
                        $manifest->where(function ($route) use ($hub): void {
                            $route->where(fn ($q) => $q->where('origin_type', 'delivery_hub')->where('origin_id', $hub->id))
                                ->orWhere(fn ($q) => $q->where('destination_type', 'delivery_hub')->where('destination_id', $hub->id));
                        });
                    });
            })
            ->with([
                'buyer.buyerProfile', 'seller.user', 'seller', 'items.product',
                'shipment.scans', 'shipment.deliveryHub', 'shipment.logisticsBranch',
                'statusHistory',
            ])
            ->latest('updated_at')
            ->get();

        $parcels = $orders->map(fn (Order $order): array => $this->serializeOrder($order, $hub))->values();
        $parcelByOrder = $parcels->keyBy('id');

        $returns = OrderReturn::query()
            ->where(function ($query) use ($orders, $hub): void {
                $query->where('return_delivery_hub_id', $hub->id)
                    ->orWhere(fn ($legacy) => $legacy->whereNull('return_delivery_hub_id')
                        ->whereIn('order_id', $orders->modelKeys()));
            })
            ->with([
                'events', 'order.buyer.buyerProfile', 'order.seller.user', 'order.seller', 'order.items',
                'order.shipment.scans', 'order.shipment.deliveryHub', 'order.shipment.logisticsBranch', 'order.statusHistory',
            ])
            ->latest('requested_at')
            ->get()
            ->map(function (OrderReturn $return) use ($parcelByOrder, $hub): ?array {
                $parcel = $parcelByOrder->get($return->order_id)
                    ?? ($return->order ? $this->serializeOrder($return->order, $hub) : null);
                if (! $parcel || in_array(mb_strtolower(trim((string) $return->return_type)), ['refund', 'refund_only', 'partial_refund'], true)) {
                    return null;
                }
                $returnHubId = $return->return_delivery_hub_id ?: $return->order->shipment?->logistics_delivery_hub_id;

                return [
                    ...$parcel,
                    'id' => $return->id,
                    'return_id' => $return->id,
                    'order_id' => $return->order_id,
                    'status' => $return->status,
                    'return_status' => $return->status,
                    'return_type' => $return->return_type,
                    'return_delivery_hub_id' => $return->return_delivery_hub_id,
                    'pickup_date' => $return->pickup_date?->toDateString(),
                    'pickup_time' => $return->pickup_time,
                    'failed_pickup_attempts' => (int) $return->failed_pickup_attempts,
                    'can_schedule_pickup' => (int) $returnHubId === (int) $hub->id
                        && $return->canSchedulePickup(assignRider: true),
                    'can_record_pickup' => (int) $returnHubId === (int) $hub->id
                        && $return->complaint_id && $return->status === 'accepted'
                        && $return->shipping_status === 'scheduled' && $return->pickup_rider_id,
                    'pickup_url' => route('delivery_hub.api.returns.pickup', $return),
                    'pickup_rider_id' => $return->pickup_rider_id,
                    'reason' => $return->reason,
                    'solution' => $return->solution,
                    'evidence_description' => $return->evidence_description,
                    'return_items' => $return->return_items ?: $parcel['items'],
                    'requested_at' => $return->requested_at?->toISOString(),
                    'returned_at' => $return->returned_at?->toISOString(),
                    'tracking_number' => $return->tracking_number ?: $parcel['tracking_number'],
                    'parcel_tracking_number' => $parcel['tracking_number'],
                    'courier' => $return->courier ?: $parcel['courier'],
                    'shipping_status' => $return->shipping_status ?: $return->status,
                    'tracking_history' => $return->events->map(fn ($event): array => [
                        'status' => $event->status,
                        'location' => $event->location,
                        'timestamp' => $event->scanned_at?->toISOString(),
                    ])->values(),
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'data' => [
                'parcels' => $parcels,
                'returns' => $returns,
                'pickup_riders' => app(ReturnPickupService::class)->riders($hub, app(RiderOperationsService::class))
                    ->map(fn ($rider) => ['id' => $rider->id, 'name' => $rider->user?->name])->values(),
                'locations' => [
                    'sorting_centers' => $hub->logistics_branch_id && $hub->logisticsBranch
                        ? [['id' => $hub->logisticsBranch->id, 'name' => $hub->logisticsBranch->name]] : [],
                    'delivery_hubs' => [['id' => $hub->id, 'name' => $hub->hub_name]],
                ],
            ],
        ]);
    }

    private function serializeOrder(Order $order, LogisticsDeliveryHub $hub): array
    {
        $shipment = $order->shipment;
        $buyer = $order->buyer;
        $buyerProfile = $buyer?->buyerProfile;
        $seller = $order->seller;
        $sellerUser = $seller?->user;
        $history = $shipment?->scans?->map(fn ($scan): array => [
            'status' => $scan->status,
            'location' => $scan->location,
            'timestamp' => $scan->scanned_at?->toISOString(),
        ]) ?? collect();

        if ($history->isEmpty()) {
            $history = $order->statusHistory->map(fn ($event): array => [
                'status' => $event->to_status,
                'location' => null,
                'timestamp' => $event->created_at?->toISOString(),
            ]);
        }

        return [
            'id' => $order->id,
            'order_id' => $order->order_number,
            'order_number' => $order->order_number,
            'tracking_number' => $shipment?->tracking_number,
            'status' => $order->status,
            'shipping_status' => match ($order->status) {
                'in_transit' => 'in-transit',
                'out_for_delivery' => 'out-for-delivery',
                'delivered', 'completed' => 'delivered',
                'refunded' => 'returned',
                'to_ship', 'preparing', 'new', 'pending' => 'processing',
                default => str_replace('_', '-', (string) $order->status),
            },
            'current_location' => $shipment?->current_location ?: $hub->hub_name,
            'estimated_delivery' => $shipment?->estimated_delivery?->toISOString(),
            'ordered_at' => $order->created_at?->toISOString(),
            'buyer_name' => $order->delivery_name ?: $buyer?->name,
            'buyer_email' => $buyer?->email,
            'buyer_phone' => $order->delivery_phone ?: $buyerProfile?->contact_no,
            'buyer_address_details' => $this->address($buyerProfile),
            'delivery_address' => $order->delivery_address,
            'seller_name' => $seller?->store_name ?: $seller?->business_name,
            'seller_email' => $sellerUser?->email,
            'seller_phone' => $seller?->contact_no,
            'seller_province' => $seller?->province,
            'seller_city' => $seller?->municipality,
            'seller_barangay' => $seller?->barangay,
            'seller_address_details' => $this->address($seller),
            'product_name' => $order->items->first()?->product_name,
            'items' => $order->items->map(fn ($item): array => [
                'product_name' => $item->product_name,
                'variation' => $item->variation,
                'color' => $item->color,
                'size' => $item->size,
                'quantity' => $item->quantity,
                'product_price' => (float) $item->unit_price,
                'is_fragile' => (bool) ($item->is_fragile || $item->product?->is_fragile),
            ])->values(),
            'tracking_history' => $history->values(),
            'sorting_center_id' => $hub->logistics_branch_id,
            'sorting_center' => $hub->logisticsBranch?->name,
            'delivery_hub_id' => $hub->id,
            'current_delivery_hub_id' => $shipment?->logistics_delivery_hub_id,
            'destination_delivery_hub_id' => $hub->id,
            'delivery_hub' => $hub->hub_name,
            'payment_method' => $order->payment_method,
            'total_amount' => (float) $order->total,
            'courier' => $shipment?->courier,
            'notes' => null,
        ];
    }

    private function address(mixed $profile): array
    {
        if (! $profile) {
            return [];
        }

        return [
            'province' => $profile->province,
            'municipality' => $profile->municipality,
            'barangay' => $profile->barangay,
            'zip_code' => $profile->zip_code,
            'street' => $profile->street,
            'house_no' => $profile->house_number,
            'building' => $profile->building,
            'subdivision' => $profile->subdivision,
        ];
    }
}
