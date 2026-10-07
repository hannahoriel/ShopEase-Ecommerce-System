<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>An update to your ShopEase registration</title>
</head>
<body style="margin: 0; padding: 32px 16px; background: #fff4ef; color: #201918; font-family: Arial, sans-serif;">
    <main style="max-width: 560px; margin: 0 auto; padding: 32px; border-radius: 12px; background: #ffffff;">
        <h1 style="margin-top: 0; color: #75171f;">Update on your ShopEase registration</h1>
        <p>Hi {{ $registration->first_name }}, thanks for your interest in ShopEase. After reviewing your {{ ucfirst($registration->user_type) }} registration, we're unable to approve it at this time.</p>
        <p><strong>Reason:</strong> {{ $registration->rejection_reason }}</p>
        @if($registration->rejection_details)
            <p><strong>Additional details:</strong> {{ $registration->rejection_details }}</p>
        @endif
        <p>You're welcome to correct the issue above and submit a new registration.</p>
        <p style="margin: 28px 0;">
            <a href="{{ url('/auth/signup') }}" style="display: inline-block; padding: 12px 20px; border-radius: 6px; background: #75171f; color: #ffffff; text-decoration: none;">
                Register Again
            </a>
        </p>
        <p>Thanks,<br>The ShopEase Team</p>
    </main>
</body>
</html>
