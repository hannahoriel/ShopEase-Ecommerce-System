<?php

namespace Tests\Feature;

use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\SortingCenterVehicle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SortingCenterFleetApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_sorting_center_can_create_update_and_list_its_vehicles(): void
    {
        [$center, $branch] = $this->makeCenter('fleet.one@example.test');
        Storage::fake('public');
        $created = $this->actingAs($center)->postJson('/api/v1/sorting_center/fleet/vehicles', [
            'name' => 'Isuzu N-Series', 'type' => 'Truck', 'plate_number' => 'XYZ 1234',
            'make' => 'Isuzu', 'model' => 'N-Series', 'year' => 2024, 'capacity' => '3 tons',
            'availability' => 'Available', 'or_cr' => UploadedFile::fake()->image('or-cr.jpg'),
        ])->assertCreated()->assertJsonPath('data.name', 'Isuzu N-Series');

        $vehicleId = $created->json('data.id');
        $this->patchJson('/api/v1/sorting_center/fleet/vehicles/'.$vehicleId, [
            'availability' => 'Maintenance', 'driver_name' => 'Alex Garcia',
        ])->assertOk()->assertJsonPath('data.availability', 'Maintenance');
        $this->getJson('/api/v1/sorting_center/fleet/vehicles')->assertOk()
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.sorting_center_id', $branch->id);
        $this->assertCount(1, Storage::disk('public')->allFiles('sorting-center/vehicles/or-cr'));
    }

    public function test_sorting_center_can_register_a_truck_and_assign_an_approved_courier(): void
    {
        [$center, $branch] = $this->makeCenter('fleet.create-assigned@example.test');
        $courier = CourierRegistration::query()->create([
            'sorting_center_id' => $branch->id, 'first_name' => 'Andrea', 'middle_name' => 'Mae',
            'last_name' => 'Santos', 'email' => 'andrea.create@example.test', 'phone' => '09170000011',
            'vehicle' => 'Van', 'plate_number' => 'OLD 1234', 'status' => 'approved', 'reviewed_at' => now(),
        ]);

        $this->actingAs($center)->postJson('/api/v1/sorting_center/fleet/vehicles', [
            'name' => 'Manila Linehaul Truck', 'type' => 'Truck', 'plate_number' => 'NEW 5678',
            'courier_registration_id' => $courier->id,
        ])->assertCreated()->assertJsonPath('data.driver_name', 'Andrea Mae Santos');

        $this->assertDatabaseHas('sorting_center_vehicles', [
            'sorting_center_id' => $branch->id,
            'plate_number' => 'NEW 5678',
            'courier_registration_id' => $courier->id,
        ]);

        $this->assertDatabaseHas('courier_registrations', [
            'id' => $courier->id, 'vehicle' => 'Van', 'plate_number' => 'OLD 1234',
        ]);
        $this->getJson('/api/v1/sorting_center/fleet/approved-couriers')->assertOk()
            ->assertJsonPath('data.0.vehicle', 'Truck')->assertJsonPath('data.0.plate_number', 'NEW 5678');
        $this->getJson('/api/v1/sorting_center/fleet/vehicles')->assertOk()
            ->assertJsonPath('data.0.name', 'Manila Linehaul Truck')
            ->assertJsonPath('data.0.driver_name', 'Andrea Mae Santos');
    }

    public function test_sorting_center_can_reassign_a_courier_when_editing_a_registered_vehicle(): void
    {
        [$center, $branch] = $this->makeCenter('fleet.reassign@example.test');
        $vehicle = SortingCenterVehicle::query()->create([
            'sorting_center_id' => $branch->id, 'name' => 'Manila Truck', 'type' => 'Truck', 'plate_number' => 'MNL 1111',
        ]);
        $courier = CourierRegistration::query()->create([
            'sorting_center_id' => $branch->id, 'first_name' => 'Alex', 'last_name' => 'Reyes',
            'email' => 'alex.reassign@example.test', 'phone' => '09170000012', 'vehicle' => null,
            'plate_number' => null, 'status' => 'approved', 'reviewed_at' => now(),
        ]);

        $this->actingAs($center)->patchJson('/api/v1/sorting_center/fleet/vehicles/'.$vehicle->id, [
            'courier_registration_id' => $courier->id,
        ])->assertOk()->assertJsonPath('data.driver_name', 'Alex Reyes');

        $this->assertDatabaseHas('courier_registrations', [
            'id' => $courier->id, 'vehicle' => null, 'plate_number' => null,
        ]);
    }

    public function test_approved_courier_and_vehicle_can_be_archived_and_restored(): void
    {
        [$center, $branch] = $this->makeCenter('fleet.archive@example.test');
        $vehicle = SortingCenterVehicle::query()->create([
            'sorting_center_id' => $branch->id, 'name' => 'Hino 500', 'type' => 'Truck', 'plate_number' => 'ABC 4321',
        ]);
        $courier = CourierRegistration::query()->create([
            'sorting_center_id' => $branch->id, 'first_name' => 'Jamie', 'last_name' => 'Reyes',
            'email' => 'jamie@example.test', 'phone' => '09170000001', 'vehicle' => 'Truck',
            'plate_number' => 'DEF 6789', 'status' => 'approved', 'reviewed_at' => now(),
        ]);

        $this->actingAs($center)->getJson('/api/v1/sorting_center/fleet/approved-couriers')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $courier->id);
        $this->postJson('/api/v1/sorting_center/fleet/vehicles/'.$vehicle->id.'/archive', ['reason' => 'Vehicle retired'])
            ->assertCreated();
        $this->postJson('/api/v1/sorting_center/fleet/couriers/'.$courier->id.'/archive', ['reason' => 'Courier requested removal'])
            ->assertCreated();
        $this->getJson('/api/v1/sorting_center/fleet/archives')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/v1/sorting_center/fleet/vehicles')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/sorting_center/fleet/approved-couriers')->assertOk()->assertJsonCount(0, 'data');

        $this->postJson('/api/v1/sorting_center/fleet/vehicles/'.$vehicle->id.'/restore', [])->assertOk();
        $this->postJson('/api/v1/sorting_center/fleet/couriers/'.$courier->id.'/restore', [])->assertOk();
        $this->getJson('/api/v1/sorting_center/fleet/archives')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_sorting_center_can_edit_only_an_approved_couriers_vehicle_details(): void
    {
        [$center, $branch] = $this->makeCenter('fleet.courier-edit@example.test');
        [$otherCenter] = $this->makeCenter('fleet.other-courier-edit@example.test');
        $courier = CourierRegistration::query()->create([
            'sorting_center_id' => $branch->id, 'first_name' => 'Jamie', 'last_name' => 'Reyes',
            'email' => 'edit.courier@example.test', 'phone' => '09170000009', 'vehicle' => null,
            'plate_number' => null, 'status' => 'approved', 'reviewed_at' => now(),
        ]);
        $assignedVehicle = SortingCenterVehicle::query()->create([
            'sorting_center_id' => $branch->id, 'courier_registration_id' => $courier->id,
            'name' => 'Jamie Reyes Van', 'type' => 'Van', 'plate_number' => 'OLD 1234', 'driver_name' => 'Jamie Reyes',
        ]);

        $this->actingAs($center)->patchJson('/api/v1/sorting_center/fleet/couriers/'.$courier->id, [
            'vehicle' => 'Truck', 'plate_number' => 'NEW 5678', 'first_name' => 'Changed',
        ])->assertOk()->assertJsonPath('data.vehicle', 'Truck')->assertJsonPath('data.plate_number', 'NEW 5678')
            ->assertJsonPath('vehicle.plate_number', 'NEW 5678')
            ->assertJsonPath('vehicle.id', $assignedVehicle->id)
            ->assertJsonPath('vehicle.driver_name', 'Jamie Reyes');

        $this->assertSame('Jamie', $courier->fresh()->first_name);
        $this->assertNull($courier->fresh()->vehicle);
        $this->assertNull($courier->fresh()->plate_number);
        $this->getJson('/api/v1/sorting_center/fleet/vehicles')->assertOk()
            ->assertJsonPath('data.0.plate_number', 'NEW 5678')
            ->assertJsonPath('data.0.driver_name', 'Jamie Reyes');
        $this->actingAs($otherCenter)->patchJson('/api/v1/sorting_center/fleet/couriers/'.$courier->id, [
            'vehicle' => 'Van', 'plate_number' => 'NOPE 0000',
        ])->assertNotFound();
    }

    public function test_another_center_cannot_read_or_archive_fleet_records(): void
    {
        [, $firstBranch] = $this->makeCenter('fleet.owner@example.test');
        [$otherCenter] = $this->makeCenter('fleet.other@example.test');
        $vehicle = SortingCenterVehicle::query()->create([
            'sorting_center_id' => $firstBranch->id, 'name' => 'Private Truck', 'type' => 'Truck', 'plate_number' => 'OWN 1234',
        ]);

        $this->actingAs($otherCenter)->getJson('/api/v1/sorting_center/fleet/vehicles')->assertOk()->assertJsonCount(0, 'data');
        $this->postJson('/api/v1/sorting_center/fleet/vehicles/'.$vehicle->id.'/archive', ['reason' => 'Unauthorized'])
            ->assertNotFound();
    }

    public function test_vehicles_and_drivers_page_renders_center_scoped_backend_data(): void
    {
        [$center, $branch] = $this->makeCenter('fleet.page@example.test');
        SortingCenterVehicle::query()->create([
            'sorting_center_id' => $branch->id, 'name' => 'Page Test Truck', 'type' => 'Truck', 'plate_number' => 'PAGE 1234',
        ]);

        $this->actingAs($center)->get(route('sorting-center.vehicles-drivers'))
            ->assertOk()->assertSee('Page Test Truck')->assertSee('PAGE 1234');
    }

    public function test_vehicles_page_does_not_create_trucks_from_courier_registration_details(): void
    {
        [$center, $branch] = $this->makeCenter('fleet.reconcile@example.test');
        CourierRegistration::query()->create([
            'sorting_center_id' => $branch->id, 'first_name' => 'Andrea', 'middle_name' => 'Mae',
            'last_name' => 'Santos', 'email' => 'andrea.reconcile@example.test', 'phone' => '09170000010',
            'vehicle' => 'Truck', 'plate_number' => 'MNL 1234', 'status' => 'approved', 'reviewed_at' => now(),
        ]);

        $this->actingAs($center)->getJson('/api/v1/sorting_center/fleet/vehicles')
            ->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/sorting_center/fleet/approved-couriers')->assertOk()
            ->assertJsonPath('data.0.vehicle', null)->assertJsonPath('data.0.plate_number', null);
        $this->assertDatabaseMissing('sorting_center_vehicles', [
            'sorting_center_id' => $branch->id,
            'plate_number' => 'MNL 1234',
        ]);
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
