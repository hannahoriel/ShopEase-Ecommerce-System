<?php

namespace Tests\Feature;

use App\Models\Admin\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerAnnouncementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_dashboard_announcements_show_only_active_published_buyer_audience_items(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        Announcement::create([
            'title' => 'Everyone notice',
            'type' => 'Announcement',
            'body' => 'Visible to every platform user.',
            'audience' => 'All Users',
            'status' => 'Published',
            'published_at' => now()->subMinute(),
            'is_active' => true,
        ]);

        Announcement::create([
            'title' => 'Buyer notice',
            'type' => 'Announcement',
            'body' => 'Visible to buyers.',
            'audience' => 'Buyers',
            'status' => 'Published',
            'published_at' => now()->subMinute(),
            'is_active' => true,
        ]);

        Announcement::create([
            'title' => 'Seller-only notice',
            'type' => 'Announcement',
            'body' => 'Not visible to buyers.',
            'audience' => 'Sellers',
            'status' => 'Published',
            'published_at' => now()->subMinute(),
            'is_active' => true,
        ]);

        Announcement::create([
            'title' => 'Logistics-only notice',
            'type' => 'Announcement',
            'body' => 'Not visible to buyers.',
            'audience' => 'Logistics',
            'status' => 'Published',
            'published_at' => now()->subMinute(),
            'is_active' => true,
        ]);

        Announcement::create([
            'title' => 'Future notice',
            'type' => 'Announcement',
            'body' => 'Not yet published.',
            'audience' => 'All Users',
            'status' => 'Scheduled',
            'published_at' => now()->addDay(),
            'is_active' => false,
        ]);

        $response = $this->actingAs($buyer)
            ->getJson('/api/v1/buyer/dashboard/announcements')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['title' => 'Everyone notice'])
            ->assertJsonFragment(['title' => 'Buyer notice'])
            ->assertJsonMissing(['title' => 'Seller-only notice'])
            ->assertJsonMissing(['title' => 'Logistics-only notice'])
            ->assertJsonMissing(['title' => 'Future notice']);

        $this->assertEqualsCanonicalizing(
            ['Everyone notice', 'Buyer notice'],
            collect($response->json('data'))->pluck('title')->all()
        );
    }

    public function test_due_scheduled_announcement_is_published_and_returned_to_buyers(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $announcement = Announcement::create([
            'title' => 'Newly published notice',
            'type' => 'Announcement',
            'body' => 'Its schedule has arrived.',
            'audience' => 'All Users',
            'status' => 'Scheduled',
            'published_at' => now()->subMinute(),
            'is_active' => false,
        ]);

        $this->actingAs($buyer)
            ->getJson('/api/v1/buyer/dashboard/announcements')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Newly published notice');

        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'status' => 'Published',
            'is_active' => true,
        ]);
    }

    public function test_non_buyers_cannot_read_buyer_dashboard_announcements(): void
    {
        $seller = User::factory()->create(['role' => User::ROLE_SELLER]);

        $this->actingAs($seller)
            ->getJson('/api/v1/buyer/dashboard/announcements')
            ->assertForbidden();
    }
}
