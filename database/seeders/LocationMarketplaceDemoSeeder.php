<?php

namespace Database\Seeders;

use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\OrderReturn;
use App\Models\Admin\OrderReturnEvent;
use App\Models\Admin\Shipment;
use App\Models\Buyer\Buyer;
use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\DeliveryHubParcelIssue;
use App\Models\Logistics\DeliveryHubRiderRegistration;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsBranchRoute;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\LogisticsTransferManifest;
use App\Models\Logistics\SortingCenterParcelIssue;
use App\Models\Logistics\SortingCenterVehicle;
use App\Models\Rider\Rider;
use App\Models\Seller\Product;
use App\Models\Seller\ProductReview;
use App\Models\Seller\Seller;
use App\Models\User;
use App\Services\ShippingFeeCalculator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LocationMarketplaceDemoSeeder extends Seeder
{
    private const PASSWORD_ENV = 'LOCATION_MARKETPLACE_DEMO_PASSWORD';

    private string $password;

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('LocationMarketplaceDemoSeeder is restricted to local and testing environments.');
        }

        $this->password = Hash::make(env(self::PASSWORD_ENV, 'Demo@12345'));
        $this->call([EcommerceRouteDemoSeeder::class, BulacanRouteDemoSeeder::class]);

        $locations = $this->locations();
        $riders = [];
        $couriers = [];
        foreach ($locations as $key => &$location) {
            $location['center'] = $location['hub']->logisticsBranch;
            $location['riders'] = $this->riders($key, $location['center'], $location['hub'], $location['barangay']);
            $location['courier'] = $this->courier($key, $location['center']);
            $riders[$key] = $location['riders'];
            $couriers[$key] = $location['courier'];
        }
        unset($location);

        $sellers = [];
        $products = [];
        $orders = [];
        foreach ($locations as $key => $location) {
            $sellers[$key] = $this->seller($key, $location);
            $products[$key] = $this->products($key, $sellers[$key]);
            $orders[$key] = $this->orders($key, $locations, $location, $sellers[$key], $products[$key], $riders);
            $this->productReviews($products[$key], $orders[$key], $sellers[$key]);
        }

        foreach (array_keys($locations) as $index => $key) {
            $previousKey = array_keys($locations)[($index + count($locations) - 1) % count($locations)];
            $nextKey = array_keys($locations)[($index + 1) % count($locations)];
            $inbound = $this->sortingCenterArrival($previousKey, $key, $locations, $orders);
            $this->stageReturnCargo($key, $previousKey, $locations, $orders, $inbound);
            $this->parcelReports($key, $location = $locations[$key], $orders[$key]);
            $this->returnAndRefundRecords($key, $locations, $orders[$key], $riders);
            $this->hubArrival($key, $locations, $orders[$key], $couriers[$key]);
            $this->seedRoutes($locations[$key]['center'], $locations[$nextKey]['center']);
        }

        $this->command?->info('Location Marketplace demo ready: 3 shops with 20 orders each, product photos, local pickup/delivery riders, parcel issues, returns/refunds, and inbound return-cargo trips.');
        $this->command?->info('Seller logins: demo.{manila|calamba|bulacan}.seller@shopease.test');
        $this->command?->info('Buyer logins: demo.{manila|calamba|bulacan}.buyer01..buyer20@shopease.test');
        $this->command?->info('All seeded users use '.self::PASSWORD_ENV.' (default: Demo@12345).');
        $this->command?->info('Riders: demo.{manila|calamba|bulacan}.{pickup|delivery}@shopease.test; Couriers: demo.{manila|calamba|bulacan}.courier@shopease.test.');
    }

    private function locations(): array
    {
        $fixtures = [
            'manila' => ['province' => 'Metro Manila', 'municipality' => 'Manila', 'barangay' => 'Intramuros', 'hub' => 'Manila Delivery Hub', 'center' => 'Manila Sorting Center', 'postal' => '1002'],
            'calamba' => ['province' => 'Laguna', 'municipality' => 'Calamba City', 'barangay' => 'Real', 'hub' => 'Calamba Delivery Hub', 'center' => 'Calamba Sorting Center', 'postal' => '4027'],
            'bulacan' => ['province' => 'Bulacan', 'municipality' => 'Guiguinto', 'barangay' => 'Tuktukan', 'hub' => 'Guiguinto Delivery Hub', 'center' => 'Bulacan Main Sorting Center', 'postal' => '3015'],
        ];
        $locations = [];

        foreach ($fixtures as $key => $fixture) {
            $hub = LogisticsDeliveryHub::query()->where('status', 'active')->whereNull('archived_at')
                ->whereRaw('LOWER(province) = ?', [mb_strtolower($fixture['province'])])
                ->get()->first(fn (LogisticsDeliveryHub $candidate): bool => $this->samePlace($candidate->municipality, $fixture['municipality']));

            if (! $hub) {
                $center = $this->center($fixture);
                $hubUser = $this->user('demo.'.$key.'.hub@shopease.test', ucfirst($key).' Delivery Hub', User::ROLE_DELIVERY_HUB);
                $hub = LogisticsDeliveryHub::query()->updateOrCreate(
                    ['logistics_branch_id' => $center->id, 'hub_name' => $fixture['hub']],
                    ['user_id' => $hubUser->id, 'province' => $fixture['province'], 'municipality' => $fixture['municipality'],
                        'barangay' => $fixture['barangay'], 'postal_code' => $fixture['postal'], 'street' => 'Market Service Road',
                        'manager_name' => ucfirst($key).' Demo Hub Manager', 'manager_email' => $hubUser->email,
                        'manager_phone' => '09170007001', 'login_email' => $hubUser->email, 'status' => 'active', 'archived_at' => null],
                );
            }

            $locations[$key] = [...$fixture, 'hub' => $hub];
        }

        return $locations;
    }

    private function center(array $location): LogisticsBranch
    {
        $center = LogisticsBranch::query()->where('status', 'active')->whereNull('archived_at')
            ->whereRaw('LOWER(province) = ?', [mb_strtolower($location['province'])])->first();
        if ($center) return $center;

        $network = Logistics::query()->where('registration_status', 'active')->orderBy('id')->firstOrFail();
        $email = 'demo.'.Str::slug($location['center']).'.center@shopease.test';
        $manager = $this->user($email, $location['center'].' Manager', User::ROLE_SORTING_CENTER);

        return LogisticsBranch::query()->updateOrCreate(
            ['logistics_id' => $network->id, 'name' => $location['center']],
            ['user_id' => $manager->id, 'address' => $location['barangay'].', '.$location['municipality'].', '.$location['province'],
                'contact_person' => $location['center'].' Manager', 'phone' => '09170007002', 'province' => $location['province'],
                'municipality' => $location['municipality'], 'barangay' => $location['barangay'], 'postal_code' => $location['postal'],
                'street' => 'Market Service Road', 'manager_email' => $manager->email, 'login_email' => $manager->email,
                'status' => 'active', 'archived_at' => null],
        );
    }

    private function seller(string $key, array $location): Seller
    {
        $first = match ($key) {'manila' => 'Mika', 'calamba' => 'Lara', default => 'Leah'};
        $last = match ($key) {'manila' => 'Santos', 'calamba' => 'Reyes', default => 'Mendoza'};
        $email = 'demo.'.$key.'.seller@shopease.test';
        $user = $this->user($email, $first.' '.$last, User::ROLE_SELLER);

        return Seller::query()->updateOrCreate(['user_id' => $user->id], [
            'first_name' => $first, 'last_name' => $last, 'sex' => 'other', 'contact_no' => '0917000701'.array_search($key, ['manila', 'calamba', 'bulacan'], true),
            'birthday' => '1993-06-12', 'age' => 33, 'province' => $location['province'], 'municipality' => $location['municipality'],
            'barangay' => $location['barangay'], 'street' => 'Commerce Avenue', 'house_number' => '18',
            'business_name' => ucfirst($key).' Market Shop', 'store_name' => ucfirst($key).' Market Shop',
            'line_of_business' => 'Retail', 'registration_status' => 'active', 'approved_at' => now(),
        ]);
    }

    private function products(string $key, Seller $seller): array
    {
        $fixtures = [
            'manila' => [
                ['Everyday Linen Shirt', "Women's Apparel", 899, 0.6, 'photo-1598033129183-c4f50c736f10'],
                ['City Crossbody Bag', 'Bags & Travel', 1299, 0.8, 'photo-1548036328-c9fa89d128fa'],
                ['Ceramic Coffee Set', 'Home & Living', 749, 1.4, 'photo-1495474472287-4d71bcdd2085'],
                ['Wireless Earbuds', 'Electronics', 1599, 0.3, 'photo-1505740420928-5e560c06d30e'],
                ['Daily Skin Care Kit', 'Beauty', 599, 0.7, 'photo-1608248543803-ba4f8c70ae0b'],
                ['Minimalist Gold Earrings', 'Jewelry', 499, 0.1, 'photo-1535632066927-ab7c9ab60908'],
                ['Canvas Notebook Set', 'Office & School', 299, 0.5, 'photo-1531346878377-a5be20888e57'],
                ['Insulated Travel Mug', 'Kitchen & Dining', 459, 0.6, 'photo-1514228742587-6b1558fcca3d'],
                ['Classic Leather Wallet', 'Accessories', 699, 0.2, 'photo-1627123424574-724758594e93'],
                ['Soft Knit Cardigan', "Women's Apparel", 1099, 0.8, 'photo-1576566588028-4147f3842f27'],
                ['Compact Desk Lamp', 'Home & Living', 579, 1.0, 'photo-1507473885765-e6ed057f782c'],
                ['Reusable Makeup Pads', 'Beauty', 249, 0.2, 'photo-1596462502278-27bfdc403348'],
                ['Travel Organizer Pouch', 'Bags & Travel', 389, 0.3, 'photo-1553062407-98eeb64c6a62'],
                ['Bluetooth Speaker Mini', 'Electronics', 999, 0.7, 'photo-1608043152269-423dbba4e7e1'],
                ['Stainless Water Bottle', 'Sports & Outdoors', 429, 0.5, 'photo-1602143407151-7111542de6e8'],
                ['Printed Cotton Scarf', "Women's Apparel", 349, 0.2, 'photo-1601924994987-69e26d50dc26'],
                ['Scented Candle Trio', 'Home & Living', 529, 1.1, 'photo-1603006905003-be475563bc59'],
                ['Phone Stand Aluminum', 'Electronics', 279, 0.3, 'photo-1586953208448-b95a79798f07'],
                ['Daily Planner 2026', 'Office & School', 319, 0.4, 'photo-1506784983877-45594efa4cbe'],
            ],
            'calamba' => [
                ['Kitchen Storage Set', 'Kitchen & Dining', 649, 1.2, 'photo-1556911220-e15b29be8c8f'],
                ['Trail Running Bottle', 'Sports & Outdoors', 399, 0.5, 'photo-1602143407151-7111542de6e8'],
                ['Pet Comfort Bed', 'Pet Supplies', 1099, 2.1, 'photo-1543466835-00a7907e9de1'],
                ['Garden Tool Kit', 'Home & Garden', 899, 2.8, 'photo-1416879595882-3373a0480b5b'],
                ['Baby Blanket Set', 'Baby & Kids', 799, 0.9, 'photo-1516627145497-ae6968895b74'],
                ['Bamboo Cutting Board', 'Kitchen & Dining', 349, 0.8, 'photo-1593618998160-e34014e67546'],
                ['Resistance Band Set', 'Sports & Outdoors', 299, 0.4, 'photo-1598289431512-b97b0917affc'],
                ['Cat Scratching Lounge', 'Pet Supplies', 749, 1.7, 'photo-1545249390-6bdfa286032f'],
                ['Herb Garden Starter Kit', 'Home & Garden', 599, 1.3, 'photo-1466692476868-aef1dfb1e735'],
                ['Kids Lunch Box Set', 'Baby & Kids', 429, 0.6, 'photo-1607877742574-a9c6c6f0b8d4'],
                ['Glass Food Container Set', 'Kitchen & Dining', 699, 1.8, 'photo-1603199506016-b9a594b593c0'],
                ['Yoga Mat Lightweight', 'Sports & Outdoors', 899, 1.4, 'photo-1592432678016-e910b452f9a2'],
                ['Dog Walking Harness', 'Pet Supplies', 549, 0.5, 'photo-1601758228041-f3b2795255f1'],
                ['Outdoor Solar Lantern', 'Home & Garden', 479, 0.7, 'photo-1513506003901-1e6a229e2d15'],
                ['Soft Plush Toy Bear', 'Baby & Kids', 389, 0.4, 'photo-1559454403-b8fb88521f11'],
                ['Nonstick Frying Pan', 'Kitchen & Dining', 899, 1.5, 'photo-1584990347449-a9f16d4b5e2e'],
                ['Fitness Jump Rope', 'Sports & Outdoors', 259, 0.3, 'photo-1434596922112-19c563067271'],
                ['Pet Travel Water Bottle', 'Pet Supplies', 329, 0.4, 'photo-1583337130417-3346a1be7dee'],
                ['Ceramic Planter Pair', 'Home & Garden', 519, 1.6, 'photo-1485955900006-10f4d324d411'],
                ['Baby Silicone Feeding Set', 'Baby & Kids', 649, 0.7, 'photo-1584515933487-779824d29309'],
            ],
            'bulacan' => [
                ['Precision Tool Set', 'Tools & Hardware', 1199, 3.2, 'photo-1504148455328-c376907d081c'],
                ['Car Care Bundle', 'Automotive', 849, 2.0, 'photo-1486262715619-67b85e0b08d3'],
                ['Handwoven Market Tote', 'Handicrafts', 549, 0.7, 'photo-1544816155-12df9643f363'],
                ['Pantry Essentials Box', 'Grocery', 699, 4.0, 'photo-1542838132-92c53300491e'],
                ['Cotton Home Towel Set', 'Home & Living', 499, 1.1, 'photo-1728034261564-18930dcb2c8e'],
                ['Heavy Duty Screwdriver Kit', 'Tools & Hardware', 699, 1.8, 'photo-1581147036324-c1c89c2c8b5c'],
                ['Motorcycle Phone Mount', 'Automotive', 399, 0.5, 'photo-1503376780353-7e6692767b70'],
                ['Woven Placemat Set', 'Handicrafts', 429, 0.7, 'photo-1603199506016-b9a594b593c0'],
                ['Dried Mango Snack Pack', 'Grocery', 299, 0.6, 'photo-1600271886742-f049cd451bba'],
                ['Bamboo Laundry Basket', 'Home & Living', 899, 2.3, 'photo-1631679706909-1844bbd07221'],
                ['Adjustable Wrench Pair', 'Tools & Hardware', 579, 1.2, 'photo-1530124566582-a618bc2615dc'],
                ['Car Seat Organizer', 'Automotive', 649, 0.9, 'photo-1503376780353-7e6692767b70'],
                ['Handmade Abaca Coasters', 'Handicrafts', 249, 0.3, 'photo-1603199506016-b9a594b593c0'],
                ['Local Coffee Bean Bag', 'Grocery', 459, 0.5, 'photo-1445116572660-236099ec97a0'],
                ['Woven Storage Basket', 'Home & Living', 699, 1.4, 'photo-1593085260707-5377ba37f868'],
                ['Cordless Drill Accessory Set', 'Tools & Hardware', 999, 2.0, 'photo-1504148455328-c376907d081c'],
                ['Microfiber Car Cloth Pack', 'Automotive', 279, 0.4, 'photo-1486262715619-67b85e0b08d3'],
                ['Handwoven Table Runner', 'Handicrafts', 599, 0.8, 'photo-1601924994987-69e26d50dc26'],
                ['Dried Pineapple Bites', 'Grocery', 329, 0.5, 'photo-1600271886742-f049cd451bba'],
                ['Cotton Cushion Cover Pair', 'Home & Living', 549, 0.6, 'photo-1616486338812-3dadae4b4ace'],
            ],
        ];

        $products = [];
        foreach ($fixtures[$key] as $index => [$name, $category, $price, $weight, $photo]) {
            if (! str_starts_with($photo, 'photo-')) {
                throw new \RuntimeException('A product photo is missing for '.$name.'.');
            }
            $sku = 'LOC-DEMO-'.strtoupper($key).'-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
            $product = Product::query()->updateOrCreate(
                ['seller_id' => $seller->id, 'sku' => $sku],
                ['name' => $name, 'description' => $name.' from '.ucfirst($key).' Market Shop. Local demo product for catalog and checkout testing.',
                    'category' => $category, 'price' => $price, 'weight_kg' => $weight, 'pricing_mode' => 'fixed', 'pricing_source' => 'base',
                    'photos' => ['https://images.unsplash.com/'.$photo.'?auto=format&fit=crop&w=900&q=85'],
                    'stock_quantity' => 40, 'status' => 'active', 'is_archived' => false, 'archived_by_admin' => false],
            );
            foreach (Product::query()->where('seller_id', $seller->id)->where('name', $name)->where('id', '<>', $product->id)->get() as $duplicate) {
                OrderItem::query()->where('product_id', $duplicate->id)->update(['product_id' => $product->id, 'product_name' => $product->name]);
                foreach ($duplicate->reviews as $review) {
                    $existingReview = ProductReview::query()->where('product_id', $product->id)->where('buyer_id', $review->buyer_id)->first();
                    if ($existingReview) {
                        $review->delete();
                    } else {
                        $review->update(['product_id' => $product->id]);
                    }
                }
                $duplicate->delete();
            }
            $products[] = $product;
        }

        return $products;
    }

    private function productReviews(array $products, array $orders, Seller $seller): void
    {
        foreach ($products as $index => $product) {
            $purchase = $orders[$index + 1];
            ProductReview::query()->updateOrCreate(
                ['product_id' => $product->id, 'buyer_id' => $purchase['buyerUser']->id],
                ['seller_id' => $seller->id, 'rating' => [5, 4, 5, 4, 5][$index % 5],
                    'title' => 'Happy with my purchase',
                    'body' => 'The '.strtolower($product->name).' arrived as described and has been useful. Would recommend this product.'],
            );
        }
    }

    private function orders(string $key, array $locations, array $location, Seller $seller, array $products, array $riders): array
    {
        $keys = array_keys($locations);
        $position = array_search($key, $keys, true);
        $nextKey = $keys[($position + 1) % count($keys)];
        $previousKey = $keys[($position + count($keys) - 1) % count($keys)];
        $orders = [];
        $statuses = ['pending', 'preparing', 'to_ship', 'in_transit', 'out_for_delivery', 'delivered', 'completed', 'pending', 'preparing', 'to_ship', 'in_transit', 'out_for_delivery', 'delivered', 'completed', 'pending', 'preparing', 'in_transit', 'out_for_delivery', 'delivered', 'completed'];

        for ($index = 0; $index < 20; $index++) {
            $buyerLocationKey = $index % 2 === 0 ? $nextKey : $previousKey;
            $buyerLocation = $locations[$buyerLocationKey];
            $email = 'demo.'.$key.'.buyer'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT).'@shopease.test';
            $first = ['Alex', 'Jamie', 'Taylor', 'Morgan', 'Jordan', 'Avery', 'Casey', 'Riley', 'Cameron', 'Drew'][$index % 10];
            $last = ['Rivera', 'Cruz', 'Lim', 'Garcia', 'Reyes', 'Flores', 'Bautista', 'Mendoza', 'Navarro', 'Aquino'][($index + intdiv($index, 10)) % 10];
            $buyerUser = $this->user($email, $first.' '.$last, User::ROLE_BUYER);
            $buyer = Buyer::query()->updateOrCreate(['user_id' => $buyerUser->id], [
                'first_name' => $first, 'last_name' => $last, 'sex' => 'other', 'contact_no' => '09178'.str_pad((string) (($position + 1) * 100000 + $index), 6, '0', STR_PAD_LEFT),
                'birthday' => '1998-04-12', 'age' => 28, 'province' => $buyerLocation['province'], 'municipality' => $buyerLocation['municipality'],
                'barangay' => $buyerLocation['barangay'], 'street' => 'Market Road', 'house_number' => (string) (10 + $index),
                'zip_code' => $buyerLocation['postal'], 'registration_status' => 'active', 'approved_at' => now(),
            ]);

            $product = $products[$index % count($products)];
            $status = $index < 8 ? 'in_transit' : $statuses[$index];
            $orderNumber = 'LOC-'.strtoupper($key).'-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);
            $shippingFee = ShippingFeeCalculator::forItems([(object) ['product' => $product, 'quantity' => 1]]);
            $order = Order::query()->updateOrCreate(['order_number' => $orderNumber], [
                'buyer_id' => $buyerUser->id, 'seller_id' => $seller->id, 'logistics_id' => $location['center']->logistics_id,
                'total' => (float) $product->price + $shippingFee, 'shipping_fee' => $shippingFee, 'voucher_discount' => 0,
                'status' => $status, 'pickup_date' => in_array($status, ['to_ship', 'in_transit', 'out_for_delivery', 'delivered', 'completed'], true) ? now()->subDays(1)->toDateString() : null,
                'pickup_time' => '10:00', 'delivery_name' => $first.' '.$last, 'delivery_phone' => $buyer->contact_no,
                'delivery_address' => $buyer->house_number.' '.$buyer->street.', '.$buyer->barangay.', '.$buyer->municipality.', '.$buyer->province,
                'delivery_barangay' => $buyer->barangay, 'delivery_province' => $buyer->province, 'delivery_municipality' => $buyer->municipality,
                'payment_method' => $index % 2 ? 'Cash on Delivery' : 'ShopEase Wallet',
            ]);
            $item = OrderItem::query()->updateOrCreate(['order_id' => $order->id], [
                'product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1,
                'unit_price' => $product->price, 'variation' => 'Standard', 'color' => 'Assorted', 'size' => 'Standard',
            ]);

            $specialLocation = $index < 4 ? $locations[$nextKey] : ($index < 8 ? $location : $buyerLocation);
            $atHub = in_array($status, ['out_for_delivery', 'delivered', 'completed'], true);
            if ($index < 8) $atHub = false;
            if ($index === 16) $atHub = false;
            $rider = $atHub ? $riders[$specialLocation === $location ? $key : $buyerLocationKey]['delivery'] : $riders[$key]['pickup'];
            $shipmentLocation = $index < 4
                ? $locations[$nextKey]['center']->name.' · '.$locations[$nextKey]['center']->province
                : ($index < 8 || $index === 16
                    ? $location['center']->name.' · '.$location['center']->province
                    : ($atHub ? $buyerLocation['hub']->hub_name.' · '.$buyerLocation['municipality'].', '.$buyerLocation['province'] : 'Seller pickup · '.$location['barangay'].', '.$location['municipality']));
            $shipment = Shipment::query()->updateOrCreate(['order_id' => $order->id], [
                'tracking_number' => $orderNumber.'-P', 'scan_token' => hash('sha256', $orderNumber.'-P'),
                'courier' => 'ShopEase Location Demo', 'estimated_delivery' => now()->addDays(3)->toDateString(),
                'shipping_fee' => $shippingFee, 'picked_up_at' => in_array($status, ['in_transit', 'out_for_delivery', 'delivered', 'completed'], true) ? now()->subHours(6) : null,
                'delivered_at' => in_array($status, ['delivered', 'completed'], true) ? now()->subHours(1) : null,
                'current_location' => $shipmentLocation, 'location_updated_at' => now(),
                'logistics_branch_id' => $index < 4 ? $locations[$nextKey]['center']->id : $location['center']->id,
                'logistics_delivery_hub_id' => $atHub ? $buyerLocation['hub']->id : null, 'rider_id' => $rider->id,
            ]);
            $orders[$index + 1] = compact('order', 'item', 'shipment', 'buyer', 'buyerUser', 'product', 'buyerLocationKey');
        }

        return $orders;
    }

    private function riders(string $key, LogisticsBranch $center, LogisticsDeliveryHub $hub, string $barangay): array
    {
        $riders = [];
        foreach (['pickup' => ['Nico', 'Garcia'], 'delivery' => ['Mara', 'Santos']] as $operation => [$first, $last]) {
            $email = 'demo.'.$key.'.'.$operation.'@shopease.test';
            $user = $this->user($email, $first.' '.$last.' '.ucfirst($key), User::ROLE_RIDER);
            $rider = Rider::query()->updateOrCreate(['user_id' => $user->id], [
                'logistics_branch_id' => $center->id, 'first_name' => $first, 'last_name' => $last, 'sex' => 'other',
                'contact_no' => $operation === 'pickup' ? '09170007101' : '09170007102', 'birthday' => '1995-01-01', 'age' => 31,
                'province' => $hub->province, 'municipality' => $hub->municipality, 'barangay' => $barangay,
                'street' => 'Rider Service Road', 'house_number' => '5', 'vehicle' => 'Motorcycle',
                'registration_type' => 'delivery_hub', 'assignment_region' => $hub->province === 'Bulacan' ? 'Central Luzon' : ($hub->province === 'Laguna' ? 'Calabarzon' : 'National Capital Region'),
                'assignment_province' => $hub->province, 'assignment_barangay' => $barangay,
                'plate_number' => strtoupper(substr($key, 0, 3)).'-'.strtoupper($operation).'-01',
                'municipal_hub' => $hub->hub_name, 'logistics_operation' => $operation,
                'courier_company' => $center->logistics?->business_name ?: 'ShopEase Logistics',
                'registration_status' => 'active', 'approved_at' => now(), 'rejected_at' => null,
            ]);
            DeliveryHubRiderRegistration::query()->updateOrCreate(
                ['user_id' => $user->id],
                ['logistics_delivery_hub_id' => $hub->id, 'status' => 'approved', 'reviewed_at' => now(), 'rejection_reason' => null],
            );
            $riders[$operation] = $rider;
        }

        return $riders;
    }

    private function courier(string $key, LogisticsBranch $center): CourierRegistration
    {
        $email = 'demo.'.$key.'.courier@shopease.test';
        $user = $this->user($email, ucfirst($key).' Demo Courier', User::ROLE_COURIER);
        $courier = CourierRegistration::query()->updateOrCreate(['email' => $email], [
            'user_id' => $user->id, 'sorting_center_id' => $center->id, 'first_name' => ucfirst($key),
            'last_name' => 'Courier', 'sex' => 'other', 'birthday' => '1992-01-01', 'phone' => '09170007201',
            'region' => $center->province === 'Bulacan' ? 'Central Luzon' : ($center->province === 'Laguna' ? 'Calabarzon' : 'National Capital Region'),
            'province' => $center->province, 'city' => $center->municipality, 'barangay' => $center->barangay,
            'street' => 'Courier Road', 'house_no' => '8', 'postal_code' => $center->postal_code,
            'status' => 'approved', 'reviewed_at' => now(), 'rejection_reason' => null,
        ]);
        SortingCenterVehicle::query()->updateOrCreate(
            ['sorting_center_id' => $center->id, 'plate_number' => 'DEMO-'.strtoupper(substr($key, 0, 3)).'-001'],
            ['courier_registration_id' => $courier->id, 'name' => ucfirst($key).' Demo Linehaul Truck', 'type' => 'Truck',
                'make' => 'Isuzu', 'model' => 'N-Series', 'year' => 2024, 'capacity' => '5 tons',
                'driver_name' => ucfirst($key).' Courier', 'registration_date' => now()->subMonths(6),
                'availability' => 'Available', 'color' => 'White', 'notes' => 'Location Marketplace demo vehicle'],
        );

        return $courier;
    }

    private function sortingCenterArrival(string $from, string $to, array $locations, array $orders): LogisticsTransferManifest
    {
        $origin = $locations[$from]['center'];
        $destination = $locations[$to]['center'];
        $courier = $locations[$from]['courier'];
        $vehicle = SortingCenterVehicle::query()->where('courier_registration_id', $courier->id)->firstOrFail();
        $manifest = LogisticsTransferManifest::query()->updateOrCreate(['batch_key' => 'LOC-DEMO-IN-'.$from.'-'.$to], [
            'origin_type' => 'sorting_center', 'origin_id' => $origin->id, 'destination_type' => 'sorting_center', 'destination_id' => $destination->id,
            'courier_registration_id' => $courier->id, 'vehicle_id' => $vehicle->id,
            'courier_name' => trim($courier->first_name.' '.$courier->last_name), 'vehicle_reference' => $vehicle->plate_number,
            'vehicle_type' => $vehicle->type, 'status' => 'arrived', 'scheduled_at' => now()->subHours(2),
            'loaded_at' => now()->subHours(2), 'departed_at' => now()->subHour(),
            'arrival_confirmed_at' => now()->subMinutes(20), 'arrived_at' => now()->subMinutes(20),
        ]);
        $members = [];
        for ($index = 1; $index <= 4; $index++) {
            $shipment = $orders[$from][$index]['shipment'];
            $shipment->update(['logistics_branch_id' => $destination->id, 'logistics_delivery_hub_id' => null,
                'current_location' => $destination->name.' · '.$destination->province, 'location_updated_at' => now()->subMinutes(20)]);
            $members[$shipment->id] = ['load_status' => 'loaded', 'loaded_at' => now()->subHours(2), 'received_at' => now()->subMinutes(20)];
        }
        $this->saveManifestShipments($manifest, $members);
        $vehicle->update(['availability' => 'Available']);

        return $manifest;
    }

    private function stageReturnCargo(string $key, string $destinationKey, array $locations, array $orders, LogisticsTransferManifest $inbound): void
    {
        $origin = $locations[$key]['center'];
        $destination = $locations[$destinationKey]['center'];
        $batchKey = 'LOC-DEMO-RETURN-'.$key.'-'.$destinationKey;
        $outbound = LogisticsTransferManifest::query()->updateOrCreate(['batch_key' => $batchKey], [
            'origin_type' => 'sorting_center', 'origin_id' => $origin->id, 'destination_type' => 'sorting_center', 'destination_id' => $destination->id,
            'courier_registration_id' => $inbound->courier_registration_id, 'vehicle_id' => $inbound->vehicle_id,
            'courier_name' => $inbound->courier_name, 'vehicle_reference' => $inbound->vehicle_reference,
            'vehicle_type' => $inbound->vehicle_type, 'returning_manifest_id' => $inbound->id,
            'status' => 'loaded', 'scheduled_at' => now()->addMinutes(30), 'loaded_at' => now(), 'departed_at' => null,
        ]);
        $members = [];
        for ($index = 5; $index <= 8; $index++) {
            $shipment = $orders[$key][$index]['shipment'];
            $shipment->update(['logistics_branch_id' => $origin->id, 'logistics_delivery_hub_id' => null,
                'current_location' => $origin->name.' · '.$origin->province, 'location_updated_at' => now()]);
            $members[$shipment->id] = ['load_status' => 'loaded', 'loaded_at' => now(), 'received_at' => null];
        }
        $this->saveManifestShipments($outbound, $members);
    }

    private function hubArrival(string $key, array $locations, array $orders, CourierRegistration $courier): void
    {
        $location = $locations[$key];
        $center = $location['center'];
        $hub = $location['hub'];
        $vehicle = SortingCenterVehicle::query()->where('courier_registration_id', $courier->id)->firstOrFail();
        $manifest = LogisticsTransferManifest::query()->updateOrCreate(['batch_key' => 'LOC-DEMO-HUB-IN-'.$key], [
            'origin_type' => 'sorting_center', 'origin_id' => $center->id, 'destination_type' => 'delivery_hub', 'destination_id' => $hub->id,
            'courier_registration_id' => $courier->id, 'vehicle_id' => $vehicle->id,
            'courier_name' => trim($courier->first_name.' '.$courier->last_name), 'vehicle_reference' => $vehicle->plate_number,
            'vehicle_type' => $vehicle->type, 'status' => 'arrived', 'scheduled_at' => now()->subHours(2),
            'loaded_at' => now()->subHours(2), 'departed_at' => now()->subHour(),
            'arrival_confirmed_at' => now()->subMinutes(15), 'arrived_at' => now()->subMinutes(15),
        ]);
        $members = [];
        foreach ([13, 14, 19] as $orderIndex) {
            $shipment = $orders[$orderIndex]['shipment'];
            $shipment->update(['logistics_branch_id' => $center->id, 'logistics_delivery_hub_id' => $hub->id,
                'current_location' => $hub->hub_name.' · '.$hub->municipality.', '.$hub->province, 'location_updated_at' => now()->subMinutes(15)]);
            $members[$shipment->id] = ['load_status' => 'loaded', 'loaded_at' => now()->subHours(2), 'received_at' => now()->subMinutes(15)];
        }
        $this->saveManifestShipments($manifest, $members);
        $vehicle->update(['availability' => 'Available']);
    }

    private function saveManifestShipments(LogisticsTransferManifest $manifest, array $members): void
    {
        foreach ($members as $shipmentId => $attributes) {
            DB::table('logistics_transfer_manifest_shipments')->updateOrInsert(
                ['transfer_manifest_id' => $manifest->id, 'shipment_id' => $shipmentId],
                [...$attributes, 'updated_at' => now(), 'created_at' => now()],
            );
        }
    }

    private function parcelReports(string $key, array $location, array $orders): void
    {
        $lost = $orders[17]['shipment'];
        SortingCenterParcelIssue::query()->updateOrCreate(
            ['sorting_center_id' => $location['center']->id, 'tracking_number' => $lost->tracking_number],
            ['reported_by' => $location['center']->user_id, 'issue_type' => 'Lost Parcel',
                'description' => 'Demo report: parcel could not be located during the sorting-center handoff. Please review the attached evidence.',
                'evidence_name' => 'lost-parcel-'.strtolower($key).'.jpg', 'status' => 'Under Review',
                'accountability' => ['status' => 'Under Review']],
        );
        $damaged = $orders[18]['shipment'];
        DeliveryHubParcelIssue::query()->updateOrCreate(
            ['delivery_hub_id' => $location['hub']->id, 'tracking_number' => $damaged->tracking_number],
            ['reported_by' => $location['hub']->user_id, 'issue_type' => 'Damaged Parcel',
                'description' => 'Demo report: visible package damage was found before the final delivery attempt. Please review the attached evidence.',
                'evidence_name' => 'damaged-parcel-'.strtolower($key).'.jpg', 'status' => 'Under Review',
                'accountability' => ['status' => 'Under Review']],
        );
    }

    private function returnAndRefundRecords(string $key, array $locations, array $orders, array $ridersByLocation): void
    {
        foreach ([13, 14, 19] as $index) {
            $buyerLocationKey = $orders[$index]['buyerLocationKey'];
            $this->returnRecord($key, $locations[$buyerLocationKey], $orders[$index], $ridersByLocation[$buyerLocationKey]['pickup'], 'return', 'pending', null, 'Changed mind after delivery; item is unused.', 'return');
        }
        $buyerLocationKey = $orders[20]['buyerLocationKey'];
        $this->returnRecord($key, $locations[$buyerLocationKey], $orders[20], $ridersByLocation[$buyerLocationKey]['pickup'], 'refund', 'pending', null, 'Product arrived with a missing accessory; buyer requested a refund.', 'refund');
    }

    private function returnRecord(string $key, array $location, array $record, Rider $pickupRider, string $returnType, string $status, ?string $shippingStatus, string $reason, string $solution): void
    {
        $order = $record['order'];
        $item = $record['item'];
        $complaint = Complaint::query()->updateOrCreate(
            ['subject' => 'LOC-DEMO-'.$key.'-'.$order->order_number.'-'.$returnType],
            ['user_id' => $record['buyerUser']->id, 'order_id' => $order->id,
                'type' => $returnType === 'return' ? 'Return Request' : 'Refund Request',
                'description' => $reason, 'status' => $status === 'pending' ? 'open' : 'in_progress'],
        );
        $returnItems = [[
            'order_item_id' => $item->id, 'product_name' => $item->product_name, 'variation' => $item->variation,
            'color' => $item->color, 'size' => $item->size, 'quantity' => 1,
            'unit_price' => (float) $item->unit_price, 'product_price' => (float) $item->unit_price,
        ]];
        $return = OrderReturn::query()->updateOrCreate(['complaint_id' => $complaint->id], [
            'order_id' => $order->id, 'return_type' => $returnType, 'status' => $status, 'shipping_status' => $shippingStatus,
            'courier' => $shippingStatus ? 'ShopEase '.$key.' Demo Courier' : null,
            'tracking_number' => $returnType === 'return' ? 'LOC-RET-'.strtoupper($key).'-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) : null,
            'reason' => $reason, 'solution' => $solution, 'evidence_description' => 'Photo evidence provided by the buyer for the demo return/refund request.',
            'return_items' => $returnItems, 'requested_at' => now()->subDay(), 'returned_at' => null,
            'reviewed_by' => $status === 'accepted' ? $location['hub']->user_id : null,
            'reviewed_at' => $status === 'accepted' ? now()->subHours(6) : null,
            'refund_amount' => (float) $item->unit_price, 'refunded_at' => null,
            'evidence' => ['demo-return-evidence.jpg'], 'problem_type' => $returnType === 'return' ? 'Item no longer needed' : 'Missing item/accessory',
            'buyer_solution' => $returnType, 'pickup_date' => $returnType === 'return' && $status === 'accepted' ? now()->subHours(1)->toDateString() : null,
            'pickup_time' => $returnType === 'return' && $status === 'accepted' ? '10:00' : null,
            'pickup_address' => $record['buyer']->house_number.' '.$record['buyer']->street.', '.$record['buyer']->barangay.', '.$record['buyer']->municipality,
            'return_delivery_hub_id' => $returnType === 'return' ? $location['hub']->id : null,
        ]);
        if ($returnType === 'return' && $status === 'accepted') {
            $return->forceFill(['pickup_rider_id' => $pickupRider->id])->save();
        } else {
            $return->forceFill(['pickup_rider_id' => null])->save();
        }
        if ($returnType === 'return' && $status === 'accepted') {
            OrderReturnEvent::query()->updateOrCreate(
                ['order_return_id' => $return->id, 'status' => 'hub_received'],
                ['location' => $location['hub']->hub_name.' · '.$location['municipality'], 'scanned_at' => now()->subMinutes(15)],
            );
        } else {
            OrderReturnEvent::query()->where('order_return_id', $return->id)->delete();
        }
    }

    private function seedRoutes(LogisticsBranch $origin, LogisticsBranch $next): void
    {
        if ($origin->id !== $next->id) {
            LogisticsBranchRoute::query()->updateOrCreate(
                ['origin_branch_id' => $origin->id, 'next_branch_id' => $next->id],
                ['status' => 'active'],
            );
        }
    }

    private function user(string $email, string $name, string $role): User
    {
        return User::query()->updateOrCreate(['email' => $email], [
            'name' => $name, 'role' => $role, 'password' => $this->password,
            'email_verified_at' => now(), 'registration_status' => 'active', 'approved_at' => now(), 'rejected_at' => null,
        ]);
    }

    private function samePlace(?string $left, ?string $right): bool
    {
        $normalize = fn (?string $value): string => str_replace([' city', ' municipality', ' '], ['', '', ''], mb_strtolower(trim((string) $value)));
        return $normalize($left) === $normalize($right);
    }
}
