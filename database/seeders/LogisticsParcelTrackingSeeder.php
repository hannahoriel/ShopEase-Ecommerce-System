<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\OrderReturn;
use App\Models\Admin\OrderReturnEvent;
use App\Models\Admin\Shipment;
use App\Models\Admin\ShipmentScan;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LogisticsParcelTrackingSeeder extends Seeder
{
    public function run(): void
    {
        $logisticsUser = User::query()->updateOrCreate(
            ['email' => 'tracking.logistics@shopease.test'],
            ['name' => 'Parcel Tracking Demo Logistics', 'role' => User::ROLE_LOGISTICS,
                'registration_status' => 'active', 'email_verified_at' => now(),
                'password' => Hash::make('password')],
        );
        $logistics = Logistics::query()->updateOrCreate(
            ['user_id' => $logisticsUser->id],
            ['last_name' => 'Demo', 'first_name' => 'Logistics', 'sex' => 'other',
                'contact_no' => '09170000000', 'birthday' => '1990-01-01', 'age' => 36,
                'province' => 'Laguna', 'municipality' => 'Santa Cruz', 'barangay' => 'Poblacion',
                'street' => 'Demo Road', 'house_number' => '1', 'business_name' => 'ShopEase Demo Logistics',
                'registration_status' => 'active', 'approved_at' => now()],
        );
        $branch = LogisticsBranch::query()->updateOrCreate(
            ['logistics_id' => $logistics->id, 'name' => 'Laguna Sorting Center'],
            ['address' => 'Demo Road, Santa Cruz, Laguna', 'contact_person' => 'Demo Manager',
                'phone' => '09170000000', 'province' => 'Laguna', 'municipality' => 'Santa Cruz',
                'barangay' => 'Poblacion', 'postal_code' => '4009', 'street' => 'Demo Road',
                'manager_email' => 'manager.tracking@shopease.test', 'login_email' => 'center.tracking@shopease.test',
                'status' => 'active'],
        );
        $hub = LogisticsDeliveryHub::query()->updateOrCreate(
            ['logistics_branch_id' => $branch->id, 'hub_name' => 'Santa Cruz Delivery Hub'],
            ['province' => 'Laguna', 'municipality' => 'Santa Cruz', 'barangay' => 'Poblacion',
                'postal_code' => '4009', 'street' => 'Demo Road', 'manager_name' => 'Demo Hub Manager',
                'manager_email' => 'hub.manager@shopease.test', 'manager_phone' => '09170000001',
                'login_email' => 'hub.tracking@shopease.test', 'status' => 'active'],
        );

        $buyer = User::query()->updateOrCreate(
            ['email' => 'buyer.tracking@shopease.test'],
            ['name' => 'Alex Rivera', 'role' => User::ROLE_BUYER, 'registration_status' => 'active',
                'email_verified_at' => now(), 'password' => Hash::make('password')],
        );
        $sellerUser = User::query()->updateOrCreate(
            ['email' => 'seller.tracking@shopease.test'],
            ['name' => 'Demo Store Owner', 'role' => User::ROLE_SELLER, 'registration_status' => 'active',
                'email_verified_at' => now(), 'password' => Hash::make('password')],
        );
        $seller = Seller::query()->updateOrCreate(
            ['user_id' => $sellerUser->id],
            ['first_name' => 'Demo', 'last_name' => 'Seller', 'contact_no' => '09170000002',
                'province' => 'Laguna', 'municipality' => 'Calamba City', 'barangay' => 'Canlubang',
                'street' => 'Market Road', 'house_number' => '12', 'store_name' => 'Everyday Basics',
                'business_name' => 'Everyday Basics', 'registration_status' => 'active', 'approved_at' => now()],
        );

        $fixtures = [
            ['order' => 'DEMO-TRACK-1001', 'status' => 'in_transit', 'tracking' => 'DEMO-TRK-1001', 'location' => 'Laguna Sorting Center'],
            ['order' => 'DEMO-TRACK-1002', 'status' => 'out_for_delivery', 'tracking' => 'DEMO-TRK-1002', 'location' => 'Santa Cruz Delivery Hub'],
            ['order' => 'DEMO-TRACK-1003', 'status' => 'delivered', 'tracking' => 'DEMO-TRK-1003', 'location' => 'Delivered to recipient'],
        ];

        foreach ($fixtures as $index => $fixture) {
            $order = Order::query()->updateOrCreate(
                ['order_number' => $fixture['order']],
                ['buyer_id' => $buyer->id, 'seller_id' => $seller->id, 'total' => 1599 + ($index * 100),
                    'status' => $fixture['status'], 'delivery_name' => 'Alex Rivera',
                    'delivery_phone' => '09170000003', 'delivery_address' => '24 Rizal Street, Santa Cruz, Laguna',
                    'payment_method' => 'Cash on Delivery'],
            );
            OrderItem::query()->updateOrCreate(
                ['order_id' => $order->id, 'product_name' => 'Wireless Headphones'],
                ['quantity' => 1, 'unit_price' => 1599 + ($index * 100), 'variation' => 'Bluetooth 5.3',
                    'color' => 'Black', 'size' => 'Standard'],
            );
            $shipment = Shipment::query()->updateOrCreate(
                ['order_id' => $order->id],
                ['tracking_number' => $fixture['tracking'], 'courier' => 'ShopEase Logistics',
                    'estimated_delivery' => now()->addDays($index + 1)->toDateString(),
                    'current_location' => $fixture['location'], 'logistics_branch_id' => $branch->id,
                    'logistics_delivery_hub_id' => $hub->id,
                    'picked_up_at' => now()->subHours(5),
                    'delivered_at' => $fixture['status'] === 'delivered' ? now()->subHour() : null],
            );

            $scans = [
                ['status' => 'to_ship', 'location' => 'Seller Warehouse', 'scanned_at' => now()->subHours(8)],
                ['status' => $fixture['status'], 'location' => $fixture['location'], 'scanned_at' => now()->subHours(2)],
            ];
            foreach ($scans as $scan) {
                ShipmentScan::query()->updateOrCreate(
                    ['shipment_id' => $shipment->id, 'status' => $scan['status']],
                    ['location' => $scan['location'], 'scanned_at' => $scan['scanned_at']],
                );
            }
        }

        $pendingOrder = Order::query()->where('order_number', 'DEMO-TRACK-1001')->firstOrFail();
        $pendingReturn = OrderReturn::query()->updateOrCreate(
            ['tracking_number' => 'DEMO-RET-1001'],
            ['order_id' => $pendingOrder->id, 'return_type' => 'return', 'status' => 'pending',
                'shipping_status' => 'return-requested', 'reason' => 'Incorrect size',
                'solution' => 'Awaiting return pickup', 'return_items' => [[
                    'product_name' => 'Wireless Headphones', 'variation' => 'Bluetooth 5.3',
                    'color' => 'Black', 'size' => 'Standard', 'quantity' => 1, 'product_price' => 1599,
                ]], 'requested_at' => now()->subDay(), 'courier' => 'ShopEase Logistics'],
        );
        OrderReturnEvent::query()->updateOrCreate(
            ['order_return_id' => $pendingReturn->id, 'status' => 'return-requested'],
            ['location' => 'Santa Cruz Delivery Hub', 'scanned_at' => now()->subDay()],
        );

        $returnedOrder = Order::query()->where('order_number', 'DEMO-TRACK-1003')->firstOrFail();
        $returned = OrderReturn::query()->updateOrCreate(
            ['tracking_number' => 'DEMO-RET-1003'],
            ['order_id' => $returnedOrder->id, 'return_type' => 'return', 'status' => 'refunded',
                'shipping_status' => 'returned', 'reason' => 'Damaged packaging',
                'solution' => 'Returned to seller warehouse', 'return_items' => [[
                    'product_name' => 'Wireless Headphones', 'quantity' => 1, 'product_price' => 1799,
                ]], 'refund_amount' => 1799, 'reviewed_by' => $sellerUser->id,
                'reviewed_at' => now()->subDays(2), 'refunded_at' => now()->subHour(),
                'requested_at' => now()->subDays(3), 'returned_at' => now()->subDay(),
                'courier' => 'ShopEase Logistics'],
        );
        OrderReturnEvent::query()->updateOrCreate(
            ['order_return_id' => $returned->id, 'status' => 'returned'],
            ['location' => 'Calamba City Seller Warehouse', 'scanned_at' => now()->subDay()],
        );

        OrderReturnEvent::query()->updateOrCreate(
            ['order_return_id' => $returned->id, 'status' => 'refunded'],
            ['location' => 'Seller refund processing', 'scanned_at' => now()->subHour()],
        );
    }
}
