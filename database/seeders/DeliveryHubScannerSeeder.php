<?php

namespace Database\Seeders;

use App\Models\Logistics\DeliveryHubScannerState;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeliveryHubScannerSeeder extends Seeder
{
    public function run(): void
    {
        $hubUser = User::query()->updateOrCreate(['email' => 'scanner.hub@shopease.test'], [
            'name' => 'Demo Delivery Hub',
            'password' => 'Scanner@123',
            'role' => User::ROLE_DELIVERY_HUB,
            'registration_status' => 'active',
            'email_verified_at' => now(),
        ]);
        $companyUser = User::query()->firstOrCreate(['email' => 'scanner.logistics@shopease.test'], [
            'name' => 'Demo Scanner Logistics', 'password' => 'Scanner@123', 'role' => User::ROLE_LOGISTICS,
            'registration_status' => 'active', 'email_verified_at' => now(),
        ]);
        $company = Logistics::query()->updateOrCreate(['user_id' => $companyUser->id], [
            'last_name' => 'Logistics', 'first_name' => 'Demo', 'sex' => 'other', 'contact_no' => '09170000000',
            'birthday' => '1990-01-01', 'age' => 36, 'province' => 'Laguna', 'municipality' => 'Calamba',
            'barangay' => 'Poblacion', 'street' => 'Demo Road', 'house_number' => '1',
        ]);
        $branch = LogisticsBranch::query()->firstOrCreate(
            ['logistics_id' => $company->id, 'name' => 'Demo Sorting Center'],
            ['user_id' => $companyUser->id, 'address' => 'Poblacion, Calamba, Laguna']
        );
        $hub = LogisticsDeliveryHub::query()->updateOrCreate(['user_id' => $hubUser->id], [
            'logistics_branch_id' => $branch->id, 'hub_name' => 'Demo Delivery Hub', 'province' => 'Laguna',
            'municipality' => 'Calamba', 'barangay' => 'Poblacion', 'postal_code' => '4027', 'street' => 'Demo Road',
            'manager_name' => 'Demo Hub Manager', 'manager_email' => $hubUser->email, 'manager_phone' => '09170000001',
            'login_email' => $hubUser->email, 'status' => 'active',
        ]);

        $scannerState = DeliveryHubScannerState::query()->firstOrCreate(
            ['logistics_delivery_hub_id' => $hub->id],
            ['updated_by' => $hubUser->id, 'state' => [
                'parcels' => [
                    [
                        'id' => 'DH-SEED-IN-001', 'tracking' => 'SHP-DH-DEMO-001', 'kind' => 'sorting',
                        'status' => 'incoming', 'barangay' => 'Poblacion', 'destBarangay' => 'Poblacion',
                        'loadedFromName' => 'Demo Sorting Center', 'courierName' => 'Mark Reyes', 'truckId' => 'ABC 1234',
                        'transferId' => 'DH-SEED-MANIFEST-001', 'history' => [
                            ['status' => 'In Transit', 'date' => now()->subHour()->toISOString(), 'location' => 'Demo Sorting Center', 'details' => 'Demo parcel dispatched to hub'],
                        ],
                    ],
                    [
                        'id' => 'DH-SEED-RECEIVED-002', 'tracking' => 'SHP-DH-DEMO-002', 'kind' => 'sorting',
                        'status' => 'received', 'barangay' => 'Pansol', 'destBarangay' => 'Pansol', 'receivedAt' => now()->subMinutes(20)->toISOString(),
                        'loadedFromName' => 'Demo Sorting Center', 'courierName' => 'Mark Reyes', 'truckId' => 'ABC 1234',
                        'transferId' => 'DH-SEED-MANIFEST-001', 'history' => [
                            ['status' => 'In Transit', 'date' => now()->subHour()->toISOString(), 'location' => 'Demo Sorting Center', 'details' => 'Demo parcel dispatched to hub'],
                            ['status' => 'Received at Delivery Hub', 'date' => now()->subMinutes(20)->toISOString(), 'location' => 'Demo Delivery Hub', 'details' => 'Demo scan fixture'],
                        ],
                    ],
                ],
                'manifests' => [[
                    'id' => 'DH-SEED-MANIFEST-001', 'sourceType' => 'sorting', 'sourceLabel' => 'Demo Sorting Center',
                    'originName' => 'Demo Sorting Center', 'originCenterId' => $branch->id, 'destinationHubId' => $hub->id,
                    'destinationHubName' => 'Demo Delivery Hub', 'barangay' => 'Poblacion', 'status' => 'loaded',
                    'createdAt' => now()->subHour()->toISOString(), 'courierName' => 'Mark Reyes', 'truckId' => 'ABC 1234',
                    'parcelIds' => ['DH-SEED-IN-001', 'DH-SEED-RECEIVED-002'], 'count' => 2,
                ]],
                'events' => [[
                    'at' => now()->subMinutes(20)->toISOString(), 'action' => 'Demo data loaded',
                    'tracking' => 'SHP-DH-DEMO-002', 'desc' => 'Received parcel sample for scanner workflow testing',
                ]],
                'issues' => [],
            ]]
        );

        // Repair the initial demo batch if an earlier version of this seeder
        // stored it as an outbound transfer instead of an inbound load.
        $state = $scannerState->state;
        $manifestIndex = collect($state['manifests'] ?? [])->search(
            static fn (array $manifest): bool => ($manifest['id'] ?? null) === 'DH-SEED-MANIFEST-001'
        );
        if ($manifestIndex !== false) {
            $manifest = $state['manifests'][$manifestIndex];
            if (($manifest['sourceType'] ?? null) !== 'sorting' || ($manifest['status'] ?? null) !== 'loaded') {
                $state['manifests'][$manifestIndex]['sourceType'] = 'sorting';
                $state['manifests'][$manifestIndex]['status'] = 'loaded';
                $scannerState->update(['updated_by' => $hubUser->id, 'state' => $state]);
            }
        }
    }
}
