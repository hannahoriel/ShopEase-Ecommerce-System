<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_api_returns_seller_scoped_live_order_and_product_data(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-07 12:00:00'));

        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Reports Store',
            'registration_status' => 'active',
        ]);
        $otherUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $otherSeller = Seller::create([
            'user_id' => $otherUser->id,
            'store_name' => 'Other Store',
            'registration_status' => 'active',
        ]);
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Seller Product',
            'price' => 100,
            'stock_quantity' => 10,
            'status' => 'active',
        ]);
        $otherProduct = Product::create([
            'seller_id' => $otherSeller->id,
            'name' => 'Other Seller Product',
            'price' => 900,
            'stock_quantity' => 10,
            'status' => 'active',
        ]);

        $includedOrder = $this->createCompletedOrder($seller->id, $buyer->id, 200, 20, '2026-10-06');
        OrderItem::create([
            'order_id' => $includedOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 100,
        ]);

        $this->createCompletedOrder($seller->id, $buyer->id, 75, 5, '2026-09-30');
        $otherOrder = $this->createCompletedOrder($otherSeller->id, $buyer->id, 900, 90, '2026-10-06');
        OrderItem::create([
            'order_id' => $otherOrder->id,
            'product_id' => $otherProduct->id,
            'product_name' => $otherProduct->name,
            'quantity' => 10,
            'unit_price' => 90,
        ]);
        Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'total' => 500,
            'commission_amount' => 50,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($sellerUser)->getJson(
            '/api/v1/seller/reports?start_date=2026-10-01&end_date=2026-10-07&period=week&per_page=7'
        );
        $response->assertOk()
            ->assertJsonPath('stats.total_sales', 200)
            ->assertJsonPath('stats.total_orders', 1)
            ->assertJsonPath('stats.total_profit', 180)
            ->assertJsonPath('stats.gross_profit_margin', 90)
            ->assertJsonPath('performance.admin_commission', 20)
            ->assertJsonPath('top_products.0.name', 'Seller Product')
            ->assertJsonPath('top_products.0.quantity', 2)
            ->assertJsonPath('orders.total', 1)
            ->assertJsonPath('orders.data.0.id', $includedOrder->order_number)
            ->assertJsonPath('orders.data.0.commission', 20)
            ->assertJsonPath('orders.data.0.profit', 180)
            ->assertJsonPath('filters.period', 'week');

        $this->assertEquals(200, array_sum($response->json('sales_chart.current')));
        $this->assertEquals(75, array_sum($response->json('sales_chart.previous')));

        $this->actingAs($sellerUser)
            ->get(route('seller.reports'))
            ->assertOk()
            ->assertViewIs('pages.seller.reports')
            ->assertSee('Seller Product')
            ->assertSee('sellerReportsData', false);
    }

    public function test_reports_api_validates_date_ranges_and_rejects_non_sellers(): void
    {
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Reports Store',
            'registration_status' => 'active',
        ]);

        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/reports?start_date=2026-10-08&end_date=2026-10-07')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('end_date');

        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $this->actingAs($buyer)
            ->getJson('/api/v1/seller/reports')
            ->assertForbidden();
    }

    private function createCompletedOrder(
        int $sellerId,
        int $buyerId,
        float $total,
        float $commission,
        string $date
    ): Order {
        $order = Order::create([
            'buyer_id' => $buyerId,
            'seller_id' => $sellerId,
            'order_number' => 'RPT-' . $sellerId . '-' . str_replace('-', '', $date),
            'total' => $total,
            'commission_amount' => $commission,
            'status' => 'completed',
        ]);
        $order->forceFill([
            'created_at' => Carbon::parse($date)->setTime(12, 0),
        ])->save();

        return $order;
    }
}
