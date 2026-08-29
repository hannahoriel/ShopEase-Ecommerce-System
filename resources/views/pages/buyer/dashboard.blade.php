<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Buyer Dashboard - ShopEase</title></head>
<body>
    <h1>Buyer Dashboard</h1>
    <p>Welcome, {{ auth()->user()->name }}.</p>
    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log out</button></form>
</body>
</html>
