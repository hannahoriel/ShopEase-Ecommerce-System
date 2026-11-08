@php
    $reportCenter = $center ?? $sortingCenter ?? null;
    $todayManila = \Carbon\Carbon::today('Asia/Manila');
    $sortingCenterReportPayload = [
        'records' => [],
        'hubs' => [],
        'couriers' => [],
        'center' => data_get($reportCenter, 'name') ?: 'Sorting Center',
        'today' => $todayManila->toDateString(),
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Sorting Center Reports — ShopEase</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* Content-only styles; original Sorting Center sidebar and navbar remain the single source of layout styles. */
.sr-page,.sr-page *{box-sizing:border-box}.sr-page{color:#172236;font-family:Poppins,Arial,sans-serif}.sr-shell{max-width:1680px;margin:0 auto}
.sr-period{background:#fff;border:1px solid #e7e9ee;border-radius:15px;padding:17px 20px;display:grid;grid-template-columns:minmax(230px,1fr) auto 40px;gap:16px;align-items:center;margin-bottom:25px;box-shadow:0 2px 10px #39405706}
.sr-period-heading{display:flex;align-items:center;gap:12px}.sr-calendar{width:42px;height:42px;border-radius:11px;background:#faf0f3;color:#a21c41;display:grid;place-items:center;flex-shrink:0}.sr-calendar svg{width:21px;height:21px}.sr-period-heading strong{display:block;font-size:13px;font-weight:600}.sr-period-heading small{display:block;font-size:11px;color:#9ca3af;margin-top:3px}
.sr-dates{display:flex;align-items:center;gap:12px}.sr-dates label{font-size:11px;font-weight:600;color:#858996;display:flex;align-items:center;gap:9px}.sr-dates input{min-width:154px;height:41px;border:1px solid #dce0e7;border-radius:9px;background:#fff;padding:0 11px;color:#263449;font:400 12px Poppins,sans-serif;outline:0}.sr-dates input:focus{border-color:#9ca3af;box-shadow:0 0 0 3px #a9adb51b}.sr-dash{color:#b4b5ba}
.sr-reset{border:0;background:transparent;color:#303039;border-radius:9px;width:40px;height:40px;display:grid;place-items:center;cursor:pointer}.sr-reset:hover{background:#fff0ec}.sr-reset svg{width:19px;height:19px}.sr-reset.spinning svg{animation:sr-spin .65s ease both}@keyframes sr-spin{to{transform:rotate(360deg)}}
.sr-error{color:#a52b46;font-size:11px;margin:-15px 0 15px}.sr-error:empty{display:none}.sr-caption{font-size:11px;letter-spacing:1.35px;font-weight:700;color:#a21c41;margin-bottom:12px}.sr-caption span{letter-spacing:normal;color:#9ba1ac;font-weight:400;margin-left:10px}
.sr-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:18px}.sr-kpi,.sr-card{min-width:0;background:white;border:1px solid #e7e9ee;border-radius:15px;box-shadow:0 2px 10px #39405705}.sr-kpi{padding:20px 19px}.sr-kpi-head{display:flex;align-items:center;justify-content:space-between;gap:8px;color:#7a8391;font-size:12px;font-weight:600}.sr-kpi-icon{display:block;width:30px;height:30px;flex:0 0 30px;background:none;border:0;border-radius:0;color:#a21c41}.sr-kpi strong{font-size:clamp(22px,2vw,27px);color:#172236;display:block;margin:14px 0 5px;font-weight:700;line-height:1.2}.sr-kpi small{font-size:10px;line-height:1.65;color:#9ca3af;display:block}
.sr-two{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(0,1fr);gap:18px;margin-bottom:18px}.sr-balanced{grid-template-columns:minmax(0,1fr) minmax(0,1fr)}.sr-three{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-bottom:18px}.sr-card{padding:21px 21px 23px}.sr-card-header{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:20px}.sr-card-header h2{font-size:16px;font-weight:650;margin:0 0 5px;color:#1c293d}.sr-card-header p{font-size:11px;color:#9ba1ad;line-height:1.5;margin:0}.sr-pill{background:#faf0f3;color:#9a5065;border-radius:99px;padding:7px 11px;font-size:10px;font-weight:600;white-space:nowrap}
.sr-chart{position:relative;height:355px;min-height:355px}.sr-chart svg{display:block;width:100%;height:100%;overflow:visible}.sr-chart .sr-bar{cursor:pointer;transition:fill .16s}.sr-chart .sr-bar:hover,.sr-chart .sr-bar:focus{fill:#7e1834;outline:none}.sr-chart-tip{position:absolute;z-index:2;background:#262334;color:white;border-radius:9px;padding:9px 12px;white-space:nowrap;pointer-events:none;transform:translate(-50%,-100%);box-shadow:0 8px 24px #1618232e;font-size:11px}.sr-chart-tip strong{display:block;font-size:13px}.sr-chart-tip[hidden]{display:none!important}
.sr-meter-row{margin-bottom:19px}.sr-meter-row>div:first-child{display:flex;justify-content:space-between;gap:12px;font-size:12px;color:#788391;margin-bottom:8px}.sr-meter-row strong{color:#253248;font-weight:600}.sr-meter{height:8px;background:#f1f2f4;border-radius:99px;overflow:hidden}.sr-meter i{height:100%;background:#aa3156;border-radius:inherit;display:block;transition:width .35s}
.sr-table-wrap{overflow:auto}.sr-table{width:100%;border-collapse:collapse;min-width:530px}.sr-table th{padding:12px 10px;border-bottom:1px solid #e9e9ed;font-size:11px;color:#98a0ac;text-align:left;font-weight:600;white-space:nowrap}.sr-table td{padding:13px 10px;border-bottom:1px solid #f0f0f3;font-size:11px;color:#677384}.sr-table tbody tr:last-child td{border-bottom:none}.sr-table tbody tr:hover td{background:#fdf9f9}.sr-table td strong{font-weight:600;color:#263046}.sr-table .number{text-align:right;white-space:nowrap}.sr-empty{color:#9ba1ad;font-size:12px;text-align:center;padding:28px 10px!important}
.sr-hub{display:flex;justify-content:space-between;align-items:center;gap:15px;padding:15px 14px;border:1px solid #ece4e5;border-radius:11px;background:#fcf9fa;margin-bottom:10px}.sr-hub strong{display:block;color:#233049;font-size:12px;font-weight:600}.sr-hub small{display:block;color:#9ba1ab;font-size:10px;margin-top:4px}.sr-hub-metric{text-align:right;flex-shrink:0;color:#8b2540;font-size:14px;font-weight:600}.sr-hub-metric small{font-weight:400}.sr-info-row{display:flex;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid #eff0f2;padding:12px 0;font-size:12px;color:#78828e}.sr-info-row:last-child{border-bottom:none}.sr-info-row strong{color:#29364a;font-size:12px;font-weight:600}.sr-small-note{font-size:11px;color:#9ca3af;line-height:1.75;margin:13px 0 0}.sr-dot{display:inline-block;width:8px;height:8px;background:#b34260;border-radius:50%;margin-right:6px}.sr-tag{display:inline-flex;border-radius:99px;background:#f8eef1;color:#96354e;font-size:10px;padding:5px 9px;font-weight:600}.sr-tag.green{background:#eaf6ef;color:#28835b}.sr-tag.amber{background:#fff2e5;color:#ad6b2b}
.sr-toolbar{display:flex;gap:11px;align-items:end;flex-wrap:wrap;margin:0 0 14px}.sr-control-group{display:grid;gap:6px;flex:1 1 230px}.sr-control-group.filter{flex:0 1 220px}.sr-control-group label{font-size:11px;color:#88919e;font-weight:600}.sr-control{height:40px;min-width:0;width:100%;border:1px solid #dce0e7;border-radius:9px;background:white;color:#253148;padding:0 13px;font:400 12px Poppins,sans-serif;outline:none}.sr-control:focus{border-color:#a7acb6;box-shadow:0 0 0 3px #a7acb625}.sr-pager{display:flex;align-items:center;justify-content:flex-end;gap:6px;flex-wrap:wrap}.sr-pager button{height:30px;min-width:30px;padding:0 8px;border:1px solid #e5dadd;border-radius:7px;background:white;color:#6b6265;cursor:pointer;font-size:11px}.sr-pager button.active{background:#811d32;border-color:#811d32;color:white}.sr-pager button:disabled{opacity:.4;cursor:default}.sr-table-footer{display:flex;justify-content:space-between;align-items:center;gap:12px;padding-top:17px;font-size:11px;color:#9ba1ad}.sr-footnote{font-size:10px;color:#a4aab4;line-height:1.7;margin:8px 2px 0}
@media(max-width:1250px){.sr-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}.sr-two,.sr-balanced{grid-template-columns:1fr}.sr-three{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:960px){.sr-period{grid-template-columns:1fr 42px}.sr-period-heading{grid-column:1/-1}.sr-dates{grid-column:1;flex-wrap:wrap}.sr-reset{grid-column:2;grid-row:2;justify-self:end}}@media(max-width:670px){.sr-page{padding-left:0!important;padding-right:0!important}.sr-kpis,.sr-three{grid-template-columns:1fr 1fr;gap:10px}.sr-kpi{padding:15px 13px}.sr-kpi strong{font-size:20px}.sr-card{padding:17px 14px}.sr-dates label{flex-direction:column;align-items:flex-start;flex:1;gap:5px}.sr-dates input{min-width:0;width:100%}.sr-dash{display:none}.sr-chart{height:300px;min-height:300px}.sr-card-header h2{font-size:14px}.sr-toolbar .sr-control-group.filter{flex:1 1 100%}.sr-table-footer{flex-wrap:wrap}.sr-pager{margin-left:auto}}
@media(prefers-reduced-motion:reduce){.sr-reset.spinning svg{animation-duration:.01ms}}

/* Courier performance — same Admin/Logistics Reports table treatment. */
.sr-courier-overview{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:0 0 16px}
.sr-courier-overview>div{border:1px solid #e9e9ee;background:#fcfafb;border-radius:11px;padding:13px 15px;min-width:0}
.sr-courier-overview span{font-size:11px;color:#89929d;display:block;margin-bottom:5px}
.sr-courier-overview strong{font-size:21px;color:#1f2d41;font-weight:650;display:block}
.sr-courier-help{font-size:10px;color:#949caa;line-height:1.7;margin:12px 0 0}
@media(max-width:670px){.sr-courier-overview{grid-template-columns:1fr 1fr}.sr-courier-overview>div:last-child{grid-column:1/-1}}
</style>
</head>
<body class="lg-body">
@include('components.logistics.sorting-center.sidebar')
@include('components.logistics.sorting-center.navbar')
<main class="lg-main sr-page"><div class="sr-shell" id="sorting-center-reports">
  <section class="sr-period" aria-label="Reports reporting period">
    <div class="sr-period-heading"><span class="sr-calendar" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/></svg></span><span><strong>Reporting period</strong><small>Select a date range to update all report sections</small></span></div>
    <div class="sr-dates"><label for="sr-from">From <input id="sr-from" type="date" max="{{ $todayManila->toDateString() }}"></label><span class="sr-dash">—</span><label for="sr-to">To <input id="sr-to" type="date" max="{{ $todayManila->toDateString() }}"></label></div>
    <button class="sr-reset" id="sr-reset" type="button" aria-label="Reset reporting period" title="Reset to current month"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7v5h-5"/><path d="M4 17v-5h5"/><path d="M5.8 9A7.5 7.5 0 0 1 19.5 7.5L20 12M4 12l.5 4.5A7.5 7.5 0 0 0 18.2 15"/></svg></button>
  </section>
  <p id="sr-error" class="sr-error" aria-live="polite"></p>
  <div class="sr-caption">SORTING CENTER PERFORMANCE <span id="sr-period-name">Current month</span></div>
  <section class="sr-kpis">
    <article class="sr-kpi"><div class="sr-kpi-head"><span>Parcels handled</span><svg class="sr-kpi-icon" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 7 9-4 9 4v10l-9 4-9-4ZM3 7l9 4 9-4M12 11v10M7.5 5l9 4"/></svg></div><strong id="sr-handled">—</strong><small>Unique parcels recorded at this sorting center</small></article>
    <article class="sr-kpi"><div class="sr-kpi-head"><span>Received scans</span><svg class="sr-kpi-icon" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m-5-5 5 5 5-5M4 16v5h16v-5"/></svg></div><strong id="sr-received">—</strong><small>Parcels scanned and received at this center</small></article>
    <article class="sr-kpi"><div class="sr-kpi-head"><span>Outbound transfers</span><svg class="sr-kpi-icon" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12h18m-6-6 6 6-6 6"/></svg></div><strong id="sr-outbound">—</strong><small>To assigned delivery hubs or next sorting centers</small></article>
    <article class="sr-kpi"><div class="sr-kpi-head"><span>Parcel disputes</span><svg class="sr-kpi-icon" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 2 21h20L12 3Zm0 7v5m0 3h.01"/></svg></div><strong id="sr-disputes">—</strong><small>Lost and damaged cases reported here</small></article>
  </section>
  <div class="sr-two">
    <section class="sr-card"><div class="sr-card-header"><div><h2>Parcel volume trend</h2><p>Daily parcels handled by this sorting center</p></div><span class="sr-pill">Parcels</span></div><div class="sr-chart" id="sr-volume-chart"></div></section>
    <section class="sr-card"><div class="sr-card-header"><div><h2>Parcel movement</h2><p>Operational status of parcels recorded at this center</p></div><span class="sr-pill" id="sr-count-pill">— records</span></div><div id="sr-status-bars"></div><p class="sr-small-note">Each parcel movement is based on its recorded transfer and scan activity.</p></section>
  </div>
  <div class="sr-two sr-balanced">
    <section class="sr-card"><div class="sr-card-header"><div><h2>Assigned delivery hubs</h2><p>Only delivery hubs under this sorting center</p></div><span class="sr-pill" id="sr-hubs-pill">Assigned hubs</span></div><div id="sr-hubs"></div><p class="sr-small-note">These are subordinate delivery hubs; other sorting centers are not included in this list.</p></section>
    <section class="sr-card"><div class="sr-card-header"><div><h2>Outbound destination summary</h2><p>Where parcels leaving this center are sent</p></div><span class="sr-pill">Transfers</span></div><div class="sr-table-wrap"><table class="sr-table"><thead><tr><th>Destination</th><th>Destination type</th><th class="number">Parcels</th></tr></thead><tbody id="sr-destinations"></tbody></table></div></section>
  </div>
  <div class="sr-three">
    <section class="sr-card"><div class="sr-card-header"><div><h2>Scanner activity</h2><p>Received and departure scan counts</p></div><span class="sr-pill">Scanners</span></div><div id="sr-scanners"></div></section>
    <section class="sr-card"><div class="sr-card-header"><div><h2>Parcel disputes</h2><p>Lost and damaged cases reported here</p></div><span class="sr-pill">Cases</span></div><div id="sr-dispute-details"></div></section>
    <section class="sr-card"><div class="sr-card-header"><div><h2>Batch processing</h2><p>Grouped transfer activity for this center</p></div><span class="sr-pill">Batches</span></div><div id="sr-batches"></div></section>
  </div>

  <section class="sr-card" style="margin-bottom:18px" aria-label="Courier operations">
    <div class="sr-card-header"><div><h2>Courier transfer performance</h2><p>Outbound parcels assigned to couriers from this sorting center, including transfers to other sorting centers and assigned delivery hubs</p></div><span class="sr-pill">Center couriers only</span></div>
    <div class="sr-courier-overview">
      <div><span>Couriers with outbound assignments</span><strong id="sr-courier-active">0</strong></div>
      <div><span>Assigned outbound parcels</span><strong id="sr-courier-parcels">0</strong></div>
      <div><span>Without courier assignment</span><strong id="sr-courier-unassigned">0</strong></div>
    </div>
    <div class="sr-table-wrap"><table class="sr-table"><thead><tr><th>Courier</th><th class="number">To Delivery Hubs</th><th class="number">To Sorting Centers</th><th class="number">Total Outbound</th><th>Latest Transfer</th></tr></thead><tbody id="sr-courier-rows"></tbody></table></div>
    <p class="sr-courier-help">Outbound counts describe parcels assigned to couriers, not confirmed deliveries or driver earnings. Rider performance is managed by the destination Delivery Hub. Figures follow the reporting period above.</p>
  </section>
  <section class="sr-card"><div class="sr-card-header"><div><h2>Sorting center parcel records</h2><p>Search parcels handled at this center, including onward center-to-center transfers</p></div><span class="sr-pill" id="sr-record-count">— records</span></div>
    <div class="sr-toolbar"><div class="sr-control-group"><label for="sr-search">Search parcels</label><input class="sr-control" type="search" id="sr-search" placeholder="Search tracking number, batch, or destination" autocomplete="off" value=""></div><div class="sr-control-group filter"><label for="sr-status-filter">Parcel status</label><select class="sr-control" id="sr-status-filter"><option value="all">All statuses</option><option value="incoming">Incoming transfer</option><option value="received">Received</option><option value="hub_dispatch">Departed to delivery hub</option><option value="center_transfer">Departed to sorting center</option><option value="dispute">Dispute reported</option></select></div></div>
    <div class="sr-table-wrap"><table class="sr-table"><thead><tr><th>Date &amp; time</th><th>Parcel reference</th><th>Batch</th><th>Destination</th><th>Type</th><th>Status</th></tr></thead><tbody id="sr-record-rows"></tbody></table></div>
    <div class="sr-table-footer"><span id="sr-record-summary">Showing 0 records</span><div class="sr-pager" id="sr-pager" aria-label="Parcel record pagination"></div></div>
  </section>
  <p class="sr-footnote">Figures are based on parcel scans and transfer manifests assigned to this sorting center.</p>
</div></main>
<script type="application/json" id="sr-report-data">@json($sortingCenterReportPayload)</script>
<script>
(() => {
 'use strict';
 document.addEventListener('DOMContentLoaded', () => {
  const root=document.getElementById('sorting-center-reports'), source=document.getElementById('sr-report-data');
  if(!root||!source)return;
  let payload;try{payload=JSON.parse(source.textContent)}catch(error){console.error('Sorting center report data is invalid',error);return;}
  let records=Array.isArray(payload.records)?payload.records:[];
  let hubs=Array.isArray(payload.hubs)?payload.hubs:[];
  let couriers=Array.isArray(payload.couriers)?payload.couriers:[];
  const $=id=>document.getElementById(id);
  const esc=v=>String(v??'').replace(/[&<>"']/g,ch=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
  const fmt=n=>Number(n||0).toLocaleString('en-PH');
  const labelDate=iso=>new Date(iso+'T12:00:00').toLocaleDateString('en-PH',{month:'short',day:'numeric'});
  const names={incoming:'Incoming transfer',received:'Received at center',hub_dispatch:'Departed to delivery hub',center_transfer:'Departed to next sorting center',dispute:'Dispute reported'};
  const statusKeys=Object.keys(names);
  const from=$('sr-from'), to=$('sr-to'), reset=$('sr-reset'), error=$('sr-error');
  const search=$('sr-search'), status=$('sr-status-filter');
  const pageSize=10;let page=1;let selected=[];let filtered=[];let animationToken=0;let requestToken=0;
  const outgoing=r=>r.status==='hub_dispatch'||r.status==='center_transfer';
  const count=(rows,key)=>rows.filter(r=>r.status===key).length;
  const ratio=(a,b)=>b?Math.round(a/b*1000)/10:0;
  function rowHtml(label,value){return `<div class="sr-info-row"><span>${esc(label)}</span><strong>${esc(value)}</strong></div>`;}
  function currentMonth(){from.value=payload.today.slice(0,7)+'-01';to.value=payload.today;page=1;search.value='';status.value='all';updateAll();}
  async function updateAll(){
    if(!from.value||!to.value||from.value>to.value){error.textContent='Select a valid From and To date.';return;}
    error.textContent='';
    const token=++requestToken;
    try {
    const response=await fetch(`{{ route('sorting-center.reports.data') }}?from=${encodeURIComponent(from.value)}&to=${encodeURIComponent(to.value)}`,{headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});
    if(!response.ok)throw new Error('Could not load sorting center reports. Please try again.');
    const result=await response.json();if(token!==requestToken)return;
    const data=result.data||{};records=Array.isArray(data.records)?data.records:[];hubs=Array.isArray(data.hubs)?data.hubs:[];couriers=Array.isArray(data.couriers)?data.couriers:[];
    selected=records.filter(r=>r.date>=from.value&&r.date<=to.value);
    $('sr-period-name').textContent=`${labelDate(from.value)} – ${labelDate(to.value)} ${to.value.slice(0,4)}`;
    $('sr-handled').textContent=fmt(new Set(selected.map(x=>x.ref)).size);
    $('sr-received').textContent=fmt(count(selected,'received'));
    $('sr-outbound').textContent=fmt(selected.filter(outgoing).length);
    $('sr-disputes').textContent=fmt(count(selected,'dispute'));
    $('sr-count-pill').textContent=fmt(selected.length)+' records';
    $('sr-hubs-pill').textContent=fmt(hubs.length)+' assigned hubs';
    const bars=$('sr-status-bars');bars.innerHTML=statusKeys.map(k=>{
     const c=count(selected,k);return `<div class="sr-meter-row"><div><span>${esc(names[k])}</span><strong>${fmt(c)}</strong></div><div class="sr-meter"><i style="width:${ratio(c,selected.length)}%"></i></div></div>`;
    }).join('');
    renderHubs();renderDestinations();renderOperations();renderCouriers();drawChart();page=1;renderTable();
    } catch (failure) { if(token===requestToken)error.textContent=failure.message; }
  }
  function renderHubs(){
    $('sr-hubs').innerHTML=hubs.map(h=>{
      const own=selected.filter(r=>r.status==='hub_dispatch'&&r.destination===h);
      return `<div class="sr-hub"><div><strong>${esc(h)}</strong><small>Assigned Delivery Hub</small></div><div class="sr-hub-metric">${fmt(own.length)}<small>outbound parcels</small></div></div>`;
    }).join('')||'<p class="sr-small-note">No delivery hubs are assigned to this center.</p>';
  }
  function renderDestinations(){
    const grouped=new Map();
    selected.filter(outgoing).forEach(r=>{const key=r.destination_type+'|'+r.destination;grouped.set(key,(grouped.get(key)||0)+1)});
    const items=[...grouped].sort((a,b)=>b[1]-a[1]);
    $('sr-destinations').innerHTML=items.length?items.map(([key,n])=>{
      const [type,...parts]=key.split('|');return `<tr><td><strong>${esc(parts.join('|'))}</strong></td><td><span class="sr-tag ${type==='Delivery Hub'?'green':'amber'}">${esc(type)}</span></td><td class="number">${fmt(n)}</td></tr>`;
    }).join(''):'<tr><td class="sr-empty" colspan="3">No outbound transfers for this period.</td></tr>';
  }
  function renderOperations(){
    const received=count(selected,'received'),toHub=count(selected,'hub_dispatch'),toCenter=count(selected,'center_transfer');
    $('sr-scanners').innerHTML=rowHtml('Received scanner',fmt(received))+rowHtml('Departed to delivery hubs',fmt(toHub))+rowHtml('Departed to sorting centers',fmt(toCenter));
    const issues=selected.filter(r=>r.status==='dispute');
    $('sr-dispute-details').innerHTML=rowHtml('Lost parcels',fmt(issues.filter(r=>r.issue==='Lost').length))+rowHtml('Damaged parcels',fmt(issues.filter(r=>r.issue==='Damaged').length))+rowHtml('Dispute share',ratio(issues.length,selected.length).toFixed(1)+'%');
    const batchSet=new Set(selected.map(r=>r.batch)),outboundBatches=new Set(selected.filter(outgoing).map(r=>r.batch));
    $('sr-batches').innerHTML=rowHtml('Batches with parcel records',fmt(batchSet.size))+rowHtml('Batches with outbound parcels',fmt(outboundBatches.size))+rowHtml('Outbound parcels',fmt(toHub+toCenter));
  }

  function renderCouriers(){
    const outbound=selected.filter(outgoing);
    const mapped=new Map(couriers.map(name=>[String(name),{name:String(name),hub:0,center:0,last:''}]));
    for(const r of outbound){
      const name=String(r.courier||'').trim();
      if(!name)continue;
      if(!mapped.has(name))mapped.set(name,{name,hub:0,center:0,last:''});
      const row=mapped.get(name);
      if(r.status==='hub_dispatch')row.hub++;
      if(r.status==='center_transfer')row.center++;
      const timestamp=String(r.date||'')+' '+String(r.time||'');
      if(timestamp>row.last)row.last=timestamp;
    }
    const rows=[...mapped.values()].sort((a,b)=>(b.hub+b.center)-(a.hub+a.center)||a.name.localeCompare(b.name));
    const assigned=outbound.filter(r=>String(r.courier||'').trim()).length;
    $('sr-courier-active').textContent=fmt(rows.filter(r=>r.hub+r.center>0).length);
    $('sr-courier-parcels').textContent=fmt(assigned);
    $('sr-courier-unassigned').textContent=fmt(outbound.length-assigned);
    $('sr-courier-rows').innerHTML=rows.length?rows.map(r=>`<tr><td><strong>${esc(r.name)}</strong></td><td class="number">${fmt(r.hub)}</td><td class="number">${fmt(r.center)}</td><td class="number"><strong>${fmt(r.hub+r.center)}</strong></td><td>${r.last?esc(r.last):'No outbound transfer'}</td></tr>`).join(''):'<tr><td colspan="5" class="sr-empty">No couriers assigned to this sorting center.</td></tr>';
  }
  function renderTable(){
    const query=search.value.trim().toLowerCase();const filter=status.value;
    filtered=selected.filter(r=>(filter==='all'||r.status===filter)&&(!query||[r.ref,r.destination,r.batch,r.destination_type,r.status,r.courier].join(' ').toLowerCase().includes(query))).sort((a,b)=>b.date.localeCompare(a.date)||b.time.localeCompare(a.time));
    const maxPages=Math.max(1,Math.ceil(filtered.length/pageSize));page=Math.max(1,Math.min(page,maxPages));
    const show=filtered.slice((page-1)*pageSize,page*pageSize);
    $('sr-record-rows').innerHTML=show.length?show.map(r=>`<tr><td>${esc(r.date)}<br><span style="color:#9ba1ad">${esc(r.time)}</span></td><td><strong>${esc(r.ref)}</strong></td><td>${esc(r.batch)}</td><td>${esc(r.destination)}</td><td>${esc(r.destination_type)}</td><td><span class="sr-tag ${r.status==='received'?'green':r.status==='dispute'?'amber':''}">${esc(names[r.status]||r.status)}</span></td></tr>`).join(''):'<tr><td colspan="6" class="sr-empty">No parcel records match the selected filters.</td></tr>';
    $('sr-record-count').textContent=fmt(filtered.length)+' records';
    $('sr-record-summary').textContent=filtered.length?`Showing ${(page-1)*pageSize+1}–${Math.min(page*pageSize,filtered.length)} of ${fmt(filtered.length)}`:'Showing 0 records';
    const pager=$('sr-pager');pager.replaceChildren();
    function button(text,target,disabled,active=false){const b=document.createElement('button');b.type='button';b.textContent=text;b.disabled=disabled;b.classList.toggle('active',active);b.addEventListener('click',()=>{page=target;renderTable()});pager.append(b);}
    button('‹',page-1,page===1);
    const pages=new Set([1,maxPages,page-1,page,page+1]);let last=0;
    [...pages].filter(p=>p>=1&&p<=maxPages).sort((a,b)=>a-b).forEach(p=>{if(last&&p-last>1){const d=document.createElement('span');d.textContent='…';pager.append(d)}button(String(p),p,false,p===page);last=p});
    button('›',page+1,page===maxPages);
  }
  function drawChart(){
    const target=$('sr-volume-chart'),totals=new Map();
    selected.forEach(r=>totals.set(r.date,(totals.get(r.date)||0)+1));
    const entries=[...totals].sort((a,b)=>a[0].localeCompare(b[0]));
    if(!entries.length){target.innerHTML='<p class="sr-empty">No parcels for this period.</p>';return;}
    const step=Math.max(1,Math.ceil(entries.length/14)),points=[];
    for(let i=0;i<entries.length;i+=step){const slice=entries.slice(i,i+step);points.push({label:slice.length>1?`${labelDate(slice[0][0])} – ${labelDate(slice[slice.length-1][0])}`:labelDate(slice[0][0]),short:labelDate(slice[0][0]),total:slice.reduce((a,r)=>a+r[1],0)})}
    const w=Math.max(340,Math.floor(target.clientWidth||640)),h=Math.max(300,Math.floor(target.clientHeight||355));
    const left=52,right=10,top=16,bottom=44,plotW=w-left-right,plotH=h-top-bottom,max=Math.max(...points.map(p=>p.total),1)*1.15;
    const svgNS='http://www.w3.org/2000/svg',svg=document.createElementNS(svgNS,'svg');svg.setAttribute('viewBox',`0 0 ${w} ${h}`);svg.setAttribute('role','img');svg.setAttribute('aria-label','Sorting center daily parcel volume');
    const make=(tag,attrs,txt)=>{const node=document.createElementNS(svgNS,tag);Object.entries(attrs).forEach(([k,v])=>node.setAttribute(k,v));if(txt!==undefined)node.textContent=txt;svg.append(node);return node};
    for(let i=0;i<=4;i++){const frac=i/4,y=top+plotH*(1-frac);make('line',{x1:left,y1:y,x2:w-right,y2:y,stroke:'#edf0f3','stroke-dasharray':'4 5'});make('text',{x:left-9,y:y+4,'text-anchor':'end',fill:'#9ca3af','font-size':10},String(Math.round(max*frac)))}
    const tooltip=document.createElement('div');tooltip.className='sr-chart-tip';tooltip.hidden=true;
    const slot=plotW/points.length,bw=Math.min(59,slot*.78),bars=[];
    points.forEach((p,i)=>{
      const x=left+slot*i+(slot-bw)/2,fullHeight=p.total/max*plotH,fullY=top+plotH-fullHeight;
      const r=make('rect',{x,y:top+plotH,width:bw,height:0,rx:4,fill:'#a21c41',class:'sr-bar',tabindex:0,'aria-label':`${p.label}: ${p.total} parcels`});bars.push({r,fullHeight,i});
      const show=()=>{tooltip.innerHTML=`<span>${esc(p.label)}</span><strong>${fmt(p.total)} parcels</strong>`;tooltip.hidden=false;tooltip.style.left=Math.max(88,Math.min(w-88,x+bw/2))+'px';tooltip.style.top=Math.max(48,fullY-5)+'px'};
      r.addEventListener('mouseenter',show);r.addEventListener('focus',show);r.addEventListener('mouseleave',()=>tooltip.hidden=true);r.addEventListener('blur',()=>tooltip.hidden=true);
      make('text',{x:x+bw/2,y:h-15,'text-anchor':'middle',fill:'#9ca3af','font-size':10},p.short);
    });target.replaceChildren(svg,tooltip);
    const token=++animationToken;
    if(window.matchMedia('(prefers-reduced-motion: reduce)').matches){bars.forEach(({r,fullHeight})=>{r.setAttribute('height',fullHeight);r.setAttribute('y',top+plotH-fullHeight)});return}
    const start=performance.now();function frame(now){if(token!==animationToken)return;let running=false;bars.forEach(({r,fullHeight,i})=>{const p=Math.min(1,Math.max(0,(now-start-i*50)/730)),e=1-Math.pow(1-p,3),height=fullHeight*e;r.setAttribute('height',height);r.setAttribute('y',top+plotH-height);if(p<1)running=true});if(running)requestAnimationFrame(frame)}requestAnimationFrame(frame);
  }
  for(const input of [from,to])input.addEventListener('change',updateAll);
  search.addEventListener('input',()=>{page=1;renderTable()});status.addEventListener('change',()=>{page=1;renderTable()});
  reset.addEventListener('click',()=>{reset.classList.remove('spinning');void reset.offsetWidth;reset.classList.add('spinning');currentMonth()});
  reset.addEventListener('animationend',()=>reset.classList.remove('spinning'));
  let prevWidth=0;const chart=$('sr-volume-chart');
  if('ResizeObserver'in window){new ResizeObserver(entries=>{const width=Math.round(entries[0].contentRect.width);if(width>0&&prevWidth>0&&Math.abs(prevWidth-width)>15){drawChart()}if(width>0)prevWidth=width}).observe(chart)}
  currentMonth();
 });
})();
</script>
</body>
</html>
