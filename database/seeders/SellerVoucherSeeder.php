<?php

namespace Database\Seeders;

use App\Models\Seller\Seller;
use App\Models\Seller\SellerVoucher;
use Illuminate\Database\Seeder;

class SellerVoucherSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('SellerVoucherSeeder is restricted to local and testing environments.');
        }

        foreach (Seller::where('registration_status', 'active')->get() as $seller) {
            SellerVoucher::firstOrCreate(
                ['seller_id' => $seller->id, 'code' => 'DEMO10'],
                [
                    'title' => 'Demo Shop Savings', 'discount_type' => 'percent', 'discount_value' => 10,
                    'minimum_spend' => 100, 'maximum_discount' => 100, 'eligible_buyers' => 'all',
                    'stacking' => 'none', 'quantity' => 100, 'claimed' => 0, 'per_buyer' => 2,
                    'starts_at' => now()->subDay(), 'ends_at' => now()->addMonths(2),
                    'terms' => 'Demo voucher for local and test checkout flows.', 'enabled' => true,
                ]
            );
            SellerVoucher::firstOrCreate(
                ['seller_id' => $seller->id, 'code' => 'DEMO50'],
                [
                    'title' => 'Demo ₱50 Off', 'discount_type' => 'fixed', 'discount_value' => 50,
                    'minimum_spend' => 250, 'maximum_discount' => 50, 'eligible_buyers' => 'all',
                    'stacking' => 'shipping', 'quantity' => 50, 'claimed' => 0, 'per_buyer' => 1,
                    'starts_at' => now()->subDay(), 'ends_at' => now()->addMonths(2),
                    'terms' => 'Demo fixed discount for local and test checkout flows.', 'enabled' => true,
                ]
            );
        }
    }
}
