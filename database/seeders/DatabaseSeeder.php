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
        // RiderAccountSeeder is a dedicated demo fixture and is intentionally
        // excluded from the default seed run so real rider registrations stay
        // separate from seeded operational accounts.
        $this->call(CourierAccountSeeder::class);
        $this->call(EcommerceRouteDemoSeeder::class);
        $this->call(HubPickupAndDeliveryRiderSeeder::class);
        $this->call(BulacanRouteDemoSeeder::class);
        $this->call(AdminMessagesSeeder::class);
        $this->call(LogisticsMessagesSeeder::class);
        $this->call(LogisticsAccountSeeder::class);
        $this->call(DeliveryHubScannerSeeder::class);
        $this->call(SellerVoucherSeeder::class);
    }
}
