<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Api\Seller\OrderStatusController as ApiOrderStatusController;
use App\Models\Admin\Order;
use Illuminate\Http\Request;

class OrderStatusController extends ApiOrderStatusController
{
    public function page(Request $request)
    {
        $seller = $this->sellerFor($request->user());
        $orders = Order::where('seller_id', $seller->id)
            ->with('buyer:id,name,email,contact_no')
            ->latest()
            ->get();

        return view('pages.seller.order-status', compact('orders'));
    }
}
