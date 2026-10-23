<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class BuyerAccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_receives_email_otp_and_can_change_password_after_verification(): void
    {
        $buyer = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'password' => Hash::make('Old-password-123'),
        ]);
        $sentCode = null;

        Mail::shouldReceive('raw')
            ->once()
            ->withArgs(function (string $text, callable $callback) use ($buyer, &$sentCode): bool {
                preg_match('/verification code is (\d{6})\./', $text, $matches);
                $sentCode = $matches[1] ?? null;

                $message = Mockery::mock();
                $message->shouldReceive('to')->once()->with($buyer->email)->andReturnSelf();
                $message->shouldReceive('subject')->once()->with('ShopEase Account Security Verification')->andReturnSelf();
                $callback($message);

                return $sentCode !== null;
            });

        $this->actingAs($buyer)
            ->postJson('/api/v1/buyer/account/security/otp/send', [
                'purpose' => 'change',
                'current_password' => 'Old-password-123',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'A verification code was sent to your registered email.');

        $storedOtp = Cache::get("buyer-security-otp:{$buyer->id}:change");
        $this->assertNotNull($sentCode);
        $this->assertTrue(Hash::check($sentCode, $storedOtp['hash']));

        $verificationToken = $this->postJson('/api/v1/buyer/account/security/otp/verify', [
            'purpose' => 'change',
            'code' => $sentCode,
        ])->assertOk()->json('verification_token');

        $this->postJson('/api/v1/buyer/account/security/password/change', [
            'current_password' => 'Old-password-123',
            'new_password' => 'New-password-456',
            'new_password_confirmation' => 'New-password-456',
            'verification_token' => $verificationToken,
        ])->assertOk()->assertJsonPath('message', 'Password changed successfully.');

        $this->assertTrue(Hash::check('New-password-456', $buyer->fresh()->password));
        $this->assertNull(Cache::get("buyer-security-verified:{$buyer->id}:change"));
    }

    public function test_buyer_must_provide_the_current_password_to_request_a_change_code(): void
    {
        Mail::shouldReceive('raw')->never();
        $buyer = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'password' => Hash::make('Correct-password-123'),
        ]);

        $this->actingAs($buyer)
            ->postJson('/api/v1/buyer/account/security/otp/send', [
                'purpose' => 'change',
                'current_password' => 'Wrong-password-123',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('current_password');
    }

    public function test_buyer_can_reset_password_with_a_verified_forgot_password_code(): void
    {
        $buyer = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'password' => Hash::make('Old-password-123'),
        ]);
        $verificationToken = 'buyer-reset-token';
        Cache::put(
            "buyer-security-verified:{$buyer->id}:forgot",
            Hash::make($verificationToken),
            now()->addMinutes(5)
        );

        $this->actingAs($buyer)
            ->postJson('/api/v1/buyer/account/security/password/reset', [
                'new_password' => 'Reset-password-456',
                'new_password_confirmation' => 'Reset-password-456',
                'verification_token' => $verificationToken,
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Password reset successfully.');

        $this->assertTrue(Hash::check('Reset-password-456', $buyer->fresh()->password));
    }

    public function test_security_routes_are_limited_to_authenticated_buyers(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->postJson('/api/v1/buyer/account/security/otp/send', [
                'purpose' => 'forgot',
            ])
            ->assertForbidden();
    }

    public function test_unauthenticated_users_cannot_access_buyer_security_routes(): void
    {
        $this->postJson('/api/v1/buyer/account/security/otp/send', [
            'purpose' => 'forgot',
        ])->assertUnauthorized();
    }
}
