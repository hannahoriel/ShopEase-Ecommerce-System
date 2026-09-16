<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_issues_a_token_without_authenticating_a_web_session(): void
    {
        $user = User::factory()->create([
            'email' => 'api.user@example.com',
            'password' => 'Password123!',
            'role' => User::ROLE_BUYER,
            'registration_status' => 'active',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);

        $response->assertOk()
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonPath('role', $user->role)
            ->assertJsonStructure(['message', 'user', 'role', 'token']);

        $this->assertGuest();
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);
    }

    public function test_api_logout_revokes_the_current_token(): void
    {
        $user = User::factory()->create([
            'email' => 'api.logout@example.com',
            'password' => 'Password123!',
            'role' => User::ROLE_BUYER,
            'registration_status' => 'active',
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);
        $token = $login->json('token');

        $this->withToken($token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJson(['message' => 'Logout successful.']);

        $this->assertDatabaseCount('personal_access_tokens', 0);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
