<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Logistics\LogisticsDeliveryHub;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryHubParcelTrackingController extends ParcelTrackingController
{
    public function page(Request $request): View
    {
        $hub = LogisticsDeliveryHub::where('user_id', $request->user()->id)->whereNull('archived_at')->firstOrFail();
        $scope = fn ($query) => $query->where('logistics_delivery_hub_id', $hub->id);
        $relations = ['buyer.buyerProfile', 'seller.user', 'items.product', 'shipment.scans',
            'shipment.rider.logisticsBranch.deliveryHubs', 'shipment.logisticsBranch.deliveryHubs', 'shipment.deliveryHub', 'statusHistory'];
        $orders = Order::whereHas('shipment', $scope)->with($relations)->latest('updated_at')->get();
        $returns = OrderReturn::whereHas('order.shipment', $scope)
            ->with([...array_map(fn ($relation) => 'order.'.$relation, $relations), 'events'])
            ->latest('requested_at')->get();

        return view('pages.logistics.delivery-hub.parcel-tracking', [
            'deliveryHub' => $hub,
            'deliveryHubParcels' => $orders->map(fn (Order $order) => $this->serializeParcel($order))->values(),
            'deliveryHubReturns' => $returns->map(fn (OrderReturn $return) => $this->serializeReturn($return))->values(),
        ]);
    }
}
