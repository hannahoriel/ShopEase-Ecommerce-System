<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Api\Seller\ShippingStatusController as ApiShippingStatusController;
use Illuminate\Http\Request;

class ShippingStatusController extends ApiShippingStatusController
{
    public function page(Request $request)
    {
        $seller = $this->sellerFor($request->user());
        $orders = $this->shippingQuery($seller->id)->latest()->get();

        return view('pages.seller.shipping-status', compact('orders'));
    }
}
