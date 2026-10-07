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
            'photos' => ['products/actual-catalog-product.jpg'],
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
        $newDemoOrder = Order::query()
            ->where('order_number', 'SE-DEMO-ORDER-STATUS-NEW-01')
            ->firstOrFail();
        foreach (['preparing', 'to_ship'] as $staleStatus) {
            OrderStatusHistory::create([
                'order_id' => $newDemoOrder->id,
                'from_status' => $staleStatus === 'preparing' ? 'new' : 'preparing',
                'to_status' => $staleStatus,
                'changed_by' => $productOwner->id,
                'notes' => 'Stale status history.',
            ]);
        }
        $this->seed(SellerOrderStatusSeeder::class);

        $this->assertDatabaseCount('orders', 9);
        $this->assertDatabaseCount('order_items', 9);
        $this->assertDatabaseCount('order_status_histories', 12);
        $this->assertSame(
            7,
            Order::query()->where('status', 'new')->count(),
        );
        $this->assertSame(
            6,
            Order::query()->where('order_number', 'like', 'SE-DEMO-ORDER-STATUS-NEW-%')->count(),
        );
        $this->assertSame('new', $newDemoOrder->fresh()->status);
        $this->assertDatabaseMissing('order_status_histories', [
            'order_id' => $newDemoOrder->id,
            'to_status' => 'preparing',
        ]);
        $this->assertDatabaseMissing('order_status_histories', [
            'order_id' => $newDemoOrder->id,
            'to_status' => 'to_ship',
        ]);
        $this->assertSame(
            ['new', 'preparing', 'to_ship', 'new', 'new', 'new', 'new', 'new', 'new'],
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
            array_merge(
                $seededReviews->pluck('buyer_id')->all(),
                array_map(
                    fn (int $index): int => $seededReviews[$index % $seededReviews->count()]->buyer_id,
                    range(0, 5),
                ),
            ),
            Order::query()->orderBy('id')->pluck('buyer_id')->all(),
        );
        $this->assertSame(
            array_merge(
                $seededReviews->pluck('product_id')->all(),
                array_map(
                    fn (int $index): int => $seededReviews[$index % $seededReviews->count()]->product_id,
                    range(0, 5),
                ),
            ),
            OrderItem::query()->orderBy('id')->pluck('product_id')->all(),
        );
        $this->assertDatabaseMissing('users', ['email' => 'buyer@shopease.test']);

        $this->actingAs($productOwner)
            ->getJson('/api/v1/seller/order-status?per_page=10')
            ->assertOk()
            ->assertJsonCount(9, 'data')
            ->assertJsonPath('data.0.seller_id', $seller->id);

        $toShipOrder = Order::query()->where('status', 'to_ship')->firstOrFail();
        $this->assertSame(3, OrderStatusHistory::query()->where('order_id', $toShipOrder->id)->count());

        $this->getJson("/api/v1/seller/orders/{$toShipOrder->id}")
            ->assertOk()
            ->assertJsonPath('items.0.product_id', $product->id)
            ->assertJsonPath('items.0.product_name', 'Actual catalog product')
            ->assertJsonPath('items.0.product.photos.0', 'products/actual-catalog-product.jpg')
            ->assertJsonPath('buyer.id', $seededReviews[2]->buyer_id);

        $this->get('/seller/order-status')
            ->assertOk()
            ->assertSee('sellerOrderStatusConfig')
            ->assertSee('/storage/products/actual-catalog-product.jpg')
            ->assertSee('Actual catalog product');

        $css = file_get_contents(resource_path('css/seller/order-status.css'));
        $this->assertIsString($css);
        $this->assertStringContainsString('text-overflow: ellipsis !important;', $css);
        $this->assertStringContainsString('minmax(0, 1.45fr)', $css);
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
