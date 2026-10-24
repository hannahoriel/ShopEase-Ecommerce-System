<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Admin\Shipment;
use App\Models\Buyer\Buyer;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Rider\DeliveryProof;
use App\Models\Rider\Rider;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RiderDeliveryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_logistics_assigns_a_delivery_and_rider_status_updates_reach_the_buyer_and_seller(): void
    {
        Storage::fake('local');
        [$logisticsUser, $logistics, $branch] = $this->logistics('North Logistics');
        [$riderUser, $rider] = $this->rider();
        [$sellerUser, $seller] = $this->seller('Seller One');
        $buyer = $this->buyer();
        $order = $this->order($seller, $buyer, 'to_ship');

        $this->actingAs($logisticsUser)
            ->getJson('/api/v1/logistics/branches')
            ->assertOk()
            ->assertJsonPath('data.0.id', $branch->id);

        $this->actingAs($logisticsUser)
            ->getJson('/api/v1/logistics/riders')
            ->assertOk()
            ->assertJsonPath('data.0.available', true);

        $this->actingAs($logisticsUser)
            ->patchJson("/api/v1/logistics/riders/{$rider->id}/branch", [
                'logistics_branch_id' => $branch->id,
            ])
            ->assertOk();

        $this->actingAs($logisticsUser)
            ->getJson('/api/v1/logistics/available-deliveries')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $order->id)
            ->assertJsonMissingPath('data.0.delivery_address');

        $this->actingAs($logisticsUser)
            ->postJson("/api/v1/logistics/deliveries/{$order->id}/assign", [
                'rider_id' => $rider->id,
            ])
            ->assertOk()
            ->assertJsonPath('data.shipment.rider_id', $rider->id);

        $this->assertDatabaseHas('shipments', [
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'courier' => 'North Logistics',
        ]);

        $this->actingAs($logisticsUser)
            ->getJson('/api/v1/logistics/deliveries')
            ->assertOk()
            ->assertJsonPath('data.0.id', $order->id);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin)
            ->getJson('/api/v1/admin/logistics-management')
            ->assertOk()
            ->assertJsonPath('companies.0.active_deliveries', 1)
            ->assertJsonPath('companies.0.branches_data.0.active_deliveries', 1);

        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/deliveries')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $order->id)
            ->assertJsonPath('data.0.delivery_address', $order->delivery_address)
            ->assertJsonPath('data.0.buyer.contact_no', $buyer->buyerProfile->contact_no)
            ->assertJsonPath('data.0.seller.contact_no', $seller->contact_no)
            ->assertJsonPath('data.0.seller.address', '12, Main Street, Shop Building, Real, Calamba, Laguna');

        $this->actingAs($riderUser)
            ->post("/api/v1/rider/deliveries/{$order->id}/proof", [
                'status' => 'in_transit',
                'scanned_code' => $order->order_number,
                'proof_photo' => UploadedFile::fake()->image('pickup.jpg'),
            ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('status', 'in_transit')
            ->assertJsonPath('proofs.0.status', 'in_transit');
        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/orders/{$order->id}/status", ['status' => 'out_for_delivery'])
            ->assertStatus(422);
        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/shipping/{$order->id}/status", ['status' => 'out_for_delivery'])
            ->assertStatus(422);
        $this->actingAs($riderUser)
            ->patchJson("/api/v1/rider/deliveries/{$order->id}/status", ['status' => 'out_for_delivery'])
            ->assertOk()
            ->assertJsonPath('status', 'out_for_delivery');
        $this->actingAs($riderUser)
            ->patchJson("/api/v1/rider/deliveries/{$order->id}/status", ['status' => 'delivered'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');
        $this->actingAs($riderUser)
            ->post("/api/v1/rider/deliveries/{$order->id}/proof", [
                'status' => 'delivered',
                'scanned_code' => $order->order_number,
                'proof_photo' => UploadedFile::fake()->image('delivery.jpg'),
                'delivery_method' => 'handed_to_buyer',
            ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('status', 'delivered');

        $this->assertNotNull(Shipment::where('order_id', $order->id)->firstOrFail()->delivered_at);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'changed_by' => $riderUser->id,
            'from_status' => 'out_for_delivery',
            'to_status' => 'delivered',
        ]);
        $this->assertDatabaseHas('buyer_notifications', [
            'buyer_id' => $buyer->id,
            'order_id' => $order->id,
            'type' => 'delivered',
        ]);

        $this->actingAs($buyer)
            ->getJson('/api/v1/buyer/orders')
            ->assertOk()
            ->assertJsonPath('data.0.status', 'delivered')
            ->assertJsonPath('data.0.shipment.tracking_number', 'SE-TRACK123');
    }

    public function test_logistics_and_riders_cannot_access_deliveries_owned_by_another_company(): void
    {
        [$ownerUser, , $ownerBranch] = $this->logistics('Owner Logistics');
        [$otherLogisticsUser] = $this->logistics('Other Logistics');
        [$ownerRiderUser, $ownerRider] = $this->rider($ownerBranch);
        [, $otherRider] = $this->rider();
        [$sellerUser, $seller] = $this->seller('Seller Two');
        $buyer = $this->buyer();
        $order = $this->order($seller, $buyer, 'to_ship', $ownerRider);

        $this->actingAs($ownerUser)
            ->get(route('logistics.deliveries'))
            ->assertOk()
            ->assertSee('available-deliveries');

        $this->actingAs($ownerRiderUser)
            ->get(route('rider.dashboard'))
            ->assertOk()
            ->assertSee('/api/v1/rider/deliveries');

        $this->actingAs($otherLogisticsUser)
            ->getJson('/api/v1/logistics/deliveries')
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->actingAs($otherLogisticsUser)
            ->postJson("/api/v1/logistics/deliveries/{$order->id}/assign", ['rider_id' => $otherRider->id])
            ->assertNotFound();

        $this->actingAs($ownerRiderUser)
            ->getJson("/api/v1/rider/deliveries/{$order->id}")
            ->assertOk();
        $this->actingAs($otherLogisticsUser)
            ->getJson("/api/v1/rider/deliveries/{$order->id}")
            ->assertForbidden();

        $this->actingAs($sellerUser)
            ->getJson('/api/v1/logistics/available-deliveries')
            ->assertForbidden();
    }

    public function test_rider_must_submit_a_matching_scan_and_photo_to_complete_pickup_and_delivery(): void
    {
        Storage::fake('local');
        [$logisticsUser, , $branch] = $this->logistics('Proof Logistics');
        [$riderUser, $rider] = $this->rider($branch);
        [, $seller] = $this->seller('Proof Seller');
        $buyer = $this->buyer();
        $order = $this->order($seller, $buyer, 'to_ship', $rider);

        $this->actingAs($riderUser)
            ->post("/api/v1/rider/deliveries/{$order->id}/proof", [
                'status' => 'in_transit',
                'scanned_code' => 'WRONG-CODE',
                'proof_photo' => UploadedFile::fake()->image('pickup.jpg'),
            ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('scanned_code');

        $this->assertDatabaseCount('delivery_proofs', 0);
        $this->actingAs($riderUser)
            ->patchJson("/api/v1/rider/deliveries/{$order->id}/status", ['status' => 'in_transit'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->actingAs($riderUser)
            ->post("/api/v1/rider/deliveries/{$order->id}/proof", [
                'status' => 'in_transit',
                'scanned_code' => $order->order_number,
                'proof_photo' => UploadedFile::fake()->image('pickup.jpg'),
            ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('status', 'in_transit')
            ->assertJsonPath('proofs.0.status', 'in_transit');

        $pickupProof = DeliveryProof::query()->firstOrFail();
        Storage::disk('local')->assertExists($pickupProof->photo_path);
        $this->actingAs($riderUser)
            ->get("/api/v1/rider/deliveries/{$order->id}/proofs/{$pickupProof->id}/photo")
            ->assertOk();

        [$otherRiderUser, $otherRider] = $this->rider();
        $order->shipment()->update(['rider_id' => $otherRider->id]);
        $this->actingAs($otherRiderUser)
            ->get("/api/v1/rider/deliveries/{$order->id}/proofs/{$pickupProof->id}/photo")
            ->assertNotFound();
        $order->shipment()->update(['rider_id' => $rider->id]);

        $this->actingAs($riderUser)
            ->post("/api/v1/rider/deliveries/{$order->id}/proof", [
                'status' => 'delivered',
                'scanned_code' => 'https://shopease.test/api/v1/orders/verify/'.$order->order_number,
                'proof_photo' => UploadedFile::fake()->image('delivery.jpg'),
                'delivery_method' => 'left_at_door',
            ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->actingAs($riderUser)
            ->patchJson("/api/v1/rider/deliveries/{$order->id}/status", ['status' => 'out_for_delivery'])
            ->assertOk()
            ->assertJsonPath('status', 'out_for_delivery');
        $this->actingAs($riderUser)
            ->post("/api/v1/rider/deliveries/{$order->id}/proof", [
                'status' => 'delivered',
                'scanned_code' => 'https://shopease.test/api/v1/orders/verify/'.$order->order_number,
                'proof_photo' => UploadedFile::fake()->image('delivery.jpg'),
                'delivery_method' => 'left_at_door',
            ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('status', 'delivered')
            ->assertJsonPath('proofs.0.delivery_method', 'left_at_door');

        $this->assertDatabaseCount('delivery_proofs', 2);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'changed_by' => $riderUser->id,
            'from_status' => 'out_for_delivery',
            'to_status' => 'delivered',
        ]);
    }

    public function test_rider_operations_are_persisted_and_shared_with_logistics(): void
    {
        Storage::fake('local');
        Carbon::setTestNow(Carbon::parse('2026-10-24 08:20:00', 'Asia/Manila'));
        [$logisticsUser, , $branch] = $this->logistics('Operations Logistics');
        $branch->update([
            'contact_person' => 'Central Dispatch',
            'phone' => '09170001111',
        ]);
        [$riderUser, $rider] = $this->rider($branch);
        [$sellerUser, $seller] = $this->seller('Operations Seller');
        $buyer = $this->buyer();
        $order = $this->order($seller, $buyer, 'out_for_delivery', $rider);
        $riderUser->notify(new \App\Notifications\RiderEventNotification(
            'Delivery task assigned',
            "Order {$order->order_number} has been assigned to you.",
            'delivery_task',
            $order->id,
        ));

        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/profile')
            ->assertOk()
            ->assertJsonPath('data.name', $riderUser->name)
            ->assertJsonPath('data.branch_contact_person', 'Central Dispatch')
            ->assertJsonPath('data.branch_phone', '09170001111');
        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/content/star-courier-guidelines')
            ->assertOk()
            ->assertJsonPath('data.scoring_configured', false)
            ->assertJsonMissingPath('data.levels');
        $this->actingAs($riderUser)
            ->patchJson('/api/v1/rider/profile', [
                'name' => $riderUser->name,
                'phone' => '09175556666',
            ])
            ->assertOk()
            ->assertJsonPath('data.phone', '09175556666');

        $this->actingAs($riderUser)
            ->post('/api/v1/rider/attendance/clock-in', [
                'photo' => UploadedFile::fake()->image('clock-in.jpg'),
            ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.status', 'late');
        $this->actingAs($riderUser)
            ->post('/api/v1/rider/attendance/clock-out', [
                'photo' => UploadedFile::fake()->image('clock-out.jpg'),
            ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.clock_out_at', fn ($value) => is_string($value));

        $this->actingAs($riderUser)
            ->postJson('/api/v1/rider/recipients', [
                'name' => 'Saved Buyer',
                'phone' => '09170000000',
                'address' => 'Calamba, Laguna',
                'note' => 'Call first',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Saved Buyer');
        $recipientId = \App\Models\Rider\Recipient::query()->firstOrFail()->id;
        $this->actingAs($riderUser)
            ->patchJson("/api/v1/rider/recipients/{$recipientId}", [
                'name' => 'Updated Buyer',
                'phone' => '09170000000',
                'address' => 'Calamba, Laguna',
                'note' => 'Leave with guard',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Buyer');
        $this->actingAs($riderUser)->deleteJson("/api/v1/rider/recipients/{$recipientId}")->assertOk();

        $this->actingAs($riderUser)
            ->postJson('/api/v1/rider/support-tickets', [
                'order_id' => $order->id,
                'subject' => 'Cannot reach buyer',
                'description' => 'The number is unavailable.',
                'priority' => 'high',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'open');
        $ticketId = \App\Models\Rider\SupportTicket::query()->firstOrFail()->id;
        $this->actingAs($logisticsUser)
            ->getJson('/api/v1/logistics/rider-support-tickets')
            ->assertOk()
            ->assertJsonPath('data.0.id', $ticketId)
            ->assertJsonPath('data.0.messages.0.body', 'The number is unavailable.');
        $this->actingAs($logisticsUser)
            ->postJson("/api/v1/logistics/rider-support-tickets/{$ticketId}/messages", [
                'body' => 'We confirmed the buyer contact number.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.body', 'We confirmed the buyer contact number.');
        $this->actingAs($riderUser)
            ->getJson("/api/v1/rider/support-tickets/{$ticketId}/messages")
            ->assertOk()
            ->assertJsonPath('data.1.body', 'We confirmed the buyer contact number.')
            ->assertJsonPath('data.1.is_rider', false);
        $this->actingAs($logisticsUser)
            ->patchJson("/api/v1/logistics/rider-support-tickets/{$ticketId}/review", [
                'status' => 'resolved',
                'resolution' => 'Contact details confirmed with the buyer.',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'resolved');
        $this->actingAs($riderUser)
            ->patchJson("/api/v1/rider/support-tickets/{$ticketId}/archive")
            ->assertOk();
        $this->actingAs($riderUser)
            ->postJson("/api/v1/rider/deliveries/{$order->id}/actions", [
                'action' => 'reschedule',
                'reason' => 'Buyer requested another delivery time.',
                'reschedule_date' => '2026-10-25',
                'reschedule_slot' => 'morning',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending');
        $actionId = \App\Models\Rider\DeliveryAction::query()->firstOrFail()->id;
        $this->actingAs($logisticsUser)
            ->getJson('/api/v1/logistics/rider-delivery-actions?status=pending')
            ->assertOk()
            ->assertJsonPath('data.0.id', $actionId);
        $this->actingAs($logisticsUser)
            ->patchJson("/api/v1/logistics/rider-delivery-actions/{$actionId}/review", [
                'status' => 'approved',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'approved');

        $this->actingAs($riderUser)
            ->post('/api/v1/rider/deliveries/'.$order->id.'/proof', [
                'status' => 'delivered',
                'scanned_code' => $order->order_number,
                'proof_photo' => UploadedFile::fake()->image('delivery.jpg'),
                'delivery_method' => 'handed_to_buyer',
            ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('status', 'delivered');
        $this->assertDatabaseHas('rider_cod_collections', [
            'rider_id' => $rider->id,
            'order_id' => $order->id,
            'amount' => 559,
        ]);

        $this->actingAs($riderUser)
            ->postJson('/api/v1/rider/remittances', [
                'amount' => '559.00',
                'method' => 'hub_turn_in',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending');
        $remittance = \App\Models\Rider\Remittance::query()->firstOrFail();
        $this->actingAs($logisticsUser)
            ->getJson('/api/v1/logistics/rider-remittances?status=pending')
            ->assertOk()
            ->assertJsonPath('data.0.id', $remittance->id);
        $this->actingAs($logisticsUser)
            ->patchJson("/api/v1/logistics/rider-remittances/{$remittance->id}/review", [
                'status' => 'verified',
                'review_notes' => 'Cash received at hub.',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'verified');

        $this->actingAs($logisticsUser)
            ->postJson('/api/v1/logistics/rider-earnings', [
                'rider_id' => $rider->id,
                'order_id' => $order->id,
                'type' => 'base_pay',
                'description' => 'Verified delivery payout',
                'amount' => '115.00',
            ])
            ->assertCreated();
        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/earnings')
            ->assertOk()
            ->assertJsonPath('data.data.0.amount', '115.00');
        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/star-courier?period=month&year=2026&month=10')
            ->assertOk()
            ->assertJsonPath('data.completed_deliveries', 1)
            ->assertJsonPath('data.posted_earnings', '115.00');

        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/notifications')
            ->assertOk()
            ->assertJsonPath('unread_count', 3)
            ->assertJsonCount(3, 'data');
        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/finance')
            ->assertOk()
            ->assertJsonPath('data.cod_remitted', 559);
        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/attendance')
            ->assertOk()
            ->assertJsonPath('today.status', 'late');

        Carbon::setTestNow();
    }

    public function test_rider_page_data_uses_persistent_and_grouped_endpoints(): void
    {
        Storage::fake('local');
        [$riderUser, $rider] = $this->rider();
        [$sellerUser, $seller] = $this->seller('Rider Chat Seller');
        $buyer = $this->buyer();
        $order = $this->order($seller, $buyer, 'to_ship', $rider);

        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/content/knowledge-base')
            ->assertOk()
            ->assertJsonPath('data.title', 'Knowledge Base')
            ->assertJsonPath('data.categories.0.category', 'Delivery Protocols');

        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/content/navigation')
            ->assertOk()
            ->assertJsonPath('data.default_provider', 'google_maps');

        $this->actingAs($riderUser)
            ->postJson('/api/v1/rider/calculations', [
                'expression' => '1000 - 385',
                'result' => '₱615.00',
                'category' => 'COD Change',
            ])
            ->assertCreated()
            ->assertJsonPath('data.expression', '1000 - 385');

        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/calculations')
            ->assertOk()
            ->assertJsonCount(1, 'data');
        $this->actingAs($riderUser)
            ->deleteJson('/api/v1/rider/calculations')
            ->assertOk()
            ->assertJsonPath('data.cleared', true);
        $this->assertDatabaseCount('rider_calculations', 0);

        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/verify-orders')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $order->id)
            ->assertJsonPath('data.0.status', 'to_ship');

        $this->actingAs($riderUser)
            ->patchJson("/api/v1/rider/verify-orders/{$order->id}/archive")
            ->assertOk()
            ->assertJsonPath('data.archived', true);
        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/verify-orders?archived=true')
            ->assertOk()
            ->assertJsonCount(1, 'data');
        $this->actingAs($riderUser)
            ->deleteJson("/api/v1/rider/verify-orders/{$order->id}/archive")
            ->assertOk()
            ->assertJsonPath('data.archived', false);

        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/pickup-tasks')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $order->id);
        $this->actingAs($riderUser)
            ->post("/api/v1/rider/pickup-tasks/{$order->id}/proof", [
                'status' => 'in_transit',
                'scanned_code' => $order->order_number,
                'proof_photo' => UploadedFile::fake()->image('pickup-task.jpg'),
            ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('status', 'in_transit');
        $this->actingAs($riderUser)
            ->getJson('/api/v1/rider/pickup-delivery-records?type=pickup')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->actingAs($riderUser)
            ->postJson("/api/v1/rider/deliveries/{$order->id}/messages", [
                'body' => 'I am on my way to pick up the parcel.',
                'is_pickup' => true,
            ])
            ->assertCreated()
            ->assertJsonPath('data.body', 'I am on my way to pick up the parcel.');
        $this->assertDatabaseHas('rider_order_messages', [
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'recipient_id' => $sellerUser->id,
        ]);
        $this->actingAs($riderUser)
            ->getJson("/api/v1/rider/deliveries/{$order->id}/messages")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.is_rider', true);
    }

    public function test_buyer_and_seller_can_reply_to_assigned_rider_messages(): void
    {
        [$riderUser, $rider] = $this->rider();
        [$sellerUser, $seller] = $this->seller('Rider Message Seller');
        $buyer = $this->buyer();
        $order = $this->order($seller, $buyer, 'to_ship', $rider);

        $this->actingAs($riderUser)
            ->postJson("/api/v1/rider/deliveries/{$order->id}/messages", [
                'body' => 'I am on the way to collect your parcel.',
                'is_pickup' => true,
            ])
            ->assertCreated();

        $this->actingAs($sellerUser)
            ->getJson("/api/v1/seller/orders/{$order->id}/rider-messages")
            ->assertOk()
            ->assertJsonPath('data.0.body', 'I am on the way to collect your parcel.')
            ->assertJsonPath('data.0.is_sender', false);

        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/orders/{$order->id}/rider-messages", [
                'body' => 'The parcel is ready for pickup.',
            ])
            ->assertCreated();

        $this->actingAs($sellerUser)
            ->getJson("/api/v1/seller/orders/{$order->id}/rider-messages")
            ->assertOk()
            ->assertJsonFragment([
                'body' => 'The parcel is ready for pickup.',
                'is_sender' => true,
            ]);

        $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/orders/{$order->id}/rider-messages", [
                'body' => 'I will be available to receive my parcel.',
            ])
            ->assertCreated();
        $this->actingAs($buyer)
            ->getJson("/api/v1/buyer/orders/{$order->id}/rider-messages")
            ->assertOk()
            ->assertJsonPath(
                'data.0.body',
                'I will be available to receive my parcel.'
            )
            ->assertJsonPath('data.0.is_sender', true);

        $this->actingAs($riderUser)
            ->getJson("/api/v1/rider/deliveries/{$order->id}/messages")
            ->assertOk()
            ->assertJsonFragment([
                'body' => 'The parcel is ready for pickup.',
                'is_rider' => false,
            ])
            ->assertJsonFragment([
                'body' => 'I will be available to receive my parcel.',
                'is_rider' => false,
            ]);

        $otherBuyer = $this->buyer();
        $this->actingAs($otherBuyer)
            ->getJson("/api/v1/buyer/orders/{$order->id}/rider-messages")
            ->assertNotFound();
    }

    private function logistics(string $businessName): array
    {
        $user = User::factory()->create([
            'role' => User::ROLE_LOGISTICS,
            'registration_status' => 'active',
        ]);
        $logistics = Logistics::create([
            'user_id' => $user->id,
            'last_name' => 'Contact',
            'first_name' => 'Carrier',
            'sex' => 'other',
            'contact_no' => '09171234567',
            'birthday' => '1990-01-01',
            'age' => 36,
            'province' => 'Laguna',
            'municipality' => 'Calamba',
            'barangay' => 'Real',
            'street' => 'Main Street',
            'house_number' => '1',
            'business_name' => $businessName,
            'registration_status' => 'active',
        ]);
        $branch = LogisticsBranch::create([
            'logistics_id' => $logistics->id,
            'name' => 'Central Hub',
            'address' => 'Calamba, Laguna',
        ]);

        return [$user, $logistics, $branch];
    }

    private function rider(?LogisticsBranch $branch = null): array
    {
        $user = User::factory()->create([
            'role' => User::ROLE_RIDER,
            'registration_status' => 'active',
        ]);
        $rider = Rider::create([
            'user_id' => $user->id,
            'logistics_branch_id' => $branch?->id,
            'last_name' => 'Rider',
            'first_name' => 'Alex',
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

        return [$user, $rider];
    }

    private function seller(string $name): array
    {
        $user = User::factory()->create([
            'role' => User::ROLE_SELLER,
            'registration_status' => 'active',
        ]);
        $seller = Seller::create([
            'user_id' => $user->id,
            'store_name' => $name,
            'contact_no' => '09171111111',
            'house_number' => '12',
            'street' => 'Main Street',
            'building' => 'Shop Building',
            'barangay' => 'Real',
            'municipality' => 'Calamba',
            'province' => 'Laguna',
            'registration_status' => 'active',
        ]);

        return [$user, $seller];
    }

    private function buyer(): User
    {
        $user = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'registration_status' => 'active',
        ]);
        Buyer::create([
            'user_id' => $user->id,
            'last_name' => 'Buyer',
            'first_name' => 'Test',
            'sex' => 'other',
            'contact_no' => '09179999999',
            'birthday' => '1995-01-01',
            'age' => 31,
            'province' => 'Laguna',
            'municipality' => 'Calamba',
            'barangay' => 'Real',
            'street' => 'Buyer Street',
            'house_number' => '2',
            'registration_status' => 'active',
        ]);

        return $user;
    }

    private function order(Seller $seller, User $buyer, string $status, ?Rider $rider = null): Order
    {
        $order = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_number' => 'SE-TEST-'.uniqid(),
            'total' => 559,
            'status' => $status,
            'delivery_name' => 'Test Buyer',
            'delivery_phone' => '09179999999',
            'delivery_address' => '2 Buyer Street, Calamba, Laguna',
            'payment_method' => 'Cash on Delivery',
        ]);
        Shipment::create([
            'order_id' => $order->id,
            'rider_id' => $rider?->id,
            'tracking_number' => 'SE-TRACK123',
            'courier' => 'Ease Express',
            'estimated_delivery' => now()->addDays(2)->toDateString(),
        ]);

        return $order;
    }
}
