<?php

namespace Tests\Feature;

use App\Models\Buyer\Buyer;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccountAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_navbar_shows_the_buyer_name_and_initials_avatar(): void
    {
        $buyer = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'name' => 'Different Account Name',
        ]);
        Buyer::create([
            'user_id' => $buyer->id,
            'first_name' => 'Alex',
            'last_name' => 'Rivera',
            'sex' => 'Prefer not to say',
            'contact_no' => '09171234567',
            'birthday' => '1995-01-01',
            'age' => 31,
            'province' => 'Laguna',
            'municipality' => 'Santa Rosa',
            'barangay' => 'Balibago',
        ]);

        $this->actingAs($buyer)
            ->get(route('buyer.dashboard'))
            ->assertOk()
            ->assertSee('Alex Rivera')
            ->assertSee('aria-label="Alex Rivera profile picture"', false)
            ->assertSee('>AR</span>', false);
    }

    public function test_seller_account_shows_initials_when_no_profile_photo_exists(): void
    {
        $sellerUser = User::factory()->create([
            'role' => User::ROLE_SELLER,
            'name' => 'Casey Seller',
        ]);
        Seller::create(['user_id' => $sellerUser->id]);

        $this->actingAs($sellerUser)
            ->get(route('seller.account'))
            ->assertOk()
            ->assertSee('Casey Seller')
            ->assertSee('>CS</span>', false);
    }

    public function test_admin_navbar_shows_initials_when_no_profile_photo_exists(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'name' => 'Jordan Admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.account'))
            ->assertOk()
            ->assertSee('Jordan Admin')
            ->assertSee('>JA</span>', false);
    }

    public function test_logistics_navbar_shows_initials_when_no_profile_photo_exists(): void
    {
        $logistics = User::factory()->create([
            'role' => User::ROLE_LOGISTICS,
            'name' => 'Morgan Logistics',
        ]);

        $this->actingAs($logistics)
            ->get(route('logistics.dashboard'))
            ->assertOk()
            ->assertSee('Morgan Logistics')
            ->assertSee('>ML</span>', false);
    }

    public function test_uploaded_profile_photo_is_used_on_each_page_after_update(): void
    {
        Storage::fake('public');
        $buyer = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'name' => 'Alex Rivera',
        ]);

        $this->actingAs($buyer)
            ->putJson('/api/v1/profile/photo', [
                'profile_photo' => UploadedFile::fake()->image('buyer.png'),
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Profile photo updated successfully.');

        $storedPath = $buyer->fresh()->profile_photo_path;
        $this->assertNotEmpty($storedPath);
        Storage::disk('public')->assertExists($storedPath);

        $photoUrl = Storage::disk('public')->url($storedPath);
        $this->get(route('buyer.dashboard'))
            ->assertOk()
            ->assertSee($photoUrl, false);
        $this->get(route('buyer.my-purchases'))
            ->assertOk()
            ->assertSee($photoUrl, false);
    }

    public function test_profile_photo_upload_is_available_to_riders_too(): void
    {
        Storage::fake('public');
        $rider = User::factory()->create([
            'role' => User::ROLE_RIDER,
            'name' => 'Riley Rider',
        ]);

        $this->actingAs($rider)
            ->putJson('/api/v1/profile/photo', [
                'profile_photo' => UploadedFile::fake()->image('rider.png'),
            ])
            ->assertOk();

        Storage::disk('public')->assertExists($rider->fresh()->profile_photo_path);
    }
}
