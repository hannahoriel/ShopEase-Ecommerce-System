<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
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

        // The admin Seller Compliance UI expects compliance_score and compliance.
        // Prefer actual recorded values: never invent a default 100% score.
        $score = $this->field($seller, ['compliance_score'])
            ?? $this->field($user, ['compliance_score']);
        $status = $this->field($seller, ['compliance', 'compliance_status'])
            ?? $this->field($user, ['compliance', 'compliance_status']);
        $name = $this->field($seller, ['store_name', 'shop_name', 'business_name'])
            ?? $this->field($user, ['store_name', 'name']);
        $policy = $this->field($seller, ['compliance_policy', 'policy_message']) ?? null;
        $notices = $this->notices((int) $user->id, $seller);
        $complaints = $this->complaints((int) $user->id, $seller);

        return [
            'compliance' => [
                'score' => is_numeric($score) ? min(100, max(0, (float) $score)) : null,
                'status' => $status,
                'label' => $this->field($seller, ['compliance_label']),
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

            $adminFlag = filter_var(
                $this->field($product, ['archived_by_admin']) ?? false,
                FILTER_VALIDATE_BOOLEAN
            );
            $adminActor = $adminFlag || in_array($actor, [
                'admin', 'administrator', 'superadmin', 'super-admin',
            ], true);
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

    private function complaints(int $userId, ?object $seller): array
    {
        // Find complaints by an EXPLICIT seller owner column only, never by a
        // name substring: names can collide across different seller accounts.
        $table = Schema::hasTable('complaints') ? 'complaints'
            : (Schema::hasTable('complaints_disputes') ? 'complaints_disputes' : null);
        if (!$table) return [];
        $sellerIdCol = $this->column($table, ['seller_id', 'seller_user_id']);
        if (!$sellerIdCol) return [];
        $ownerId = $sellerIdCol === 'seller_user_id' ? $userId : ($seller->id ?? null);
        if ($ownerId === null) return [];
        $rows = DB::table($table)->where($sellerIdCol, $ownerId)->orderByDesc('id')->limit(500)->get();
        return $rows->map(function ($row) {
            $status = strtolower(str_replace('_', '-', (string) ($this->field($row, ['status']) ?? 'open')));
            if ($status === 'in progress') $status = 'in-progress';
            return [
                'id' => $this->field($row, ['complaint_id', 'case_id', 'id']),
                'type' => $this->field($row, ['type', 'complaint_type', 'category']),
                'order_id' => $this->field($row, ['order_id']),
                'status' => $status,
                'filed_at' => $this->field($row, ['date_filed', 'filed_at', 'created_at']),
                'updated_at' => $this->field($row, ['updated_at']),
                'complainant' => $this->field($row, ['complainant_name', 'buyer_name']),
                'product_name' => $this->field($row, ['product_name']),
                'description' => $this->field($row, ['description', 'complaint_description', 'details']),
                'resolution' => $this->field($row, ['resolution', 'admin_response']),
                // Additional complaint detail fields are optional. Return only
                // values already scoped to the authenticated seller above.
                'party1' => $this->field($row, ['party1', 'buyer_name', 'complainant_name']),
                'role1' => 'Buyer',
                'party2' => $this->field($row, ['party2', 'shop_name', 'store_name']),
                'role2' => 'Seller',
                'party1Email' => $this->field($row, ['buyer_email', 'complainant_email']),
                'orderDate' => $this->field($row, ['order_date']),
                'paymentMethod' => $this->field($row, ['payment_method']),
                'paymentStatus' => $this->field($row, ['payment_status']),
                'shippingMethod' => $this->field($row, ['shipping_method']),
                'orderStatus' => $this->field($row, ['order_status']),
                'orderTotal' => $this->field($row, ['order_total']),
                'productVariant' => $this->field($row, ['product_variant']),
                'productPrice' => $this->field($row, ['product_price']),
                'productQuantity' => $this->field($row, ['product_quantity']),
                'productCategory' => $this->field($row, ['product_category']),
                'sellerResponse' => $this->field($row, ['seller_response']),
                'evidence' => $this->jsonArray($this->field($row, ['evidence', 'attachments'])),
                'updates' => $this->jsonArray($this->field($row, ['updates'])),
            ];
        })->all();
    }

    private function jsonArray(mixed $value): array
    {
        if (is_array($value)) return $value;
        if (!is_string($value)) return [];
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
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
