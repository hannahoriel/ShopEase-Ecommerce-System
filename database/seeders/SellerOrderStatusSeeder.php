<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Seller\ProductReview;
use Illuminate\Database\Seeder;

class SellerOrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = ProductReview::query()
            ->whereHas('product', fn ($query) => $query
                ->where('status', 'active')
                ->where('is_archived', false))
            ->with(['product.seller', 'buyer'])
            ->orderBy('id')
            ->limit(3)
            ->get();

        if ($reviews->count() < 3) {
            $this->command?->info('At least three customer reviews for active products are required. Run ProductReviewSeeder first.');

            return;
        }

        $now = now();
        $fixtures = [
            [
                'order_number' => 'SE-DEMO-ORDER-STATUS-NEW',
                'status' => 'new',
                'created_at' => $now->copy()->subHours(2),
                'pickup_date' => null,
                'history' => [
                    [null, 'new', 'buyer', 'Order placed by buyer.', $now->copy()->subHours(2)],
                ],
            ],
            [
                'order_number' => 'SE-DEMO-ORDER-STATUS-PREPARING',
                'status' => 'preparing',
                'created_at' => $now->copy()->subDay(),
                'pickup_date' => null,
                'history' => [
                    [null, 'new', 'buyer', 'Order placed by buyer.', $now->copy()->subDay()],
                    ['new', 'preparing', 'seller', 'Seller started preparing the order.', $now->copy()->subHours(23)],
                ],
            ],
            [
                'order_number' => 'SE-DEMO-ORDER-STATUS-TO-SHIP',
                'status' => 'to_ship',
                'created_at' => $now->copy()->subDays(2),
                'pickup_date' => $now->copy()->addDay()->toDateString(),
                'history' => [
                    [null, 'new', 'buyer', 'Order placed by buyer.', $now->copy()->subDays(2)],
                    ['new', 'preparing', 'seller', 'Seller started preparing the order.', $now->copy()->subDays(2)->addHour()],
                    ['preparing', 'to_ship', 'seller', 'Pickup has been scheduled.', $now->copy()->subDays(2)->addHours(2)],
                ],
            ],
        ];

        foreach ($fixtures as $index => $fixture) {
            $review = $reviews[$index];
            $product = $review->product;
            $seller = $product->seller;
            $price = (float) $product->price;

            $order = Order::updateOrCreate(
                ['order_number' => $fixture['order_number']],
                [
                    'buyer_id' => $review->buyer_id,
                    'seller_id' => $seller->id,
                    'total' => $price,
                    'commission_amount' => 0,
                    'status' => $fixture['status'],
                    'pickup_date' => $fixture['pickup_date'],
                    'pickup_time' => $fixture['pickup_date'] ? '14:00' : null,
                    'delivery_name' => $review->buyer->name,
                    'delivery_phone' => $review->buyer->contact_no,
                    'delivery_address' => 'Calamba, Laguna',
                    'payment_method' => 'Cash on Delivery',
                    'created_at' => $fixture['created_at'],
                    'updated_at' => $fixture['created_at'],
                ],
            );
            OrderItem::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => 1,
                    'unit_price' => $price,
                ],
            );

            foreach ($fixture['history'] as [$fromStatus, $toStatus, $actor, $notes, $changedAt]) {
                OrderStatusHistory::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'to_status' => $toStatus,
                    ],
                    [
                        'from_status' => $fromStatus,
                        'changed_by' => $actor === 'buyer' ? $review->buyer_id : $seller->user_id,
                        'notes' => $notes,
                        'created_at' => $changedAt,
                        'updated_at' => $changedAt,
                    ],
                );
            }
        }

        $this->command?->info('Seller order status demo data seeded from existing customer feedback.');
    }
}
