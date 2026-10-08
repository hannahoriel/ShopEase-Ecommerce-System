<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Buyer\BuyerNotification;
use App\Models\Buyer\CartItem;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Database\Seeders\DemoMarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoMarketplaceSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_accounts_and_connected_marketplace_fixtures_are_seeded_idempotently(): void
    {
        $this->seed();
        $this->seed(DemoMarketplaceSeeder::class);

        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $buyer = User::where('email', 'buyer@gmail.com')->firstOrFail();
        $sellerUser = User::where('email', 'seller@gmail.com')->firstOrFail();
        $seller = Seller::where('user_id', $sellerUser->id)->firstOrFail();
        $backpack = Product::where('seller_id', $seller->id)->where('sku', 'SE-DEMO-BACKPACK')->firstOrFail();
        $order = Order::where('order_number', 'SE-DEMO-BUYER-PREPARING')->firstOrFail();

        $this->assertTrue(Hash::check('Admin@123', $admin->password));
        $this->assertTrue(Hash::check('Buyer@1', $buyer->password));
        $this->assertTrue(Hash::check('Seller@1', $sellerUser->password));
        $this->postJson('/api/v1/auth/login', ['email' => 'admin@gmail.com', 'password' => 'Admin@123'])
            ->assertOk()
            ->assertJsonPath('role', User::ROLE_ADMIN);
        $this->postJson('/api/v1/auth/login', ['email' => 'buyer@gmail.com', 'password' => 'Buyer@1'])
            ->assertOk()
            ->assertJsonPath('role', User::ROLE_BUYER);
        $this->postJson('/api/v1/auth/login', ['email' => 'seller@gmail.com', 'password' => 'Seller@1'])
            ->assertOk()
            ->assertJsonPath('role', User::ROLE_SELLER);
        $this->assertDatabaseHas('admins', ['email' => 'admin@gmail.com']);
        $this->assertDatabaseHas('buyers', ['user_id' => $buyer->id, 'registration_status' => 'active']);
        $this->assertSame('active', $seller->registration_status);
        $this->assertSame($buyer->id, $order->buyer_id);
        $this->assertSame($seller->id, $order->seller_id);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => Product::where('sku', 'SE-DEMO-EARBUDS')->value('id'),
        ]);
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $buyer->id,
            'product_id' => $backpack->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('commission_transactions', ['order_id' => $order->id]);
        $this->assertDatabaseHas('shipments', [
            'order_id' => Order::where('order_number', 'SE-DEMO-BUYER-IN-TRANSIT')->value('id'),
        ]);
        $this->assertDatabaseHas('order_cancellation_requests', [
            'order_id' => Order::where('order_number', 'SE-DEMO-BUYER-CANCEL-REVIEW')->value('id'),
            'status' => 'rejected',
        ]);
        $this->assertSame(1, BuyerNotification::where('type', 'cancellation_rejected')->count());
        $this->assertSame(2, CartItem::where('user_id', $buyer->id)->count());
        $this->assertDatabaseHas('registrations', ['email' => 'demo-registration-pending@shopease.test', 'status' => 'pending']);
        $this->assertDatabaseHas('platform_policies', ['title' => 'Buyer Purchase Policy']);

        $this->actingAs($buyer)
            ->getJson('/api/v1/buyer/orders')
            ->assertOk()
            ->assertJsonFragment(['order_number' => 'SE-DEMO-BUYER-PENDING']);
        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/inventory')
            ->assertOk()
            ->assertJsonFragment(['sku' => 'SE-DEMO-BACKPACK']);
    }
}
