<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SortingCenterParcelTrackingController extends Controller
{
    public function page(Request $request): View
    {
        $data = $this->trackingData($request);

        return view('pages.logistics.sorting-center.parcel-tracking', [
            'sortingCenterParcels' => $data['parcels'],
            'sortingCenterReturns' => $data['returns'],
            'logisticsLocations' => $data['locations'],
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->trackingData($request));
    }

    private function trackingData(Request $request): array
    {
        $center = LogisticsBranch::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('archived_at')
            ->firstOrFail();

        $hubs = LogisticsDeliveryHub::query()
            ->where('logistics_branch_id', $center->id)
            ->whereNull('archived_at')
            ->where('status', 'active')
            ->get();

        // The shipment's current branch is updated by the scanner route when it
        // reaches a hub or transfers to another sorting center. This naturally
        // excludes parcels that have left this center's network.
        $hubIds = $hubs->modelKeys();
        $orders = Order::query()
            ->whereHas('shipment', function ($query) use ($center, $hubIds): void {
                $query->where('logistics_branch_id', $center->id);
                if ($hubIds !== []) {
                    $query->orWhereIn('logistics_delivery_hub_id', $hubIds);
                }
            })
            ->with(['buyer.buyerProfile', 'seller.user', 'items', 'shipment.scans', 'shipment.deliveryHub', 'shipment.logisticsBranch', 'statusHistory'])
            ->latest('updated_at')
            ->get();

        $parcels = $orders->map(function (Order $order) use ($center): array {
            $shipment = $order->shipment;
            $history = $shipment->scans->map(fn ($scan) => [
                'status' => $scan->status,
                'location' => $scan->location,
                'timestamp' => $scan->scanned_at?->toISOString(),
            ]);
            if ($history->isEmpty()) {
                $history = $order->statusHistory->map(fn ($event) => [
                    'status' => $event->to_status,
                    'location' => null,
                    'timestamp' => $event->created_at?->toISOString(),
                ]);
            }
            $buyer = $order->buyer;
            $buyerProfile = $buyer?->buyerProfile;
            $seller = $order->seller;
            $sellerUser = $seller?->user;
            $hub = $shipment->deliveryHub;

            return [
                'id' => $order->id,
                'order_id' => $order->order_number,
                'order_number' => $order->order_number,
                'tracking_number' => $shipment->tracking_number,
                'status' => $order->status,
                'shipping_status' => match ($order->status) {
                    'in_transit' => 'in-transit', 'out_for_delivery' => 'out-for-delivery',
                    'delivered', 'completed' => 'delivered', 'refunded' => 'returned',
                    'to_ship', 'preparing', 'new', 'pending' => 'processing',
                    default => str_replace('_', '-', $order->status),
                },
                'current_location' => $shipment->current_location ?: ($hub?->hub_name ?: $center->name),
                'estimated_delivery' => $shipment->estimated_delivery?->toISOString(),
                'ordered_at' => $order->created_at?->toISOString(),
                'buyer_name' => $order->delivery_name ?: $buyer?->name,
                'buyer_email' => $buyer?->email,
                'buyer_phone' => $order->delivery_phone ?: $buyerProfile?->contact_no,
                'buyer_address_details' => $this->addressFromProfile($buyerProfile),
                'delivery_address' => $order->delivery_address,
                'seller_name' => $seller?->store_name ?: $seller?->business_name,
                'seller_email' => $sellerUser?->email,
                'seller_phone' => $seller?->contact_no,
                'seller_address_details' => $this->addressFromProfile($seller),
                'product_name' => $order->items->first()?->product_name,
                'items' => $order->items->map(fn ($item) => [
                    'product_name' => $item->product_name, 'variation' => $item->variation,
                    'color' => $item->color, 'size' => $item->size, 'quantity' => $item->quantity,
                    'product_price' => (float) $item->unit_price,
                ])->values(),
                'tracking_history' => $history->values(),
                'sorting_center_id' => $center->id,
                'sorting_center' => $center->name,
                'delivery_hub_id' => $hub?->id,
                'current_delivery_hub_id' => $hub?->id,
                'delivery_hub' => $hub?->hub_name,
                'current_sorting_center_id' => $center->id,
                'payment_method' => $order->payment_method,
                'total_amount' => (float) $order->total,
                'courier' => $shipment->courier,
                'notes' => null,
            ];
        })->values();

        $returns = OrderReturn::query()
            ->whereIn('order_id', $orders->modelKeys())
            ->with(['order.buyer', 'order.items', 'order.shipment.scans', 'events'])
            ->latest('requested_at')
            ->get()
            ->map(function (OrderReturn $return) use ($parcels): array {
                $parcel = $parcels->firstWhere('id', $return->order_id);
                if (! $parcel) {
                    return [];
                }

                return [
                    ...$parcel,
                    'return_id' => $return->id,
                    'status' => $return->status,
                    'return_status' => $return->status,
                    'return_type' => $return->return_type,
                    'reason' => $return->reason,
                    'solution' => $return->solution,
                    'return_items' => $return->return_items ?: $parcel['items'],
                    'requested_at' => $return->requested_at?->toISOString(),
                    'returned_at' => $return->returned_at?->toISOString(),
                    'tracking_number' => $return->tracking_number ?: $parcel['tracking_number'],
                    'shipping_status' => $return->shipping_status ?: $return->status,
                    'tracking_history' => $return->events->map(fn ($event) => [
                        'status' => $event->status, 'location' => $event->location,
                        'timestamp' => $event->scanned_at?->toISOString(),
                    ])->values(),
                ];
            })->filter()->values();

        return [
            'parcels' => $parcels,
            'returns' => $returns,
            'locations' => [
                'sorting_centers' => [['id' => $center->id, 'name' => $center->name]],
                'delivery_hubs' => $hubs->map(fn (LogisticsDeliveryHub $hub) => [
                    'id' => $hub->id, 'name' => $hub->hub_name,
                ])->values(),
            ],
        ];
    }

    private function addressFromProfile(mixed $profile): array
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
