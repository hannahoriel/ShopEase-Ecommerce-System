{{-- Logistics command dashboard — frontend prototype. Persistent demo data is stored in this browser only. --}}
@php
    $logisticsName = data_get(auth()->user(), 'name') ?: 'Logistics Partner';
    $demoStats = $logisticsStats ?? ['sorting_centers'=>0,'delivery_hubs'=>0,'parcels_in_network'=>0,'earnings'=>0];
    $dashboardAnnouncements = $dashboardAnnouncements ?? collect();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Logistics Dashboard — ShopEase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Self-contained dashboard UI: CSS and JavaScript are embedded below. --}}
    <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
:root{--lg-maroon:#7B1B1B;--lg-dark:#52070B;--lg-peach:#FFE6DE;--lg-bg:#FCF8F6;--lg-line:#F0E9E6;--lg-ink:#17120F;--lg-muted:#8C8784}
*{box-sizing:border-box}body.lg-body{margin:0;background:var(--lg-bg);font-family:Poppins,sans-serif;color:var(--lg-ink)}button,input,select{font-family:inherit}button{cursor:pointer}a{text-decoration:none}body.lg-modal-open{overflow:hidden}
.lg-sidebar{position:fixed;left:0;top:0;bottom:0;width:260px;z-index:50;background:linear-gradient(155deg,#7B1B1B 0%,#52070B 80%);border-top-right-radius:58px;padding:20px 14px 18px;display:flex;flex-direction:column;transition:width .3s ease;box-shadow:4px 0 18px #52070b12;overflow:hidden;color:white}
.lg-sidebar-logo{height:74px;display:flex;align-items:center;justify-content:center;gap:7px;margin-bottom:20px;color:white;white-space:nowrap}.lg-sidebar-logo img{max-width:115px;max-height:53px;object-fit:contain}.lg-sidebar-logo>span{font-size:11px;letter-spacing:1.1px;font-weight:700}.lg-sidebar-logo small{display:block;font-size:8px;font-weight:400;letter-spacing:1.5px;opacity:.65}
.lg-sidebar-menu{overflow:auto;scrollbar-width:thin;flex:1}.lg-nav-category{margin:15px 13px 9px;font-size:10px;letter-spacing:1.4px;color:#f5c8c7;font-weight:600}.lg-nav-link{height:43px;width:100%;display:flex;align-items:center;gap:12px;padding:0 13px;border:0;background:none;color:#fff;border-radius:999px;font-size:12px;font-weight:500;text-align:left;margin:3px 0;white-space:nowrap;transition:background .2s}.lg-nav-link:hover{background:#ffffff1b}.lg-nav-link.active{background:#ffffff30}.lg-nav-link img{width:20px;height:20px;object-fit:contain;flex:0 0 20px}.lg-sidebar-bottom{border-top:1px solid #ffffff25;padding:14px 11px 3px;white-space:nowrap;font-size:11px}.lg-sidebar-bottom small{display:block;color:#ffc6bc;font-size:10px;margin-top:3px}
.lg-navbar{position:fixed;top:0;left:0;right:0;height:88px;background:var(--lg-peach);z-index:35;display:flex;align-items:center;justify-content:space-between;padding:0 26px 0 288px;transition:padding-left .3s ease;gap:15px}.lg-nav-left,.lg-nav-right{display:flex;align-items:center;gap:22px}.lg-nav-left h2{font-size:21px;font-weight:700;margin:0;white-space:nowrap}.lg-icon-button{border:0;background:transparent;display:grid;place-items:center;width:30px;height:34px;color:#52070B;flex-shrink:0}.lg-icon-button svg{width:24px;height:24px}.lg-icon-button img{width:24px;height:26px;object-fit:contain}.lg-navbar-caption{font-size:12px;color:#815d57}.lg-profile-wrap{position:relative}.lg-profile-btn{border:1px solid #ebe0dc;background:white;border-radius:10px;padding:6px 10px;display:flex;align-items:center;gap:9px;box-shadow:0 2px 8px #0000000b;color:#29211f;text-align:left;min-width:190px}.lg-profile-btn img{width:34px;height:34px;object-fit:contain}.lg-profile-btn strong{display:block;font-size:12px;font-weight:600;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.lg-profile-btn small{font-size:10px;color:#a19b98}.lg-profile-chevron{margin-left:auto}.lg-profile-menu{position:absolute;right:0;top:53px;width:214px;border:1px solid var(--lg-line);box-shadow:0 12px 30px #0002;border-radius:13px;background:white;padding:8px;z-index:100}.lg-profile-menu[hidden]{display:none}.lg-profile-menu>span{display:block;font-size:10px;color:var(--lg-muted);padding:9px}.lg-profile-menu a,.lg-profile-menu button{display:block;width:100%;text-align:left;border:0;background:transparent;padding:10px;border-radius:7px;color:var(--lg-ink);font-size:12px}.lg-profile-menu a:hover,.lg-profile-menu button:hover{background:#FFF2EE}
.lg-main{margin-left:260px;padding:114px 25px 36px;min-height:100vh;transition:margin-left .3s ease;max-width:none}.lg-body.lg-collapsed .lg-sidebar{width:80px}.lg-body.lg-collapsed .lg-sidebar-logo>span,.lg-body.lg-collapsed .lg-nav-category,.lg-body.lg-collapsed .lg-nav-link span,.lg-body.lg-collapsed .lg-sidebar-bottom{display:none}.lg-body.lg-collapsed .lg-sidebar-logo img{max-width:60px}.lg-body.lg-collapsed .lg-nav-link{justify-content:center;padding:0}.lg-body.lg-collapsed .lg-main{margin-left:80px}.lg-body.lg-collapsed .lg-navbar{padding-left:108px}
.lg-welcome{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:16px}.lg-overline{font-size:10px;letter-spacing:1.3px;color:#9b1239;font-weight:700;margin:0 0 5px}.lg-welcome h1{margin:0;font-weight:650;font-size:24px}.lg-welcome p:not(.lg-overline){font-size:12px;color:var(--lg-muted);line-height:1.7;margin:4px 0 0}.lg-welcome-actions{display:flex;align-items:center;gap:11px;flex-shrink:0}.lg-date{padding:11px 13px;background:white;border:1px solid var(--lg-line);border-radius:9px;color:#665b56;font-size:11px;white-space:nowrap}.lg-btn{border-radius:8px;padding:10px 15px;min-height:37px;font-weight:600;font-size:11px;display:inline-flex;justify-content:center;align-items:center;gap:7px;transition:background .2s,box-shadow .2s;white-space:nowrap}.lg-btn-primary{color:white;border:1px solid #7B1B1B;background:#7B1B1B}.lg-btn-primary:hover{background:#621515}.lg-btn-outline{color:#7B1B1B;background:white;border:1px solid #d9bfc0}.lg-btn-outline:hover{background:#fff6f5}
.lg-notice{display:flex;gap:10px;align-items:start;margin-bottom:19px;font-size:11px;line-height:1.7;color:#80572e;padding:11px 14px;border:1px solid #f3e4d5;background:#fff9f2;border-radius:10px}.lg-notice strong{font-weight:600}.lg-notice-dot{width:8px;height:8px;background:#cd9250;flex:0 0 8px;border-radius:50%;margin-top:5px}
.lg-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:15px;margin-bottom:20px}.lg-stat{display:flex;align-items:center;gap:12px;padding:17px 16px;min-height:91px;background:white;border:1px solid var(--lg-line);border-radius:16px;box-shadow:0 2px 12px rgba(42,20,15,.05)}.lg-stat-icon{width:40px;height:40px;object-fit:contain;flex-shrink:0}.lg-stat strong{display:block;font-size:23px;line-height:1.2;font-weight:600;white-space:nowrap}.lg-stat span{display:block;font-size:11px;color:var(--lg-muted);margin-top:5px}
.lg-main-grid{display:grid;grid-template-columns:minmax(0,1.66fr) minmax(320px,1fr);gap:18px;align-items:start}.lg-side-stack{display:grid;gap:18px}.lg-panel{background:white;border:1px solid var(--lg-line);border-radius:16px;box-shadow:0 1px 4px #00000008;min-width:0;overflow:hidden}.lg-panel-header{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:19px 19px 15px}.lg-panel-header h2{font-size:15px;font-weight:600;margin:0}.lg-panel-header p{font-size:11px;line-height:1.6;color:var(--lg-muted);margin:4px 0 0}.lg-panel-icon{width:30px;height:30px;object-fit:contain}.lg-toolbar{display:flex;gap:9px;padding:0 19px 16px;flex-wrap:wrap}.lg-toolbar select,.lg-search-box{height:37px;background:white;border:1px solid #D9D6D4;border-radius:8px;font-size:11px;color:#68615e}.lg-toolbar select{padding:0 8px;max-width:170px}.lg-search-box{display:flex;gap:6px;align-items:center;padding:0 10px;flex:1;min-width:190px}.lg-search-box span{font-size:19px}.lg-search-box input{width:100%;outline:none;border:0;background:none;font-size:11px}.lg-table-scroll{overflow:auto}.lg-table{width:100%;border-collapse:collapse;min-width:625px}.lg-table th{font-weight:500;font-size:11px;color:var(--lg-muted);padding:13px 15px;white-space:nowrap;text-align:left;border-bottom:1px solid #E5E7EB}.lg-table td{font-size:11px;padding:14px 15px;border-bottom:1px solid #f0edef;color:var(--lg-ink);vertical-align:middle}.lg-table tr:hover td{background:#FFFAF8}.lg-table tr:last-child td{border-bottom:0}.lg-table td strong{display:block;font-size:11px;font-weight:600}.lg-table td small{display:block;color:#9c9692;font-size:10px;margin-top:4px}.lg-tag{display:inline-block;border-radius:20px;padding:5px 9px;font-size:10px;white-space:nowrap}.lg-tag.sorting{color:#9b1239;background:#fce9ee}.lg-tag.delivery{color:#765422;background:#fff0d9}.lg-tag.active{color:#2d7745;background:#e9f7ec}.lg-table-empty{text-align:center;padding:38px 14px!important;color:#8c8784!important}.lg-table-foot{display:flex;justify-content:space-between;color:var(--lg-muted);font-size:10px;padding:13px 18px;border-top:1px solid #ece9e7;gap:10px}
.lg-scan-form{display:grid;gap:8px;padding:0 19px 20px}.lg-scan-form label,.lg-field label{font-size:11px;font-weight:500;color:#2b2624}.lg-scan-form label span,.lg-field label span{color:#d32443}.lg-scan-form input,.lg-scan-form select,.lg-field input,.lg-field select{height:39px;border:1px solid #D9D6D4;border-radius:8px;background:#fff;padding:0 11px;font-size:11px;color:#302b29;min-width:0;outline:none}.lg-scan-form input:focus,.lg-scan-form select:focus,.lg-field input:focus,.lg-field select:focus{border-color:#9b1239;box-shadow:0 0 0 2px #9b123916}.lg-scan-form .lg-full{margin-top:8px;width:100%}.lg-help{font-size:10px;color:#9b9290;line-height:1.65;margin:6px 0 0}.lg-flow{padding-bottom:15px}.lg-flow-step{display:flex;align-items:flex-start;gap:13px;margin:0 19px;padding:13px 0;border-top:1px solid #f2ebeb}.lg-flow-step>span{display:grid;place-items:center;color:#9b1239;background:#fce9ee;border-radius:8px;width:30px;height:30px;flex:0 0 30px;font-size:11px;font-weight:700}.lg-flow-step b{font-size:11px;font-weight:600;display:block;margin-top:1px}.lg-flow-step small{font-size:10px;color:var(--lg-muted);display:block;line-height:1.6;margin-top:3px}
.lg-lower-grid{display:grid;grid-template-columns:minmax(0,1.66fr) minmax(320px,1fr);gap:18px;align-items:stretch;margin-top:18px}.lg-activity-table{min-width:580px}.lg-soft-pill{border-radius:20px;background:#fce9ee;color:#9b1239;padding:6px 10px;font-size:10px;white-space:nowrap}.lg-earnings-total{margin:0 19px 15px;background:#fff9f7;border:1px solid #f3e5e0;border-radius:12px;padding:15px}.lg-earnings-total small,.lg-earnings-total span{display:block;color:var(--lg-muted);font-size:10px}.lg-earnings-total strong{display:block;font-size:24px;margin:4px 0;color:#7B1B1B}.lg-earnings-row{display:flex;justify-content:space-between;gap:9px;border-bottom:1px solid #f3eded;padding:11px 0;margin:0 19px;font-size:11px}.lg-earnings-row span{color:var(--lg-muted)}.lg-earnings-row b{font-weight:600}.lg-lower-grid .lg-help{margin:12px 19px 17px}.lg-announcements{margin-top:18px;padding-bottom:15px}.lg-announcements>*:not(.lg-panel-header){margin:0 18px}
.lg-modal{position:fixed;inset:0;background:#1a0b0b88;display:grid;place-items:center;z-index:200;padding:15px}.lg-modal[hidden]{display:none}.lg-modal-card{background:white;border-radius:17px;max-width:660px;width:100%;max-height:95vh;overflow:auto;box-shadow:0 24px 70px #0003;padding:24px}.lg-modal-header{display:flex;justify-content:space-between;gap:20px;margin-bottom:23px}.lg-modal-header h2{font-size:19px;margin:0}.lg-modal-header p{color:var(--lg-muted);font-size:11px;margin:5px 0}.lg-close{background:#f8f1ef;border:0;border-radius:50%;width:32px;height:32px;font-size:23px;color:#625452}.lg-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.lg-field{display:grid;gap:7px}.lg-field small{font-size:10px;color:#9b9390;line-height:1.5}.lg-field.lg-wide{grid-column:1/-1}.lg-form-error{font-size:11px;color:#b42323;min-height:15px}.lg-modal-actions{display:flex;justify-content:flex-end;gap:10px}.lg-toast{position:fixed;right:22px;bottom:20px;background:white;border:1px solid #d6babc;border-left:4px solid #7B1B1B;border-radius:10px;padding:14px 19px;font-size:12px;max-width:370px;z-index:250;box-shadow:0 12px 35px #0002}.lg-toast[hidden]{display:none}
@media(max-width:1260px){.lg-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.lg-main-grid,.lg-lower-grid{grid-template-columns:1fr}.lg-side-stack{grid-template-columns:1fr 1fr}}
@media(max-width:900px){.lg-sidebar{width:80px}.lg-sidebar-logo>span,.lg-nav-category,.lg-nav-link span,.lg-sidebar-bottom{display:none}.lg-sidebar-logo img{max-width:60px}.lg-nav-link{justify-content:center;padding:0}.lg-navbar{padding-left:108px}.lg-main{margin-left:80px}.lg-body.lg-collapsed .lg-sidebar{width:260px;box-shadow:0 0 0 100vw #0003}.lg-body.lg-collapsed .lg-sidebar-logo>span,.lg-body.lg-collapsed .lg-nav-category,.lg-body.lg-collapsed .lg-nav-link span,.lg-body.lg-collapsed .lg-sidebar-bottom{display:block}.lg-body.lg-collapsed .lg-nav-link{justify-content:flex-start;padding:0 13px}.lg-body.lg-collapsed .lg-main{margin-left:80px}.lg-body.lg-collapsed .lg-navbar{padding-left:108px}}
@media(max-width:700px){.lg-sidebar{transform:translateX(-100%);width:260px}.lg-body.lg-collapsed .lg-sidebar{transform:translateX(0)}.lg-navbar,.lg-body.lg-collapsed .lg-navbar{padding:0 14px;height:75px}.lg-nav-left h2{font-size:16px}.lg-nav-right{gap:8px}.lg-navbar-caption,.lg-profile-btn small,.lg-profile-chevron{display:none}.lg-profile-btn{min-width:unset}.lg-profile-btn strong{font-size:10px;max-width:92px}.lg-main,.lg-body.lg-collapsed .lg-main{margin-left:0;padding:96px 13px 25px}.lg-welcome{align-items:flex-start;flex-direction:column}.lg-welcome-actions{width:100%;flex-wrap:wrap}.lg-side-stack{grid-template-columns:1fr}.lg-panel-header{align-items:flex-start;flex-wrap:wrap}.lg-form-grid{grid-template-columns:1fr}.lg-field.lg-wide{grid-column:auto}}
@media(max-width:440px){.lg-stats{grid-template-columns:1fr 1fr;gap:8px}.lg-stat{padding:12px 9px;gap:7px;min-height:85px}.lg-stat-icon{width:27px;height:27px}.lg-stat strong{font-size:17px}.lg-stat span{font-size:9px}.lg-toolbar>*{max-width:none!important;width:100%}.lg-date{display:none}}
@media(prefers-reduced-motion:reduce){.lg-sidebar,.lg-main,.lg-navbar,.lg-btn{transition:none}}

    </style>
</head>
<body class="lg-body">
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
{{-- Logistics navbar — uses ShopEase Admin peach header palette. --}}
<header class="lg-navbar">
    <div class="lg-nav-left"><button type="button" id="lgSidebarToggle" class="lg-icon-button" aria-label="Toggle sidebar" aria-expanded="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button><h2>Dashboard</h2></div>
    <div class="lg-nav-right"><span class="lg-navbar-caption">Logistics Management</span>
        <button class="lg-icon-button lg-notify-button" type="button" id="lgNotifyButton" title="Notifications" aria-label="Show notification message"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/notification.png') }}" alt="" onerror="this.style.display='none'"></button>
        <div class="lg-profile-wrap"><button class="lg-profile-btn" id="lgProfileButton" type="button" aria-expanded="false"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/admin-profile.png') }}" alt="" onerror="this.style.display='none'"><span><strong>{{ data_get(auth()->user(),'name') ?: 'Logistics Partner' }}</strong><small>Logistics Account · Preview</small></span><span class="lg-profile-chevron">⌄</span></button>
            <div class="lg-profile-menu" id="lgProfileMenu" hidden><span>ShopEase Logistics</span><a href="{{ url('/logistics/landing') }}">Visit Landing Page</a>@if(\Illuminate\Support\Facades\Route::has('logout'))<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log Out</button></form>@endif</div></div>
    </div>
</header>
    <main class="lg-main" id="lgMain">
        <div class="lg-welcome">
            <div><p class="lg-overline">SHOPEASE · LOGISTICS OPERATIONS</p>
                <h1>Logistics Overview</h1>
                <p>Oversee sorting centers, municipal hubs, parcel transfers, and network earnings across the Philippines.</p>
            </div>
            <div class="lg-welcome-actions"><span class="lg-date">{{ now()->format('M d, Y') }}</span><button type="button" class="lg-btn lg-btn-primary" data-modal="hub">+ Create Branch / Hub</button></div>
        </div>
        <div class="lg-notice"><span class="lg-notice-dot"></span><div><strong>Dashboard preview mode.</strong> Hub creation, parcel check-ins and earnings examples are browser-only demo interactions until connected to your backend. No actual shipment status is changed.</div></div>
        <section class="lg-stats" aria-label="Logistics statistics">
            @foreach([
                ['id'=>'sorting','number'=>$demoStats['sorting_centers'],'label'=>'Provincial Sorting Centers','icon'=>'icons/admin/commission/rate.png'],
                ['id'=>'hubs','number'=>$demoStats['delivery_hubs'],'label'=>'Municipal Delivery Hubs','icon'=>'icons/admin/dashboard/sidebar&navbar/logistic-management.png'],
                ['id'=>'parcels','number'=>$demoStats['parcels_in_network'],'label'=>'Parcels in Network','icon'=>'icons/admin/commission/total-orders.png'],
                ['id'=>'earnings','number'=>'₱ '.number_format($demoStats['earnings'],2),'label'=>'Network Earnings · Demo','icon'=>'icons/admin/commission/total-commission.png'],
            ] as $stat)
            <div class="lg-stat"><img class="lg-stat-icon" src="{{ asset($stat['icon']) }}" alt="" onerror="this.style.display='none'"><div><strong id="lgStat-{{ $stat['id'] }}">{{ $stat['number'] }}</strong><span>{{ $stat['label'] }}</span></div></div>
            @endforeach
        </section>
        <div class="lg-main-grid">
            <section class="lg-panel lg-network-panel">
                <div class="lg-panel-header"><div><h2>Branch & Hub Network</h2><p>One main sorting center per province, connected to delivery hubs in its municipalities.</p></div><button class="lg-btn lg-btn-outline" type="button" data-modal="hub">Manage Network <span aria-hidden="true">↗</span></button></div>
                <div class="lg-toolbar"><label class="lg-search-box"><span aria-hidden="true">⌕</span><input id="lgHubSearch" placeholder="Search province, town or hub..." autocomplete="off"></label>
                    <select id="lgHubType" aria-label="Filter hub type"><option value="all">All facility types</option><option value="sorting">Sorting centers</option><option value="delivery">Delivery hubs</option></select>
                    <select id="lgHubProvince" aria-label="Filter province"><option value="all">All provinces</option></select>
                </div>
                <div class="lg-table-scroll"><table class="lg-table"><thead><tr><th>Facility / Branch</th><th>Location</th><th>Type</th><th>Connected to</th><th>Status</th></tr></thead><tbody id="lgHubRows"></tbody></table></div>
                <div class="lg-table-foot"><span id="lgHubCount">0 facilities</span><span>Philippine provincial routing</span></div>
            </section>
            <aside class="lg-side-stack">
                <section class="lg-panel"><div class="lg-panel-header"><div><h2>Parcel Check-in</h2><p>Scan or enter a tracking code when a parcel arrives at a facility.</p></div><img class="lg-panel-icon" src="{{ asset('icons/admin/dashboard/sidebar&navbar/logistic-management.png') }}" alt="" onerror="this.style.display='none'"></div>
                    <form id="lgScanForm" class="lg-scan-form"><label for="lgTrackingCode">Tracking number <span>*</span></label><input id="lgTrackingCode" name="tracking_code" placeholder="e.g. SHP-100245" required maxlength="60">
                        <label for="lgScanFacility">Received at <span>*</span></label><select id="lgScanFacility" required></select>
                        <label for="lgParcelDestination">Destination province <span>*</span></label><select id="lgParcelDestination" required></select>
                        <button class="lg-btn lg-btn-primary lg-full" type="submit">Confirm Parcel Arrival</button>
                        <p class="lg-help">USB/Bluetooth scanners that type a tracking number can use this input. Camera scanning requires a separate scanner integration.</p>
                    </form>
                </section>
                <section class="lg-panel lg-flow"><div class="lg-panel-header"><div><h2>How parcels move</h2><p>Sorting and handoff workflow</p></div></div>
                    <div class="lg-flow-step"><span>01</span><div><b>Origin sorting center</b><small>Receive, scan, and group parcels by province.</small></div></div>
                    <div class="lg-flow-step"><span>02</span><div><b>Inter-province transfer</b><small>Record scans at each intermediate sorting center if the route passes through one.</small></div></div>
                    <div class="lg-flow-step"><span>03</span><div><b>Destination sorting center</b><small>Sort parcels into destination municipality batches.</small></div></div>
                    <div class="lg-flow-step"><span>04</span><div><b>Municipal delivery hub</b><small>Receive the parcel for local delivery handling.</small></div></div>
                </section>
            </aside>
        </div>
        <div class="lg-lower-grid">
            <section class="lg-panel"><div class="lg-panel-header"><div><h2>Recent Parcel Activity</h2><p>Arrival history for this demo session, with the latest scans first.</p></div><span class="lg-soft-pill" id="lgActivityBadge">0 check-ins</span></div>
                <div class="lg-table-scroll"><table class="lg-table lg-activity-table"><thead><tr><th>Tracking No.</th><th>Received at</th><th>Destination</th><th>Check-in time</th></tr></thead><tbody id="lgActivityRows"></tbody></table></div>
            </section>
            <section class="lg-panel"><div class="lg-panel-header"><div><h2>Earnings Snapshot</h2><p>Space for COD handling and delivery fees once records are connected.</p></div></div>
                <div class="lg-earnings-total"><small>Available earnings data</small><strong>₱ 0.00</strong><span>Backend integration pending</span></div>
                <div class="lg-earnings-row"><span>Handling / sorting fees</span><b>₱ 0.00</b></div><div class="lg-earnings-row"><span>Delivery hub allocation</span><b>₱ 0.00</b></div><div class="lg-earnings-row"><span>Settlements pending</span><b>₱ 0.00</b></div>
                <p class="lg-help">No fabricated sales, parcel earnings, or settlement figures are shown.</p>
            </section>
        </div>
        <section class="lg-panel lg-announcements" aria-label="Platform announcements">
            <div class="lg-panel-header"><div><h2>Platform Announcements</h2><p>Updates from the ShopEase administration.</p></div></div>
            <div class="lg-announcement-slides">
                @forelse($dashboardAnnouncements as $index => $announcement)
                    <article data-logistics-announcement @if($index > 0) hidden @endif>
                        <small>{{ $announcement->badge_label ?: ($announcement->type ?: 'Announcement') }} · {{ ($announcement->published_at ?? $announcement->created_at)?->format('F Y') }}</small>
                        <h3>{{ $announcement->title }}</h3>
                        <p>{{ $announcement->body ?: 'There are no details for this announcement.' }}</p>
                    </article>
                @empty
                    <p class="lg-help">No platform announcements available yet.</p>
                @endforelse
            </div>
            @if($dashboardAnnouncements->count() > 1)
                <button class="lg-btn lg-btn-outline" type="button" data-dashboard-announcement-next aria-label="Next announcement">Next announcement</button>
            @endif
        </section>
    </main>
    <div class="lg-modal" id="lgHubModal" hidden role="dialog" aria-modal="true" aria-labelledby="lgModalTitle"><div class="lg-modal-card">
        <div class="lg-modal-header"><div><h2 id="lgModalTitle">Create a Branch / Hub</h2><p>Add a new facility to the demo logistics network.</p></div><button type="button" class="lg-close" data-close-modal aria-label="Close">×</button></div>
        <form id="lgHubForm"><div class="lg-form-grid">
            <div class="lg-field"><label for="lgFacilityName">Facility name <span>*</span></label><input id="lgFacilityName" required maxlength="90" placeholder="e.g. Laguna Sorting Center"></div>
            <div class="lg-field"><label for="lgFacilityType">Facility type <span>*</span></label><select id="lgFacilityType" required><option value="sorting">Provincial Sorting Center</option><option value="delivery">Municipal Delivery Hub</option></select></div>
            <div class="lg-field"><label for="lgProvinceInput">Province <span>*</span></label><input id="lgProvinceInput" required maxlength="65" placeholder="e.g. Laguna"></div>
            <div class="lg-field"><label for="lgMunicipalityInput">City / Municipality <span>*</span></label><input id="lgMunicipalityInput" required maxlength="65" placeholder="e.g. Santa Cruz"></div>
            <div class="lg-field lg-wide"><label for="lgParentCenter">Parent provincial sorting center</label><select id="lgParentCenter"></select><small id="lgParentHelp">Required for delivery hubs. Sorting centers serve their own province.</small></div>
        </div><p id="lgHubValidation" class="lg-form-error" aria-live="polite"></p>
        <div class="lg-modal-actions"><button type="button" class="lg-btn lg-btn-outline" data-close-modal>Cancel</button><button type="submit" class="lg-btn lg-btn-primary">Create Facility (Demo)</button></div></form>
    </div></div>
    <div id="lgToast" class="lg-toast" role="status" aria-live="polite" hidden></div>
    <script>
// ShopEase Logistics Dashboard — UI DEMO ONLY. No backend records are created or modified.
document.addEventListener('DOMContentLoaded', () => {
  'use strict';
  const $ = id => document.getElementById(id);
  const body = document.body;
  const storageKey = 'shopease-logistics-dashboard-demo-v1';
  const provinces = ['Abra','Agusan del Norte','Agusan del Sur','Aklan','Albay','Antique','Apayao','Aurora','Basilan','Bataan','Batanes','Batangas','Benguet','Biliran','Bohol','Bukidnon','Bulacan','Cagayan','Camarines Norte','Camarines Sur','Camiguin','Capiz','Catanduanes','Cavite','Cebu','Cotabato','Davao del Norte','Davao del Sur','Davao Occidental','Davao Oriental','Dinagat Islands','Eastern Samar','Guimaras','Ifugao','Ilocos Norte','Ilocos Sur','Iloilo','Isabela','Kalinga','La Union','Laguna','Lanao del Norte','Lanao del Sur','Leyte','Maguindanao del Norte','Maguindanao del Sur','Marinduque','Masbate','Misamis Occidental','Misamis Oriental','Mountain Province','Negros Occidental','Negros Oriental','Northern Samar','Nueva Ecija','Nueva Vizcaya','Occidental Mindoro','Oriental Mindoro','Palawan','Pampanga','Pangasinan','Quezon','Quirino','Rizal','Romblon','Samar','Sarangani','Siquijor','Sorsogon','South Cotabato','Southern Leyte','Sultan Kudarat','Sulu','Surigao del Norte','Surigao del Sur','Tarlac','Tawi-Tawi','Zambales','Zamboanga del Norte','Zamboanga del Sur','Zamboanga Sibugay'];
  const demoFacilities = [
    {id:'sc-laguna',name:'Laguna Provincial Sorting Center',type:'sorting',province:'Laguna',city:'Santa Cruz',parent:null},
    {id:'dh-pila',name:'Pila Delivery Hub',type:'delivery',province:'Laguna',city:'Pila',parent:'sc-laguna'},
    {id:'dh-calamba',name:'Calamba Delivery Hub',type:'delivery',province:'Laguna',city:'Calamba',parent:'sc-laguna'},
    {id:'sc-cavite',name:'Cavite Provincial Sorting Center',type:'sorting',province:'Cavite',city:'Trece Martires',parent:null},
    {id:'dh-dasma',name:'Dasmariñas Delivery Hub',type:'delivery',province:'Cavite',city:'Dasmariñas',parent:'sc-cavite'},
    {id:'sc-batangas',name:'Batangas Provincial Sorting Center',type:'sorting',province:'Batangas',city:'Batangas City',parent:null}
  ];
  let state;
  try { state = JSON.parse(localStorage.getItem(storageKey) || 'null'); } catch { state = null; }
  if (!state || !Array.isArray(state.facilities) || !Array.isArray(state.scans)) state = {facilities:demoFacilities,scans:[]};
  const persist = () => { try { localStorage.setItem(storageKey,JSON.stringify(state)); } catch {} };
  const norm = s => String(s || '').trim().toLocaleLowerCase();
  const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const parentOf = f => state.facilities.find(p => p.id === f.parent);
  let toastTimeout;
  function toast(message) { const el=$('lgToast'); el.textContent=message;el.hidden=false;clearTimeout(toastTimeout);toastTimeout=setTimeout(()=>el.hidden=true,4300); }
  const provSelect = $('lgHubProvince');
  function renderFacilities() {
    const query=norm($('lgHubSearch').value), type=$('lgHubType').value, prov=provSelect.value;
    const results=state.facilities.filter(f=>(type==='all'||f.type===type)&&(prov==='all'||norm(f.province)===norm(prov))&&norm([f.name,f.city,f.province,parentOf(f)?.name].join(' ')).includes(query));
    $('lgHubRows').innerHTML=results.map(f=>`<tr><td><strong>${esc(f.name)}</strong><small>${f.type==='sorting'?'Province-level routing':'Last-mile distribution'}</small></td><td>${esc(f.city)}<small>${esc(f.province)}</small></td><td><span class="lg-tag ${f.type}">${f.type==='sorting'?'Sorting Center':'Delivery Hub'}</span></td><td>${esc(parentOf(f)?.name || 'Provincial origin')}</td><td><span class="lg-tag active">Active · Demo</span></td></tr>`).join('') || '<tr><td class="lg-table-empty" colspan="5">No facilities match your search.</td></tr>';
    $('lgHubCount').textContent=`${results.length} of ${state.facilities.length} facilities`;
    $('lgStat-sorting').textContent=state.facilities.filter(f=>f.type==='sorting').length;
    $('lgStat-hubs').textContent=state.facilities.filter(f=>f.type==='delivery').length;
    $('lgStat-parcels').textContent=state.scans.length;
    const provinceBefore=provSelect.value;
    provSelect.innerHTML='<option value="all">All provinces</option>'+[...new Set(state.facilities.map(f=>f.province))].sort().map(p=>`<option value="${esc(p)}">${esc(p)}</option>`).join('');
    provSelect.value=[...provSelect.options].some(o=>o.value===provinceBefore)?provinceBefore:'all';
    const facilityBefore=$('lgScanFacility').value;
    $('lgScanFacility').innerHTML='<option value="">Select facility...</option>'+state.facilities.map(f=>`<option value="${esc(f.id)}">${esc(f.name)} · ${esc(f.province)}</option>`).join('');
    $('lgScanFacility').value=facilityBefore;
    fillParents();
  }
  function fillParents() {
    const current=$('lgParentCenter').value;
    $('lgParentCenter').innerHTML='<option value="">Choose provincial sorting center...</option>'+state.facilities.filter(f=>f.type==='sorting').map(f=>`<option value="${esc(f.id)}">${esc(f.name)} · ${esc(f.province)}</option>`).join('');
    $('lgParentCenter').value=current;
  }
  function syncType() { const delivery=$('lgFacilityType').value==='delivery'; $('lgParentCenter').disabled=!delivery; $('lgParentCenter').required=delivery; $('lgParentHelp').textContent=delivery?'A hub must connect to a sorting center in the same province.':'Only one provincial sorting center is allowed per province in this demo.'; }
  function renderScans() {
    $('lgActivityBadge').textContent=`${state.scans.length} check-ins`;
    $('lgActivityRows').innerHTML=state.scans.slice(0,12).map(s=>`<tr><td><strong>${esc(s.code)}</strong></td><td>${esc(s.facility)}</td><td>${esc(s.destination)}</td><td>${esc(new Date(s.timestamp).toLocaleString('en-PH',{dateStyle:'medium',timeStyle:'short'}))}</td></tr>`).join('') || '<tr><td colspan="4" class="lg-table-empty">No parcel check-ins yet. Use the scanner form to create a demo arrival record.</td></tr>';
  }
  $('lgHubSearch').addEventListener('input',renderFacilities);
  $('lgHubType').addEventListener('change',renderFacilities);
  provSelect.addEventListener('change',renderFacilities);
  $('lgFacilityType').addEventListener('change',syncType);
  function openModal() { $('lgHubModal').hidden=false;body.classList.add('lg-modal-open');$('lgHubValidation').textContent='';$('lgFacilityName').focus();syncType(); }
  function closeModal() { $('lgHubModal').hidden=true;body.classList.remove('lg-modal-open'); }
  document.querySelectorAll('[data-modal="hub"]').forEach(b=>b.addEventListener('click',openModal));
  document.querySelectorAll('[data-close-modal]').forEach(b=>b.addEventListener('click',closeModal));
  $('lgHubModal').addEventListener('click',e=>{if(e.target===$('lgHubModal'))closeModal();});
  document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal();});
  $('lgHubForm').addEventListener('submit',e=>{
    e.preventDefault();
    const name=$('lgFacilityName').value.trim(),type=$('lgFacilityType').value,province=$('lgProvinceInput').value.trim(),city=$('lgMunicipalityInput').value.trim(),parent=$('lgParentCenter').value;
    let error='';
    if (!name||!province||!city) error='Please complete the required fields.';
    else if (state.facilities.some(f=>norm(f.name)===norm(name))) error='A facility with this name already exists.';
    else if (type==='sorting'&&state.facilities.some(f=>f.type==='sorting'&&norm(f.province)===norm(province))) error='This province already has a sorting center. Add a municipal delivery hub instead.';
    else if (type==='delivery'&&!parent) error='Please choose the parent sorting center.';
    else if (type==='delivery'&&norm(state.facilities.find(f=>f.id===parent)?.province)!==norm(province)) error='The delivery hub must belong to the same province as its parent sorting center.';
    if(error){$('lgHubValidation').textContent=error;return;}
    state.facilities.push({id:'demo-'+Date.now().toString(36),name,type,province,city,parent:type==='delivery'?parent:null});persist();
    const prev=provSelect.value; $('lgHubForm').reset();$('lgFacilityType').value='sorting';closeModal();renderFacilities();syncType();toast(`${name} created in browser demo data.`);
  });
  $('lgParcelDestination').innerHTML='<option value="">Select destination province...</option>'+provinces.map(p=>`<option value="${esc(p)}">${esc(p)}</option>`).join('');
  $('lgScanForm').addEventListener('submit',e=>{
    e.preventDefault();
    const code=$('lgTrackingCode').value.trim().toUpperCase(),id=$('lgScanFacility').value,destination=$('lgParcelDestination').value;
    const f=state.facilities.find(x=>x.id===id);
    if(!code||!f||!destination){toast('Please provide a tracking code, facility, and destination province.');return;}
    const duplicate=state.scans.find(s=>s.code===code&&s.facilityId===id);
    if(duplicate){toast('This parcel has already been checked in at that facility in the demo.');return;}
    state.scans.unshift({code,facilityId:id,facility:f.name,destination,timestamp:new Date().toISOString()});persist();renderScans();renderFacilities();$('lgTrackingCode').value='';$('lgTrackingCode').focus();toast(`${code} marked as received at ${f.name} (demo only).`);
  });
  $('lgSidebarToggle').addEventListener('click',()=>{body.classList.toggle('lg-collapsed');$('lgSidebarToggle').setAttribute('aria-expanded',String(!body.classList.contains('lg-collapsed')));});
  $('lgProfileButton').addEventListener('click',()=>{const menu=$('lgProfileMenu');menu.hidden=!menu.hidden;$('lgProfileButton').setAttribute('aria-expanded',String(!menu.hidden));});
  document.addEventListener('click',e=>{if(!e.target.closest('.lg-profile-wrap')){$('lgProfileMenu').hidden=true;$('lgProfileButton').setAttribute('aria-expanded','false');}});
  $('lgNotifyButton').addEventListener('click',()=>toast('No live notifications connected yet.'));
  const announcements=[...document.querySelectorAll('[data-logistics-announcement]')];
  let currentAnnouncement=0;
  document.querySelector('[data-dashboard-announcement-next]')?.addEventListener('click',()=>{
    announcements[currentAnnouncement].hidden=true;
    currentAnnouncement=(currentAnnouncement+1)%announcements.length;
    announcements[currentAnnouncement].hidden=false;
  });
  renderFacilities();renderScans();syncType();
});

    </script>
</body>
</html>
