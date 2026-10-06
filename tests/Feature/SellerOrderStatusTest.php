<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\Shipment;
use App\Models\Seller\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerOrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_list_and_update_only_owned_orders(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-16 12:00:00'));
        [$sellerUser, $seller] = $this->seller('Seller One');
        [, $otherSeller] = $this->seller('Other Seller');
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $ownedOrder = $this->order($seller, $buyer, 'pending', 559);
        $otherOrder = $this->order($otherSeller, $buyer, 'pending', 999);

        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/order-status?status[]=pending&per_page=10')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ownedOrder->id)
            ->assertJsonPath('data.0.buyer.name', $buyer->name);

        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/orders/{$ownedOrder->id}/status", [
                'status' => 'preparing',
                'notes' => 'Seller started preparing the order.',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'preparing');

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $ownedOrder->id,
            'from_status' => 'pending',
            'to_status' => 'preparing',
            'changed_by' => $sellerUser->id,
        ]);

        $this->actingAs($sellerUser)
            ->getJson("/api/v1/seller/orders/{$otherOrder->id}")
            ->assertNotFound();
    }

    public function test_seller_can_schedule_preparing_order_and_invalid_transition_is_rejected(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-16 12:00:00'));
        [$sellerUser, $seller] = $this->seller('Seller One');
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $order = $this->order($seller, $buyer, 'preparing', 559);

        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$order->id}/schedule", [
                'pickup_date' => '2026-09-18',
                'pickup_time' => '14:30',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'to_ship');

        $scheduledOrder = $order->fresh();
        $this->assertSame('to_ship', $scheduledOrder->status);
        $this->assertSame('2026-09-18', $scheduledOrder->pickup_date->toDateString());
        $this->assertSame('14:30', $scheduledOrder->pickup_time->format('H:i'));
        $this->assertSame(1, OrderStatusHistory::where('order_id', $order->id)->count());
        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/order-status')
            ->assertOk()
            ->assertJsonPath('data.0.status', 'to_ship')
            ->assertJsonPath('data.0.pickup_date_display', '2026-09-18')
            ->assertJsonPath('data.0.pickup_time_display', '14:30');

        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/orders/{$order->id}/status", ['status' => 'delivered'])
            ->assertStatus(422);

        $newOrder = $this->order($seller, $buyer, 'pending', 559);
        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$newOrder->id}/schedule", [
                'pickup_date' => '2026-09-18',
                'pickup_time' => '14:30',
            ])
            ->assertStatus(422);
        $this->assertNull($newOrder->fresh()->pickup_date);
        $this->assertSame('pending', $newOrder->fresh()->status);
    }

    public function test_seller_can_print_a_waybill_and_qr_scans_record_location_and_status(): void
    {
        [$sellerUser, $seller] = $this->seller('Seller One');
        $seller->update([
            'house_number' => '25',
            'street' => 'Rizal Street',
            'barangay' => 'Poblacion',
            'municipality' => 'Calamba',
            'province' => 'Laguna',
        ]);
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $order = $this->order($seller, $buyer, 'pending', 559);

        $waybillResponse = $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$order->id}/waybill")
            ->assertOk()
            ->assertJsonPath('order.id', $order->id)
            ->assertJsonPath('order.seller.store_name', 'Seller One Store')
            ->assertJsonPath('order.seller.house_number', '25')
            ->assertJsonPath('order.seller.street', 'Rizal Street')
            ->assertJsonPath('order.seller.barangay', 'Poblacion')
            ->assertJsonPath('order.seller.municipality', 'Calamba')
            ->assertJsonPath('order.seller.province', 'Laguna')
            ->assertJsonPath('shipment.courier', 'Ease Express');

        $scanUrl = $waybillResponse->json('scan_url');
        $shipment = Shipment::where('order_id', $order->id)->firstOrFail();
        $this->assertSame(64, strlen($shipment->scan_token));
        $this->assertStringContainsString($shipment->scan_token, $scanUrl);

        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$order->id}/waybill")
            ->assertOk()
            ->assertJsonPath('shipment.scan_token', $shipment->scan_token)
            ->assertJsonPath('shipment.tracking_number', $shipment->tracking_number);

        $this->get(route('parcel.scan.show', $shipment->scan_token))
            ->assertOk()
            ->assertSee('Parcel tracking update')
            ->assertSee('updates will be available after the order is prepared');

        $this->from(route('parcel.scan.show', $shipment->scan_token))
            ->post(route('parcel.scan.store', $shipment->scan_token), [
                'location' => 'Calamba sorting hub',
                'status' => 'delivered',
            ])
            ->assertRedirect(route('parcel.scan.show', $shipment->scan_token))
            ->assertSessionHasErrors('status');
        $this->assertSame('pending', $order->fresh()->status);

        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/orders/{$order->id}/status", ['status' => 'preparing'])
            ->assertOk();
        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$order->id}/schedule", [
                'pickup_date' => now()->addDay()->toDateString(),
                'pickup_time' => '14:30',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'to_ship');

        $this->post(route('parcel.scan.store', $shipment->scan_token), [
            'location' => 'Calamba sorting hub',
            'status' => 'in_transit',
        ])->assertRedirect(route('parcel.scan.show', $shipment->scan_token));

        $this->assertSame('in_transit', $order->fresh()->status);
        $this->assertSame('Calamba sorting hub', $shipment->fresh()->current_location);
        $this->assertDatabaseHas('shipment_scans', [
            'shipment_id' => $shipment->id,
            'status' => 'in_transit',
            'location' => 'Calamba sorting hub',
        ]);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'from_status' => 'to_ship',
            'to_status' => 'in_transit',
            'notes' => 'Parcel scan recorded at Calamba sorting hub.',
        ]);
        $this->get(route('parcel.scan.show', $shipment->scan_token))
            ->assertOk()
            ->assertSee('Calamba sorting hub')
            ->assertSee('Out For Delivery');
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

    private function order(Seller $seller, User $buyer, string $status, float $total): Order
    {
        return Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'total' => $total,
            'status' => $status,
        ]);
    }
}
