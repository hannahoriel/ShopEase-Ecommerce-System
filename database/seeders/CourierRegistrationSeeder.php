<?php

namespace Database\Seeders;

use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\Logistics;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CourierRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $centers = LogisticsBranch::query()->whereHas('user', fn ($query) => $query->where('role', User::ROLE_SORTING_CENTER))->get();
        if ($centers->isEmpty()) {
            $company = Logistics::query()->where('registration_status', 'active')->first()
                ?? Logistics::query()->first();
            if (! $company) {
                $this->command?->warn('Courier registration demo data skipped: no logistics company is configured.');
                return;
            }
            $centerUser = User::query()->updateOrCreate(
                ['email' => 'sorting.center.demo@shopease.test'],
                ['name' => 'Demo Sorting Center', 'password' => Hash::make(env('SORTING_CENTER_SEED_PASSWORD', 'SortingCenter@123')),
                    'role' => User::ROLE_SORTING_CENTER, 'registration_status' => 'active', 'email_verified_at' => now()]
            );
            $centers = collect([LogisticsBranch::query()->updateOrCreate(
                ['user_id' => $centerUser->id],
                ['logistics_id' => $company->id, 'name' => 'Demo Sorting Center',
                    'address' => '12 Rizal Street, Calamba, Laguna', 'province' => 'Laguna',
                    'municipality' => 'Calamba', 'barangay' => 'Poblacion', 'postal_code' => '4027',
                    'contact_person' => 'Demo Sorting Center', 'phone' => '09170000010', 'status' => 'active']
            )]);
        }

        $fixtures = [
            ['Mark', 'Angelo', 'Reyes', 'mark.reyes@example.com', '09170000011', 'Santa Cruz', 'ABC 1234', 'pending', null, 4],
            ['Joshua', 'Miguel', 'Santos', 'joshua.santos@example.com', '09170000012', 'Pila', 'DEF 5678', 'pending', null, 3],
            ['Adrian', 'Paul', 'Mendoza', 'adrian.mendoza@example.com', '09170000013', 'Calamba', 'GHI 9012', 'approved', null, 7],
            ['Carlo', 'Vincent', 'Cruz', 'carlo.cruz@example.com', '09170000014', 'San Pablo', 'JKL 3456', 'rejected', 'Unclear or unreadable uploaded documents', 9],
        ];

        foreach ($centers as $center) {
            foreach ($fixtures as [$first, $middle, $last, $email, $phone, $city, $plate, $status, $reason, $daysAgo]) {
                CourierRegistration::query()->updateOrCreate(
                    ['sorting_center_id' => $center->id, 'email' => $email],
                    [
                        'first_name' => $first, 'middle_name' => $middle, 'last_name' => $last,
                        'sex' => 'Male', 'birthday' => '1998-03-15', 'phone' => $phone,
                        'region' => 'CALABARZON (Region IV-A)', 'province' => 'Laguna', 'city' => $city,
                        'barangay' => 'Poblacion', 'street' => 'Rizal Street', 'house_no' => '12',
                        'postal_code' => '4000', 'vehicle' => 'Truck', 'plate_number' => $plate,
                        'status' => $status, 'rejection_reason' => $reason,
                        'reviewed_by' => null, 'reviewed_at' => $status === 'pending' ? null : now()->subDays($daysAgo),
                        'created_at' => now()->subDays($daysAgo), 'updated_at' => now()->subDays($daysAgo),
                    ]
                );
            }
        }
        $this->command?->info('Courier registration fixtures are ready for configured sorting centers.');
    }
}
