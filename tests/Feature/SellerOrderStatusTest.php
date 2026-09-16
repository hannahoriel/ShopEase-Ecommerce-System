<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
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
            ->patchJson("/api/v1/seller/orders/{$order->id}/status", ['status' => 'delivered'])
            ->assertStatus(422);
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
