<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\Shipment;
use App\Models\Admin\ShipmentScan;
use App\Models\Seller\ProductReview;
use App\Models\Seller\Seller;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellerReportsSeeder extends Seeder
{
    private const REPORT_ORDER_PREFIX = 'SE-DEMO-REPORT-';

    public function run(): void
    {
        $this->call(ProductReviewSeeder::class);

        $reviewsBySeller = ProductReview::query()
            ->whereHas('product', fn ($query) => $query
                ->where('status', 'active')
                ->where('is_archived', false))
            ->with(['product', 'buyer'])
            ->orderBy('id')
            ->get()
            ->filter(fn (ProductReview $review): bool => $review->product && $review->buyer)
            ->groupBy('seller_id');

        if ($reviewsBySeller->isEmpty()) {
            $this->command?->info('No customer feedback for active products was found. Add an active seller product before seeding seller reports.');

            return;
        }

        $now = Carbon::now();
        $fixtures = $this->fixtures($now);

        foreach ($reviewsBySeller as $sellerId => $reviews) {
            $seller = Seller::query()->find($sellerId);

            if (! $seller || ! $seller->user) {
                continue;
            }

            $reviews = $reviews->values();

            foreach ($fixtures as $index => $fixture) {
                $review = $reviews[$index % $reviews->count()];
                $product = $review->product;
                $buyer = $review->buyer;
                $quantity = $fixture['quantity'];
                $total = round((float) $product->price * $quantity, 2);
                $orderNumber = self::REPORT_ORDER_PREFIX.$seller->id.'-'.$fixture['key'];
                $createdAt = $fixture['created_at'];

                $order = Order::updateOrCreate(
                    ['order_number' => $orderNumber],
                    [
                        'buyer_id' => $buyer->id,
                        'seller_id' => $seller->id,
                        'total' => $total,
                        'commission_amount' => round($total * 0.05, 2),
                        'status' => $fixture['status'],
                        'pickup_date' => in_array($fixture['status'], ['to_ship', 'in_transit', 'out_for_delivery', 'delivered', 'completed'], true)
                            ? $createdAt->copy()->addDay()->toDateString()
                            : null,
                        'pickup_time' => in_array($fixture['status'], ['to_ship', 'in_transit', 'out_for_delivery', 'delivered', 'completed'], true)
                            ? '14:00'
                            : null,
                        'delivery_name' => $buyer->name,
                        'delivery_phone' => $buyer->contact_no,
                        'delivery_address' => 'Calamba, Laguna',
                        'payment_method' => 'Cash on Delivery',
                    ],
                );

                DB::table('orders')
                    ->where('id', $order->id)
                    ->update([
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                OrderItem::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                    ],
                );

                $this->seedStatusHistory($order, $fixture, $seller, $buyer);

                if ($fixture['shipping_scans']) {
                    $this->seedShipment($order, $fixture, $createdAt, $now);
                } else {
                    Shipment::query()->where('order_id', $order->id)->delete();
                }
            }
        }

        $this->command?->info('Seller reports demo data seeded with linked orders, products, buyers, shipments, and customer feedback.');
    }

    private function fixtures(Carbon $now): array
    {
        return [
            [
                'key' => 'NEW',
                'status' => 'new',
                'created_at' => $now->copy()->subHours(2),
                'quantity' => 1,
                'history' => ['new'],
                'shipping_scans' => [],
            ],
            [
                'key' => 'PREPARING',
                'status' => 'preparing',
                'created_at' => $now->copy()->subDay(),
                'quantity' => 1,
                'history' => ['new', 'preparing'],
                'shipping_scans' => [],
            ],
            [
                'key' => 'TO-SHIP',
                'status' => 'to_ship',
                'created_at' => $now->copy()->subDays(2),
                'quantity' => 2,
                'history' => ['new', 'preparing', 'to_ship'],
                'shipping_scans' => ['to_ship'],
            ],
            [
                'key' => 'IN-TRANSIT',
                'status' => 'in_transit',
                'created_at' => $now->copy()->subDays(3),
                'quantity' => 1,
                'history' => ['new', 'preparing', 'to_ship', 'in_transit'],
                'shipping_scans' => ['to_ship', 'in_transit'],
            ],
            [
                'key' => 'OUT-FOR-DELIVERY',
                'status' => 'out_for_delivery',
                'created_at' => $now->copy()->subDays(4),
                'quantity' => 1,
                'history' => ['new', 'preparing', 'to_ship', 'in_transit', 'out_for_delivery'],
                'shipping_scans' => ['to_ship', 'in_transit', 'out_for_delivery'],
            ],
            [
                'key' => 'DELIVERED',
                'status' => 'delivered',
                'created_at' => $now->copy()->subDays(5),
                'quantity' => 1,
                'history' => ['new', 'preparing', 'to_ship', 'in_transit', 'out_for_delivery', 'delivered'],
                'shipping_scans' => ['to_ship', 'in_transit', 'out_for_delivery', 'delivered'],
            ],
            [
                'key' => 'COMPLETED-TODAY',
                'status' => 'completed',
                'created_at' => $now->copy()->startOfDay(),
                'quantity' => 2,
                'history' => ['new', 'preparing', 'to_ship', 'in_transit', 'out_for_delivery', 'delivered', 'completed'],
                'shipping_scans' => ['to_ship', 'in_transit', 'out_for_delivery', 'delivered'],
            ],
            [
                'key' => 'COMPLETED-THIS-MONTH',
                'status' => 'completed',
                'created_at' => $now->copy()->startOfMonth(),
                'quantity' => 1,
                'history' => ['new', 'preparing', 'to_ship', 'in_transit', 'out_for_delivery', 'delivered', 'completed'],
                'shipping_scans' => ['to_ship', 'in_transit', 'out_for_delivery', 'delivered'],
            ],
            [
                'key' => 'COMPLETED-PREVIOUS-MONTH',
                'status' => 'completed',
                'created_at' => $now->copy()->startOfMonth()->subDays(3),
                'quantity' => 1,
                'history' => ['new', 'preparing', 'to_ship', 'in_transit', 'out_for_delivery', 'delivered', 'completed'],
                'shipping_scans' => ['to_ship', 'in_transit', 'out_for_delivery', 'delivered'],
            ],
        ];
    }

    private function seedStatusHistory(Order $order, array $fixture, Seller $seller, $buyer): void
    {
        $statuses = $fixture['history'];
        OrderStatusHistory::query()
            ->where('order_id', $order->id)
            ->whereNotIn('to_status', $statuses)
            ->delete();

        foreach ($statuses as $index => $status) {
            $changedAt = $fixture['created_at']->copy()->addMinutes($index * 5);
            $history = OrderStatusHistory::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'to_status' => $status,
                ],
                [
                    'from_status' => $statuses[$index - 1] ?? null,
                    'changed_by' => $index === 0 ? $buyer->id : $seller->user_id,
                    'notes' => $this->statusNote($status),
                ],
            );

            DB::table('order_status_histories')
                ->where('id', $history->id)
                ->update([
                    'created_at' => $changedAt,
                    'updated_at' => $changedAt,
                ]);
        }
    }

    private function seedShipment(Order $order, array $fixture, Carbon $createdAt, Carbon $now): void
    {
        $suffix = strtoupper(substr(hash('sha256', (string) $order->order_number), 0, 12));
        $delivered = in_array($order->status, ['delivered', 'completed'], true);
        $shipment = Shipment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'tracking_number' => 'SE-RPT-'.$suffix,
                'scan_token' => hash('sha256', 'scan-'.$order->order_number),
                'courier' => 'Ease Express',
                'estimated_delivery' => $delivered
                    ? $createdAt->copy()->addDay()->toDateString()
                    : $now->copy()->addDays(3)->toDateString(),
                'shipping_fee' => 0,
                'picked_up_at' => in_array($order->status, ['in_transit', 'out_for_delivery', 'delivered', 'completed'], true)
                    ? $createdAt->copy()->addMinutes(15)
                    : null,
                'delivered_at' => $delivered ? $createdAt->copy()->addHours(2) : null,
                'current_location' => $this->shippingLocation($order->status),
            ],
        );

        ShipmentScan::query()
            ->where('shipment_id', $shipment->id)
            ->whereNotIn('status', $fixture['shipping_scans'])
            ->delete();

        foreach ($fixture['shipping_scans'] as $index => $status) {
            $scannedAt = $createdAt->copy()->addMinutes(15 + ($index * 30));
            $scan = ShipmentScan::updateOrCreate(
                [
                    'shipment_id' => $shipment->id,
                    'status' => $status,
                ],
                [
                    'location' => $this->shippingLocation($status),
                    'scanned_at' => $scannedAt,
                ],
            );
            $scan->forceFill([
                'created_at' => $scannedAt,
                'updated_at' => $scannedAt,
            ])->saveQuietly();
        }
    }

    private function statusNote(string $status): string
    {
        return match ($status) {
            'new' => 'Order placed by buyer.',
            'preparing' => 'Seller started preparing the order.',
            'to_ship' => 'Order is ready for courier pickup.',
            'in_transit' => 'Courier picked up the order.',
            'out_for_delivery' => 'Order is out for delivery.',
            'delivered' => 'Courier delivered the order.',
            'completed' => 'Order completed successfully.',
            default => 'Order status updated.',
        };
    }

    private function shippingLocation(string $status): string
    {
        return match ($status) {
            'to_ship' => 'Seller warehouse, Calamba, Laguna',
            'in_transit' => 'Sorting hub, Laguna',
            'out_for_delivery' => 'Calamba, Laguna',
            'delivered' => 'Buyer address, Calamba, Laguna',
            default => 'Calamba, Laguna',
        };
    }
}
