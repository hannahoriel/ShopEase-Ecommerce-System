<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Complaint;
use App\Models\Admin\ComplaintUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller
{
    public function page()
    {
        $caseData = $this->caseData();

        return view('pages.admin.complaints-disputes', [
            'complaintRows' => $caseData['data'],
            'complaintCounts' => $caseData['counts'],
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json($this->caseData());
    }

    private function caseData(): array
    {
        $complaints = Complaint::query()
            ->with([
                'user:id,name,email',
                'order.seller.user:id,name,email',
                'order.items.product',
                'order.shipment',
                'updates.user:id,name',
            ])
            ->latest()
            ->get()
            ->map(fn (Complaint $complaint): array => $this->serialize($complaint));

        return [
            'data' => $complaints,
            'counts' => [
                'open' => $complaints->where('status', 'open')->count(),
                'in_progress' => $complaints->where('status', 'in-progress')->count(),
                'resolved' => $complaints->where('status', 'resolved')->count(),
                'total' => $complaints->count(),
            ],
        ];
    }

    public function updateStatus(Request $request, Complaint $complaint): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['open', 'in-progress', 'resolved'])],
        ]);

        $previousStatus = str_replace('_', '-', $complaint->status);
        $newStatus = $data['status'];
        $update = null;

        DB::transaction(function () use ($request, $complaint, $previousStatus, $newStatus, &$update): void {
            if ($previousStatus === $newStatus) {
                return;
            }

            $complaint->update(['status' => str_replace('-', '_', $newStatus)]);

            $labels = [
                'open' => 'Open',
                'in-progress' => 'In progress',
                'resolved' => 'Resolved',
            ];

            $message = match ($newStatus) {
                'resolved' => 'Complaint marked as Resolved.',
                'open' => 'Complaint reopened and marked as Open.',
                default => "Status updated from {$labels[$previousStatus]} to {$labels[$newStatus]}.",
            };

            $update = ComplaintUpdate::create([
                'complaint_id' => $complaint->id,
                'user_id' => $request->user()->id,
                'type' => 'status',
                'message' => $message,
            ]);
        });

        $complaint->refresh();

        return response()->json([
            'data' => [
                'id' => $complaint->id,
                'status' => str_replace('_', '-', $complaint->status),
                'updated_at' => $complaint->updated_at?->toISOString(),
                'update' => $update ? [
                    'id' => (string) $update->id,
                    'type' => $update->type,
                    'message' => $update->message,
                    'actor' => $request->user()->name,
                    'timestamp' => $update->created_at?->toISOString(),
                ] : null,
            ],
        ]);
    }

    private function serialize(Complaint $complaint): array
    {
        $order = $complaint->order;
        $buyer = $complaint->user;
        $seller = $order?->seller;
        $sellerUser = $seller?->user;
        $item = $order?->items->first();

        return [
            'databaseId' => $complaint->id,
            'id' => 'CMP-'.str_pad((string) $complaint->id, 4, '0', STR_PAD_LEFT),
            'party1' => $buyer?->name ?? 'Deleted buyer',
            'party1Email' => $buyer?->email ?? '—',
            'role1' => 'Buyer',
            'party2' => $seller?->store_name ?? 'ShopEase Support',
            'party2Email' => $sellerUser?->email ?? '—',
            'role2' => 'Seller',
            'type' => $complaint->type ?: 'Other',
            'status' => str_replace('_', '-', $complaint->status),
            'date' => $complaint->created_at?->format('M d, Y') ?? '',
            'time' => $complaint->created_at?->format('g:i A') ?? '',
            'lastUpdated' => $complaint->updated_at?->format('M d, Y g:i A') ?? '',
            'description' => $complaint->description ?: $complaint->subject,
            'orderId' => $order?->order_number ?: ($order ? 'ORD-'.$order->id : '—'),
            'orderDate' => $order?->created_at?->format('M d, Y g:i A') ?? '—',
            'paymentMethod' => $order?->payment_method ?? '—',
            'paymentStatus' => $order?->status === 'refunded' ? 'Refunded' : '—',
            'shippingMethod' => $order?->shipment?->courier ?? '—',
            'orderStatus' => $order?->status ? str($order->status)->replace('_', ' ')->title()->toString() : '—',
            'orderTotal' => $order ? '₱'.number_format((float) $order->total, 2) : '—',
            'productName' => $item?->product_name ?? '—',
            'productVariant' => collect([$item?->variation, $item?->color, $item?->size])->filter()->implode(' · ') ?: '—',
            'productPrice' => $item ? '₱'.number_format((float) $item->unit_price, 2) : '—',
            'productQuantity' => $item ? 'x'.$item->quantity : '—',
            'productCategory' => $item?->product?->category ?? '—',
            'shopName' => $seller?->store_name ?? 'ShopEase Support',
            'shopOwner' => $sellerUser?->name ?? '—',
            'updates' => $complaint->updates->map(fn (ComplaintUpdate $update): array => [
                'id' => (string) $update->id,
                'type' => $update->type,
                'message' => $update->message,
                'actor' => $update->user?->name ?? 'System',
                'timestamp' => $update->created_at?->toISOString(),
            ])->values(),
        ];
    }
}
