<?php

namespace Tests\Feature;

use App\Models\Buyer\Buyer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BuyerAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_update_account_details_and_persist_them(): void
    {
        $buyerUser = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'email' => 'buyer@example.test',
        ]);
        $this->createBuyerProfile($buyerUser);

        $this->actingAs($buyerUser)
            ->putJson('/api/v1/buyer/account', $this->validAccountData([
                'first_name' => 'Updated',
                'last_name' => 'Buyer',
                'middle_name' => 'Marie',
                'email' => 'updated@example.test',
                'zip_code' => '4026',
                'building' => 'Tower 2',
            ]))
            ->assertOk()
            ->assertJsonPath('message', 'Buyer account updated successfully.')
            ->assertJsonPath('user.name', 'Updated Marie Buyer')
            ->assertJsonPath('user.email', 'updated@example.test')
            ->assertJsonPath('profile.first_name', 'Updated')
            ->assertJsonPath('profile.middle_name', 'Marie')
            ->assertJsonPath('profile.zip_code', '4026')
            ->assertJsonPath('profile.building', 'Tower 2');

        $this->assertDatabaseHas('users', [
            'id' => $buyerUser->id,
            'name' => 'Updated Marie Buyer',
            'email' => 'updated@example.test',
        ]);
        $this->assertDatabaseHas('buyers', [
            'user_id' => $buyerUser->id,
            'first_name' => 'Updated',
            'last_name' => 'Buyer',
            'middle_initial' => 'Marie',
            'zip_code' => '4026',
            'building' => 'Tower 2',
        ]);
    }

    public function test_buyer_can_save_a_cover_photo_with_account_details(): void
    {
        Storage::fake('public');
        $buyerUser = User::factory()->create(['role' => User::ROLE_BUYER]);
        $this->createBuyerProfile($buyerUser);

        $response = $this->actingAs($buyerUser)
            ->putJson('/api/v1/buyer/account', $this->validAccountData([
                'cover_photo' => UploadedFile::fake()->image('cover.jpg'),
            ]))
            ->assertOk();

        $coverPath = $buyerUser->buyerProfile->fresh()->cover_photo_path;
        $this->assertNotEmpty($coverPath);
        $this->assertStringContainsString('/storage/buyer-covers/', $response->json('profile.cover_photo_url'));
        Storage::disk('public')->assertExists($coverPath);
    }

    public function test_only_buyers_can_update_buyer_account_and_email_must_be_unique(): void
    {
        $buyerUser = User::factory()->create(['role' => User::ROLE_BUYER]);
        $this->createBuyerProfile($buyerUser);
        $anotherUser = User::factory()->create(['email' => 'taken@example.test']);

        $this->actingAs($buyerUser)
            ->putJson('/api/v1/buyer/account', $this->validAccountData([
                'email' => $anotherUser->email,
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->actingAs($anotherUser)
            ->putJson('/api/v1/buyer/account', $this->validAccountData())
            ->assertForbidden();
    }

    public function test_unauthenticated_users_cannot_update_buyer_account(): void
    {
        $this->putJson('/api/v1/buyer/account', $this->validAccountData())
            ->assertUnauthorized();
    }

    public function test_buyer_account_page_loads_saved_email_and_cover_photo(): void
    {
        Storage::fake('public');
        $buyerUser = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'email' => 'saved@example.test',
        ]);
        $profile = $this->createBuyerProfile($buyerUser);
        $profile->update(['cover_photo_path' => 'buyer-covers/'.$buyerUser->id.'/cover.jpg']);
        Storage::disk('public')->put($profile->cover_photo_path, 'cover');

        $this->actingAs($buyerUser)
            ->get(route('buyer.my-purchases'))
            ->assertOk()
            ->assertSee('value="saved@example.test"', false)
            ->assertSee(Storage::disk('public')->url($profile->cover_photo_path), false)
            ->assertSee('data-update-url="'.url('/api/v1/buyer/account').'"', false)
            ->assertDontSee('UI demonstration only');
    }

    public function test_account_navigation_opens_only_account_details_not_platform_policies(): void
    {
        $buyerUser = User::factory()->create(['role' => User::ROLE_BUYER]);
        $this->createBuyerProfile($buyerUser);

        $this->actingAs($buyerUser)
            ->get(route('buyer.my-purchases', ['section' => 'account']))
            ->assertOk()
            ->assertSee('data-panel-content="account"', false)
            ->assertDontSee('data-panel-content="account" hidden', false)
            ->assertSee('data-panel-content="platform-policies" hidden', false)
            ->assertSee('Account Details')
            ->assertSee('Save Changes')
            ->assertSee('Platform Policies');
    }

    private function createBuyerProfile(User $user): Buyer
    {
        return Buyer::create([
            'user_id' => $user->id,
            'first_name' => 'Original',
            'last_name' => 'Buyer',
            'sex' => 'Prefer not to say',
            'contact_no' => '09171234567',
            'birthday' => '1995-01-01',
            'age' => 31,
            'province' => 'Laguna',
            'municipality' => 'Santa Rosa',
            'barangay' => 'Balibago',
            'zip_code' => '4026',
        ]);
    }

    private function validAccountData(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Alex',
            'last_name' => 'Rivera',
            'middle_name' => '',
            'sex' => 'Prefer not to say',
            'email' => 'buyer@example.test',
            'contact_no' => '09171234567',
            'birthday' => '1995-01-01',
            'province' => 'Laguna',
            'municipality' => 'Santa Rosa',
            'barangay' => 'Balibago',
            'zip_code' => '4026',
            'street' => 'Main Street',
            'house_number' => '12',
            'building' => '',
            'subdivision' => '',
        ], $overrides);
    }
}
