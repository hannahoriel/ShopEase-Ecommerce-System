{{-- Logistics navbar — uses ShopEase Admin peach header palette. --}}
<header class="lg-navbar">
    <div class="lg-nav-left"><button type="button" id="lgSidebarToggle" class="lg-icon-button" aria-label="Toggle sidebar" aria-expanded="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button><h2>Dashboard</h2></div>
    <div class="lg-nav-right"><span class="lg-navbar-caption">Logistics Management</span>
        <button class="lg-icon-button lg-notify-button" type="button" id="lgNotifyButton" title="Notifications" aria-label="Show notification message"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/notification.png') }}" alt="" onerror="this.style.display='none'"></button>
        <div class="lg-profile-wrap"><button class="lg-profile-btn" id="lgProfileButton" type="button" aria-expanded="false"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/admin-profile.png') }}" alt="" onerror="this.style.display='none'"><span><strong>{{ data_get(auth()->user(),'name') ?: 'Logistics Partner' }}</strong><small>Logistics Account · Preview</small></span><span class="lg-profile-chevron">⌄</span></button>
            <div class="lg-profile-menu" id="lgProfileMenu" hidden><span>ShopEase Logistics</span><a href="{{ url('/logistics/landing') }}">Visit Landing Page</a>@if(\Illuminate\Support\Facades\Route::has('logout'))<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log Out</button></form>@endif</div></div>
    </div>
</header>
