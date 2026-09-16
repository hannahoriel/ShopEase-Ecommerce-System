<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_dashboard_returns_only_the_authenticated_sellers_metrics(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-16 12:00:00'));

        $sellerUser = User::factory()->create([
            'role' => User::ROLE_SELLER,
            'name' => 'Seller One',
        ]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'One Store',
            'registration_status' => 'active',
        ]);
        $otherUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $otherSeller = Seller::create([
            'user_id' => $otherUser->id,
            'store_name' => 'Other Store',
            'registration_status' => 'active',
        ]);
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        Product::create([
            'seller_id' => $seller->id,
            'name' => 'Low stock item',
            'price' => 25,
            'stock_quantity' => 5,
        ]);
        Product::create([
            'seller_id' => $otherSeller->id,
            'name' => 'Other item',
            'price' => 100,
            'stock_quantity' => 1,
        ]);

        Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'total' => 100,
            'status' => 'completed',
            'created_at' => now()->subDay(),
        ]);
        Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'total' => 50,
            'status' => 'pending',
        ]);
        Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $otherSeller->id,
            'total' => 999,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($sellerUser)->getJson('/api/v1/seller/dashboard');

        $response->assertOk()
            ->assertJsonPath('seller.store_name', 'One Store')
            ->assertJsonPath('stats.total_sales', 100)
            ->assertJsonPath('stats.total_orders', 2)
            ->assertJsonPath('stats.pending_orders', 1)
            ->assertJsonPath('stats.low_stock_products', 1)
            ->assertJsonPath('sales_summary.average_order_value', 100)
            ->assertJsonPath('orders_by_status.completed', 1)
            ->assertJsonPath('orders_by_status.pending', 1)
            ->assertJsonCount(2, 'recent_orders');

        $this->actingAs($sellerUser)
            ->get(route('seller.dashboard'))
            ->assertOk()
            ->assertViewIs('pages.seller.dashboard')
            ->assertSee('Welcome, One Store!')
            ->assertSee('₱100.00');
    }

    public function test_non_sellers_cannot_access_the_seller_dashboard(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->getJson('/api/v1/seller/dashboard')
            ->assertForbidden();
    }
}
