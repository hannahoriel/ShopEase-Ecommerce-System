<?php

namespace Tests\Feature;

use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsAccountChangeRequest;
use App\Models\Logistics\LogisticsAccountSetting;
use App\Models\User;
use Database\Seeders\LogisticsAccountSeeder;
use Database\Seeders\LogisticsParcelTrackingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class LogisticsAccountApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_page_and_api_read_saved_logistics_profile_and_preferences(): void
    {
        [$user] = $this->fixture();
        LogisticsAccountChangeRequest::query()->create([
            'user_id' => $user->id, 'section' => 'profile', 'requested_data' => ['company_phone' => '09170000000'],
            'status' => 'pending',
        ]);

        $this->actingAs($user)->get('/logistics/account')->assertOk()->assertSee('Demo Cargo Company');
        $this->actingAs($user)->getJson('/api/v1/logistics/account')
            ->assertOk()->assertJsonPath('data.profile.company_name', 'Demo Cargo Company')
            ->assertJsonPath('data.preferences.notification_preferences.messages', true)
            ->assertJsonPath('data.change_requests.0.status', 'pending');
    }

    public function test_profile_changes_are_reviewed_by_admin_before_becoming_account_data(): void
    {
        [$user, $logistics] = $this->fixture();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $request = $this->actingAs($user)->postJson('/api/v1/logistics/account/profile-requests', [
            'company_name' => 'Updated Cargo Company', 'company_phone' => '09179998888', 'website' => 'https://cargo.example.test',
        ])->assertCreated()->assertJsonPath('data.status', 'pending');
        $changeId = $request->json('data.id');
        $this->assertDatabaseHas('logistics_account_change_requests', ['id' => $changeId, 'status' => 'pending']);

        $this->actingAs($user)->postJson('/api/v1/logistics/account/profile-requests', [
            'company_name' => 'Another Change',
        ])->assertUnprocessable();

        $this->actingAs($admin)->getJson('/api/v1/admin/logistics/account-change-requests?status=pending')
            ->assertOk()->assertJsonPath('data.data.0.id', $changeId);
        $this->actingAs($admin)->patchJson("/api/v1/admin/logistics/account-change-requests/{$changeId}", [
            'status' => 'approved', 'review_note' => 'Verified company details.',
        ])->assertOk()->assertJsonPath('data.status', 'approved');

        $this->assertSame('Updated Cargo Company', $logistics->fresh()->business_name);
        $this->assertSame('09179998888', $logistics->fresh()->contact_no);
        $this->assertSame('https://cargo.example.test', $logistics->fresh()->registration_details['company']['website']);

        $rejected = $this->actingAs($user)->postJson('/api/v1/logistics/account/profile-requests', [
            'company_name' => 'Unverified Name',
        ])->assertCreated();
        $this->actingAs($admin)->patchJson('/api/v1/admin/logistics/account-change-requests/'.$rejected->json('data.id'), [
            'status' => 'rejected', 'review_note' => 'Please submit supporting documents.',
        ])->assertOk()->assertJsonPath('data.status', 'rejected');
        $this->assertSame('Updated Cargo Company', $logistics->fresh()->business_name);
    }

    public function test_notification_preferences_and_cover_photo_are_saved_to_the_database(): void
    {
        [$user] = $this->fixture();
        Storage::fake('public');

        $this->actingAs($user)->putJson('/api/v1/logistics/account/preferences', [
            'notification_preferences' => ['messages' => false, 'promotions' => true],
            'privacy_settings' => ['public_company_contact' => true],
        ])->assertOk()->assertJsonPath('data.notification_preferences.messages', false)
            ->assertJsonPath('data.notification_preferences.parcel_updates', true)
            ->assertJsonPath('data.privacy_settings.public_company_contact', true);

        $photo = UploadedFile::fake()->image('cover.jpg', 900, 300);
        $this->actingAs($user)->post('/api/v1/logistics/account/cover-photo', ['cover_photo' => $photo], [
            'Accept' => 'application/json',
        ])->assertOk()->assertJsonStructure(['cover_photo_url']);

        $settings = LogisticsAccountSetting::query()->where('user_id', $user->id)->firstOrFail();
        $this->assertFalse($settings->notification_preferences['messages']);
        $this->assertTrue($settings->notification_preferences['parcel_updates']);
        Storage::disk('public')->assertExists($settings->cover_photo_path);
    }

    public function test_logistics_password_change_uses_email_verification_and_updates_password(): void
    {
        [$user] = $this->fixture();
        Mail::fake();

        $this->actingAs($user)->postJson('/api/v1/account/security/password/otp/send', [
            'current_password' => 'demo-password',
        ])->assertOk();
        Cache::put("mobile-password:change:{$user->id}:otp", [
            'hash' => Hash::make('123456'), 'attempts' => 0,
        ], now()->addMinutes(10));

        $verified = $this->actingAs($user)->postJson('/api/v1/account/security/password/otp/verify', [
            'code' => '123456',
        ])->assertOk();
        $this->actingAs($user)->postJson('/api/v1/account/security/password/change', [
            'current_password' => 'demo-password',
            'verification_token' => $verified->json('verification_token'),
            'password' => 'Better-Password-2026',
            'password_confirmation' => 'Better-Password-2026',
        ])->assertOk();
        $this->assertTrue(Hash::check('Better-Password-2026', $user->fresh()->password));
    }

    public function test_logistics_demo_account_seeder_is_repeatable_and_populates_profile_settings_and_requests(): void
    {
        $this->seed(LogisticsParcelTrackingSeeder::class);
        $this->seed(LogisticsAccountSeeder::class);
        $this->seed(LogisticsAccountSeeder::class);

        $user = User::query()->where('email', 'tracking.logistics@shopease.test')->firstOrFail();
        $this->actingAs($user)->getJson('/api/v1/logistics/account')
            ->assertOk()->assertJsonPath('data.profile.company_name', 'ShopEase Demo Logistics')
            ->assertJsonPath('data.preferences.notification_preferences.promotions', false)
            ->assertJsonPath('data.change_requests.0.status', 'pending');
        $this->assertSame(1, LogisticsAccountChangeRequest::query()->where('user_id', $user->id)->count());
    }

    public function test_logistics_forgot_password_can_verify_email_and_reset_password(): void
    {
        [$user] = $this->fixture();
        Mail::fake();
        $emailHash = hash('sha256', mb_strtolower(trim($user->email)));
        $scope = 'forgot:'.$emailHash;

        $this->postJson('/api/v1/auth/password/forgot/otp/send', ['email' => $user->email])->assertOk();
        Cache::put("mobile-password:{$scope}:otp", [
            'hash' => Hash::make('654321'), 'attempts' => 0,
        ], now()->addMinutes(10));
        $verified = $this->postJson('/api/v1/auth/password/forgot/otp/verify', [
            'email' => $user->email, 'code' => '654321',
        ])->assertOk();
        $this->postJson('/api/v1/auth/password/forgot/reset', [
            'email' => $user->email, 'verification_token' => $verified->json('verification_token'),
            'password' => 'Reset-Password-2026', 'password_confirmation' => 'Reset-Password-2026',
        ])->assertOk();
        $this->assertTrue(Hash::check('Reset-Password-2026', $user->fresh()->password));
    }

    private function fixture(): array
    {
        $user = User::factory()->create([
            'role' => User::ROLE_LOGISTICS,
            'password' => 'demo-password',
        ]);
        $logistics = Logistics::query()->create([
            'user_id' => $user->id, 'last_name' => 'Demo', 'first_name' => 'Carrier', 'sex' => 'other',
            'contact_no' => '09171234567', 'birthday' => '1990-01-01', 'age' => 36, 'province' => 'Laguna',
            'municipality' => 'Calamba', 'barangay' => 'Real', 'street' => 'Main Street', 'house_number' => '1',
            'business_name' => 'Demo Cargo Company', 'registration_status' => 'active',
            'registration_details' => ['company' => ['company_name' => 'Demo Cargo Company', 'company_email' => $user->email]],
        ]);
        LogisticsAccountSetting::query()->create([
            'user_id' => $user->id,
            'privacy_settings' => ['public_company_contact' => false, 'share_delivery_updates' => true],
            'notification_preferences' => ['messages' => true],
        ]);
        return [$user, $logistics];
    }
}
