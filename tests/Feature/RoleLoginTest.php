<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RoleLoginTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('roleDashboardProvider')]
    public function test_users_are_sent_to_their_role_dashboard(string $role, string $dashboard): void
    {
        $user = User::factory()->create([
            'role' => $role,
            'email' => "$role@example.com",
            'password' => 'password',
        ]);

        $response = $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirectToRoute('dashboard');
        $this->get(route('dashboard'))->assertRedirectToRoute($dashboard);
        $this->assertAuthenticatedAs($user);
    }

    public static function roleDashboardProvider(): array
    {
        return [
            'admin' => [User::ROLE_ADMIN, 'admin.dashboard'],
            'buyer' => [User::ROLE_BUYER, 'buyer.dashboard'],
            'seller' => [User::ROLE_SELLER, 'seller.dashboard'],
            'logistics' => [User::ROLE_LOGISTICS, 'logistics.dashboard'],
            'rider' => [User::ROLE_RIDER, 'rider.dashboard'],
        ];
    }

    public function test_login_page_renders_with_auth_layout(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Log In')
            ->assertSee('ShopEase');
    }

    public function test_admin_dashboard_logout_button_uses_post_form(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('logout'))
            ->assertSee('method="POST"')
            ->assertSee('name="_token"');
    }

    public function test_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_a_user_cannot_open_another_role_dashboard(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
