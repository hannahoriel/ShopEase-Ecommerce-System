{{-- Sorting Center Dashboard — frontend-ready; inject $sortingCenter and $sortingCenterStats from controller later. --}}
@php
    $center = $center ?? $sortingCenter ?? null;
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
    <article data-sc-announcement><small>UPDATES</small><h3>Loading announcements</h3><p>Checking for published ShopEase updates.</p></article>
  </div><div class="sc-ann-controls"><div class="sc-dots" id="scAnnDots"></div><div class="sc-arrows"><button type="button" id="scAnnPrev" aria-label="Previous announcement">‹</button><button type="button" id="scAnnNext" aria-label="Next announcement">›</button></div></div></aside>

 </div>
 <section class="sc-section" aria-label="Sorting center overview"><h2>At This Sorting Center</h2><div class="sc-grid4">
 <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="m3 7 9-4 9 4v10l-9 4-9-4zM3 7l9 4 9-4m-9 4v10"/></svg></span><div><span class="sc-kpi-label">Parcels at This Hub</span><strong id="scStatParcels">0</strong></div></article>
 <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M3 21V8l9-5 9 5v13M3 12h18M8 16h3m2 0h3M8 21v-5h8v5"/></svg></span><div><span class="sc-kpi-label">Delivery Hubs</span><strong id="scStatHubs">0</strong></div></article>
 <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M3 17V7h12v10H3zm12-7h4l3 4v3h-7M5 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm14 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg></span><div><span class="sc-kpi-label">Vehicles</span><strong id="scStatVehicles">0</strong></div></article>
 <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M12 3 2 21h20L12 3zm0 7v5m0 3h.01"/></svg></span><div><span class="sc-kpi-label">Parcel Disputes</span><strong id="scStatDisputes">0</strong></div></article>
 </div></section>
 <section class="sc-section sc-movement" aria-labelledby="scMovementsTitle"><h2 id="scMovementsTitle">Parcels Moving Through This Center</h2><p class="sc-section-sub">Overview of incoming and outgoing parcel transfers.</p><div class="sc-grid2">
  <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M3 12h14m-5-5 5 5-5 5M3 5h6m-6 14h6"/></svg></span><div><span class="sc-kpi-label">Incoming Transfers</span><strong id="scStatIncoming">0</strong></div></article>
  <article class="sc-kpi"><span class="sc-kpi-icon"><svg viewBox="0 0 24 24"><path d="M3 12h18m-6-6 6 6-6 6M3 6h6m-6 12h6"/></svg></span><div><span class="sc-kpi-label">Departed Parcels</span><strong id="scStatDeparted">0</strong></div></article>
 </div></section>
 <div class="sc-bottom-panels">
  <section class="sc-panel sc-hubs-panel" aria-labelledby="scHubTitle">
   <div class="sc-panel-heading"><div><h2 id="scHubTitle">Delivery Hubs</h2><p>Delivery hubs assigned to this sorting center</p></div><span class="sc-hub-tag" id="scHubTag">0 hubs</span></div>
   <div class="sc-table-toolbar"><label class="sc-hub-search"><span aria-hidden="true">⌕</span><input type="search" id="scHubSearch" placeholder="Search delivery hubs, municipality, or manager…" aria-label="Search delivery hubs"></label><select id="scHubStatus" aria-label="Filter delivery hubs by status"><option value="all">All Statuses</option><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
   <div class="sc-table-scroll"><table class="sc-hub-table"><thead><tr><th>Delivery Hub</th><th>Municipality</th><th>Manager</th><th>Parcels</th><th>Status</th></tr></thead><tbody id="scHubRows"><tr class="sc-hub-empty"><td colspan="5">Loading assigned delivery hubs…</td></tr></tbody></table></div><div class="sc-table-footer" id="scHubCount">Loading delivery hubs…</div>
  </section>
   <section class="sc-panel sc-earnings-panel" aria-labelledby="scOperationsTitle"><div class="sc-panel-heading"><div><h2 id="scOperationsTitle">Center Operations</h2><p>Parcel handling and assigned facilities</p></div></div>
    <div class="sc-earnings-total"><span>Assigned Delivery Hubs</span><strong id="scOpsHubs">0</strong><small>Facilities operating under this sorting center</small></div>
    <div class="sc-earnings-row"><span>Parcels at This Center</span><strong id="scOpsParcels">0</strong></div>
    <div class="sc-earnings-row"><span>Parcel Disputes</span><strong id="scOpsDisputes">0</strong></div>
    <div class="sc-earnings-row"><span>Financial reporting</span><strong>Managed by Logistics Company</strong></div>
   </section>
 </div>
 <p class="sc-info" id="scDashboardStatus">Loading sorting center operations from database…</p>
</div></main>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const byId=id=>document.getElementById(id);
 const clock=()=>{const el=byId('scLiveClock');if(el)el.textContent=new Intl.DateTimeFormat('en-PH',{timeZone:'Asia/Manila',weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'numeric',minute:'2-digit',second:'2-digit',hour12:true}).format(new Date());};clock();setInterval(clock,1000);
 const scHubSearch=byId('scHubSearch'),scHubStatus=byId('scHubStatus'),scHubRows=byId('scHubRows');
 const updateHubFilter=()=>{if(!scHubRows)return;const q=(scHubSearch?.value||'').trim().toLowerCase(), status=scHubStatus?.value||'all';let count=0;[...scHubRows.querySelectorAll('tr:not(.sc-hub-empty)')].forEach(row=>{const visible=row.textContent.toLowerCase().includes(q)&&(status==='all'||row.dataset.scHubStatus===status);row.hidden=!visible;if(visible)count++;});const noData=scHubRows.querySelector('.sc-hub-empty');if(noData)noData.hidden=!!q||status!=='all';const footer=byId('scHubCount');if(footer)footer.textContent=count+' matching delivery hub(s)';};
 scHubSearch?.addEventListener('input',updateHubFilter);scHubStatus?.addEventListener('change',updateHubFilter);
 let slides=[...document.querySelectorAll('[data-sc-announcement]')],dots=byId('scAnnDots'),index=0,timer;
 const show=n=>{if(!slides.length)return;index=(n+slides.length)%slides.length;slides.forEach((el,i)=>el.hidden=i!==index);[...dots.children].forEach((dot,i)=>{dot.classList.toggle('active',i===index);dot.setAttribute('aria-current',String(i===index));});};
 const restart=()=>{clearInterval(timer);if(slides.length>1&&!document.hidden&&!matchMedia('(prefers-reduced-motion: reduce)').matches)timer=setInterval(()=>show(index+1),6500);};
 byId('scAnnPrev')?.addEventListener('click',()=>{show(index-1);restart();});byId('scAnnNext')?.addEventListener('click',()=>{show(index+1);restart();});
 const renderAnnouncements=items=>{const container=document.querySelector('.sc-slides');container.replaceChildren();dots.replaceChildren();(items.length?items:[{badge_label:'UPDATES',title:'No current announcements',body:'There are no published updates for sorting centers.'}]).forEach((item,i)=>{const article=document.createElement('article'),badge=document.createElement('small'),title=document.createElement('h3'),body=document.createElement('p');article.dataset.scAnnouncement='';article.hidden=i>0;badge.textContent=item.badge_label||'ANNOUNCEMENT';title.textContent=item.title||'Update';body.textContent=item.body||'';article.append(badge,title,body);container.append(article);const dot=document.createElement('button');dot.type='button';dot.setAttribute('aria-label',`Show announcement ${i+1}`);dot.addEventListener('click',()=>{show(i);restart();});dots.append(dot);});slides=[...container.children];index=0;const controls=document.querySelector('.sc-ann-controls');if(controls)controls.hidden=slides.length<=1;show(0);restart();};
 document.addEventListener('visibilitychange',restart);
 const esc=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
 const loadDashboard=async()=>{try{const response=await fetch(`{{ route('sorting-center.dashboard.data') }}`,{headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});if(!response.ok)throw new Error('Could not load sorting center dashboard data.');const {data}=await response.json();const stats=data.stats||{};const num=n=>Number(n||0).toLocaleString('en-PH');[['scStatParcels','parcels_at_center'],['scStatHubs','delivery_hubs'],['scStatVehicles','vehicles'],['scStatDisputes','parcel_disputes'],['scStatIncoming','incoming_transfers'],['scStatDeparted','departed_parcels'],['scOpsHubs','delivery_hubs'],['scOpsParcels','parcels_at_center'],['scOpsDisputes','parcel_disputes']].forEach(([id,key])=>{byId(id).textContent=num(stats[key]);});const hubs=data.hubs||[];byId('scHubTag').textContent=`${num(hubs.length)} hubs`;scHubRows.innerHTML=hubs.length?hubs.map(h=>{const status=String(h.status||'active').toLowerCase();return `<tr data-sc-hub-status="${esc(status)}"><td><strong>${esc(h.name)}</strong></td><td>${esc(h.municipality||'—')}</td><td>${esc(h.manager||'—')}</td><td>${num(h.parcels)}</td><td><span class="sc-status ${status==='inactive'?'inactive':'active'}">${esc(status[0].toUpperCase()+status.slice(1))}</span></td></tr>`;}).join(''):'<tr class="sc-hub-empty"><td colspan="5">No delivery hubs are assigned to this sorting center.</td></tr>';updateHubFilter();renderAnnouncements(data.announcements||[]);byId('scDashboardStatus').textContent='Dashboard activity is loaded from this sorting center’s shipments, transfers, fleet, and published announcements.';}catch(error){byId('scDashboardStatus').textContent=error.message;}};
 loadDashboard();
 byId('scTrackForm')?.addEventListener('submit',async e=>{e.preventDefault();const input=byId('scTrackQuery'),result=byId('scTrackResult');result.textContent='Looking up parcel…';try{const url=`{{ route('sorting-center.dashboard.data') }}?tracking=${encodeURIComponent(input.value.trim())}`;const response=await fetch(url,{headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});const body=await response.json();if(!response.ok)throw new Error(body.message||'Parcel not found at this sorting center.');const parcel=body.data;result.textContent=`${parcel.tracking} · ${parcel.status}${parcel.location?' · '+parcel.location:''}${parcel.updated_at?' · Updated '+parcel.updated_at:''}`;}catch(error){result.textContent=error.message;}});
 show(0);restart();
});
</script>
</body></html>
