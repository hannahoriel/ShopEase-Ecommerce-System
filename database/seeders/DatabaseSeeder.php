<?php

namespace Database\Seeders;

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
        // Keep local databases focused on real registrations and transactions.
        // These are the only intentionally seeded operational accounts.
        $this->call(RiderAccountSeeder::class);
        $this->call(CourierAccountSeeder::class);
        $this->call(AdminMessagesSeeder::class);
        $this->call(LogisticsMessagesSeeder::class);
        $this->call(LogisticsAccountSeeder::class);
        $this->call(SellerVoucherSeeder::class);
    }
}
