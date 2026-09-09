<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MultiStepRegistrationPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_completion_persists_user_and_buyer_profile(): void
    {
        $response = $this->withSession([
            'buyer_registration' => [
                'last_name' => 'Cruz',
                'first_name' => 'Ana',
                'middle_initial' => 'M',
                'sex' => 'female',
                'email' => 'ana.buyer@example.com',
                'contact_no' => '09170000001',
                'birthday' => '2000-01-15',
                'province' => 'Cebu',
                'municipality' => 'Cebu City',
                'barangay' => 'Lahug',
                'street' => 'Main Street',
                'house_no' => '10',
            ],
        ])->post(route('buyer.register.complete'));

        $response->assertRedirect(route('login'));
        $user = User::where('email', 'ana.buyer@example.com')->firstOrFail();

        $this->assertDatabaseHas('buyers', [
            'user_id' => $user->id,
            'house_number' => '10',
            'registration_status' => 'pending',
        ]);
    }

    public function test_seller_completion_persists_user_and_seller_profile(): void
    {
        $response = $this->withSession([
            'seller_registration' => [
                'last_name' => 'Santos',
                'first_name' => 'Liza',
                'sex' => 'female',
                'email' => 'liza.seller@example.com',
                'contact_no' => '09170000002',
                'birthday' => '1995-05-20',
                'province' => 'Cebu',
                'municipality' => 'Cebu City',
                'barangay' => 'Lahug',
                'street' => 'Main Street',
                'house_no' => '11',
                'business_name' => 'Liza Craft Store',
                'line_of_business' => 'Handcrafted Goods',
            ],
            'seller_registration_verification' => [
                'email' => 'liza.seller@example.com',
                'code' => Hash::make('123456'),
                'expires_at' => now()->addMinutes(10),
                'verified' => true,
            ],
        ])->post(route('seller.register.complete'));

        $response->assertRedirect(route('login'));
        $user = User::where('email', 'liza.seller@example.com')->firstOrFail();

        $this->assertDatabaseHas('sellers', [
            'user_id' => $user->id,
            'business_name' => 'Liza Craft Store',
            'line_of_business' => 'Handcrafted Goods',
            'house_number' => '11',
            'registration_status' => 'pending',
        ]);
    }
}
