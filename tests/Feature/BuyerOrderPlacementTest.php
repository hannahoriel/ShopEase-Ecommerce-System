<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Buyer\BuyerNotification;
use App\Models\Buyer\CartItem;
use App\Models\Seller\Product;
use App\Models\Seller\ProductOption;
use App\Models\Seller\ProductVariantCombination;
use App\Models\Seller\Seller;
use App\Models\User;
use Carbon\Carbon;
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
        ProductOption::create([
            'product_id' => $firstProduct->id,
            'type' => 'color',
            'name' => 'Black',
            'stock' => 10,
        ]);
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
        $this->assertDatabaseCount('buyer_notifications', 2);
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
        $this->assertSame(8, $firstProduct->fresh()->stock_quantity);
        $this->assertSame(8, ProductOption::where('product_id', $firstProduct->id)->value('stock'));
        $this->assertSame(9, $secondProduct->fresh()->stock_quantity);

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

    public function test_insufficient_stock_prevents_order_and_preserves_the_cart(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [, $product] = $this->sellerWithProduct('First Store', 'Limited Item', 100);
        $product->update(['stock_quantity' => 1]);
        CartItem::create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

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
        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseCount('cart_items', 1);
        $this->assertSame(1, $product->fresh()->stock_quantity);
    }

    public function test_buyer_cancellation_restores_reserved_stock_only_once(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [$seller, $product] = $this->sellerWithProduct('First Store', 'Wireless Earbuds', 1200);
        CartItem::create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $orderId = $this->actingAs($buyer)
            ->postJson('/api/v1/buyer/orders', [
                'delivery_name' => 'Buyer Example',
                'delivery_phone' => '09171234567',
                'delivery_address' => '1 Main Street, Manila',
                'payment_method' => 'Cash on Delivery',
            ])
            ->assertCreated()
            ->json('orders.0.id');

        $this->assertSame(7, $product->fresh()->stock_quantity);
        $this->actingAs(User::findOrFail($seller->user_id))
            ->getJson('/api/v1/seller/inventory')
            ->assertOk()
            ->assertJsonPath('data.0.stock_quantity', 7);

        $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/orders/{$orderId}/cancel")
            ->assertUnprocessable()
            ->assertJsonValidationErrors('reason');

        $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/orders/{$orderId}/cancel", [
                'reason' => 'other',
                'other_reason' => 'I placed the wrong order.',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled')
            ->assertJsonPath('data.can_cancel', false)
            ->assertJsonPath('data.auto_cancelled', true)
            ->assertJsonPath('data.cancellation_request.buyer_other_reason', 'I placed the wrong order.');

        $this->assertSame(10, $product->fresh()->stock_quantity);
        $this->actingAs(User::findOrFail($seller->user_id))
            ->getJson('/api/v1/seller/inventory')
            ->assertOk()
            ->assertJsonPath('data.0.stock_quantity', 10);

        $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/orders/{$orderId}/cancel", [
                'reason' => 'changed_mind',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled')
            ->assertJsonPath('message', 'This order has already been cancelled.');

        $this->assertSame(10, $product->fresh()->stock_quantity);
        $this->assertDatabaseCount('order_status_histories', 1);
    }

    public function test_buyer_cancellation_after_five_hours_waits_for_seller_review(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-08 18:00:00'));
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [$seller, $product] = $this->sellerWithProduct('First Store', 'Wireless Earbuds', 1200);
        $order = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_number' => 'SE-TEST-LATE-CANCEL',
            'total' => 1200,
            'status' => 'preparing',
            'delivery_name' => 'Buyer Example',
            'delivery_phone' => '09171234567',
            'delivery_address' => '1 Main Street, Manila',
            'payment_method' => 'Cash on Delivery',
        ]);
        $order->created_at = now()->subHours(5)->subSecond();
        $order->save();
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => $product->price,
        ]);
        $product->update(['stock_quantity' => 8]);

        $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/orders/{$order->id}/cancel", [
                'reason' => 'ordered_by_mistake',
            ])
            ->assertStatus(202)
            ->assertJsonPath('data.status', 'preparing')
            ->assertJsonPath('data.auto_cancelled', false)
            ->assertJsonPath('data.cancellation_request.status', 'pending');

        $requestId = $order->latestCancellationRequest()->value('id');
        $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/orders/{$order->id}/cancel", [
                'reason' => 'changed_mind',
            ])
            ->assertStatus(202)
            ->assertJsonPath('data.cancellation_request.id', $requestId);
        $this->assertDatabaseCount('order_cancellation_requests', 1);

        $this->assertSame(8, $product->fresh()->stock_quantity);
        $this->assertSame('preparing', $order->fresh()->status);

        $this->actingAs($buyer)
            ->getJson('/api/v1/buyer/orders')
            ->assertOk()
            ->assertJsonPath('data.0.can_cancel', false)
            ->assertJsonPath('data.0.cancellation_request.buyer_reason', 'ordered_by_mistake');

        $sellerUser = User::findOrFail($seller->user_id);
        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/order-status')
            ->assertOk()
            ->assertJsonPath('data.0.latest_cancellation_request.status', 'pending')
            ->assertJsonPath('data.0.latest_cancellation_request.buyer_reason', 'ordered_by_mistake');

        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/orders/{$order->id}/status", ['status' => 'to_ship'])
            ->assertUnprocessable();

        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$order->id}/cancellation-requests/{$order->latestCancellationRequest->id}/reject", [
                'reason' => 'other',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('other_reason');

        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$order->id}/cancellation-requests/{$order->latestCancellationRequest->id}/reject", [
                'reason' => 'other',
                'other_reason' => 'The order is already being packed.',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'rejected')
            ->assertJsonPath('data.seller_other_reason', 'The order is already being packed.');

        $this->assertDatabaseHas('buyer_notifications', [
            'buyer_id' => $buyer->id,
            'order_id' => $order->id,
            'type' => 'cancellation_rejected',
            'title' => 'Cancellation request declined',
        ]);
        $this->actingAs($buyer)
            ->getJson('/api/v1/buyer/notifications')
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('data.0.data.order_number', 'SE-TEST-LATE-CANCEL')
            ->assertJsonPath('data.0.data.seller_other_reason', 'The order is already being packed.')
            ->assertJsonPath('data.0.message', 'The seller declined your cancellation request. Reason: The order is already being packed.');

        $this->actingAs($buyer)
            ->getJson('/api/v1/buyer/orders')
            ->assertOk()
            ->assertJsonPath('data.0.cancellation_request.status', 'rejected')
            ->assertJsonPath('data.0.cancellation_request.seller_other_reason', 'The order is already being packed.');

        $this->assertSame(8, $product->fresh()->stock_quantity);
        $this->assertSame('preparing', $order->fresh()->status);

        Carbon::setTestNow();
    }

    public function test_buyer_notifications_are_private_and_can_be_marked_read(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $otherBuyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [$seller] = $this->sellerWithProduct('First Store', 'Wireless Earbuds', 1200);
        $order = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_number' => 'SE-TEST-NOTIFICATION-API',
            'total' => 1200,
            'status' => 'pending',
            'delivery_name' => 'Buyer Example',
            'delivery_phone' => '09171234567',
            'delivery_address' => '1 Main Street, Manila',
            'payment_method' => 'Cash on Delivery',
        ]);
        $firstNotification = BuyerNotification::createForOrder($order, 'order', 'Order received', 'Your order was received.');
        BuyerNotification::createForOrder($order, 'shipping', 'On the way', 'Your package is on the way.');

        $this->actingAs($buyer)
            ->getJson('/api/v1/buyer/notifications')
            ->assertOk()
            ->assertJsonPath('unread_count', 2)
            ->assertJsonCount(2, 'data');

        $this->actingAs($otherBuyer)
            ->getJson('/api/v1/buyer/notifications')
            ->assertOk()
            ->assertJsonPath('unread_count', 0)
            ->assertJsonCount(0, 'data');
        $this->actingAs($otherBuyer)
            ->patchJson("/api/v1/buyer/notifications/{$firstNotification->id}/read")
            ->assertNotFound();

        $this->actingAs($buyer)
            ->patchJson("/api/v1/buyer/notifications/{$firstNotification->id}/read")
            ->assertOk()
            ->assertJsonPath('data.id', $firstNotification->id);
        $this->actingAs($buyer)
            ->postJson('/api/v1/buyer/notifications/read-all')
            ->assertOk()
            ->assertJsonPath('updated_count', 1);
        $this->assertDatabaseCount('buyer_notifications', 2);
        $this->assertDatabaseMissing('buyer_notifications', [
            'id' => $firstNotification->id,
            'read_at' => null,
        ]);
    }

    public function test_buyer_cancellation_at_five_hour_boundary_is_automatically_approved(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-08 18:00:00'));
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [$seller, $product] = $this->sellerWithProduct('First Store', 'Wireless Earbuds', 1200);
        $order = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_number' => 'SE-TEST-EXACT-FIVE-HOURS',
            'total' => 1200,
            'status' => 'pending',
            'delivery_name' => 'Buyer Example',
            'delivery_phone' => '09171234567',
            'delivery_address' => '1 Main Street, Manila',
            'payment_method' => 'Cash on Delivery',
        ]);
        $order->created_at = now()->subHours(5);
        $order->save();
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => $product->price,
        ]);
        $product->update(['stock_quantity' => 9]);

        $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/orders/{$order->id}/cancel", [
                'reason' => 'changed_mind',
            ])
            ->assertOk()
            ->assertJsonPath('data.auto_cancelled', true)
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertSame(10, $product->fresh()->stock_quantity);
        Carbon::setTestNow();
    }

    public function test_seller_can_approve_late_buyer_cancellation_and_restore_stock(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-08 18:00:00'));
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [$seller, $product] = $this->sellerWithProduct('First Store', 'Wireless Earbuds', 1200);
        $order = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_number' => 'SE-TEST-APPROVE-CANCEL',
            'total' => 1200,
            'status' => 'to_ship',
            'delivery_name' => 'Buyer Example',
            'delivery_phone' => '09171234567',
            'delivery_address' => '1 Main Street, Manila',
            'payment_method' => 'Cash on Delivery',
        ]);
        $order->created_at = now()->subHours(6);
        $order->save();
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => $product->price,
        ]);
        $product->update(['stock_quantity' => 8]);

        $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/orders/{$order->id}/cancel", [
                'reason' => 'changed_mind',
            ])
            ->assertStatus(202);

        $requestId = $order->latestCancellationRequest()->value('id');
        $this->actingAs(User::findOrFail($seller->user_id))
            ->postJson("/api/v1/seller/orders/{$order->id}/cancellation-requests/{$requestId}/approve")
            ->assertOk()
            ->assertJsonPath('status', 'cancelled')
            ->assertJsonPath('latest_cancellation_request.status', 'approved');

        $this->assertSame(10, $product->fresh()->stock_quantity);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'to_status' => 'cancelled',
            'notes' => 'Buyer cancellation request approved by seller.',
        ]);

        Carbon::setTestNow();
    }

    public function test_order_reserves_the_exact_connected_variant_stock(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [, $product] = $this->sellerWithProduct('First Store', 'Wireless Earbuds', 1200);
        ProductOption::create([
            'product_id' => $product->id,
            'type' => 'color',
            'name' => 'Black',
            'stock' => 0,
        ]);
        $combination = ProductVariantCombination::create([
            'product_id' => $product->id,
            'choices' => ['colors' => 'Black'],
            'pricing_mode' => 'base',
            'additions' => [],
            'stock' => 3,
            'available' => true,
        ]);
        CartItem::create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'color' => 'Black',
        ]);

        $this->actingAs($buyer)
            ->postJson('/api/v1/buyer/orders', [
                'delivery_name' => 'Buyer Example',
                'delivery_phone' => '09171234567',
                'delivery_address' => '1 Main Street, Manila',
                'payment_method' => 'Cash on Delivery',
            ])
            ->assertCreated();

        $this->assertSame(1, $product->fresh()->stock_quantity);
        $this->assertSame(1, ProductOption::where('product_id', $product->id)->value('stock'));
        $this->assertSame(1, $combination->fresh()->stock);
    }

    public function test_buyer_product_details_include_grouped_variant_combinations(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [, $product] = $this->sellerWithProduct('First Store', 'Wireless Earbuds', 1200);
        ProductOption::create([
            'product_id' => $product->id,
            'type' => 'variation',
            'name' => 'Pack of 2',
            'stock' => 4,
        ]);
        ProductOption::create([
            'product_id' => $product->id,
            'type' => 'color',
            'name' => 'Black',
            'stock' => 4,
        ]);
        ProductVariantCombination::create([
            'product_id' => $product->id,
            'choices' => ['variations' => 'Pack of 2', 'colors' => 'Black'],
            'pricing_mode' => 'fixed',
            'additions' => [],
            'stock' => 4,
            'available' => true,
        ]);

        $this->actingAs($buyer)
            ->getJson("/api/v1/buyer/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('pricing_mode', 'fixed')
            ->assertJsonPath('connected_variants.0.variations', 'Pack of 2')
            ->assertJsonPath('connected_variants.0.colors', 'Black')
            ->assertJsonPath('connected_variants.0.stock', 4)
            ->assertJsonPath('variations.0.name', 'Pack of 2');
    }

    public function test_seller_cancellation_also_restores_reserved_stock(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [$seller, $product] = $this->sellerWithProduct('First Store', 'Wireless Earbuds', 1200);
        CartItem::create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $orderId = $this->actingAs($buyer)
            ->postJson('/api/v1/buyer/orders', [
                'delivery_name' => 'Buyer Example',
                'delivery_phone' => '09171234567',
                'delivery_address' => '1 Main Street, Manila',
                'payment_method' => 'Cash on Delivery',
            ])
            ->assertCreated()
            ->json('orders.0.id');

        $sellerUser = User::findOrFail($seller->user_id);
        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/orders/{$orderId}/status", ['status' => 'preparing'])
            ->assertOk()
            ->assertJsonPath('status', 'preparing');
        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$orderId}/schedule", [
                'pickup_date' => now()->addDay()->toDateString(),
                'pickup_time' => '14:30',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'to_ship');

        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$orderId}/cancel-shipment", [
                'reason' => 'Other',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('other_reason');

        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$orderId}/cancel-shipment", [
                'reason' => 'Other',
                'other_reason' => 'Courier pickup is unavailable.',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'cancelled');

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $orderId,
            'from_status' => 'to_ship',
            'to_status' => 'cancelled',
            'notes' => 'Scheduled shipment cancelled by seller. Reason: Other: Courier pickup is unavailable.',
        ]);
        $this->assertSame(10, $product->fresh()->stock_quantity);
        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$orderId}/cancel-shipment", [
                'reason' => 'Item is out of stock',
            ])
            ->assertUnprocessable();
        $this->assertSame(10, $product->fresh()->stock_quantity);
    }

    public function test_buyer_can_cancel_until_shipment_is_in_transit(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [$seller] = $this->sellerWithProduct('First Store', 'Wireless Earbuds', 1200);
        $order = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_number' => 'SE-TEST-PRETRANSIT',
            'total' => 1200,
            'status' => 'to_ship',
            'delivery_name' => 'Buyer Example',
            'delivery_phone' => '09171234567',
            'delivery_address' => '1 Main Street, Manila',
            'payment_method' => 'Cash on Delivery',
        ]);

        $this->actingAs($buyer)
            ->getJson('/api/v1/buyer/orders')
            ->assertOk()
            ->assertJsonPath('data.0.can_cancel', true);

        $order->update(['status' => 'in_transit']);

        $this->actingAs($buyer)
            ->getJson('/api/v1/buyer/orders')
            ->assertOk()
            ->assertJsonPath('data.0.can_cancel', false);

        $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/orders/{$order->id}/cancel", ['reason' => 'changed_mind'])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'This order can no longer be cancelled.');
    }

    public function test_buyer_cannot_cancel_another_buyers_order(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $anotherBuyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [$seller, $product] = $this->sellerWithProduct('First Store', 'Wireless Earbuds', 1200);
        $order = Order::create([
            'buyer_id' => $anotherBuyer->id,
            'seller_id' => $seller->id,
            'order_number' => 'SE-TEST-ORDER',
            'total' => 1200,
            'status' => 'pending',
            'delivery_name' => 'Buyer Example',
            'delivery_phone' => '09171234567',
            'delivery_address' => '1 Main Street, Manila',
            'payment_method' => 'Cash on Delivery',
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => $product->price,
        ]);

        $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/orders/{$order->id}/cancel")
            ->assertNotFound();

        $this->assertSame(10, $product->fresh()->stock_quantity);
        $this->assertSame('pending', $order->fresh()->status);
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
