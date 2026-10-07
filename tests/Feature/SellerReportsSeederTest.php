<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\Shipment;
use App\Models\Admin\ShipmentScan;
use App\Models\Seller\Product;
use App\Models\Seller\ProductReview;
use App\Models\Seller\Seller;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\SellerReportsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerReportsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_connected_report_dashboard_shipping_and_feedback_data_idempotently(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-07 12:00:00'));

        $sellerUser = User::factory()->create([
            'role' => User::ROLE_SELLER,
            'name' => 'Reports Demo Seller',
        ]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Reports Demo Store',
            'registration_status' => 'active',
        ]);
        Product::create([
            'seller_id' => $seller->id,
            'name' => 'Reports Demo Product',
            'price' => 250,
            'stock_quantity' => 20,
            'status' => 'active',
            'is_archived' => false,
        ]);

        $this->seed(SellerReportsSeeder::class);
        $this->seed(SellerReportsSeeder::class);

        $orders = Order::query()
            ->where('seller_id', $seller->id)
            ->where('order_number', 'like', 'SE-DEMO-REPORT-'.$seller->id.'-%')
            ->get();
        $reviews = ProductReview::query()->where('seller_id', $seller->id)->get();

        $this->assertCount(9, $orders);
        $this->assertGreaterThanOrEqual(5, $reviews->count());
        $this->assertSame(1, $orders->where('status', 'new')->count());
        $this->assertSame(1, $orders->where('status', 'preparing')->count());
        $this->assertSame(1, $orders->where('status', 'to_ship')->count());
        $this->assertSame(1, $orders->where('status', 'in_transit')->count());
        $this->assertSame(1, $orders->where('status', 'out_for_delivery')->count());
        $this->assertSame(1, $orders->where('status', 'delivered')->count());
        $this->assertSame(3, $orders->where('status', 'completed')->count());
        $this->assertSame(7, Shipment::query()
            ->whereIn('order_id', $orders->pluck('id'))
            ->count());
        $this->assertSame(42, OrderStatusHistory::query()
            ->whereIn('order_id', $orders->pluck('id'))
            ->count());
        $this->assertSame(22, ShipmentScan::query()
            ->whereIn('shipment_id', Shipment::query()
                ->whereIn('order_id', $orders->pluck('id'))
                ->select('id'))
            ->count());

        foreach ($orders as $order) {
            $orderProductId = $order->items()->value('product_id');
            $this->assertDatabaseHas('order_items', [
                'order_id' => $order->id,
                'product_id' => $orderProductId,
            ]);
            $this->assertDatabaseHas('product_reviews', [
                'seller_id' => $seller->id,
                'product_id' => $orderProductId,
                'buyer_id' => $order->buyer_id,
            ]);
        }

        $dashboardResponse = $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/dashboard')
            ->assertOk()
            ->assertJsonPath('orders_by_status.new', 1)
            ->assertJsonPath('orders_by_status.preparing', 1)
            ->assertJsonPath('orders_by_status.to_ship', 1)
            ->assertJsonPath('orders_by_status.in_transit', 1)
            ->assertJsonPath('orders_by_status.out_for_delivery', 1)
            ->assertJsonPath('orders_by_status.delivered', 1)
            ->assertJsonPath('orders_by_status.completed', 3);

        $completedOrders = $orders->where('status', 'completed');
        $currentMonthSales = $completedOrders
            ->filter(fn (Order $order): bool => $order->created_at->isSameMonth(Carbon::now()))
            ->sum('total');
        $previousMonthSales = $completedOrders
            ->filter(fn (Order $order): bool => $order->created_at->isSameMonth(Carbon::now()->subMonth()))
            ->sum('total');
        $this->assertEquals(
            $currentMonthSales,
            array_sum($dashboardResponse->json('sales_chart.month.current')),
        );
        $this->assertEquals(
            $previousMonthSales,
            array_sum($dashboardResponse->json('sales_chart.month.previous')),
        );

        $reportResponse = $this->getJson('/api/v1/seller/reports')
            ->assertOk()
            ->assertJsonPath('stats.total_orders', 3)
            ->assertJsonPath('orders.total', 3);
        $this->assertNotEmpty($reportResponse->json('top_products'));
        $this->assertEquals(
            $completedOrders->sum('total'),
            $reportResponse->json('stats.total_sales'),
        );
        $this->assertEquals(
            $currentMonthSales,
            array_sum($reportResponse->json('sales_chart.current')),
        );
        $this->assertEquals(
            $previousMonthSales,
            array_sum($reportResponse->json('sales_chart.previous')),
        );

        $this->getJson('/api/v1/seller/shipping-status')
            ->assertOk()
            ->assertJsonPath('total', 4);

        $feedbackResponse = $this->getJson('/api/v1/seller/feedback')->assertOk();
        $this->assertSame($reviews->count(), $feedbackResponse->json('feedback.total'));
        $this->assertNotEmpty($feedbackResponse->json('products'));
        $this->assertSame(
            $reviews->count(),
            collect($feedbackResponse->json('products'))->sum('total_reviews'),
        );
    }

    public function test_it_skips_report_fixtures_when_no_active_products_exist(): void
    {
        $this->seed(SellerReportsSeeder::class);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('shipments', 0);
        $this->assertDatabaseCount('product_reviews', 0);
    }
}
