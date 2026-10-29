<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\OrderReturn;
use App\Models\Admin\OrderReturnEvent;
use App\Models\Admin\Shipment;
use App\Models\Admin\ShipmentScan;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Seller\Seller;
use App\Models\User;
use Database\Seeders\LogisticsParcelTrackingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogisticsParcelTrackingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_logistics_tracking_api_returns_parcels_returns_histories_and_network_locations(): void
    {
        [$user, $branch, $hub, $order, $shipment] = $this->trackingFixture();
        ShipmentScan::query()->create([
            'shipment_id' => $shipment->id,
            'status' => 'in_transit',
            'location' => 'Laguna Sorting Center',
            'scanned_at' => now()->subHour(),
        ]);
        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_name' => 'Wireless Headphones',
            'quantity' => 1,
            'unit_price' => 1599,
            'variation' => 'Bluetooth 5.3',
            'color' => 'Black',
        ]);
        $return = OrderReturn::query()->create([
            'order_id' => $order->id,
            'return_type' => 'return',
            'status' => 'pending',
            'shipping_status' => 'return-requested',
            'tracking_number' => 'RET-TRACK-1001',
            'reason' => 'Incorrect size',
            'solution' => 'Awaiting pickup',
            'return_items' => [['product_name' => 'Wireless Headphones', 'quantity' => 1]],
            'requested_at' => now()->subDay(),
        ]);
        OrderReturnEvent::query()->create([
            'order_return_id' => $return->id,
            'status' => 'return-requested',
            'location' => 'Santa Cruz Delivery Hub',
            'scanned_at' => now()->subDay(),
        ]);

        $this->actingAs($user)
            ->getJson('/api/v1/logistics/parcel-tracking')
            ->assertOk()
            ->assertJsonCount(1, 'parcels')
            ->assertJsonPath('parcels.0.order_number', 'SE-TRACK-1001')
            ->assertJsonPath('parcels.0.shipping_status', 'in-transit')
            ->assertJsonPath('parcels.0.tracking_history.0.location', 'Laguna Sorting Center')
            ->assertJsonPath('parcels.0.items.0.product_name', 'Wireless Headphones')
            ->assertJsonPath('parcels.0.sorting_center_id', $branch->id)
            ->assertJsonPath('parcels.0.delivery_hub_id', $hub->id)
            ->assertJsonCount(1, 'returns')
            ->assertJsonPath('returns.0.return_id', $return->id)
            ->assertJsonPath('returns.0.reason', 'Incorrect size')
            ->assertJsonPath('returns.0.tracking_history.0.location', 'Santa Cruz Delivery Hub')
            ->assertJsonPath('locations.sorting_centers.0.name', 'Laguna Sorting Center')
            ->assertJsonPath('locations.delivery_hubs.0.name', 'Santa Cruz Delivery Hub');

        $this->actingAs($user)
            ->get('/logistics/parcel-tracking')
            ->assertOk()
            ->assertSee('TRACK-1001')
            ->assertDontSee('DEMO-TRK-001');
    }

    public function test_tracking_endpoint_only_returns_records_owned_by_the_authenticated_logistics_company(): void
    {
        [$user] = $this->trackingFixture('owner');
        $this->trackingFixture('other');

        $this->actingAs($user)
            ->getJson('/api/v1/logistics/parcel-tracking')
            ->assertOk()
            ->assertJsonCount(1, 'parcels')
            ->assertJsonPath('parcels.0.tracking_number', 'TRK-owner');
    }

    public function test_tracking_endpoints_reject_guests_and_non_logistics_accounts(): void
    {
        $this->getJson('/api/v1/logistics/parcel-tracking')->assertUnauthorized();

        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $this->actingAs($buyer)
            ->getJson('/api/v1/logistics/parcel-tracking')
            ->assertForbidden();
    }

    public function test_tracking_demo_seeder_is_repeatable_and_populates_all_ui_sections(): void
    {
        $seeder = new LogisticsParcelTrackingSeeder;
        $seeder->run();
        $seeder->run();

        $user = User::query()->where('email', 'tracking.logistics@shopease.test')->firstOrFail();
        $this->actingAs($user)
            ->getJson('/api/v1/logistics/parcel-tracking')
            ->assertOk()
            ->assertJsonCount(3, 'parcels')
            ->assertJsonCount(2, 'returns')
            ->assertJsonPath('locations.sorting_centers.0.name', 'Laguna Sorting Center')
            ->assertJsonPath('locations.delivery_hubs.0.name', 'Santa Cruz Delivery Hub');

        $this->assertDatabaseCount('shipments', 3);
        $this->assertDatabaseCount('order_returns', 2);
    }

    private function trackingFixture(string $suffix = '1001'): array
    {
        $user = User::factory()->create([
            'role' => User::ROLE_LOGISTICS,
            'registration_status' => 'active',
        ]);
        $logistics = Logistics::query()->create([
            'user_id' => $user->id,
            'last_name' => 'Carrier',
            'first_name' => 'Test',
            'sex' => 'other',
            'contact_no' => '09171234567',
            'birthday' => '1990-01-01',
            'age' => 36,
            'province' => 'Laguna',
            'municipality' => 'Santa Cruz',
            'barangay' => 'Poblacion',
            'street' => 'Main Street',
            'house_number' => '1',
            'business_name' => 'Test Logistics',
            'registration_status' => 'active',
        ]);
        $branch = LogisticsBranch::query()->create([
            'logistics_id' => $logistics->id,
            'name' => 'Laguna Sorting Center',
            'address' => 'Main Street, Santa Cruz, Laguna',
            'province' => 'Laguna',
            'municipality' => 'Santa Cruz',
            'barangay' => 'Poblacion',
            'postal_code' => '4009',
            'street' => 'Main Street',
            'login_email' => "branch-{$suffix}@example.test",
            'status' => 'active',
        ]);
        $hub = LogisticsDeliveryHub::query()->create([
            'logistics_branch_id' => $branch->id,
            'hub_name' => 'Santa Cruz Delivery Hub',
            'province' => 'Laguna',
            'municipality' => 'Santa Cruz',
            'barangay' => 'Poblacion',
            'postal_code' => '4009',
            'street' => 'Hub Road',
            'manager_name' => 'Hub Manager',
            'manager_email' => "manager-{$suffix}@example.test",
            'manager_phone' => '09171234568',
            'login_email' => "hub-{$suffix}@example.test",
            'status' => 'active',
        ]);
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::query()->create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Test Store',
            'registration_status' => 'active',
        ]);
        $order = Order::query()->create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_number' => "SE-TRACK-{$suffix}",
            'total' => 1599,
            'status' => 'in_transit',
            'delivery_name' => 'Test Buyer',
            'delivery_phone' => '09170000000',
            'delivery_address' => 'Santa Cruz, Laguna',
            'payment_method' => 'Cash on Delivery',
        ]);
        $shipment = Shipment::query()->create([
            'order_id' => $order->id,
            'tracking_number' => "TRK-{$suffix}",
            'courier' => 'Test Logistics',
            'estimated_delivery' => now()->addDay()->toDateString(),
            'current_location' => 'Laguna Sorting Center',
            'logistics_branch_id' => $branch->id,
            'logistics_delivery_hub_id' => $hub->id,
        ]);

        return [$user, $branch, $hub, $order, $shipment];
    }
}
