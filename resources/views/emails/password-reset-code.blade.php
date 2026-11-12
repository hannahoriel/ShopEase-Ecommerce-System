<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase password reset</title>
</head>
<body style="margin: 0; padding: 32px; font-family: Arial, sans-serif; color: #2b1717; background: #f8f3f0;">
    @include('emails.partials.brand-header')
    <div style="max-width: 560px; margin: 0 auto; padding: 32px; background: #ffffff; border-radius: 12px;">
        <h1 style="margin-top: 0; color: #7b1e2b;">Reset your ShopEase password</h1>
        <p>Enter this verification code to reset your password.</p>
        <p style="margin: 28px 0; font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #7b1e2b;">{{ $code }}</p>
        <p>This code expires in 10 minutes. If you did not request a password reset, you can ignore this email.</p>
        <p>Thanks,<br>The ShopEase Team</p>
    </div>
</body>
</html>
