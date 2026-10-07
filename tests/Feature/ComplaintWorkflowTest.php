<?php

namespace Tests\Feature;

use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_complaint_is_saved_and_available_to_the_admin_ui_and_api(): void
    {
        [$buyer, $order] = $this->createBuyerOrder();

        $created = $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/orders/{$order->id}/complaints", [
                'type' => 'Wrong Item',
                'description' => 'I received a different item from what I ordered.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.reference', 'CMP-0001')
            ->assertJsonPath('data.status', 'open');

        $complaintId = $created->json('data.id');

        $this->assertDatabaseHas('complaints', [
            'id' => $complaintId,
            'user_id' => $buyer->id,
            'order_id' => $order->id,
            'type' => 'Wrong Item',
            'status' => 'open',
        ]);
        $this->assertDatabaseHas('complaint_updates', [
            'complaint_id' => $complaintId,
            'type' => 'submitted',
        ]);

        $this->actingAs($buyer)
            ->getJson('/api/v1/buyer/orders')
            ->assertOk()
            ->assertJsonPath('data.0.complaint.reference', 'CMP-0001')
            ->assertJsonPath('data.0.complaint.status', 'open');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/complaints')
            ->assertOk()
            ->assertJsonPath('counts.open', 1)
            ->assertJsonPath('data.0.id', 'CMP-0001')
            ->assertJsonPath('data.0.party1', $buyer->name)
            ->assertJsonPath('data.0.type', 'Wrong Item')
            ->assertJsonPath('data.0.orderId', $order->order_number)
            ->assertJsonPath('data.0.status', 'open');

        $this->actingAs($admin)
            ->get('/admin/complaints-disputes')
            ->assertOk()
            ->assertSee('I received a different item from what I ordered.')
            ->assertSee('Wrong Item');
    }

    public function test_admin_status_changes_are_saved_with_audit_updates(): void
    {
        [$buyer, $order] = $this->createBuyerOrder();
        $complaint = Complaint::create([
            'user_id' => $buyer->id,
            'order_id' => $order->id,
            'subject' => 'Late Delivery',
            'type' => 'Late Delivery',
            'description' => 'The package has not arrived.',
            'status' => 'open',
        ]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->patchJson("/admin/complaints-disputes/{$complaint->id}/status", [
                'status' => 'in-progress',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'in-progress')
            ->assertJsonPath('data.update.actor', $admin->name);

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'status' => 'in_progress',
        ]);
        $this->assertDatabaseHas('complaint_updates', [
            'complaint_id' => $complaint->id,
            'user_id' => $admin->id,
            'type' => 'status',
            'message' => 'Status updated from Open to In progress.',
        ]);
    }

    public function test_buyers_cannot_submit_complaints_for_another_users_order(): void
    {
        [$owner, $order] = $this->createBuyerOrder();
        $otherBuyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($otherBuyer)
            ->postJson("/api/v1/buyer/orders/{$order->id}/complaints", [
                'type' => 'Other',
                'description' => 'This should not be accepted.',
            ])
            ->assertNotFound();

        $this->actingAs($owner)
            ->postJson("/api/v1/buyer/orders/{$order->id}/complaints", [
                'type' => 'Unknown',
                'description' => 'This should fail validation.',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('type');

        $this->actingAs($otherBuyer)
            ->getJson('/api/v1/admin/complaints')
            ->assertForbidden();
        $this->actingAs($otherBuyer)
            ->get('/admin/complaints-disputes')
            ->assertForbidden();

        $this->actingAs($admin)
            ->get('/admin/complaints-disputes')
            ->assertOk();

        $this->assertDatabaseCount('complaints', 0);
    }

    /**
     * @return array{0: User, 1: Order}
     */
    private function createBuyerOrder(): array
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Test Shop',
            'registration_status' => 'active',
        ]);
        $order = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_number' => 'SE-TEST-0001',
            'total' => 1250,
            'status' => 'completed',
            'payment_method' => 'Cash on Delivery',
        ]);

        return [$buyer, $order];
    }
}
