<?php

namespace Database\Seeders;

use App\Models\Admin\Admin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = [
            'first_name' => 'Shop',
            'middle_name' => null,
            'last_name' => 'Ease',
            'phone_number' => '0912345678',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin@123'),
            'profile_picture' => null,
        ];

        Admin::updateOrCreate(
            ['email' => $admin['email']],
            $admin
        );

        User::updateOrCreate(
            ['email' => $admin['email']],
            [
                'name' => 'Shop Ease',
                'first_name' => $admin['first_name'],
                'last_name' => $admin['last_name'],
                'contact_no' => $admin['phone_number'],
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make('Admin@123'),
            ]
        );

        foreach ([
            [
                'name' => 'Buyer User',
                'email' => 'buyer@shopease.test',
                'role' => User::ROLE_BUYER,
                'password' => Hash::make('password')
            ],
            [
                'name' => 'Seller User',
                'email' => 'seller@shopease.test',
                'role' => User::ROLE_SELLER,
                'password' => Hash::make('seller123')
            ],
            [
                'name' => 'Logistics User',
                'email' => 'logistics@shopease.test',
                'role' => User::ROLE_LOGISTICS,
                'password' => Hash::make('password')
            ],
            [
                'name' => 'Rider User',
                'email' => 'rider@shopease.test',
                'role' => User::ROLE_RIDER,
                'password' => Hash::make('password')
            ],
        ] as $account) {

            User::updateOrCreate(
                ['email' => $account['email']],
                $account
            );
        }
    }
}