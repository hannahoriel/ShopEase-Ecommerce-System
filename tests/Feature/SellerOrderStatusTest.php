<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\SellerPickupAttempt;
use App\Models\Admin\Shipment;
use App\Models\Rider\EarningEntry;
use App\Models\Rider\Rider;
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
        $this->assertDatabaseHas('shipments', [
            'order_id' => $order->id,
            'courier' => 'Ease Express',
        ]);
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

    public function test_buyer_cancellation_clears_pickup_schedule_and_is_attributed_to_buyer(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-16 12:00:00'));
        [$sellerUser, $seller] = $this->seller('Seller One');
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $order = $this->order($seller, $buyer, 'to_ship', 559);
        $order->update([
            'pickup_date' => '2026-09-18',
            'pickup_time' => '14:30',
        ]);

        $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/orders/{$order->id}/cancel", [
                'reason' => 'changed_mind',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertNull($order->fresh()->pickup_date);
        $this->assertNull($order->fresh()->pickup_time);

        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/order-status')
            ->assertOk()
            ->assertJsonPath('data.0.cancellation_actor', 'buyer')
            ->assertJsonPath('data.0.pickup_date', null)
            ->assertJsonPath('data.0.pickup_time', null);
    }

    public function test_seller_approval_of_buyer_cancellation_clears_pickup_schedule(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-16 18:00:00'));
        [$sellerUser, $seller] = $this->seller('Seller One');
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $order = $this->order($seller, $buyer, 'to_ship', 559);
        $order->update([
            'pickup_date' => '2026-09-18',
            'pickup_time' => '14:30',
        ]);
        $order->forceFill(['created_at' => now()->subHours(6)])->save();

        $requestId = $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/orders/{$order->id}/cancel", [
                'reason' => 'changed_mind',
            ])
            ->assertStatus(202)
            ->json('data.cancellation_request.id');

        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$order->id}/cancellation-requests/{$requestId}/approve")
            ->assertOk()
            ->assertJsonPath('status', 'cancelled')
            ->assertJsonPath('cancellation_actor', 'buyer')
            ->assertJsonPath('pickup_date', null)
            ->assertJsonPath('pickup_time', null);

        $this->assertNull($order->fresh()->pickup_date);
        $this->assertNull($order->fresh()->pickup_time);
    }

    public function test_seller_cancellation_clears_pickup_schedule_and_is_attributed_to_seller(): void
    {
        [$sellerUser, $seller] = $this->seller('Seller One');
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $order = $this->order($seller, $buyer, 'to_ship', 559);
        $order->update([
            'pickup_date' => '2026-09-18',
            'pickup_time' => '14:30',
        ]);

        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$order->id}/cancel-shipment", [
                'reason' => 'Unable to fulfill the order',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'cancelled')
            ->assertJsonPath('cancellation_actor', 'seller')
            ->assertJsonPath('pickup_date', null)
            ->assertJsonPath('pickup_time', null);

        $this->assertNull($order->fresh()->pickup_date);
        $this->assertNull($order->fresh()->pickup_time);
    }

    public function test_three_missed_seller_pickups_cancel_the_order_and_post_one_rider_deduction(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-16 17:00:00', 'Asia/Manila'));
        [$sellerUser, $seller] = $this->seller('Seller One');
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $riderUser = User::factory()->create([
            'role' => User::ROLE_RIDER,
            'registration_status' => 'active',
        ]);
        $rider = Rider::create([
            'user_id' => $riderUser->id,
            'municipal_hub' => 'Calamba',
            'logistics_operation' => 'pickup',
            'last_name' => 'Rider',
            'first_name' => 'Test',
            'sex' => 'other',
            'contact_no' => '09170000000',
            'birthday' => '1990-01-01',
            'age' => 36,
            'province' => 'Laguna',
            'municipality' => 'Calamba',
            'barangay' => 'Real',
            'street' => 'Main Street',
            'house_number' => '1',
            'vehicle' => 'Motorcycle',
            'plate_number' => 'ABC 1234',
            'registration_status' => 'active',
        ]);
        $order = $this->order($seller, $buyer, 'preparing', 559);

        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$order->id}/schedule", [
                'pickup_date' => '2026-09-16',
                'pickup_time' => '17:30',
            ])
            ->assertOk()
            ->assertJsonPath('seller_pickup_attempts.0.attempt_number', 1);
        $shipment = $order->shipment()->firstOrFail();
        $shipment->update(['rider_id' => $rider->id]);
        SellerPickupAttempt::query()->where('order_id', $order->id)->update(['rider_id' => $rider->id]);

        foreach ([
            ['date' => '2026-09-16', 'time' => '18:00', 'reschedule' => '2026-09-17'],
            ['date' => '2026-09-17', 'time' => '18:00', 'reschedule' => '2026-09-18'],
        ] as $attempt) {
            Carbon::setTestNow(Carbon::parse($attempt['date'].' '.$attempt['time'], 'Asia/Manila'));
            $this->artisan('orders:process-missed-seller-pickups')->assertSuccessful();
            $this->assertDatabaseHas('seller_pickup_attempts', [
                'order_id' => $order->id,
                'attempt_number' => SellerPickupAttempt::query()->where('order_id', $order->id)->count(),
                'status' => 'missed',
            ]);
            $this->assertSame('to_ship', $order->fresh()->status);
            $this->assertNull($order->fresh()->pickup_date);
            $this->assertSame(0, EarningEntry::query()->where('order_id', $order->id)->count());

            Carbon::setTestNow(Carbon::parse($attempt['date'].' 18:15', 'Asia/Manila'));
            $this->actingAs($sellerUser)
                ->postJson("/api/v1/seller/orders/{$order->id}/schedule", [
                    'pickup_date' => $attempt['reschedule'],
                    'pickup_time' => '10:00',
                ])
                ->assertOk()
                ->assertJsonPath('seller_pickup_attempts.'.(SellerPickupAttempt::query()->where('order_id', $order->id)->count() - 1).'.status', 'scheduled');
        }

        Carbon::setTestNow(Carbon::parse('2026-09-18 17:59', 'Asia/Manila'));
        $this->artisan('orders:process-missed-seller-pickups')->assertSuccessful();
        $this->assertSame('to_ship', $order->fresh()->status);

        Carbon::setTestNow(Carbon::parse('2026-09-18 18:00', 'Asia/Manila'));
        $this->artisan('orders:process-missed-seller-pickups')->assertSuccessful();

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertDatabaseHas('seller_pickup_attempts', [
            'order_id' => $order->id,
            'attempt_number' => 3,
            'status' => 'missed',
        ]);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'to_status' => 'cancelled',
            'changed_by' => null,
        ]);
        $deduction = EarningEntry::query()->where('order_id', $order->id)->firstOrFail();
        $this->assertSame($rider->id, $deduction->rider_id);
        $this->assertSame('pickup_no_show_deduction', $deduction->type);
        $this->assertSame('-20.00', $deduction->amount);
        $this->assertDatabaseHas('buyer_notifications', [
            'order_id' => $order->id,
            'title' => 'Order cancelled: pickup rider did not collect it',
        ]);
        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/finance')
            ->assertOk()
            ->assertJsonPath('data.earnings.0.type', 'pickup_no_show_deduction')
            ->assertJsonPath('data.earnings.0.amount', '-20.00')
            ->assertJsonPath('data.earnings_total', -20);
        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/earnings')
            ->assertOk()
            ->assertJsonPath('data.data.0.type', 'pickup_no_show_deduction')
            ->assertJsonPath('data.data.0.amount', '-20.00');

        $this->artisan('orders:process-missed-seller-pickups')->assertSuccessful();
        $this->assertSame(1, EarningEntry::query()->where('order_id', $order->id)->count());
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
