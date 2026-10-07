<?php

namespace Tests\Feature;

use App\Models\Admin\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPlatformSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_settings_are_restricted_to_admins(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->get('/admin/platform-settings')
            ->assertForbidden();

        $this->actingAs($buyer)
            ->getJson('/admin/platform-settings/api')
            ->assertForbidden();
    }

    public function test_admin_can_create_schedule_and_delete_announcements(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->travelTo(now()->startOfDay());

        $created = $this->actingAs($admin)->postJson('/admin/platform-settings/api/announcements', [
            'title' => 'Delivery update',
            'description' => 'New delivery hours are now in effect.',
            'type' => 'Announcement',
            'audience' => 'Buyers',
            'status' => 'Scheduled',
            'publish_date' => now()->addDay()->toDateString(),
            'publish_time' => '09:30',
        ])->assertCreated()
            ->assertJsonPath('announcement.status', 'Scheduled')
            ->assertJsonPath('announcement.audience', 'Buyers');

        $announcementId = $created->json('announcement.id');

        $this->assertDatabaseHas('announcements', [
            'id' => $announcementId,
            'audience' => 'Buyers',
            'created_by' => $admin->id,
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->postJson("/admin/platform-settings/api/announcements/{$announcementId}", [
                '_method' => 'PATCH',
                'title' => 'Updated delivery update',
                'description' => 'Updated announcement copy.',
                'type' => 'Announcement',
                'audience' => 'Sellers',
                'status' => 'Published',
            ])
            ->assertOk()
            ->assertJsonPath('announcement.title', 'Updated delivery update')
            ->assertJsonPath('announcement.status', 'Published');

        $this->actingAs($admin)
            ->deleteJson("/admin/platform-settings/api/announcements/{$announcementId}")
            ->assertOk();

        $this->assertDatabaseMissing('announcements', ['id' => $announcementId]);
    }

    public function test_admin_can_upload_an_announcement_banner(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post('/admin/platform-settings/api/announcements', [
            'title' => 'Banner announcement',
            'description' => 'Announcement with a cover image.',
            'type' => 'Announcement',
            'audience' => 'All Users',
            'status' => 'Published',
            'banner' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $response->assertCreated();
        $bannerPath = Announcement::firstOrFail()->banner_path;

        $this->assertNotEmpty($bannerPath);
        Storage::disk('public')->assertExists($bannerPath);
    }

    public function test_due_scheduled_announcements_are_published_and_visible_in_settings(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $announcement = Announcement::create([
            'title' => 'Past scheduled notice',
            'type' => 'Announcement',
            'body' => 'This should now be visible.',
            'badge_label' => 'Announcement',
            'audience' => 'All Users',
            'status' => 'Scheduled',
            'published_at' => now()->subMinute(),
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->getJson('/admin/platform-settings/api')
            ->assertOk()
            ->assertJsonPath('announcements.0.status', 'Published');

        $this->actingAs($admin)
            ->get('/admin/platform-settings')
            ->assertOk()
            ->assertSee('Past scheduled notice');

        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'status' => 'Published',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_edit_and_delete_platform_policies(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $payload = [
            'title' => 'Seller Policy',
            'category' => 'Seller Policy',
            'description' => 'Updated seller standards.',
            'content' => 'Sellers must provide accurate product details.',
            'status' => 'Scheduled',
            'publish_at' => now()->addDay()->toDateTimeString(),
        ];

        $created = $this->actingAs($admin)
            ->postJson('/admin/platform-settings/api/policies', $payload)
            ->assertCreated()
            ->assertJsonPath('policy.status', 'Scheduled');

        $policyId = $created->json('policy.id');

        $this->assertDatabaseHas('platform_policies', [
            'id' => $policyId,
            'created_by' => $admin->id,
        ]);

        $payload['title'] = 'Updated Seller Policy';

        $this->actingAs($admin)
            ->patchJson("/admin/platform-settings/api/policies/{$policyId}", $payload)
            ->assertOk()
            ->assertJsonPath('policy.title', 'Updated Seller Policy');

        $this->actingAs($admin)
            ->deleteJson("/admin/platform-settings/api/policies/{$policyId}")
            ->assertOk();

        $this->assertDatabaseMissing('platform_policies', ['id' => $policyId]);

        unset($payload['publish_at']);
        $payload['status'] = 'Published';

        $this->actingAs($admin)
            ->postJson('/admin/platform-settings/api/policies', $payload)
            ->assertCreated()
            ->assertJsonPath('policy.status', 'Published');
    }

    public function test_announcement_and_policy_validation_matches_the_ui_limits(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->postJson('/admin/platform-settings/api/announcements', [
                'title' => str_repeat('a', 81),
                'description' => 'Description',
                'type' => 'Announcement',
                'audience' => 'Buyers',
                'status' => 'Published',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('title');

        $this->actingAs($admin)
            ->postJson('/admin/platform-settings/api/announcements', [
                'title' => 'Scheduled notice',
                'description' => 'Description',
                'type' => 'Announcement',
                'audience' => 'Buyers',
                'status' => 'Scheduled',
                'publish_date' => now()->subDay()->toDateString(),
                'publish_time' => '09:30',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('publish_date');

        $this->actingAs($admin)
            ->postJson('/admin/platform-settings/api/policies', [
                'title' => 'Policy',
                'category' => 'Unknown',
                'description' => 'Description',
                'content' => 'Content',
                'status' => 'Published',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('category');

        $this->actingAs($admin)
            ->postJson('/admin/platform-settings/api/policies', [
                'title' => 'Scheduled policy',
                'category' => 'Seller Policy',
                'description' => 'Description',
                'content' => 'Content',
                'status' => 'Scheduled',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('publish_at');
    }
}
