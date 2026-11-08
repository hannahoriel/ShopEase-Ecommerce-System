<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\CodCollection;
use App\Models\Rider\Remittance;
use App\Models\Rider\Rider;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/** Seeds one verified Manila COD remittance so the delivery hub cash-in flow can be tried. */
class ManilaDeliveryHubFinanceSeeder extends Seeder
{
    private const AMOUNT = 1176.00;
    private const REFERENCE_PREFIX = 'MNL-PIN-TEST-';

    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('ManilaDeliveryHubFinanceSeeder is restricted to local and testing environments.');
        }

        $hub = LogisticsDeliveryHub::query()
            ->where('hub_name', 'Manila Delivery Hub')
            ->where('province', 'Metro Manila')
            ->where('municipality', 'Manila')
            ->where('status', 'active')
            ->whereNull('archived_at')
            ->with('logisticsBranch')
            ->orderBy('id')
            ->first();

        if (! $hub || ! $hub->logisticsBranch || ! $hub->user_id) {
            throw new \RuntimeException('Active Manila Delivery Hub is missing. Run EcommerceRouteDemoSeeder first.');
        }
        if (! filter_var((string) $hub->manager_email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Set a valid manager email on Manila Delivery Hub before testing cash-in OTP.');
        }

        $password = Hash::make((string) env('LOGISTICS_FINANCE_DEMO_PASSWORD', 'Demo@12345'));
        $riderUser = User::query()->updateOrCreate(['email' => 'rider.finance.manila@shopease.test'], [
            'name' => 'Jamie Cruz (Finance Demo)', 'role' => User::ROLE_RIDER, 'password' => $password,
            'email_verified_at' => now(), 'registration_status' => 'active', 'approved_at' => now(), 'rejected_at' => null,
        ]);
        $rider = Rider::query()->updateOrCreate(['user_id' => $riderUser->id], [
            'logistics_branch_id' => $hub->logistics_branch_id,
            'first_name' => 'Jamie', 'last_name' => 'Cruz', 'sex' => 'other',
            'contact_no' => '09170001991', 'birthday' => '1995-01-01', 'age' => 31,
            'province' => $hub->province, 'municipality' => $hub->municipality,
            'barangay' => $hub->barangay ?: 'Intramuros', 'street' => 'Finance Demo Street', 'house_number' => '1',
            'vehicle' => 'Motorcycle', 'registration_type' => 'delivery_hub',
            'assignment_region' => 'NCR', 'assignment_province' => $hub->province,
            'assignment_barangay' => $hub->barangay ?: 'Intramuros', 'plate_number' => 'MNL-FIN-001',
            'courier_company' => $hub->logisticsBranch->logistics?->business_name,
            'logistics_operation' => 'delivery', 'municipal_hub' => $hub->hub_name,
            'registration_status' => 'active', 'approved_at' => now(), 'rejected_at' => null,
        ]);

        $sellerUser = User::query()->updateOrCreate(['email' => 'seller.finance.manila@shopease.test'], [
            'name' => 'Manila Finance Demo Seller', 'role' => User::ROLE_SELLER, 'password' => $password,
            'email_verified_at' => now(), 'registration_status' => 'active', 'approved_at' => now(), 'rejected_at' => null,
        ]);
        $seller = Seller::query()->updateOrCreate(['user_id' => $sellerUser->id], [
            'first_name' => 'Manila', 'last_name' => 'Demo Seller', 'sex' => 'other',
            'contact_no' => '09170001992', 'birthday' => '1990-01-01', 'age' => 36,
            'province' => 'Metro Manila', 'municipality' => 'Manila', 'barangay' => 'Intramuros',
            'street' => 'Finance Demo Street', 'house_number' => '2', 'store_name' => 'Manila Finance Demo Shop',
            'business_name' => 'Manila Finance Demo Shop', 'line_of_business' => 'General Merchandise',
            'registration_status' => 'active', 'approved_at' => now(), 'rejected_at' => null,
        ]);
        $buyer = User::query()->updateOrCreate(['email' => 'buyer.finance.manila@shopease.test'], [
            'name' => 'Alex Rivera (Finance Demo)', 'role' => User::ROLE_BUYER, 'password' => $password,
            'email_verified_at' => now(), 'registration_status' => 'active', 'approved_at' => now(), 'rejected_at' => null,
        ]);

        $fixtureQuery = Remittance::query()->where('rider_id', $rider->id)
            ->where('reference_number', 'like', self::REFERENCE_PREFIX.'%');
        $activeFixture = (clone $fixtureQuery)->whereIn('status', ['pending', 'verified'])->orderByDesc('id')->first();
        $latestFixture = (clone $fixtureQuery)->orderByDesc('id')->first();
        $sequence = $activeFixture
            ? (int) substr((string) $activeFixture->reference_number, strlen(self::REFERENCE_PREFIX))
            : ($latestFixture
                ? ((int) substr((string) $latestFixture->reference_number, strlen(self::REFERENCE_PREFIX))) + 1
                : 1);
        $reference = self::REFERENCE_PREFIX.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
        $orderNumber = 'SE-MNL-FINANCE-PIN-'.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);

        $order = Order::query()->updateOrCreate(['order_number' => $orderNumber], [
            'buyer_id' => $buyer->id, 'seller_id' => $seller->id,
            'logistics_id' => $hub->logisticsBranch->logistics_id,
            'total' => self::AMOUNT, 'shipping_fee' => 50, 'voucher_discount' => 0,
            'status' => 'delivered', 'payment_method' => 'Cash on Delivery',
            'delivery_name' => $buyer->name, 'delivery_phone' => '09170001993',
            'delivery_address' => 'Finance Demo Street, Intramuros, Manila, Metro Manila',
        ]);
        $collection = CodCollection::query()->updateOrCreate(['order_id' => $order->id], [
            'rider_id' => $rider->id, 'amount' => self::AMOUNT, 'collected_at' => now(),
        ]);

        $remittance = Remittance::query()->updateOrCreate(['reference_number' => $reference], [
            'rider_id' => $rider->id, 'delivery_hub_id' => $hub->id,
            'amount' => self::AMOUNT, 'actual_amount' => self::AMOUNT, 'discrepancy_amount' => 0,
            'method' => 'hub_turn_in', 'status' => 'verified',
            'reviewed_by' => $hub->user_id, 'reviewed_at' => now(),
            'review_notes' => 'Local finance demo fixture for first cash-in PIN testing.',
        ]);
        $remittance->collections()->syncWithoutDetaching([$collection->id]);

        $this->command?->info('Manila Delivery Hub finance demo is ready.');
        $this->command?->line('Verified rider remittance: '.$reference.' — ₱'.number_format(self::AMOUNT, 2));
        $this->command?->line('Manager OTP will be sent to: '.$hub->manager_email);
    }
}
