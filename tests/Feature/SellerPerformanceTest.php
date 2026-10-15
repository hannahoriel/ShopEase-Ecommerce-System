<?php

namespace Tests\Feature;

use App\Models\Admin\Complaint;
use App\Models\Admin\ComplaintUpdate;
use App\Models\Admin\Order;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_performance_page_and_endpoint_are_seller_only(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->get(route('seller.performance'))
            ->assertForbidden();

        $this->actingAs($buyer)
            ->getJson(route('seller.performance.data'))
            ->assertForbidden();

        $seller = User::factory()->create(['role' => User::ROLE_SELLER]);
        Seller::create(['user_id' => $seller->id, 'store_name' => 'Performance Shop']);

        $this->actingAs($seller)
            ->get(route('seller.performance'))
            ->assertOk()
            ->assertSee('spBootstrap')
            ->assertSee('spSettings');

        $this->getJson(route('seller.performance.data'))
            ->assertOk()
            ->assertJsonPath('compliance.store_name', 'Performance Shop')
            ->assertJsonPath('compliance.score', 100)
            ->assertJsonPath('compliance.status', 'compliant')
            ->assertJsonPath('notices', [])
            ->assertJsonPath('complaints', []);
    }

    public function test_performance_data_uses_real_seller_products_and_order_complaints(): void
    {
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Performance Shop',
            'registration_status' => 'active',
        ]);
        $otherSellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $otherSeller = Seller::create(['user_id' => $otherSellerUser->id, 'store_name' => 'Other Shop']);
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        Product::create([
            'seller_id' => $seller->id,
            'name' => 'Warned Product',
            'price' => 100,
            'status' => 'warning',
            'warning_reason' => 'Missing product information',
        ]);
        Product::create([
            'seller_id' => $seller->id,
            'name' => 'Under Review Product',
            'price' => 100,
            'status' => 'under_review',
        ]);
        Product::create([
            'seller_id' => $seller->id,
            'name' => 'Removed Product',
            'price' => 100,
            'status' => 'archived',
            'is_archived' => true,
            'archived_by_admin' => true,
            'archive_reason' => 'Policy violation',
            'archive_details' => 'Admin removed the product.',
            'archive_actor' => 'admin',
        ]);
        Product::create([
            'seller_id' => $seller->id,
            'name' => 'Seller Archived Product',
            'price' => 100,
            'status' => 'archived',
            'is_archived' => true,
            'archived_by_admin' => false,
            'archive_reason' => 'Seasonal listing',
            'archive_actor' => 'seller',
        ]);

        $order = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_number' => 'SE-PERF-0001',
            'total' => 250,
            'status' => 'completed',
            'payment_method' => 'Cash on Delivery',
        ]);
        $complaint = Complaint::create([
            'user_id' => $buyer->id,
            'order_id' => $order->id,
            'subject' => 'Wrong Item',
            'type' => 'Wrong Item',
            'description' => 'The item received was not the one ordered.',
            'status' => 'in_progress',
        ]);
        ComplaintUpdate::create([
            'complaint_id' => $complaint->id,
            'user_id' => $sellerUser->id,
            'type' => 'status',
            'message' => 'Status updated from Open to In progress.',
        ]);

        $otherOrder = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $otherSeller->id,
            'order_number' => 'SE-OTHER-0001',
            'total' => 150,
            'status' => 'completed',
            'payment_method' => 'Cash on Delivery',
        ]);
        Complaint::create([
            'user_id' => $buyer->id,
            'order_id' => $otherOrder->id,
            'subject' => 'Other seller issue',
            'type' => 'Other',
            'description' => 'This complaint belongs to another seller.',
            'status' => 'open',
        ]);

        $payload = $this->actingAs($sellerUser)
            ->getJson(route('seller.performance.data'))
            ->assertOk()
            ->assertJsonPath('compliance.score', 85)
            ->assertJsonPath('compliance.status', 'warning')
            ->assertJsonPath('notices.0.status', 'removed')
            ->assertJsonPath('notices.0.product_name', 'Removed Product')
            ->assertJsonPath('notices.1.status', 'warning')
            ->assertJsonPath('notices.1.product_name', 'Warned Product')
            ->assertJsonCount(1, 'complaints')
            ->assertJsonPath('complaints.0.id', 'CMP-'.str_pad((string) $complaint->id, 4, '0', STR_PAD_LEFT))
            ->assertJsonPath('complaints.0.status', 'in-progress')
            ->assertJsonPath('complaints.0.party1', $buyer->name)
            ->assertJsonPath('complaints.0.orderId', 'SE-PERF-0001')
            ->assertJsonPath('complaints.0.updates.0.type', 'status');

        $this->assertStringNotContainsString('Other seller issue', $payload->getContent());
        $this->assertStringNotContainsString('Seller Archived Product', $payload->getContent());
    }
}
