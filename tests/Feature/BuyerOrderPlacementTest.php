<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Buyer\CartItem;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerOrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_places_real_orders_and_receives_unique_order_numbers_for_qr_codes(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [$firstSeller, $firstProduct] = $this->sellerWithProduct('First Store', 'Wireless Earbuds', 1200);
        [, $secondProduct] = $this->sellerWithProduct('Second Store', 'Watch', 2400);
        $this->actingAs($buyer)
            ->get('/buyer/checkout')
            ->assertOk()
            ->assertSee('buyerCheckoutConfig')
            ->assertSee('checkoutOrderResults');

        CartItem::create([
            'user_id' => $buyer->id,
            'product_id' => $firstProduct->id,
            'quantity' => 2,
            'color' => 'Black',
        ]);
        CartItem::create([
            'user_id' => $buyer->id,
            'product_id' => $secondProduct->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($buyer)->postJson('/api/v1/buyer/orders', [
            'delivery_name' => 'Buyer Example',
            'delivery_phone' => '09171234567',
            'delivery_address' => '1 Main Street, Manila',
            'payment_method' => 'Cash on Delivery',
        ]);

        $response->assertCreated()
            ->assertJsonCount(2, 'orders')
            ->assertJsonPath('orders.0.items.0.product_name', 'Wireless Earbuds');

        $orderNumbers = collect($response->json('orders'))->pluck('order_number');
        $this->assertCount(2, $orderNumbers->unique());
        $this->assertStringContainsString(
            '/api/v1/orders/verify/'.$orderNumbers->first(),
            $response->json('orders.0.qr_payload')
        );
        $verified = $this->getJson('/api/v1/orders/verify/'.$orderNumbers->first());
        $verified->assertOk()
            ->assertJsonPath('valid', true)
            ->assertJsonPath('order_number', $orderNumbers->first())
            ->assertJsonMissingPath('buyer_id');
        $this->assertDatabaseCount('orders', 2);
        $this->assertDatabaseHas('orders', [
            'buyer_id' => $buyer->id,
            'seller_id' => $firstSeller->id,
            'total' => 2400,
            'commission_amount' => 240,
            'delivery_address' => '1 Main Street, Manila',
        ]);
        $this->assertDatabaseHas('commission_transactions', [
            'order_id' => Order::where('order_number', $orderNumbers->first())->value('id'),
            'seller_id' => $firstSeller->id,
            'order_amount' => 2400,
            'commission_rate' => 10,
            'commission_amount' => 240,
            'status' => 'pending',
        ]);
        $this->assertDatabaseCount('commission_transactions', 2);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $firstProduct->id,
            'product_name' => 'Wireless Earbuds',
            'color' => 'Black',
            'quantity' => 2,
            'unit_price' => 1200,
        ]);
        $this->assertDatabaseCount('cart_items', 0);

        $this->actingAs($buyer)
            ->getJson('/api/v1/buyer/orders')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['order_number' => $orderNumbers->first()])
            ->assertJsonFragment(['product_name' => 'Wireless Earbuds']);

        $this->actingAs($buyer)
            ->get('/buyer/my-purchases')
            ->assertOk()
            ->assertSee('buyerPurchasesConfig');
    }

    public function test_buyer_cannot_place_an_order_with_an_empty_cart(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->postJson('/api/v1/buyer/orders', [
                'delivery_name' => 'Buyer Example',
                'delivery_phone' => '09171234567',
                'delivery_address' => '1 Main Street, Manila',
                'payment_method' => 'Cash on Delivery',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cart');

        $this->assertDatabaseCount('orders', 0);
    }

    private function sellerWithProduct(string $storeName, string $productName, float $price): array
    {
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => $storeName,
            'registration_status' => 'active',
        ]);
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => $productName,
            'price' => $price,
            'stock_quantity' => 10,
            'status' => 'active',
            'is_archived' => false,
        ]);

        return [$seller, $product];
    }
}
