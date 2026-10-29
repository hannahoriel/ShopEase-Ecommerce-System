<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $hub->hub_name }} · ShopEase Delivery Hub</title>
    <style>
        body{margin:0;background:#fcf8f6;color:#241b19;font:15px Arial,sans-serif}.card{max-width:680px;margin:8vh auto;padding:32px;background:#fff;border:1px solid #f0e9e6;border-radius:16px;box-shadow:0 12px 36px #52070b12}h1{color:#7b1b1b;margin-top:0}.muted{color:#817773;line-height:1.6}.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:26px 0}.item{padding:14px;background:#fcf8f6;border-radius:10px}.item small{display:block;color:#817773;margin-bottom:7px}.item strong{overflow-wrap:anywhere}.logout button{border:0;border-radius:8px;background:#7b1b1b;color:white;padding:11px 17px;font-weight:600;cursor:pointer}@media(max-width:600px){.card{margin:18px;padding:22px}.grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<main class="card">
    <p class="muted">ShopEase · Delivery Hub</p>
    <h1>{{ $hub->hub_name }}</h1>
    <p class="muted">You are signed in to your delivery hub account.</p>
    <section class="grid" aria-label="Delivery hub details">
        <div class="item"><small>Login email</small><strong>{{ auth()->user()->email }}</strong></div>
        <div class="item"><small>Sorting center</small><strong>{{ $hub->logisticsBranch->name }}</strong></div>
        <div class="item"><small>Municipality</small><strong>{{ $hub->municipality }}</strong></div>
        <div class="item"><small>Province</small><strong>{{ $hub->province }}</strong></div>
    </section>
    <form class="logout" method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log Out</button></form>
</main>
</body>
</html>
