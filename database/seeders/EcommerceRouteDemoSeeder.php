<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\Shipment;
use App\Models\Buyer\Buyer;
use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\DeliveryHubRiderRegistration;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\SortingCenterVehicle;
use App\Models\Rider\Rider;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Creates one connected parcel route that can be advanced through the actual
 * rider and facility scanners. Run with: php artisan db:seed --class=EcommerceRouteDemoSeeder
 */
class EcommerceRouteDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('EcommerceRouteDemoSeeder is restricted to local and testing environments.');
        }

        $password = Hash::make(env('ECOMMERCE_ROUTE_DEMO_PASSWORD', 'Demo@12345'));
        $network = $this->existingNetwork($password);
        $manilaCenter = $this->activeCenter($network, 'Metro Manila')
            ?? $this->center($network, 'Manila Sorting Center', 'Metro Manila', 'Manila', 'Intramuros', '1002', 'center.manila.route@shopease.test', $password);
        $lagunaCenter = $this->activeCenter($network, 'Laguna')
            ?? $this->center($network, 'Calamba Sorting Center', 'Laguna', 'Calamba City', 'Real', '4027', 'center.laguna.route@shopease.test', $password);
        $manilaHub = $this->hub($manilaCenter, 'Manila Delivery Hub', 'Metro Manila', 'Manila', 'Intramuros', '1002', 'hub.manila.route@shopease.test', $password);
        $calambaHub = $this->hub($lagunaCenter, 'Calamba Delivery Hub', 'Laguna', 'Calamba City', 'Real', '4027', 'hub.calamba.route@shopease.test', $password);

        $sellerUser = $this->user('seller.route@shopease.test', 'Mika Santos', User::ROLE_SELLER, $password);
        $seller = Seller::query()->updateOrCreate(['user_id' => $sellerUser->id], [
            'first_name' => 'Mika', 'last_name' => 'Santos', 'sex' => 'female', 'contact_no' => '09170001001',
            'birthday' => '1994-03-12', 'age' => 32, 'province' => 'Metro Manila', 'municipality' => 'Manila',
            'barangay' => 'Intramuros', 'street' => 'Muralla Street', 'house_number' => '18',
            'business_name' => 'Manila Home Goods', 'store_name' => 'Manila Home Goods',
            'line_of_business' => "Women's Apparel",
            'registration_status' => 'active', 'approved_at' => now(),
        ]);

        $buyerUser = $this->user('buyer.route@shopease.test', 'Alex Rivera', User::ROLE_BUYER, $password);
        Buyer::query()->updateOrCreate(['user_id' => $buyerUser->id], [
            'first_name' => 'Alex', 'last_name' => 'Rivera', 'sex' => 'other', 'contact_no' => '09170001002',
            'birthday' => '1998-07-20', 'age' => 28, 'province' => 'Laguna', 'municipality' => 'Calamba City',
            'barangay' => 'Bonifacio', 'street' => 'Market Road', 'house_number' => '24',
            'registration_status' => 'active', 'approved_at' => now(),
        ]);

        $product = Product::query()->updateOrCreate(
            ['seller_id' => $seller->id, 'sku' => 'SE-ROUTE-WOMENS-APPAREL-001'],
            ['name' => 'Linen Everyday Blouse', 'description' => 'Lightweight everyday blouse from Manila Home Goods.',
                'category' => "Women's Apparel", 'price' => 1299, 'pricing_mode' => 'fixed',
                'pricing_source' => 'base', 'photos' => [], 'stock_quantity' => 20, 'status' => 'active',
                'is_archived' => false, 'archived_by_admin' => false],
        );

        $pickupRider = $this->rider($network, $manilaCenter, $manilaHub, 'rider.pickup.route@shopease.test', 'Nico', 'Garcia', 'PICKUP-ROUTE-01', 'pickup');
        $legacyDeliveryRider = User::query()->where('email', 'rider.delivery.route@shopease.test')->first();
        if ($legacyDeliveryRider && ! User::query()->where('email', 'calamba.rider@shopease.test')->exists()) {
            $legacyDeliveryRider->update(['email' => 'calamba.rider@shopease.test']);
        }
        $deliveryRider = $this->rider($network, $lagunaCenter, $calambaHub, 'calamba.rider@shopease.test', 'Jamie', 'Cruz', 'DELIVERY-ROUTE-01', 'delivery', 'Bonifacio');
        $manilaCourier = $this->courier($manilaCenter, 'courier.manila.route@shopease.test', 'Carlo', 'Reyes', 'TRUCK-MNL-001', $password);
        $lagunaCourier = $this->courier($lagunaCenter, 'courier.laguna.route@shopease.test', 'Bea', 'Lim', 'TRUCK-LAG-001', $password);

        $order = Order::query()->updateOrCreate(['order_number' => 'SE-ROUTE-MNL-LAG-1001'], [
            'buyer_id' => $buyerUser->id, 'seller_id' => $seller->id, 'total' => 1299,
            'status' => 'to_ship', 'pickup_date' => now()->addDay()->toDateString(), 'pickup_time' => '10:00',
            'delivery_name' => 'Alex Rivera', 'delivery_phone' => '09170001002',
            'delivery_address' => '24 Market Road, Bonifacio, Calamba City, Laguna', 'payment_method' => 'Cash on Delivery',
            'shipping_fee' => 80,
        ]);
        OrderItem::query()->updateOrCreate(
            ['order_id' => $order->id],
            ['product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1, 'unit_price' => 1299,
                'variation' => 'Linen', 'color' => 'Ivory', 'size' => 'Medium'],
        );
        Shipment::query()->updateOrCreate(['order_id' => $order->id], [
            'tracking_number' => 'SE-ROUTE-1001', 'scan_token' => hash('sha256', 'SE-ROUTE-1001'),
            'courier' => 'ShopEase Route Logistics', 'estimated_delivery' => now()->addDays(3)->toDateString(),
            'shipping_fee' => 80, 'current_location' => 'Seller pickup · Intramuros, Manila',
            // Keep the hub unset until rider drop-off is confirmed. This makes
            // the scheduled pickup appear in the seller municipality's incoming queue.
            'logistics_branch_id' => $manilaCenter->id, 'logistics_delivery_hub_id' => null,
            'rider_id' => $pickupRider->id,
        ]);

        $this->command?->info('Manila-to-Laguna route demo is ready. Order: SE-ROUTE-MNL-LAG-1001; tracking: SE-ROUTE-1001.');
        $this->command?->info('Demo accounts share ECOMMERCE_ROUTE_DEMO_PASSWORD (default: Demo@12345).');
    }

    private function user(string $email, string $name, string $role, string $password): User
    {
        return User::query()->updateOrCreate(['email' => $email], [
            'name' => $name, 'role' => $role, 'password' => $password,
            'email_verified_at' => now(), 'registration_status' => 'active',
            'approved_at' => now(), 'rejected_at' => null,
        ]);
    }

    private function existingNetwork(string $password): Logistics
    {
        $configuredEmail = trim((string) env('ECOMMERCE_ROUTE_LOGISTICS_EMAIL', ''));
        $network = $configuredEmail !== ''
            ? Logistics::query()->whereHas('user', fn ($query) => $query->where('email', $configuredEmail))->first()
            : null;
        $network ??= Logistics::query()->whereRaw('LOWER(business_name) LIKE ?', ['%shopease logistics%'])->orderBy('id')->first();
        $network ??= Logistics::query()->where('registration_status', 'active')->orderBy('id')->first();

        if ($network) {
            return $network;
        }

        // Fresh local databases may not have a logistics account yet. Create a
        // single ShopEase Logistics account for the route fixture in that case.
        $logisticsUser = $this->user('route.logistics@shopease.test', 'ShopEase Logistics', User::ROLE_LOGISTICS, $password);

        return Logistics::query()->updateOrCreate(
            ['user_id' => $logisticsUser->id],
            ['first_name' => 'ShopEase', 'last_name' => 'Logistics', 'sex' => 'other',
                'contact_no' => '09170001000', 'birthday' => '1990-01-01', 'age' => 36,
                'province' => 'Metro Manila', 'municipality' => 'Manila', 'barangay' => 'Intramuros',
                'street' => 'Port Area Road', 'house_number' => '1', 'business_name' => 'ShopEase Logistics',
                'registration_status' => 'active', 'approved_at' => now()],
        );
    }

    private function activeCenter(Logistics $network, string $province): ?LogisticsBranch
    {
        return LogisticsBranch::query()->where('logistics_id', $network->id)
            ->where('status', 'active')->whereNull('archived_at')
            ->whereRaw('LOWER(province) = ?', [mb_strtolower($province)])
            ->orderBy('id')->first();
    }

    private function center(Logistics $network, string $name, string $province, string $city, string $barangay, string $postalCode, string $email, string $password): LogisticsBranch
    {
        $manager = $this->user($email, $name.' Manager', User::ROLE_SORTING_CENTER, $password);
        return LogisticsBranch::query()->updateOrCreate(
            ['logistics_id' => $network->id, 'name' => $name],
            ['user_id' => $manager->id, 'address' => "{$barangay}, {$city}, {$province}", 'contact_person' => $name.' Manager',
                'phone' => '09170001010', 'province' => $province, 'municipality' => $city, 'barangay' => $barangay,
                'postal_code' => $postalCode, 'street' => 'Logistics Avenue', 'manager_email' => $email,
                'login_email' => $email, 'status' => 'active', 'archived_at' => null],
        );
    }

    private function hub(LogisticsBranch $center, string $name, string $province, string $city, string $barangay, string $postalCode, string $email, string $password): LogisticsDeliveryHub
    {
        $operator = $this->user($email, $name.' Operator', User::ROLE_DELIVERY_HUB, $password);
        return LogisticsDeliveryHub::query()->updateOrCreate(
            ['logistics_branch_id' => $center->id, 'hub_name' => $name],
            ['user_id' => $operator->id, 'province' => $province, 'municipality' => $city, 'barangay' => $barangay,
                'postal_code' => $postalCode, 'street' => 'Logistics Avenue', 'manager_name' => $name.' Manager',
                'manager_email' => $email, 'manager_phone' => '09170001011', 'login_email' => $email,
                'status' => 'active', 'archived_at' => null],
        );
    }

    private function rider(Logistics $network, LogisticsBranch $center, LogisticsDeliveryHub $hub, string $email, string $first, string $last, string $plate, string $operation, ?string $assignedBarangay = null): Rider
    {
        $assignedBarangay ??= $hub->barangay;
        $user = $this->user($email, $first.' '.$last, User::ROLE_RIDER, Hash::make(env('ECOMMERCE_ROUTE_DEMO_PASSWORD', 'Demo@12345')));
        $rider = Rider::query()->updateOrCreate(['user_id' => $user->id], [
            'logistics_branch_id' => $center->id, 'first_name' => $first, 'last_name' => $last, 'sex' => 'other',
            'contact_no' => '09170001012', 'birthday' => '1995-01-01', 'age' => 31,
            'province' => $hub->province, 'municipality' => $hub->municipality, 'barangay' => $assignedBarangay,
            'street' => 'Rider Street', 'house_number' => '5', 'vehicle' => 'Motorcycle', 'plate_number' => $plate,
            'registration_type' => 'delivery_hub', 'assignment_region' => $hub->province === 'Laguna' ? 'Calabarzon' : 'NCR',
            'assignment_province' => $hub->province, 'assignment_barangay' => $assignedBarangay,
            'municipal_hub' => $hub->hub_name, 'logistics_operation' => $operation,
            'courier_company' => $network->business_name, 'registration_status' => 'active',
            'approved_at' => now(), 'rejected_at' => null,
        ]);
        DeliveryHubRiderRegistration::query()->updateOrCreate(
            ['user_id' => $user->id],
            ['logistics_delivery_hub_id' => $hub->id, 'status' => 'approved', 'reviewed_at' => now(), 'rejection_reason' => null],
        );
        return $rider;
    }

    private function courier(LogisticsBranch $center, string $email, string $first, string $last, string $plate, string $password): CourierRegistration
    {
        $user = $this->user($email, $first.' '.$last, User::ROLE_COURIER, $password);
        $courier = CourierRegistration::query()->updateOrCreate(
            ['email' => $email],
            ['user_id' => $user->id, 'sorting_center_id' => $center->id, 'first_name' => $first, 'last_name' => $last,
                'sex' => 'other', 'birthday' => '1992-01-01', 'phone' => '09170001013', 'region' => 'Calabarzon',
                'province' => $center->province, 'city' => $center->municipality, 'barangay' => $center->barangay,
                'street' => 'Courier Street', 'house_no' => '8', 'postal_code' => $center->postal_code,
                'vehicle' => 'Truck', 'plate_number' => $plate, 'status' => 'approved', 'reviewed_at' => now(),
                'rejection_reason' => null],
        );
        SortingCenterVehicle::query()->updateOrCreate(
            ['sorting_center_id' => $center->id, 'plate_number' => $plate],
            ['name' => $first.' '.$last.' Linehaul Truck', 'type' => 'Truck', 'make' => 'Isuzu',
                'model' => 'N-Series', 'year' => 2023, 'capacity' => '5 tons',
                'driver_name' => $first.' '.$last, 'registration_date' => now()->subMonths(6),
                'availability' => 'Available', 'color' => 'White', 'notes' => 'Ecommerce route demo vehicle'],
        );

        return $courier;
    }
}
