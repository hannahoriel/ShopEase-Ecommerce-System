<?php

namespace Tests\Support;

use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\Rider;
use App\Models\User;

final class CheckoutRoute
{
    public static function createHub(LogisticsBranch $branch): LogisticsDeliveryHub
    {
        $hub = $branch->deliveryHubs()->create([
            'hub_name' => 'Checkout Delivery Hub',
            'login_email' => 'hub-'.$branch->id.'@example.com',
            'province' => $branch->province,
            'municipality' => $branch->municipality,
            'barangay' => 'Real', 'postal_code' => '4027', 'street' => 'Main Street',
            'manager_name' => 'Demo Manager', 'manager_email' => 'manager@example.com', 'manager_phone' => '09170001000',
            'status' => 'active',
        ]);
        $user = User::factory()->create([
            'role' => User::ROLE_RIDER, 'registration_status' => 'active',
        ]);
        Rider::create([
            'user_id' => $user->id, 'logistics_branch_id' => $branch->id,
            'first_name' => 'Checkout', 'last_name' => 'Rider', 'sex' => 'other',
            'contact_no' => '09170001000', 'birthday' => '1995-01-01', 'age' => 31,
            'province' => $hub->province, 'municipality' => $hub->municipality,
            'barangay' => 'Real', 'street' => 'Main Street', 'house_number' => '1',
            'vehicle' => 'Motorcycle', 'plate_number' => 'CHECKOUT-'.$user->id,
            'logistics_operation' => 'Both', 'registration_status' => 'active',
        ]);
        $hub->riderRegistrations()->create(['user_id' => $user->id, 'status' => 'approved']);

        return $hub;
    }
}
