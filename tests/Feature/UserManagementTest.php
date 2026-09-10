<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admins_can_access_user_management(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($buyer)->get(route('admin.user.management'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.user.management'))->assertOk();
    }

    public function test_admin_can_list_filter_and_view_user_details(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $buyer = User::factory()->create([
            'name' => 'Maria Santos',
            'role' => User::ROLE_BUYER,
            'registration_status' => 'active',
            'created_at' => Carbon::parse('2026-09-01 10:30:00'),
        ]);
        User::factory()->create([
            'name' => 'Liza Gomez',
            'role' => User::ROLE_SELLER,
            'registration_status' => 'deactivated',
        ]);
        User::factory()->create(['role' => User::ROLE_LOGISTICS]);

        $response = $this->actingAs($admin)->getJson(route('admin.user.management.list', [
            'search' => 'maria',
            'type' => 'buyer',
            'status' => 'active',
            'per_page' => 10,
        ]));

        $response->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.id', $buyer->id)
            ->assertJsonPath('data.0.type', 'buyer')
            ->assertJsonPath('counts.total', 2);

        $this->actingAs($admin)
            ->getJson(route('admin.user.management.show', $buyer))
            ->assertOk()
            ->assertJsonPath('details.first_name', $buyer->first_name);
    }

    public function test_admin_can_suspend_deactivate_and_activate_an_account(): void
    {
        Carbon::setTestNow('2026-09-04 12:00:00');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'registration_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->patchJson(route('admin.user.management.status', $user), [
                'status' => 'suspended',
                'reason' => 'Fraudulent activity',
                'duration' => 7,
                'details' => 'Repeated chargeback attempts',
            ])
            ->assertOk()
            ->assertJsonPath('user.status', 'suspended');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'registration_status' => 'suspended',
            'account_action_reason' => 'Fraudulent activity',
            'account_action_details' => 'Repeated chargeback attempts',
            'suspended_until' => '2026-09-11 12:00:00',
        ]);

        $this->actingAs($admin)
            ->patchJson(route('admin.user.management.status', $user), [
                'status' => 'deactivated',
                'reason' => 'Request by the user',
            ])
            ->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'registration_status' => 'deactivated',
            'suspended_until' => null,
        ]);

        $this->actingAs($admin)
            ->patchJson(route('admin.user.management.status', $user), ['status' => 'active'])
            ->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'registration_status' => 'active',
            'account_action_reason' => null,
        ]);
    }

    public function test_status_changes_require_valid_action_data(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($admin)
            ->patchJson(route('admin.user.management.status', $user), ['status' => 'suspended'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['reason', 'duration']);

        $this->actingAs($admin)
            ->patchJson(route('admin.user.management.status', $user), [
                'status' => 'suspended',
                'reason' => 'Request by the user',
                'duration' => 3,
            ])
            ->assertUnprocessable();

        $adminTarget = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin)
            ->patchJson(route('admin.user.management.status', $adminTarget), ['status' => 'active'])
            ->assertNotFound();
    }
}
