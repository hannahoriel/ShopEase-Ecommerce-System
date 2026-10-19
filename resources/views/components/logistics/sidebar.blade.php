{{-- Logistics sidebar, matches the maroon rounded Admin navigation design. --}}
<aside id="lgSidebar" class="lg-sidebar" aria-label="Logistics navigation">
    <a class="lg-sidebar-logo" href="{{ url('/logistics/dashboard') }}">
        <img src="{{ asset('icons/admin/dashboard/sidebar&navbar/shopease.png') }}" alt="ShopEase" onerror="this.style.display='none'">
        <span>LOGISTICS<small>PARTNER PORTAL</small></span>
    </a>
    <nav class="lg-sidebar-menu">
        <p class="lg-nav-category">OVERVIEW</p>
        <a class="lg-nav-link active" href="{{ url('/logistics/dashboard') }}" title="Dashboard"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/dashboard.png') }}" alt=""><span>Dashboard</span></a>
        <p class="lg-nav-category">NETWORK MANAGEMENT</p>
        <button class="lg-nav-link" type="button" data-modal="hub" title="Branches and Hubs"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/logistic-management.png') }}" alt=""><span>Branches & Hubs</span></button>
        <a class="lg-nav-link" href="#lgScanForm" title="Parcel Scanner"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/registrations.png') }}" alt=""><span>Parcel Scanner</span></a>
        <a class="lg-nav-link" href="#lgActivityRows" title="Parcel Tracking"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/reports.png') }}" alt=""><span>Parcel Tracking</span></a>
        <p class="lg-nav-category">FINANCE & ACCOUNT</p>
        <a class="lg-nav-link" href="#lgMain" title="Earnings"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/commission.png') }}" alt=""><span>Logistics Earnings</span></a>
        <a class="lg-nav-link" href="{{ url('/logistics/landing') }}" title="Landing Page"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/account-management.png') }}" alt=""><span>Logistics Landing</span></a>
    </nav>
    <div class="lg-sidebar-bottom"><span>Network administration</span><small>ShopEase Logistics</small></div>
</aside>
