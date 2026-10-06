<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\Shipment;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerShippingStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_list_shipping_orders_and_advance_their_own_shipment(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-16 12:00:00'));
        [$sellerUser, $seller] = $this->seller('Seller One');
        [, $otherSeller] = $this->seller('Other Seller');
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $order = $this->order($seller, $buyer, 'to_ship');
        $inTransitOrder = $this->order($seller, $buyer, 'in_transit');
        $this->order($seller, $buyer, 'preparing');
        $this->order($seller, $buyer, 'new');
        $otherOrder = $this->order($otherSeller, $buyer, 'to_ship');

        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/shipping-status?per_page=10')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $order->id, 'status' => 'to_ship'])
            ->assertJsonFragment(['id' => $inTransitOrder->id, 'status' => 'in_transit']);
        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/shipping-status?status=in_transit')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/shipping/{$order->id}/status", [
                'status' => 'in_transit',
                'courier' => 'Ease Express',
                'estimated_delivery' => '2026-09-20',
                'shipping_fee' => 60,
            ])
            ->assertOk()
            ->assertJsonPath('status', 'in_transit')
            ->assertJsonPath('shipment.courier', 'Ease Express');

        $shipment = Shipment::where('order_id', $order->id)->firstOrFail();
        $this->assertStringStartsWith('SE-', $shipment->tracking_number);
        $this->assertNotNull($shipment->picked_up_at);
        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/shipping-status')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $order->id, 'status' => 'in_transit'])
            ->assertJsonFragment(['id' => $inTransitOrder->id, 'status' => 'in_transit']);
        $this->actingAs($sellerUser)
            ->getJson("/api/v1/seller/shipping/{$order->id}")
            ->assertOk()
            ->assertJsonPath('shipment.tracking_number', $shipment->tracking_number)
            ->assertJsonPath('status_history.0.to_status', 'in_transit');

        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/shipping/{$order->id}/status", ['status' => 'out_for_delivery'])
            ->assertOk()
            ->assertJsonPath('status', 'out_for_delivery');

        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/shipping/{$order->id}/status", ['status' => 'delivered'])
            ->assertOk()
            ->assertJsonPath('status', 'delivered');

        $this->assertNotNull($shipment->fresh()->delivered_at);
        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/shipping-status')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $order->id, 'status' => 'delivered']);

        $this->actingAs($sellerUser)
            ->getJson("/api/v1/seller/shipping/{$otherOrder->id}")
            ->assertNotFound();
    }

    public function test_invalid_shipping_transition_is_rejected_and_web_page_is_protected(): void
    {
        [$sellerUser, $seller] = $this->seller('Seller One');
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $order = $this->order($seller, $buyer, 'to_ship');
        $this->order($seller, $buyer, 'in_transit');
        $this->order($seller, $buyer, 'preparing');
        $this->order($seller, $buyer, 'new');
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Canvas Tote',
            'price' => 559,
            'photos' => ['products/canvas-tote.jpg'],
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 559,
        ]);

        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/shipping/{$order->id}/status", ['status' => 'delivered'])
            ->assertStatus(422);

        $this->actingAs($sellerUser)
            ->get(route('seller.shipping.status'))
            ->assertOk()
            ->assertViewIs('pages.seller.shipping-status')
            ->assertSee('SE-TEST-' . $seller->id . '-TO_SHIP')
            ->assertSee('SE-TEST-' . $seller->id . '-IN_TRANSIT')
            ->assertDontSee('SE-TEST-' . $seller->id . '-PREPARING', false)
            ->assertDontSee('SE-TEST-' . $seller->id . '-NEW', false)
            ->assertSee('storage/products/canvas-tote.jpg')
            ->assertSee('shipping-product-photo', false)
            ->assertSee('sellerShippingStatusConfig');

        $this->actingAs($sellerUser)
            ->getJson("/api/v1/seller/shipping/{$order->id}")
            ->assertOk()
            ->assertJsonPath('items.0.product.photos.0', 'products/canvas-tote.jpg');

        $this->actingAs($buyer = User::factory()->create(['role' => User::ROLE_BUYER]))
            ->getJson('/api/v1/seller/shipping-status')
            ->assertForbidden();
    }

    private function seller(string $name): array
    {
        $user = User::factory()->create(['role' => User::ROLE_SELLER, 'name' => $name]);
        $seller = Seller::create([
            'user_id' => $user->id,
            'store_name' => $name . ' Store',
            'registration_status' => 'active',
        ]);

        return [$user, $seller];
    }

    private function order(Seller $seller, User $buyer, string $status): Order
    {
        return Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_number' => 'SE-TEST-' . $seller->id . '-' . strtoupper($status),
            'total' => 559,
            'status' => $status,
        ]);
    }
}
