<?php

namespace Database\Seeders;

use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\Seller\SellerConversation;
use App\Models\Seller\SellerMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SellerMessagesSeeder extends Seeder
{
    public function run(): void
    {
        $seller = Seller::query()
            ->whereHas('user', fn ($users) => $users->where('role', User::ROLE_SELLER))
            ->orderByRaw("CASE WHEN registration_status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->first();

        if ($seller) {
            $sellerUser = $seller->user;
        } else {
            $sellerUser = User::firstOrCreate(
                ['email' => 'seller@shopease.test'],
                [
                    'name' => 'Seller User',
                    'role' => User::ROLE_SELLER,
                    'password' => Hash::make('seller123'),
                ],
            );
            $seller = Seller::firstOrCreate(
                ['user_id' => $sellerUser->id],
                [
                    'store_name' => 'ShopEase Demo Store',
                    'registration_status' => 'active',
                ],
            );
        }
        $admin = User::query()->where('role', User::ROLE_ADMIN)->orderBy('id')->first()
            ?? User::firstOrCreate(
                ['email' => 'admin@shopease.test'],
                [
                    'name' => 'ShopEase Admin',
                    'role' => User::ROLE_ADMIN,
                    'password' => Hash::make('password'),
                ],
            );

        $now = Carbon::now();
        $product = Product::updateOrCreate(
            ['sku' => 'SE-DEMO-MESSAGE-PRODUCT'],
            [
                'seller_id' => $seller->id,
                'name' => 'Classic Everyday Backpack',
                'description' => 'A durable everyday backpack with a padded laptop sleeve and water-resistant fabric.',
                'category' => 'Bags',
                'price' => 1250,
                'stock_quantity' => 8,
                'status' => 'active',
                'is_archived' => false,
            ],
        );
        $product->options()->updateOrCreate(
            ['type' => 'color', 'name' => 'Black'],
            ['stock' => 4, 'price' => 0, 'price_type' => 'addon'],
        );
        $product->options()->updateOrCreate(
            ['type' => 'color', 'name' => 'Navy'],
            ['stock' => 4, 'price' => 0, 'price_type' => 'addon'],
        );

        $buyerFixtures = [
            [
                'name' => 'Juan Dela Cruz',
                'email' => 'seller.messages.juan@shopease.test',
                'order_number' => 'SE-DEMO-MESSAGE-ORDER-2028',
                'order_status' => 'new',
                'created_at' => $now->copy()->subMinutes(7),
                'messages' => [
                    ['buyer', 'Hi! Is this still available in black?', 12],
                    ['seller', 'Hi Juan! Yes, the black variant is still available.', 10],
                    ['buyer', 'Great. If I order today, when can you ship it?', 7],
                    ['seller', 'Your order is currently New. Shipment tracking will be available once it has been handed to the courier.', 6],
                ],
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'seller.messages.maria@shopease.test',
                'order_number' => 'SE-DEMO-MESSAGE-ORDER-2026',
                'order_status' => 'to_ship',
                'created_at' => $now->copy()->subHours(2),
                'messages' => [
                    ['seller', 'Hi Maria, your order has already been packed and is ready for pickup.', 25],
                    ['buyer', 'Thank you for the update!', 18],
                ],
            ],
            [
                'name' => 'Carlo Reyes',
                'email' => 'seller.messages.carlo@shopease.test',
                'order_number' => 'SE-DEMO-MESSAGE-ORDER-2027',
                'order_status' => 'preparing',
                'created_at' => $now->copy()->subDay(),
                'messages' => [
                    ['buyer', 'Hello, can I still change the delivery address for my order?', 62],
                    ['seller', 'Hi Carlo. I can check that while your order is still being prepared.', 58],
                ],
            ],
        ];

        foreach ($buyerFixtures as $index => $fixture) {
            $buyer = User::firstOrCreate(
                ['email' => $fixture['email']],
                [
                    'name' => $fixture['name'],
                    'role' => User::ROLE_BUYER,
                    'password' => Hash::make('password'),
                ],
            );
            $order = Order::updateOrCreate(
                ['order_number' => $fixture['order_number']],
                [
                    'buyer_id' => $buyer->id,
                    'seller_id' => $seller->id,
                    'total' => 1250 + ($index * 250),
                    'commission_amount' => 62.50 + ($index * 12.50),
                    'status' => $fixture['order_status'],
                    'delivery_name' => $buyer->name,
                    'delivery_phone' => '0917000000'.($index + 1),
                    'delivery_address' => 'Calamba, Laguna',
                    'payment_method' => 'Cash on Delivery',
                ],
            );
            $order->forceFill([
                'created_at' => $fixture['created_at'],
                'updated_at' => $fixture['created_at'],
            ])->saveQuietly();
            OrderItem::updateOrCreate(
                ['order_id' => $order->id, 'product_id' => $product->id],
                [
                    'product_name' => $product->name,
                    'variation' => null,
                    'color' => $index === 0 ? 'Black' : 'Navy',
                    'size' => null,
                    'quantity' => 1,
                    'unit_price' => $product->price,
                ],
            );

            $conversation = SellerConversation::updateOrCreate(
                ['seed_key' => 'seller-messages-buyer-'.($index + 1)],
                [
                    'seller_id' => $seller->id,
                    'type' => SellerConversation::TYPE_BUYER,
                    'buyer_id' => $buyer->id,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                ],
            );

            $this->seedMessages($conversation, $fixture['messages'], $sellerUser, $buyer, $now, 'buyer-'.($index + 1));
        }

        foreach ([
            [
                'suffix' => '0148',
                'order_number' => 'SE-DEMO-MESSAGE-ORDER-2028',
                'subject' => 'Package handover proof',
                'description' => 'Admin requested evidence of the package condition before courier pickup.',
                'status' => 'in_progress',
                'messages' => [
                    ['admin', 'Please provide a clear photo of the package before shipment and any courier handover proof available.', 20],
                    ['seller', 'Understood. I will send the requested proof here shortly.', 16],
                    ['admin', 'Thank you. We will add it to the case once received.', 12],
                ],
            ],
            [
                'suffix' => '0139',
                'order_number' => 'SE-DEMO-MESSAGE-ORDER-2026',
                'subject' => 'Item condition confirmation',
                'description' => 'Admin requested confirmation that the item was sealed before pickup.',
                'status' => 'open',
                'messages' => [
                    ['admin', 'Please confirm whether the item was sealed and complete before courier pickup.', 140],
                    ['seller', 'Yes. The item was sealed, complete, and documented before pickup.', 127],
                ],
            ],
        ] as $index => $fixture) {
            $order = Order::query()->where('order_number', $fixture['order_number'])->firstOrFail();
            $complaint = Complaint::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'subject' => 'SE-DEMO-MESSAGE-COMPLAINT-'.$fixture['suffix'].' '.$fixture['subject'],
                ],
                [
                    'user_id' => $order->buyer_id,
                    'description' => $fixture['description'],
                    'status' => $fixture['status'],
                ],
            );
            $conversation = SellerConversation::updateOrCreate(
                ['seed_key' => 'seller-messages-complaint-'.($index + 1)],
                [
                    'seller_id' => $seller->id,
                    'type' => SellerConversation::TYPE_COMPLAINT,
                    'buyer_id' => $order->buyer_id,
                    'order_id' => $order->id,
                    'complaint_id' => $complaint->id,
                ],
            );

            $this->seedMessages($conversation, $fixture['messages'], $sellerUser, $admin, $now, 'complaint-'.($index + 1));
        }

        $this->command?->info("Seller message demo conversations seeded for {$sellerUser->email}.");
    }

    private function seedMessages(
        SellerConversation $conversation,
        array $fixtures,
        User $seller,
        User $otherParty,
        Carbon $now,
        string $conversationKey,
    ): void {
        foreach ($fixtures as $index => [$sender, $body, $minutesAgo]) {
            $createdAt = $now->copy()->subMinutes($minutesAgo);
            SellerMessage::updateOrCreate(
                ['seed_key' => "seller-messages-{$conversationKey}-".($index + 1)],
                [
                    'conversation_id' => $conversation->id,
                    'sender_id' => $sender === 'seller' ? $seller->id : $otherParty->id,
                    'body' => $body,
                    'read_at' => $sender === 'seller' ? $createdAt->copy()->addMinute() : null,
                ],
            )->forceFill([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ])->saveQuietly();
        }

        $conversation->forceFill([
            'updated_at' => $now->copy()->subMinutes($fixtures[array_key_last($fixtures)][2]),
        ])->saveQuietly();
    }
}
