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
        $this->call(DemoMarketplaceSeeder::class);
        $this->call(ProductReviewSeeder::class);
        $this->call(SellerOrderStatusSeeder::class);
        $this->call(SellerShippingStatusSeeder::class);
        $this->call(SellerReportsSeeder::class);
        $this->call(LogisticsManagementSeeder::class);
        $this->call(CourierAccountSeeder::class);
        $this->call(CourierRegistrationSeeder::class);
        $this->call(SortingCenterFleetSeeder::class);
        $this->call(AdminMessagesSeeder::class);
        $this->call(LogisticsMessagesSeeder::class);
        $this->call(LogisticsAccountSeeder::class);
        $this->call(SellerVoucherSeeder::class);
    }
}
