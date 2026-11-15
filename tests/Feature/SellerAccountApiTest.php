<?php

namespace Tests\Feature;

use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerAccountApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_mobile_api_uploads_photos_shared_with_the_store_profile(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->post('/api/v1/seller/account', [
                '_method' => 'PUT',
                'name' => 'Mobile Seller',
                'contact_no' => '09171234567',
                'profile_photo' => UploadedFile::fake()->image('profile.png'),
                'cover_photo' => UploadedFile::fake()->image('cover.jpg'),
            ])
            ->assertOk()
            ->assertJsonPath('user.name', 'Mobile Seller')
            ->assertJsonStructure(['seller' => ['avatar_url', 'cover_url']]);
        $this->assertNotEmpty($response->json('seller.avatar_url'));
        $this->assertNotEmpty($response->json('seller.cover_url'));

        $seller->refresh();
        $this->assertNotEmpty($seller->profile_photo_path);
        $this->assertNotEmpty($seller->cover_photo_path);
        $this->assertSame($seller->profile_photo_path, $user->fresh()->profile_photo_path);
        Storage::disk('public')->assertExists($seller->profile_photo_path);
        Storage::disk('public')->assertExists($seller->cover_photo_path);

        $profileResponse = $this->getJson('/api/v1/seller/account')
            ->assertOk()
            ->assertJsonStructure(['seller' => ['avatar_url', 'cover_url']]);
        $this->assertNotEmpty($profileResponse->json('seller.avatar_url'));
        $this->assertNotEmpty($profileResponse->json('seller.cover_url'));
    }
}
