<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAccountProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_profile_fields_and_the_changes_are_loaded_again(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'name' => 'Old Admin Name',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.account'))
            ->assertOk()
            ->assertSee('href="'.route('admin.account').'"', false)
            ->assertSee('Old Admin Name');

        $this->put(route('admin.account.update'), [
            'first_name' => 'Taylor',
            'last_name' => 'Admin',
            'birthday' => '1990-05-12',
            'sex' => 'Prefer not to say',
            'contact_number' => '9123456789',
        ])->assertRedirect(route('admin.account'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('admin_account_profiles', [
            'user_id' => $admin->id,
            'first_name' => 'Taylor',
            'last_name' => 'Admin',
            'birthday' => '1990-05-12',
            'sex' => 'Prefer not to say',
            'contact_number' => '9123456789',
        ]);
        $this->assertSame('Taylor Admin', $admin->fresh()->name);

        $this->get(route('admin.account'))
            ->assertOk()
            ->assertSee('value="Taylor"', false)
            ->assertSee('value="Admin"', false)
            ->assertSee('value="1990-05-12"', false)
            ->assertSee('value="9123456789"', false);
    }

    public function test_admin_can_upload_profile_and_cover_photos_and_the_saved_photos_are_displayed(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->put(route('admin.account.update'), [
                'first_name' => 'Photo',
                'last_name' => 'Admin',
                'profile_photo' => UploadedFile::fake()->image('profile.png'),
                'cover_photo' => UploadedFile::fake()->image('cover.jpg'),
            ])->assertRedirect(route('admin.account'));

        $profile = (array) \Illuminate\Support\Facades\DB::table('admin_account_profiles')
            ->where('user_id', $admin->id)
            ->first();
        $this->assertNotEmpty($profile['profile_photo_path']);
        $this->assertNotEmpty($profile['cover_photo_path']);
        Storage::disk('public')->assertExists($profile['profile_photo_path']);
        Storage::disk('public')->assertExists($profile['cover_photo_path']);

        $this->get(route('admin.account'))
            ->assertOk()
            ->assertSee(Storage::disk('public')->url($profile['profile_photo_path']), false)
            ->assertSee(Storage::disk('public')->url($profile['cover_photo_path']), false);
    }

    public function test_profile_update_rejects_invalid_contact_number_without_saving_profile_changes(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->from(route('admin.account'))
            ->put(route('admin.account.update'), [
                'first_name' => 'Rejected',
                'last_name' => 'Update',
                'contact_number' => 'not-a-phone-number',
            ])
            ->assertRedirect(route('admin.account'))
            ->assertSessionHasErrors('contact_number');

        $this->assertDatabaseMissing('admin_account_profiles', [
            'user_id' => $admin->id,
            'first_name' => 'Rejected',
        ]);
        $this->assertNotSame('Rejected Update', $admin->fresh()->name);
    }

    public function test_non_admin_cannot_view_or_update_admin_profile(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->get(route('admin.account'))
            ->assertForbidden();

        $this->put(route('admin.account.update'), [
            'first_name' => 'Unauthorized',
            'last_name' => 'Update',
        ])->assertForbidden();

        $this->assertDatabaseMissing('admin_account_profiles', [
            'user_id' => $buyer->id,
        ]);
    }
}
