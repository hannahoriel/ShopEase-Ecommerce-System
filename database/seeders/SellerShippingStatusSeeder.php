<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Admin\Shipment;
use Illuminate\Database\Seeder;

class SellerShippingStatusSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::query()
            ->whereIn('status', ['to_ship', 'in_transit', 'out_for_delivery', 'delivered'])
            ->where('order_number', 'like', 'SE-DEMO-ORDER-STATUS-%')
            ->get();

        foreach ($orders as $order) {
            $trackingSuffix = strtoupper(substr(hash('sha256', (string) $order->order_number), 0, 12));
            $estimatedDelivery = $order->pickup_date?->copy()->addDay()->toDateString()
                ?? now()->addDays(3)->toDateString();

            Shipment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'tracking_number' => "SE-{$trackingSuffix}",
                    'courier' => 'Ease Express',
                    'estimated_delivery' => $estimatedDelivery,
                    'shipping_fee' => 0,
                    'picked_up_at' => in_array($order->status, ['in_transit', 'out_for_delivery', 'delivered'], true)
                        ? $order->updated_at
                        : null,
                    'delivered_at' => $order->status === 'delivered' ? $order->updated_at : null,
                ],
            );
        }

        $this->command?->info("Seeded shipping records from {$orders->count()} existing shipping-stage orders.");
    }
}
