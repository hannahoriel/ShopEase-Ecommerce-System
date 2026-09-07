<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Complaint;
use App\Models\Order;
use App\Models\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DashboardDemoSeeder extends Seeder
{
    public function run(): void
    {
        // --- Buyers -------------------------------------------------
        $buyers = User::factory()
            ->count(60)
            ->state(function () {
                $createdAt = Carbon::now()->subDays(rand(0, 29));
                $isActive = rand(0, 100) > 15;

                return [
                    'role' => 'buyer',
                    'status' => $isActive ? 'active' : 'pending',
                    'approved_at' => $isActive ? $createdAt->copy()->addHours(rand(1, 48)) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            })
            ->create();

        // --- Sellers (users + seller profile) ------------------------
        $sellerUsers = User::factory()
            ->count(20)
            ->state(function () {
                $createdAt = Carbon::now()->subDays(rand(0, 29));
                $isActive = rand(0, 100) > 20;

                return [
                    'role' => 'seller',
                    'status' => $isActive ? 'active' : 'pending',
                    'approved_at' => $isActive ? $createdAt->copy()->addHours(rand(1, 48)) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            })
            ->create();

        $sellers = $sellerUsers->map(function (User $user) {
            return Seller::create([
                'user_id' => $user->id,
                'store_name' => $user->name . "'s Store",
                'commission_rate' => 5,
                'status' => $user->status,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);
        });

        $activeSellers = $sellers->where('status', 'active')->values();

        // --- Orders ---------------------------------------------------
        if ($activeSellers->isNotEmpty()) {
            foreach (range(1, 1256) as $i) {
                $createdAt = Carbon::now()->subDays(rand(0, 29))->subMinutes(rand(0, 1440));
                $status = collect(['completed', 'completed', 'completed', 'pending', 'refunded'])->random();
                $total = rand(5000, 50000) / 100; // ₱50.00–₱500.00
                $seller = $activeSellers->random();

                Order::create([
                    'buyer_id' => $buyers->random()->id,
                    'seller_id' => $seller->id,
                    'total' => $total,
                    'commission_amount' => round($total * ($seller->commission_rate / 100), 2),
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        // --- Complaints -------------------------------------------------
        $statuses = ['open' => 20, 'in_progress' => 10, 'resolved' => 45];

        foreach ($statuses as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                Complaint::create([
                    'user_id' => $buyers->random()->id,
                    'subject' => 'Issue with order #' . rand(1000, 9999),
                    'description' => 'Auto-generated demo complaint.',
                    'status' => $status,
                ]);
            }
        }

        // --- Announcement -------------------------------------------------
        Announcement::create([
            'title' => 'Augzu Sale 2026!',
            'body' => 'Abangan ang mga katangahan ngayong August',
            'badge_label' => 'August',
            'is_active' => true,
        ]);
    }
}
