<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerOrderStatusFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_status_index_accepts_frontend_to_ship_tab_filter(): void
    {
        $sellerUser = User::factory()->create([
            'role' => User::ROLE_SELLER,
            'email' => 'seller-flow@example.com',
        ]);

        Seller::create([
            'user_id' => $sellerUser->id,
            'registration_status' => 'active',
        ]);

        $buyer = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'email' => 'buyer-flow@example.com',
        ]);

        $order = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $sellerUser->id,
            'total' => 559.00,
            'commission_amount' => 55.90,
            'status' => 'to_ship',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time' => '10:00:00',
        ]);

        $response = $this->actingAs($sellerUser, 'sanctum')
            ->getJson('/api/v1/seller/order-status?status=to-ship');

        $response->assertOk();
        $this->assertSame($order->id, $response->json('data.0.id'));
    }

    public function test_order_status_update_accepts_frontend_status_format(): void
    {
        $sellerUser = User::factory()->create([
            'role' => User::ROLE_SELLER,
            'email' => 'seller-update@example.com',
        ]);

        Seller::create([
            'user_id' => $sellerUser->id,
            'registration_status' => 'active',
        ]);

        $buyer = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'email' => 'buyer-update@example.com',
        ]);

        $order = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $sellerUser->id,
            'total' => 559.00,
            'commission_amount' => 55.90,
            'status' => 'preparing',
        ]);

        $response = $this->actingAs($sellerUser, 'sanctum')
            ->patchJson('/api/v1/seller/orders/' . $order->id . '/status', [
                'status' => 'to-ship',
                'notes' => 'Ready for collection',
            ]);

        $response->assertOk();
        $this->assertSame('to_ship', $order->fresh()->status);
    }
}
