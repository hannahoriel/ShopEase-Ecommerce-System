<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use Tests\TestCase;

class SellerNotificationPreferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_disabled_category_blocks_new_database_notifications(): void
    {
        $seller = User::factory()->create(['role' => User::ROLE_SELLER]);
        $preferences = [
            'new_orders' => false,
            'order_updates' => true,
            'messages' => true,
            'reviews' => true,
            'compliance' => true,
            'email_alerts' => true,
            'announcements' => true,
            'promotions' => false,
        ];

        $this->actingAs($seller)
            ->postJson('/seller/account/settings/notifications', ['preferences' => $preferences])
            ->assertOk();

        $seller->notify(new class extends Notification
        {
            public function via(object $notifiable): array
            {
                return ['database'];
            }

            public function toArray(object $notifiable): array
            {
                return ['type' => 'new_order', 'title' => 'New order'];
            }
        });

        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_enabled_category_allows_new_database_notifications(): void
    {
        $seller = User::factory()->create(['role' => User::ROLE_SELLER]);

        $seller->notify(new class extends Notification
        {
            public function via(object $notifiable): array
            {
                return ['database'];
            }

            public function toArray(object $notifiable): array
            {
                return ['type' => 'new_order', 'title' => 'New order'];
            }
        });

        $this->assertDatabaseCount('notifications', 1);
    }
}
