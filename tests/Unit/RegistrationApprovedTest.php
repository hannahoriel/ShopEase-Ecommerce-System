<?php

namespace Tests\Unit;

use App\Mail\RegistrationApproved;
use App\Models\Admin\Registration;
use PHPUnit\Framework\TestCase;

class RegistrationApprovedTest extends TestCase
{
    public function test_approval_mail_supports_all_registration_roles(): void
    {
        foreach (['buyer', 'seller', 'logistics', 'rider'] as $role) {
            $registration = new Registration([
                'first_name' => 'Approved',
                'user_type' => $role,
            ]);

            $mail = new RegistrationApproved($registration);
            $built = $mail->build();

            $this->assertSame('Your ShopEase registration has been approved', $built->subject);
            $this->assertSame('email.registrations.approved', $built->view);
            $this->assertSame($role, $built->viewData['registration']->user_type);
        }
    }
}
