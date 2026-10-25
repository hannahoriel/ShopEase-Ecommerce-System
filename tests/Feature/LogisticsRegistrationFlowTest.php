<?php

namespace Tests\Feature;

use App\Mail\LogisticsVerificationCode;
use App\Models\Admin\Registration;
use App\Models\Logistics\Logistics;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LogisticsRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_logistics_registration_creates_an_account_pending_admin_approval(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Mail::fake();

        $this->submitLogisticsRegistration()
            ->assertRedirectToRoute('logistics.landing.info')
            ->assertSessionHas('success', 'Your logistics registration has been submitted. It is now waiting for administrator approval.');
        Mail::assertSent(LogisticsVerificationCode::class, fn (LogisticsVerificationCode $mail): bool =>
            preg_match('/^\d{6}$/', $mail->code) === 1
            && $mail->hasTo('company@example.com')
        );

        $user = User::where('email', 'company@example.com')->firstOrFail();

        $this->assertSame(User::ROLE_LOGISTICS, $user->role);
        $this->assertSame('pending', $user->registration_status);
        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'user_type' => User::ROLE_LOGISTICS,
            'business_name' => 'Fast Freight',
            'status' => 'pending',
        ]);

        $logistics = Logistics::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('pending', $logistics->registration_status);
        $this->assertSame('Fast Freight', $logistics->business_name);
        $this->assertSame('company@example.com', $user->email);
        $this->assertNotEmpty($logistics->registration_details['documents']['business_registration']['path']);
        Storage::disk('public')->assertExists($logistics->registration_details['documents']['business_registration']['path']);

        $this->get(route('logistics.landing.info'))
            ->assertOk()
            ->assertSee('waiting for administrator approval');

        $this->post(route('logistics.login.attempt'), [
            'email' => $user->email,
            'password' => 'Password123!',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin)
            ->getJson('/api/v1/admin/logistics-management')
            ->assertOk()
            ->assertJsonCount(1, 'pendingRequests')
            ->assertJsonPath('pendingRequests.0.company', 'Fast Freight')
            ->assertJsonPath('pendingRequests.0.email', 'company@example.com')
            ->assertJsonPath('pendingRequests.0.phone', '09171234567')
            ->assertJsonPath('pendingRequests.0.dti_permit', Storage::disk('public')->url($logistics->registration_details['documents']['business_registration']['path']))
            ->assertJsonPath('pendingRequests.0.business_permit', Storage::disk('public')->url($logistics->registration_details['documents']['mayors_permit']['path']))
            ->assertJsonPath('stats.pending_requests', 1);

        $this->actingAs($admin)
            ->get('/admin/logistics-management')
            ->assertOk()
            ->assertSee('Fast Freight');

        $registration = Registration::where('user_id', $user->id)->firstOrFail();
        $this->actingAs($admin)
            ->postJson("/api/v1/admin/logistics-management/registrations/{$registration->id}/approve")
            ->assertOk();
        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'registration_status' => 'active',
        ]);
        $this->assertDatabaseHas('logistics', [
            'user_id' => $user->id,
            'registration_status' => 'active',
        ]);
    }

    public function test_approved_logistics_user_logs_in_to_the_logistics_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_LOGISTICS,
            'email' => 'approved-company@example.com',
            'password' => 'Password123!',
            'registration_status' => 'active',
        ]);

        $this->post(route('logistics.login.attempt'), [
            'email' => $user->email,
            'password' => 'Password123!',
        ])->assertRedirectToRoute('logistics.dashboard');

        $this->assertAuthenticatedAs($user);
        $this->get(route('logistics.dashboard'))->assertOk();
    }

    public function test_logistics_login_rejects_credentials_for_other_roles(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_BUYER,
            'email' => 'buyer@example.com',
            'password' => 'Password123!',
            'registration_status' => 'active',
        ]);

        $this->post(route('logistics.login.attempt'), [
            'email' => $user->email,
            'password' => 'Password123!',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logistics_verification_code_can_be_resent_and_verified(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Mail::fake();
        $this->saveRegistrationSteps();

        $this->postJson(route('logistics.register.send-code'))
            ->assertOk()
            ->assertJsonPath('retry_after', 60);
        Mail::assertSent(LogisticsVerificationCode::class, 1);

        $this->postJson(route('logistics.register.send-code'))
            ->assertTooManyRequests()
            ->assertJsonStructure(['message', 'retry_after']);

        $this->travel(61)->seconds();
        $this->postJson(route('logistics.register.send-code'))
            ->assertOk()
            ->assertJsonPath('retry_after', 60);
        Mail::assertSent(LogisticsVerificationCode::class, 2);

        $codes = [];
        Mail::assertSent(LogisticsVerificationCode::class, function (LogisticsVerificationCode $mail) use (&$codes): bool {
            $codes[] = $mail->code;

            return true;
        });
        $this->postJson(route('logistics.register.verify-code'), ['code' => $codes[0]])
            ->assertUnprocessable();
        $this->postJson(route('logistics.register.verify-code'), ['code' => $codes[1]])
            ->assertOk()
            ->assertJsonPath('message', 'Email verified successfully.');
    }

    public function test_logistics_registration_cannot_be_submitted_before_email_verification(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Mail::fake();
        $this->saveRegistrationSteps();

        $this->post(route('logistics.register.complete'))
            ->assertRedirectToRoute('logistics.register.review')
            ->assertSessionHasErrors('verification');

        $this->assertDatabaseMissing('users', ['email' => 'company@example.com']);
    }

    public function test_incomplete_saved_company_details_redirect_back_instead_of_throwing_undefined_key_error(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Mail::fake();
        $this->saveRegistrationSteps();

        $company = session('logistics_registration.company');
        unset($company['representative_sex']);
        session()->put('logistics_registration.company', $company);

        $this->post(route('logistics.register.complete'))
            ->assertRedirectToRoute('logistics.register.company')
            ->assertSessionHasErrors('representative_sex');

        $this->get(route('logistics.register.company'))
            ->assertOk()
            ->assertSee('Fast Freight')
            ->assertSee('Representative Sex');
        $this->assertDatabaseMissing('users', ['email' => 'company@example.com']);
    }

    public function test_review_page_keeps_email_verification_button_clickable_and_renders_code_inputs(): void
    {
        $response = $this->get(route('logistics.register.review'));

        $response->assertOk()
            ->assertSee('id="startEmailVerification" type="button"', false)
            ->assertDontSee('id="startEmailVerification" type="button" class="continue-button" disabled', false)
            ->assertSee('id="emailVerificationModal"', false)
            ->assertSee('class="email-code-input"', false)
            ->assertSee('id="resendCode"', false);
    }

    private function submitLogisticsRegistration()
    {
        $this->saveRegistrationSteps();

        $this->postJson(route('logistics.register.send-code'))->assertOk();
        $code = null;
        Mail::assertSent(LogisticsVerificationCode::class, function (LogisticsVerificationCode $mail) use (&$code): bool {
            $code = $mail->code;

            return true;
        });
        $this->postJson(route('logistics.register.verify-code'), ['code' => $code])->assertOk();

        return $this->post(route('logistics.register.complete'));
    }

    private function saveRegistrationSteps(): void
    {
        $this->post(route('logistics.register.company.submit'), [
            'company_name' => 'Fast Freight',
            'registered_business_name' => 'Fast Freight Incorporated',
            'company_type' => 'Corporation',
            'company_email' => 'company@example.com',
            'company_phone' => '09171234567',
            'representative_name' => 'Andrea Cruz',
            'representative_position' => 'Operations Manager',
            'representative_email' => 'andrea@example.com',
            'representative_phone' => '09179876543',
            'representative_sex' => 'female',
            'representative_birthday' => '1990-05-20',
            'region' => 'NCR',
            'province' => 'Metro Manila',
            'city' => 'Quezon City',
            'barangay' => 'Diliman',
            'postal_code' => '1101',
            'street' => 'Main Street',
            'house_no' => '12',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'accept_terms' => '1',
        ])->assertRedirectToRoute('logistics.register.permits');

        $this->post(route('logistics.register.permits.submit'), [
            'business_registration' => UploadedFile::fake()->create('business.pdf', 1, 'application/pdf'),
            'mayors_permit' => UploadedFile::fake()->create('permit.pdf', 1, 'application/pdf'),
            'bir_certificate' => UploadedFile::fake()->create('bir.pdf', 1, 'application/pdf'),
            'representative_authorization' => UploadedFile::fake()->create('representative-id.pdf', 1, 'application/pdf'),
        ])->assertRedirectToRoute('logistics.register.review');

        $this->get(route('logistics.register.review'))
            ->assertOk()
            ->assertSee('Verify Email & Submit')
            ->assertSee(route('logistics.register.complete'), false);
    }
}
