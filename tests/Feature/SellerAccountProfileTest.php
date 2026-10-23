<?php

namespace Tests\Feature;

use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerAccountProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_update_profile_and_address_fields(): void
    {
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'first_name' => 'Original',
            'last_name' => 'Seller',
        ]);

        $this->actingAs($sellerUser)
            ->put(route('seller.account.update'), $this->profileData())
            ->assertRedirect(route('seller.account'))
            ->assertSessionHas('success');

        $seller->refresh();
        $this->assertSame('Updated', $seller->first_name);
        $this->assertSame('Shop', $seller->last_name);
        $this->assertSame('1000', $seller->zip_code);
        $this->assertSame('Unit 2', $seller->building);
        $this->assertSame('Green Village', $seller->subdivision);
        $this->assertSame(30, $seller->age);

        $this->get(route('seller.account'))
            ->assertOk()
            ->assertSee('1000')
            ->assertSee('Unit 2')
            ->assertSee('Green Village');
    }

    public function test_seller_can_upload_profile_and_cover_photos(): void
    {
        Storage::fake('public');
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create(['user_id' => $sellerUser->id]);

        $this->actingAs($sellerUser)
            ->put(route('seller.account.update'), $this->profileData([
                'cover_photo' => UploadedFile::fake()->image('cover.jpg'),
                'profile_photo' => UploadedFile::fake()->image('profile.png'),
            ]))
            ->assertRedirect(route('seller.account'))
            ->assertSessionHas('success');

        $seller->refresh();
        $this->assertNotEmpty($seller->cover_photo_path);
        $this->assertNotEmpty($seller->profile_photo_path);
        $this->assertSame($seller->profile_photo_path, $sellerUser->fresh()->profile_photo_path);
        Storage::disk('public')->assertExists($seller->cover_photo_path);
        Storage::disk('public')->assertExists($seller->profile_photo_path);
    }

    public function test_non_seller_cannot_update_seller_profile(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->put(route('seller.account.update'), $this->profileData())
            ->assertForbidden();
    }

    public function test_profile_update_rejects_email_change_until_email_verification_is_available(): void
    {
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        Seller::create(['user_id' => $sellerUser->id]);

        $this->actingAs($sellerUser)
            ->put(route('seller.account.update'), $this->profileData([
                'email' => 'new-address@example.test',
            ]))
            ->assertSessionHasErrors('email');

        $this->assertSame($sellerUser->email, $sellerUser->fresh()->email);
    }

    private function profileData(array $overrides = []): array
    {
        return array_merge([
            'last_name' => 'Shop',
            'first_name' => 'Updated',
            'middle_initial' => 'Marie',
            'sex' => 'Female',
            'email' => auth()->user()?->email ?? 'seller@example.test',
            'contact_no' => '09171234567',
            'birthday' => '1996-04-12',
            'province' => 'Laguna',
            'municipality' => 'Santa Rosa',
            'barangay' => 'Balibago',
            'zip_code' => '1000',
            'street' => 'Market Street',
            'house_number' => '12',
            'building' => 'Unit 2',
            'subdivision' => 'Green Village',
        ], $overrides);
    }
}
