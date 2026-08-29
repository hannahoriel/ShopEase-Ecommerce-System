<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_register_with_role_specific_profile_fields(): void
    {
        $response = $this->post(route('register.attempt'), [
            'role' => User::ROLE_BUYER,
            'last_name' => 'Dela Cruz',
            'first_name' => 'Maria',
            'middle_initial' => 'A',
            'sex' => 'female',
            'email' => 'buyer.reg@example.com',
            'contact_no' => '09171234567',
            'birthday' => '2000-01-15',
            'province' => 'Metro Manila',
            'municipality' => 'Quezon City',
            'barangay' => 'Diliman',
            'street' => 'Mabini Street',
            'house_number' => '123',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'email' => 'buyer.reg@example.com',
            'role' => User::ROLE_BUYER,
            'last_name' => 'Dela Cruz',
            'first_name' => 'Maria',
            'middle_initial' => 'A',
            'sex' => 'female',
            'contact_no' => '09171234567',
            'province' => 'Metro Manila',
            'municipality' => 'Quezon City',
            'barangay' => 'Diliman',
            'street' => 'Mabini Street',
            'house_number' => '123',
        ]);

        $user = User::where('email', 'buyer.reg@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame(26, $user->age);
    }

    public function test_seller_registration_collects_business_details(): void
    {
        $response = $this->post(route('register.attempt'), [
            'role' => User::ROLE_SELLER,
            'last_name' => 'Santos',
            'first_name' => 'Liza',
            'middle_initial' => 'R',
            'sex' => 'female',
            'email' => 'seller.reg@example.com',
            'contact_no' => '09991234567',
            'birthday' => '1995-05-20',
            'province' => 'Cebu',
            'municipality' => 'Cebu City',
            'barangay' => 'Lahug',
            'street' => 'Osmeña Boulevard',
            'house_number' => '55',
            'business_name' => 'Liza Craft Store',
            'line_of_business' => 'Handcrafted Goods',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'email' => 'seller.reg@example.com',
            'role' => User::ROLE_SELLER,
            'business_name' => 'Liza Craft Store',
            'line_of_business' => 'Handcrafted Goods',
        ]);
    }

    public function test_rider_registration_collects_vehicle_information(): void
    {
        $response = $this->post(route('register.attempt'), [
            'role' => User::ROLE_RIDER,
            'last_name' => 'Reyes',
            'first_name' => 'Mark',
            'middle_initial' => 'T',
            'sex' => 'male',
            'email' => 'rider.reg@example.com',
            'contact_no' => '09261234567',
            'birthday' => '1998-09-10',
            'province' => 'Laguna',
            'municipality' => 'Santa Rosa',
            'barangay' => 'Balibago',
            'street' => 'P. Burgos Street',
            'house_number' => '8',
            'vehicle' => 'motorcycle',
            'plate_number' => 'ABC 1234',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'email' => 'rider.reg@example.com',
            'role' => User::ROLE_RIDER,
            'vehicle' => 'motorcycle',
            'plate_number' => 'ABC 1234',
        ]);
    }
}
