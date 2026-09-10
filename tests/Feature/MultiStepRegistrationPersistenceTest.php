<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ],
            'buyer_registration_verification' => [
                'email' => 'ana.buyer@example.com',
                'code' => Hash::make('123456'),
                'expires_at' => now()->addMinutes(10),
                'verified' => true,
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
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
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

    public function test_seller_valid_id_uploaded_in_step_one_is_persisted(): void
    {
        Storage::fake('public');

        $response = $this->withSession([
            'seller_registration' => [
                'last_name' => 'Santos',
                'first_name' => 'Liza',
                'sex' => 'female',
                'email' => 'valid-id.seller@example.com',
                'contact_no' => '09170000004',
                'birthday' => '1995-05-20',
                'province' => 'Cebu',
                'municipality' => 'Cebu City',
                'barangay' => 'Lahug',
                'street' => 'Main Street',
                'house_no' => '13',
                'business_name' => 'Liza Craft Store',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ],
        ])->post(route('seller.register.submit'), [
            'last_name' => 'Santos',
            'first_name' => 'Liza',
            'sex' => 'female',
            'email' => 'valid-id.seller@example.com',
            'contact_no' => '09170000004',
            'birthday' => '1995-05-20',
            'province' => 'Cebu',
            'municipality' => 'Cebu City',
            'barangay' => 'Lahug',
            'street' => 'Main Street',
            'house_no' => '13',
            'business_name' => 'Liza Craft Store',
            'valid_id' => UploadedFile::fake()->create('valid-id.png', 50, 'image/png'),
        ]);

        $response->assertRedirect(route('seller.business.register'));
        $path = session('seller_registration.valid_id_path');
        $this->assertNotEmpty($path);
        Storage::disk('public')->assertExists($path);

        $this->get(route('seller.review.register'))
            ->assertOk()
            ->assertSee('valid-id.png');
    }

    public function test_seller_business_permit_uploaded_in_step_two_is_persisted(): void
    {
        Storage::fake('public');

        $response = $this->withSession([
            'seller_registration' => [
                'last_name' => 'Santos',
                'first_name' => 'Liza',
                'sex' => 'female',
                'email' => 'permit.seller@example.com',
                'contact_no' => '09170000003',
                'birthday' => '1995-05-20',
                'province' => 'Cebu',
                'municipality' => 'Cebu City',
                'barangay' => 'Lahug',
                'street' => 'Main Street',
                'house_no' => '12',
                'business_name' => 'Liza Craft Store',
                'line_of_business' => 'Handcrafted Goods',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ],
            'seller_registration_verification' => [
                'email' => 'permit.seller@example.com',
                'code' => Hash::make('123456'),
                'expires_at' => now()->addMinutes(10),
                'verified' => true,
            ],
        ])->post(route('seller.business.submit'), [
            'business_name' => 'Liza Craft Store',
            'line_of_business' => 'Handcrafted Goods',
            'categories' => ['Handcrafted Goods'],
            'business_permit' => UploadedFile::fake()->create('permit.pdf', 50, 'application/pdf'),
        ]);

        $response->assertRedirect(route('seller.review.register'));
        $path = session('seller_registration.business_permit_path');
        $this->assertNotEmpty($path);
        Storage::disk('public')->assertExists($path);

        $this->get(route('seller.review.register'))
            ->assertOk()
            ->assertSee('permit.pdf');

        $response = $this->post(route('seller.register.complete'));

        $response->assertRedirect(route('login'));
        $user = User::where('email', 'permit.seller@example.com')->firstOrFail();

        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'business_permit_path' => $path,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'upload_business_permit' => $path,
        ]);
        $this->assertDatabaseHas('sellers', [
            'user_id' => $user->id,
            'upload_business_permit' => $path,
        ]);
    }
}
