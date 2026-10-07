<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistics Dashboard - ShopEase</title>
    @vite(['resources/css/components/dashboard-announcement-carousel.css', 'resources/js/components/dashboard-announcement-carousel.js'])
</head>
<body>
    <main class="logistics-dashboard">
        <h1>Logistics Dashboard</h1>
        <p>Welcome, {{ auth()->user()->name }}.</p>

        <section class="logistics-announcement-card" aria-label="Platform announcements">
            @include('components.dashboard-announcement-carousel', [
                'dashboardAnnouncementVariant' => 'logistics',
            ])
        </section>

        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log out</button></form>
    </main>
</body>
</html>
