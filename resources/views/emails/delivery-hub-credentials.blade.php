<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;background:#fcf8f6;font-family:Arial,sans-serif;color:#241b19">
<div style="max-width:560px;margin:32px auto;padding:28px;background:#fff;border:1px solid #f0e9e6;border-radius:12px">
    <h1 style="font-size:22px;color:#7b1b1b">Delivery Hub Account Created</h1>
    <p>Hello {{ $hubName }},</p>
    <p>Your ShopEase delivery hub account is ready. Use these credentials to log in:</p>
    <p><strong>Email:</strong> {{ $email }}<br><strong>Password:</strong> {{ $password }}</p>
    <p><a href="{{ url('/logistics/landing') }}" style="display:inline-block;padding:12px 18px;background:#7b1b1b;color:#fff;text-decoration:none;border-radius:7px">Open ShopEase Login</a></p>
    <p style="font-size:13px;color:#777">Keep this password private and store it securely.</p>
</div>
</body>
</html>
