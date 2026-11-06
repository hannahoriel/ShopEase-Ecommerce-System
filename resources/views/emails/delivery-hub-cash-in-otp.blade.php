<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Cash-in verification</title></head>
<body style="margin:0;background:#fff7f5;font-family:Arial,sans-serif;color:#2b2020">
  <div style="max-width:560px;margin:32px auto;padding:30px;background:#fff;border:1px solid #f0dfdc;border-radius:14px">
    <p style="margin:0 0 8px;color:#8a2432;font-weight:bold;letter-spacing:.08em">SHOPEASE FINANCE</p>
    <h1 style="font-size:22px;margin:0 0 16px">Confirm delivery hub cash-in</h1>
    <p style="line-height:1.6">A cash-in of <strong>₱{{ $amount }}</strong> for remittance <strong>{{ $remittanceReference }}</strong> was requested at {{ $hubName }}.</p>
    <p style="margin:24px 0;padding:18px;text-align:center;background:#fff3f1;border-radius:10px;font-size:30px;letter-spacing:.28em;font-weight:bold;color:#7b1b26">{{ $code }}</p>
    <p style="font-size:13px;line-height:1.6;color:#756765">This code expires in 10 minutes and can be used once. If you did not request this cash-in, contact your delivery hub administrator immediately.</p>
  </div>
</body>
</html>
