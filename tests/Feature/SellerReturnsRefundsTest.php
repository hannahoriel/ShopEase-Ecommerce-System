<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerReturnsRefundsTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_submit_return_request_and_seller_can_review_it(): void
    {
        [$buyer, $sellerUser, $order, $itemId] = $this->marketplace('completed');

        $created = $this->actingAs($buyer, 'sanctum')->postJson("/api/v1/buyer/orders/{$order->id}/returns", [
            'return_type' => 'return',
            'reason' => 'Item arrived damaged',
            'evidence_description' => 'Photo of damaged packaging attached.',
            'items' => [['order_item_id' => $itemId, 'quantity' => 1]],
        ])->assertCreated()->assertJsonPath('data.status', 'pending');

        $returnId = $created->json('data.id');
        $this->actingAs($sellerUser, 'sanctum')
            ->patchJson("/api/v1/seller/returns-refunds/{$returnId}/decision", [
                'decision' => 'accepted', 'solution' => 'Return approved.',
            ])->assertOk()->assertJsonPath('data.status', 'accepted');

        $this->assertDatabaseHas('order_return_events', ['order_return_id' => $returnId, 'status' => 'accepted']);
        $this->assertDatabaseHas('order_returns', ['id' => $returnId, 'reviewed_by' => $sellerUser->id]);
    }

    public function test_seller_can_complete_refund_only_request_and_other_sellers_cannot_see_it(): void
    {
        [$buyer, $sellerUser, $order, $itemId] = $this->marketplace('completed');
        $return = OrderReturn::create([
            'order_id' => $order->id, 'return_type' => 'refund', 'status' => 'accepted',
            'reason' => 'Defective item', 'return_items' => [['order_item_id' => $itemId, 'quantity' => 1]],
            'refund_amount' => 125, 'requested_at' => now(), 'reviewed_at' => now(),
        ]);

        $this->actingAs($sellerUser, 'sanctum')->getJson('/api/v1/seller/returns-refunds')
            ->assertOk()->assertJsonFragment(['id' => $return->id]);
        $this->actingAs($sellerUser, 'sanctum')->postJson("/api/v1/seller/returns-refunds/{$return->id}/refund")
            ->assertOk()->assertJsonPath('data.status', 'refunded');
        $this->assertNotNull($return->fresh()->refunded_at);

        $otherUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        Seller::create(['user_id' => $otherUser->id, 'registration_status' => 'active']);
        $this->actingAs($otherUser, 'sanctum')->getJson('/api/v1/seller/returns-refunds')
            ->assertOk()->assertJsonMissing(['id' => $return->id]);
    }

    public function test_buyer_cannot_request_returns_for_someone_elses_order(): void
    {
        [$buyer, , $order, $itemId] = $this->marketplace('completed');
        $otherBuyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($otherBuyer, 'sanctum')->postJson("/api/v1/buyer/orders/{$order->id}/returns", [
            'return_type' => 'refund', 'reason' => 'Damaged',
            'items' => [['order_item_id' => $itemId, 'quantity' => 1]],
        ])->assertNotFound();
    }

    private function marketplace(string $status): array
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create(['user_id' => $sellerUser->id, 'registration_status' => 'active']);
        $order = Order::create([
            'buyer_id' => $buyer->id, 'seller_id' => $seller->id, 'order_number' => 'RET-' . uniqid(),
            'total' => 250, 'status' => $status, 'delivery_name' => $buyer->name,
            'delivery_phone' => '09171234567', 'delivery_address' => 'Test Address',
        ]);
        $item = $order->items()->create(['product_name' => 'Test item', 'quantity' => 2, 'unit_price' => 125]);
        return [$buyer, $sellerUser, $order, $item->id];
    }
}
