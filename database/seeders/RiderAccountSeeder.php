<?php

namespace Database\Seeders;

use App\Models\Logistics\LogisticsBranch;
use App\Models\Rider\Rider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class RiderAccountSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('RIDER_SEED_PASSWORD');
        if (! is_string($password) || $password === '') {
            throw new RuntimeException('Set RIDER_SEED_PASSWORD before running RiderAccountSeeder.');
        }

        $user = User::updateOrCreate(
            ['email' => 'rider@gmail.com'],
            [
                'name' => 'ShopEase Rider',
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
                'first_name' => 'ShopEase',
                'last_name' => 'Rider',
                'sex' => 'other',
                'contact_no' => '09170000000',
                'birthday' => '1995-01-01',
                'age' => 31,
                'province' => 'Metro Manila',
                'municipality' => 'Quezon City',
                'barangay' => 'Demo',
                'street' => 'Rider Account Street',
                'house_number' => '1',
                'vehicle' => 'Motorcycle',
                'plate_number' => 'DEMO-0001',
                'registration_status' => 'active',
                'approved_at' => now(),
                'rejected_at' => null,
            ]
        );

        $this->command?->info('Rider login account rider@gmail.com is ready.');
    }
}
