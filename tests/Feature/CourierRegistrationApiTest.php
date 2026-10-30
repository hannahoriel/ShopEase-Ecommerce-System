<?php

namespace Tests\Feature;

use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourierRegistrationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_sorting_center_can_list_inspect_and_review_only_its_own_pending_applications(): void
    {
        [$center, $branch] = $this->makeCenter('center.one@example.test');
        [, $otherBranch] = $this->makeCenter('center.two@example.test');
        $application = CourierRegistration::query()->create([
            'sorting_center_id' => $branch->id, 'first_name' => 'Jamie', 'last_name' => 'Reyes',
            'email' => 'jamie@example.test', 'phone' => '09170000001', 'vehicle' => 'Truck', 'plate_number' => 'ABC 1234',
        ]);
        $otherApplication = CourierRegistration::query()->create([
            'sorting_center_id' => $otherBranch->id, 'first_name' => 'Other', 'last_name' => 'Applicant',
            'email' => 'other@example.test', 'phone' => '09170000002', 'vehicle' => 'Truck', 'plate_number' => 'XYZ 4321',
        ]);

        $this->actingAs($center)->getJson('/api/v1/sorting_center/courier-registrations')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $application->id);
        $this->getJson('/api/v1/sorting_center/courier-registrations/'.$application->id)
            ->assertOk()->assertJsonPath('data.email', 'jamie@example.test');
        $this->getJson('/api/v1/sorting_center/courier-registrations/'.$otherApplication->id)->assertNotFound();

        $this->patchJson('/api/v1/sorting_center/courier-registrations/'.$application->id.'/decision', ['status' => 'approved'])
            ->assertOk()->assertJsonPath('data.status', 'approved');
        $this->assertDatabaseHas('courier_registrations', [
            'id' => $application->id, 'status' => 'approved', 'reviewed_by' => $center->id,
        ]);
        $this->patchJson('/api/v1/sorting_center/courier-registrations/'.$application->id.'/decision', ['status' => 'rejected', 'reason' => 'No'])
            ->assertUnprocessable();
        $this->patchJson('/api/v1/sorting_center/courier-registrations/'.$otherApplication->id.'/decision', ['status' => 'approved'])
            ->assertNotFound();
    }

    public function test_rejection_requires_a_reason_and_persists_it(): void
    {
        [$center, $branch] = $this->makeCenter('center.reject@example.test');
        $application = CourierRegistration::query()->create([
            'sorting_center_id' => $branch->id, 'first_name' => 'Taylor', 'last_name' => 'Santos',
            'email' => 'taylor@example.test', 'phone' => '09170000003', 'vehicle' => 'Truck', 'plate_number' => 'DEF 5678',
        ]);

        $this->actingAs($center)->patchJson('/api/v1/sorting_center/courier-registrations/'.$application->id.'/decision', ['status' => 'rejected'])
            ->assertUnprocessable();
        $this->patchJson('/api/v1/sorting_center/courier-registrations/'.$application->id.'/decision', [
            'status' => 'rejected', 'reason' => 'Unreadable OR/CR document',
        ])->assertOk()->assertJsonPath('data.rejection_reason', 'Unreadable OR/CR document');
    }

    public function test_applicant_can_submit_a_courier_registration_to_a_sorting_center(): void
    {
        [, $branch] = $this->makeCenter('center.intake@example.test');
        Storage::fake('public');

        $this->postJson('/api/v1/courier-registrations', [
            'sorting_center_id' => $branch->id, 'first_name' => 'Alex', 'middle_name' => 'James',
            'last_name' => 'Garcia', 'sex' => 'Male', 'birthday' => '1998-03-15',
            'email' => 'alex.garcia@example.test', 'phone' => '09170000004',
            'region' => 'CALABARZON', 'province' => 'Laguna', 'city' => 'Calamba',
            'barangay' => 'Poblacion', 'street' => 'Rizal Street', 'house_no' => '12',
            'postal_code' => '4027', 'vehicle' => 'Truck', 'plate_number' => 'ABC 1234',
            'or_cr' => UploadedFile::fake()->image('or-cr.jpg'),
        ])->assertCreated()->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.email', 'alex.garcia@example.test');

        $this->assertDatabaseHas('courier_registrations', [
            'sorting_center_id' => $branch->id, 'email' => 'alex.garcia@example.test', 'status' => 'pending',
        ]);
        $this->assertCount(1, Storage::disk('public')->allFiles('courier-registrations/or-cr'));
    }

    private function makeCenter(string $email): array
    {
        $center = User::query()->create(['name' => 'Test Center', 'email' => $email, 'password' => 'password', 'role' => User::ROLE_SORTING_CENTER]);
        $companyUser = User::query()->create(['name' => 'Test Logistics', 'email' => 'company+'.uniqid().'@example.test', 'password' => 'password', 'role' => User::ROLE_LOGISTICS]);
        $company = Logistics::query()->create([
            'user_id' => $companyUser->id, 'last_name' => 'Logistics', 'first_name' => 'Demo', 'sex' => 'other',
            'contact_no' => '09170000000', 'birthday' => '1990-01-01', 'age' => 36,
            'province' => 'Laguna', 'municipality' => 'Calamba', 'barangay' => 'Poblacion',
            'street' => 'Main Street', 'house_number' => '1',
        ]);
        $branch = LogisticsBranch::query()->create([
            'logistics_id' => $company->id, 'user_id' => $center->id, 'name' => 'Test Center', 'address' => 'Calamba, Laguna',
        ]);
        return [$center, $branch];
    }
}
