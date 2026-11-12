<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Buyer\BuyerNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class OrderComplaintController extends Controller
{
    public const REASONS = ['Buyer Conduct', 'Incorrect Delivery Information', 'Refused Delivery', 'Payment Issue', 'Other'];

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order_id' => ['required_without:order_number', 'integer'],
            'order_number' => ['required_without:order_id', 'string', 'max:100'],
            'type' => ['required', Rule::in(self::REASONS)],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'evidence' => ['required', 'array', 'min:1', 'max:5'],
            'evidence.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $order = Order::query()->with('buyer')
            ->whereHas('seller', fn ($seller) => $seller->where('user_id', $request->user()->id))
            ->when(isset($data['order_id']),
                fn ($query) => $query->whereKey($data['order_id']),
                fn ($query) => $query->where('order_number', $data['order_number']))
            ->firstOrFail();
        abort_unless($order->buyer, 422, 'This order has no buyer account to report.');

        $paths = [];
        try {
            foreach ($request->file('evidence') as $file) {
                $path = $file->store('seller-complaints', 'public');
                if (! $path) {
                    throw new \RuntimeException('Could not save complaint evidence.');
                }
                $paths[] = $path;
            }

            $complaint = DB::transaction(function () use ($request, $order, $data, $paths): Complaint {
                $complaint = Complaint::create([
                    'user_id' => $order->buyer_id,
                    'order_id' => $order->id,
                    'subject' => 'Seller report: '.$data['type'],
                    'type' => $data['type'],
                    'description' => $data['description'],
                    'status' => 'open',
                    'evidence' => $paths,
                ]);
                $complaint->updates()->create([
                    'user_id' => $request->user()->id,
                    'type' => 'submitted',
                    'message' => 'Complaint submitted by the seller.',
                ]);
                BuyerNotification::createForBuyer(
                    $order->buyer, 'complaint_update', 'Seller complaint filed',
                    'The seller filed a complaint about your order. ShopEase will review it.',
                    ['complaint_id' => $complaint->id, 'url' => '/buyer/my-purchases?section=complaints'],
                    $order,
                );

                return $complaint;
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($paths);
            throw $exception;
        }

        return response()->json([
            'message' => 'Report submitted. ShopEase will review your complaint.',
            'reference' => 'CMP-'.str_pad((string) $complaint->id, 4, '0', STR_PAD_LEFT),
        ], 201);
    }
}
