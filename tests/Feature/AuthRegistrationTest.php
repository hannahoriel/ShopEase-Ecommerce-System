<?php

namespace Tests\Feature;

use App\Models\Admin\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
        ]);

        $this->assertDatabaseHas('buyers', [
            'user_id' => User::where('email', 'buyer.reg@example.com')->value('id'),
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
            'registration_status' => 'pending',
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
        ]);

        $this->assertDatabaseHas('sellers', [
            'user_id' => User::where('email', 'seller.reg@example.com')->value('id'),
            'business_name' => 'Liza Craft Store',
            'line_of_business' => 'Handcrafted Goods',
            'registration_status' => 'pending',
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
        ]);

        $this->assertDatabaseHas('riders', [
            'user_id' => User::where('email', 'rider.reg@example.com')->value('id'),
            'vehicle' => 'motorcycle',
            'plate_number' => 'ABC 1234',
            'registration_status' => 'pending',
        ]);
    }

    public function test_admin_can_view_pending_registrations_page_and_display_data(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'email' => 'admin.reg@example.com',
        ]);

        Registration::create([
            'user_type' => 'buyer',
            'last_name' => 'Dela Cruz',
            'first_name' => 'Maria',
            'middle_name' => 'A',
            'sex' => 'female',
            'birthdate' => '2000-01-15',
            'email' => 'maria.pending@example.com',
            'phone' => '09171234567',
            'password' => bcrypt('Password123!'),
            'province' => 'Metro Manila',
            'municipality' => 'Quezon City',
            'barangay' => 'Diliman',
            'street' => 'Mabini Street',
            'house_no' => '123',
            'zip_code' => '1101',
            'valid_id_path' => 'ids/maria.jpg',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.registrations'));

        $response->assertOk()
            ->assertSee('Account Registrations')
            ->assertSee('Pending Requests')
            ->assertSee('Approved Users')
            ->assertSee('Rejected Users')
            ->assertSee('Maria A Dela Cruz')
            ->assertSee('maria.pending@example.com')
            ->assertSee('registration-search');
    }

    public function test_approved_buyer_can_login_with_submitted_password_and_reaches_buyer_dashboard(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $password = 'Password123!';
        $user = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'registration_status' => 'pending',
            'password' => $password,
            'email' => 'approved.buyer@example.com',
        ]);
        $registration = Registration::create([
            'user_type' => User::ROLE_BUYER,
            'last_name' => 'Buyer',
            'first_name' => 'Approved',
            'sex' => 'female',
            'birthdate' => '2000-01-15',
            'email' => $user->email,
            'phone' => '09170000001',
            'password' => Hash::make($password),
            'province' => 'Cebu',
            'municipality' => 'Cebu City',
            'barangay' => 'Lahug',
            'street' => 'Main Street',
            'house_no' => '10',
            'zip_code' => '6000',
            'valid_id_path' => 'ids/buyer.jpg',
            'status' => 'pending',
            'user_id' => $user->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.registrations.approve', $registration))
            ->assertRedirect();

        $this->post(route('logout'));

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => $password,
        ])->assertRedirect(route('buyer.dashboard'));
    }

    public function test_approved_seller_can_login_with_submitted_password_and_reaches_seller_dashboard(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $password = 'Password123!';
        $user = User::factory()->create([
            'role' => User::ROLE_SELLER,
            'registration_status' => 'pending',
            'password' => $password,
            'email' => 'approved.seller@example.com',
        ]);
        $registration = Registration::create([
            'user_type' => User::ROLE_SELLER,
            'last_name' => 'Seller',
            'first_name' => 'Approved',
            'sex' => 'female',
            'birthdate' => '1995-05-20',
            'email' => $user->email,
            'phone' => '09170000002',
            'password' => Hash::make($password),
            'province' => 'Cebu',
            'municipality' => 'Cebu City',
            'barangay' => 'Lahug',
            'street' => 'Main Street',
            'house_no' => '11',
            'zip_code' => '6000',
            'business_name' => 'Approved Store',
            'valid_id_path' => 'ids/seller.jpg',
            'status' => 'pending',
            'user_id' => $user->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.registrations.approve', $registration))
            ->assertRedirect();

        $this->post(route('logout'));

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => $password,
        ])->assertRedirect(route('seller.dashboard'));
    }
}
