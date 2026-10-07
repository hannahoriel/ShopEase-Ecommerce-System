<?php

namespace Tests\Feature;

use App\Models\Admin\Announcement;
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
        Announcement::create([
            'title' => 'Seller update',
            'body' => 'Store operations update.',
            'badge_label' => 'Notice',
            'is_active' => true,
        ]);

        Product::create([
            'seller_id' => $seller->id,
            'name' => 'Low stock item',
            'price' => 25,
            'stock_quantity' => 5,
        ]);
        Product::create([
            'seller_id' => $seller->id,
            'name' => 'Archived low stock item',
            'price' => 25,
            'stock_quantity' => 2,
            'is_archived' => true,
        ]);
        Product::create([
            'seller_id' => $otherSeller->id,
            'name' => 'Other item',
            'price' => 100,
            'stock_quantity' => 1,
        ]);

        $completedOrder = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'total' => 100,
            'status' => 'completed',
        ]);
        $completedOrder->forceFill(['created_at' => now()->subDay()])->save();
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
            ->assertJsonCount(2, 'recent_orders')
            ->assertJsonPath('orders_by_status_period.week.new_orders', 1)
            ->assertJsonPath('orders_by_status_period.week.delivered', 1)
            ->assertJsonPath('sales_chart.week.current.1', 100)
            ->assertJsonCount(7, 'sales_chart.month.labels')
            ->assertJsonPath('sales_chart.month.labels.0', 'Sep 1')
            ->assertJsonPath('sales_chart.month.labels.6', 'Sep 30')
            ->assertJsonPath('sales_chart.month.current.2', 100)
            ->assertJsonCount(6, 'sales_chart.year.labels')
            ->assertJsonPath('sales_chart.year.labels.0', 'Jan')
            ->assertJsonPath('sales_chart.year.labels.4', 'Sep')
            ->assertJsonPath('sales_chart.year.current.4', 100)
            ->assertJsonPath('recent_orders.0.buyer_name', $buyer->name)
            ->assertJsonCount(1, 'low_stock_products')
            ->assertJsonPath('low_stock_products.0.name', 'Low stock item')
            ->assertJsonPath('announcement.title', 'Seller update');

        $this->assertEquals(100, array_sum($response->json('sales_chart.month.current')));
        $this->assertEquals(100, array_sum($response->json('sales_chart.year.current')));

        $this->actingAs($sellerUser)
            ->get(route('seller.dashboard'))
            ->assertOk()
            ->assertViewIs('pages.seller.dashboard')
            ->assertSee('Welcome, One Store!')
            ->assertSee('₱100.00')
            ->assertSee('ORD-', false)
            ->assertSee('Low stock item')
            ->assertSee('Seller update')
            ->assertSee('Store operations update.');
    }

    public function test_non_sellers_cannot_access_the_seller_dashboard(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->getJson('/api/v1/seller/dashboard')
            ->assertForbidden();
    }
}
