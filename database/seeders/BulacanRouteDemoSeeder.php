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
use App\Models\Logistics\LogisticsBranchRoute;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\SortingCenterVehicle;
use App\Models\Rider\Rider;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/** Seeds a Bulacan-to-Laguna parcel route with a configured Manila transfer hop. */
class BulacanRouteDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('BulacanRouteDemoSeeder is restricted to local and testing environments.');
        }

        $password = Hash::make(env('ECOMMERCE_ROUTE_DEMO_PASSWORD', 'Demo@12345'));
        $network = $this->network($password);

        $bulacanCenter = $this->center(
            $network, 'Bulacan Main Sorting Center', 'Bulacan', 'Malolos City', 'Longos', '3000',
            'center.bulacan.route@shopease.test', $password,
        );
        $manilaCenter = $this->centerForProvince(
            $network, 'Metro Manila', 'Manila Sorting Center', 'Manila', 'Intramuros', '1002',
            'center.manila.bulacan-route@shopease.test', $password,
        );
        $lagunaCenter = $this->centerForProvince(
            $network, 'Laguna', 'Calamba Sorting Center', 'Calamba City', 'Real', '4027',
            'center.laguna.bulacan-route@shopease.test', $password,
        );

        $bulacanHub = $this->hub(
            $bulacanCenter, 'Guiguinto Delivery Hub', 'Bulacan', 'Guiguinto', 'Malis', '3015',
            'hub.bulacan.route@shopease.test', $password,
        );
        $lagunaHub = $this->hub(
            $lagunaCenter, 'Calamba Delivery Hub', 'Laguna', 'Calamba City', 'Bucal', '4027',
            'hub.laguna.bulacan-route@shopease.test', $password,
        );

        // Configure actual directed legs. The parcel must pass Manila before
        // it can be dispatched to the Laguna sorting center.
        $this->route($bulacanCenter, $manilaCenter);
        $this->route($manilaCenter, $lagunaCenter);

        $sellerUser = $this->user('seller.bulacan.route@shopease.test', 'Leah Mendoza', User::ROLE_SELLER, $password);
        $seller = Seller::query()->updateOrCreate(['user_id' => $sellerUser->id], [
            'first_name' => 'Leah', 'last_name' => 'Mendoza', 'sex' => 'female', 'contact_no' => '09171110001',
            'birthday' => '1994-02-10', 'age' => 32, 'province' => 'Bulacan', 'municipality' => 'Guiguinto',
            'barangay' => 'Tuktukan', 'street' => 'MacArthur Highway', 'house_number' => '18',
            'business_name' => 'Bulacan Apparel House', 'store_name' => 'Bulacan Apparel House',
            'line_of_business' => "Women's Apparel", 'registration_status' => 'active', 'approved_at' => now(),
        ]);

        $buyerUser = $this->user('buyer.bulacan.route@shopease.test', 'Alex Rivera', User::ROLE_BUYER, $password);
        Buyer::query()->updateOrCreate(['user_id' => $buyerUser->id], [
            'first_name' => 'Alex', 'last_name' => 'Rivera', 'sex' => 'other', 'contact_no' => '09172220002',
            'birthday' => '1998-07-20', 'age' => 28, 'province' => 'Laguna', 'municipality' => 'Calamba City',
            'barangay' => 'Bonifacio', 'street' => 'Market Road', 'house_number' => '24',
            'registration_status' => 'active', 'approved_at' => now(),
        ]);

        $product = Product::query()->updateOrCreate(
            ['seller_id' => $seller->id, 'sku' => 'SE-BULACAN-APPAREL-001'],
            ['name' => 'Bulacan Everyday Blouse', 'description' => 'Demo apparel parcel for the Bulacan to Laguna route.',
                'category' => "Women's Apparel", 'price' => 899, 'pricing_mode' => 'fixed', 'pricing_source' => 'base',
                'photos' => [], 'stock_quantity' => 30, 'status' => 'active', 'is_archived' => false, 'archived_by_admin' => false],
        );

        $pickupRider = $this->rider(
            $network, $bulacanCenter, $bulacanHub, 'rider.pickup.bulacan.route@shopease.test',
            'Nico', 'Garcia', 'BULACAN-PICKUP-01', 'pickup', 'Tuktukan', $password,
        );
        $this->rider(
            $network, $bulacanCenter, $bulacanHub, 'rider.delivery.bulacan.route@shopease.test',
            'Mara', 'Santos', 'BULACAN-DELIVERY-01', 'delivery', 'Tuktukan', $password,
        );
        $this->rider(
            $network, $lagunaCenter, $lagunaHub, 'rider.delivery.laguna.route@shopease.test',
            'Jamie', 'Cruz', 'LAGUNA-DELIVERY-01', 'delivery', 'Bonifacio', $password,
        );

        $this->courier($bulacanCenter, 'courier.bulacan.route@shopease.test', 'Carlo', 'Reyes', 'TRUCK-BUL-001', $password);
        $this->courier($manilaCenter, 'courier.manila.bulacan-route@shopease.test', 'Bea', 'Lim', 'TRUCK-MNL-BUL-001', $password);
        $this->courier($lagunaCenter, 'courier.laguna.bulacan-route@shopease.test', 'Dino', 'Ramos', 'TRUCK-LAG-BUL-001', $password);

        $order = Order::query()->updateOrCreate(['order_number' => 'SE-BULACAN-CALAMBA-0001'], [
            'buyer_id' => $buyerUser->id, 'seller_id' => $seller->id, 'total' => 899,
            'status' => 'to_ship', 'pickup_date' => now()->addDay()->toDateString(), 'pickup_time' => '09:00',
            'delivery_name' => 'Alex Rivera', 'delivery_phone' => '09172220002',
            'delivery_address' => '24 Market Road, Bonifacio, Calamba City, Laguna',
            'payment_method' => 'Cash on Delivery', 'shipping_fee' => 120,
        ]);
        OrderItem::query()->updateOrCreate(['order_id' => $order->id], [
            'product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1,
            'unit_price' => 899, 'variation' => 'Linen', 'color' => 'Cream', 'size' => 'Medium',
        ]);
        Shipment::query()->updateOrCreate(['order_id' => $order->id], [
            'tracking_number' => 'SE-BULACAN-CALAMBA-0001',
            'scan_token' => hash('sha256', 'SE-BULACAN-CALAMBA-0001'),
            'courier' => 'ShopEase Logistics', 'estimated_delivery' => now()->addDays(4)->toDateString(),
            'shipping_fee' => 120, 'current_location' => 'Seller pickup · Tuktukan, Guiguinto, Bulacan',
            'logistics_branch_id' => $bulacanCenter->id, 'logistics_delivery_hub_id' => null,
            'rider_id' => $pickupRider->id,
        ]);

        $this->command?->info('Bulacan-to-Laguna route is ready: Bulacan Sorting Center → Manila Sorting Center → Calamba Sorting Center → Calamba Delivery Hub.');
        $this->command?->info('Tracking: SE-BULACAN-CALAMBA-0001. Demo accounts use ECOMMERCE_ROUTE_DEMO_PASSWORD (default: Demo@12345).');
    }

    private function network(string $password): Logistics
    {
        $configuredEmail = trim((string) env('ECOMMERCE_ROUTE_LOGISTICS_EMAIL', ''));
        $network = $configuredEmail !== ''
            ? Logistics::query()->whereHas('user', fn ($query) => $query->where('email', $configuredEmail))->first()
            : null;
        // Prefer the existing ShopEase Logistics operator account used by the
        // logistics parcel tracking screens. This keeps all demo facilities,
        // couriers and riders inside that same logistics network.
        $network ??= Logistics::query()->whereHas('user', fn ($query) => $query->where('email', 'tracking.logistics@shopease.test'))->first();
        $network ??= Logistics::query()->whereRaw('LOWER(business_name) = ?', ['shopease logistics'])->orderBy('id')->first();
        $network ??= Logistics::query()->whereRaw('LOWER(business_name) LIKE ?', ['%shopease logistics%'])->orderBy('id')->first();
        if ($network) {
            return $network;
        }

        $user = $this->user('route.logistics@shopease.test', 'ShopEase Logistics', User::ROLE_LOGISTICS, $password);
        return Logistics::query()->updateOrCreate(['user_id' => $user->id], [
            'first_name' => 'ShopEase', 'last_name' => 'Logistics', 'sex' => 'other', 'contact_no' => '09170001000',
            'birthday' => '1990-01-01', 'age' => 36, 'province' => 'Metro Manila', 'municipality' => 'Manila',
            'barangay' => 'Intramuros', 'street' => 'Port Area Road', 'house_number' => '1',
            'business_name' => 'ShopEase Logistics', 'registration_status' => 'active', 'approved_at' => now(),
        ]);
    }

    private function centerForProvince(Logistics $network, string $province, string $name, string $city, string $barangay, string $postalCode, string $email, string $password): LogisticsBranch
    {
        return LogisticsBranch::query()->where('logistics_id', $network->id)->where('status', 'active')
            ->whereNull('archived_at')->whereRaw('LOWER(province) = ?', [mb_strtolower($province)])->orderBy('id')->first()
            ?? $this->center($network, $name, $province, $city, $barangay, $postalCode, $email, $password);
    }

    private function center(Logistics $network, string $name, string $province, string $city, string $barangay, string $postalCode, string $email, string $password): LogisticsBranch
    {
        $manager = $this->user($email, $name.' Manager', User::ROLE_SORTING_CENTER, $password);
        return LogisticsBranch::query()->updateOrCreate(['logistics_id' => $network->id, 'name' => $name], [
            'user_id' => $manager->id, 'address' => "{$barangay}, {$city}, {$province}",
            'contact_person' => $name.' Manager', 'phone' => '09173330010', 'province' => $province,
            'municipality' => $city, 'barangay' => $barangay, 'postal_code' => $postalCode,
            'house_no' => '120', 'street' => 'Logistics Avenue', 'building' => $name.' Operations Building',
            'manager_email' => $email, 'login_email' => $email,
            'status' => 'active', 'archived_at' => null,
        ]);
    }

    private function hub(LogisticsBranch $center, string $name, string $province, string $city, string $barangay, string $postalCode, string $email, string $password): LogisticsDeliveryHub
    {
        $operator = $this->user($email, $name.' Operator', User::ROLE_DELIVERY_HUB, $password);
        return LogisticsDeliveryHub::query()->updateOrCreate(['logistics_branch_id' => $center->id, 'hub_name' => $name], [
            'user_id' => $operator->id, 'province' => $province, 'municipality' => $city, 'barangay' => $barangay,
            'postal_code' => $postalCode, 'house_no' => '45', 'street' => 'Logistics Avenue',
            'building' => $name.' Operations Building', 'manager_name' => $name.' Manager',
            'manager_email' => $email, 'manager_phone' => '09174440010', 'login_email' => $email,
            'status' => 'active', 'archived_at' => null,
        ]);
    }

    private function route(LogisticsBranch $origin, LogisticsBranch $next): void
    {
        LogisticsBranchRoute::query()->updateOrCreate(
            ['origin_branch_id' => $origin->id, 'next_branch_id' => $next->id],
            ['status' => 'active'],
        );
    }

    private function rider(Logistics $network, LogisticsBranch $center, LogisticsDeliveryHub $hub, string $email, string $first, string $last, string $plate, string $operation, string $barangay, string $password): Rider
    {
        $user = $this->user($email, $first.' '.$last, User::ROLE_RIDER, $password);
        $rider = Rider::query()->updateOrCreate(['user_id' => $user->id], [
            'logistics_branch_id' => $center->id, 'first_name' => $first, 'last_name' => $last, 'sex' => 'other',
            'contact_no' => '09175550010', 'birthday' => '1995-01-01', 'age' => 31,
            'province' => $hub->province, 'municipality' => $hub->municipality, 'barangay' => $barangay,
            'street' => 'Rider Street', 'house_number' => '5', 'vehicle' => 'Motorcycle', 'plate_number' => $plate,
            'registration_type' => 'delivery_hub', 'assignment_region' => $hub->province === 'Bulacan' ? 'Central Luzon' : 'Calabarzon',
            'assignment_province' => $hub->province, 'assignment_barangay' => $barangay, 'municipal_hub' => $hub->hub_name,
            'logistics_operation' => $operation, 'courier_company' => $network->business_name,
            'registration_status' => 'active', 'approved_at' => now(), 'rejected_at' => null,
        ]);
        DeliveryHubRiderRegistration::query()->updateOrCreate(
            ['user_id' => $user->id],
            ['logistics_delivery_hub_id' => $hub->id, 'status' => 'approved', 'reviewed_at' => now(), 'rejection_reason' => null],
        );
        return $rider;
    }

    private function courier(LogisticsBranch $center, string $email, string $first, string $last, string $plate, string $password): void
    {
        $user = $this->user($email, $first.' '.$last, User::ROLE_COURIER, $password);
        CourierRegistration::query()->updateOrCreate(['email' => $email], [
            'user_id' => $user->id, 'sorting_center_id' => $center->id, 'first_name' => $first, 'last_name' => $last,
            'sex' => 'other', 'birthday' => '1992-01-01', 'phone' => '09176660010',
            'region' => match (mb_strtolower($center->province)) {
                'bulacan', 'pampanga', 'bataan', 'nueva ecija', 'tarlac', 'zambales', 'aurora' => 'Central Luzon',
                'laguna', 'cavite', 'batangas', 'rizal', 'quezon' => 'Calabarzon',
                'metro manila', 'ncr' => 'National Capital Region',
                default => $center->province,
            },
            'province' => $center->province, 'city' => $center->municipality, 'barangay' => $center->barangay,
            'street' => 'Courier Street', 'house_no' => '8', 'postal_code' => $center->postal_code,
            'vehicle' => 'Truck', 'plate_number' => $plate, 'status' => 'approved', 'reviewed_at' => now(), 'rejection_reason' => null,
        ]);
        SortingCenterVehicle::query()->updateOrCreate(
            ['sorting_center_id' => $center->id, 'plate_number' => $plate],
            ['name' => $first.' '.$last.' Linehaul Truck', 'type' => 'Truck', 'make' => 'Isuzu', 'model' => 'N-Series',
                'year' => 2023, 'capacity' => '5 tons', 'driver_name' => $first.' '.$last,
                'registration_date' => now()->subMonths(6), 'availability' => 'Available', 'color' => 'White',
                'notes' => 'Bulacan route demo vehicle'],
        );
    }

    private function user(string $email, string $name, string $role, string $password): User
    {
        return User::query()->updateOrCreate(['email' => $email], [
            'name' => $name, 'role' => $role, 'password' => $password, 'email_verified_at' => now(),
            'registration_status' => 'active', 'approved_at' => now(), 'rejected_at' => null,
        ]);
    }
}
