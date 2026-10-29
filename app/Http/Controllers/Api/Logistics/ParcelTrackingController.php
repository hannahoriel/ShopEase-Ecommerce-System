<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Admin\Shipment;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParcelTrackingController extends Controller
{
    public function page(Request $request): View
    {
        $tracking = $this->trackingData($request->user());

        return view('pages.logistics.parcel-tracking', [
            'logisticsParcels' => $tracking['parcels'],
            'logisticsReturns' => $tracking['returns'],
            'logisticsLocations' => $tracking['locations'],
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->trackingData($request->user()));
    }

    private function trackingData(User $user): array
    {
        $logistics = Logistics::query()
            ->where('user_id', $user->id)
            ->where('registration_status', 'active')
            ->firstOrFail();

        $branches = $logistics->branches()->whereNull('archived_at')->with('deliveryHubs')->get();
        $branchIds = $branches->modelKeys();
        $hubs = $branches->flatMap(fn (LogisticsBranch $branch) => $branch->deliveryHubs)
            ->whereNull('archived_at')->values();
        $hubIds = $hubs->pluck('id')->all();

        $networkShipment = function ($query) use ($logistics, $branchIds, $hubIds): void {
            $query->where(function ($scope) use ($logistics, $branchIds, $hubIds): void {
                if ($branchIds !== []) {
                    $scope->whereIn('logistics_branch_id', $branchIds);
                }
                if ($hubIds !== []) {
                    $method = $branchIds === [] ? 'whereIn' : 'orWhereIn';
                    $scope->{$method}('logistics_delivery_hub_id', $hubIds);
                }
                $method = ($branchIds === [] && $hubIds === []) ? 'whereHas' : 'orWhereHas';
                $scope->{$method}('rider.logisticsBranch', fn ($branch) => $branch->where('logistics_id', $logistics->id)
                    ->whereNull('archived_at'));
            });
        };

        $orders = Order::query()
            ->whereHas('shipment', $networkShipment)
            ->with([
                'buyer.buyerProfile',
                'seller.user',
                'items',
                'shipment.scans',
                'shipment.rider.logisticsBranch.deliveryHubs',
                'shipment.logisticsBranch.deliveryHubs',
                'shipment.deliveryHub',
                'statusHistory',
            ])
            ->latest('updated_at')
            ->get();

        $returns = OrderReturn::query()
            ->whereHas('order.shipment', $networkShipment)
            ->with([
                'order.buyer.buyerProfile',
                'order.seller.user',
                'order.items',
                'order.shipment.rider.logisticsBranch',
                'order.shipment.rider.logisticsBranch.deliveryHubs',
                'order.shipment.logisticsBranch.deliveryHubs',
                'order.shipment.deliveryHub',
                'order.shipment.scans',
                'events',
            ])
            ->latest('requested_at')
            ->get();

        return [
            'parcels' => $orders->map(fn (Order $order): array => $this->serializeParcel($order))->values(),
            'returns' => $returns->map(fn (OrderReturn $return): array => $this->serializeReturn($return))->values(),
            'locations' => [
                'sorting_centers' => $branches->map(fn (LogisticsBranch $branch): array => [
                    'id' => $branch->id,
                    'name' => $branch->name,
                ])->values(),
                'delivery_hubs' => $hubs->map(fn (LogisticsDeliveryHub $hub): array => [
                    'id' => $hub->id,
                    'name' => $hub->hub_name,
                    'sorting_center_id' => $hub->logistics_branch_id,
                ])->values(),
            ],
        ];
    }

    private function serializeParcel(Order $order): array
    {
        /** @var Shipment $shipment */
        $shipment = $order->shipment;
        $rider = $shipment->rider;
        $branch = $shipment->logisticsBranch ?? $rider?->logisticsBranch;
        $hub = $shipment->deliveryHub;
        if (! $hub && $branch && $rider?->municipal_hub) {
            $hub = $branch->deliveryHubs->first(fn (LogisticsDeliveryHub $candidate): bool =>
                mb_strtolower($candidate->hub_name) === mb_strtolower($rider->municipal_hub)
            );
        }
        $buyer = $order->buyer;
        $seller = $order->seller;
        $sellerUser = $seller?->user;
        $buyerProfile = $buyer?->buyerProfile;

        return [
            'id' => $order->id,
            'order_id' => $order->order_number,
            'order_number' => $order->order_number,
            'tracking_number' => $shipment->tracking_number,
            'status' => $order->status,
            'shipping_status' => $this->shipmentStatus($order->status),
            'current_location' => $shipment->current_location,
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
            'payment_method' => $order->payment_method,
            'total_amount' => (float) $order->total,
            'items' => $order->items->map(fn ($item): array => [
                'product_name' => $item->product_name,
                'variation' => $item->variation,
                'color' => $item->color,
                'size' => $item->size,
                'quantity' => $item->quantity,
                'product_price' => (float) $item->unit_price,
            ])->values(),
            'tracking_history' => $this->orderHistory($order),
            'sorting_center_id' => $branch?->id,
            'sorting_center' => $branch?->name,
            'delivery_hub_id' => $hub?->id,
            'delivery_hub' => $hub?->hub_name,
            'courier' => $shipment->courier,
            'notes' => null,
        ];
    }

    private function serializeReturn(OrderReturn $return): array
    {
        $order = $return->order;
        $parcel = $this->serializeParcel($order);

        return [
            ...$parcel,
            'return_id' => $return->id,
            'status' => $return->status,
            'return_status' => $return->status,
            'return_type' => $return->return_type,
            'type' => $return->return_type,
            'reason' => $return->reason,
            'solution' => $return->solution,
            'evidence_description' => $return->evidence_description,
            'return_items' => $return->return_items ?: $parcel['items'],
            'amount' => (float) $order->total,
            'requested_at' => $return->requested_at?->toISOString(),
            'returned_at' => $return->returned_at?->toISOString(),
            'courier' => $return->courier,
            'tracking_number' => $return->tracking_number,
            'shipping_status' => $return->shipping_status ?: $return->status,
            'tracking_history' => $return->events->map(fn ($event): array => [
                'status' => $event->status,
                'location' => $event->location,
                'timestamp' => $event->scanned_at?->toISOString(),
            ])->values(),
        ];
    }

    private function orderHistory(Order $order): array
    {
        $scans = $order->shipment->scans->map(fn ($scan): array => [
            'status' => $scan->status,
            'location' => $scan->location,
            'timestamp' => $scan->scanned_at?->toISOString(),
        ]);
        if ($scans->isNotEmpty()) {
            return $scans->sortBy('timestamp')->values()->all();
        }

        return $order->statusHistory->map(fn ($event): array => [
            'status' => $event->to_status,
            'location' => null,
            'timestamp' => $event->created_at?->toISOString(),
        ])->values()->all();
    }

    private function shipmentStatus(string $status): string
    {
        return match ($status) {
            'in_transit' => 'in-transit',
            'out_for_delivery' => 'out-for-delivery',
            'delivered', 'completed' => 'delivered',
            'refunded' => 'returned',
            'to_ship', 'preparing', 'new', 'pending' => 'processing',
            default => str_replace('_', '-', $status),
        };
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
            'zip_code' => $profile->zip_code ?? null,
            'street' => $profile->street,
            'house_no' => $profile->house_number,
            'building' => $profile->building ?? null,
            'subdivision' => $profile->subdivision ?? null,
        ];
    }
}
