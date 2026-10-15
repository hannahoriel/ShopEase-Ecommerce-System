<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\Complaint;
use App\Models\Seller\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Read-only seller performance. Adapt table/column names to your existing
 * project schema; no admin API is called or exposed to sellers.
 */
class SellerPerformanceController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.seller.seller-performance', [
            'performance' => $this->payload($request),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        return response()->json($this->payload($request));
    }

    private function payload(Request $request): array
    {
        $user = $request->user();
        abort_unless($user && strtolower((string) $user->role) === 'seller', 403);

        $seller = null;
        if (Schema::hasTable('sellers') && Schema::hasColumn('sellers', 'user_id')) {
            $seller = DB::table('sellers')->where('user_id', $user->id)->first();
        }

        // Keep recorded compliance values authoritative; derive them only when
        // the seller record has no stored score or status.
        $score = $this->field($seller, ['compliance_score'])
            ?? $this->field($user, ['compliance_score']);
        $status = $this->field($seller, ['compliance', 'compliance_status'])
            ?? $this->field($user, ['compliance', 'compliance_status']);
        $name = $this->field($seller, ['store_name', 'shop_name', 'business_name'])
            ?? $this->field($user, ['store_name', 'name']);
        $policy = $this->field($seller, ['compliance_policy', 'policy_message']) ?? null;
        $notices = $this->notices((int) $user->id, $seller);
        $complaints = $this->complaints($seller);
        $label = $this->field($seller, ['compliance_label']);

        if ($seller) {
            $products = Product::query()->where('seller_id', $seller->id);
            $reviewCount = (clone $products)->whereIn(DB::raw('LOWER(status)'), ['pending', 'under_review', 'review'])->count();
            $warningCount = (clone $products)->whereRaw('LOWER(status) = ?', ['warning'])->count();
            $activeComplaintCount = collect($complaints)
                ->filter(fn (array $complaint): bool => in_array($complaint['status'], ['open', 'in-progress'], true))
                ->count();

            if ($score === null) {
                $registrationStatus = strtolower((string) $seller->registration_status);
                $score = in_array($registrationStatus, ['suspended', 'rejected'], true)
                    ? 0
                    : max(0, 100 - ($reviewCount * 5) - ($activeComplaintCount * 10));
            }

            if ($status === null && $score !== null) {
                $status = in_array(strtolower((string) $seller->registration_status), ['suspended', 'rejected'], true)
                    ? 'suspended'
                    : ($warningCount > 0 || $activeComplaintCount > 0 || (float) $score < 80 ? 'warning' : 'compliant');
            }

            if ($label === null && $status !== null) {
                $label = match (strtolower((string) $status)) {
                    'compliant' => 'Compliant',
                    'warning' => 'Warning',
                    'under-review' => 'Under Review',
                    'suspended', 'rejected' => 'Suspended',
                    default => ucfirst(str_replace(['-', '_'], ' ', (string) $status)),
                };
            }
        }

        return [
            'compliance' => [
                'score' => is_numeric($score) ? min(100, max(0, (float) $score)) : null,
                'status' => $status,
                'label' => $label,
                'policy' => $policy,
                'store_name' => $name,
            ],
            'notices' => $notices,
            'complaints' => $complaints,
        ];
    }

    /**
     * Compliance notices are ADMIN enforcement actions only.
     * A seller's own archive (including its archive_reason) is not a violation.
     */
    private function notices(int $userId, ?object $seller): array
    {
        if (!Schema::hasTable('products')) {
            return [];
        }

        $ownerColumn = $this->column('products', ['seller_id', 'user_id']);
        if (!$ownerColumn) {
            return [];
        }

        $ownerId = $ownerColumn === 'user_id' ? $userId : ($seller->id ?? null);
        if ($ownerId === null) {
            return [];
        }

        $products = DB::table('products')
            ->where($ownerColumn, $ownerId)
            ->orderByDesc('id')
            ->limit(1000)
            ->get();

        $result = [];
        foreach ($products as $product) {
            $actor = strtolower(trim((string) ($this->field($product, [
                'archive_actor', 'archived_by_role', 'archived_by', 'removed_by_role',
            ]) ?? '')));
            $actor = str_replace(['_', ' '], '-', $actor);

            $adminFlag = $this->field($product, ['archived_by_admin']);
            $adminActor = $adminFlag !== null
                ? filter_var($adminFlag, FILTER_VALIDATE_BOOLEAN)
                : in_array($actor, ['admin', 'administrator', 'superadmin', 'super-admin'], true);
            $isArchived = filter_var(
                $this->field($product, ['is_archived']) ?? false,
                FILTER_VALIDATE_BOOLEAN
            );

            $moderationStatus = strtolower(trim((string) ($this->field($product, [
                'compliance_status', 'moderation_status',
            ]) ?? '')));
            $productStatus = strtolower(trim((string) ($this->field($product, ['status']) ?? '')));

            // A warning must have a recorded warning reason or explicit warning status.
            // Do not treat the seller's archive_reason as a compliance warning.
            $warningReason = $this->field($product, ['warning_reason']);
            $hasWarning = $warningReason !== null || in_array(
                $moderationStatus ?: $productStatus,
                ['warning', 'warned', 'warning-issued', 'warning_issued'],
                true
            );

            $adminRemoved = $adminActor && (
                $isArchived || in_array($productStatus, ['archived', 'removed', 'removed-by-admin'], true)
            );

            if ($adminRemoved) {
                // Prefer the actual admin removal reason; only use archive_reason
                // after verifying this was an administrator action.
                $result[] = [
                    'product_id' => $this->field($product, ['id']),
                    'product_name' => $this->field($product, ['name', 'product_name', 'title']) ?? 'Product',
                    'status' => 'removed',
                    'archive_actor' => 'admin',
                    'archived_by_admin' => true,
                    'reason' => $this->field($product, ['remove_reason', 'archive_reason']),
                    'details' => $this->field($product, ['remove_details', 'archive_details']),
                    'date' => $this->field($product, ['archived_at', 'updated_at', 'created_at']),
                ];
                continue;
            }

            if ($hasWarning) {
                // Warning metadata is created by moderation/admin workflows.
                // It remains a notice even if the seller later archives the item.
                $result[] = [
                    'product_id' => $this->field($product, ['id']),
                    'product_name' => $this->field($product, ['name', 'product_name', 'title']) ?? 'Product',
                    'status' => 'warning',
                    'reason' => $warningReason,
                    'details' => $this->field($product, ['warning_details']),
                    'date' => $this->field($product, ['warning_at', 'warned_at', 'updated_at', 'created_at']),
                ];
            }

            // No notice for seller-initiated archives, whether status is
            // 'archived' or an archive_reason was provided.
        }

        usort($result, fn ($a, $b) => strcmp((string) $b['date'], (string) $a['date']));

        return array_slice($result, 0, 100);
    }

    private function complaints(?object $seller): array
    {
        if (! $seller || ! Schema::hasTable('complaints') || ! Schema::hasTable('orders')) {
            return [];
        }

        return Complaint::query()
            ->whereHas('order', fn ($query) => $query->where('seller_id', $seller->id))
            ->with([
                'user:id,name,email',
                'order.seller',
                'order.buyer:id,name,email',
                'order.items.product',
                'order.shipment',
                'updates.user:id,name',
            ])
            ->latest()
            ->limit(500)
            ->get()
            ->map(function (Complaint $complaint): array {
                $order = $complaint->order;
                $seller = $order?->seller;
                $buyer = $complaint->user ?? $order?->buyer;
                $item = $order?->items->first();
                $product = $item?->product;
                $status = strtolower(str_replace('_', '-', (string) $complaint->status));
                $photos = is_array($product?->photos) ? $product->photos : [];

                return [
                    'id' => 'CMP-'.str_pad((string) $complaint->id, 4, '0', STR_PAD_LEFT),
                    'type' => $complaint->type ?: $complaint->subject,
                    'status' => $status,
                    'filed_at' => $complaint->created_at?->toISOString(),
                    'updated_at' => $complaint->updated_at?->toISOString(),
                    'order_id' => $order?->order_number ?: ($order ? 'ORD-'.$order->id : null),
                    'orderId' => $order?->order_number ?: ($order ? 'ORD-'.$order->id : null),
                    'complainant' => $buyer?->name ?? 'Deleted buyer',
                    'party1' => $buyer?->name ?? 'Deleted buyer',
                    'role1' => 'Buyer',
                    'party2' => $seller?->store_name ?? 'Your Shop',
                    'role2' => 'Seller',
                    'party1Email' => $buyer?->email,
                    'description' => $complaint->description ?: $complaint->subject,
                    'orderDate' => $order?->created_at?->toISOString(),
                    'paymentMethod' => $order?->payment_method,
                    'paymentStatus' => $order?->status === 'refunded' ? 'Refunded' : null,
                    'shippingMethod' => $order?->shipment?->courier,
                    'orderStatus' => $order?->status,
                    'orderTotal' => $order?->total,
                    'product_name' => $item?->product_name,
                    'productName' => $item?->product_name,
                    'productVariant' => collect([$item?->variation, $item?->color, $item?->size])->filter()->implode(' · '),
                    'productPrice' => $item?->unit_price,
                    'productQuantity' => $item?->quantity,
                    'productCategory' => $product?->category,
                    'productImage' => $photos[0] ?? null,
                    'shopName' => $seller?->store_name,
                    'evidence' => [],
                    'updates' => $complaint->updates->map(fn ($update): array => [
                        'type' => $update->type,
                        'message' => $update->message,
                        'actor' => $update->user?->name ?? 'ShopEase Support',
                        'timestamp' => $update->created_at?->toISOString(),
                    ])->values()->all(),
                ];
            })
            ->all();
    }

    private function column(string $table, array $choices): ?string
    {
        foreach ($choices as $column) if (Schema::hasColumn($table, $column)) return $column;
        return null;
    }

    private function field(?object $row, array $choices): mixed
    {
        if (!$row) return null;
        foreach ($choices as $column) {
            if (isset($row->{$column}) && $row->{$column} !== '') return $row->{$column};
        }
        return null;
    }
}
