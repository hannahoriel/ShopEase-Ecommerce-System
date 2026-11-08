<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\OrderReturn;
use App\Models\Admin\OrderReturnEvent;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReturnsRefundsSeeder extends Seeder
{
    private const ORDER_PREFIX = 'SE-DEMO-RET-';

    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('ReturnsRefundsSeeder is restricted to local and testing environments.');
        }

        DB::transaction(function (): void {
            $now = now();
            $buyer = User::query()->updateOrCreate(
                ['email' => 'returns.buyer@shopease.test'],
                ['name' => 'Jamie Santos', 'role' => User::ROLE_BUYER,
                    'registration_status' => 'active', 'email_verified_at' => $now,
                    'password' => Hash::make('password')],
            );
            $sellerUser = User::query()->updateOrCreate(
                ['email' => 'returns.seller@shopease.test'],
                ['name' => 'Returns Demo Store', 'role' => User::ROLE_SELLER,
                    'registration_status' => 'active', 'email_verified_at' => $now,
                    'password' => Hash::make('password')],
            );
            $seller = Seller::query()->updateOrCreate(
                ['user_id' => $sellerUser->id],
                ['first_name' => 'Returns', 'last_name' => 'Demo', 'contact_no' => '09170000123',
                    'province' => 'Laguna', 'municipality' => 'Calamba City', 'barangay' => 'Real',
                    'street' => 'Market Road', 'house_number' => '10', 'store_name' => 'Returns Demo Store',
                    'business_name' => 'Returns Demo Store', 'registration_status' => 'active', 'approved_at' => $now],
            );

            $fixtures = [
                [
                    'key' => 'PENDING', 'return_type' => 'return', 'status' => 'pending',
                    'shipping_status' => 'return-requested', 'requested_at' => $now->copy()->subDays(2),
                    'reason' => 'The item does not fit as expected.', 'solution' => null,
                    'refund_amount' => 1599, 'returned_at' => null, 'reviewed_at' => null, 'refunded_at' => null,
                    'tracking_number' => null, 'events' => [
                        ['status' => 'return-requested', 'location' => 'Buyer return request'],
                    ],
                ],
                [
                    'key' => 'ACCEPTED-REFUND', 'return_type' => 'refund', 'status' => 'accepted',
                    'shipping_status' => null, 'requested_at' => $now->copy()->subDays(3),
                    'reason' => 'The package arrived with a missing accessory.',
                    'solution' => 'Refund approved; no return shipment is required.',
                    'refund_amount' => 499, 'returned_at' => null, 'reviewed_at' => $now->copy()->subDays(2), 'refunded_at' => null,
                    'tracking_number' => null, 'events' => [
                        ['status' => 'accepted', 'location' => 'Seller review'],
                    ],
                ],
                [
                    'key' => 'REFUNDED-RETURN', 'return_type' => 'return', 'status' => 'refunded',
                    'shipping_status' => 'returned', 'requested_at' => $now->copy()->subDays(10),
                    'reason' => 'The item arrived damaged.', 'solution' => 'Item received and refund completed.',
                    'refund_amount' => 1299, 'returned_at' => $now->copy()->subDays(6),
                    'reviewed_at' => $now->copy()->subDays(8), 'refunded_at' => $now->copy()->subDays(5),
                    'tracking_number' => 'RET-DEMO-1003', 'events' => [
                        ['status' => 'return-requested', 'location' => 'Buyer return request', 'days_ago' => 10],
                        ['status' => 'picked-up', 'location' => 'Calamba Delivery Hub', 'days_ago' => 9],
                        ['status' => 'in-transit', 'location' => 'Laguna Sorting Center', 'days_ago' => 8],
                        ['status' => 'returned', 'location' => 'Returns Demo Store', 'days_ago' => 6],
                        ['status' => 'refunded', 'location' => 'Seller refund processing', 'days_ago' => 5],
                    ],
                ],
                [
                    'key' => 'DECLINED', 'return_type' => 'refund', 'status' => 'declined',
                    'shipping_status' => null, 'requested_at' => $now->copy()->subDays(4),
                    'reason' => 'The buyer changed their mind after delivery.',
                    'solution' => 'The request does not meet the refund policy requirements.',
                    'refund_amount' => 899, 'returned_at' => null, 'reviewed_at' => $now->copy()->subDays(3), 'refunded_at' => null,
                    'tracking_number' => null, 'events' => [
                        ['status' => 'declined', 'location' => 'Seller review'],
                    ],
                ],
            ];

            foreach ($fixtures as $index => $fixture) {
                $unitPrice = [1599, 499, 1299, 899][$index];
                $quantity = 1;
                $orderNumber = self::ORDER_PREFIX.$fixture['key'];
                $order = Order::query()->updateOrCreate(
                    ['order_number' => $orderNumber],
                    ['buyer_id' => $buyer->id, 'seller_id' => $seller->id,
                        'total' => $unitPrice * $quantity, 'status' => 'completed',
                        'delivery_name' => $buyer->name, 'delivery_phone' => '09170000456',
                        'delivery_address' => '10 Market Road, Calamba City, Laguna',
                        'payment_method' => 'Cash on Delivery'],
                );
                $item = OrderItem::query()->updateOrCreate(
                    ['order_id' => $order->id, 'product_name' => 'Everyday Carry Backpack'],
                    ['quantity' => $quantity, 'unit_price' => $unitPrice,
                        'variation' => 'Classic', 'color' => 'Black', 'size' => 'Standard'],
                );

                $returnItems = [[
                    'order_item_id' => $item->id,
                    'product_name' => $item->product_name,
                    'variation' => $item->variation,
                    'color' => $item->color,
                    'size' => $item->size,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'product_price' => $unitPrice,
                ]];
                $return = OrderReturn::query()->updateOrCreate(
                    ['order_id' => $order->id],
                    ['order_id' => $order->id, 'return_type' => $fixture['return_type'],
                        'status' => $fixture['status'], 'shipping_status' => $fixture['shipping_status'],
                        'courier' => $fixture['shipping_status'] ? 'ShopEase Returns' : null,
                        'tracking_number' => $fixture['tracking_number'],
                        'reason' => $fixture['reason'], 'solution' => $fixture['solution'],
                        'return_items' => $returnItems, 'refund_amount' => $fixture['refund_amount'],
                        'requested_at' => $fixture['requested_at'], 'returned_at' => $fixture['returned_at'],
                        'reviewed_by' => $fixture['reviewed_at'] ? $sellerUser->id : null,
                        'reviewed_at' => $fixture['reviewed_at'], 'refunded_at' => $fixture['refunded_at']],
                );

                foreach ($fixture['events'] as $event) {
                    $scannedAt = $now->copy()->subDays($event['days_ago'] ?? 0);
                    OrderReturnEvent::query()->updateOrCreate(
                        ['order_return_id' => $return->id, 'status' => $event['status']],
                        ['location' => $event['location'], 'scanned_at' => $scannedAt],
                    );
                }
            }
        });

        $this->command?->info('Seeded repeatable return and refund requests for returns.seller@shopease.test.');
    }
}
