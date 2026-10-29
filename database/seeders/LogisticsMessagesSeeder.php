<?php

namespace Database\Seeders;

use App\Models\Admin\AdminConversation;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsConversation;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class LogisticsMessagesSeeder extends Seeder
{
    public function run(): void
    {
        $logistics = Logistics::query()->whereHas('user', fn ($query) => $query
            ->where('email', 'tracking.logistics@shopease.test'))->first();
        if (! $logistics) {
            return;
        }

        $branch = LogisticsBranch::query()->where('logistics_id', $logistics->id)
            ->where('login_email', 'center.tracking@shopease.test')->first();
        $hub = $branch?->deliveryHubs()->where('login_email', 'hub.tracking@shopease.test')->first();
        if (! $branch || ! $hub) {
            return;
        }

        $centerUser = $this->demoUser('center.tracking@shopease.test', 'Laguna Sorting Center', User::ROLE_SORTING_CENTER);
        $hubUser = $this->demoUser('hub.tracking@shopease.test', 'Santa Cruz Delivery Hub', User::ROLE_DELIVERY_HUB);
        $branch->forceFill(['user_id' => $centerUser->id])->save();
        $hub->forceFill(['user_id' => $hubUser->id])->save();

        $centerConversation = $this->seedThread($logistics, $centerUser, 'sorting', $branch->id, null, [
            [$logistics->user_id, 'The inbound parcel batch is on its way to Laguna Sorting Center.'],
            [$centerUser->id, 'Received. We will scan the parcels as soon as the truck arrives.'],
        ]);
        $this->seedAttachment($centerConversation, $logistics->user_id, 'demo-parcel-manifest.txt', 'Demo manifest for parcel tracking test data.');
        $this->seedThread($logistics, $hubUser, 'delivery', $branch->id, $hub->id, [
            [$hubUser->id, 'The delivery route is ready for today’s parcels.'],
            [$logistics->user_id, 'Thanks. Please confirm once the riders complete dispatch.'],
        ]);

        $admin = User::query()->where('role', User::ROLE_ADMIN)->orderBy('id')->first();
        if ($admin) {
            $conversation = AdminConversation::query()->firstOrCreate([
                'category' => AdminConversation::CATEGORY_LOGISTICS,
                'user_id' => $logistics->user_id,
                'complaint_id' => null,
                'complaint_party' => null,
            ]);
            $conversation->messages()->updateOrCreate(
                ['sender_id' => $logistics->user_id, 'body' => 'Please confirm the weekly logistics network review schedule.'],
                ['read_at' => null]
            );
            $conversation->messages()->updateOrCreate(
                ['sender_id' => $admin->id, 'body' => 'The network review is scheduled for Thursday at 10:00 AM.'],
                ['read_at' => now()]
            );
            $adminAttachmentPath = "admin-messages/{$conversation->id}/demo-dispatch-note.txt";
            Storage::disk('local')->put($adminAttachmentPath, 'Demo dispatch note for message attachment testing.');
            $conversation->messages()->updateOrCreate(
                ['sender_id' => $logistics->user_id, 'attachment_name' => 'demo-dispatch-note.txt'],
                ['body' => null, 'attachment_path' => $adminAttachmentPath, 'attachment_mime_type' => 'text/plain',
                    'attachment_size' => Storage::disk('local')->size($adminAttachmentPath), 'read_at' => null]
            );
        }
    }

    private function demoUser(string $email, string $name, string $role): User
    {
        return User::query()->updateOrCreate(['email' => $email], [
            'name' => $name,
            'role' => $role,
            'registration_status' => 'active',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
    }

    private function seedThread(Logistics $logistics, User $contact, string $type, int $branchId, ?int $hubId, array $messages): LogisticsConversation
    {
        $conversation = LogisticsConversation::query()->updateOrCreate(
            ['logistics_id' => $logistics->id, 'contact_user_id' => $contact->id],
            ['contact_type' => $type, 'logistics_branch_id' => $branchId, 'logistics_delivery_hub_id' => $hubId]
        );
        foreach ($messages as [$senderId, $body]) {
            $conversation->messages()->updateOrCreate(
                ['sender_id' => $senderId, 'body' => $body],
                ['read_at' => (int) $senderId === (int) $logistics->user_id ? null : now()]
            );
        }

        return $conversation;
    }

    private function seedAttachment(LogisticsConversation $conversation, int $senderId, string $name, string $contents): void
    {
        $path = "logistics-messages/{$conversation->id}/{$name}";
        Storage::disk('local')->put($path, $contents);
        $conversation->messages()->updateOrCreate(
            ['sender_id' => $senderId, 'attachment_name' => $name],
            ['body' => null, 'attachment_path' => $path, 'attachment_mime_type' => 'text/plain',
                'attachment_size' => Storage::disk('local')->size($path), 'read_at' => null]
        );
    }
}
