<?php

namespace Database\Seeders;

use App\Models\Logistics\DeliveryHubRiderRegistration;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\Rider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HubPickupAndDeliveryRiderSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('HubPickupAndDeliveryRiderSeeder is restricted to local and testing environments.');
        }

        $password = Hash::make(env('HUB_RIDER_DEMO_PASSWORD', 'Demo@12345'));
        $calambaHub = LogisticsDeliveryHub::query()->whereRaw('LOWER(hub_name) = ?', ['calamba delivery hub'])
            ->where('status', 'active')->whereNull('archived_at')->firstOrFail();
        $manilaHub = LogisticsDeliveryHub::query()->whereRaw('LOWER(hub_name) = ?', ['manila delivery hub'])
            ->where('status', 'active')->whereNull('archived_at')->firstOrFail();

        $this->seedRider($calambaHub, 'rider.pickup.calamba.hub@shopease.test', 'Nico', 'Garcia', 'CALAMBA-PICKUP-01', 'pickup', 'Bonifacio', $password);
        $this->seedRider($manilaHub, 'rider.delivery.manila.hub@shopease.test', 'Mara', 'Santos', 'MANILA-DELIVERY-01', 'delivery', 'Intramuros', $password);

        $this->command?->info('Calamba pickup rider and Manila delivery rider are active and registered to their delivery hubs.');
        $this->command?->info('Demo riders share HUB_RIDER_DEMO_PASSWORD (default: Demo@12345).');
    }

    private function seedRider(
        LogisticsDeliveryHub $hub,
        string $email,
        string $firstName,
        string $lastName,
        string $plate,
        string $operation,
        string $assignedBarangay,
        string $password,
    ): void {
        $user = User::query()->updateOrCreate(['email' => $email], [
            'name' => $firstName.' '.$lastName,
            'role' => User::ROLE_RIDER,
            'password' => $password,
            'email_verified_at' => now(),
            'registration_status' => 'active',
            'approved_at' => now(),
            'rejected_at' => null,
        ]);

        Rider::query()->updateOrCreate(['user_id' => $user->id], [
            'logistics_branch_id' => $hub->logistics_branch_id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'sex' => 'other',
            'contact_no' => '09170002001',
            'birthday' => '1995-01-01',
            'age' => 31,
            'province' => $hub->province,
            'municipality' => $hub->municipality,
            'barangay' => $assignedBarangay,
            'street' => 'Rider Service Road',
            'house_number' => '1',
            'vehicle' => 'Motorcycle',
            'registration_type' => 'delivery_hub',
            'assignment_region' => $hub->province === 'Laguna' ? 'Calabarzon' : 'NCR',
            'assignment_province' => $hub->province,
            'assignment_barangay' => $assignedBarangay,
            'plate_number' => $plate,
            'municipal_hub' => $hub->hub_name,
            'logistics_operation' => $operation,
            'courier_company' => $hub->logisticsBranch?->logistics?->business_name,
            'registration_status' => 'active',
            'approved_at' => now(),
            'rejected_at' => null,
        ]);

        DeliveryHubRiderRegistration::query()->updateOrCreate(
            ['user_id' => $user->id],
            ['logistics_delivery_hub_id' => $hub->id, 'status' => 'approved', 'reviewed_at' => now(), 'rejection_reason' => null],
        );
    }
}
