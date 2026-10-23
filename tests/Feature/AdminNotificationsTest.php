<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\SellerCategoryChangeRequested;
use App\Notifications\SellerDocumentChangeRequested;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_navbar_shows_only_real_unread_notifications_and_links_to_all_notifications(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.notifications.index'))
            ->assertOk()
            ->assertDontSee('>4</span>', false)
            ->assertSee('You have no unread notifications.')
            ->assertSee('href="'.route('admin.notifications.index').'"', false);

        $admin->notify(new SellerCategoryChangeRequested(42, 'North Store'));
        $admin->notify(new SellerDocumentChangeRequested(17, 'West Store', 'replace_valid_id'));

        $this->get(route('admin.notifications.index'))
            ->assertOk()
            ->assertSee('aria-label="2 unread notifications"', false)
            ->assertSee('Seller Category Change Request')
            ->assertSee('Seller Document Change Request')
            ->assertSee('href="'.route('admin.notifications.index', ['filter' => 'unread']).'"', false);
    }

    public function test_admin_can_mark_notifications_read_and_unread_badge_disappears(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $admin->notify(new SellerCategoryChangeRequested(42, 'North Store'));
        $notification = $admin->unreadNotifications()->firstOrFail();

        $this->actingAs($admin)
            ->patchJson("/api/v1/admin/notifications/{$notification->id}/read")
            ->assertOk();

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'notifiable_id' => $admin->id,
        ]);
        $this->assertNotNull($notification->fresh()->read_at);

        $this->get(route('admin.notifications.index'))
            ->assertOk()
            ->assertDontSee('aria-label="1 unread notifications"', false)
            ->assertSee('No new notifications');
    }

    public function test_admin_notifications_api_lists_and_marks_items_read(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $admin->notify(new SellerCategoryChangeRequested(42, 'North Store'));
        $notification = $admin->unreadNotifications()->firstOrFail();

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/notifications?filter=unread')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('counts.unread', 1);

        $this->patchJson("/api/v1/admin/notifications/{$notification->id}/read")
            ->assertOk()
            ->assertJsonPath('message', 'Notification marked as read.');

        $this->patchJson('/api/v1/admin/notifications/read-all')
            ->assertOk()
            ->assertJsonPath('message', 'All notifications marked as read.');
    }

    public function test_non_admin_cannot_open_admin_notifications(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->get(route('admin.notifications.index'))
            ->assertForbidden();
    }

    public function test_admin_notification_preferences_control_database_notification_delivery(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        $this->postJson('/api/v1/admin/account/notifications', [
            'preferences' => [
                'seller_compliance' => '0',
                'registrations' => '1',
                'complaints' => '1',
                'commissions' => '1',
                'logistics' => '1',
                'messages' => '1',
                'platform' => '1',
                'reports' => '1',
            ],
        ])->assertOk();

        $admin->notify(new SellerCategoryChangeRequested(42, 'North Store'));
        $this->assertDatabaseCount('notifications', 0);

        $this->postJson('/api/v1/admin/account/notifications', [
            'preferences' => [
                'seller_compliance' => '1',
                'registrations' => '1',
                'complaints' => '1',
                'commissions' => '1',
                'logistics' => '1',
                'messages' => '1',
                'platform' => '1',
                'reports' => '1',
            ],
        ])->assertOk();

        $admin->notify(new SellerCategoryChangeRequested(43, 'South Store'));
        $this->assertDatabaseCount('notifications', 1);
    }
}
