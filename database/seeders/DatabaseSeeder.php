<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ([
    ['name' => 'Admin User', 'email' => 'admin@gmail.com', 'role' => User::ROLE_ADMIN, 'password' => 'Admin@123'],
    ['name' => 'Buyer User', 'email' => 'buyer@shopease.test', 'role' => User::ROLE_BUYER, 'password' => 'password'],
    ['name' => 'Seller User', 'email' => 'seller@shopease.test', 'role' => User::ROLE_SELLER, 'password' => 'seller123'],
    ['name' => 'Logistics User', 'email' => 'logistics@shopease.test', 'role' => User::ROLE_LOGISTICS, 'password' => 'password'],
    ['name' => 'Rider User', 'email' => 'rider@shopease.test', 'role' => User::ROLE_RIDER, 'password' => 'password'],
] as $account) {
    User::factory()->create($account);
}
    }
}
