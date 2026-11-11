<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Buyer\OrderController;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Seller\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderPageController extends Controller
{
    public function show(Request $request)
    {
        $requested = (string) $request->query('order', '');
        abort_if($requested === '', 404);
        $record = Order::where('buyer_id', $request->user()->id)
            ->where(fn ($query) => $query->where('order_number', $requested)
                ->when(ctype_digit($requested), fn ($query) => $query->orWhere('id', (int)$requested)))
            ->with(['seller', 'items.product', 'complaints', 'latestCancellationRequest', 'statusHistory.changedBy:id,role',
                'shipment.scans', 'shipment.rider.logisticsBranch', 'logisticsCompany'])->firstOrFail();
        $reviews = ProductReview::where('buyer_id', $request->user()->id)
            ->whereIn('product_id', $record->items->pluck('product_id'))->get();
        $order = app(OrderController::class)->presentOrder($record, $reviews->pluck('product_id')->map(fn ($id) => (string)$id)->all());
        $order['reviews'] = $reviews->map(fn ($review) => [
            'product_id' => $review->product_id, 'rating' => $review->rating, 'body' => $review->body,
            'is_anonymous' => $review->is_anonymous,
            'photo_url' => $review->photo_path ? Storage::disk('public')->url($review->photo_path) : null,
        ])->all();
        $order['returns'] = OrderReturn::where('order_id', $record->id)->with('events')->latest()->get()->toArray();
        $apiToken = $request->user()->createToken('buyer-order-page')->plainTextToken;
        return view('pages.buyer.view-order', compact('order', 'apiToken'));
    }
}
