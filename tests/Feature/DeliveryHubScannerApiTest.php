<?php

namespace Tests\Feature;

use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\DeliveryHubScannerState;
use App\Models\User;
use Database\Seeders\DeliveryHubScannerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryHubScannerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_hub_can_save_and_reload_scanner_workflow_state(): void
    {
        [$hubUser, $hub] = $this->makeHub('scanner.one@example.test');
        $state = [
            'parcels' => [['id' => 'p-1', 'tracking' => 'SHP-TEST-001', 'status' => 'received', 'kind' => 'sorting']],
            'manifests' => [['id' => 'm-1', 'parcelIds' => ['p-1'], 'status' => 'dispatched']],
            'events' => [['action' => 'Parcel received', 'tracking' => 'SHP-TEST-001']],
            'issues' => [['id' => 'i-1', 'tracking' => 'SHP-TEST-001', 'status' => 'Open']],
        ];

        $this->actingAs($hubUser)->getJson('/api/v1/delivery_hub/scanner')
            ->assertOk()->assertJsonPath('hub_id', $hub->id)->assertJsonPath('state.parcels', []);
        $this->putJson('/api/v1/delivery_hub/scanner', ['state' => $state])
            ->assertOk()->assertJsonPath('state.parcels.0.status', 'received');
        $this->getJson('/api/v1/delivery_hub/scanner')
            ->assertOk()->assertExactJson(['hub_id' => $hub->id, 'state' => $state]);
        $this->actingAs($hubUser)->get('/delivery-hub/scanners')
            ->assertOk()->assertSee('SHP-TEST-001', false)->assertSee('/api/v1/delivery_hub/scanner', false);
    }

    public function test_scanner_state_is_isolated_to_authenticated_hub(): void
    {
        [$firstUser, $firstHub] = $this->makeHub('scanner.owner@example.test');
        [$otherUser, $otherHub] = $this->makeHub('scanner.other@example.test');
        $this->actingAs($firstUser)->putJson('/api/v1/delivery_hub/scanner', ['state' => [
            'parcels' => [['tracking' => 'PRIVATE-001', 'status' => 'incoming']], 'manifests' => [], 'events' => [],
        ]])->assertOk();

        $this->actingAs($otherUser)->getJson('/api/v1/delivery_hub/scanner')
            ->assertOk()->assertJsonPath('hub_id', $otherHub->id)->assertJsonPath('state.parcels', []);
        $this->assertNotSame($firstHub->id, $otherHub->id);
    }

    public function test_invalid_scanner_records_are_rejected(): void
    {
        [$hubUser] = $this->makeHub('scanner.validation@example.test');
        $this->actingAs($hubUser)->putJson('/api/v1/delivery_hub/scanner', ['state' => [
            'parcels' => [['status' => 'incoming']], 'manifests' => [], 'events' => [],
        ]])->assertUnprocessable()->assertJsonValidationErrors('state.parcels.0.tracking');
    }

    public function test_demo_fixture_supports_receive_scan_and_returning_truck_flow(): void
    {
        $this->seed(DeliveryHubScannerSeeder::class);
        $hub = LogisticsDeliveryHub::query()->whereHas('user', fn ($query) => $query->where('email', 'scanner.hub@shopease.test'))->firstOrFail();
        $fixture = DeliveryHubScannerState::query()->where('logistics_delivery_hub_id', $hub->id)->firstOrFail();
        $manifest = collect($fixture->state['manifests'])->firstWhere('id', 'DH-SEED-MANIFEST-001');

        $this->assertSame('sorting', $manifest['sourceType']);
        $this->assertSame('loaded', $manifest['status']);
        $this->assertNotEmpty($manifest['truckId']);
        $this->assertSame('incoming', collect($fixture->state['parcels'])->firstWhere('tracking', 'SHP-DH-DEMO-001')['status']);
    }

    private function makeHub(string $email): array
    {
        $hubUser = User::query()->create([
            'name' => 'Test Delivery Hub', 'email' => $email, 'password' => 'password', 'role' => User::ROLE_DELIVERY_HUB,
        ]);
        $companyUser = User::query()->create([
            'name' => 'Test Logistics Company', 'email' => 'company+'.uniqid().'@example.test', 'password' => 'password', 'role' => User::ROLE_LOGISTICS,
        ]);
        $company = Logistics::query()->create([
            'user_id' => $companyUser->id, 'last_name' => 'Logistics', 'first_name' => 'Test', 'sex' => 'other',
            'contact_no' => '09170000000', 'birthday' => '1990-01-01', 'age' => 36, 'province' => 'Laguna',
            'municipality' => 'Calamba', 'barangay' => 'Poblacion', 'street' => 'Main Street', 'house_number' => '1',
        ]);
        $branch = LogisticsBranch::query()->create([
            'logistics_id' => $company->id, 'user_id' => $companyUser->id, 'name' => 'Test Branch', 'address' => 'Calamba, Laguna',
        ]);
        $hub = LogisticsDeliveryHub::query()->create([
            'logistics_branch_id' => $branch->id, 'user_id' => $hubUser->id, 'hub_name' => 'Test Hub',
            'province' => 'Laguna', 'municipality' => 'Calamba', 'barangay' => 'Poblacion', 'postal_code' => '4027',
            'street' => 'Main Street', 'manager_name' => 'Test Manager', 'manager_email' => $email,
            'manager_phone' => '09170000001', 'login_email' => $email,
        ]);

        return [$hubUser, $hub];
    }
}
