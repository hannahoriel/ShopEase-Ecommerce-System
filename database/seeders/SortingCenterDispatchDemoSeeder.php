<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\Shipment;
use App\Models\Buyer\Buyer;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsBranchRoute;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Logistics\LogisticsTransferManifest;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SortingCenterDispatchDemoSeeder extends Seeder
{
    private const PARCELS_PER_DESTINATION = 2;

    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('SortingCenterDispatchDemoSeeder is restricted to local and testing environments.');
        }

        $centers = LogisticsBranch::query()
            ->where('status', 'active')->whereNull('archived_at')
            ->whereHas('user', fn ($query) => $query->where('role', User::ROLE_SORTING_CENTER))
            ->with('logistics')
            ->orderBy('id')->get();

        if ($centers->isEmpty()) {
            $this->command?->warn('No active Sorting Center accounts found; no dispatch demo parcels were created.');
            return;
        }

        $password = Hash::make(env('SCANNER_DISPATCH_DEMO_PASSWORD', 'Demo@12345'));
        $seller = $this->seller($password);
        $createdGroups = 0;

        foreach ($centers as $center) {
            $destinations = $this->destinations($center);
            if ($destinations->isEmpty()) {
                $this->command?->warn("{$center->name}: no active next-stop Sorting Centers or Delivery Hubs found.");
                continue;
            }

            $inbound = LogisticsTransferManifest::query()->updateOrCreate(
                ['batch_key' => 'SC-DISPATCH-DEMO-IN-'.$center->id],
                [
                    'origin_type' => 'sorting_center',
                    'origin_id' => $center->id,
                    'destination_type' => 'sorting_center',
                    'destination_id' => $center->id,
                    'courier_name' => 'Demo Inbound Courier',
                    'vehicle_reference' => 'DEMO-IN-'.$center->id,
                    'status' => 'arrived',
                    'loaded_at' => now()->subHour(),
                    'departed_at' => now()->subHour(),
                    'arrival_confirmed_at' => now()->subMinutes(45),
                    'arrived_at' => now()->subMinutes(45),
                ],
            );

            $inboundShipments = [];
            foreach ($destinations as $destinationIndex => $destination) {
                $outboundBatch = 'SC-DISPATCH-DEMO-'.$center->id.'-'.$destination['type'].'-'.$destination['id'];
                $outbound = LogisticsTransferManifest::query()->updateOrCreate(
                    ['batch_key' => $outboundBatch],
                    [
                        'origin_type' => 'sorting_center',
                        'origin_id' => $center->id,
                        'destination_type' => $destination['type'],
                        'destination_id' => $destination['id'],
                        'courier_registration_id' => null,
                        'vehicle_id' => null,
                        'courier_name' => null,
                        'vehicle_reference' => null,
                        'vehicle_type' => null,
                        'assigned_by' => null,
                        'assigned_at' => null,
                        'scheduled_at' => null,
                        'status' => 'preparing',
                        'loaded_at' => null,
                        'departed_at' => null,
                        'arrived_at' => null,
                    ],
                );

                $outboundShipments = [];
                for ($parcelIndex = 1; $parcelIndex <= self::PARCELS_PER_DESTINATION; $parcelIndex++) {
                    $sequence = $destinationIndex * self::PARCELS_PER_DESTINATION + $parcelIndex;
                    $tracking = 'SC-DISPATCH-'.$center->id.'-'.strtoupper(substr($destination['type'], 0, 2)).'-'.$destination['id'].'-'.$parcelIndex;
                    $buyer = $this->buyer($password, $center, $destination, $sequence);
                    $order = Order::query()->updateOrCreate(
                        ['order_number' => $tracking],
                        [
                            'buyer_id' => $buyer->user_id,
                            'seller_id' => $seller->id,
                            'logistics_id' => $center->logistics_id,
                            'total' => 750 + $sequence,
                            'status' => 'in_transit',
                            'delivery_name' => trim($buyer->first_name.' '.$buyer->last_name),
                            'delivery_phone' => $buyer->contact_no,
                            'delivery_address' => trim(implode(', ', array_filter([
                                $buyer->house_number, $buyer->street, $buyer->barangay,
                                $buyer->municipality, $buyer->province,
                            ]))),
                            'delivery_barangay' => $buyer->barangay,
                            'delivery_province' => $buyer->province,
                            'delivery_municipality' => $buyer->municipality,
                            'payment_method' => 'Cash on Delivery',
                            'shipping_fee' => 50,
                        ],
                    );

                    OrderItem::query()->updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'product_id' => null,
                            'product_name' => 'Dispatch Test Parcel '.$sequence,
                            'quantity' => 1,
                            'unit_price' => 750 + $sequence,
                            'variation' => 'Demo',
                            'color' => 'Brown',
                            'size' => 'Standard',
                        ],
                    );

                    $shipment = Shipment::query()->updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'tracking_number' => $tracking,
                            'scan_token' => hash('sha256', $tracking),
                            'courier' => 'ShopEase Dispatch Demo',
                            'estimated_delivery' => now()->addDays(3)->toDateString(),
                            'shipping_fee' => 50,
                            'current_location' => $center->name.' · '.$center->province,
                            'logistics_branch_id' => $center->id,
                            'logistics_delivery_hub_id' => $destination['type'] === 'delivery_hub' ? $destination['id'] : null,
                            'rider_id' => null,
                        ],
                    );

                    $outboundShipments[$shipment->id] = [
                        'load_status' => 'pending', 'loaded_at' => null, 'loaded_by' => null,
                        'received_at' => null, 'received_by' => null,
                    ];
                    $inboundShipments[$shipment->id] = [
                        'load_status' => 'loaded', 'loaded_at' => $inbound->loaded_at,
                        'loaded_by' => null, 'received_at' => $inbound->arrived_at,
                        'received_by' => null,
                    ];
                }

                $outbound->shipments()->sync($outboundShipments);
                $createdGroups++;
            }

            $inbound->shipments()->sync($inboundShipments);
            $this->command?->info("{$center->name}: staged {$destinations->count()} destination groups, ".($destinations->count() * self::PARCELS_PER_DESTINATION).' parcels.');
        }

        $this->command?->info("Dispatch scanner demo ready with {$createdGroups} groups. No courier or schedule is pre-assigned.");
    }

    private function destinations(LogisticsBranch $center)
    {
        $directNextIds = LogisticsBranchRoute::query()
            ->where('origin_branch_id', $center->id)->where('status', 'active')
            ->pluck('next_branch_id');
        $branches = LogisticsBranch::query()
            ->where('logistics_id', $center->logistics_id)
            ->where('status', 'active')->whereNull('archived_at')
            ->where('id', '!=', $center->id)
            ->when($directNextIds->isNotEmpty(), fn ($query) => $query->whereIn('id', $directNextIds))
            ->orderBy('id')->get()
            ->filter(fn (LogisticsBranch $branch): bool => LogisticsBranch::query()
                ->where('logistics_id', $center->logistics_id)->where('status', 'active')->whereNull('archived_at')
                ->whereRaw('LOWER(province) = ?', [mb_strtolower((string) $branch->province)])
                ->orderBy('id')->value('id') === $branch->id)
            ->unique(fn (LogisticsBranch $branch): string => mb_strtolower((string) $branch->province))
            ->map(fn (LogisticsBranch $branch): array => [
                'type' => 'sorting_center', 'id' => $branch->id, 'name' => $branch->name,
                'province' => $branch->province, 'municipality' => $branch->municipality ?: $branch->province,
            ]);

        $hubs = LogisticsDeliveryHub::query()
            ->where('logistics_branch_id', $center->id)->where('status', 'active')->whereNull('archived_at')
            ->orderBy('id')->get()
            ->filter(fn (LogisticsDeliveryHub $hub): bool => LogisticsDeliveryHub::query()
                ->where('logistics_branch_id', $center->id)->where('status', 'active')->whereNull('archived_at')
                ->whereRaw('LOWER(municipality) = ?', [mb_strtolower((string) $hub->municipality)])
                ->orderBy('id')->value('id') === $hub->id)
            ->unique(fn (LogisticsDeliveryHub $hub): string => mb_strtolower((string) $hub->municipality))
            ->map(fn (LogisticsDeliveryHub $hub): array => [
                'type' => 'delivery_hub', 'id' => $hub->id, 'name' => $hub->hub_name,
                'province' => $hub->province, 'municipality' => $hub->municipality,
            ]);

        return $branches->concat($hubs)->values();
    }

    private function seller(string $password): Seller
    {
        $user = User::query()->updateOrCreate(['email' => 'scanner.dispatch.demo.seller@shopease.test'], [
            'name' => 'Scanner Dispatch Demo Seller', 'password' => $password,
            'role' => User::ROLE_SELLER, 'registration_status' => 'active',
            'email_verified_at' => now(), 'approved_at' => now(),
        ]);

        return Seller::query()->updateOrCreate(['user_id' => $user->id], [
            'first_name' => 'Dispatch Demo', 'last_name' => 'Seller', 'sex' => 'other',
            'contact_no' => '09170009991', 'birthday' => '1990-01-01', 'age' => 36,
            'province' => 'Metro Manila', 'municipality' => 'Manila', 'barangay' => 'Intramuros',
            'street' => 'Demo Street', 'house_number' => '1', 'store_name' => 'Dispatch Demo Store',
            'business_name' => 'Dispatch Demo Store', 'line_of_business' => 'General Merchandise',
            'registration_status' => 'active', 'approved_at' => now(),
        ]);
    }

    private function buyer(string $password, LogisticsBranch $center, array $destination, int $sequence): Buyer
    {
        $email = 'scanner.dispatch.demo.buyer.'.$center->id.'.'.$destination['type'].'.'.$destination['id'].'.'.$sequence.'@shopease.test';
        $user = User::query()->updateOrCreate(['email' => $email], [
            'name' => 'Dispatch Demo Buyer '.$sequence, 'password' => $password,
            'role' => User::ROLE_BUYER, 'registration_status' => 'active',
            'email_verified_at' => now(), 'approved_at' => now(),
        ]);

        return Buyer::query()->updateOrCreate(['user_id' => $user->id], [
            'first_name' => 'Dispatch Demo', 'last_name' => 'Buyer '.$sequence, 'sex' => 'other',
            'contact_no' => '0917000'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'birthday' => '1995-01-01', 'age' => 31,
            'province' => $destination['province'], 'municipality' => $destination['municipality'],
            'barangay' => 'Poblacion', 'street' => 'Demo Dispatch Road', 'house_number' => (string) $sequence,
            'registration_status' => 'active', 'approved_at' => now(),
        ]);
    }
}
