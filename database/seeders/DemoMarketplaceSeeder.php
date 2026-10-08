<?php

namespace Database\Seeders;

use App\Models\Admin\Admin;
use App\Models\Admin\Announcement;
use App\Models\Admin\CommissionTransaction;
use App\Models\Admin\Complaint;
use App\Models\Admin\ComplaintUpdate;
use App\Models\Admin\Order;
use App\Models\Admin\OrderCancellationRequest;
use App\Models\Admin\OrderItem;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\PlatformPolicy;
use App\Models\Admin\Registration;
use App\Models\Admin\Shipment;
use App\Models\Admin\ShipmentScan;
use App\Models\Buyer\Buyer;
use App\Models\Buyer\BuyerNotification;
use App\Models\Buyer\CartItem;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Rider\Rider;
use App\Models\Seller\Product;
use App\Models\Seller\ProductOption;
use App\Models\Seller\ProductReview;
use App\Models\Seller\ProductVariantCombination;
use App\Models\Seller\Seller;
use App\Models\Seller\SellerConversation;
use App\Models\Seller\SellerMessage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('DemoMarketplaceSeeder is restricted to local and testing environments.');
        }

        $admin = $this->user('admin@gmail.com', 'ShopEase Admin', User::ROLE_ADMIN, 'Admin@123');
        Admin::updateOrCreate(
            ['email' => $admin->email],
            [
                'first_name' => 'ShopEase',
                'last_name' => 'Administrator',
                'phone_number' => '0912345678',
                'password' => Hash::make('Admin@123'),
            ],
        );

        $buyerUser = $this->user('buyer@gmail.com', 'Demo Buyer', User::ROLE_BUYER, 'Buyer@1');
        $buyer = Buyer::updateOrCreate(
            ['user_id' => $buyerUser->id],
            [
                'first_name' => 'Demo',
                'last_name' => 'Buyer',
                'sex' => 'female',
                'contact_no' => '09170000001',
                'birthday' => '1998-04-12',
                'age' => 28,
                'province' => 'Laguna',
                'municipality' => 'Calamba',
                'barangay' => 'Real',
                'street' => 'Market Street',
                'house_number' => '12',
                'registration_status' => 'active',
                'approved_at' => now()->subDays(30),
            ],
        );

        $sellerUser = $this->user('seller@gmail.com', 'Demo Seller', User::ROLE_SELLER, 'Seller@1');
        $seller = Seller::updateOrCreate(
            ['user_id' => $sellerUser->id],
            [
                'first_name' => 'Demo',
                'last_name' => 'Seller',
                'sex' => 'male',
                'contact_no' => '09170000002',
                'birthday' => '1992-08-20',
                'age' => 34,
                'province' => 'Laguna',
                'municipality' => 'Calamba',
                'barangay' => 'Real',
                'street' => 'Commerce Avenue',
                'house_number' => '18',
                'business_name' => 'ShopEase Demo Store',
                'line_of_business' => 'Retail',
                'store_name' => 'ShopEase Demo Store',
                'registration_status' => 'active',
                'approved_at' => now()->subDays(30),
            ],
        );

        $logisticsUser = $this->user('logistics@shopease.test', 'Ease Express Logistics', User::ROLE_LOGISTICS, 'Logistics@1');
        $logistics = Logistics::updateOrCreate(
            ['user_id' => $logisticsUser->id],
            [
                'first_name' => 'Ease',
                'last_name' => 'Express',
                'sex' => 'other',
                'contact_no' => '09170000003',
                'birthday' => '1988-06-15',
                'age' => 38,
                'province' => 'Metro Manila',
                'municipality' => 'Quezon City',
                'barangay' => 'Bagong Pag-asa',
                'street' => 'Logistics Avenue',
                'house_number' => '25',
                'business_name' => 'Ease Express',
                'registration_status' => 'active',
                'approved_at' => now()->subDays(30),
            ],
        );
        $branch = LogisticsBranch::updateOrCreate(
            ['logistics_id' => $logistics->id, 'name' => 'Calamba Demo Hub'],
            [
                'address' => '25 Logistics Avenue, Calamba, Laguna',
                'contact_person' => 'Ease Express Dispatcher',
                'phone' => '09170000004',
            ],
        );

        $riderUser = $this->user('rider@shopease.test', 'Demo Rider', User::ROLE_RIDER, 'Rider@1');
        Rider::updateOrCreate(
            ['user_id' => $riderUser->id],
            [
                'logistics_branch_id' => $branch->id,
                'first_name' => 'Demo',
                'last_name' => 'Rider',
                'sex' => 'male',
                'contact_no' => '09170000005',
                'birthday' => '1995-02-10',
                'age' => 31,
                'province' => 'Laguna',
                'municipality' => 'Calamba',
                'barangay' => 'Real',
                'street' => 'Rider Road',
                'house_number' => '5',
                'vehicle' => 'Motorcycle',
                'plate_number' => 'ABC-1234',
                'registration_status' => 'active',
                'approved_at' => now()->subDays(20),
            ],
        );

        $products = $this->seedProducts($seller);
        $this->seedOrders($buyerUser, $seller, $sellerUser, $products);
        $this->seedAdminRecords($admin);
        $this->seedBuyerSellerConversation($buyerUser, $seller, $sellerUser, $products['backpack']);

        $this->command?->info('Demo marketplace accounts and connected feature data are ready.');
    }

    private function user(string $email, string $name, string $role, string $password): User
    {
        $user = User::firstOrNew(['email' => $email]);
        $user->forceFill([
            'name' => $name,
            'role' => $role,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'registration_status' => 'active',
            'approved_at' => now()->subDays(30),
        ])->save();

        return $user;
    }

    /**
     * @return array<string, Product>
     */
    private function seedProducts(Seller $seller): array
    {
        $fixtures = [
            'backpack' => [
                'sku' => 'SE-DEMO-BACKPACK',
                'name' => 'Classic Everyday Backpack',
                'category' => 'Bags',
                'price' => 1250,
                'stock' => 24,
            ],
            'earbuds' => [
                'sku' => 'SE-DEMO-EARBUDS',
                'name' => 'Wireless Earbuds Pro',
                'category' => 'Gadgets',
                'price' => 1899,
                'stock' => 12,
            ],
            'bottle' => [
                'sku' => 'SE-DEMO-BOTTLE',
                'name' => 'Insulated Water Bottle',
                'category' => 'Home & Living',
                'price' => 499,
                'stock' => 3,
            ],
            'archived' => [
                'sku' => 'SE-DEMO-ARCHIVED',
                'name' => 'Archived Demo Listing',
                'category' => 'Accessories',
                'price' => 299,
                'stock' => 0,
            ],
        ];

        $products = [];
        foreach ($fixtures as $key => $fixture) {
            $products[$key] = Product::updateOrCreate(
                ['seller_id' => $seller->id, 'sku' => $fixture['sku']],
                [
                    'name' => $fixture['name'],
                    'description' => 'Demo listing for testing ShopEase product, inventory, and checkout flows.',
                    'category' => $fixture['category'],
                    'price' => $fixture['price'],
                    'pricing_mode' => 'fixed',
                    'pricing_source' => 'base',
                    'photos' => [],
                    'stock_quantity' => $fixture['stock'],
                    'status' => 'active',
                    'is_archived' => $key === 'archived',
                    'archived_by_admin' => $key === 'archived',
                    'archive_reason' => $key === 'archived' ? 'Demo archived listing for admin and seller testing.' : null,
                    'warning_reason' => $key === 'bottle' ? 'Low stock' : null,
                    'warning_details' => $key === 'bottle' ? 'Only three units remain in demo inventory.' : null,
                ],
            );
        }

        $this->seedProductOptions($products['backpack']);
        $this->seedProductOptions($products['earbuds']);
        $products['backpack']->productSpecifications()->updateOrCreate(
            ['key' => 'Material'],
            ['value' => 'Water-resistant recycled fabric', 'sort_order' => 0],
        );
        $products['backpack']->productSpecifications()->updateOrCreate(
            ['key' => 'Capacity'],
            ['value' => '20 liters', 'sort_order' => 1],
        );

        return $products;
    }

    private function seedProductOptions(Product $product): void
    {
        $options = $product->sku === 'SE-DEMO-BACKPACK'
            ? [
                ['type' => 'color', 'name' => 'Black', 'stock' => 12],
                ['type' => 'color', 'name' => 'Navy', 'stock' => 12],
                ['type' => 'size', 'name' => 'Standard', 'stock' => 24],
                ['type' => 'variation', 'name' => 'Classic', 'stock' => 24],
            ]
            : [
                ['type' => 'color', 'name' => 'Black', 'stock' => 6],
                ['type' => 'color', 'name' => 'White', 'stock' => 6],
                ['type' => 'variation', 'name' => 'Pro', 'stock' => 12],
            ];

        foreach ($options as $index => $option) {
            ProductOption::updateOrCreate(
                ['product_id' => $product->id, 'type' => $option['type'], 'name' => $option['name']],
                ['stock' => $option['stock'], 'price' => 0, 'price_type' => 'addon', 'sort_order' => $index],
            );
        }

        foreach ([
            [
                'choices' => ['variation' => $options[array_key_last($options)]['name'], 'color' => $options[0]['name']],
                'base_price' => (float) $product->price,
                'stock' => max(1, (int) floor($product->stock_quantity / 2)),
            ],
            [
                'choices' => ['variation' => $options[array_key_last($options)]['name'], 'color' => $options[1]['name']],
                'base_price' => (float) $product->price,
                'stock' => max(1, (int) floor($product->stock_quantity / 2)),
            ],
        ] as $index => $variant) {
            ProductVariantCombination::updateOrCreate(
                ['product_id' => $product->id, 'sort_order' => $index],
                [
                    'choices' => $variant['choices'],
                    'pricing_mode' => 'fixed',
                    'pricing_source' => 'base',
                    'base_price' => $variant['base_price'],
                    'additions' => [],
                    'additional_price' => 0,
                    'final_price' => $variant['base_price'],
                    'stock' => $variant['stock'],
                    'available' => true,
                ],
            );
        }
    }

    /**
     * @param  array<string, Product>  $products
     */
    private function seedOrders(User $buyer, Seller $seller, User $sellerUser, array $products): void
    {
        $orderFixtures = [
            ['key' => 'PENDING', 'status' => 'pending', 'product' => 'backpack', 'age' => 2, 'quantity' => 1],
            ['key' => 'PREPARING', 'status' => 'preparing', 'product' => 'earbuds', 'age' => 8, 'quantity' => 1],
            ['key' => 'TO-SHIP', 'status' => 'to_ship', 'product' => 'backpack', 'age' => 30, 'quantity' => 2],
            ['key' => 'IN-TRANSIT', 'status' => 'in_transit', 'product' => 'earbuds', 'age' => 48, 'quantity' => 1],
            ['key' => 'OUT-FOR-DELIVERY', 'status' => 'out_for_delivery', 'product' => 'bottle', 'age' => 72, 'quantity' => 1],
            ['key' => 'DELIVERED', 'status' => 'delivered', 'product' => 'backpack', 'age' => 120, 'quantity' => 1],
            ['key' => 'COMPLETED', 'status' => 'completed', 'product' => 'earbuds', 'age' => 168, 'quantity' => 1],
            ['key' => 'CANCELLED', 'status' => 'cancelled', 'product' => 'bottle', 'age' => 200, 'quantity' => 1],
            ['key' => 'CANCEL-REVIEW', 'status' => 'preparing', 'product' => 'backpack', 'age' => 12, 'quantity' => 1],
        ];

        $orders = [];
        foreach ($orderFixtures as $fixture) {
            $product = $products[$fixture['product']];
            $createdAt = now()->subHours($fixture['age']);
            $orderNumber = 'SE-DEMO-BUYER-'.$fixture['key'];
            $pickupRequired = in_array($fixture['status'], ['to_ship', 'in_transit', 'out_for_delivery', 'delivered', 'completed'], true);
            $order = Order::updateOrCreate(
                ['order_number' => $orderNumber],
                [
                    'buyer_id' => $buyer->id,
                    'seller_id' => $seller->id,
                    'total' => (float) $product->price * $fixture['quantity'],
                    'status' => $fixture['status'],
                    'pickup_date' => $pickupRequired ? $createdAt->copy()->addDay()->toDateString() : null,
                    'pickup_time' => $pickupRequired ? '14:00' : null,
                    'delivery_name' => $buyer->name,
                    'delivery_phone' => '09170000001',
                    'delivery_address' => '12 Market Street, Real, Calamba, Laguna',
                    'payment_method' => 'Cash on Delivery',
                ],
            );
            $order->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();
            CommissionTransaction::syncFromOrder($order);
            OrderItem::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'variation' => $product->sku === 'SE-DEMO-BACKPACK' ? 'Classic' : null,
                    'color' => $product->sku === 'SE-DEMO-BACKPACK' ? 'Black' : null,
                    'quantity' => $fixture['quantity'],
                    'unit_price' => $product->price,
                ],
            );

            $orders[$fixture['key']] = $order;
            $this->seedOrderHistory($order, $buyer, $sellerUser, $fixture['status'], $createdAt);
            if ($pickupRequired) {
                $this->seedShipment($order, $fixture['status'], $createdAt);
            }
        }

        CartItem::updateOrCreate(
            [
                'user_id' => $buyer->id,
                'product_id' => $products['backpack']->id,
                'variation' => 'Classic',
                'color' => 'Navy',
                'size' => 'Standard',
            ],
            ['quantity' => 2],
        );
        CartItem::updateOrCreate(
            [
                'user_id' => $buyer->id,
                'product_id' => $products['bottle']->id,
                'variation' => null,
                'color' => null,
                'size' => null,
            ],
            ['quantity' => 1],
        );

        $this->seedCancellationRequest($orders['PREPARING'], $buyer, $sellerUser, 'pending');
        $rejectedRequest = $this->seedCancellationRequest($orders['CANCEL-REVIEW'], $buyer, $sellerUser, 'rejected');
        $this->notification(
            $orders['CANCEL-REVIEW'],
            'cancellation_rejected',
            'Cancellation request declined',
            'The seller declined your cancellation request. Reason: The order is already being prepared.',
            [
                'cancellation_request_id' => $rejectedRequest->id,
                'seller_reason' => 'already_being_prepared',
                'seller_other_reason' => null,
            ],
        );

        ProductReview::updateOrCreate(
            ['product_id' => $products['earbuds']->id, 'buyer_id' => $buyer->id],
            [
                'seller_id' => $seller->id,
                'rating' => 5,
                'title' => 'Demo review',
                'body' => 'Great sound quality and fast delivery. This review is seeded for product feedback testing.',
            ],
        );

        $complaint = Complaint::updateOrCreate(
            ['subject' => 'Demo complaint for delivered order'],
            [
                'user_id' => $buyer->id,
                'order_id' => $orders['DELIVERED']->id,
                'type' => 'Late Delivery',
                'description' => 'This seeded case exercises the buyer complaint and admin case-management workflow.',
                'status' => 'in_progress',
            ],
        );
        ComplaintUpdate::updateOrCreate(
            ['complaint_id' => $complaint->id, 'message' => 'ShopEase Support is reviewing the delivery timeline.'],
            ['user_id' => $buyer->id, 'type' => 'note'],
        );

        foreach ([
            ['key' => 'PENDING', 'title' => 'Order received', 'type' => 'order'],
            ['key' => 'PREPARING', 'title' => 'Order is being processed', 'type' => 'order'],
            ['key' => 'IN-TRANSIT', 'title' => 'Your package is in transit', 'type' => 'shipping'],
            ['key' => 'DELIVERED', 'title' => 'Order delivered successfully', 'type' => 'delivered'],
        ] as $fixture) {
            $order = $orders[$fixture['key']];
            $this->notification(
                $order,
                $fixture['type'],
                $fixture['title'],
                'Your order '.$order->order_number.' is now '.Str::headline($order->status).'.',
                ['status' => $order->status],
            );
        }
    }

    private function seedOrderHistory(Order $order, User $buyer, User $seller, string $status, Carbon $createdAt): void
    {
        $steps = [
            'pending' => [['pending', $buyer, 'Order placed by buyer.']],
            'preparing' => [['pending', $buyer, 'Order placed by buyer.'], ['preparing', $seller, 'Seller started preparing the order.']],
            'to_ship' => [['pending', $buyer, 'Order placed by buyer.'], ['preparing', $seller, 'Seller started preparing the order.'], ['to_ship', $seller, 'Pickup has been scheduled.']],
            'in_transit' => [['pending', $buyer, 'Order placed by buyer.'], ['preparing', $seller, 'Seller started preparing the order.'], ['to_ship', $seller, 'Pickup has been scheduled.'], ['in_transit', $seller, 'Courier picked up the package.']],
            'out_for_delivery' => [['pending', $buyer, 'Order placed by buyer.'], ['preparing', $seller, 'Seller started preparing the order.'], ['to_ship', $seller, 'Pickup has been scheduled.'], ['in_transit', $seller, 'Courier picked up the package.'], ['out_for_delivery', $seller, 'Package is out for delivery.']],
            'delivered' => [['pending', $buyer, 'Order placed by buyer.'], ['preparing', $seller, 'Seller started preparing the order.'], ['to_ship', $seller, 'Pickup has been scheduled.'], ['in_transit', $seller, 'Courier picked up the package.'], ['out_for_delivery', $seller, 'Package is out for delivery.'], ['delivered', $seller, 'Package delivered to buyer.']],
            'completed' => [['pending', $buyer, 'Order placed by buyer.'], ['preparing', $seller, 'Seller started preparing the order.'], ['to_ship', $seller, 'Pickup has been scheduled.'], ['in_transit', $seller, 'Courier picked up the package.'], ['out_for_delivery', $seller, 'Package is out for delivery.'], ['delivered', $seller, 'Package delivered to buyer.'], ['completed', $buyer, 'Buyer confirmed order completion.']],
            'cancelled' => [['pending', $buyer, 'Order placed by buyer.'], ['cancelled', $buyer, 'Demo order cancelled.']],
        ][$status];

        OrderStatusHistory::query()
            ->where('order_id', $order->id)
            ->whereNotIn('to_status', collect($steps)->pluck(0)->all())
            ->delete();

        $previous = null;
        foreach ($steps as $index => [$toStatus, $actor, $notes]) {
            OrderStatusHistory::updateOrCreate(
                ['order_id' => $order->id, 'to_status' => $toStatus],
                [
                    'from_status' => $previous,
                    'changed_by' => $actor->id,
                    'notes' => $notes,
                    'created_at' => $createdAt->copy()->addMinutes($index * 10),
                    'updated_at' => $createdAt->copy()->addMinutes($index * 10),
                ],
            );
            $previous = $toStatus;
        }
    }

    private function seedShipment(Order $order, string $status, Carbon $createdAt): void
    {
        $scanToken = substr(hash('sha256', 'demo-shipment-'.$order->order_number), 0, 64);
        $shipment = Shipment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'tracking_number' => 'SE-DEMO-'.Str::upper(substr(hash('sha256', $order->order_number), 0, 12)),
                'scan_token' => $scanToken,
                'courier' => 'Ease Express',
                'estimated_delivery' => $createdAt->copy()->addDays(3)->toDateString(),
                'shipping_fee' => 80,
                'picked_up_at' => in_array($status, ['in_transit', 'out_for_delivery', 'delivered', 'completed'], true)
                    ? $createdAt->copy()->addHours(2)
                    : null,
                'delivered_at' => in_array($status, ['delivered', 'completed'], true)
                    ? $createdAt->copy()->addDays(2)
                    : null,
                'current_location' => $status === 'to_ship' ? 'Calamba Seller Hub' : 'Calamba, Laguna',
            ],
        );

        $scans = [
            ['to_ship', 'Calamba Seller Hub', $createdAt->copy()->addHour()],
        ];
        if (in_array($status, ['in_transit', 'out_for_delivery', 'delivered', 'completed'], true)) {
            $scans[] = ['in_transit', 'Manila Sorting Center', $createdAt->copy()->addHours(3)];
        }
        if (in_array($status, ['out_for_delivery', 'delivered', 'completed'], true)) {
            $scans[] = ['out_for_delivery', 'Calamba, Laguna', $createdAt->copy()->addDay()];
        }
        if (in_array($status, ['delivered', 'completed'], true)) {
            $scans[] = ['delivered', 'Buyer delivery address', $createdAt->copy()->addDays(2)];
        }

        foreach ($scans as [$scanStatus, $location, $scannedAt]) {
            ShipmentScan::updateOrCreate(
                ['shipment_id' => $shipment->id, 'status' => $scanStatus, 'location' => $location],
                ['scanned_at' => $scannedAt],
            );
        }
    }

    private function seedCancellationRequest(Order $order, User $buyer, User $seller, string $status): OrderCancellationRequest
    {
        $requestedAt = $order->created_at->copy()->addHours(6);

        return OrderCancellationRequest::updateOrCreate(
            ['order_id' => $order->id, 'buyer_id' => $buyer->id],
            [
                'status' => $status,
                'buyer_reason' => 'changed_mind',
                'buyer_other_reason' => null,
                'auto_approved' => false,
                'decided_by' => $status === 'rejected' ? $seller->id : null,
                'seller_reason' => $status === 'rejected' ? 'already_being_prepared' : null,
                'seller_other_reason' => null,
                'requested_at' => $requestedAt,
                'decided_at' => $status === 'rejected' ? $requestedAt->copy()->addMinutes(20) : null,
            ],
        );
    }

    private function notification(Order $order, string $type, string $title, string $message, array $data): void
    {
        $notification = BuyerNotification::query()
            ->where('buyer_id', $order->buyer_id)
            ->where('order_id', $order->id)
            ->where('type', $type)
            ->where('title', $title)
            ->first() ?? new BuyerNotification;

        $notification->fill([
            'buyer_id' => $order->buyer_id,
            'order_id' => $order->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => ['order_number' => $order->order_number, ...$data],
        ])->save();
    }

    private function seedAdminRecords(User $admin): void
    {
        foreach ([
            ['email' => 'demo-registration-pending@shopease.test', 'status' => 'pending', 'type' => 'seller'],
            ['email' => 'demo-registration-approved@shopease.test', 'status' => 'approved', 'type' => 'buyer'],
            ['email' => 'demo-registration-rejected@shopease.test', 'status' => 'rejected', 'type' => 'seller'],
        ] as $index => $fixture) {
            Registration::updateOrCreate(
                ['email' => $fixture['email']],
                [
                    'user_type' => $fixture['type'],
                    'first_name' => 'Demo',
                    'last_name' => 'Applicant '.($index + 1),
                    'sex' => 'other',
                    'birthdate' => '1995-01-15',
                    'phone' => '0917111000'.($index + 1),
                    'password' => Hash::make('DemoApplicant@1'),
                    'province' => 'Laguna',
                    'municipality' => 'Calamba',
                    'barangay' => 'Real',
                    'street' => 'Applicant Street',
                    'house_no' => (string) (10 + $index),
                    'zip_code' => '4027',
                    'business_name' => $fixture['type'] === 'seller' ? 'Demo Applicant Store' : null,
                    'business_category' => $fixture['type'] === 'seller' ? 'Retail' : null,
                    'valid_id_path' => 'demo/valid-id.jpg',
                    'status' => $fixture['status'],
                    'rejection_reason' => $fixture['status'] === 'rejected' ? 'invalid_document' : null,
                    'rejection_details' => $fixture['status'] === 'rejected' ? 'Demo rejected application for workflow testing.' : null,
                    'reviewed_by' => $fixture['status'] === 'pending' ? null : $admin->id,
                    'reviewed_at' => $fixture['status'] === 'pending' ? null : now()->subDays(2),
                ],
            );
        }

        foreach ([
            [
                'title' => 'Welcome to ShopEase Demo',
                'type' => 'Announcement',
                'body' => 'Explore products, place a test order, and follow its delivery from your buyer account.',
                'audience' => 'All Users',
                'badge_label' => 'Demo',
            ],
            [
                'title' => 'Seller inventory reminders',
                'type' => 'Announcement',
                'body' => 'Keep product stock and listing information up to date.',
                'audience' => 'Sellers',
                'badge_label' => 'Seller update',
            ],
        ] as $fixture) {
            Announcement::updateOrCreate(
                ['title' => $fixture['title']],
                [
                    ...$fixture,
                    'status' => 'Published',
                    'published_at' => now()->subDay(),
                    'created_by' => $admin->id,
                    'is_active' => true,
                ],
            );
        }

        foreach ([
            ['title' => 'Buyer Purchase Policy', 'category' => 'Buyer', 'description' => 'Demo buyer purchase and cancellation guidance.'],
            ['title' => 'Seller Listing Policy', 'category' => 'Seller', 'description' => 'Demo seller product listing and inventory guidance.'],
        ] as $fixture) {
            PlatformPolicy::updateOrCreate(
                ['title' => $fixture['title']],
                [
                    ...$fixture,
                    'content' => $fixture['description'].' These seeded policy records are for development and testing.',
                    'status' => 'Published',
                    'published_at' => now()->subDay(),
                    'created_by' => $admin->id,
                ],
            );
        }
    }

    private function seedBuyerSellerConversation(User $buyer, Seller $seller, User $sellerUser, Product $product): void
    {
        $order = Order::query()->where('order_number', 'SE-DEMO-BUYER-PREPARING')->firstOrFail();
        $conversation = SellerConversation::updateOrCreate(
            ['seed_key' => 'demo-marketplace-buyer-conversation'],
            [
                'seller_id' => $seller->id,
                'type' => SellerConversation::TYPE_BUYER,
                'buyer_id' => $buyer->id,
                'order_id' => $order->id,
                'product_id' => $product->id,
            ],
        );

        foreach ([
            ['key' => 'demo-marketplace-message-1', 'sender' => $buyer, 'body' => 'Hi! Can you confirm whether this item is available?', 'read_at' => now()],
            ['key' => 'demo-marketplace-message-2', 'sender' => $sellerUser, 'body' => 'Hi! Yes, it is available and your order is being prepared.', 'read_at' => null],
        ] as $message) {
            SellerMessage::updateOrCreate(
                ['seed_key' => $message['key']],
                [
                    'conversation_id' => $conversation->id,
                    'sender_id' => $message['sender']->id,
                    'body' => $message['body'],
                    'read_at' => $message['read_at'],
                ],
            );
        }
    }
}
