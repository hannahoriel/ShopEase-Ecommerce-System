<?php

namespace Tests\Feature;

use App\Models\Admin\Announcement;
use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Seller\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_displays_calculated_metrics(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-04 12:00:00'));

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $pendingBuyer = User::factory()->create(['role' => User::ROLE_BUYER, 'registration_status' => 'pending']);
        $pendingSeller = User::factory()->create(['role' => User::ROLE_SELLER, 'registration_status' => 'pending']);
        $activeBuyer = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'registration_status' => 'active',
            'approved_at' => now(),
        ]);
        $sellerUser = User::factory()->create([
            'role' => User::ROLE_SELLER,
            'registration_status' => 'active',
            'approved_at' => now(),
        ]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Test Store',
            'registration_status' => 'active',
            'updated_at' => now(),
        ]);

        Order::create([
            'buyer_id' => $activeBuyer->id,
            'seller_id' => $seller->id,
            'total' => 100,
            'commission_amount' => 10,
            'status' => 'completed',
        ]);
        Order::create([
            'buyer_id' => $activeBuyer->id,
            'seller_id' => $seller->id,
            'total' => 50,
            'commission_amount' => 5,
            'status' => 'pending',
        ]);
        Order::create([
            'buyer_id' => $activeBuyer->id,
            'seller_id' => $seller->id,
            'total' => 20,
            'commission_amount' => 2,
            'status' => 'refunded',
        ]);

        Complaint::create([
            'user_id' => $activeBuyer->id,
            'subject' => 'Test complaint',
            'description' => 'Test complaint description',
            'status' => 'open',
        ]);
        foreach (range(1, 2) as $complaintNumber) {
            Complaint::create([
                'user_id' => $activeBuyer->id,
                'subject' => "Resolved complaint $complaintNumber",
                'description' => 'Resolved complaint description',
                'status' => 'resolved',
            ]);
        }
        Announcement::create([
            'title' => 'Test Announcement',
            'body' => 'Dashboard test announcement',
            'badge_label' => 'Test',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertViewIs('pages.admin.Dashboard')
            ->assertViewHas('salesSummary', [
                'gross_sales' => '₱100.00',
                'total_orders' => '3',
                'average_order_value' => '₱100.00',
                'completed_orders' => '1',
                'returns_refunds' => '1',
            ])
            ->assertViewHas('complaints', ['open' => 1, 'in_progress' => 0, 'resolved' => 2])
            ->assertViewHas('pendingBreakdown', ['sellers' => 1, 'buyers' => 1])
            ->assertViewHas('announcementMonth', 'September 2026')
            ->assertViewHas('announcement', fn (Announcement $announcement) => $announcement->title === 'Test Announcement')
            ->assertSee('₱100.00')
            ->assertSee('Test Announcement')
            ->assertSee('Dashboard test announcement');
    }
}
