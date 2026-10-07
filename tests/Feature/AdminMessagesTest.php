<?php

namespace Tests\Feature;

use App\Models\Admin\AdminConversation;
use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Seller\Seller;
use App\Models\User;
use Database\Seeders\AdminMessagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_reply_to_a_buyer_conversation(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $conversationId = $this->actingAs($admin)
            ->postJson('/admin/messages/api/conversations', [
                'type' => AdminConversation::CATEGORY_BUYERS,
                'user_id' => $buyer->id,
            ])
            ->assertCreated()
            ->json('data.id');

        $this->postJson("/admin/messages/api/conversations/{$conversationId}/messages", [
            'body' => 'How can we help?',
            'attachment' => UploadedFile::fake()->image('details.png'),
        ])
            ->assertCreated()
            ->assertJsonPath('data.sender', 'admin')
            ->assertJsonPath('data.text', 'How can we help?')
            ->assertJsonPath('data.attachment.name', 'details.png');

        $this->getJson('/admin/messages/api?type=buyers')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', $buyer->name)
            ->assertJsonPath('data.0.messages.0.text', 'How can we help?');

        $this->assertDatabaseHas('admin_messages', [
            'conversation_id' => $conversationId,
            'sender_id' => $admin->id,
            'body' => 'How can we help?',
            'attachment_name' => 'details.png',
        ]);
    }

    public function test_admin_complaint_threads_keep_buyer_and_seller_messages_separate(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create(['user_id' => $sellerUser->id, 'business_name' => 'Demo Store']);
        $order = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'total' => 100,
            'status' => 'pending',
        ]);
        $complaint = Complaint::create([
            'user_id' => $buyer->id,
            'order_id' => $order->id,
            'subject' => 'Item issue',
            'description' => 'The delivered item was damaged.',
            'status' => 'open',
        ]);

        $this->actingAs($admin)
            ->postJson('/admin/messages/api/conversations', [
                'type' => AdminConversation::CATEGORY_COMPLAINTS,
                'user_id' => $buyer->id,
                'complaint_id' => $complaint->id,
                'complaint_party' => 'buyer',
            ])
            ->assertCreated();

        $this->getJson('/admin/messages/api?type=complaints')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.parties.buyer.name', $buyer->name)
            ->assertJsonPath('data.0.parties.seller.name', $sellerUser->name)
            ->assertJsonPath('data.0.parties.buyer.conversationId', fn ($id) => is_int($id))
            ->assertJsonPath('data.0.parties.seller.conversationId', fn ($id) => is_int($id));

        $this->assertDatabaseCount('admin_conversations', 2);
    }

    public function test_admin_message_page_and_endpoints_reject_non_admin_users(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->get('/admin/messages')
            ->assertForbidden();

        $this->actingAs($buyer)
            ->getJson('/admin/messages/api')
            ->assertForbidden();
    }

    public function test_admin_messages_seeder_populates_each_ui_category_and_is_repeatable(): void
    {
        $this->seed(AdminMessagesSeeder::class);
        $this->seed(AdminMessagesSeeder::class);

        $this->assertDatabaseCount('admin_conversations', 5);
        $this->assertDatabaseCount('admin_messages', 10);

        $admin = User::query()->where('role', User::ROLE_ADMIN)->firstOrFail();
        $this->actingAs($admin)
            ->getJson('/admin/messages/api')
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonPath('counts.logistics', 1)
            ->assertJsonPath('counts.buyers', 1)
            ->assertJsonPath('counts.sellers', 1)
            ->assertJsonPath('counts.complaints', 2);

        $this->getJson('/admin/messages/api?type=complaints')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.parties.buyer.name', 'Juan Dela Cruz')
            ->assertJsonPath('data.0.parties.seller.name', 'Tech Haven');
    }
}
