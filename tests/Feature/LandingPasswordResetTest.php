<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LandingPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('portals')]
    public function test_password_reset_requires_an_emailed_code(string $portal, string $role, string $page): void
    {
        config(['mail.default' => 'array']);
        $code = null;
        Event::listen(MessageSent::class, function (MessageSent $event) use (&$code): void {
            preg_match('/\b[0-9]{6}\b/', strip_tags($event->message->getHtmlBody() ?? ''), $match);
            $code = $match[0] ?? null;
        });

        $user = User::create([
            'name' => 'Recovery Test',
            'email' => 'recovery@example.test',
            'role' => $role,
            'password' => 'OldPassword123!',
            'email_verified_at' => $role === User::ROLE_BUYER ? null : now(),
            'registration_status' => 'active',
        ]);
        $password = ['password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'];

        $this->get($page)->assertOk()->assertSee('Forgot your password?')->assertSee('Verify Your Email');
        $this->postJson(route('landing.recovery.reset'), $password)->assertUnprocessable();
        $this->postJson(route('landing.recovery.send'), ['email' => $user->email, 'portal' => $portal])->assertOk();
        $this->postJson(route('landing.recovery.send'), ['email' => $user->email, 'portal' => $portal])
            ->assertStatus(429)->assertJsonStructure(['retry_after']);
        $this->travel(61)->seconds();
        $code = null;
        $this->postJson(route('landing.recovery.send'), ['email' => $user->email, 'portal' => $portal])
            ->assertOk()->assertJsonPath('retry_after', 60);
        $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $code ?? '');
        $this->postJson(route('landing.recovery.verify'), ['code' => '000000'])->assertUnprocessable();
        $this->postJson(route('landing.recovery.verify'), ['code' => $code])->assertOk();
        $this->postJson(route('landing.recovery.reset'), $password)->assertOk();
        $this->assertTrue(Hash::check($password['password'], $user->fresh()->password));
        $this->postJson(route('landing.recovery.reset'), $password)->assertUnprocessable();
        $this->assertGuest();
    }

    public static function portals(): array
    {
        return [
            ['main', User::ROLE_BUYER, '/auth/login?modal=1'],
            ['main', User::ROLE_SELLER, '/auth/login?modal=1'],
            ['main', User::ROLE_ADMIN, '/auth/login?modal=1'],
            ['logistics', User::ROLE_DELIVERY_HUB, '/logistics/landing'],
        ];
    }
}
