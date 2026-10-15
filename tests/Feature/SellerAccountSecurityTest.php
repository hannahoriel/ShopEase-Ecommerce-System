<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class SellerAccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_sending_otp_emails_a_code_to_the_authenticated_sellers_email(): void
    {
        $seller = User::factory()->create(['role' => User::ROLE_SELLER]);
        $sentCode = null;

        Mail::shouldReceive('raw')
            ->twice()
            ->withArgs(function (string $text, callable $callback) use ($seller, &$sentCode): bool {
                preg_match('/verification code is (\d{6})\./', $text, $matches);
                $sentCode = $matches[1] ?? null;

                $message = Mockery::mock();
                $message->shouldReceive('to')->once()->with($seller->email)->andReturnSelf();
                $message->shouldReceive('subject')->once()->with('ShopEase Account Security Verification')->andReturnSelf();
                $callback($message);

                return $sentCode !== null;
            });

        $this->actingAs($seller)
            ->postJson('/seller/account/settings/otp/send', ['purpose' => 'change'])
            ->assertOk()
            ->assertJsonPath('message', 'A verification code was sent to your registered email.');

        $record = Cache::get("seller-security-otp:{$seller->id}:change");
        $this->assertNotNull($sentCode);
        $this->assertTrue(Hash::check($sentCode, $record['hash']));

        $this->actingAs($seller)
            ->postJson('/seller/account/settings/otp/send', ['purpose' => 'change'])
            ->assertOk()
            ->assertJsonPath('message', 'A verification code was sent to your registered email.');

        $this->actingAs($seller)
            ->postJson('/seller/account/settings/otp/send', ['purpose' => 'change'])
            ->assertStatus(429)
            ->assertJsonPath('message', 'Please wait before requesting another verification code.')
            ->assertJsonPath('retry_after', 600)
            ->assertHeader('Retry-After', '600');
    }

    public function test_verified_email_allows_seller_to_change_password(): void
    {
        $seller = User::factory()->create([
            'role' => User::ROLE_SELLER,
            'password' => Hash::make('Old-password-123'),
        ]);
        $code = '123456';
        Cache::put("seller-security-otp:{$seller->id}:change", [
            'hash' => Hash::make($code),
            'attempts' => 0,
        ], now()->addMinutes(10));

        $verification = $this->actingAs($seller)
            ->postJson('/seller/account/settings/otp/verify', [
                'purpose' => 'change',
                'code' => $code,
            ])
            ->assertOk()
            ->json('verification_token');

        $this->postJson('/seller/account/settings/password/change', [
            'current_password' => 'Old-password-123',
            'new_password' => 'New-password-456',
            'new_password_confirmation' => 'New-password-456',
            'verification_token' => $verification,
        ])->assertOk();

        $this->assertTrue(Hash::check('New-password-456', $seller->fresh()->password));
    }

    public function test_verified_email_allows_seller_to_reset_password(): void
    {
        $seller = User::factory()->create([
            'role' => User::ROLE_SELLER,
            'password' => Hash::make('Old-password-123'),
        ]);
        $verification = 'verified-reset-token';
        Cache::put("seller-security-verified:{$seller->id}:forgot", Hash::make($verification), now()->addMinutes(5));

        $this->actingAs($seller)
            ->postJson('/seller/account/settings/password/reset', [
                'new_password' => 'Reset-password-789',
                'new_password_confirmation' => 'Reset-password-789',
                'verification_token' => $verification,
            ])
            ->assertOk();

        $this->assertTrue(Hash::check('Reset-password-789', $seller->fresh()->password));
    }
}
