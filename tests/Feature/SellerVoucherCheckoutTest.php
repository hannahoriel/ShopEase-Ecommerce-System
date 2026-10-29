<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Buyer\CartItem;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\Seller\SellerVoucher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerVoucherCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_create_list_and_pause_only_their_own_vouchers(): void
    {
        [$sellerUser, $seller] = $this->seller();
        $this->actingAs($sellerUser)->postJson('/api/v1/seller/vouchers', $this->voucherPayload())
            ->assertCreated()->assertJsonPath('data.id', 'SAVE10');

        $voucher = SellerVoucher::where('seller_id', $seller->id)->firstOrFail();
        $this->getJson('/api/v1/seller/vouchers')->assertOk()->assertJsonCount(1, 'data');
        $this->patchJson('/api/v1/seller/vouchers/'.$voucher->id, ['enabled' => false])
            ->assertOk()->assertJsonPath('data.enabled', false);

        [$otherSellerUser] = $this->seller();
        $this->actingAs($otherSellerUser)->patchJson('/api/v1/seller/vouchers/'.$voucher->id, ['enabled' => true])->assertNotFound();
    }

    public function test_buyer_can_preview_and_redeem_a_shop_voucher_and_pays_fifty_peso_shipping(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [$sellerUser, $seller, $product] = $this->sellerWithProduct();
        CartItem::create(['user_id' => $buyer->id, 'product_id' => $product->id, 'quantity' => 1]);
        $voucher = SellerVoucher::create([
            'seller_id' => $seller->id, 'code' => 'SAVE10', 'title' => 'Ten Percent Off',
            'discount_type' => 'percent', 'discount_value' => 10, 'minimum_spend' => 500,
            'maximum_discount' => 100, 'eligible_buyers' => 'all', 'stacking' => 'none',
            'quantity' => 2, 'claimed' => 0, 'per_buyer' => 1, 'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(), 'enabled' => true,
        ]);

        $this->actingAs($buyer)->postJson('/api/v1/buyer/vouchers/preview', ['code' => 'save10'])
            ->assertOk()->assertJsonPath('data.discount', 100);
        $this->getJson('/api/v1/buyer/vouchers')->assertOk()->assertJsonPath('data.0.id', 'SAVE10');
        $response = $this->postJson('/api/v1/buyer/orders', [
            'delivery_name' => 'Test Buyer', 'delivery_phone' => '09171234567',
            'delivery_address' => '1 Test Street, Manila', 'payment_method' => 'Cash on Delivery',
            'voucher_code' => 'SAVE10',
        ])->assertCreated()->assertJsonPath('orders.0.total', 950);

        $order = Order::findOrFail($response->json('orders.0.id'));
        $this->assertSame('50.00', $order->shipping_fee);
        $this->assertSame('100.00', $order->voucher_discount);
        $this->assertSame('SAVE10', $order->voucher_code);
        $this->assertSame(1, $voucher->fresh()->claimed);
        $this->assertDatabaseHas('seller_voucher_redemptions', ['seller_voucher_id' => $voucher->id, 'buyer_id' => $buyer->id, 'order_id' => $order->id]);

        CartItem::create(['user_id' => $buyer->id, 'product_id' => $product->id, 'quantity' => 1]);
        $this->postJson('/api/v1/buyer/orders', [
            'delivery_name' => 'Test Buyer', 'delivery_phone' => '09171234567',
            'delivery_address' => '1 Test Street, Manila', 'payment_method' => 'Cash on Delivery',
            'voucher_code' => 'SAVE10',
        ])->assertUnprocessable();
    }

    public function test_buyer_cannot_apply_a_voucher_below_its_minimum_spend(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        [, $seller, $product] = $this->sellerWithProduct(400);
        CartItem::create(['user_id' => $buyer->id, 'product_id' => $product->id, 'quantity' => 1]);
        SellerVoucher::create([
            'seller_id' => $seller->id, 'code' => 'MIN500', 'title' => 'Minimum Spend',
            'discount_type' => 'fixed', 'discount_value' => 50, 'minimum_spend' => 500,
            'maximum_discount' => 50, 'eligible_buyers' => 'all', 'stacking' => 'none',
            'quantity' => 5, 'per_buyer' => 1, 'starts_at' => now()->subDay(), 'ends_at' => now()->addDay(), 'enabled' => true,
        ]);
        $this->actingAs($buyer)->postJson('/api/v1/buyer/vouchers/preview', ['code' => 'MIN500'])->assertUnprocessable();
    }

    public function test_seller_can_target_products_by_sku(): void
    {
        [$sellerUser, $seller, $product] = $this->sellerWithProduct();
        $product->update(['sku' => 'TEST-SKU-100']);
        $payload = $this->voucherPayload();
        $payload['code'] = 'SKU10';
        $payload['eligibleItems'] = 'selected';
        $payload['productIds'] = 'TEST-SKU-100';

        $this->actingAs($sellerUser)->postJson('/api/v1/seller/vouchers', $payload)
            ->assertCreated()->assertJsonPath('data.productIds.0', (string) $product->id);
    }

    public function test_voucher_cannot_be_previewed_or_redeemed_after_its_allocation_is_used(): void
    {
        [$sellerUser, $seller, $product] = $this->sellerWithProduct();
        $voucher = SellerVoucher::create([
            'seller_id' => $seller->id, 'code' => 'ONLYONE', 'title' => 'One Use Only',
            'discount_type' => 'fixed', 'discount_value' => 50, 'minimum_spend' => 0,
            'maximum_discount' => 50, 'eligible_buyers' => 'all', 'stacking' => 'none',
            'quantity' => 1, 'claimed' => 0, 'per_buyer' => 1,
            'starts_at' => now()->subDay(), 'ends_at' => now()->addDay(), 'enabled' => true,
        ]);
        $placeOrder = fn () => [
            'delivery_name' => 'Test Buyer', 'delivery_phone' => '09171234567',
            'delivery_address' => '1 Test Street, Manila', 'payment_method' => 'Cash on Delivery',
            'voucher_code' => 'ONLYONE', 'voucher_seller_id' => $seller->id,
        ];

        $firstBuyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        CartItem::create(['user_id' => $firstBuyer->id, 'product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($firstBuyer)->postJson('/api/v1/buyer/orders', $placeOrder())->assertCreated();
        $this->assertSame(1, $voucher->fresh()->claimed);
        $this->assertSame(0, $voucher->fresh()->quantity - $voucher->fresh()->claimed);

        $secondBuyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        CartItem::create(['user_id' => $secondBuyer->id, 'product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($secondBuyer)->postJson('/api/v1/buyer/vouchers/preview', ['code' => 'ONLYONE'])
            ->assertUnprocessable()->assertJsonValidationErrors('voucher_code');
        $this->postJson('/api/v1/buyer/orders', $placeOrder())->assertUnprocessable();

        $this->assertSame(1, $voucher->fresh()->claimed);
        $this->assertDatabaseCount('seller_voucher_redemptions', 1);
        $this->assertDatabaseCount('orders', 1);
    }

    private function seller(): array
    {
        $user = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create(['user_id' => $user->id, 'store_name' => 'Voucher Shop', 'registration_status' => 'active']);
        return [$user, $seller];
    }

    private function sellerWithProduct(float $price = 1000): array
    {
        [$user, $seller] = $this->seller();
        $product = Product::create([
            'seller_id' => $seller->id, 'name' => 'Test Product', 'price' => $price,
            'sku' => 'TEST-PRODUCT',
            'stock_quantity' => 10, 'status' => 'active', 'is_archived' => false,
        ]);
        return [$user, $seller, $product];
    }

    private function voucherPayload(): array
    {
        return [
            'title' => 'Ten Percent Off', 'code' => 'SAVE10', 'discountType' => 'percent',
            'discountValue' => 10, 'minimumSpend' => 100, 'maximumDiscount' => 100,
            'eligibleItems' => 'all', 'eligibleBuyers' => 'all', 'stacking' => 'none',
            'quantity' => 5, 'perBuyer' => 1, 'starts' => now()->addMinute()->toDateTimeString(),
            'ends' => now()->addMonth()->toDateTimeString(), 'enabled' => true,
        ];
    }
}
