<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Admin\Shipment;
use App\Models\Buyer\Buyer;
use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\LogisticsTransferManifest;
use App\Models\Logistics\SortingCenterVehicle;
use App\Models\Rider\Rider;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParcelRouteFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_real_order_moves_from_seller_hub_through_province_center_to_buyer_hub_and_delivery(): void
    {
        $network = $this->network();
        $lagunaCourierUser = $this->user('rider', 'luzon.route.courier@example.test');
        $cebuCourierUser = $this->user('rider', 'visayas.route.courier@example.test');
        $network['laguna_courier']->update(['user_id' => $lagunaCourierUser->id]);
        $network['cebu_courier']->update(['user_id' => $cebuCourierUser->id]);
        $seller = $this->user('seller', 'seller.flow@example.test');
        $sellerProfile = Seller::query()->create([
            'user_id' => $seller->id, 'first_name' => 'Sam', 'last_name' => 'Seller', 'store_name' => 'Laguna Store',
            'province' => 'Laguna', 'municipality' => 'Calamba', 'barangay' => 'Seller Barangay', 'street' => 'Market Road',
            'contact_no' => '09170000001', 'registration_status' => 'active',
        ]);
        $buyer = $this->user('buyer', 'buyer.flow@example.test');
        Buyer::query()->create([
            'user_id' => $buyer->id, 'first_name' => 'Bea', 'last_name' => 'Buyer', 'sex' => 'other',
            'contact_no' => '09170000002', 'birthday' => '1995-01-01', 'age' => 31,
            'province' => 'Cebu', 'municipality' => 'Cebu City', 'barangay' => 'Buyer Barangay',
            'street' => 'Ocean Road', 'house_number' => '5', 'registration_status' => 'active',
        ]);
        $order = Order::query()->create([
            'buyer_id' => $buyer->id, 'seller_id' => $sellerProfile->id, 'order_number' => 'SE-FLOW-0001',
            'total' => 1200, 'status' => 'to_ship', 'delivery_name' => 'Bea Buyer', 'delivery_phone' => '09170000002',
            'delivery_address' => 'Buyer Barangay, Cebu City, Cebu', 'payment_method' => 'cod',
        ]);
        $shipment = Shipment::query()->create(['order_id' => $order->id, 'tracking_number' => 'SHP-FLOW-0001']);

        $queue = $this->actingAs($network['seller_hub_user'])->getJson('/api/v1/delivery_hub/scanner/queue');
        $this->assertSame(200, $queue->status(), $queue->getContent());
        $queue->assertJsonPath('parcels.0.tracking', 'SHP-FLOW-0001')->assertJsonPath('parcels.0.status', 'incoming');
        $received = $this->postJson('/api/v1/delivery_hub/scanner/parcel-scan', ['tracking' => $shipment->tracking_number, 'action' => 'receive']);
        $this->assertSame(200, $received->status(), $received->getContent());
        $received->assertJsonPath('shipment.logistics_delivery_hub_id', $network['seller_hub']->id);
        $this->assertSame($network['seller_pickup_rider']->id, $shipment->fresh()->rider_id);

        $departedFromSellerHub = $this->postJson('/api/v1/delivery_hub/scanner/parcel-scan', [
            'tracking' => $shipment->tracking_number, 'action' => 'depart', 'batch_key' => 'seller-hub-to-origin-sc',
            'courier_registration_id' => $network['laguna_courier']->id,
            'vehicle_id' => $network['laguna_truck']->id,
            'courier_name' => 'Luzon Courier',
            'vehicle_reference' => 'SC-LAG-001',
            'scheduled_at' => now()->addDay()->toDateTimeString(),
        ]);
        $this->assertSame(200, $departedFromSellerHub->status(), $departedFromSellerHub->getContent());
        $this->assertSame('in_transit', $order->fresh()->status);
        $this->assertSame($network['laguna']->id, $shipment->fresh()->logistics_branch_id);

        $sellerHubManifest = LogisticsTransferManifest::query()
            ->where('batch_key', 'seller-hub-to-origin-sc')
            ->firstOrFail();
        $sellerLegArrival = $this->actingAs($lagunaCourierUser)
            ->postJson("/api/v1/courier/manifests/{$sellerHubManifest->id}/arrival");
        $this->assertSame(200, $sellerLegArrival->status(), $sellerLegArrival->getContent());

        $this->actingAs($network['laguna_user'])->getJson('/api/v1/sorting_center/scanner/queue')
            ->assertOk()->assertJsonPath('parcels.0.tracking', 'SHP-FLOW-0001');
        $this->assertSame('loaded', $sellerHubManifest->status);
        $this->assertSame('loaded', $sellerHubManifest->shipments()->first()->pivot->load_status);
        $receivedAtLaguna = $this->postJson('/api/v1/sorting_center/scanner/parcel-scan', [
            'tracking' => $shipment->tracking_number,
            'action' => 'receive',
        ]);
        $this->assertSame(200, $receivedAtLaguna->status(), $receivedAtLaguna->getContent());
        $this->postJson('/api/v1/sorting_center/scanner/parcel-scan', [
            'tracking' => $shipment->tracking_number, 'action' => 'depart', 'batch_key' => 'laguna-to-cebu',
            'courier_registration_id' => $network['laguna_courier']->id, 'vehicle_id' => $network['laguna_truck']->id,
            'courier_name' => 'Luzon Courier', 'vehicle_reference' => 'SC-LAG-001',
            'scheduled_at' => now()->addDay()->toDateTimeString(),
        ])->assertOk();
        $transfer = LogisticsTransferManifest::query()->where('batch_key', 'laguna-to-cebu')->firstOrFail();
        $this->actingAs($lagunaCourierUser)->postJson("/api/v1/courier/manifests/{$transfer->id}/departure")
            ->assertOk();
        $this->assertSame($network['cebu']->id, $shipment->fresh()->logistics_branch_id);

        $this->postJson("/api/v1/courier/manifests/{$transfer->id}/arrival")->assertOk();
        $this->actingAs($network['cebu_user'])->postJson('/api/v1/sorting_center/scanner/parcel-scan', ['tracking' => $shipment->tracking_number, 'action' => 'receive'])->assertOk();
        $this->assertSame('arrived', $transfer->fresh()->status);

        // Confirm that a truck arriving from Laguna is assigned cargo back to Laguna.
        $returnSellerUser = $this->user('seller', 'cebu.return.seller@example.test');
        $returnSeller = Seller::query()->create([
            'user_id' => $returnSellerUser->id, 'first_name' => 'Cora', 'last_name' => 'Seller', 'store_name' => 'Cebu Store',
            'province' => 'Cebu', 'municipality' => 'Cebu City', 'barangay' => 'Return Barangay', 'street' => 'Harbor Road',
            'contact_no' => '09170000003', 'registration_status' => 'active',
        ]);
        $returnBuyerUser = $this->user('buyer', 'laguna.return.buyer@example.test');
        Buyer::query()->create([
            'user_id' => $returnBuyerUser->id, 'first_name' => 'Lia', 'last_name' => 'Buyer', 'sex' => 'other',
            'contact_no' => '09170000004', 'birthday' => '1995-01-01', 'age' => 31,
            'province' => 'Laguna', 'municipality' => 'Calamba', 'barangay' => 'Return Buyer Barangay',
            'street' => 'Lake Road', 'house_number' => '2', 'registration_status' => 'active',
        ]);
        $returnOrder = Order::query()->create([
            'buyer_id' => $returnBuyerUser->id, 'seller_id' => $returnSeller->id, 'order_number' => 'SE-FLOW-RETURN-0001',
            'total' => 600, 'status' => 'in_transit', 'delivery_name' => 'Lia Buyer', 'delivery_phone' => '09170000004',
            'delivery_address' => 'Return Buyer Barangay, Calamba, Laguna', 'payment_method' => 'cod',
        ]);
        $returnShipment = Shipment::query()->create([
            'order_id' => $returnOrder->id, 'tracking_number' => 'SHP-FLOW-RETURN-0001', 'logistics_branch_id' => $network['cebu']->id,
        ]);
        $returnInbound = LogisticsTransferManifest::query()->create([
            'batch_key' => 'laguna-to-cebu-return-test',
            'origin_type' => 'sorting_center',
            'origin_id' => $network['laguna']->id,
            'destination_type' => 'sorting_center',
            'destination_id' => $network['cebu']->id,
            'courier_registration_id' => $network['laguna_courier']->id,
            'vehicle_id' => $network['laguna_truck']->id,
            'courier_name' => 'Luzon Courier',
            'vehicle_reference' => 'SC-LAG-001',
            'status' => 'preparing',
        ]);
        $returnInbound->shipments()->attach($returnShipment->id, [
            'load_status' => 'loaded',
            'loaded_at' => now(),
            'loaded_by' => $lagunaCourierUser->id,
        ]);
        $returnShipment->scans()->create([
            'status' => 'Received at Sorting Center',
            'location' => 'Laguna Sorting Center · Laguna',
            'scanned_at' => now(),
        ]);
        $returnShipment->update([
            'current_location' => 'Laguna Sorting Center · Laguna',
        ]);
        $this->actingAs($lagunaCourierUser)->postJson("/api/v1/courier/manifests/{$returnInbound->id}/departure")
            ->assertOk();
        $this->postJson("/api/v1/courier/manifests/{$returnInbound->id}/arrival")->assertOk();
        $this->actingAs($network['cebu_user']);
        $this->postJson('/api/v1/sorting_center/scanner/parcel-scan', [
            'tracking' => $returnShipment->tracking_number,
            'action' => 'receive',
        ])->assertOk();
        $this->postJson('/api/v1/sorting_center/scanner/parcel-scan', [
            'tracking' => $returnShipment->tracking_number, 'action' => 'depart', 'batch_key' => 'cebu-return-to-laguna-missing-trip',
            'courier_registration_id' => $network['cebu_courier']->id, 'vehicle_id' => $network['cebu_truck']->id,
        ])->assertUnprocessable();
        $this->postJson('/api/v1/sorting_center/scanner/parcel-scan', [
            'tracking' => $returnShipment->tracking_number, 'action' => 'depart', 'batch_key' => 'cebu-return-to-laguna',
            'returning_manifest_id' => $returnInbound->id,
        ])->assertOk();
        $returnTransfer = LogisticsTransferManifest::query()->where('batch_key', 'cebu-return-to-laguna')->firstOrFail();
        $this->actingAs($lagunaCourierUser)
            ->postJson("/api/v1/courier/manifests/{$returnTransfer->id}/load-scan", [
                'tracking' => $returnShipment->tracking_number,
            ])
            ->assertOk();
        $returnTransferDeparture = $this->actingAs($lagunaCourierUser)
            ->postJson("/api/v1/courier/manifests/{$returnTransfer->id}/departure");
        $this->assertSame(200, $returnTransferDeparture->status(), $returnTransferDeparture->getContent());
        $this->assertSame($returnInbound->id, $returnTransfer->returning_manifest_id);
        $this->assertSame($network['laguna_courier']->id, $returnTransfer->courier_registration_id);
        $this->assertSame($network['laguna_truck']->id, $returnTransfer->vehicle_id);
        $this->assertSame($network['laguna']->id, $returnShipment->fresh()->logistics_branch_id);

        $this->postJson("/api/v1/courier/manifests/{$returnTransfer->id}/arrival")->assertOk();
        $this->actingAs($network['laguna_user'])->postJson('/api/v1/sorting_center/scanner/parcel-scan', [
            'tracking' => $returnShipment->tracking_number,
            'action' => 'receive',
        ])->assertOk();

        $this->actingAs($network['cebu_user']);
        $this->postJson('/api/v1/sorting_center/scanner/parcel-scan', [
            'tracking' => $shipment->tracking_number, 'action' => 'depart', 'batch_key' => 'cebu-to-buyer-hub',
            'courier_registration_id' => $network['cebu_courier']->id, 'vehicle_id' => $network['cebu_truck']->id,
            'courier_name' => 'Visayas Courier', 'vehicle_reference' => 'SC-CEB-001',
            'scheduled_at' => now()->addDay()->toDateTimeString(),
        ])->assertOk();
        $buyerTrip = LogisticsTransferManifest::query()->where('batch_key', 'cebu-to-buyer-hub')->firstOrFail();
        $this->actingAs($cebuCourierUser)->postJson("/api/v1/courier/manifests/{$buyerTrip->id}/departure")
            ->assertOk();
        $this->assertSame($network['buyer_hub']->id, $shipment->fresh()->logistics_delivery_hub_id);

        $this->postJson("/api/v1/courier/manifests/{$buyerTrip->id}/arrival")->assertOk();
        $this->actingAs($network['buyer_hub_user'])->getJson('/api/v1/delivery_hub/scanner/queue')
            ->assertOk()->assertJsonPath('parcels.0.tracking', 'SHP-FLOW-0001');
        $this->postJson('/api/v1/delivery_hub/scanner/parcel-scan', ['tracking' => $shipment->tracking_number, 'action' => 'receive'])->assertOk();
        $this->postJson('/api/v1/delivery_hub/scanner/parcel-scan', ['tracking' => $shipment->tracking_number, 'action' => 'depart'])->assertOk()
            ->assertJsonPath('shipment.order_status', 'out_for_delivery')->assertJsonPath('shipment.rider_id', $network['buyer_delivery_rider']->id);

        $this->assertSame('out_for_delivery', $order->fresh()->status);
        $this->assertSame(8, $shipment->scans()->count());
        $this->assertDatabaseHas('order_status_histories', ['order_id' => $order->id, 'to_status' => 'in_transit']);
        $this->assertDatabaseHas('order_status_histories', ['order_id' => $order->id, 'to_status' => 'out_for_delivery']);
    }

    public function test_courier_loading_departure_and_arrival_require_individual_receiving_scans(): void
    {
        $network = $this->network();
        $courierUser = $this->user('rider', 'luzon.courier@example.test');
        $courier = $network['laguna_courier'];
        $courier->update(['user_id' => $courierUser->id]);

        $sellerUser = $this->user('seller', 'courier.flow.seller@example.test');
        $seller = Seller::query()->create([
            'user_id' => $sellerUser->id, 'first_name' => 'Sam', 'last_name' => 'Seller',
            'store_name' => 'Flow Seller', 'province' => 'Laguna', 'municipality' => 'Calamba',
            'barangay' => 'Poblacion', 'street' => 'Main Road', 'contact_no' => '09170000011',
            'registration_status' => 'active',
        ]);
        $buyerUser = $this->user('buyer', 'courier.flow.buyer@example.test');
        Buyer::query()->create([
            'user_id' => $buyerUser->id, 'first_name' => 'Bea', 'last_name' => 'Buyer',
            'sex' => 'other', 'contact_no' => '09170000012', 'birthday' => '1995-01-01',
            'age' => 31, 'province' => 'Cebu', 'municipality' => 'Cebu City',
            'barangay' => 'Poblacion', 'street' => 'Ocean Road', 'house_number' => '5',
            'registration_status' => 'active',
        ]);

        $returnBuyerUser = $this->user('buyer', 'courier.return.buyer@example.test');
        Buyer::query()->create([
            'user_id' => $returnBuyerUser->id, 'first_name' => 'Lia', 'last_name' => 'Buyer',
            'sex' => 'other', 'contact_no' => '09170000013', 'birthday' => '1995-01-01',
            'age' => 31, 'province' => 'Laguna', 'municipality' => 'Calamba',
            'barangay' => 'Poblacion', 'street' => 'Lake Road', 'house_number' => '2',
            'registration_status' => 'active',
        ]);

        $shipments = collect([
            'SHP-COURIER-001',
            'SHP-COURIER-002',
            'SHP-COURIER-003',
        ])->map(
            function (string $tracking, int $index) use ($seller, $buyerUser, $returnBuyerUser, $network): Shipment {
                $buyer = $index === 2 ? $returnBuyerUser : $buyerUser;
                $order = Order::query()->create([
                    'buyer_id' => $buyer->id, 'seller_id' => $seller->id,
                    'order_number' => 'SE-COURIER-00'.($index + 1), 'total' => 500,
                    'status' => 'in_transit',
                    'delivery_name' => $index === 2 ? 'Lia Buyer' : 'Bea Buyer',
                    'delivery_phone' => $index === 2 ? '09170000013' : '09170000012',
                    'delivery_address' => $index === 2 ? 'Calamba, Laguna' : 'Cebu City, Cebu',
                    'payment_method' => 'cod',
                ]);
                return Shipment::query()->create([
                    'order_id' => $order->id,
                    'tracking_number' => $tracking,
                    'logistics_branch_id' => $network['laguna']->id,
                ]);
            },
        );
        $manifest = LogisticsTransferManifest::query()->create([
            'batch_key' => 'COURIER-OUT-001',
            'origin_type' => 'sorting_center',
            'origin_id' => $network['laguna']->id,
            'destination_type' => 'sorting_center',
            'destination_id' => $network['cebu']->id,
            'courier_registration_id' => $courier->id,
            'vehicle_id' => $network['laguna_truck']->id,
            'courier_name' => 'Luzon Courier',
            'vehicle_reference' => 'SC-LAG-001',
            'status' => 'preparing',
        ]);
        $manifest->shipments()->attach($shipments->mapWithKeys(
            fn (Shipment $shipment): array => [$shipment->id => ['load_status' => 'pending']],
        )->all());
        $shipments->first()->scans()->create([
            'status' => 'Received at Sorting Center',
            'location' => 'Laguna Sorting Center · Laguna',
            'scanned_at' => now(),
        ]);
        $shipments->first()->update([
            'current_location' => 'Laguna Sorting Center · Laguna',
        ]);
        $returnShipment = $shipments[2];
        $returnShipment->scans()->create([
            'status' => 'Received at Sorting Center',
            'location' => 'Laguna Sorting Center · Laguna',
            'scanned_at' => now(),
        ]);
        $returnShipment->update([
            'current_location' => 'Laguna Sorting Center · Laguna',
        ]);

        $this->actingAs($courierUser)->getJson('/api/v1/courier/dashboard')
            ->assertOk()
            ->assertJsonPath('data.summary.in_truck', 0)
            ->assertJsonPath('data.summary.outbound_parcels', 3)
            ->assertJsonPath('data.summary.active_trips', 1);

        $this->postJson("/api/v1/courier/manifests/{$manifest->id}/load-scan", [
            'tracking' => 'SHP-COURIER-001',
        ])->assertOk();
        $this->postJson("/api/v1/courier/manifests/{$manifest->id}/load-scan", [
            'tracking' => 'SHP-COURIER-003',
        ])->assertOk();
        $this->assertSame('loaded', $manifest->shipments()->first()->pivot->load_status);
        $this->assertSame(2, $manifest->shipments()->wherePivot('load_status', 'loaded')->count());
        $this->postJson("/api/v1/courier/manifests/{$manifest->id}/departure")
            ->assertStatus(409);

        $this->postJson("/api/v1/courier/manifests/{$manifest->id}/parcels/{$shipments[1]->id}/reconcile", [
            'status' => 'missing',
            'notes' => 'Parcel was not present at origin.',
        ])->assertOk();
        $this->postJson("/api/v1/courier/manifests/{$manifest->id}/departure")->assertOk();
        $this->postJson("/api/v1/courier/manifests/{$manifest->id}/arrival")
            ->assertOk()
            ->assertJsonPath('data.status', 'awaiting_receiving');

        $this->getJson('/api/v1/courier/dashboard')
            ->assertOk()
            ->assertJsonPath('data.summary.in_truck', 2);
        $this->assertSame(0, $manifest->shipments()->wherePivotNotNull('received_at')->count());
        $this->assertDatabaseHas('logistics_transfer_manifest_events', [
            'transfer_manifest_id' => $manifest->id,
            'event_type' => 'arrival_confirmed',
        ]);

        $this->actingAs($network['cebu_user']);
        $this->postJson('/api/v1/sorting_center/scanner/parcel-scan', [
            'tracking' => 'SHP-COURIER-001',
            'action' => 'receive',
        ])->assertOk();
        $this->postJson('/api/v1/sorting_center/scanner/parcel-scan', [
            'tracking' => 'SHP-COURIER-003',
            'action' => 'receive',
        ])->assertOk();
        $this->assertSame('arrived', $manifest->fresh()->status);
        $this->actingAs($courierUser);
        $this->getJson('/api/v1/courier/dashboard')
            ->assertOk()
            ->assertJsonPath('data.summary.in_truck', 0);

        $pickupResponse = $this->actingAs($courierUser)->getJson('/api/v1/courier/return-pickup')
            ->assertOk()
            ->assertJsonPath('data.0.eligible_parcels.0.tracking_number', 'SHP-COURIER-003');
        $trip = $pickupResponse->json('data.0.trip');
        $eligibleShipment = $pickupResponse->json('data.0.eligible_parcels.0');
        $returnManifestResponse = $this->postJson('/api/v1/courier/return-pickup/manifests', [
            'outbound_manifest_id' => $manifest->id,
            'shipment_ids' => [$eligibleShipment['id']],
        ])->assertCreated();
        $returnManifestId = $returnManifestResponse->json('data.id');
        $this->assertSame($manifest->id, $trip['id']);
        $this->assertSame($network['cebu']->id, $returnShipment->fresh()->logistics_branch_id);

        $this->postJson("/api/v1/courier/manifests/{$returnManifestId}/load-scan", [
            'tracking' => 'SHP-COURIER-003',
        ])->assertOk();
        $this->postJson("/api/v1/courier/manifests/{$returnManifestId}/departure")
            ->assertOk();
        $this->assertSame($network['laguna']->id, $returnShipment->fresh()->logistics_branch_id);
        $this->postJson("/api/v1/courier/manifests/{$returnManifestId}/arrival")
            ->assertOk();
        $this->assertSame(
            0,
            LogisticsTransferManifest::query()->findOrFail($returnManifestId)
                ->shipments()->wherePivotNotNull('received_at')->count(),
        );
        $this->actingAs($network['laguna_user'])->postJson('/api/v1/sorting_center/scanner/parcel-scan', [
            'tracking' => 'SHP-COURIER-003',
            'action' => 'receive',
        ])->assertOk();
        $this->assertSame(
            'arrived',
            LogisticsTransferManifest::query()->findOrFail($returnManifestId)->status,
        );
    }

    private function network(): array
    {
        $companyUser = $this->user('logistics', 'network.logistics@example.test');
        $logistics = Logistics::query()->create([
            'user_id' => $companyUser->id, 'last_name' => 'Network', 'first_name' => 'ShopEase', 'sex' => 'other',
            'contact_no' => '09170000000', 'birthday' => '1990-01-01', 'age' => 36, 'province' => 'Laguna',
            'municipality' => 'Calamba', 'barangay' => 'Poblacion', 'street' => 'Main Road', 'house_number' => '1',
            'registration_status' => 'active',
        ]);
        $lagunaUser = $this->user('sorting_center', 'laguna.center@example.test');
        $cebuUser = $this->user('sorting_center', 'cebu.center@example.test');
        $laguna = $this->branch($logistics, $companyUser, $lagunaUser, 'Laguna Sorting Center', 'Laguna');
        $cebu = $this->branch($logistics, $companyUser, $cebuUser, 'Cebu Sorting Center', 'Cebu');
        $sellerHubUser = $this->user('delivery_hub', 'calamba.hub@example.test');
        $buyerHubUser = $this->user('delivery_hub', 'cebu.hub@example.test');
        $sellerHub = $this->hub($laguna, $sellerHubUser, 'Calamba Hub', 'Laguna', 'Calamba');
        $buyerHub = $this->hub($cebu, $buyerHubUser, 'Cebu City Hub', 'Cebu', 'Cebu City');
        $sellerPickupRider = $this->rider($laguna, $sellerHub, 'pickup', 'Seller Barangay', 'Pickup Rider');
        $buyerDeliveryRider = $this->rider($cebu, $buyerHub, 'delivery', 'Buyer Barangay', 'Delivery Rider');
        [$lagunaCourier, $lagunaTruck] = $this->fleet($laguna, 'Luzon Courier', 'SC-LAG-001');
        [$cebuCourier, $cebuTruck] = $this->fleet($cebu, 'Visayas Courier', 'SC-CEB-001');

        return [
            'laguna' => $laguna, 'cebu' => $cebu, 'seller_hub' => $sellerHub, 'buyer_hub' => $buyerHub,
            'seller_hub_user' => $sellerHubUser, 'buyer_hub_user' => $buyerHubUser,
            'laguna_user' => $lagunaUser, 'cebu_user' => $cebuUser,
            'seller_pickup_rider' => $sellerPickupRider, 'buyer_delivery_rider' => $buyerDeliveryRider,
            'laguna_courier' => $lagunaCourier, 'laguna_truck' => $lagunaTruck,
            'cebu_courier' => $cebuCourier, 'cebu_truck' => $cebuTruck,
        ];
    }

    private function user(string $role, string $email): User
    {
        return User::query()->create(['name' => ucfirst($role), 'email' => $email, 'password' => 'password', 'role' => $role, 'registration_status' => 'active']);
    }

    private function branch(Logistics $logistics, User $companyUser, User $user, string $name, string $province): LogisticsBranch
    {
        return LogisticsBranch::query()->create([
            'logistics_id' => $logistics->id, 'user_id' => $user->id, 'name' => $name, 'address' => $name,
            'province' => $province, 'municipality' => 'Operations Center', 'status' => 'active',
        ]);
    }

    private function hub(LogisticsBranch $branch, User $user, string $name, string $province, string $municipality): LogisticsDeliveryHub
    {
        return LogisticsDeliveryHub::query()->create([
            'logistics_branch_id' => $branch->id, 'user_id' => $user->id, 'hub_name' => $name, 'province' => $province,
            'municipality' => $municipality, 'barangay' => 'Poblacion', 'postal_code' => '1000', 'street' => 'Main Road',
            'manager_name' => 'Hub Manager', 'manager_email' => $user->email, 'manager_phone' => '09170000000',
            'login_email' => $user->email, 'status' => 'active',
        ]);
    }

    private function rider(LogisticsBranch $branch, LogisticsDeliveryHub $hub, string $operation, string $barangay, string $name): Rider
    {
        [$first, $last] = explode(' ', $name, 2);
        $user = $this->user('rider', strtolower(str_replace(' ', '.', $name)).'@example.test');
        return Rider::query()->create([
            'user_id' => $user->id, 'logistics_branch_id' => $branch->id, 'first_name' => $first, 'last_name' => $last,
            'sex' => 'other', 'contact_no' => '09170000000', 'birthday' => '1990-01-01', 'age' => 36,
            'province' => $hub->province, 'municipality' => $hub->municipality, 'barangay' => $barangay,
            'street' => 'Main Road', 'house_number' => '1', 'vehicle' => 'Motorcycle', 'plate_number' => 'RIDER-001',
            'registration_status' => 'active', 'logistics_operation' => $operation, 'municipal_hub' => $hub->hub_name,
        ]);
    }

    private function fleet(LogisticsBranch $branch, string $name, string $plate): array
    {
        $courier = CourierRegistration::query()->create([
            'sorting_center_id' => $branch->id, 'first_name' => $name, 'last_name' => 'Driver',
            'email' => strtolower(str_replace(' ', '.', $name)).'@example.test', 'phone' => '09170000000',
            'vehicle' => 'Truck', 'plate_number' => $plate, 'status' => 'approved',
        ]);
        $truck = SortingCenterVehicle::query()->create([
            'sorting_center_id' => $branch->id, 'name' => 'Cargo Truck', 'type' => 'Truck', 'plate_number' => $plate,
            'availability' => 'Available', 'driver_name' => $name,
        ]);
        return [$courier, $truck];
    }
}
