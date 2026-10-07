<?php

namespace Tests\Feature;

use App\Models\Admin\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogisticsDashboardAnnouncementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_logistics_dashboard_rotates_only_shared_and_logistics_announcements(): void
    {
        $logisticsUser = User::factory()->create(['role' => User::ROLE_LOGISTICS]);

        Announcement::create([
            'title' => 'Shared update',
            'body' => 'For all platform users.',
            'audience' => 'All Users',
            'status' => 'Published',
            'is_active' => true,
        ]);
        Announcement::create([
            'title' => 'Logistics update',
            'body' => 'For logistics users.',
            'audience' => 'Logistics',
            'status' => 'Published',
            'is_active' => true,
        ]);
        Announcement::create([
            'title' => 'Seller-only update',
            'body' => 'For sellers only.',
            'audience' => 'Sellers',
            'status' => 'Published',
            'is_active' => true,
        ]);
        Announcement::create([
            'title' => 'Buyer-only update',
            'body' => 'For buyers only.',
            'audience' => 'Buyers',
            'status' => 'Published',
            'is_active' => true,
        ]);

        $this->actingAs($logisticsUser)
            ->get(route('logistics.dashboard'))
            ->assertOk()
            ->assertSee('Shared update')
            ->assertSee('Logistics update')
            ->assertDontSee('Seller-only update')
            ->assertDontSee('Buyer-only update')
            ->assertSee('data-dashboard-announcement-next', false);
    }
}
