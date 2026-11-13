<?php

namespace Tests\Feature;

use App\Models\Admin\Announcement;
use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Admin\Registration;
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

        $admin = $this->primaryAdmin();
        $pendingBuyer = User::factory()->create(['role' => User::ROLE_BUYER, 'registration_status' => 'pending']);
        $pendingSeller = User::factory()->create(['role' => User::ROLE_SELLER, 'registration_status' => 'pending']);
        Registration::factory()->create([
            'user_id' => $pendingBuyer->id,
            'user_type' => User::ROLE_BUYER,
            'status' => 'pending',
        ]);
        Registration::factory()->create([
            'user_id' => $pendingSeller->id,
            'user_type' => User::ROLE_SELLER,
            'status' => 'pending',
        ]);
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
        Announcement::create([
            'title' => 'Seller Dashboard Announcement',
            'body' => 'Relevant to seller dashboard.',
            'audience' => 'Sellers',
            'status' => 'Published',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/dashboard')
            ->assertOk()
            ->assertJsonPath('sales_summary.gross_sales', 100)
            ->assertJsonPath('sales_summary.total_orders', 3)
            ->assertJsonPath('sales_summary.average_order_value', 100)
            ->assertJsonPath('sales_summary.completed_orders', 1)
            ->assertJsonPath('sales_summary.returns_refunds', 1)
            ->assertJsonPath('complaints.open', 1)
            ->assertJsonPath('complaints.in_progress', 0)
            ->assertJsonPath('complaints.resolved', 2)
            ->assertJsonPath('pending_breakdown.sellers', 1)
            ->assertJsonPath('pending_breakdown.buyers', 1);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('₱100.00')
            ->assertSee('Test Announcement')
            ->assertSee('Dashboard test announcement')
            ->assertSee('Seller Dashboard Announcement')
            ->assertSee('dashboard-announcement-card bg-maroon-900', false)
            ->assertSee('data-dashboard-announcement-carousel', false);
    }
}
