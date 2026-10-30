<?php

namespace Tests\Feature;

use App\Models\Logistics\DeliveryHubRiderRegistration;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\Rider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeliveryHubRiderRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_rider_registration_appears_at_the_selected_hub_and_approval_activates_login(): void
    {
        [$hubUser, $hub] = $this->makeHub();
        $email = 'mobile.rider@example.test';
        $verificationToken = 'verified-mobile-email-token';
        $scope = 'mobile-registration:rider:'.hash('sha256', mb_strtolower($email));
        Cache::put("{$scope}:verified", Hash::make($verificationToken));

        $this->getJson('/api/v1/delivery-hubs/available?province=Laguna')
            ->assertOk()->assertJsonPath('data.0.id', $hub->id);

        $registrationResponse = $this->postJson('/api/v1/auth/register', [
            'role' => User::ROLE_RIDER,
            'registration_type' => 'rider',
            'delivery_hub_id' => $hub->id,
            'last_name' => 'Reyes',
            'first_name' => 'Jamie',
            'middle_initial' => 'A.',
            'sex' => 'female',
            'email' => $email,
            'email_verification_token' => $verificationToken,
            'contact_no' => '09170000001',
            'birthday' => '1998-03-15',
            'province' => 'Laguna',
            'municipality' => 'Pila',
            'barangay' => 'Poblacion',
            'street' => 'Rizal Street',
            'house_number' => '12',
            'vehicle' => 'Motorcycle',
            'plate_number' => 'ABC 1234',
            'password' => 'RiderPassword123!',
            'password_confirmation' => 'RiderPassword123!',
        ])->assertCreated()->assertJsonPath('status', 'pending');

        $application = DeliveryHubRiderRegistration::query()->where('user_id', User::query()->where('email', $email)->value('id'))->firstOrFail();
        $this->assertSame($hub->id, $application->logistics_delivery_hub_id);
        $this->assertDatabaseHas('users', ['email' => $email, 'registration_status' => 'pending']);

        $this->actingAs($hubUser)->getJson('/api/v1/delivery_hub/rider-registrations')
            ->assertOk()->assertJsonPath('data.0.id', $application->id)
            ->assertJsonPath('data.0.email', $email)
            ->assertJsonPath('data.0.delivery_hub_name', 'Pila Delivery Hub');

        $this->postJson('/api/v1/auth/login', ['email' => $email, 'password' => 'RiderPassword123!'])
            ->assertUnprocessable();

        $this->patchJson('/api/v1/delivery_hub/rider-registrations/'.$application->id.'/decision', ['status' => 'approved'])
            ->assertOk()->assertJsonPath('data.status', 'approved');

        $this->assertDatabaseHas('users', ['email' => $email, 'registration_status' => 'active']);
        $this->assertDatabaseHas('riders', [
            'user_id' => $application->user_id,
            'logistics_branch_id' => $hub->logistics_branch_id,
            'municipal_hub' => 'Pila Delivery Hub',
            'registration_status' => 'active',
        ]);
        $this->assertDatabaseHas('registrations', ['user_id' => $application->user_id, 'status' => 'approved']);

        $this->postJson('/api/v1/auth/login', ['email' => $email, 'password' => 'RiderPassword123!'])
            ->assertOk()->assertJsonPath('role', User::ROLE_RIDER);

        $this->getJson('/api/v1/rider/registration-status/'.$registrationResponse->json('status_token'))
            ->assertOk()->assertJsonPath('status', 'approved');
    }

    public function test_delivery_hub_cannot_read_or_review_another_hubs_rider_application(): void
    {
        [$hubUser, $hub] = $this->makeHub();
        [, $otherHub] = $this->makeHub('other.hub@example.test');
        $applicant = User::query()->create([
            'name' => 'Taylor Applicant', 'email' => 'taylor@example.test', 'password' => 'password',
            'role' => User::ROLE_RIDER, 'registration_status' => 'pending',
        ]);
        $rider = Rider::query()->create([
            'user_id' => $applicant->id, 'first_name' => 'Taylor', 'last_name' => 'Applicant', 'sex' => 'other',
            'contact_no' => '09170000002', 'birthday' => '1998-01-01', 'age' => 28, 'province' => 'Laguna',
            'municipality' => 'Pila', 'barangay' => 'Poblacion', 'street' => 'Main', 'house_number' => '1',
            'vehicle' => 'Motorcycle', 'plate_number' => 'DEF 5678', 'registration_status' => 'pending',
        ]);
        $application = DeliveryHubRiderRegistration::query()->create([
            'logistics_delivery_hub_id' => $otherHub->id, 'user_id' => $rider->user_id, 'status' => 'pending',
        ]);

        $this->actingAs($hubUser)->getJson('/api/v1/delivery_hub/rider-registrations/'.$application->id)->assertNotFound();
        $this->patchJson('/api/v1/delivery_hub/rider-registrations/'.$application->id.'/decision', ['status' => 'approved'])
            ->assertNotFound();
        $this->assertDatabaseHas('users', ['id' => $applicant->id, 'registration_status' => 'pending']);
    }

    public function test_delivery_hub_rejection_requires_a_reason_and_blocks_login(): void
    {
        [$hubUser, $hub] = $this->makeHub();
        $applicant = User::query()->create([
            'name' => 'Rejected Rider', 'email' => 'rejected.rider@example.test', 'password' => 'RiderPassword123!',
            'role' => User::ROLE_RIDER, 'registration_status' => 'pending',
        ]);
        Rider::query()->create([
            'user_id' => $applicant->id, 'first_name' => 'Rejected', 'last_name' => 'Rider', 'sex' => 'other',
            'contact_no' => '09170000004', 'birthday' => '1998-01-01', 'age' => 28, 'province' => 'Laguna',
            'municipality' => 'Pila', 'barangay' => 'Poblacion', 'street' => 'Main', 'house_number' => '1',
            'vehicle' => 'Motorcycle', 'plate_number' => 'GHI 9012', 'registration_status' => 'pending',
        ]);
        $application = DeliveryHubRiderRegistration::query()->create([
            'logistics_delivery_hub_id' => $hub->id, 'user_id' => $applicant->id, 'status' => 'pending',
        ]);

        $this->actingAs($hubUser)->patchJson('/api/v1/delivery_hub/rider-registrations/'.$application->id.'/decision', ['status' => 'rejected'])
            ->assertUnprocessable();
        $this->patchJson('/api/v1/delivery_hub/rider-registrations/'.$application->id.'/decision', [
            'status' => 'rejected', 'reason' => 'The license image is unreadable.',
        ])->assertOk()->assertJsonPath('data.rejection_reason', 'The license image is unreadable.');

        $this->assertDatabaseHas('users', ['id' => $applicant->id, 'registration_status' => 'rejected']);
        $this->postJson('/api/v1/auth/login', ['email' => $applicant->email, 'password' => 'RiderPassword123!'])
            ->assertUnprocessable();
    }

    private function makeHub(string $hubEmail = 'delivery.hub@example.test'): array
    {
        $centerUser = User::query()->create([
            'name' => 'Test Center', 'email' => 'center+'.uniqid().'@example.test', 'password' => 'password',
            'role' => User::ROLE_SORTING_CENTER, 'registration_status' => 'active',
        ]);
        $companyUser = User::query()->create([
            'name' => 'Test Logistics', 'email' => 'logistics+'.uniqid().'@example.test', 'password' => 'password',
            'role' => User::ROLE_LOGISTICS, 'registration_status' => 'active',
        ]);
        $hubUser = User::query()->create([
            'name' => 'Pila Delivery Hub', 'email' => $hubEmail, 'password' => 'password',
            'role' => User::ROLE_DELIVERY_HUB, 'registration_status' => 'active',
        ]);
        $company = Logistics::query()->create([
            'user_id' => $companyUser->id, 'last_name' => 'Logistics', 'first_name' => 'Demo', 'sex' => 'other',
            'contact_no' => '09170000000', 'birthday' => '1990-01-01', 'age' => 36,
            'province' => 'Laguna', 'municipality' => 'Pila', 'barangay' => 'Poblacion', 'street' => 'Main Street',
            'house_number' => '1', 'business_name' => 'Demo Logistics', 'registration_status' => 'active',
        ]);
        $branch = LogisticsBranch::query()->create([
            'logistics_id' => $company->id, 'user_id' => $centerUser->id, 'name' => 'Pila Sorting Center',
            'address' => 'Pila, Laguna', 'province' => 'Laguna', 'municipality' => 'Pila', 'status' => 'active',
        ]);
        $hub = LogisticsDeliveryHub::query()->create([
            'logistics_branch_id' => $branch->id, 'user_id' => $hubUser->id, 'hub_name' => 'Pila Delivery Hub',
            'province' => 'Laguna', 'municipality' => 'Pila', 'barangay' => 'Poblacion', 'postal_code' => '4010',
            'street' => 'Rizal Street', 'manager_name' => 'Hub Manager', 'manager_email' => $hubEmail,
            'manager_phone' => '09170000003', 'login_email' => $hubEmail, 'status' => 'active',
        ]);

        return [$hubUser, $hub];
    }
}
