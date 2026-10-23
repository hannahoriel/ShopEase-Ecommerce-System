<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApiSurfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_load_each_admin_api_resource(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        foreach ([
            '/api/v1/admin/dashboard',
            '/api/v1/admin/messages',
            '/api/v1/admin/commissions',
            '/api/v1/admin/notifications',
            '/api/v1/admin/account',
            '/api/v1/admin/logistics-management',
            '/api/v1/admin/complaints',
            '/api/v1/admin/platform-settings',
            '/api/v1/admin/registrations',
            '/api/v1/admin/seller-compliance',
            '/api/v1/admin/users',
        ] as $endpoint) {
            $this->getJson($endpoint)->assertOk();
        }
    }

    public function test_admin_api_resources_reject_non_admin_users(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->getJson('/api/v1/admin/dashboard')
            ->assertForbidden();

        $this->getJson('/api/v1/admin/account')
            ->assertForbidden();

        $this->getJson('/api/v1/admin/platform-settings')
            ->assertForbidden();
    }
}
