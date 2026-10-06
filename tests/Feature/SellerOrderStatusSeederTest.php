<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\OrderItem;
use App\Models\Seller\Product;
use App\Models\Seller\ProductReview;
use App\Models\Seller\Seller;
use App\Models\User;
use Database\Seeders\ProductReviewSeeder;
use Database\Seeders\SellerOrderStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerOrderStatusSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_repeatable_orders_for_each_seller_status_flow_stage(): void
    {
        $productOwner = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $productOwner->id,
            'store_name' => 'Existing Product Seller',
            'registration_status' => 'active',
        ]);
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Actual catalog product',
            'price' => 749.50,
            'status' => 'active',
            'is_archived' => false,
        ]);

        $this->seed(ProductReviewSeeder::class);
        $seededReviews = ProductReview::query()
            ->where('product_id', $product->id)
            ->orderBy('id')
            ->limit(3)
            ->get();

        $this->seed(SellerOrderStatusSeeder::class);
        $this->seed(SellerOrderStatusSeeder::class);

        $this->assertDatabaseCount('orders', 3);
        $this->assertDatabaseCount('order_items', 3);
        $this->assertDatabaseCount('order_status_histories', 6);
        $this->assertSame(
            ['new', 'preparing', 'to_ship'],
            Order::query()->where('seller_id', $seller->id)->orderBy('id')->pluck('status')->all(),
        );
        $this->assertDatabaseHas('orders', [
            'order_number' => 'SE-DEMO-ORDER-STATUS-TO-SHIP',
            'seller_id' => $seller->id,
            'status' => 'to_ship',
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'product_name' => 'Actual catalog product',
            'unit_price' => 749.50,
        ]);
        $this->assertSame(
            $seededReviews->pluck('buyer_id')->all(),
            Order::query()->orderBy('id')->pluck('buyer_id')->all(),
        );
        $this->assertSame(
            $seededReviews->pluck('product_id')->all(),
            OrderItem::query()->orderBy('id')->pluck('product_id')->all(),
        );
        $this->assertDatabaseMissing('users', ['email' => 'buyer@shopease.test']);

        $this->actingAs($productOwner)
            ->getJson('/api/v1/seller/order-status?per_page=10')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.seller_id', $seller->id);

        $toShipOrder = Order::query()->where('status', 'to_ship')->firstOrFail();
        $this->assertSame(3, OrderStatusHistory::query()->where('order_id', $toShipOrder->id)->count());

        $this->getJson("/api/v1/seller/orders/{$toShipOrder->id}")
            ->assertOk()
            ->assertJsonPath('items.0.product_id', $product->id)
            ->assertJsonPath('items.0.product_name', 'Actual catalog product')
            ->assertJsonPath('buyer.id', $seededReviews[2]->buyer_id);

        $this->get('/seller/order-status')
            ->assertOk()
            ->assertSee('sellerOrderStatusConfig')
            ->assertSee('Actual catalog product');
    }

    public function test_it_does_not_seed_orders_when_no_active_product_exists(): void
    {
        $this->seed(SellerOrderStatusSeeder::class);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseMissing('users', ['email' => 'seller@shopease.test']);
    }

    public function test_it_does_not_seed_orders_without_customer_feedback(): void
    {
        $this->seed(SellerOrderStatusSeeder::class);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }
}
