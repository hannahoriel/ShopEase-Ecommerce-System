<?php

namespace Database\Seeders;

use App\Models\Admin\AdminConversation;
use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Logistics\Logistics;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminMessagesSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('role', User::ROLE_ADMIN)->first()
            ?? User::firstOrCreate(
                ['email' => 'admin.messages@shopease.test'],
                [
                    'name' => 'ShopEase Admin',
                    'password' => 'password',
                    'role' => User::ROLE_ADMIN,
                ]
            );

        $buyer = $this->demoUser('buyer.messages@shopease.test', 'Juan Dela Cruz', User::ROLE_BUYER);
        $sellerUser = $this->demoUser('seller.messages@shopease.test', 'Tech Haven', User::ROLE_SELLER);
        $logisticsUser = $this->demoUser('logistics.messages@shopease.test', 'Ease Express', User::ROLE_LOGISTICS);

        $seller = Seller::updateOrCreate(
            ['user_id' => $sellerUser->id],
            [
                'first_name' => 'Tech',
                'last_name' => 'Haven',
                'business_name' => 'Tech Haven',
                'store_name' => 'Tech Haven',
                'registration_status' => 'active',
            ]
        );

        Logistics::updateOrCreate(
            ['user_id' => $logisticsUser->id],
            [
                'first_name' => 'Ease',
                'last_name' => 'Express',
                'sex' => 'other',
                'contact_no' => '09171234567',
                'birthday' => '1988-06-15',
                'age' => 38,
                'province' => 'Metro Manila',
                'municipality' => 'Quezon City',
                'barangay' => 'Example',
                'street' => 'Logistics Avenue',
                'house_number' => '25',
                'business_name' => 'Ease Express',
                'registration_status' => 'active',
                'approved_at' => now()->subDays(30),
            ]
        );

        $order = Order::updateOrCreate(
            ['order_number' => 'ORD-ADMIN-MESSAGES-DEMO'],
            [
                'buyer_id' => $buyer->id,
                'seller_id' => $seller->id,
                'total' => 1250,
                'status' => 'pending',
            ]
        );

        $complaint = Complaint::updateOrCreate(
            ['subject' => 'Demo complaint conversation for Admin Messages'],
            [
                'user_id' => $buyer->id,
                'order_id' => $order->id,
                'type' => 'Order issue',
                'description' => 'Demo complaint used to preview private buyer and seller conversations.',
                'status' => 'in_progress',
            ]
        );

        $this->seedConversation(
            AdminConversation::CATEGORY_LOGISTICS,
            $logisticsUser,
            $admin,
            [
                ['sender' => $logisticsUser, 'body' => 'Pickup schedule for the demo shipment has been confirmed.', 'unread' => true],
                ['sender' => $admin, 'body' => 'Thank you. Please update the tracking status after the parcels are scanned.', 'unread' => false],
            ]
        );

        $this->seedConversation(
            AdminConversation::CATEGORY_BUYERS,
            $buyer,
            $admin,
            [
                ['sender' => $buyer, 'body' => 'Hello Admin, I need help checking the status of my order.', 'unread' => true],
                ['sender' => $admin, 'body' => 'Hi Juan. We are checking the latest order update for you.', 'unread' => false],
            ]
        );

        $this->seedConversation(
            AdminConversation::CATEGORY_SELLERS,
            $sellerUser,
            $admin,
            [
                ['sender' => $sellerUser, 'body' => 'Can you review our latest compliance submission?', 'unread' => true],
                ['sender' => $admin, 'body' => 'The documents are in the review queue. We will update you after validation.', 'unread' => false],
            ]
        );

        foreach ([
            ['party' => 'buyer', 'user' => $buyer],
            ['party' => 'seller', 'user' => $sellerUser],
        ] as $entry) {
            $this->seedConversation(
                AdminConversation::CATEGORY_COMPLAINTS,
                $entry['user'],
                $admin,
                $entry['party'] === 'buyer'
                    ? [
                        ['sender' => $buyer, 'body' => 'The item arrived damaged. I can provide photos of the package.', 'unread' => true],
                        ['sender' => $admin, 'body' => 'Thank you. Please attach photos of the item and packaging for our review.', 'unread' => false],
                    ]
                    : [
                        ['sender' => $sellerUser, 'body' => 'We can provide the packing photo and dispatch details for this order.', 'unread' => true],
                        ['sender' => $admin, 'body' => 'Please share the packing evidence so we can complete the review.', 'unread' => false],
                    ],
                $complaint,
                $entry['party']
            );
        }
    }

    private function demoUser(string $email, string $name, string $role): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => 'password',
                'role' => $role,
                'registration_status' => 'active',
                'approved_at' => now()->subDays(30),
            ]
        );
    }

    private function seedConversation(
        string $category,
        User $user,
        User $admin,
        array $messages,
        ?Complaint $complaint = null,
        ?string $complaintParty = null
    ): void {
        $conversation = AdminConversation::firstOrCreate([
            'category' => $category,
            'user_id' => $user->id,
            'complaint_id' => $complaint?->id,
            'complaint_party' => $complaintParty,
        ]);

        foreach ($messages as $message) {
            $sender = $message['sender'];
            $conversation->messages()->updateOrCreate(
                [
                    'sender_id' => $sender->id,
                    'body' => $message['body'],
                ],
                [
                    'read_at' => $message['unread'] ? null : now(),
                ]
            );
        }
    }
}
