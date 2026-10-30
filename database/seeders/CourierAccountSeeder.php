<?php

namespace Database\Seeders;

use App\Models\Logistics\LogisticsBranch;
use App\Models\Rider\Rider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CourierAccountSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('COURIER_SEED_PASSWORD', 'Courier@123');

        $user = User::updateOrCreate(
            ['email' => 'courier.demo@shopease.test'],
            [
                'name' => 'Demo Courier',
                'password' => Hash::make($password),
                'role' => User::ROLE_RIDER,
                'email_verified_at' => now(),
                'registration_status' => 'active',
                'approved_at' => now(),
                'rejected_at' => null,
            ]
        );

        $branch = LogisticsBranch::query()
            ->whereHas('logistics', fn ($query) => $query->where('registration_status', 'active'))
            ->orderBy('id')
            ->first();

        Rider::updateOrCreate(
            ['user_id' => $user->id],
            [
                'logistics_branch_id' => $branch?->id,
                'first_name' => 'Demo',
                'last_name' => 'Courier',
                'sex' => 'other',
                'contact_no' => '09170000002',
                'birthday' => '1995-01-01',
                'age' => 31,
                'province' => 'Laguna',
                'municipality' => 'Calamba',
                'barangay' => 'Demo',
                'street' => 'Courier Demo Street',
                'house_number' => '2',
                'vehicle' => 'Truck',
                'registration_type' => 'courier',
                'assignment_region' => 'Calabarzon',
                'assignment_province' => 'Laguna',
                'plate_number' => 'COURIER-0001',
                'courier_company' => $branch?->logistics?->business_name,
                'registration_status' => 'active',
                'approved_at' => now(),
                'rejected_at' => null,
            ]
        );

        $this->command?->info('Courier demo account courier.demo@shopease.test is ready.');
    }
}
