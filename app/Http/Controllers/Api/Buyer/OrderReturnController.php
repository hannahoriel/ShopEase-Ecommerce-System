<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use Illuminate\Http\Request;

class OrderReturnController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['data' => OrderReturn::query()->whereHas('order', fn ($q) => $q->where('buyer_id', $request->user()->id))
            ->with(['order:id,order_number,status', 'events'])->latest('requested_at')->get()]);
    }

    public function store(Request $request, Order $order)
    {
        return app(ReturnComplaintIntakeController::class)->store($request, $order, app(\App\Services\ReturnComplaintService::class));
    }
}