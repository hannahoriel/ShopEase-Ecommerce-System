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
        $this->call(SellerMessagesSeeder::class);
        $this->call(LogisticsManagementSeeder::class);
        $this->call(AdminMessagesSeeder::class);
    }
}
