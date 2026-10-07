<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Admin\Shipment;
use App\Models\Seller\Seller;
use App\Models\User;
use Database\Seeders\SellerShippingStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerShippingStatusSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_idempotent_shipments_for_all_demo_shipping_stage_orders(): void
    {
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Shipping Seeder Shop',
            'registration_status' => 'active',
        ]);
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $readyOrder = $this->makeOrder($seller, $buyer, 'to_ship', 'SE-DEMO-ORDER-STATUS-TO-SHIP');
        $this->makeOrder($seller, $buyer, 'preparing', 'SE-DEMO-ORDER-STATUS-PREPARING');
        $inTransitOrder = $this->makeOrder($seller, $buyer, 'in_transit', 'SE-DEMO-ORDER-STATUS-IN-TRANSIT');
        $this->makeOrder($seller, $buyer, 'to_ship', 'REAL-ORDER-123');

        $this->seed(SellerShippingStatusSeeder::class);
        $this->seed(SellerShippingStatusSeeder::class);

        $this->assertDatabaseCount('shipments', 2);
        $this->assertDatabaseHas('shipments', ['order_id' => $inTransitOrder->id]);
        $shipment = Shipment::query()->where('order_id', $readyOrder->id)->firstOrFail();
        $this->assertSame('Ease Express', $shipment->courier);
        $this->assertStringStartsWith('SE-', $shipment->tracking_number);
        $this->assertNotNull($shipment->estimated_delivery);

        $response = $this->actingAs($sellerUser)->getJson('/api/v1/seller/shipping-status');
        $response->assertOk();
        $orderPayload = collect($response->json('data'))->firstWhere('id', $readyOrder->id);
        $this->assertSame($shipment->tracking_number, $orderPayload['shipment']['tracking_number']);
        $this->assertNotNull(Shipment::query()->where('order_id', $inTransitOrder->id)->first());
    }

    private function makeOrder(Seller $seller, User $buyer, string $status, string $number): Order
    {
        return Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_number' => $number,
            'total' => 500,
            'status' => $status,
        ]);
    }
}
