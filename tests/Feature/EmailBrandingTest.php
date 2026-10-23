<?php

namespace Tests\Feature;

use App\Mail\AccountStatusChanged;
use App\Mail\BuyerVerificationCode;
use App\Mail\RegistrationApproved;
use App\Mail\RegistrationRejected;
use App\Mail\SellerVerificationCode;
use App\Models\Admin\Registration;
use App\Models\User;
use Tests\TestCase;

class EmailBrandingTest extends TestCase
{
    public function test_markdown_emails_use_shopease_name_without_a_logo(): void
    {
        $mail = new AccountStatusChanged(
            user: User::factory()->make(['name' => 'Test Buyer']),
            status: 'suspended',
            reason: 'Policy violation',
            details: null,
            duration: 7,
        );

        $html = $mail->render();

        $this->assertStringContainsString('ShopEase', $html);
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('laravel.com/img/notification-logo', $html);
        $this->assertSame('ShopEase', config('mail.from.name'));
    }

    public function test_every_email_template_uses_shopease_name_without_a_logo(): void
    {
        $registration = new Registration([
            'first_name' => 'Test',
            'user_type' => 'buyer',
            'rejection_reason' => 'Incomplete documents',
        ]);
        $mailables = [
            new BuyerVerificationCode('123456'),
            new SellerVerificationCode('123456'),
            new RegistrationApproved($registration),
            new RegistrationRejected($registration),
        ];

        foreach ($mailables as $mailable) {
            $html = $mailable->render();

            $this->assertStringContainsString('ShopEase', $html);
            $this->assertStringNotContainsString('<img', $html);
            $this->assertStringNotContainsString('laravel.com/img/notification-logo', $html);
        }
    }
}
