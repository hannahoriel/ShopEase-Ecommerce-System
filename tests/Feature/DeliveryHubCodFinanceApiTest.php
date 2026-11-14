<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\CodCollection;
use App\Models\Rider\Remittance;
use App\Models\Rider\Rider;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeliveryHubCodFinanceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_hub_reviews_cashins_and_distributes_cod_remittance_once(): void
    {
        $hubUser = $this->user(User::ROLE_DELIVERY_HUB);
        $riderUser = $this->user(User::ROLE_RIDER);
        $buyer = $this->user(User::ROLE_BUYER);
        $sellerUser = $this->user(User::ROLE_SELLER);
        $logisticsUser = $this->user(User::ROLE_LOGISTICS);
        $logistics = Logistics::create([
            'user_id' => $logisticsUser->id, 'last_name' => 'Carrier', 'first_name' => 'Test', 'sex' => 'other',
            'contact_no' => '09170000001', 'birthday' => '1990-01-01', 'age' => 36, 'province' => 'Laguna',
            'municipality' => 'Calamba', 'barangay' => 'Real', 'street' => 'Main', 'house_number' => '1',
            'business_name' => 'Test Logistics', 'registration_status' => 'active',
        ]);
        $branch = LogisticsBranch::create(['logistics_id' => $logistics->id, 'name' => 'Calamba', 'address' => 'Calamba']);
        $hub = LogisticsDeliveryHub::create([
            'logistics_branch_id' => $branch->id, 'user_id' => $hubUser->id, 'hub_name' => 'Calamba Hub',
            'province' => 'Laguna', 'municipality' => 'Calamba', 'barangay' => 'Real', 'postal_code' => '4027',
            'street' => 'Main', 'manager_name' => 'Hub Manager', 'manager_email' => 'hub@example.test',
            'manager_phone' => '09170000002', 'login_email' => $hubUser->email, 'status' => 'active',
        ]);
        $rider = Rider::create([
            'user_id' => $riderUser->id, 'logistics_branch_id' => $branch->id, 'last_name' => 'Rider',
            'first_name' => 'Test', 'sex' => 'other', 'contact_no' => '09170000003', 'birthday' => '1990-01-01',
            'age' => 36, 'province' => 'Laguna', 'municipality' => 'Calamba', 'barangay' => 'Real',
            'street' => 'Main', 'house_number' => '1', 'vehicle' => 'Motorcycle', 'plate_number' => 'ABC123',
            'municipal_hub' => 'Calamba Hub', 'registration_status' => 'active',
        ]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id, 'store_name' => 'Test Shop', 'contact_no' => '09170000004',
            'house_number' => '1', 'street' => 'Main', 'barangay' => 'Real', 'municipality' => 'Calamba',
            'province' => 'Laguna', 'registration_status' => 'active',
        ]);
        $order = Order::create([
            'buyer_id' => $buyer->id, 'seller_id' => $seller->id,
            'order_number' => 'SE-COD-FINANCE-1', 'total' => 559, 'shipping_fee' => 58,
            'status' => 'delivered', 'payment_method' => 'Cash on Delivery',
        ]);
        CodCollection::create(['rider_id' => $rider->id, 'order_id' => $order->id, 'amount' => 559, 'collected_at' => now()]);
        Remittance::create(['rider_id' => $rider->id, 'amount' => 559, 'method' => 'hub_turn_in', 'status' => 'pending']);
        $this->actingAs($riderUser)->postJson('/api/v1/rider/remittances', [
            'amount' => '559.00', 'method' => 'hub_turn_in',
        ])->assertOk()->assertJsonPath('message', 'The existing pending remittance was matched to these COD collections and sent to the hub for review.');
        $rider->remittances()->update(['status' => 'rejected']);
        $remittanceResponse = $this->actingAs($riderUser)->postJson('/api/v1/rider/remittances', [
            'amount' => '559.00', 'method' => 'hub_turn_in',
        ])->assertCreated()->assertJsonPath('data.delivery_hub_id', $hub->id);
        $remittance = Remittance::findOrFail($remittanceResponse->json('data.id'));
        $this->assertDatabaseHas('rider_cod_remittance_items', [
            'remittance_id' => $remittance->id, 'cod_collection_id' => CodCollection::first()->id,
        ]);
        $this->actingAs($riderUser)->postJson('/api/v1/rider/remittances', [
            'amount' => '559.00', 'method' => 'hub_turn_in',
        ])->assertStatus(422);

        $this->actingAs($hubUser)->getJson('/api/v1/delivery_hub/finance')
            ->assertOk()->assertJsonPath('data.remittances.0.id', $remittance->id)
            ->assertJsonPath('data.wallet_pin_configured', false);
        $this->actingAs($hubUser)->patchJson("/api/v1/delivery_hub/rider-remittances/{$remittance->id}/review", [
            'status' => 'verified', 'actual_amount' => '558.00',
        ])->assertOk()->assertJsonPath('data.status', 'discrepancy');
        $this->actingAs($hubUser)->patchJson("/api/v1/delivery_hub/rider-remittances/{$remittance->id}/review", [
            'status' => 'verified', 'actual_amount' => '559.00',
        ])->assertOk()->assertJsonPath('data.status', 'verified');
        $pinSetup = $this->actingAs($hubUser)->postJson('/api/v1/delivery_hub/finance/wallet-pin/setup', ['pin' => '246810'])
            ->assertOk()->assertJsonPath('data.configured', true);
        $walletPinToken = $pinSetup->json('data.verification_token');
        $pinRecord = \Illuminate\Support\Facades\DB::table('hub_wallet_pins')->where('delivery_hub_id', $hub->id)->first();
        $this->assertNotSame('246810', $pinRecord->pin_hash);
        $this->assertTrue(Hash::check('246810', $pinRecord->pin_hash));
        $this->assertSame(64, strlen($walletPinToken));
        $this->actingAs($hubUser)->postJson('/api/v1/delivery_hub/finance/wallet-pin/setup', ['pin' => '111111'])
            ->assertStatus(409);
        $this->actingAs($hubUser)->postJson('/api/v1/delivery_hub/finance/wallet-pin/verify', ['pin' => '000000'])
            ->assertUnprocessable();
        $verifiedPin = $this->actingAs($hubUser)->postJson('/api/v1/delivery_hub/finance/wallet-pin/verify', ['pin' => '246810'])
            ->assertOk()->assertJsonPath('data.configured', true);
        $walletPinToken = $verifiedPin->json('data.verification_token');
        $this->actingAs($hubUser)->getJson('/api/v1/delivery_hub/finance')
            ->assertOk()->assertJsonPath('data.wallet_pin_configured', true);
        $this->actingAs($hubUser)->postJson("/api/v1/delivery_hub/rider-remittances/{$remittance->id}/cash-in")
            ->assertForbidden();
        $this->actingAs($hubUser)->postJson("/api/v1/delivery_hub/rider-remittances/{$remittance->id}/cash-in", [
            'wallet_pin_token' => $walletPinToken,
        ])->assertOk()->assertJsonPath('data.amount', 559);
        $this->assertDatabaseHas('cod_wallet_accounts', ['owner_key' => 'delivery_hub:'.$hub->id.':available', 'balance' => '559.00']);
        $this->actingAs($hubUser)->postJson("/api/v1/delivery_hub/rider-remittances/{$remittance->id}/cash-in")
            ->assertStatus(409);

        $this->actingAs($hubUser)->postJson("/api/v1/delivery_hub/rider-remittances/{$remittance->id}/distribute")
            ->assertOk()->assertJsonPath('data.orders', 1)->assertJsonPath('data.amount', 559);
        $this->assertDatabaseHas('cod_settlements', [
            'order_id' => $order->id, 'delivery_hub_id' => $hub->id, 'status' => 'held',
            'logistics_id' => $logistics->id,
            'shipping_fee' => '50.00', 'platform_shipping_margin' => '8.00',
            'commission_amount' => '50.10', 'seller_amount' => '450.90',
        ]);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'logistics_id' => $logistics->id]);
        $this->assertDatabaseHas('cod_wallet_accounts', ['owner_key' => 'logistics:'.$logistics->id.':available', 'balance' => '50.00']);
        $this->assertDatabaseHas('cod_wallet_accounts', ['owner_key' => 'platform:platform:available', 'balance' => '58.10']);
        $this->actingAs($hubUser)->postJson("/api/v1/delivery_hub/rider-remittances/{$remittance->id}/distribute")
            ->assertStatus(409);
        $this->assertDatabaseCount('cod_settlements', 1);
        $rejectable = Remittance::create([
            'rider_id' => $rider->id, 'delivery_hub_id' => $hub->id, 'amount' => 25,
            'method' => 'hub_turn_in', 'status' => 'pending',
        ]);
        $this->actingAs($hubUser)->patchJson("/api/v1/delivery_hub/rider-remittances/{$rejectable->id}/review", [
            'status' => 'rejected', 'rejection_reason' => 'other',
        ])->assertUnprocessable();
        $this->actingAs($hubUser)->patchJson("/api/v1/delivery_hub/rider-remittances/{$rejectable->id}/review", [
            'status' => 'rejected', 'rejection_reason' => 'other', 'review_notes' => 'Receipt details are unreadable.',
        ])->assertOk()->assertJsonPath('data.review_notes', 'Rejection reason: Other — Receipt details are unreadable.');
        $this->actingAs($riderUser)->getJson('/api/v1/rider/finance')
            ->assertOk()->assertJsonPath('data.cod_remitted', 559)->assertJsonPath('data.cod_pending_remittance', 0);

        OrderStatusHistory::create(['order_id' => $order->id, 'from_status' => 'out_for_delivery', 'to_status' => 'delivered']);
        $this->actingAs($buyer)->patchJson("/api/v1/buyer/orders/{$order->id}/confirm-received")
            ->assertOk()->assertJsonPath('order.status', 'completed');
        $this->assertDatabaseHas('cod_settlements', ['order_id' => $order->id, 'status' => 'released']);
        $this->assertDatabaseHas('cod_wallet_accounts', ['owner_key' => 'seller:'.$seller->id.':on_hold', 'balance' => '0.00']);
        $this->assertDatabaseHas('cod_wallet_accounts', ['owner_key' => 'seller:'.$seller->id.':available', 'balance' => '450.90']);
    }

    private function user(string $role): User
    {
        return User::create([
            'name' => ucfirst($role).' User', 'email' => $role.'-'.uniqid().'@example.test',
            'password' => 'password', 'role' => $role, 'registration_status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
