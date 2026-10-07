<?php

namespace Tests\Feature;

use App\Mail\RegistrationApproved;
use App\Models\Admin\Registration;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Rider\Rider;
use App\Models\User;
use Database\Seeders\LogisticsManagementSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminLogisticsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_page_and_api_load_real_logistics_requests_companies_and_archive(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->createLogisticsApplication('pending');
        $active = $this->createLogisticsApplication('approved', 'Active Carrier');
        $rejected = $this->createLogisticsApplication('rejected', 'Rejected Carrier');
        LogisticsBranch::create([
            'logistics_id' => $active['profile']->id,
            'name' => 'Calamba Hub',
            'address' => 'Calamba, Laguna',
        ]);

        $this->actingAs($admin)
            ->get('/admin/logistics-management')
            ->assertOk()
            ->assertSee('logisticsManagementConfig')
            ->assertSee('Active Carrier');

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/logistics-management')
            ->assertOk()
            ->assertJsonCount(1, 'pendingRequests')
            ->assertJsonCount(1, 'companies')
            ->assertJsonCount(1, 'rejectedArchive')
            ->assertJsonPath('companies.0.company', 'Active Carrier')
            ->assertJsonPath('companies.0.branches', 1)
            ->assertJsonPath('stats.pending_requests', 1)
            ->assertJsonPath('stats.accepted_logistics', 1)
            ->assertJsonPath('stats.rejected_logistics', 1);

        $this->assertSame('Rejected Carrier', $rejected['registration']->business_name);
    }

    public function test_admin_can_approve_or_reject_logistics_applications_from_the_ui_endpoints(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $pending = $this->createLogisticsApplication('pending', 'Approve Carrier');

        $this->actingAs($admin)
            ->postJson("/admin/logistics-management/api/registrations/{$pending['registration']->id}/approve")
            ->assertOk()
            ->assertJsonPath('counts.approved', 1);

        Mail::assertSent(RegistrationApproved::class, function (RegistrationApproved $mail): bool {
            $this->assertStringContainsString('Welcome to ShopEase', $mail->render());

            return true;
        });

        $this->assertDatabaseHas('logistics', [
            'id' => $pending['profile']->id,
            'registration_status' => 'active',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $pending['user']->id,
            'registration_status' => 'active',
        ]);

        $toReject = $this->createLogisticsApplication('pending', 'Reject Carrier');
        $this->actingAs($admin)
            ->postJson("/admin/logistics-management/api/registrations/{$toReject['registration']->id}/reject", [
                'reason' => 'Insufficient delivery coverage or operational capacity.',
                'details' => 'Limited region coverage.',
            ])
            ->assertOk()
            ->assertJsonPath('counts.rejected', 1);

        $this->assertDatabaseHas('registrations', [
            'id' => $toReject['registration']->id,
            'status' => 'rejected',
            'rejection_reason' => 'Insufficient delivery coverage or operational capacity.',
            'rejection_details' => 'Limited region coverage.',
        ]);
        $this->assertDatabaseHas('logistics', [
            'id' => $toReject['profile']->id,
            'registration_status' => 'rejected',
        ]);
    }

    public function test_admin_can_manage_company_branches_and_branch_rider_counts(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $application = $this->createLogisticsApplication('approved', 'Branch Carrier');
        $branch = $this->actingAs($admin)
            ->postJson("/admin/logistics-management/api/companies/{$application['profile']->id}/branches", [
                'name' => 'Manila Hub',
                'address' => 'Manila',
                'contact_person' => 'Alex Santos',
                'phone' => '09171234567',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Manila Hub')
            ->json('data');

        $branchModel = LogisticsBranch::findOrFail($branch['id']);
        $riderUser = User::factory()->create(['role' => User::ROLE_RIDER]);
        Rider::create([
            'user_id' => $riderUser->id,
            'logistics_branch_id' => $branchModel->id,
            'last_name' => 'Rider',
            'first_name' => 'Test',
            'sex' => 'other',
            'contact_no' => '09170000000',
            'birthday' => '1990-01-01',
            'age' => 36,
            'province' => 'Metro Manila',
            'municipality' => 'Manila',
            'barangay' => 'Barangay 1',
            'street' => 'Main Street',
            'house_number' => '1',
            'vehicle' => 'motorcycle',
            'plate_number' => 'ABC 1234',
        ]);

        $this->actingAs($admin)
            ->getJson("/api/v1/admin/logistics-management/companies/{$application['profile']->id}/branches")
            ->assertOk()
            ->assertJsonPath('data.0.riders', 1);

        $this->actingAs($admin)
            ->patchJson("/admin/logistics-management/api/branches/{$branch['id']}", [
                'name' => 'Manila Central Hub',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Manila Central Hub');

        $this->actingAs($admin)
            ->deleteJson("/admin/logistics-management/api/branches/{$branch['id']}")
            ->assertOk();

        $this->assertDatabaseMissing('logistics_branches', ['id' => $branch['id']]);
        $this->assertNull($riderUser->riderProfile()->firstOrFail()->logistics_branch_id);
    }

    public function test_logistics_management_endpoints_require_an_admin(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->get('/admin/logistics-management')
            ->assertForbidden();

        $this->actingAs($buyer)
            ->getJson('/api/v1/admin/logistics-management')
            ->assertForbidden();
    }

    public function test_logistics_management_seeder_is_repeatable_and_populates_connected_dashboard_data(): void
    {
        $this->seed(LogisticsManagementSeeder::class);
        $this->seed(LogisticsManagementSeeder::class);

        $this->assertDatabaseCount('registrations', 9);
        $this->assertDatabaseCount('logistics', 9);
        $this->assertDatabaseCount('logistics_branches', 4);
        $this->assertDatabaseCount('riders', 1);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/logistics-management')
            ->assertOk()
            ->assertJsonCount(3, 'pendingRequests')
            ->assertJsonCount(3, 'companies')
            ->assertJsonCount(3, 'rejectedArchive')
            ->assertJsonPath('companies.0.company', 'Demo Logistics - Active')
            ->assertJsonPath('companies.0.branches', 2)
            ->assertJsonPath('companies.0.riders', 1)
            ->assertJsonPath('stats.pending_requests', 3)
            ->assertJsonPath('stats.accepted_logistics', 3)
            ->assertJsonPath('stats.rejected_logistics', 3);
    }

    /**
     * @return array{user: User, profile: Logistics, registration: Registration}
     */
    private function createLogisticsApplication(string $status, string $businessName = 'Pending Carrier'): array
    {
        $user = User::factory()->create([
            'name' => 'Carrier Contact',
            'role' => User::ROLE_LOGISTICS,
            'registration_status' => $status === 'approved' ? 'active' : $status,
            'approved_at' => $status === 'approved' ? now() : null,
            'rejected_at' => $status === 'rejected' ? now() : null,
        ]);
        $profile = Logistics::create([
            'user_id' => $user->id,
            'last_name' => 'Contact',
            'first_name' => 'Carrier',
            'sex' => 'other',
            'contact_no' => '09171234567',
            'birthday' => '1990-01-01',
            'age' => 36,
            'province' => 'Laguna',
            'municipality' => 'Calamba',
            'barangay' => 'Real',
            'street' => 'Main Street',
            'house_number' => '1',
            'business_name' => $businessName,
            'registration_status' => $status === 'approved' ? 'active' : $status,
            'approved_at' => $status === 'approved' ? now() : null,
            'rejected_at' => $status === 'rejected' ? now() : null,
        ]);
        $registration = Registration::create([
            'user_type' => User::ROLE_LOGISTICS,
            'last_name' => 'Contact',
            'first_name' => 'Carrier',
            'sex' => 'other',
            'birthdate' => '1990-01-01',
            'email' => $user->email,
            'phone' => '09171234567',
            'province' => 'Laguna',
            'municipality' => 'Calamba',
            'barangay' => 'Real',
            'street' => 'Main Street',
            'house_no' => '1',
            'zip_code' => '4027',
            'business_name' => $businessName,
            'valid_id_path' => 'ids/carrier.jpg',
            'status' => $status === 'approved' ? 'approved' : $status,
            'reviewed_at' => in_array($status, ['approved', 'rejected'], true) ? now() : null,
            'rejection_reason' => $status === 'rejected' ? 'Incomplete or missing business permits.' : null,
            'user_id' => $user->id,
        ]);

        return compact('user', 'profile', 'registration');
    }
}
