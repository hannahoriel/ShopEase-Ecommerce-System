<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your ShopEase registration has been approved</title>
</head>
<body style="margin: 0; padding: 32px 16px; background: #fff4ef; color: #201918; font-family: Arial, sans-serif;">
    <main style="max-width: 560px; margin: 0 auto; padding: 32px; border-radius: 12px; background: #ffffff;">
        <h1 style="margin-top: 0; color: #75171f;">Welcome to ShopEase, {{ $registration->first_name }}!</h1>
        <p>Your {{ ucfirst($registration->user_type) }} registration has been reviewed and approved. You can now log in and start using your account.</p>
        <p style="margin: 28px 0;">
            <a href="{{ url('/auth/login') }}" style="display: inline-block; padding: 12px 20px; border-radius: 6px; background: #75171f; color: #ffffff; text-decoration: none;">
                Log In to ShopEase
            </a>
        </p>
        <p>If you didn't request this account, please contact our support team.</p>
        <p>Thanks,<br>The ShopEase Team</p>
    </main>
</body>
</html>
