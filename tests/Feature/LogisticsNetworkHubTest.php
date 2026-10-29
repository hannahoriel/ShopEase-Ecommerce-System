<?php

namespace Tests\Feature;

use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Rider\Rider;
use App\Models\User;
use App\Mail\SortingCenterCredentials;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogisticsNetworkHubTest extends TestCase
{
    use RefreshDatabase;

    public function test_network_hubs_page_receives_an_authenticated_api_configuration(): void
    {
        [$user] = $this->logisticsAccount('First Logistics');

        $this->actingAs($user)
            ->get(route('logistics.network-hubs'))
            ->assertOk()
            ->assertSee('networkHubsConfig')
            ->assertSee('api\/v1\/logistics\/network-hubs', false);
    }

    public function test_logistics_user_can_create_edit_archive_and_restore_a_sorting_center(): void
    {
        [$user, $logistics] = $this->logisticsAccount('First Logistics');
        $payload = $this->centerPayload();

        $created = $this->actingAs($user)
            ->postJson('/api/v1/logistics/network-hubs', $payload)
            ->assertCreated()
            ->assertJsonPath('data.branch_name', $payload['branch_name'])
            ->assertJsonPath('data.city', $payload['city']);

        $centerId = $created->json('data.id');
        $this->assertDatabaseHas('logistics_branches', [
            'id' => $centerId,
            'logistics_id' => $logistics->id,
            'name' => $payload['branch_name'],
            'login_email' => $payload['login_email'],
        ]);

        $this->actingAs($user)
            ->putJson("/api/v1/logistics/network-hubs/{$centerId}", [
                ...$payload,
                'branch_name' => 'Updated Sorting Center',
            ])
            ->assertOk()
            ->assertJsonPath('data.branch_name', 'Updated Sorting Center');

        $this->actingAs($user)
            ->postJson("/api/v1/logistics/network-hubs/{$centerId}/archive")
            ->assertOk();
        $this->actingAs($user)
            ->getJson('/api/v1/logistics/network-hubs')
            ->assertOk()
            ->assertJsonCount(0, 'data');
        $this->actingAs($user)
            ->getJson('/api/v1/logistics/branches')
            ->assertOk()
            ->assertJsonCount(0, 'data');
        $this->actingAs($user)
            ->getJson('/api/v1/logistics/network-hubs/archive')
            ->assertOk()
            ->assertJsonPath('data.0.id', $centerId);

        $this->actingAs($user)
            ->postJson("/api/v1/logistics/network-hubs/{$centerId}/restore")
            ->assertOk()
            ->assertJsonPath('data.branch_name', 'Updated Sorting Center');
        $this->actingAs($user)
            ->getJson('/api/v1/logistics/branches')
            ->assertOk()
            ->assertJsonPath('data.0.id', $centerId);
    }

    public function test_creating_a_sorting_center_emails_real_login_credentials_to_its_login_email(): void
    {
        Mail::fake();
        [$user] = $this->logisticsAccount('Credential Email Test Logistics');
        $payload = [
            ...$this->centerPayload(),
            'manager_email' => 'branch.manager.test@example.com',
            'login_email' => 'sorting.center.test@example.com',
            'generated_password' => 'TestBranchPassword2026!',
        ];

        $this->actingAs($user)
            ->postJson('/api/v1/logistics/network-hubs', $payload)
            ->assertCreated()
            ->assertJsonPath('data.manager_email', 'branch.manager.test@example.com')
            ->assertJsonPath('data.login_email', 'sorting.center.test@example.com')
            ->assertJsonMissingPath('data.generated_password')
            ->assertJsonMissingPath('data.password');

        $branchUser = User::query()->where('email', 'sorting.center.test@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_SORTING_CENTER, $branchUser->role);
        Mail::assertSent(SortingCenterCredentials::class, function (SortingCenterCredentials $mail) use ($branchUser): bool {
            $password = $mail->password;

            return $mail->hasTo($branchUser->email)
                && strlen($password) === 8
                && preg_match('/[a-z]/', $password) === 1
                && preg_match('/[A-Z]/', $password) === 1
                && preg_match('/[0-9]/', $password) === 1
                && preg_match('/[^A-Za-z0-9]/', $password) === 1
                && Hash::check($password, $branchUser->password);
        });
    }

    public function test_network_hubs_enforce_scoped_unique_provinces_and_login_emails(): void
    {
        [$user] = $this->logisticsAccount('First Logistics');
        [$otherUser] = $this->logisticsAccount('Second Logistics');
        $payload = $this->centerPayload();

        $this->actingAs($user)->postJson('/api/v1/logistics/network-hubs', $payload)->assertCreated();
        $this->actingAs($otherUser)->postJson('/api/v1/logistics/network-hubs', [
            ...$payload,
            'login_email' => 'second-company-center@example.com',
        ])->assertCreated();
        $this->actingAs($user)
            ->postJson('/api/v1/logistics/network-hubs', [
                ...$payload,
                'branch_name' => 'Second Center',
                'login_email' => 'another@example.com',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('province');

        $this->actingAs($user)
            ->postJson('/api/v1/logistics/network-hubs', [
                ...$payload,
                'branch_name' => 'Second Center',
                'province' => 'Cavite',
                'login_email' => $payload['login_email'],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('login_email');

        $this->assertDatabaseCount('logistics_branches', 2);
    }

    public function test_delivery_hubs_are_scoped_editable_archivable_and_restorable(): void
    {
        [$user, $logistics] = $this->logisticsAccount('First Logistics');
        $center = LogisticsBranch::create([
            'logistics_id' => $logistics->id,
            'name' => 'Laguna Main Sorting Center',
            'address' => 'Calamba, Laguna',
            'province' => 'Laguna',
            'municipality' => 'Calamba',
            'status' => 'active',
        ]);
        $riderUser = User::factory()->create([
            'role' => User::ROLE_RIDER,
            'registration_status' => 'active',
        ]);
        Rider::create([
            'user_id' => $riderUser->id,
            'logistics_branch_id' => $center->id,
            'last_name' => 'Rider',
            'first_name' => 'Alex',
            'sex' => 'other',
            'contact_no' => '09171234567',
            'birthday' => '1990-01-01',
            'age' => 36,
            'province' => 'Laguna',
            'municipality' => 'Pila',
            'barangay' => 'San Antonio',
            'street' => 'Hub Road',
            'house_number' => '2',
            'vehicle' => 'Delivery Van',
            'plate_number' => 'ABC 1234',
            'courier_company' => 'First Logistics',
            'municipal_hub' => 'Pila Delivery Hub',
            'registration_status' => 'active',
        ]);
        [$otherUser] = $this->logisticsAccount('Other Logistics');
        $hubPayload = $this->hubPayload();

        $created = $this->actingAs($user)
            ->postJson("/api/v1/logistics/network-hubs/{$center->id}/delivery-hubs", $hubPayload)
            ->assertCreated()
            ->assertJsonPath('data.hub_name', $hubPayload['hub_name'])
            ->assertJsonPath('data.province', 'Laguna')
            ->assertJsonPath('data.riders_count', 1)
            ->assertJsonPath('data.couriers_count', 1)
            ->assertJsonPath('data.vehicles_count', 1);
        $hubId = $created->json('data.id');

        $this->actingAs($user)
            ->putJson("/api/v1/logistics/delivery-hubs/{$hubId}", [
                ...$hubPayload,
                'hub_name' => 'Updated Pila Delivery Hub',
            ])
            ->assertOk()
            ->assertJsonPath('data.hub_name', 'Updated Pila Delivery Hub');

        $this->actingAs($otherUser)
            ->postJson("/api/v1/logistics/delivery-hubs/{$hubId}/archive")
            ->assertNotFound();

        $this->actingAs($user)
            ->postJson("/api/v1/logistics/delivery-hubs/{$hubId}/archive")
            ->assertOk();
        $this->actingAs($user)
            ->getJson("/api/v1/logistics/network-hubs/{$center->id}/delivery-hubs")
            ->assertOk()
            ->assertJsonCount(0, 'data');
        $this->actingAs($user)
            ->getJson("/api/v1/logistics/network-hubs/{$center->id}/delivery-hubs/archive")
            ->assertOk()
            ->assertJsonPath('data.0.id', $hubId);

        $this->actingAs($user)
            ->postJson("/api/v1/logistics/delivery-hubs/{$hubId}/restore")
            ->assertOk()
            ->assertJsonPath('data.hub_name', 'Updated Pila Delivery Hub');

        $this->assertDatabaseHas('logistics_delivery_hubs', [
            'id' => $hubId,
            'logistics_branch_id' => $center->id,
            'archived_at' => null,
        ]);
    }

    private function logisticsAccount(string $businessName): array
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

        return [$user, $logistics];
    }

    private function centerPayload(): array
    {
        return [
            'branch_name' => 'Laguna Main Sorting Center',
            'province' => 'Laguna',
            'city' => 'Calamba',
            'barangay' => 'Real',
            'postal_code' => '4000',
            'house_no' => '1',
            'street' => 'Main Street',
            'building' => '',
            'subdivision' => '',
            'manager_name' => 'Alex Manager',
            'manager_email' => 'manager@example.com',
            'manager_phone' => '09171234567',
            'login_email' => 'center@example.com',
        ];
    }

    private function hubPayload(): array
    {
        return [
            'hub_name' => 'Pila Delivery Hub',
            'city' => 'Pila',
            'barangay' => 'San Antonio',
            'postal_code' => '4010',
            'house_no' => '2',
            'street' => 'Hub Road',
            'building' => '',
            'subdivision' => '',
            'manager_name' => 'Hub Manager',
            'manager_email' => 'hub-manager@example.com',
            'manager_phone' => '09171234568',
            'login_email' => 'hub@example.com',
        ];
    }
}
