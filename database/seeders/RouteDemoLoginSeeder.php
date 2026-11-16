<?php

namespace Database\Seeders;

use App\Models\Buyer\Buyer;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RouteDemoLoginSeeder extends Seeder
{
    private array $credentials = [];

    public function run(): void
    {
        $this->credentials = [];
        DB::transaction(function (): void {
            $network = $this->network();
            $manila = $this->center($network, 'Manila Sorting Center', 'Metro Manila', 'Manila', 'Intramuros', '1002', 'center.manila.route@shopease.test');
            $laguna = $this->center($network, 'Calamba Sorting Center', 'Laguna', 'Calamba City', 'Real', '4027', 'center.laguna.route@shopease.test');
            $this->hub($manila, 'Manila Delivery Hub', 'Metro Manila', 'Manila', 'Intramuros', '1002', 'hub.manila.route@shopease.test');
            $this->hub($laguna, 'Calamba Delivery Hub', 'Laguna', 'Calamba City', 'Real', '4027', 'hub.calamba.route@shopease.test');
            $this->marketplaceProfiles();
        });

        if ($this->credentials !== []) {
            $this->command?->table(['Email', 'Role', 'Generated password'], $this->credentials);
        }
    }

    private function user(string $email, string $name, string $role): User
    {
        $existing = User::query()->where('email', $email)->first();
        if ($existing) {
            throw new \RuntimeException("Demo email {$email} already exists. No accounts were changed.");
        }

        $password = bin2hex(random_bytes(18)).'Aa1!';
        $user = User::query()->create([
            'email' => $email, 'name' => $name, 'role' => $role, 'password' => Hash::make($password),
            'email_verified_at' => now(), 'registration_status' => 'active', 'approved_at' => now(),
        ]);
        $this->credentials[] = [$email, $role, $password];

        return $user;
    }

    private function marketplaceProfiles(): void
    {
        $sellerUser = $this->user('seller.route@shopease.test', 'Mika Santos', User::ROLE_SELLER);
        Seller::query()->updateOrCreate(['user_id' => $sellerUser->id], [
            'first_name' => 'Mika', 'last_name' => 'Santos', 'sex' => 'female', 'contact_no' => '09170001001',
            'birthday' => '1994-03-12', 'age' => 32, 'province' => 'Metro Manila', 'municipality' => 'Manila',
            'barangay' => 'Intramuros', 'street' => 'Muralla Street', 'house_number' => '18',
            'business_name' => 'Manila Home Goods', 'store_name' => 'Manila Home Goods',
            'line_of_business' => "Women's Apparel",
            'registration_status' => 'active', 'approved_at' => now(),
        ]);

        $buyerUser = $this->user('buyer.route@shopease.test', 'Alex Rivera', User::ROLE_BUYER);
        Buyer::query()->updateOrCreate(['user_id' => $buyerUser->id], [
            'first_name' => 'Alex', 'last_name' => 'Rivera', 'sex' => 'other', 'contact_no' => '09170001002',
            'birthday' => '1998-07-20', 'age' => 28, 'province' => 'Laguna', 'municipality' => 'Calamba City',
            'barangay' => 'Bonifacio', 'street' => 'Market Road', 'house_number' => '24',
            'registration_status' => 'active', 'approved_at' => now(),
        ]);
    }

    private function network(): Logistics
    {
        $logisticsUser = $this->user('route.logistics@shopease.test', 'ShopEase Logistics', User::ROLE_LOGISTICS);

        return Logistics::query()->updateOrCreate(
            ['user_id' => $logisticsUser->id],
            ['first_name' => 'ShopEase', 'last_name' => 'Logistics', 'sex' => 'other',
                'contact_no' => '09170001000', 'birthday' => '1990-01-01', 'age' => 36,
                'province' => 'Metro Manila', 'municipality' => 'Manila', 'barangay' => 'Intramuros',
                'street' => 'Port Area Road', 'house_number' => '1', 'business_name' => 'ShopEase Logistics',
                'registration_status' => 'active', 'approved_at' => now()],
        );
    }

    private function center(Logistics $network, string $name, string $province, string $city, string $barangay, string $postalCode, string $email): LogisticsBranch
    {
        $manager = $this->user($email, $name.' Manager', User::ROLE_SORTING_CENTER);
        return LogisticsBranch::query()->updateOrCreate(
            ['logistics_id' => $network->id, 'name' => $name],
            ['user_id' => $manager->id, 'address' => "{$barangay}, {$city}, {$province}", 'contact_person' => $name.' Manager',
                'phone' => '09170001010', 'province' => $province, 'municipality' => $city, 'barangay' => $barangay,
                'postal_code' => $postalCode, 'street' => 'Logistics Avenue', 'manager_email' => $email,
                'login_email' => $email, 'status' => 'active', 'archived_at' => null],
        );
    }

    private function hub(LogisticsBranch $center, string $name, string $province, string $city, string $barangay, string $postalCode, string $email): LogisticsDeliveryHub
    {
        $operator = $this->user($email, $name.' Operator', User::ROLE_DELIVERY_HUB);
        return LogisticsDeliveryHub::query()->updateOrCreate(
            ['logistics_branch_id' => $center->id, 'hub_name' => $name],
            ['user_id' => $operator->id, 'province' => $province, 'municipality' => $city, 'barangay' => $barangay,
                'postal_code' => $postalCode, 'street' => 'Logistics Avenue', 'manager_name' => $name.' Manager',
                'manager_email' => $email, 'manager_phone' => '09170001011', 'login_email' => $email,
                'status' => 'active', 'archived_at' => null],
        );
    }
}
