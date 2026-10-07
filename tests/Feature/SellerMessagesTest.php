<?php

namespace Tests\Feature;

use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\Seller\SellerConversation;
use App\Models\Seller\SellerMessage;
use App\Models\User;
use Database\Seeders\SellerMessagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_list_open_and_reply_to_their_buyer_and_complaint_conversations(): void
    {
        $this->seed(SellerMessagesSeeder::class);
        $sellerUser = User::query()->where('email', 'seller@shopease.test')->firstOrFail();

        $buyerThreads = $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/messages?type=buyers')
            ->assertOk()
            ->assertJsonPath('counts.buyers', 3)
            ->assertJsonPath('unread.buyers', 4)
            ->assertJsonCount(3, 'data');
        $conversation = SellerConversation::query()
            ->where('seed_key', 'seller-messages-buyer-1')
            ->firstOrFail();

        $this->getJson("/api/v1/seller/messages/conversations/{$conversation->id}")
            ->assertOk()
            ->assertJsonPath('data.type', 'buyers')
            ->assertJsonPath('data.title', 'Juan Dela Cruz')
            ->assertJsonPath('data.context_label', 'Related Order')
            ->assertJsonPath('data.messages.0.sender', 'buyer');

        $this->assertDatabaseMissing('seller_messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $conversation->buyer_id,
            'read_at' => null,
        ]);

        $this->postJson("/api/v1/seller/messages/conversations/{$conversation->id}/messages", [
            'body' => 'I can confirm this item is available.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.sender', 'seller')
            ->assertJsonPath('data.text', 'I can confirm this item is available.');

        $this->getJson('/api/v1/seller/messages?type=complaints')
            ->assertOk()
            ->assertJsonPath('counts.complaints', 2)
            ->assertJsonCount(2, 'data');

        $complaintConversation = SellerConversation::query()
            ->where('seed_key', 'seller-messages-complaint-1')
            ->firstOrFail();
        $this->getJson("/api/v1/seller/messages/conversations/{$complaintConversation->id}")
            ->assertOk()
            ->assertJsonPath('data.type', 'complaints')
            ->assertJsonPath('data.badge', 'Admin')
            ->assertJsonPath('data.messages.0.sender', 'admin');

        $this->assertSame(14, SellerMessage::query()->count());
        $this->assertNotEmpty($buyerThreads->json('data'));
    }

    public function test_seller_can_start_conversations_only_from_their_own_buyer_orders(): void
    {
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Messaging Seller',
            'registration_status' => 'active',
        ]);
        $otherSellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $otherSeller = Seller::create([
            'user_id' => $otherSellerUser->id,
            'store_name' => 'Other Messaging Seller',
            'registration_status' => 'active',
        ]);
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $ownOrder = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_number' => 'MSG-OWN-ORDER',
            'total' => 300,
            'status' => 'new',
        ]);
        $otherOrder = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $otherSeller->id,
            'order_number' => 'MSG-OTHER-ORDER',
            'total' => 300,
            'status' => 'new',
        ]);

        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/messages/contacts')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.order_id', $ownOrder->id);

        $response = $this->postJson('/api/v1/seller/messages/conversations', [
            'order_id' => $ownOrder->id,
        ])->assertCreated()
            ->assertJsonPath('data.title', $buyer->name);

        $this->postJson('/api/v1/seller/messages/conversations', [
            'order_id' => $ownOrder->id,
        ])->assertOk()
            ->assertJsonPath('data.id', $response->json('data.id'));

        $this->postJson('/api/v1/seller/messages/conversations', [
            'order_id' => $otherOrder->id,
        ])->assertNotFound();

        $this->postJson('/api/v1/seller/messages/conversations/'.$response->json('data.id').'/messages', [
            'body' => '   ',
        ])->assertUnprocessable();
    }

    public function test_message_endpoints_reject_non_sellers_and_other_sellers_conversations(): void
    {
        $this->seed(SellerMessagesSeeder::class);
        $conversation = SellerConversation::query()
            ->where('seed_key', 'seller-messages-buyer-1')
            ->firstOrFail();
        $otherSeller = User::factory()->create(['role' => User::ROLE_SELLER]);
        Seller::create([
            'user_id' => $otherSeller->id,
            'store_name' => 'Unrelated Seller',
            'registration_status' => 'active',
        ]);
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->getJson('/api/v1/seller/messages?type=buyers')
            ->assertForbidden();

        $this->actingAs($otherSeller)
            ->getJson("/api/v1/seller/messages/conversations/{$conversation->id}")
            ->assertNotFound();
    }

    public function test_buyer_product_questions_get_product_replies_and_unrelated_questions_use_fallback(): void
    {
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Product Answers Store',
            'registration_status' => 'active',
        ]);
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Canvas Tote',
            'description' => 'A lightweight cotton tote bag.',
            'category' => 'Bags',
            'sku' => 'TEST-CANVAS-TOTE',
            'price' => 850,
            'stock_quantity' => 6,
            'status' => 'active',
            'is_archived' => false,
        ]);
        $product->options()->create([
            'type' => 'color',
            'name' => 'Black',
            'stock' => 3,
        ]);

        $this->actingAs($buyer)
            ->postJson('/api/v1/buyer/messages/conversations', ['product_id' => $product->id])
            ->assertCreated()
            ->assertJsonPath('data.product_name', 'Canvas Tote');

        $conversation = SellerConversation::query()->firstOrFail();
        $this->postJson("/api/v1/buyer/messages/conversations/{$conversation->id}/messages", [
            'body' => 'Is this product still available?',
        ])
            ->assertCreated()
            ->assertJsonPath('data.auto_reply', true)
            ->assertJsonPath('data.messages.1.sender', 'seller')
            ->assertJsonPath('data.messages.1.text', 'Yes, Canvas Tote is available. There are 6 unit(s) in stock.');

        $this->postJson("/api/v1/buyer/messages/conversations/{$conversation->id}/messages", [
            'body' => 'Is black still available?',
        ])
            ->assertCreated()
            ->assertJsonPath('data.messages.1.text', 'Yes, Canvas Tote is available in Black. There are 3 unit(s) of this option in stock.');

        $this->postJson("/api/v1/buyer/messages/conversations/{$conversation->id}/messages", [
            'body' => 'Can you help me reset my email password?',
        ])
            ->assertCreated()
            ->assertJsonPath('data.messages.1.text', "Can't answer the question because it isn't related to the product or your order. Please ask about this product or your order.");

        $this->assertDatabaseCount('seller_messages', 6);
    }

    public function test_buyer_order_questions_get_current_shipment_details(): void
    {
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Shipment Answers Store',
            'registration_status' => 'active',
        ]);
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $order = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_number' => 'AUTO-REPLY-SHIP-001',
            'total' => 500,
            'status' => 'to_ship',
        ]);
        $order->shipment()->create([
            'tracking_number' => 'TRACK-AUTO-001',
            'courier' => 'Ease Express',
            'estimated_delivery' => now()->addDays(2)->toDateString(),
            'current_location' => 'Calamba Hub',
        ]);

        $this->actingAs($buyer)
            ->postJson('/api/v1/buyer/messages/conversations', ['order_id' => $order->id])
            ->assertCreated();

        $conversation = SellerConversation::query()->firstOrFail();
        $this->postJson("/api/v1/buyer/messages/conversations/{$conversation->id}/messages", [
            'body' => 'Kailan dadating ang order ko?',
        ])
            ->assertCreated()
            ->assertJsonPath(
                'data.messages.1.text',
                'Your order #AUTO-REPLY-SHIP-001 is currently To Ship. Shipment details — courier: Ease Express; tracking number: TRACK-AUTO-001; estimated delivery: '.now()->addDays(2)->format('M j, Y').'; latest location: Calamba Hub.',
            );
    }

    public function test_seller_can_send_and_download_an_attachment_up_to_15_mb(): void
    {
        Storage::fake('local');
        $this->seed(SellerMessagesSeeder::class);
        $sellerUser = User::query()->where('email', 'seller@shopease.test')->firstOrFail();
        $conversation = SellerConversation::query()
            ->where('seed_key', 'seller-messages-buyer-1')
            ->firstOrFail();

        $response = $this->actingAs($sellerUser)
            ->post("/api/v1/seller/messages/conversations/{$conversation->id}/messages", [
                'body' => 'Please see the attached file.',
                'attachment' => UploadedFile::fake()->create('seller-document.pdf', 15360, 'application/pdf'),
            ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.text', 'Please see the attached file.')
            ->assertJsonPath('data.attachment.name', 'seller-document.pdf')
            ->assertJsonPath('data.attachment.size', 15728640);

        $message = SellerMessage::query()->findOrFail($response->json('data.id'));
        Storage::disk('local')->assertExists($message->attachment_path);

        $this->get($response->json('data.attachment.url'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
        $this->get($response->json('data.attachment.url').'?download=1')
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename=seller-document.pdf');

        $photoResponse = $this->post("/api/v1/seller/messages/conversations/{$conversation->id}/messages", [
            'attachment' => UploadedFile::fake()->create('buyer-photo.jpg', 10, 'image/jpeg'),
        ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.text', null)
            ->assertJsonPath('data.attachment.mime_type', 'image/jpeg');
        $this->get($photoResponse->json('data.attachment.url'))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename=buyer-photo.jpg');

        $videoResponse = $this->post("/api/v1/seller/messages/conversations/{$conversation->id}/messages", [
            'attachment' => UploadedFile::fake()->create('product-video.mp4', 10, 'video/mp4'),
        ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.text', null)
            ->assertJsonPath('data.attachment.mime_type', 'video/mp4');
        $this->get($videoResponse->json('data.attachment.url'))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename=product-video.mp4');
    }

    public function test_seller_message_attachments_over_15_mb_are_rejected_with_a_clear_message(): void
    {
        Storage::fake('local');
        $this->seed(SellerMessagesSeeder::class);
        $sellerUser = User::query()->where('email', 'seller@shopease.test')->firstOrFail();
        $conversation = SellerConversation::query()
            ->where('seed_key', 'seller-messages-buyer-1')
            ->firstOrFail();

        $this->actingAs($sellerUser)
            ->postJson("/api/v1/seller/messages/conversations/{$conversation->id}/messages", [
                'attachment' => UploadedFile::fake()->create('too-large.pdf', 15361, 'application/pdf'),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('attachment')
            ->assertJsonPath('errors.attachment.0', 'You can only attach files up to 15 MB.');

        $this->assertDatabaseMissing('seller_messages', [
            'conversation_id' => $conversation->id,
            'attachment_name' => 'too-large.pdf',
        ]);
    }

    public function test_other_sellers_cannot_download_a_conversation_attachment(): void
    {
        Storage::fake('local');
        $this->seed(SellerMessagesSeeder::class);
        $sellerUser = User::query()->where('email', 'seller@shopease.test')->firstOrFail();
        $conversation = SellerConversation::query()
            ->where('seed_key', 'seller-messages-buyer-1')
            ->firstOrFail();
        $message = $conversation->messages()->create([
            'sender_id' => $sellerUser->id,
            'body' => null,
            'attachment_path' => 'seller-messages/'.$conversation->id.'/private.pdf',
            'attachment_name' => 'private.pdf',
            'attachment_mime_type' => 'application/pdf',
            'attachment_size' => 10,
        ]);
        Storage::disk('local')->put($message->attachment_path, 'private');
        $otherSeller = User::factory()->create(['role' => User::ROLE_SELLER]);
        Seller::create([
            'user_id' => $otherSeller->id,
            'store_name' => 'Different Seller',
            'registration_status' => 'active',
        ]);

        $this->actingAs($otherSeller)
            ->get("/api/v1/seller/messages/conversations/{$conversation->id}/messages/{$message->id}/attachment")
            ->assertNotFound();
    }

    public function test_seller_messages_page_is_authenticated_and_bootstraps_the_messaging_api(): void
    {
        $seller = User::factory()->create(['role' => User::ROLE_SELLER]);
        Seller::create([
            'user_id' => $seller->id,
            'store_name' => 'Messaging Page Seller',
            'registration_status' => 'active',
        ]);
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($seller)
            ->get('/seller/messages')
            ->assertOk()
            ->assertViewIs('pages.seller.messages')
            ->assertSee('sellerMessagesConfig', false)
            ->assertSee('newBuyerConversationDialog', false)
            ->assertSee('apiToken', false);

        $this->actingAs($buyer)
            ->get('/seller/messages')
            ->assertForbidden();
    }

    public function test_message_seeder_is_repeatable_and_creates_related_demo_records(): void
    {
        $this->seed(SellerMessagesSeeder::class);
        $this->seed(SellerMessagesSeeder::class);

        $this->assertDatabaseCount('seller_conversations', 5);
        $this->assertDatabaseCount('seller_messages', 13);
        $this->assertDatabaseCount('complaints', 2);
        $this->assertDatabaseCount('orders', 3);
        $this->assertDatabaseHas('products', ['sku' => 'SE-DEMO-MESSAGE-PRODUCT']);
        $this->assertDatabaseCount('order_items', 3);
        $this->assertDatabaseHas('seller_conversations', [
            'type' => 'buyers',
            'order_id' => Order::query()->where('order_number', 'SE-DEMO-MESSAGE-ORDER-2028')->value('id'),
        ]);
        $this->assertDatabaseHas('seller_conversations', [
            'type' => 'complaints',
            'complaint_id' => Complaint::query()
                ->where('subject', 'like', 'SE-DEMO-MESSAGE-COMPLAINT-0148%')
                ->value('id'),
        ]);
    }

    public function test_message_seeder_attaches_demo_threads_to_an_existing_seller_account(): void
    {
        $sellerUser = User::factory()->create([
            'email' => 'shop-owner@example.test',
            'role' => User::ROLE_SELLER,
        ]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Existing Shop',
            'registration_status' => 'active',
        ]);

        $this->seed(SellerMessagesSeeder::class);

        $this->assertDatabaseCount('seller_conversations', 5);
        $this->assertDatabaseHas('seller_conversations', [
            'seller_id' => $seller->id,
            'seed_key' => 'seller-messages-buyer-1',
        ]);
        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/messages?type=buyers')
            ->assertOk()
            ->assertJsonCount(3, 'data');
        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/messages?type=complaints')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
