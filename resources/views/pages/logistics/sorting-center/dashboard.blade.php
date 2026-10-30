{{-- Sorting Center Dashboard — frontend-ready; inject $sortingCenter and $sortingCenterStats from controller later. --}}
@php
    $center = $sortingCenter ?? null;
    $centerName = data_get($center, 'branch_name') ?: data_get($center, 'name') ?: 'Sorting Center';
    $stats = $sortingCenterStats ?? [];
    $number = fn($key) => is_numeric(data_get($stats, $key)) ? number_format((int)data_get($stats, $key)) : '0';
    $centerAnnouncements = $sortingCenterAnnouncements ?? [];
    $centerHubs = $sortingCenterDeliveryHubs ?? data_get($center,'hubs',[]);
    $centerHubs = is_iterable($centerHubs) ? collect($centerHubs) : collect();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Sorting Center Dashboard — ShopEase</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
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
</style>
</head>
<body class="lg-body">
@include('components.logistics.sorting-center.sidebar')
@include('components.logistics.sorting-center.navbar')
<main class="lg-main sc-main" id="scMain"><div class="sc-inner">
 <header class="sc-hero"><p class="sc-eyebrow">ShopEase · Sorting Center Operations</p><h1>{{ $centerName }}</h1><p>Monitor parcels, delivery hubs, transfers, and updates at your sorting center.</p></header>
 <div class="sc-top sc-top-reordered">
  <section class="sc-panel sc-tracker" aria-labelledby="scTrackTitle"><h2 id="scTrackTitle">Track a Parcel</h2><p class="sc-clock" id="scLiveClock">Loading Philippine date and time…</p><form class="sc-track-form" id="scTrackForm"><input type="search" id="scTrackQuery" placeholder="Enter parcel tracking number" aria-label="Parcel tracking number" maxlength="80" required><button class="sc-primary" type="submit">Track Parcel</button></form><p id="scTrackResult" role="status" class="sc-track-result">Enter a tracking number. Live lookup will be available when parcel tracking is connected.</p></section>
  <aside class="sc-panel sc-announcements" aria-label="Platform announcements"><h2>Platform Announcement</h2><p class="sc-announce-desc">Updates from ShopEase administration</p><div class="sc-slides">
  @forelse($centerAnnouncements as $i => $announcement)
    <article data-sc-announcement {{ $i > 0 ? 'hidden' : '' }}><small>{{ data_get($announcement,'badge_label','ANNOUNCEMENT') }}</small><h3>{{ data_get($announcement,'title','Update') }}</h3><p>{{ data_get($announcement,'body','') }}</p></article>
  @empty
    <article data-sc-announcement><small>WELCOME · PREVIEW</small><h3>Welcome to Your Sorting Center</h3><p>Monitor your local delivery network, parcel movements, and operational updates here.</p></article>
    <article data-sc-announcement hidden><small>OPERATIONS · PREVIEW</small><h3>Parcel Movement Overview</h3><p>Incoming and departed parcel counts will reflect confirmed shipment records once connected.</p></article>
    <article data-sc-announcement hidden><small>PLATFORM · PREVIEW</small><h3>Announcements Appear Here</h3><p>Published notices from ShopEase administration will appear in this carousel.</p></article>
  @endforelse
  </div><div class="sc-ann-controls"><div class="sc-dots" id="scAnnDots"></div><div class="sc-arrows"><button type="button" id="scAnnPrev" aria-label="Previous announcement">‹</button><button type="button" id="scAnnNext" aria-label="Next announcement">›</button></div></div></aside>

 </div>
 <section class="sc-section" aria-label="Sorting center overview"><h2>At This Sorting Center</h2><div class="sc-grid4">
  <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="m3 7 9-4 9 4v10l-9 4-9-4zM3 7l9 4 9-4m-9 4v10"/></svg></span><div><span class="sc-kpi-label">Parcels at This Hub</span><strong>{{ $number('parcels_at_center') }}</strong></div></article>
  <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M3 21V8l9-5 9 5v13M3 12h18M8 16h3m2 0h3M8 21v-5h8v5"/></svg></span><div><span class="sc-kpi-label">Delivery Hubs</span><strong>{{ $number('delivery_hubs') }}</strong></div></article>
  <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M3 17V7h12v10H3zm12-7h4l3 4v3h-7M5 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm14 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg></span><div><span class="sc-kpi-label">Vehicles</span><strong>{{ $number('vehicles') }}</strong></div></article>
  <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M12 3 2 21h20L12 3zm0 7v5m0 3h.01"/></svg></span><div><span class="sc-kpi-label">Parcel Disputes</span><strong>{{ $number('parcel_disputes') }}</strong></div></article>
 </div></section>
 <section class="sc-section sc-movement" aria-labelledby="scMovementsTitle"><h2 id="scMovementsTitle">Parcels Moving Through This Center</h2><p class="sc-section-sub">Overview of incoming and outgoing parcel transfers.</p><div class="sc-grid2">
  <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M3 12h14m-5-5 5 5-5 5M3 5h6m-6 14h6"/></svg></span><div><span class="sc-kpi-label">Incoming Transfers</span><strong>{{ $number('incoming_transfers') }}</strong></div></article>
  <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M3 12h18m-6-6 6 6-6 6M3 6h6m-6 12h6"/></svg></span><div><span class="sc-kpi-label">Departed Parcels</span><strong>{{ $number('departed_parcels') }}</strong></div></article>
 </div></section>
 <div class="sc-bottom-panels">
  <section class="sc-panel sc-hubs-panel" aria-labelledby="scHubTitle">
   <div class="sc-panel-heading"><div><h2 id="scHubTitle">Delivery Hubs</h2><p>Delivery hubs assigned to this sorting center</p></div><span class="sc-hub-tag">{{ count($centerHubs) }} hubs</span></div>
   <div class="sc-table-toolbar"><label class="sc-hub-search"><span aria-hidden="true">⌕</span><input type="search" id="scHubSearch" placeholder="Search delivery hubs, municipality, or manager…" aria-label="Search delivery hubs"></label><select id="scHubStatus" aria-label="Filter delivery hubs by status"><option value="all">All Statuses</option><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
   <div class="sc-table-scroll"><table class="sc-hub-table"><thead><tr><th>Delivery Hub</th><th>Municipality</th><th>Manager</th><th>Parcels</th><th>Status</th></tr></thead><tbody id="scHubRows">
   @forelse($centerHubs as $hub)
     @php
       $hubName=data_get($hub,'hub_name') ?: data_get($hub,'branch_name') ?: data_get($hub,'name') ?: 'Unnamed Hub';
       $hubCity=data_get($hub,'city') ?: data_get($hub,'municipality') ?: '—';
       $hubManager=data_get($hub,'manager_name') ?: '—';
       $hubParcels=data_get($hub,'parcels_count');
       $hubStatus=strtolower((string)(data_get($hub,'status') ?: 'active'));
     @endphp
     <tr data-sc-hub-status="{{ $hubStatus }}"><td><strong>{{ $hubName }}</strong></td><td>{{ $hubCity }}</td><td>{{ $hubManager }}</td><td>{{ is_numeric($hubParcels) ? number_format((int)$hubParcels) : '—' }}</td><td><span class="sc-status {{ $hubStatus === 'inactive' ? 'inactive' : 'active' }}">{{ ucfirst($hubStatus) }}</span></td></tr>
   @empty
     <tr class="sc-hub-empty"><td colspan="5">No delivery hubs are connected yet. Assigned hubs will appear here when data is provided.</td></tr>
   @endforelse
   </tbody></table></div><div class="sc-table-footer" id="scHubCount">{{ count($centerHubs) }} delivery hub(s)</div>
  </section>
  <section class="sc-panel sc-earnings-panel" aria-labelledby="scEarningsTitle"><div class="sc-panel-heading"><div><h2 id="scEarningsTitle">Network Earnings</h2><p>Sorting center earnings summary</p></div></div>
   <div class="sc-earnings-total"><span>Available Earnings</span><strong>₱ {{ number_format((float) data_get($stats,'earnings',0),2) }}</strong><small>Figures display provided data only</small></div>
   <div class="sc-earnings-row"><span>Parcel Handling Fees</span><strong>{{ is_numeric(data_get($stats,'handling_fees')) ? '₱ '.number_format((float)data_get($stats,'handling_fees'),2) : 'Not connected' }}</strong></div>
   <div class="sc-earnings-row"><span>Transfer Fees</span><strong>{{ is_numeric(data_get($stats,'transfer_fees')) ? '₱ '.number_format((float)data_get($stats,'transfer_fees'),2) : 'Not connected' }}</strong></div>
   <div class="sc-earnings-row"><span>Settlement Status</span><strong>{{ data_get($stats,'settlement_status','Not connected') }}</strong></div>
  </section>
 </div>
 <p class="sc-info">Dashboard counters currently show 0 unless your page receives real values. The sorting center name, activity counts, parcel search, and official announcements can be connected to backend records later.</p>
</div></main>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const byId=id=>document.getElementById(id);
 const clock=()=>{const el=byId('scLiveClock');if(el)el.textContent=new Intl.DateTimeFormat('en-PH',{timeZone:'Asia/Manila',weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'numeric',minute:'2-digit',second:'2-digit',hour12:true}).format(new Date());};clock();setInterval(clock,1000);
 byId('scTrackForm')?.addEventListener('submit',e=>{e.preventDefault();byId('scTrackResult').textContent='Parcel tracking is not connected yet. No shipment record was changed.';});

 const scHubSearch=byId('scHubSearch'),scHubStatus=byId('scHubStatus'),scHubRows=byId('scHubRows');
 const updateHubFilter=()=>{if(!scHubRows)return;const q=(scHubSearch?.value||'').trim().toLowerCase(), status=scHubStatus?.value||'all';let count=0;[...scHubRows.querySelectorAll('tr:not(.sc-hub-empty)')].forEach(row=>{const visible=row.textContent.toLowerCase().includes(q)&&(status==='all'||row.dataset.scHubStatus===status);row.hidden=!visible;if(visible)count++;});const noData=scHubRows.querySelector('.sc-hub-empty');if(noData)noData.hidden=!!q||status!=='all';const footer=byId('scHubCount');if(footer)footer.textContent=count+' matching delivery hub(s)';};
 scHubSearch?.addEventListener('input',updateHubFilter);scHubStatus?.addEventListener('change',updateHubFilter);
 const slides=[...document.querySelectorAll('[data-sc-announcement]')],dots=byId('scAnnDots');let index=0,timer;
 const show=n=>{if(!slides.length)return;index=(n+slides.length)%slides.length;slides.forEach((el,i)=>el.hidden=i!==index);[...dots.children].forEach((dot,i)=>{dot.classList.toggle('active',i===index);dot.setAttribute('aria-current',String(i===index));});};
 slides.forEach((_,i)=>{const dot=document.createElement('button');dot.type='button';dot.setAttribute('aria-label',`Show announcement ${i+1}`);dot.addEventListener('click',()=>{show(i);restart();});dots.append(dot);});
 const restart=()=>{clearInterval(timer);if(slides.length>1&&!document.hidden&&!matchMedia('(prefers-reduced-motion: reduce)').matches)timer=setInterval(()=>show(index+1),6500);};
 byId('scAnnPrev')?.addEventListener('click',()=>{show(index-1);restart();});byId('scAnnNext')?.addEventListener('click',()=>{show(index+1);restart();});
 if(slides.length<=1){const controls=document.querySelector('.sc-ann-controls');if(controls)controls.hidden=true;}
 document.addEventListener('visibilitychange',restart);show(0);restart();
});
</script>
</body></html>