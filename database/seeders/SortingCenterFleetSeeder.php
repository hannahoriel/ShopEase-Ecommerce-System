<?php

namespace Database\Seeders;

use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\SortingCenterVehicle;
use App\Models\User;
use Illuminate\Database\Seeder;

class SortingCenterFleetSeeder extends Seeder
{
    public function run(): void
    {
        $fixtures = [
            ['Isuzu Forward 6-Wheeler', 'Truck', 'SC-TRK-001', 'Isuzu', 'FRR 90', 2022, '5 tons', 'Adrian Paul Mendoza', 'Available', 'White', 'Sorting center linehaul truck'],
            ['Hino 500 Series', 'Truck', 'SC-TRK-002', 'Hino', '500 Series', 2023, '8 tons', 'Joshua Miguel Santos', 'In Service', 'White', 'Transfers to assigned delivery hubs'],
            ['Fuso Canter', 'Truck', 'SC-TRK-003', 'Fuso', 'Canter', 2021, '4 tons', null, 'Available', 'Silver', 'Backup fleet unit'],
        ];

        foreach (LogisticsBranch::query()->whereHas('user', fn ($query) => $query->where('role', User::ROLE_SORTING_CENTER))->get() as $center) {
            foreach ($fixtures as [$name, $type, $plate, $make, $model, $year, $capacity, $driver, $availability, $color, $notes]) {
                SortingCenterVehicle::query()->updateOrCreate(
                    ['sorting_center_id' => $center->id, 'plate_number' => $plate],
                    [
                        'name' => $name, 'type' => $type, 'make' => $make, 'model' => $model,
                        'year' => $year, 'capacity' => $capacity, 'driver_name' => $driver,
                        'registration_date' => now()->subDays(14), 'availability' => $availability,
                        'color' => $color, 'notes' => $notes,
                    ]
                );
            }
        }
        $this->command?->info('Sorting-center fleet demo vehicles are ready.');
    }
}
