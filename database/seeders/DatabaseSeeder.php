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
            ['name' => 'Admin User', 'email' => 'admin@shopease.test', 'role' => User::ROLE_ADMIN],
            ['name' => 'Buyer User', 'email' => 'buyer@shopease.test', 'role' => User::ROLE_BUYER],
            ['name' => 'Seller User', 'email' => 'seller@shopease.test', 'role' => User::ROLE_SELLER],
            ['name' => 'Logistics User', 'email' => 'logistics@shopease.test', 'role' => User::ROLE_LOGISTICS],
            ['name' => 'Rider User', 'email' => 'rider@shopease.test', 'role' => User::ROLE_RIDER],
        ] as $account) {
            User::factory()->create($account + ['password' => 'password']);
        }
    }
}
