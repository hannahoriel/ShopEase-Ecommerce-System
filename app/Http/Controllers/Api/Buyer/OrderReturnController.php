<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OrderReturnController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['data' => OrderReturn::query()->whereHas('order', fn ($q) => $q->where('buyer_id', $request->user()->id))
            ->with(['order:id,order_number,status', 'events'])->latest('requested_at')->get()]);
    }

    public function store(Request $request, Order $order)
    {
        abort_unless((int) $order->buyer_id === (int) $request->user()->id, 404);
        abort_unless(in_array($order->status, ['completed', 'delivered'], true), 422, 'Returns and refunds are available after delivery.');

        $data = $request->validate([
            'return_type' => ['required', Rule::in(['return', 'refund'])],
            'reason' => ['required', 'string', 'max:2000'],
            'evidence_description' => ['nullable', 'string', 'max:2000'],
            'evidence' => ['sometimes', 'array', 'max:5'],
            'evidence.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.order_item_id' => ['required', 'integer', Rule::exists('order_items', 'id')->where('order_id', $order->id)],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $items = $order->items()->get()->keyBy('id');
        foreach ($data['items'] as $requested) {
            $item = $items->get($requested['order_item_id']);
            abort_if(! $item || $requested['quantity'] > $item->quantity, 422, 'Requested quantity exceeds the purchased quantity.');
        }

        $evidence = collect($request->file('evidence', []))->map(function ($file) {
            $path = $file->store('returns/evidence', 'public');
            return ['path' => $path, 'url' => Storage::disk('public')->url($path), 'name' => $file->getClientOriginalName()];
        })->values()->all();

        return DB::transaction(function () use ($request, $order, $data, $items, $evidence) {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $existing = OrderReturn::query()->where('order_id', $order->id)->whereIn('status', ['pending', 'accepted'])->get();
            $chosen = collect($data['items'])->map(function ($requested) use ($items, $existing) {
                $item = $items->get($requested['order_item_id']);
                $alreadyRequested = $existing->sum(fn ($record) => collect($record->return_items)->where('order_item_id', $item->id)->sum('quantity'));
                abort_if($alreadyRequested + $requested['quantity'] > $item->quantity, 422, 'Some quantity is already part of another open request.');
                return [
                    'order_item_id' => $item->id,
                    'product_name' => $item->product_name,
                    'variation' => $item->variation,
                    'color' => $item->color,
                    'size' => $item->size,
                    'quantity' => $requested['quantity'],
                    'unit_price' => $item->unit_price,
                ];
            })->values()->all();

            $record = OrderReturn::create([
                'order_id' => $order->id,
                'return_type' => $data['return_type'],
                'status' => 'pending',
                'reason' => $data['reason'],
                'evidence_description' => $data['evidence_description'] ?? null,
                'evidence' => $evidence,
                'return_items' => $chosen,
                'refund_amount' => collect($chosen)->sum(fn ($item) => (float) $item['unit_price'] * $item['quantity']),
                'requested_at' => now(),
            ]);
            $record->events()->create(['status' => 'pending', 'location' => 'Buyer request submitted', 'scanned_at' => now()]);
            return response()->json(['data' => $record->load(['order:id,order_number,status', 'events'])], 201);
        });
    }
}
