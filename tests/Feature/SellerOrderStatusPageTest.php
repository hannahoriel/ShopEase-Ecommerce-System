<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Buyer\Buyer;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerOrderStatusPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_status_page_loads_buyer_phone_from_buyer_profile(): void
    {
        $sellerUser = User::query()->create([
            'name' => 'Page Seller', 'email' => 'page-seller@example.test', 'password' => 'password', 'role' => User::ROLE_SELLER,
        ]);
        $seller = Seller::query()->create([
            'user_id' => $sellerUser->id, 'store_name' => 'Page Seller Store', 'registration_status' => 'active',
        ]);
        $buyerUser = User::query()->create([
            'name' => 'Page Buyer', 'email' => 'page-buyer@example.test', 'password' => 'password', 'role' => User::ROLE_BUYER,
        ]);
        Buyer::query()->create([
            'user_id' => $buyerUser->id, 'last_name' => 'Buyer', 'first_name' => 'Page', 'sex' => 'other',
            'contact_no' => '09123456789', 'birthday' => '1990-01-01', 'age' => 36, 'province' => 'Laguna',
            'municipality' => 'Calamba', 'barangay' => 'Poblacion',
        ]);
        Order::query()->create([
            'buyer_id' => $buyerUser->id, 'seller_id' => $seller->id, 'total' => 100, 'status' => 'pending',
        ]);

        $this->actingAs($sellerUser)->getJson('/api/v1/seller/order-status')
            ->assertOk()->assertJsonPath('data.0.buyer.buyer_profile.contact_no', '09123456789');

        $this->actingAs($sellerUser)->get('/seller/order-status')
            ->assertOk()->assertSee('09123456789');
    }
}
