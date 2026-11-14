<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Complaint;
use App\Models\Admin\ComplaintUpdate;
use App\Models\Buyer\BuyerNotification;
use App\Models\Seller\Seller;
use App\Services\SellerComplianceService;
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
                'user:id,name,email,profile_photo_path',
                'order.seller.user:id,name,email,profile_photo_path',
                'order.items.product',
                'order.shipment',
                'updates.user:id,name,profile_photo_path',
                'returnRequest.events',
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
        if ($data['status']==='resolved' && $complaint->returnRequest && !in_array($complaint->returnRequest->status,['refunded','declined','cancelled'],true)) {
            abort(409,'Use the return/refund decision controls to resolve this complaint.');
        }
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

        if ($update && $complaint->user) {
            BuyerNotification::createForBuyer(
                $complaint->user,
                'complaint_update',
                'Complaint status updated',
                $update->message,
                ['complaint_id' => $complaint->id, 'url' => '/buyer/my-purchases?tab=complaints'],
                $complaint->order,
            );
        }

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

    public function updateSellerFaultDecision(
        Request $request,
        Complaint $complaint,
        SellerComplianceService $compliance
    ): JsonResponse {
        $data = $request->validate([
            'seller_at_fault' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:800'],
        ]);
        $sellerId = $complaint->order?->seller_id;
        abort_unless($sellerId, 422, 'This complaint is not linked to a seller order.');

        $sellerFault = (bool) $data['seller_at_fault'];
        $notes = trim((string) ($data['notes'] ?? ''));
        $result = DB::transaction(function () use ($request, $complaint, $compliance, $sellerFault, $notes): array {
            $lockedComplaint = Complaint::query()
                ->with('order')
                ->lockForUpdate()
                ->findOrFail($complaint->id);
            $seller = Seller::query()
                ->whereKey($lockedComplaint->order?->seller_id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedComplaint->forceFill([
                'seller_at_fault' => $sellerFault,
                'seller_fault_notes' => $notes !== '' ? $notes : null,
            ])->save();

            $message = 'Admin decision: '.($sellerFault ? 'Seller at Fault.' : 'Seller Not at Fault.');
            if ($notes !== '') {
                $message .= ' '.$notes;
            }
            $update = ComplaintUpdate::create([
                'complaint_id' => $lockedComplaint->id,
                'user_id' => $request->user()->id,
                'type' => 'seller_fault_decision',
                'message' => $message,
            ]);

            $score = $compliance->scoreFor($seller);
            $suspension = $compliance->applyAutomaticSuspension($seller, $score);

            return [
                'complaint' => $lockedComplaint->fresh(),
                'update' => $update,
                'seller' => $seller->fresh(['user']),
                'score' => $score,
                'suspension' => $suspension,
            ];
        }, 3);

        return response()->json([
            'data' => [
                'id' => $result['complaint']->id,
                'seller_at_fault' => $result['complaint']->seller_at_fault,
                'seller_fault_notes' => $result['complaint']->seller_fault_notes,
                'update' => [
                    'id' => (string) $result['update']->id,
                    'type' => $result['update']->type,
                    'message' => $result['update']->message,
                    'actor' => $request->user()->name,
                    'timestamp' => $result['update']->created_at?->toISOString(),
                ],
                'compliance_score' => $result['score'],
                'registration_status' => $result['seller']->registration_status,
                'suspended_until' => $result['seller']->user?->suspended_until?->toISOString(),
                'auto_suspended' => $result['suspension']['suspended'],
                'auto_reactivated' => $result['suspension']['reactivated'],
            ],
        ]);
    }

    private function serialize(Complaint $complaint): array
    {
        $order = $complaint->order;
        $buyer = $complaint->user;
        $seller = $order?->seller;
        $sellerUser = $seller?->user;
        $item = $complaint->selectedOrderItem();
        $return = $complaint->returnRequest;

        return [
            'databaseId' => $complaint->id,
            'returnRequest' => $return ? [
                ...$return->toArray(),
                'can_confirm_receipt' => $return->status === 'accepted' && $return->return_type === 'return'
                    && $return->pickup_date && (! $return->return_delivery_hub_id && ! $order?->shipment?->logistics_delivery_hub_id || $return->shipping_status === 'picked_up'),
            ] : null,
            'id' => 'CMP-'.str_pad((string) $complaint->id, 4, '0', STR_PAD_LEFT),
            'party1' => $buyer?->name ?? 'Deleted buyer',
            'party1Photo' => $buyer?->profile_photo_path
                ? \Illuminate\Support\Facades\Storage::disk('public')->url($buyer->profile_photo_path)
                : null,
            'party1Email' => $buyer?->email ?? '—',
            'role1' => 'Buyer',
            'party2' => $seller?->store_name ?? 'ShopEase Support',
            'party2Photo' => $sellerUser?->profile_photo_path
                ? \Illuminate\Support\Facades\Storage::disk('public')->url($sellerUser->profile_photo_path)
                : ($seller?->profile_photo_path
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($seller->profile_photo_path)
                    : null),
            'party2Email' => $sellerUser?->email ?? '—',
            'role2' => 'Seller',
            'type' => $complaint->type ?: 'Other',
            'status' => str_replace('_', '-', $complaint->status),
            'sellerAtFault' => $complaint->seller_at_fault,
            'sellerFaultNotes' => $complaint->seller_fault_notes,
            'date' => $complaint->created_at?->format('M d, Y') ?? '',
            'time' => $complaint->created_at?->format('g:i A') ?? '',
            'lastUpdated' => $complaint->updated_at?->format('M d, Y g:i A') ?? '',
            'description' => $complaint->description ?: $complaint->subject,
            'evidence' => collect($complaint->evidence ?? [])
                ->map(fn (string $path): string => \Illuminate\Support\Facades\Storage::disk('public')->url($path))
                ->values(),
            'orderId' => $order?->order_number ?: ($order ? 'ORD-'.$order->id : '—'),
            'orderDate' => $order?->created_at?->format('M d, Y g:i A') ?? '—',
            'paymentMethod' => $order?->payment_method ?? '—',
            'paymentStatus' => $order?->status === 'refunded' ? 'Refunded' : '—',
            'shippingMethod' => $order?->shipment?->courier ?? '—',
            'orderStatus' => $order?->status ? str($order->status)->replace('_', ' ')->title()->toString() : '—',
            'orderTotal' => $order ? '₱'.number_format((float) $order->total, 2) : '—',
            'productImage' => $this->productImage($item?->product?->photos ?? []),
            'productName' => $item?->product_name ?? '—',
            'is_fragile' => (bool) ($item?->is_fragile || $item?->product?->is_fragile),
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
                'profile_photo_url' => $update->user?->profile_photo_path
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($update->user->profile_photo_path)
                    : null,
                'timestamp' => $update->created_at?->toISOString(),
            ])->values(),
        ];
    }
    private function productImage(array $photos): ?string
    {
        $photo = collect($photos)->first(fn ($value): bool => is_string($value) && trim($value) !== '');
        if (! $photo) {
            return null;
        }

        if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://') || str_starts_with($photo, 'data:')) {
            return $photo;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url(ltrim(preg_replace('/^storage\//', '', $photo), '/'));
    }

}
