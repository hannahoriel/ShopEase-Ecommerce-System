<?php

namespace Tests\Feature;

use App\Models\Admin\Announcement;
use App\Models\Logistics\Logistics;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogisticsDashboardAnnouncementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_logistics_dashboard_rotates_only_shared_and_logistics_announcements(): void
    {
        $logisticsUser = User::factory()->create(['role' => User::ROLE_LOGISTICS]);
        Logistics::query()->create([
            'user_id' => $logisticsUser->id,
            'last_name' => 'Carrier',
            'first_name' => 'Demo',
            'sex' => 'other',
            'contact_no' => '09171234567',
            'birthday' => '1990-01-01',
            'age' => 36,
            'province' => 'Laguna',
            'municipality' => 'Calamba',
            'barangay' => 'Real',
            'street' => 'Main Street',
            'house_number' => '1',
            'business_name' => 'Demo Logistics',
            'registration_status' => 'active',
        ]);

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
            ->assertSee('id="lgAnnouncementNext"', false);
    }
}
