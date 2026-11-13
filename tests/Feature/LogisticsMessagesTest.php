<?php

namespace Tests\Feature;

use App\Models\Admin\AdminConversation;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\LogisticsMessage;
use App\Models\User;
use Database\Seeders\AdminMessagesSeeder;
use Database\Seeders\LogisticsMessagesSeeder;
use Database\Seeders\LogisticsParcelTrackingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LogisticsMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_logistics_can_load_contacts_and_send_admin_sorting_center_and_hub_messages(): void
    {
        [$logisticsUser, $branch, $hub, $centerUser, $hubUser, $admin] = $this->fixture();

        $this->actingAs($logisticsUser)
            ->getJson('/api/v1/logistics/messages')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.type', 'admin');

        $this->actingAs($logisticsUser)
            ->postJson('/api/v1/logistics/messages', [
                'type' => 'sorting', 'contact_id' => $branch->id, 'body' => 'Parcel batch is ready for pickup.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.message.text', 'Parcel batch is ready for pickup.')
            ->assertJsonPath('data.message.mine', true);

        $logisticsInbox = $this->getJson('/api/v1/logistics/messages')->assertOk();
        $sortingContact = collect($logisticsInbox->json('data'))
            ->first(fn (array $contact): bool => $contact['type'] === 'sorting'
                && (int) $contact['contact_id'] === (int) $branch->id);
        $conversationId = (int) $sortingContact['conversation_id'];
        $this->assertDatabaseHas('logistics_messages', [
            'conversation_id' => $conversationId,
            'sender_id' => $logisticsUser->id,
            'body' => 'Parcel batch is ready for pickup.',
        ]);

        $centerThread = $this->actingAs($centerUser)
            ->postJson('/api/v1/sorting_center/messages/thread', [
                'type' => 'sorting',
                'contact_id' => $logisticsUser->id,
            ])
            ->assertOk();
        $this->assertSame($conversationId, $centerThread->json('data.conversation_id'));
        $this->assertSame('Parcel batch is ready for pickup.', $centerThread->json('data.messages.0.text'));
        $this->assertFalse($centerThread->json('data.messages.0.mine'));
        $facilityMessage = LogisticsMessage::query()
            ->where('conversation_id', $conversationId)
            ->where('body', 'Parcel batch is ready for pickup.')
            ->firstOrFail();
        $this->assertDatabaseHas('logistics_message_reads', [
            'logistics_message_id' => $facilityMessage->id,
            'user_id' => $centerUser->id,
        ]);

        $this->actingAs($centerUser)
            ->postJson('/api/v1/sorting_center/messages', [
                'type' => 'sorting', 'contact_id' => $conversationId, 'body' => 'Pickup confirmed.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.message.mine', true);
        $this->actingAs($logisticsUser)
            ->postJson('/api/v1/logistics/messages/thread', ['type' => 'sorting', 'contact_id' => $branch->id])
            ->assertOk()
            ->assertJsonPath('data.messages.1.text', 'Pickup confirmed.')
            ->assertJsonPath('data.messages.1.mine', false);

        $hubMessage = $this->actingAs($logisticsUser)
            ->postJson('/api/v1/logistics/messages', [
                'type' => 'delivery', 'contact_id' => $hub->id, 'body' => 'Please confirm dispatch.',
            ])
            ->assertCreated();
        $hubConversationId = $hubMessage->json('data.conversation_id');
        $this->actingAs($hubUser)->getJson("/api/v1/delivery_hub/messages/{$hubConversationId}")
            ->assertOk()->assertJsonPath('data.messages.0.text', 'Please confirm dispatch.');

        $this->actingAs($logisticsUser)
            ->postJson('/api/v1/logistics/messages', [
                'type' => 'admin', 'contact_id' => $admin->id, 'body' => 'Need help with a dispatch issue.',
            ])
            ->assertCreated();
        $adminConversation = AdminConversation::query()->where('category', AdminConversation::CATEGORY_LOGISTICS)
            ->where('user_id', $logisticsUser->id)->firstOrFail();
        $this->assertDatabaseHas('admin_messages', [
            'conversation_id' => $adminConversation->id,
            'sender_id' => $logisticsUser->id,
            'body' => 'Need help with a dispatch issue.',
        ]);

        $this->actingAs($logisticsUser)->get('/logistics/messages')->assertOk()->assertSee('api/v1/logistics/messages');
    }

    public function test_logistics_cannot_message_another_companys_center_or_an_account_without_a_role(): void
    {
        [$logisticsUser] = $this->fixture();
        $otherUser = User::factory()->create(['role' => User::ROLE_LOGISTICS]);
        $otherLogistics = Logistics::query()->create([
            'user_id' => $otherUser->id, 'last_name' => 'Other', 'first_name' => 'Carrier', 'sex' => 'other',
            'contact_no' => '09170000000', 'birthday' => '1990-01-01', 'age' => 36, 'province' => 'Laguna',
            'municipality' => 'Calamba', 'barangay' => 'Real', 'street' => 'Road', 'house_number' => '2',
            'business_name' => 'Other Logistics', 'registration_status' => 'active',
        ]);
        $otherBranch = LogisticsBranch::query()->create([
            'logistics_id' => $otherLogistics->id, 'user_id' => User::factory()->create(['role' => User::ROLE_SORTING_CENTER])->id,
            'name' => 'Other Center', 'address' => 'Other Road', 'status' => 'active',
        ]);

        $this->actingAs($logisticsUser)->postJson('/api/v1/logistics/messages', [
            'type' => 'sorting', 'contact_id' => $otherBranch->id, 'body' => 'Unauthorized message.',
        ])->assertNotFound();

        $this->actingAs($logisticsUser)->postJson('/api/v1/logistics/messages', [
            'type' => 'sorting', 'contact_id' => $otherBranch->id, 'body' => '   ',
        ])->assertUnprocessable();
    }

    public function test_logistics_can_send_and_download_private_attachments_in_facility_and_admin_threads(): void
    {
        [$logisticsUser, $branch, , $centerUser, , $admin] = $this->fixture();
        Storage::fake('local');

        $facilityResponse = $this->actingAs($logisticsUser)->post('/api/v1/logistics/messages', [
            'type' => 'sorting', 'contact_id' => $branch->id,
            'attachment' => UploadedFile::fake()->create('parcel-manifest.pdf', 240, 'application/pdf'),
        ], ['Accept' => 'application/json']);
        $facilityResponse->assertCreated()->assertJsonPath('data.message.text', null)
            ->assertJsonPath('data.message.attachment_name', 'parcel-manifest.pdf');
        $facilityMessage = $facilityResponse->json('data.message');
        $facilityConversationId = $facilityResponse->json('data.conversation_id');
        $storedMessage = LogisticsMessage::query()->findOrFail($facilityMessage['id']);
        Storage::disk('local')->assertExists($storedMessage->attachment_path);

        $centerInbox = $this->actingAs($centerUser)->getJson('/api/v1/sorting_center/messages')->assertOk();
        $centerConversation = collect($centerInbox->json('data'))
            ->firstWhere('conversation_id', $facilityConversationId);
        $this->assertSame('parcel-manifest.pdf', $centerConversation['messages'][0]['attachment_name']);
        $centerAttachmentUrl = $centerConversation['messages'][0]['attachment_url'];
        $this->actingAs($centerUser)->get(parse_url($centerAttachmentUrl, PHP_URL_PATH))
            ->assertOk()->assertHeader('content-disposition');

        $adminResponse = $this->actingAs($logisticsUser)->post('/api/v1/logistics/messages', [
            'type' => 'admin', 'contact_id' => $admin->id,
            'attachment' => UploadedFile::fake()->create('dispatch-note.txt', 4, 'text/plain'),
        ], ['Accept' => 'application/json']);
        $adminResponse->assertCreated()->assertJsonPath('data.message.attachment_name', 'dispatch-note.txt');
        $adminMessage = $adminResponse->json('data.message');
        $this->actingAs($logisticsUser)->get(parse_url($adminMessage['attachment_url'], PHP_URL_PATH))
            ->assertOk()->assertHeader('content-disposition');
    }

    public function test_demo_seeders_create_repeatable_admin_center_and_hub_message_threads(): void
    {
        Storage::fake('local');
        $this->seed([LogisticsParcelTrackingSeeder::class, AdminMessagesSeeder::class, LogisticsMessagesSeeder::class]);
        $this->seed(LogisticsMessagesSeeder::class);

        $logisticsUser = User::query()->where('email', 'tracking.logistics@shopease.test')->firstOrFail();
        $this->actingAs($logisticsUser)->getJson('/api/v1/logistics/messages')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.name', 'ShopEase Admin')
            ->assertJsonPath('data.1.messages.0.text', 'The inbound parcel batch is on its way to Laguna Sorting Center.')
            ->assertJsonPath('data.2.messages.0.text', 'The delivery route is ready for today’s parcels.');

        $this->assertSame(2, \App\Models\Logistics\LogisticsConversation::query()
            ->where('logistics_id', $logisticsUser->logisticsProfile->id)->count());
        $attachment = LogisticsMessage::query()->where('attachment_name', 'demo-parcel-manifest.txt')->firstOrFail();
        Storage::disk('local')->assertExists($attachment->attachment_path);
    }

    private function fixture(): array
    {
        $logisticsUser = User::factory()->create(['role' => User::ROLE_LOGISTICS]);
        $logistics = Logistics::query()->create([
            'user_id' => $logisticsUser->id, 'last_name' => 'Carrier', 'first_name' => 'Demo', 'sex' => 'other',
            'contact_no' => '09171234567', 'birthday' => '1990-01-01', 'age' => 36, 'province' => 'Laguna',
            'municipality' => 'Calamba', 'barangay' => 'Real', 'street' => 'Main Street', 'house_number' => '1',
            'business_name' => 'Demo Logistics', 'registration_status' => 'active',
        ]);
        $centerUser = User::factory()->create(['role' => User::ROLE_SORTING_CENTER]);
        $branch = LogisticsBranch::query()->create([
            'logistics_id' => $logistics->id, 'user_id' => $centerUser->id, 'name' => 'Laguna Center',
            'address' => 'Main Street, Laguna', 'province' => 'Laguna', 'municipality' => 'Calamba', 'status' => 'active',
        ]);
        $hubUser = User::factory()->create(['role' => User::ROLE_DELIVERY_HUB]);
        $hub = LogisticsDeliveryHub::query()->create([
            'logistics_branch_id' => $branch->id, 'user_id' => $hubUser->id, 'hub_name' => 'Calamba Hub',
            'province' => 'Laguna', 'municipality' => 'Calamba', 'barangay' => 'Real', 'postal_code' => '4000',
            'street' => 'Main Street', 'manager_name' => 'Hub Manager', 'manager_email' => 'hub@example.com',
            'manager_phone' => '09170000000', 'login_email' => $hubUser->email, 'status' => 'active',
        ]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        return [$logisticsUser, $branch, $hub, $centerUser, $hubUser, $admin];
    }
}
