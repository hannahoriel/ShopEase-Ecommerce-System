{{-- Delivery Hub Dashboard — follows the Sorting Center dashboard layout. Data can be supplied by a Delivery Hub controller. --}}
@php
    $deliveryHub = $deliveryHub ?? $hub ?? null;
    $hubName = data_get($deliveryHub, 'hub_name') ?: data_get($deliveryHub, 'branch_name') ?: data_get($deliveryHub, 'name') ?: 'Delivery Hub';
    $hubStats = $deliveryHubStats ?? [];
    $hubAnnouncements = collect($deliveryHubAnnouncements ?? $dashboardAnnouncements ?? []);
    $incomingBatches = collect($incomingBatches ?? []);
    $receivedBatches = collect($receivedBatches ?? []);
    $departedBatches = collect($departedBatches ?? []);
    $deliveryAssignments = collect($deliveryAssignments ?? []);
    $hubVehicles = collect($registeredVehicles ?? []);
    $hubDrivers = collect($registeredDrivers ?? []);
    $hasBackendData = isset($deliveryHubStats) || isset($incomingBatchesProvided) || isset($deliveryAssignmentsProvided);
@endphp
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><title>Delivery Hub Dashboard — ShopEase</title><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet"><style>
/* The shared Sorting Center sidebar supplies the same .lg-body/.lg-main layout styles as Logistics. */
.sc-main,.sc-main *{box-sizing:border-box}
.sc-main{color:#241b19;font-family:Poppins,sans-serif;max-width:none}
.sc-inner{max-width:1610px;margin:0 auto}
.sc-hero{margin-bottom:23px}
.sc-eyebrow{font-size:10px;font-weight:700;letter-spacing:1.3px;color:#9b1239;text-transform:uppercase;margin:0 0 6px}
.sc-hero h1{font-size:25px;font-weight:600;line-height:1.35;margin:0;color:#261e1c}
.sc-hero p:last-child{font-size:12px;color:#918883;margin:5px 0 0}
.sc-section{margin:24px 0 0}
.sc-section h2{font-size:15px;font-weight:600;margin:0 0 12px;color:#2b2020}
.sc-section-sub{font-size:11px;color:#9b928e;margin:-6px 0 13px}
.sc-grid4,.sc-grid2{display:grid;gap:14px}.sc-grid4{grid-template-columns:repeat(4,minmax(0,1fr))}.sc-grid2{grid-template-columns:repeat(2,minmax(0,1fr))}
.sc-kpi{background:white;border:1px solid #f0e9e6;border-radius:15px;min-height:115px;padding:20px 18px;display:flex;align-items:center;gap:14px;box-shadow:0 3px 13px #33100e08}
.sc-kpi-icon{height:43px;width:43px;flex:0 0 43px;border-radius:12px;background:#fff0ea;color:#80232b;display:grid;place-items:center}
.sc-kpi-icon svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.sc-kpi:nth-child(4n+2) .sc-kpi-icon{background:#eef3fc;color:#4975a8}.sc-kpi:nth-child(4n+3) .sc-kpi-icon{background:#eaf6f0;color:#3b855b}.sc-kpi:nth-child(4n+4) .sc-kpi-icon{background:#fff3e4;color:#a9782f}
.sc-kpi-label{font-size:11px;color:#908682;line-height:1.5}.sc-kpi strong{font-size:25px;font-weight:600;display:block;margin:4px 0 0;color:#241b19;line-height:1.2}
.sc-movement .sc-kpi{min-height:104px}.sc-movement .sc-kpi strong{font-size:24px}
.sc-top{display:grid;grid-template-columns:minmax(0,1.6fr) minmax(300px,1fr);gap:17px;align-items:stretch;margin-top:24px}
.sc-panel{border:1px solid #f0e5e1;border-radius:16px;background:#fff;box-shadow:0 3px 12px #33100e07;min-width:0}
.sc-tracker{padding:22px 23px}.sc-tracker h2{font-size:15px;font-weight:600;margin:0 0 9px}.sc-clock{font-size:11px;color:#998b85;font-weight:400;margin:0 0 19px;font-variant-numeric:tabular-nums;display:flex;gap:7px;align-items:center}.sc-clock:before{content:'';width:5px;height:5px;background:#bc9992;border-radius:50%}
.sc-track-form{display:flex;gap:9px}.sc-track-form input{width:100%;min-width:0;height:43px;padding:0 14px;border:1px solid #ded1cd;border-radius:9px;outline:0;font:400 12px Poppins,sans-serif}.sc-track-form input:focus{border-color:#8b2731;box-shadow:0 0 0 3px #8b273110}
.sc-primary{border:1px solid #7b1b1b;color:#fff;background:#7b1b1b;border-radius:8px;padding:0 17px;font:600 11px Poppins,sans-serif;cursor:pointer;white-space:nowrap}.sc-primary:hover{background:#641519}
.sc-track-result{min-height:18px;font-size:11px;color:#84716b;margin:13px 0 0;line-height:1.6}
.sc-announcements{background:#52070b;border-color:#52070b;color:white;min-height:246px;display:flex;flex-direction:column;padding:20px 21px 14px}
.sc-announcements h2{font-size:13px;font-weight:600;text-transform:uppercase;color:#f5bbaa;letter-spacing:.4px;margin:0}.sc-announcements .sc-announce-desc{color:#eac2b7;font-size:10px;margin:5px 0 18px}
.sc-slides{flex:1;display:flex;align-items:center}.sc-slides article[hidden]{display:none!important}.sc-slides article{animation:scFade .25s ease}.sc-slides small{display:block;color:#f3b3a4;font-size:10px;letter-spacing:.5px}.sc-slides h3{font-size:18px;font-weight:600;margin:10px 0}.sc-slides p{font-size:11px;color:#f5d9d1;line-height:1.8;margin:0}
.sc-ann-controls{display:flex;justify-content:space-between;align-items:center;border-top:1px solid #ffffff24;padding-top:12px;margin-top:17px}.sc-dots{display:flex;gap:6px}.sc-dots button{padding:0;border:0;border-radius:10px;width:9px;height:5px;background:#ffffff66;cursor:pointer}.sc-dots button.active{width:25px;background:#ffdacd}.sc-arrows{display:flex;gap:6px}.sc-arrows button{width:29px;height:29px;border-radius:50%;background:transparent;border:1px solid #ffffff70;color:white;font-size:20px;cursor:pointer}.sc-arrows button:hover{background:#ffffff23}
.sc-info{margin-top:15px;color:#9a8b83;font-size:10px;line-height:1.7}
@keyframes scFade{from{opacity:.35;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}
@media(max-width:1240px){.sc-grid4{grid-template-columns:repeat(2,minmax(0,1fr))}.sc-top{grid-template-columns:1fr}.sc-announcements{min-height:200px}}
@media(max-width:650px){.sc-main{padding-bottom:28px}.sc-hero h1{font-size:21px}.sc-grid4,.sc-grid2{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.sc-kpi{min-height:100px;padding:13px 10px;gap:9px}.sc-kpi-icon{width:32px;height:32px;flex-basis:32px}.sc-kpi-icon svg{width:17px;height:17px}.sc-kpi strong,.sc-movement .sc-kpi strong{font-size:21px}.sc-kpi-label{font-size:10px}.sc-tracker{padding:17px}.sc-track-form{flex-direction:column}.sc-primary{min-height:40px}}
@media(prefers-reduced-motion:reduce){.sc-slides article{animation:none}}

/* Logistics-style tracker and announcements on top; table + earnings below */
.sc-top-reordered{margin-top:0;margin-bottom:24px;align-items:stretch}
.sc-bottom-panels{display:grid;grid-template-columns:minmax(0,1.65fr) minmax(300px,1fr);gap:17px;margin-top:18px;align-items:start}
.sc-panel-heading{padding:19px 19px 14px;display:flex;justify-content:space-between;align-items:center;gap:12px}
.sc-panel-heading h2{font-size:15px;font-weight:600;margin:0;color:#2b2020}.sc-panel-heading p{font-size:11px;color:#978a84;margin:5px 0 0}
.sc-hub-tag{font-size:10px;color:#8a2932;background:#faeded;padding:6px 11px;border-radius:20px;white-space:nowrap}
.sc-table-toolbar{padding:0 19px 15px;display:flex;gap:10px;flex-wrap:wrap}
.sc-hub-search{display:flex;align-items:center;gap:8px;border:1px solid #e1d7d2;border-radius:8px;padding:0 11px;height:39px;flex:1;min-width:210px;color:#918782}
.sc-hub-search input{border:0;background:transparent;outline:0;font:400 11px Poppins,sans-serif;flex:1;min-width:0;width:100%}
.sc-table-toolbar select{border:1px solid #e1d7d2;border-radius:8px;background:#fff;height:39px;padding:0 10px;color:#766d69;font:400 11px Poppins,sans-serif}
.sc-table-scroll{overflow-x:auto;scrollbar-width:none}.sc-table-scroll::-webkit-scrollbar{display:none}
.sc-hub-table{width:100%;border-collapse:collapse;min-width:590px}.sc-hub-table th{padding:13px 16px;text-align:left;color:#8e827e;font-size:11px;font-weight:500;border-bottom:1px solid #ede6e3}.sc-hub-table td{padding:16px;font-size:11px;border-bottom:1px solid #f0eae7;color:#403633}.sc-hub-table td strong{font-weight:600;font-size:11px}
.sc-hub-table tbody tr:hover{background:#fffaf8}.sc-hub-table .sc-hub-empty td{text-align:center;color:#978b86;padding:36px 12px}
.sc-status{border-radius:30px;padding:5px 11px;font-size:10px}.sc-status.active{background:#e9f7ed;color:#297445}.sc-status.inactive{background:#f7ebe9;color:#966057}.sc-table-footer{padding:14px 19px;color:#948780;font-size:10px}
.sc-earnings-total{background:#fff9f7;border:1px solid #f3e5e0;border-radius:12px;margin:0 19px 12px;padding:17px}.sc-earnings-total span,.sc-earnings-total small{font-size:10px;color:#9b918e;display:block}.sc-earnings-total strong{font-size:24px;color:#7b1b1b;display:block;margin:7px 0;font-weight:600}
.sc-earnings-row{display:flex;justify-content:space-between;align-items:center;gap:12px;border-bottom:1px solid #f2ebea;padding:13px 0;margin:0 19px;font-size:11px}.sc-earnings-row:last-child{border-bottom:0;margin-bottom:10px}.sc-earnings-row span{color:#958884}.sc-earnings-row strong{font-weight:600;color:#3c3030;text-align:right}
@media(max-width:1120px){.sc-bottom-panels{grid-template-columns:1fr}}
@media(max-width:650px){.sc-table-toolbar{flex-direction:column}.sc-table-toolbar select{width:100%}}

.dh-move-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.dh-table-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px;margin-top:18px}.dh-section-head{display:flex;justify-content:space-between;gap:12px;align-items:center;padding:18px 19px 13px}.dh-section-head h2{font-size:14px;font-weight:600;margin:0}.dh-section-head p{font-size:10px;color:#948782;margin:5px 0 0}.dh-view-link{color:#83242c;font-size:11px;font-weight:600;white-space:nowrap}.dh-list{padding:0 17px 13px;display:grid;gap:9px}.dh-batch{border:1px solid #f1e8e5;border-radius:11px;padding:12px 13px;background:#fff;display:flex;align-items:center;justify-content:space-between;gap:12px}.dh-batch strong{font-size:11px;font-weight:600;display:block;color:#322622}.dh-batch small{display:block;color:#998b86;font-size:10px;margin-top:4px;line-height:1.5}.dh-batch-side{text-align:right;flex:0 0 auto}.dh-batch-side b{font-size:12px;display:block}.dh-pill{font-size:9px;font-weight:600;padding:5px 8px;border-radius:20px;display:inline-block;margin-top:5px}.dh-pill.incoming{color:#a36a1e;background:#fff2de}.dh-pill.received{color:#287745;background:#e9f7ed}.dh-pill.departed{color:#8462a3;background:#f3eefa}.dh-pill.delivery{color:#336daa;background:#eaf2fd}.dh-empty{text-align:center;color:#a0938d;font-size:11px;padding:22px 10px;line-height:1.7}.dh-count-note{color:#968880;font-size:10px;margin:0 19px 15px}.dh-rider{display:flex;align-items:center;justify-content:space-between;gap:12px;border:1px solid #f0e8e5;padding:12px;border-radius:12px}.dh-avatar{width:38px;height:38px;background:#f7e4e1;color:#862d33;font-size:12px;font-weight:700;border-radius:50%;display:grid;place-items:center;flex:0 0 auto}.dh-rider-main{flex:1;min-width:0}.dh-rider-main b{display:block;font-size:11px}.dh-rider-main small{display:block;font-size:10px;color:#958783;margin-top:4px}.dh-rider-count{font-size:12px;font-weight:700;color:#8d2c36;white-space:nowrap}.dh-note{color:#958b85;font-size:10px;margin-top:17px;line-height:1.8}.dh-top-actions{display:flex;gap:9px;align-items:center;flex-wrap:wrap}.dh-stats-compact .sc-kpi{min-height:105px}.dh-batch-tag{font-size:10px;background:#fbedec;color:#8e2e36;padding:6px 10px;border-radius:20px}.dh-bar{height:5px;width:100%;border-radius:9px;background:#f2eae7;overflow:hidden;margin:10px 0 5px}.dh-bar span{height:100%;display:block;background:#8e333b;border-radius:9px}.dh-summary-strip{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 17px;background:#fff9f6;border-top:1px solid #f2e8e3;font-size:10px;color:#938580}.dh-summary-strip b{color:#7b1b1b;font-weight:600}@media(max-width:1200px){.dh-table-grid{grid-template-columns:1fr}}@media(max-width:850px){.dh-move-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:650px){.dh-batch{padding:11px}.dh-section-head{flex-wrap:wrap}}
.dh-date-filter{display:flex;align-items:end;flex-wrap:wrap;gap:10px;margin:0 0 20px;padding:13px 16px;border:1px solid #eee0dc;border-radius:13px;background:#fff}.dh-date-filter label{font-size:10px;color:#806d68;display:grid;gap:5px}.dh-date-filter input{height:37px;min-width:155px;padding:0 10px;border:1px solid #d9d0cc;border-radius:8px;font:500 11px Poppins,sans-serif;color:#3e3030}.dh-date-filter button{height:37px;border:1px solid #e7cac8;background:#fff4f1;color:#782329;border-radius:8px;padding:0 13px;font:600 11px Poppins,sans-serif;cursor:pointer}.dh-date-hint{font-size:10px;color:#958680;margin-left:auto}.dh-batch-info{font-size:10px;color:#6e5e59;line-height:1.6;margin-top:6px}.dh-batch-extra{display:block;margin-top:3px;color:#85343d;font-weight:600}.dh-batch{align-items:flex-start}.dh-batch>div:first-child{min-width:0;overflow-wrap:anywhere}@media(max-width:650px){.dh-date-hint{margin-left:0;width:100%}.dh-date-filter input{min-width:0;width:100%}}
</style></head><body class="lg-body">
@include('components.logistics.delivery-hub.sidebar')
@include('components.logistics.delivery-hub.navbar')
<main class="lg-main sc-main" id="dhMain"><div class="sc-inner">
<header class="sc-hero"><p class="sc-eyebrow">ShopEase · Delivery Hub Operations</p><h1>{{ $hubName }}</h1><p>Monitor received batches, incoming transfers, dispatches, out-for-delivery parcels, and assigned riders.</p></header>
 <section class="dh-date-filter" aria-label="Delivery hub dashboard date range"><label>From<input id="dhDateFrom" type="date"></label><label>To<input id="dhDateTo" type="date"></label><button id="dhDateToday" type="button">Today</button><span class="dh-date-hint" id="dhDateHint">Showing today's hub activity</span></section>
 <div class="sc-top sc-top-reordered">
  <section class="sc-panel sc-tracker" aria-labelledby="scTrackTitle"><h2 id="scTrackTitle">Track a Parcel</h2><p class="sc-clock" id="scLiveClock">Loading Philippine date and time…</p><form class="sc-track-form" id="scTrackForm"><input type="search" id="scTrackQuery" placeholder="Enter parcel tracking number" aria-label="Parcel tracking number" maxlength="80" required><button class="sc-primary" type="submit">Track Parcel</button></form><p id="scTrackResult" role="status" class="sc-track-result">Enter a tracking number. Live lookup will be available when parcel tracking is connected.</p></section>
  <aside class="sc-panel sc-announcements" aria-label="Platform announcements"><h2>Platform Announcement</h2><p class="sc-announce-desc">Updates from ShopEase administration</p><div class="sc-slides">
  @forelse($hubAnnouncements as $i => $announcement)
    <article data-dh-announcement {{ $i > 0 ? 'hidden' : '' }}><small>{{ data_get($announcement,'badge_label','ANNOUNCEMENT') }}</small><h3>{{ data_get($announcement,'title','Update') }}</h3><p>{{ data_get($announcement,'body','') }}</p></article>
  @empty
    <article data-dh-announcement><small>WELCOME · PREVIEW</small><h3>Welcome to Your Delivery Hub</h3><p>Monitor your local delivery network, parcel movements, and operational updates here.</p></article>
    <article data-dh-announcement hidden><small>OPERATIONS · PREVIEW</small><h3>Parcel Movement Overview</h3><p>Incoming and departed parcel counts will reflect confirmed shipment records once connected.</p></article>
    <article data-dh-announcement hidden><small>PLATFORM · PREVIEW</small><h3>Announcements Appear Here</h3><p>Published notices from ShopEase administration will appear in this carousel.</p></article>
  @endforelse
  </div><div class="sc-ann-controls"><div class="sc-dots" id="scAnnDots"></div><div class="sc-arrows"><button type="button" id="scAnnPrev" aria-label="Previous announcement">‹</button><button type="button" id="scAnnNext" aria-label="Next announcement">›</button></div></div></aside>

 </div>

<section class="sc-section dh-stats-compact"><h2>At This Delivery Hub</h2><div class="sc-grid4">
 <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="m3 7 9-4 9 4v10l-9 4-9-4zM3 7l9 4 9-4m-9 4v10"/></svg></span><div><span class="sc-kpi-label">Parcels at This Hub</span><strong id="dhStatParcels">0</strong></div></article>
 <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M3 17V7h12v10H3zm12-7h4l3 4v3h-7M5 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm14 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg></span><div><span class="sc-kpi-label">Vehicles</span><strong id="dhStatVehicles">0</strong></div></article>
 <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg></span><div><span class="sc-kpi-label">Drivers</span><strong id="dhStatDrivers">0</strong></div></article>
 <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M12 3 2 21h20L12 3zm0 7v5m0 3h.01"/></svg></span><div><span class="sc-kpi-label">Parcel Disputes</span><strong id="dhStatDisputes">0</strong></div></article>
</div></section>
<section class="sc-section sc-movement"><h2>Parcels Moving Through This Hub</h2><p class="sc-section-sub">Incoming transfers, departed parcels, and current last-mile deliveries.</p><div class="dh-move-grid">
 <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M3 12h14m-5-5 5 5-5 5M3 5h6m-6 14h6"/></svg></span><div><span class="sc-kpi-label">Incoming Transfers</span><strong id="dhStatIncoming">0</strong></div></article>
 <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M3 12h18m-6-6 6 6-6 6M3 6h6m-6 12h6"/></svg></span><div><span class="sc-kpi-label">Departed Parcels</span><strong id="dhStatDeparted">0</strong></div></article>
 <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M4 17V5h12v12H4zm12-8h3l3 4v4h-6M7 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm12 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg></span><div><span class="sc-kpi-label">Out for Delivery Parcels</span><strong id="dhStatDelivery">0</strong></div></article>
 <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M4 12l5 5L20 6"/></svg></span><div><span class="sc-kpi-label">Delivered Parcels</span><strong id="dhStatDelivered">0</strong></div></article>
</div></section>
<section class="sc-section"><h2>Parcel Batch Activity</h2><p class="sc-section-sub">Each batch stays separate. Received list and Parcels at This Hub use the same on-site parcel records. Departed and delivered events are tracked separately.</p>
 <div class="dh-table-grid">
  <article class="sc-panel"><div class="dh-section-head"><div><h2>Recently Received Batches</h2><p>Latest batches confirmed by the Received Scanner</p></div><a class="dh-view-link" href="{{ \Illuminate\Support\Facades\Route::has('delivery-hub.scanners') ? route('delivery-hub.scanners') : '#' }}">View Scanner →</a></div><div class="dh-list" id="dhReceivedList"></div><div class="dh-summary-strip"><span>Most recently received</span><b id="dhLastReceived">No batches yet</b></div></article>
  <article class="sc-panel"><div class="dh-section-head"><div><h2>Incoming Transfer Batches</h2><p>Expected groups with assigned courier and truck</p></div><span class="dh-batch-tag" id="dhIncomingBatchTotal">0 batches</span></div><div class="dh-list" id="dhIncomingList"></div><div class="dh-summary-strip"><span>Unreceived parcels</span><b id="dhPendingTotal">0 awaiting scan</b></div></article>
  <article class="sc-panel"><div class="dh-section-head"><div><h2>Recently Departed Batches</h2><p>Destination, assigned courier, and truck from loading/dispatch</p></div><span class="dh-batch-tag" id="dhDepartedBatchTotal">0 batches</span></div><div class="dh-list" id="dhDepartedList"></div><div class="dh-summary-strip"><span>Latest dispatch</span><b id="dhLastDeparted">No departures yet</b></div></article>
  <article class="sc-panel"><div class="dh-section-head"><div><h2>Out for Delivery · Rider / Driver Assignments</h2><p>Who is currently on the road with customer parcels</p></div><span class="dh-batch-tag" id="dhActiveRiders">0 active</span></div><div class="dh-list" id="dhRiderList"></div><div class="dh-summary-strip"><span>Assigned parcels on the road</span><b id="dhRiderParcelTotal">0 parcels</b></div></article>
 </div>
</section>
<div class="sc-bottom-panels">
 <section class="sc-panel"><div class="sc-panel-heading"><div><h2>Recent Hub Activity</h2><p>Parcel movement and operational events</p></div><span class="sc-hub-tag">Latest updates</span></div><div class="sc-table-scroll"><table class="sc-hub-table"><thead><tr><th>Batch / Parcel</th><th>Activity</th><th>Assigned Courier / Truck</th><th>Date &amp; Time</th></tr></thead><tbody id="dhActivityRows"></tbody></table></div><div class="sc-table-footer">Activity from this delivery hub only</div></section>
 <section class="sc-panel sc-earnings-panel"><div class="sc-panel-heading"><div><h2>Network Earnings</h2><p>Delivery hub revenue overview</p></div><a class="dh-view-link" href="{{ \Illuminate\Support\Facades\Route::has('delivery-hub.network-earnings') ? route('delivery-hub.network-earnings') : '#' }}">View Details →</a></div><div class="sc-earnings-total"><span>Available Earnings</span><strong id="dhEarnings">₱ 0.00</strong><small id="dhEarningsNote">Dashboard preview data</small></div><div class="sc-earnings-row"><span>Delivery Fees</span><strong id="dhDeliveryFees">₱ 0.00</strong></div><div class="sc-earnings-row"><span>Handling Fees</span><strong id="dhHandlingFees">₱ 0.00</strong></div><div class="sc-earnings-row"><span>Settlement Status</span><strong id="dhSettlement">Not connected</strong></div></section>
</div>
<p class="dh-note">Demo batches and rider assignments appear only when the dashboard has no supplied operational data. They demonstrate grouping and display; they do not confirm parcel scans, dispatches, payments, or actual rider locations.</p>
</div></main>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 'use strict';const $=id=>document.getElementById(id);const esc=x=>String(x??'—').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
 const fmtTime=value=>{if(!value)return '—';let d=new Date(value);return Number.isNaN(+d)?String(value):new Intl.DateTimeFormat('en-PH',{timeZone:'Asia/Manila',month:'short',day:'numeric',year:'numeric',hour:'numeric',minute:'2-digit'}).format(d)};
 const clock=()=>{$('scLiveClock').textContent=new Intl.DateTimeFormat('en-PH',{timeZone:'Asia/Manila',weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'numeric',minute:'2-digit',second:'2-digit',hour12:true}).format(new Date())};clock();setInterval(clock,1000);
 const supplied={stats: @json($hubStats), incoming: @json($incomingBatches->values()), received: @json($receivedBatches->values()), departed: @json($departedBatches->values()), assignments: @json($deliveryAssignments->values()), vehicles: @json($hubVehicles->values()), drivers: @json($hubDrivers->values()), delivered: @json(collect($deliveredParcels ?? [])->values())};
 // Distinct group IDs: an incoming batch is never merged with another batch simply because destinations match.
 const demo={
 incoming:[{id:'DH-IN-302',origin:'Laguna Sorting Center',total:5,received:3,pending:2,arrival_at:'2026-10-30T15:30:00+08:00',courier:'Mark Angelo Reyes',truck:'Isuzu Forward · ABC 1234'},{id:'DH-IN-303',origin:'Laguna Sorting Center',total:4,received:0,pending:4,arrival_at:'2026-10-30T17:15:00+08:00',courier:'Joshua Miguel Santos',truck:'Hino 500 · DEF 5678'}],
 received:[{id:'DH-IN-301',origin:'Laguna Sorting Center',total:5,received:5,received_at:'2026-10-30T09:40:00+08:00',staff:'Receiving Team'},{id:'DH-IN-300',origin:'Laguna Sorting Center',total:4,received:4,received_at:'2026-10-29T16:25:00+08:00',staff:'Receiving Team'}],
 departed:[{id:'DH-OUT-118',destination:'Pila North Delivery Zone',total:5,departed_at:'2026-10-30T10:20:00+08:00',truck:'Isuzu Forward · ABC 1234',courier:'Mark Angelo Reyes'},{id:'DH-OUT-117',destination:'Santa Cruz Delivery Hub',total:3,departed_at:'2026-10-29T15:45:00+08:00',truck:'Hino 500 · DEF 5678',courier:'Joshua Miguel Santos'}],
 assignments:[{id:'ROUTE-01',name:'Angela Reyes',role:'Rider',vehicle:'Motorcycle · ABC 2345',count:3,left_at:'2026-10-30T09:15:00+08:00'},{id:'ROUTE-02',name:'Miguel Santos',role:'Driver',vehicle:'Van · DEF 6789',count:2,left_at:'2026-10-30T09:50:00+08:00'}],
 delivered:[{id:'DH-DEL-101',count:2,delivered_at:'2026-10-30T11:15:00+08:00'}],vehicles:[{id:1},{id:2},{id:3}],drivers:[{id:1},{id:2}],stats:{parcel_disputes:1,earnings:2680,delivery_fees:2200,handling_fees:480,settlement_status:'Pending settlement'}
 };
 const haveData=Object.keys(supplied.stats||{}).length>0||supplied.incoming.length||supplied.received.length||supplied.departed.length||supplied.assignments.length||supplied.delivered.length;
 const data=haveData?supplied:demo;
 const n=(obj,...fields)=>{for(const f of fields){const v=obj?.[f];if(v===undefined||v===null||v==='')continue;const num=Number(v);if(Number.isFinite(num)&&num>=0)return num}return 0};
 const qty=(r,type)=>n(r,type==='received'?'on_site_count':'count',type==='received'?'parcels_at_hub':'parcels_count',type==='received'?'received':'total','count');
 const todayPH=()=>{const d=new Intl.DateTimeFormat('en-US',{timeZone:'Asia/Manila',year:'numeric',month:'2-digit',day:'2-digit'}).formatToParts(new Date());const get=t=>d.find(x=>x.type===t).value;return `${get('year')}-${get('month')}-${get('day')}`};
 const dateOf=v=>{if(!v)return '';const parsed=new Date(v);return Number.isNaN(+parsed)?String(v).slice(0,10):todayAt(parsed)};
 const todayAt=d=>{const f=new Intl.DateTimeFormat('en-US',{timeZone:'Asia/Manila',year:'numeric',month:'2-digit',day:'2-digit'}).formatToParts(d);const a=t=>f.find(x=>x.type===t).value;return `${a('year')}-${a('month')}-${a('day')}`};
 const eventDate=(r,kind)=>r[kind+'_at']||r.received_at||r.departed_at||r.arrival_at||r.dispatched_at||r.left_at||r.delivered_at||r.created_at||r.date||'';
 const matches=(r,kind)=>{const d=dateOf(eventDate(r,kind));return d&&d>=$('dhDateFrom').value&&d<=$('dhDateTo').value};
 // Courier (person responsible) is distinct from the assigned truck. Never infer a person from a vehicle field.
 const courierName=r=>r.courier_name||r.assigned_courier_name||r.assigned_courier?.name||r.courier?.name||r.courier||r.driver_name||r.assigned_driver_name||r.driver?.name||r.staff_name||r.staff||'Courier not assigned';
 const truckName=r=>r.truck_name||r.truck?.name||r.truck?.plate_number||r.truck||r.vehicle_name||r.dispatch_vehicle||r.loaded_truck||r.assigned_truck||r.plate_number||r.vehicle_plate||'Truck not recorded';
 const courierTruck=r=>`Courier: ${courierName(r)} · Truck: ${truckName(r)}`;
 const set=(id,v)=>{if($(id))$(id).textContent=String(v)};
 const makeBatch=(r,type)=>{const id=esc(r.id||r.batch_id||r.manifest_id||'Unspecified batch');const total=qty(r,type),pending=n(r,'pending');const detail=type==='incoming'?esc(r.origin||'Origin not provided'):type==='departed'?esc(r.destination||r.destination_name||r.destination_hub||'Destination not recorded'):esc(r.origin||'Received at this hub');const when=fmtTime(eventDate(r,type));const truck=(type==='incoming'||type==='departed')?`<span class="dh-batch-extra">Courier: ${esc(courierName(r))}</span><span class="dh-batch-extra">Truck: ${esc(truckName(r))}</span>`:'';const label=type==='incoming'?'Awaiting Scan':type==='received'?'At Hub':'Departed';const number=type==='incoming'?`${pending} pending`:`${total} parcels`;return `<div class="dh-batch"><div><strong>${id}</strong><small>${detail} · ${esc(when)}</small>${truck}</div><div class="dh-batch-side"><b>${esc(number)}</b><span class="dh-pill ${type}">${label}</span></div></div>`};
 const draw=(id,items,type,empty)=>{$(id).innerHTML=items.length?items.map(r=>makeBatch(r,type)).join(''):`<div class="dh-empty">${esc(empty)}</div>`};
 // The received list is an on-site inventory snapshot: if a shipment leaves, the upstream scanner/backend must update on_site_count.
 // Do not subtract daily departed counts a second time. That would cause contradictions with Received Batches.
 function render(){
  const incoming=data.incoming.filter(r=>matches(r,'arrival'));const received=data.received.filter(r=>matches(r,'received'));const departed=data.departed.filter(r=>matches(r,'departed'));const assignments=data.assignments.filter(r=>matches(r,'left'));const delivered=(data.delivered||[]).filter(r=>matches(r,'delivered'));
  const total=arr=>arr.reduce((sum,r)=>sum+qty(r,'received'),0);
  const atHub=total(received);
  set('dhStatParcels',atHub);set('dhStatVehicles',data.vehicles.length||n(data.stats,'vehicles'));set('dhStatDrivers',data.drivers.length||n(data.stats,'drivers','riders'));set('dhStatDisputes',n(data.stats,'parcel_disputes','disputes'));
  set('dhStatIncoming',incoming.reduce((sum,r)=>sum+n(r,'pending','total'),0));set('dhStatDeparted',departed.reduce((sum,r)=>sum+qty(r,'departed'),0));set('dhStatDelivery',assignments.reduce((sum,r)=>sum+n(r,'count','parcels_count'),0));set('dhStatDelivered',delivered.reduce((sum,r)=>sum+(typeof r==='number'?r:n(r,'count','parcels_count','total')),0));
  draw('dhReceivedList',received,'received','No received parcels still at the hub for this date range.');draw('dhIncomingList',incoming,'incoming','No incoming transfers in selected dates.');draw('dhDepartedList',departed,'departed','No departed batches in selected dates.');
  set('dhLastReceived',received.length?`${atHub} parcels · ${received[0].id||received[0].batch_id}`:'No batches');set('dhIncomingBatchTotal',`${incoming.length} batches`);set('dhPendingTotal',`${incoming.reduce((s,r)=>s+n(r,'pending'),0)} awaiting scan`);set('dhDepartedBatchTotal',`${departed.length} batches`);set('dhLastDeparted',departed.length?String(departed[0].id||departed[0].batch_id):'No departures');
  $('dhRiderList').innerHTML=assignments.length?assignments.map(r=>{const name=String(r.name||r.rider_name||r.driver_name||'Unassigned'),initials=name.split(/\s+/).slice(0,2).map(p=>p[0]).join('').toUpperCase();return `<div class="dh-rider"><span class="dh-avatar">${esc(initials)}</span><div class="dh-rider-main"><b>${esc(name)} · ${esc(r.role||'Rider')}</b><small>${esc(r.vehicle||r.plate_number||'Vehicle not assigned')} · Left ${esc(fmtTime(eventDate(r,'left')))}</small><small>${esc(r.id||r.route_id||'Delivery route')}</small></div><div class="dh-rider-count">${n(r,'count','parcels_count')} parcels</div></div>`}).join(''):'<div class="dh-empty">No active rider / driver dispatch records in selected dates.</div>';
  set('dhActiveRiders',`${assignments.length} active`);set('dhRiderParcelTotal',`${assignments.reduce((sum,r)=>sum+n(r,'count','parcels_count'),0)} parcels`);
  const events=[...received.map(r=>({id:r.id||r.batch_id,action:'Received / at hub',staff:r.staff||'Receiving Team',at:eventDate(r,'received')})),...incoming.map(r=>({id:r.id||r.batch_id,action:'Incoming transfer',staff:courierTruck(r),at:eventDate(r,'arrival')})),...departed.map(r=>({id:r.id||r.batch_id,action:'Departed → '+(r.destination||r.destination_name||'Destination not recorded'),staff:courierTruck(r),at:eventDate(r,'departed')})),...assignments.map(r=>({id:r.id||r.route_id,action:'Out for delivery',staff:r.name||r.rider_name||r.driver_name,at:eventDate(r,'left')})),...delivered.map(r=>({id:r.id||r.tracking_number||'Delivery',action:'Delivered',staff:r.staff||r.rider_name||'—',at:eventDate(r,'delivered')}))].sort((a,b)=>new Date(b.at||0)-new Date(a.at||0)).slice(0,8);
  $('dhActivityRows').innerHTML=events.length?events.map(r=>`<tr><td><strong>${esc(r.id)}</strong></td><td>${esc(r.action)}</td><td>${esc(r.staff)}</td><td>${esc(fmtTime(r.at))}</td></tr>`).join(''):'<tr class="sc-hub-empty"><td colspan="4">No activity recorded for selected dates.</td></tr>';
  const pesos=v=>'₱ '+Number(v||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});set('dhEarnings',pesos(data.stats.earnings));set('dhDeliveryFees',pesos(data.stats.delivery_fees));set('dhHandlingFees',pesos(data.stats.handling_fees));set('dhSettlement',data.stats.settlement_status||'Not connected');set('dhEarningsNote',haveData?'Earnings supplied by backend; period filtering needs dated transactions':'Illustrative demo earnings · not date-filtered');
  set('dhDateHint',`Showing hub activity: ${$('dhDateFrom').value} to ${$('dhDateTo').value} (Philippine dates)`);
 }
 const today=todayPH();$('dhDateFrom').value=today;$('dhDateTo').value=today;
 const updateRange=()=>{if($('dhDateFrom').value>$('dhDateTo').value){$('dhDateTo').value=$('dhDateFrom').value}render()};
 $('dhDateFrom').addEventListener('change',updateRange);$('dhDateTo').addEventListener('change',()=>{if($('dhDateTo').value<$('dhDateFrom').value)$('dhDateFrom').value=$('dhDateTo').value;render()});$('dhDateToday').addEventListener('click',()=>{$('dhDateFrom').value=todayPH();$('dhDateTo').value=todayPH();render()});render();
 $('scTrackForm')?.addEventListener('submit',e=>{e.preventDefault();$('scTrackResult').textContent='Live parcel tracking is not connected yet. No parcel status has been modified.'});
 const slides=[...document.querySelectorAll('[data-dh-announcement]')],dots=$('scAnnDots');let index=0,timer;
 const show=i=>{if(!slides.length)return;index=(i+slides.length)%slides.length;slides.forEach((el,j)=>el.hidden=j!==index);[...dots.children].forEach((el,j)=>el.classList.toggle('active',j===index))};
 slides.forEach((_,i)=>{const b=document.createElement('button');b.type='button';b.setAttribute('aria-label',`Show announcement ${i+1}`);b.onclick=()=>{show(i);restart()};dots.append(b)});
 const restart=()=>{clearInterval(timer);if(slides.length>1&&!document.hidden&&!matchMedia('(prefers-reduced-motion: reduce)').matches)timer=setInterval(()=>show(index+1),6500)};
 $('scAnnPrev')?.addEventListener('click',()=>{show(index-1);restart()});$('scAnnNext')?.addEventListener('click',()=>{show(index+1);restart()});if(slides.length<=1)document.querySelector('.sc-ann-controls').hidden=true;document.addEventListener('visibilitychange',restart);show(0);restart();
});
</script></body></html>