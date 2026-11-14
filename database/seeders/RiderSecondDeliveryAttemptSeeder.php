<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\Shipment;
use App\Models\Buyer\Buyer;
use App\Models\Rider\DeliveryAttempt;
use App\Models\Rider\Rider;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds a COD parcel with its first failed attempt so the assigned rider can record attempt two.
 * Run with: php artisan db:seed --class=RiderSecondDeliveryAttemptSeeder
 */
class RiderSecondDeliveryAttemptSeeder extends Seeder
{
    private const ORDER_NUMBER = 'SE-RIDER-ATTEMPT2-DEMO-0001';
    private const TRACKING_NUMBER = 'TRK-RIDER-ATTEMPT2-DEMO-0001';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('RiderSecondDeliveryAttemptSeeder is restricted to local and testing environments.');
        }

        $this->call(RiderAccountSeeder::class);
        $rider = Rider::query()
            ->whereHas('user', fn ($query) => $query->where('email', 'rider@gmail.com'))
            ->firstOrFail();
        $password = Hash::make(env('RIDER_SECOND_ATTEMPT_DEMO_PASSWORD', 'Demo@12345'));

        $order = DB::transaction(function () use ($rider, $password): Order {
            $sellerUser = $this->user(
                'seller.rider-attempt2@shopease.test',
                'Second Attempt Demo Seller',
                User::ROLE_SELLER,
                $password,
            );
            $seller = Seller::query()->firstOrCreate(['user_id' => $sellerUser->id], [
                'first_name' => 'Second Attempt',
                'last_name' => 'Seller',
                'contact_no' => '09170002021',
                'province' => 'Metro Manila',
                'municipality' => 'Quezon City',
                'barangay' => 'Demo',
                'street' => 'Seller Demo Street',
                'store_name' => 'Second Attempt Demo Shop',
                'business_name' => 'Second Attempt Demo Shop',
                'registration_status' => 'active',
                'approved_at' => now(),
            ]);

            $buyerUser = $this->user(
                'buyer.rider-attempt2@shopease.test',
                'Second Attempt Demo Buyer',
                User::ROLE_BUYER,
                $password,
            );
            Buyer::query()->firstOrCreate(['user_id' => $buyerUser->id], [
                'first_name' => 'Second Attempt',
                'last_name' => 'Buyer',
                'sex' => 'other',
                'contact_no' => '09170002022',
                'birthday' => '1995-01-01',
                'age' => 31,
                'province' => 'Metro Manila',
                'municipality' => 'Quezon City',
                'barangay' => 'Demo',
                'street' => 'Buyer Demo Street',
                'house_number' => '2',
                'registration_status' => 'active',
                'approved_at' => now(),
            ]);

            $product = Product::query()->firstOrCreate(
                ['seller_id' => $seller->id, 'sku' => 'RIDER-ATTEMPT2-DEMO-001'],
                [
                    'name' => 'Rider Attempt Test Parcel',
                    'description' => 'Demo item for testing a second failed delivery attempt.',
                    'category' => 'Home',
                    'price' => 950,
                    'stock_quantity' => 9,
                    'status' => 'active',
                    'is_archived' => false,
                ],
            );

            $order = Order::query()->firstOrCreate(['order_number' => self::ORDER_NUMBER], [
                'buyer_id' => $buyerUser->id,
                'seller_id' => $seller->id,
                'logistics_id' => $rider->logisticsBranch?->logistics_id,
                'total' => 1000,
                'shipping_fee' => 50,
                'status' => 'out_for_delivery',
                'delivery_name' => $buyerUser->name,
                'delivery_phone' => '09170002022',
                'delivery_address' => '2 Buyer Demo Street, Demo, Quezon City, Metro Manila',
                'delivery_barangay' => 'Demo',
                'delivery_municipality' => 'Quezon City',
                'delivery_province' => 'Metro Manila',
                'payment_method' => 'Cash on Delivery',
            ]);

            OrderItem::query()->firstOrCreate(
                ['order_id' => $order->id, 'product_id' => $product->id],
                [
                    'product_name' => $product->name,
                    'quantity' => 1,
                    'unit_price' => 950,
                ],
            );

            Shipment::query()->firstOrCreate(['order_id' => $order->id], [
                'rider_id' => $rider->id,
                'tracking_number' => self::TRACKING_NUMBER,
                'courier' => $rider->courier_company ?: 'ShopEase Logistics',
                'estimated_delivery' => now()->toDateString(),
                'shipping_fee' => 50,
                'current_location' => $rider->municipal_hub ?: 'Out for delivery',
                'logistics_branch_id' => $rider->logistics_branch_id,
            ]);

            DeliveryAttempt::query()->firstOrCreate(
                ['order_id' => $order->id, 'attempt_number' => 1],
                [
                    'rider_id' => $rider->id,
                    'reason' => 'Buyer did not answer',
                    'notes' => 'Seeded first attempt. Record the next failed delivery attempt from the rider account.',
                    'evidence_path' => 'seeded/rider-second-attempt/attempt-1.jpg',
                ],
            );

            return $order;
        });

        $this->command?->info('Second-attempt parcel is ready: '.$order->order_number.' (tracking '.self::TRACKING_NUMBER.').');
        $this->command?->info('Sign in as rider@gmail.com; RIDER_SEED_PASSWORD defaults to Rider@123.');
        $this->command?->info('One failed attempt is seeded. The next failed-delivery submission will be attempt 2.');
    }

    private function user(string $email, string $name, string $role, string $password): User
    {
        return User::query()->firstOrCreate(['email' => $email], [
            'name' => $name,
            'password' => $password,
            'role' => $role,
            'email_verified_at' => now(),
            'registration_status' => 'active',
            'approved_at' => now(),
        ]);
    }
}
