<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_admin_can_create_and_list_a_restricted_admin_account(): void
    {
        $fullAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($fullAdmin)
            ->postJson('/api/v1/admin/account/admins', [
                'first_name' => 'Alex',
                'middle_name' => 'M',
                'last_name' => 'Rivera',
                'email' => 'alex.rivera@example.com',
                'contact_number' => '09171234567',
                'birthday' => '1990-01-01',
                'sex' => 'Prefer not to say',
                'password' => 'Strong-Pass-2026',
                'permissions' => ['Dashboard', 'Messages'],
                'message_permissions' => ['Buyer', 'Seller'],
            ])
            ->assertCreated()
            ->assertJsonPath('admin.name', 'Alex M Rivera')
            ->assertJsonPath('admin.permissions.0', 'Dashboard')
            ->assertJsonPath('admin.message_permissions.0', 'Buyer')
            ->assertJsonMissingPath('admin.password');

        $newAdmin = User::query()->where('email', 'alex.rivera@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_ADMIN, $newAdmin->role);
        $this->assertTrue(Hash::check('Strong-Pass-2026', $newAdmin->password));
        $this->assertDatabaseHas('admin_account_profiles', [
            'user_id' => $newAdmin->id,
            'is_enabled' => true,
        ]);

        $this->actingAs($fullAdmin)
            ->getJson('/api/v1/admin/account/admins')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $newAdmin->id,
                'email' => 'alex.rivera@example.com',
            ]);

        $this->actingAs($newAdmin)
            ->getJson('/api/v1/admin/messages?type=buyers')
            ->assertOk();

        $this->actingAs($newAdmin)
            ->getJson('/api/v1/admin/messages?type=logistics')
            ->assertForbidden();
    }

    public function test_admin_module_permissions_and_enabled_status_are_enforced(): void
    {
        $fullAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($fullAdmin)
            ->postJson('/api/v1/admin/account/admins', [
                'first_name' => 'Limited',
                'last_name' => 'Admin',
                'email' => 'limited.admin@example.com',
                'password' => 'Strong-Pass-2026',
                'permissions' => ['Dashboard'],
            ])
            ->assertCreated();

        $limitedAdmin = User::query()->where('email', 'limited.admin@example.com')->firstOrFail();

        $this->actingAs($limitedAdmin)
            ->getJson('/api/v1/admin/dashboard')
            ->assertOk();

        $this->actingAs($limitedAdmin)
            ->getJson('/api/v1/admin/registrations')
            ->assertOk();

        $this->actingAs($limitedAdmin)
            ->get('/admin/registrations')
            ->assertOk()
            ->assertSee('Read-only access');

        $this->actingAs($limitedAdmin)
            ->postJson('/api/v1/admin/account/admins', [])
            ->assertForbidden();

        $this->actingAs($limitedAdmin)
            ->getJson('/api/v1/admin/account/admins')
            ->assertOk();

        $this->actingAs($fullAdmin)
            ->patchJson("/api/v1/admin/account/admins/{$limitedAdmin->id}", [
                'is_enabled' => false,
            ])
            ->assertOk();

        $this->actingAs($limitedAdmin)
            ->getJson('/api/v1/admin/dashboard')
            ->assertForbidden();
    }

    public function test_restricted_admin_can_edit_assigned_module_but_not_other_modules(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $restrictedAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        DB::table('admin_account_profiles')->insert([
            'user_id' => $restrictedAdmin->id,
            'permissions' => json_encode(['User Management']),
            'message_permissions' => json_encode([]),
            'is_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $buyer = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'registration_status' => 'active',
        ]);

        $this->actingAs($restrictedAdmin)
            ->getJson('/api/v1/admin/complaints')
            ->assertOk();

        $this->actingAs($restrictedAdmin)
            ->patchJson("/api/v1/admin/users/{$buyer->id}/status", [
                'status' => 'suspended',
                'reason' => 'Violation of platform policies',
                'duration' => 7,
            ])
            ->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
            'registration_status' => 'suspended',
        ]);

        $this->actingAs($restrictedAdmin)
            ->postJson('/api/v1/admin/platform-settings/announcements', [])
            ->assertForbidden();
    }

    public function test_non_admin_and_restricted_admin_cannot_manage_admin_accounts(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $this->actingAs($buyer)
            ->getJson('/api/v1/admin/account/admins')
            ->assertForbidden();

        $restrictedAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        \Illuminate\Support\Facades\DB::table('admin_account_profiles')->insert([
            'user_id' => $restrictedAdmin->id,
            'permissions' => json_encode(['Dashboard']),
            'message_permissions' => json_encode([]),
            'is_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($restrictedAdmin)
            ->postJson('/api/v1/admin/account/admins', [
                'first_name' => 'Unauthorized',
                'last_name' => 'Admin',
                'email' => 'unauthorized.admin@example.com',
                'password' => 'Strong-Pass-2026',
                'permissions' => ['Dashboard'],
            ])
            ->assertForbidden();
    }

    public function test_admin_id_photo_is_stored_privately_and_requires_authenticated_access(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $fullAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($fullAdmin)
            ->post('/api/v1/admin/account/admins', [
                'first_name' => 'Photo',
                'last_name' => 'Admin',
                'email' => 'photo.admin@example.com',
                'password' => 'Strong-Pass-2026',
                'permissions' => ['Account Management'],
                'admin_id_photo' => UploadedFile::fake()->image('admin-id.png'),
            ], ['Accept' => 'application/json']);

        $response->assertCreated();
        $newAdmin = User::query()->where('email', 'photo.admin@example.com')->firstOrFail();
        $photoPath = DB::table('admin_account_profiles')
            ->where('user_id', $newAdmin->id)
            ->value('admin_id_photo_path');

        $this->assertNotEmpty($photoPath);
        Storage::disk('local')->assertExists($photoPath);
        Storage::disk('public')->assertMissing($photoPath);

        $this->actingAs($newAdmin)
            ->get("/api/v1/admin/account/admins/{$newAdmin->id}/id-photo")
            ->assertOk();
    }
}
