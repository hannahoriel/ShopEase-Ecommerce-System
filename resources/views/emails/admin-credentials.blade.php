<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your ShopEase Admin Account</title>
</head>
<body style="margin:0;padding:24px 12px;background:#fcf8f6;font-family:Arial,sans-serif;color:#241b19">
<div style="max-width:560px;margin:0 auto;padding:28px;background:#fff;border:1px solid #f0e9e6;border-radius:12px">
    <div style="margin-bottom:24px;color:#7b1b1b;font-size:20px;font-weight:700">ShopEase</div>
    <h1 style="margin:0 0 18px;font-size:22px;color:#7b1b1b">Administrator Account Created</h1>
    <p style="margin:0 0 14px">Hello {{ $adminName }},</p>
    <p style="margin:0 0 18px;line-height:1.6">Your ShopEase administrator account is ready. Use the credentials below to sign in:</p>
    <div style="margin:0 0 22px;padding:16px;background:#fcf8f6;border:1px solid #f0e9e6;border-radius:8px;line-height:1.8">
        <strong>Email:</strong> {{ $email }}<br>
        <strong>Temporary password:</strong> {{ $password }}
    </div>
    <p style="margin:0 0 22px">
        <a href="{{ route('admin.login') }}" style="display:inline-block;padding:12px 18px;background:#7b1b1b;color:#fff;text-decoration:none;border-radius:7px;font-weight:600">Open Admin Login</a>
    </p>
    <p style="margin:0;color:#777;font-size:13px;line-height:1.6">For your security, change your temporary password after signing in and keep it private.</p>
</div>
</body>
</html>
