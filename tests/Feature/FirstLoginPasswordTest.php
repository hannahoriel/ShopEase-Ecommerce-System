<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FirstLoginPasswordTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('provisionedRoles')]
    public function test_emailed_password_requires_setup_before_authentication(string $role, string $login, string $dashboard): void
    {
        $temporary = 'EmailedPass123!';
        $replacement = 'Replacement123!';
        $user = User::create([
            'name' => 'Provisioned Account',
            'email' => 'provisioned@example.test',
            'role' => $role,
            'registration_status' => 'active',
            'password' => $temporary,
            'must_change_password' => true,
        ]);

        if ($role === User::ROLE_DELIVERY_HUB) {
            $companyUser = User::create([
                'name' => 'Provisioned Logistics',
                'email' => 'provisioned-logistics@example.test',
                'role' => User::ROLE_LOGISTICS,
                'registration_status' => 'active',
                'password' => $temporary,
            ]);
            $company = Logistics::create([
                'user_id' => $companyUser->id,
                'last_name' => 'Logistics', 'first_name' => 'Provisioned', 'sex' => 'other',
                'contact_no' => '09170000000', 'birthday' => '1990-01-01', 'age' => 36,
                'province' => 'Laguna', 'municipality' => 'Pila', 'barangay' => 'Poblacion',
                'street' => 'Main Street', 'house_number' => '1',
                'business_name' => 'Provisioned Logistics', 'registration_status' => 'active',
            ]);
            $centerUser = User::create([
                'name' => 'Provisioned Sorting Center',
                'email' => 'provisioned-center@example.test',
                'role' => User::ROLE_SORTING_CENTER,
                'registration_status' => 'active',
                'password' => $temporary,
            ]);
            $branch = LogisticsBranch::create([
                'logistics_id' => $company->id,
                'user_id' => $centerUser->id,
                'name' => 'Provisioned Sorting Center',
                'address' => 'Pila, Laguna',
                'province' => 'Laguna',
                'municipality' => 'Pila',
                'status' => 'active',
            ]);
            LogisticsDeliveryHub::create([
                'logistics_branch_id' => $branch->id,
                'user_id' => $user->id,
                'hub_name' => 'Provisioned Delivery Hub',
                'province' => 'Laguna', 'municipality' => 'Pila', 'barangay' => 'Poblacion',
                'postal_code' => '4010', 'street' => 'Main Street',
                'manager_name' => 'Hub Manager', 'manager_email' => $user->email,
                'manager_phone' => '09170000001', 'login_email' => $user->email,
                'status' => 'active',
            ]);
        }

        $credentials = ['email' => $user->email, 'password' => $temporary, 'remember' => '0'];

        $this->postJson(route('login.first-password'), [
            'password' => $replacement,
            'password_confirmation' => $replacement,
        ])->assertUnprocessable();
        $this->postJson('/api/v1/auth/login', $credentials)->assertUnprocessable();
        $this->assertGuest();

        if ($role === User::ROLE_ADMIN) {
            $this->post(route($login), $credentials)->assertRedirect(route('login', ['modal' => 1, 'setup' => 1]));
            $this->get(route('login', ['modal' => 1, 'setup' => 1]))->assertOk()->assertSee('Create a new password');
        } else {
            $this->postJson(route($login), $credentials)->assertOk()->assertJsonPath('password_setup_required', true);
        }
        $this->assertGuest();
        $this->postJson(route('login.first-password'), [
            'password' => $replacement,
            'password_confirmation' => $temporary,
        ])->assertJsonValidationErrors('password');
        $this->postJson(route('login.first-password'), [
            'password' => $temporary,
            'password_confirmation' => $temporary,
        ])->assertJsonValidationErrors('password');
        $this->assertGuest();

        $this->postJson(route('login.first-password'), [
            'password' => $replacement,
            'password_confirmation' => $replacement,
        ])->assertRedirectToRoute($dashboard);
        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->fresh()->must_change_password);
        $this->assertFalse(Hash::check($temporary, $user->fresh()->password));

        $this->post(route('logout'));
        $this->postJson(route($login), $credentials)->assertJsonValidationErrors('email');
        $this->post(route($login), array_replace($credentials, ['password' => $replacement]))->assertRedirectToRoute($dashboard);
    }

    public static function provisionedRoles(): array
    {
        return [
            [User::ROLE_ADMIN, 'login.attempt', 'admin.dashboard'],
            [User::ROLE_SORTING_CENTER, 'logistics.login.attempt', 'sorting-center.dashboard'],
            [User::ROLE_DELIVERY_HUB, 'logistics.login.attempt', 'delivery-hub.dashboard'],
        ];
    }
}

