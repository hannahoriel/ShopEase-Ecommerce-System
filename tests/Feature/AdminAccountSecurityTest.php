<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class AdminAccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_privacy_page_is_connected_to_password_security_routes(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.account'))
            ->assertOk()
            ->assertSee('Resend Code')
            ->assertSee('admin\\/account\\/security')
            ->assertSee('resources/js/admin/account.js', false)
            ->assertDontSee('Email delivery has not been configured.')
            ->assertDontSee('Password reset is not yet configured.');
    }

    public function test_admin_otp_is_emailed_and_first_resend_is_immediate_then_rate_limited_for_ten_minutes(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $sentCode = null;

        Mail::shouldReceive('raw')
            ->twice()
            ->withArgs(function (string $text, callable $callback) use ($admin, &$sentCode): bool {
                preg_match('/verification code is (\d{6})\./', $text, $matches);
                $sentCode = $matches[1] ?? null;

                $message = Mockery::mock();
                $message->shouldReceive('to')->once()->with($admin->email)->andReturnSelf();
                $message->shouldReceive('subject')->once()->with('ShopEase Admin Account Security Verification')->andReturnSelf();
                $callback($message);

                return $sentCode !== null;
            });

        $this->actingAs($admin)
            ->postJson(route('admin.account.security.otp.send'), ['purpose' => 'forgot'])
            ->assertOk()
            ->assertJsonPath('message', 'A verification code was sent to your registered email.');

        $record = Cache::get("admin-security-otp:{$admin->id}:forgot");
        $this->assertNotNull($sentCode);
        $this->assertTrue(Hash::check($sentCode, $record['hash']));

        $this->postJson(route('admin.account.security.otp.send'), ['purpose' => 'forgot'])
            ->assertOk();

        $this->postJson(route('admin.account.security.otp.send'), ['purpose' => 'forgot'])
            ->assertStatus(429)
            ->assertJsonPath('retry_after', 600)
            ->assertHeader('Retry-After', '600');
    }

    public function test_admin_can_verify_email_and_change_password(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'password' => Hash::make('Old-password-123'),
        ]);
        $sentCode = null;
        Mail::shouldReceive('raw')
            ->once()
            ->withArgs(function (string $text, callable $callback) use ($admin, &$sentCode): bool {
                preg_match('/verification code is (\d{6})\./', $text, $matches);
                $sentCode = $matches[1] ?? null;
                $message = Mockery::mock();
                $message->shouldReceive('to')->once()->with($admin->email)->andReturnSelf();
                $message->shouldReceive('subject')->once()->with('ShopEase Admin Account Security Verification')->andReturnSelf();
                $callback($message);

                return $sentCode !== null;
            });

        $this->actingAs($admin)
            ->postJson(route('admin.account.security.otp.send'), [
                'purpose' => 'change',
                'current_password' => 'Old-password-123',
            ])
            ->assertOk();

        $verificationToken = $this->postJson(route('admin.account.security.otp.verify'), [
            'purpose' => 'change',
            'code' => $sentCode,
        ])->assertOk()->json('verification_token');

        $this->postJson(route('admin.account.security.password.change'), [
            'current_password' => 'Old-password-123',
            'new_password' => 'New-password-456',
            'new_password_confirmation' => 'New-password-456',
            'verification_token' => $verificationToken,
        ])->assertOk()
            ->assertJsonPath('message', 'Password changed successfully.');

        $this->assertTrue(Hash::check('New-password-456', $admin->fresh()->password));
        $this->postJson(route('admin.account.security.password.change'), [
            'current_password' => 'Old-password-123',
            'new_password' => 'Another-password-789',
            'new_password_confirmation' => 'Another-password-789',
            'verification_token' => $verificationToken,
        ])->assertUnprocessable();
    }

    public function test_admin_can_verify_email_and_reset_password(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'password' => Hash::make('Old-password-123'),
        ]);
        $sentCode = null;
        Mail::shouldReceive('raw')
            ->once()
            ->withArgs(function (string $text, callable $callback) use ($admin, &$sentCode): bool {
                preg_match('/verification code is (\d{6})\./', $text, $matches);
                $sentCode = $matches[1] ?? null;
                $message = Mockery::mock();
                $message->shouldReceive('to')->once()->with($admin->email)->andReturnSelf();
                $message->shouldReceive('subject')->once()->with('ShopEase Admin Account Security Verification')->andReturnSelf();
                $callback($message);

                return $sentCode !== null;
            });

        $this->actingAs($admin)
            ->postJson(route('admin.account.security.otp.send'), ['purpose' => 'forgot'])
            ->assertOk();

        $verificationToken = $this->postJson(route('admin.account.security.otp.verify'), [
            'purpose' => 'forgot',
            'code' => $sentCode,
        ])->assertOk()->json('verification_token');

        $this->postJson(route('admin.account.security.password.reset'), [
            'new_password' => 'Reset-password-789',
            'new_password_confirmation' => 'Reset-password-789',
            'verification_token' => $verificationToken,
        ])->assertOk()
            ->assertJsonPath('message', 'Password reset successfully.');

        $this->assertTrue(Hash::check('Reset-password-789', $admin->fresh()->password));
    }

    public function test_sending_change_password_otp_requires_correct_current_password(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'password' => Hash::make('Old-password-123'),
        ]);

        Mail::shouldReceive('raw')->never();

        $this->actingAs($admin)
            ->postJson(route('admin.account.security.otp.send'), [
                'purpose' => 'change',
                'current_password' => 'incorrect-password',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('current_password');
    }

    public function test_non_admin_cannot_use_admin_password_security_endpoints(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->postJson(route('admin.account.security.otp.send'), ['purpose' => 'forgot'])
            ->assertForbidden();
    }
}
