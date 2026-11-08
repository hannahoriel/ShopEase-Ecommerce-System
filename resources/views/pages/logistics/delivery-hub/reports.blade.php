@php
    $hubRecord = $deliveryHub ?? $hub ?? null;
    $hubIdForAssignments = (string)($deliveryHubId ?? data_get($hubRecord, 'id') ?? 'account-'.(auth()->id() ?? 'preview'));
    $hubName = data_get($hubRecord, 'hub_name') ?: data_get($hubRecord, 'branch_name') ?: data_get($hubRecord, 'name') ?: 'Delivery Hub';
    $reportToday = \Carbon\Carbon::today('Asia/Manila');
    $deliveryHubReportPayload = [
        'today' => $reportToday->toDateString(),
        'hub' => $hubName,
        'hub_id' => $hubIdForAssignments,
        'rows' => [],
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Delivery Hub Reports — ShopEase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
/* Scoped Delivery Hub Reports design. Shared sidebar/navbar are rendered by their existing components. */
.dh-report-main,.dh-report-main *{box-sizing:border-box}
.dh-report-main{font-family:Poppins,Arial,sans-serif;color:#18253a}
.dhr-shell{max-width:1600px;width:100%;margin:auto}
.dhr-period{position:relative;display:grid;grid-template-columns:minmax(260px,1fr) auto 40px;align-items:center;gap:16px;border:1px solid #e6e8ee;background:#fff;border-radius:16px;padding:17px 22px;margin-bottom:24px;box-shadow:0 1px 6px #211d2706}
.dhr-period-label{display:flex;align-items:center;gap:13px}
.dhr-calendar{width:42px;height:42px;border-radius:11px;background:#faf0f3;color:#a21c41;display:grid;place-items:center;flex-shrink:0}.dhr-calendar svg{width:23px;height:23px}
.dhr-period-label strong{display:block;font-size:14px;font-weight:600;color:#19243a}.dhr-period-label small{display:block;margin-top:4px;font-size:11px;color:#9ba0b0}
.dhr-dates{display:flex;align-items:center;gap:10px}.dhr-dates label{display:flex;align-items:center;gap:9px;font-size:12px;color:#8994a9;font-weight:600}.dhr-dates input{height:42px;width:165px;border:1px solid #dde1e9;border-radius:10px;padding:0 10px;color:#283449;background:#fff;font-size:12px;outline:0}.dhr-dates input:focus,.dhr-table-tools input:focus,.dhr-table-tools select:focus{border-color:#acb1bb;box-shadow:0 0 0 3px #9ca3af24}.dhr-dash{color:#b9bdc7}
.dhr-reset{border:0;background:transparent;color:#313337;border-radius:9px;width:40px;height:40px;display:grid;place-items:center;cursor:pointer}.dhr-reset:hover{background:#fff1ec}.dhr-reset svg{width:19px;height:19px}.dhr-reset.spinning svg{animation:dhr-spin .65s ease-out}@keyframes dhr-spin{to{transform:rotate(360deg)}}
.dhr-date-error{grid-column:1/-1;color:#a52b42;font-size:11px;margin:0}.dhr-date-error:empty{display:none}
.dhr-section-heading{display:flex;align-items:center;gap:14px;margin:0 0 13px}.dhr-section-heading>strong{font-size:11px;font-weight:700;letter-spacing:1.6px;color:#a21c41}.dhr-section-heading>span{font-size:11px;color:#969eab}.dhr-right-note{margin-left:auto}
.dhr-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:18px}
.dhr-kpi,.dhr-card{background:white;border:1px solid #e6e8ee;border-radius:16px;box-shadow:0 2px 10px #161e3006;min-width:0}.dhr-kpi{padding:20px;min-height:143px}.dhr-kpi-label{display:flex;align-items:center;justify-content:space-between;gap:9px;color:#7d8596;font-size:12px;font-weight:600}.dhr-kpi-icon{border-radius:9px;background:#faf0f3;color:#a21c41;min-width:32px;height:32px;display:grid;place-items:center;font-size:17px}.dhr-kpi>strong{display:block;font-weight:650;font-size:clamp(21px,2vw,27px);margin:12px 0 6px;line-height:1.2;color:#18243b}.dhr-kpi>small{color:#9a9faf;font-size:10px;line-height:1.6;display:block}
.dhr-two{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(0,1fr);gap:18px;margin-bottom:18px}.dhr-three{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-bottom:18px}.dhr-card{padding:21px 23px}.dhr-card-header{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:18px}.dhr-card-header h2{font-size:17px;color:#202e42;font-weight:600;margin:0 0 5px}.dhr-card-header p{font-size:11px;color:#9fa6b4;margin:0;line-height:1.5}.dhr-pill{background:#faf0f3;color:#9a3853;border-radius:999px;padding:7px 11px;font-size:10px;font-weight:600;white-space:nowrap}
.dhr-chart-card{display:flex;flex-direction:column;min-height:420px}.dhr-volume-chart{flex:1;min-height:310px;position:relative}.dhr-volume-chart svg{width:100%;height:100%;min-height:310px;display:block;overflow:visible}.dhr-tip{position:absolute;z-index:3;background:#282230;color:white;border-radius:9px;padding:8px 11px;font-size:11px;pointer-events:none;white-space:nowrap;transform:translate(-50%,-110%);box-shadow:0 5px 18px #1b17312a}.dhr-tip[hidden]{display:none}.dhr-tip strong{display:block;margin-top:3px;font-size:12px}
.dhr-meter-list{display:grid;gap:20px}.dhr-meter-head{display:flex;justify-content:space-between;gap:12px;font-size:12px;color:#858c9b;margin-bottom:7px}.dhr-meter-head strong{color:#26334a;font-weight:600}.dhr-meter{height:8px;background:#f0f2f4;border-radius:40px;overflow:hidden}.dhr-meter i{display:block;height:100%;background:#ad3d5c;border-radius:inherit;transition:width .35s ease}.dhr-footnote,.dhr-cod-note{font-size:10px;line-height:1.7;color:#a1a5af;margin:20px 0 0}.dhr-cod-note{background:#fbf7f6;border-radius:9px;padding:10px 12px}
.dhr-metric-list{display:grid;gap:15px}.dhr-metric{display:flex;justify-content:space-between;gap:14px;align-items:flex-start;padding-bottom:13px;border-bottom:1px solid #f0e9e9}.dhr-metric:last-child{border-bottom:0;padding-bottom:0}.dhr-metric span{font-size:12px;color:#858e9d}.dhr-metric strong{font-size:12px;font-weight:600;color:#223149;white-space:nowrap}
.dhr-table-scroll{overflow:auto}.dhr-table{width:100%;border-collapse:collapse}.dhr-table th{padding:12px 10px;border-bottom:1px solid #e7e8ed;color:#98a0ad;font-size:11px;text-align:left;font-weight:600;white-space:nowrap}.dhr-table td{padding:14px 10px;border-bottom:1px solid #f0f0f3;font-size:11px;color:#657184}.dhr-table tbody tr:last-child td{border-bottom:0}.dhr-table tbody tr:hover{background:#fcfaf9}.dhr-table td:first-child{font-weight:600;color:#263349}.dhr-num{text-align:right!important;white-space:nowrap}.dhr-empty{text-align:center;padding:30px 14px!important;color:#9b9ca8!important}
.dhr-history{margin-bottom:18px}.dhr-table-tools{display:flex;align-items:end;gap:12px;flex-wrap:wrap;margin-bottom:12px}.dhr-table-tools>label{font-size:11px;color:#87909e;display:flex;flex-direction:column;gap:6px}.dhr-table-tools>label:first-child{flex:1;min-width:250px}.dhr-table-tools input,.dhr-table-tools select{border:1px solid #dee2e8;border-radius:9px;background:white;padding:0 12px;min-height:40px;outline:none;color:#343e4e;font-size:12px}.dhr-table-tools select{min-width:174px}.dhr-history-table{min-width:900px}.dhr-status{padding:6px 10px;border-radius:40px;background:#f5f5f6;color:#696876;font-size:10px;font-weight:600;white-space:nowrap}.dhr-status.delivered{background:#eaf7ef;color:#238052}.dhr-status.in-progress{background:#fff5e7;color:#b87528}.dhr-status.exception{background:#feebee;color:#ad3d57}.dhr-table-bottom{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding-top:16px;color:#9298a4;font-size:11px}.dhr-pagination{display:flex;align-items:center;gap:5px}.dhr-pagination button{min-width:30px;height:30px;border-radius:8px;border:1px solid #e3e0e4;background:white;color:#55505c;cursor:pointer;font-size:11px}.dhr-pagination button.current{color:white;background:#8b2033;border-color:#8b2033}.dhr-pagination button:disabled{opacity:.35;cursor:default}.dhr-source-note{font-size:10px;color:#a0a4af;line-height:1.7;margin:0 2px 20px}
@media(max-width:1200px){.dhr-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}.dhr-three{grid-template-columns:1fr 1fr}.dhr-two{grid-template-columns:1fr}.dhr-period{grid-template-columns:1fr auto}.dhr-period-label{grid-column:1/-1}}
@media(max-width:700px){.dhr-period{padding:14px;grid-template-columns:1fr auto}.dhr-dates{gap:6px;flex-wrap:wrap}.dhr-dates label{flex-direction:column;align-items:flex-start;flex:1;min-width:120px}.dhr-dates input{width:100%}.dhr-three,.dhr-kpis{grid-template-columns:1fr}.dhr-card{padding:16px}.dhr-right-note{display:none}.dhr-table-tools>label:first-child{min-width:100%}}
@media(prefers-reduced-motion:reduce){.dhr-reset.spinning svg{animation-duration:.01ms}}

    
.dhr-rider-role-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:5px 0 20px}
.dhr-rider-role-stats>div{background:#fcf8f7;border:1px solid #eee5e2;border-radius:11px;padding:13px}
.dhr-rider-role-stats small{font-size:10px;color:#978884;display:block;margin-bottom:5px}
.dhr-rider-role-stats strong{font-size:21px;color:#342c2b;font-weight:600}
.dhr-role-ok{color:#248658;font-size:11px}.dhr-role-partial{color:#b67224;font-size:11px}
@media(max-width:800px){.dhr-rider-role-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
</style>
</head>
<body class="lg-body">
    <x-logistics.delivery-hub.sidebar />
    <x-logistics.delivery-hub.navbar />
    <main class="lg-main dh-report-main" id="dh-report-page">
        <div class="dhr-shell">
            <section class="dhr-period" aria-label="Reporting period">
                <div class="dhr-period-label">
                    <span class="dhr-calendar" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/></svg></span>
                    <span><strong>Reporting period</strong><small>Select a date range to update all hub reports</small></span>
                </div>
                <div class="dhr-dates">
                    <label for="dhr-from">From <input type="date" id="dhr-from" max="{{ $reportToday->toDateString() }}"></label>
                    <span aria-hidden="true" class="dhr-dash">—</span>
                    <label for="dhr-to">To <input type="date" id="dhr-to" max="{{ $reportToday->toDateString() }}"></label>
                </div>
                <button type="button" id="dhr-reset" class="dhr-reset" title="Reset to current month" aria-label="Reset date and table filters"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7v5h-5M4 17v-5h5"/><path d="M5.9 9a7.5 7.5 0 0 1 13.7-1.5M18.1 15A7.5 7.5 0 0 1 4.4 16.5"/></svg></button>
                <p class="dhr-date-error" id="dhr-date-error" role="alert"></p>
            </section>

            <div class="dhr-section-heading"><strong>HUB PERFORMANCE</strong><span id="dhr-period-caption">Current month</span><span class="dhr-right-note">{{ $hubName }} · Live data</span></div>
            <section class="dhr-kpis">
                <article class="dhr-kpi"><div class="dhr-kpi-label"><span>Parcels handled</span><span class="dhr-kpi-icon">▣</span></div><strong id="dhr-total-parcels">0</strong><small>Unique parcel records assigned to this hub</small></article>
                <article class="dhr-kpi"><div class="dhr-kpi-label"><span>Delivered parcels</span><span class="dhr-kpi-icon">✓</span></div><strong id="dhr-delivered">0</strong><small>Successful deliveries during the period</small></article>
                <article class="dhr-kpi"><div class="dhr-kpi-label"><span>On-time delivery rate</span><span class="dhr-kpi-icon">◷</span></div><strong id="dhr-on-time">0%</strong><small>On-time share of delivered parcels by estimated delivery date</small></article>
                <article class="dhr-kpi"><div class="dhr-kpi-label"><span>Distribution completed</span><span class="dhr-kpi-icon">₱</span></div><strong id="dhr-distributed">₱0.00</strong><small>Buyer cash already distributed to authorized recipients</small></article>
            </section>

            <section class="dhr-two dhr-main-charts">
                <article class="dhr-card dhr-chart-card"><div class="dhr-card-header"><div><h2>Parcel volume trend</h2><p>Parcels handled per day across your selected period</p></div><span class="dhr-pill">Parcels</span></div><div id="dhr-volume-chart" class="dhr-volume-chart" aria-label="Daily parcel volume"></div></article>
                <article class="dhr-card"><div class="dhr-card-header"><div><h2>Parcel movement</h2><p>Distinct delivery stages of your hub's parcels</p></div><span class="dhr-pill" id="dhr-status-count">0 records</span></div><div id="dhr-status-list" class="dhr-meter-list"></div><p class="dhr-footnote">Each parcel appears in exactly one stage at a time.</p></article>
            </section>

            <section class="dhr-three">
                <article class="dhr-card"><div class="dhr-card-header"><div><h2>Delivery quality</h2><p>Completion and service reliability</p></div><span class="dhr-pill">Service</span></div><div class="dhr-metric-list" id="dhr-quality"></div></article>
                <article class="dhr-card"><div class="dhr-card-header"><div><h2>Parcel disputes</h2><p>Lost and damaged parcels reported by this hub</p></div><span class="dhr-pill" id="dhr-issue-count">0 cases</span></div><div class="dhr-metric-list" id="dhr-issues"></div></article>
                <article class="dhr-card"><div class="dhr-card-header"><div><h2>Rider activity</h2><p>Deliveries handled by assigned riders</p></div><span class="dhr-pill">Operations</span></div><div class="dhr-table-scroll"><table class="dhr-table"><thead><tr><th>Rider</th><th class="dhr-num">Delivered</th><th class="dhr-num">On time</th></tr></thead><tbody id="dhr-riders"></tbody></table></div></article>
            </section>

            <section class="dhr-card" style="margin-bottom:18px"><div class="dhr-card-header"><div><h2>Rider Role & Barangay Coverage</h2><p>See pickup and delivery assignments for this hub only. Pickup riders collect seller parcels and buyer returns; delivery riders deliver buyer orders.</p></div><span class="dhr-pill">Rider assignments</span></div>
<div class="dhr-rider-role-stats"><div><small>Pickup riders</small><strong id="dhr-pickup-count">0</strong></div><div><small>Delivery riders</small><strong id="dhr-delivery-count">0</strong></div><div><small>Barangays fully staffed</small><strong id="dhr-covered-count">0</strong></div><div><small>Barangays needing a rider</small><strong id="dhr-needing-count">0</strong></div></div>
<div class="dhr-table-scroll"><table class="dhr-table"><thead><tr><th>Barangay</th><th>Pickup rider</th><th>Delivery rider</th><th>Coverage</th></tr></thead><tbody id="dhr-role-coverage"></tbody></table></div><p class="dhr-footnote">Active rider assignments are loaded for this delivery hub. Rider coverage is separate from completed-parcel statistics.</p></section>

            <section class="dhr-two">
                <article class="dhr-card"><div class="dhr-card-header"><div><h2>Parcels at This Hub</h2><p>Simple breakdown of where the hub’s parcels are now</p></div><span class="dhr-pill">Hub operations</span></div><div id="dhr-dispatch" class="dhr-metric-list"></div><p class="dhr-footnote">Each order is counted once according to its latest parcel status.</p></article>
                <article class="dhr-card"><div class="dhr-card-header"><div><h2>COD collection & settlement</h2><p>Cash handling and distribution accountability</p></div><span class="dhr-pill">Finance</span></div><div class="dhr-metric-list" id="dhr-cod-stats"></div><div class="dhr-cod-note">These are buyer-payment handling figures, not earnings retained by the Delivery Hub.</div></article>
            </section>

            <section class="dhr-card dhr-history">
                <div class="dhr-card-header"><div><h2>Parcel & settlement records</h2><p>Per-order activity for the selected reporting period</p></div><span class="dhr-pill" id="dhr-history-count">0 records</span></div>
                <div class="dhr-table-tools"><label for="dhr-search">Search records<input type="search" id="dhr-search" name="dhr_parcel_record_lookup" placeholder="Search order, rider, or destination" autocomplete="off" value="" spellcheck="false"></label><label for="dhr-type">Parcel status<select id="dhr-type"><option value="all">All statuses</option><option value="delivered">Delivered</option><option value="out_for_delivery">Out for delivery</option><option value="at_hub">At hub</option><option value="exception">Under investigation</option></select></label></div>
                <div class="dhr-table-scroll"><table class="dhr-table dhr-history-table"><thead><tr><th>Date & time</th><th>Order reference</th><th>Delivery destination</th><th>Rider</th><th>Status</th><th class="dhr-num">COD</th><th>Settlement</th></tr></thead><tbody id="dhr-history-rows"></tbody></table></div>
                <div class="dhr-table-bottom"><span id="dhr-page-caption">Showing 0 records</span><div id="dhr-pagination" class="dhr-pagination"></div></div>
            </section>
            <p class="dhr-source-note">Parcel and COD figures are loaded from shipment scans and settlement records assigned to {{ $hubName }}.</p>
        </div>
    </main>
    <script type="application/json" id="dhr-data">@json($deliveryHubReportPayload)</script>
    <script>
(() => {
  'use strict';
  document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('dh-report-page');
    const raw = document.getElementById('dhr-data');
    if (!root || !raw) return;
    let source;
    try {source = JSON.parse(raw.textContent);} catch (e) {return;}
    let records = Array.isArray(source.rows) ? source.rows : [];
    let riderAssignments = [];
    const $ = id => document.getElementById(id);
    const from = $('dhr-from'), to = $('dhr-to'), reset = $('dhr-reset');
    const search = $('dhr-search'), type = $('dhr-type');
    const money = n => '₱' + Number(n).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});
    const num = n => Number(n).toLocaleString('en-PH');
    const pct = (a,b) => b ? `${(a/b*100).toFixed(1)}%` : '0.0%';
    const escape = s => String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const labels = {delivered:'Delivered',out_for_delivery:'Out for delivery',at_hub:'At delivery hub',exception:'Under investigation'};
    function renderAssignedRiderRoles(){
      const assignments=riderAssignments.filter(a=>a&&['pickup','delivery'].includes(a.role)&&String(a.barangay||'').trim());
      const groups=new Map();
      for(const assignment of assignments){const barangay=String(assignment.barangay).trim();const key=barangay.toLocaleLowerCase();const row=groups.get(key)||{barangay,pickup:[],delivery:[]};row[assignment.role].push(assignment);groups.set(key,row)}
      const roles=Array.from(groups.values()).sort((a,b)=>a.barangay.localeCompare(b.barangay));
      document.getElementById('dhr-pickup-count').textContent=assignments.filter(a=>a.role==='pickup').length;
      document.getElementById('dhr-delivery-count').textContent=assignments.filter(a=>a.role==='delivery').length;
      document.getElementById('dhr-covered-count').textContent=roles.filter(a=>a.pickup.length&&a.delivery.length).length;
      document.getElementById('dhr-needing-count').textContent=roles.filter(a=>!a.pickup.length||!a.delivery.length).length;
      const safe=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
      document.getElementById('dhr-role-coverage').innerHTML=roles.length?roles.map(r=>`<tr><td>${safe(r.barangay)}</td><td>${r.pickup.length?r.pickup.map(a=>safe(a.name||'Assigned rider')).join(', '):'Unassigned'}</td><td>${r.delivery.length?r.delivery.map(a=>safe(a.name||'Assigned rider')).join(', '):'Unassigned'}</td><td><span class="${r.pickup.length&&r.delivery.length?'dhr-role-ok':'dhr-role-partial'}">${r.pickup.length&&r.delivery.length?'Complete pair':'Needs assignment'}</span></td></tr>`).join(''):'<tr><td colspan="4" class="dhr-empty">No active pickup or delivery riders are assigned to this hub.</td></tr>';
    }
    renderAssignedRiderRoles();

    const dateLabel = value => new Date(`${value}T12:00:00`).toLocaleDateString('en-PH',{month:'short',day:'2-digit'});
    let selected = [], filtered = [], currentPage = 1, chartToken = 0, requestToken = 0;
    const perPage = 10;
    function setMonth(){
      from.value = source.today.slice(0,7)+'-01';to.value = source.today;
      search.value = '';type.value='all';currentPage=1;refresh();
    }
    function metric(container,label,value){
      const el=document.createElement('div');el.className='dhr-metric';
      const a=document.createElement('span'),b=document.createElement('strong');a.textContent=label;b.textContent=value;el.append(a,b);container.appendChild(el);
    }
    function meter(container,label,value,total){
      const row=document.createElement('div'),header=document.createElement('div'),track=document.createElement('div'),fill=document.createElement('i');
      const name=document.createElement('span'),count=document.createElement('strong');
      header.className='dhr-meter-head';track.className='dhr-meter';name.textContent=label;count.textContent=num(value);
      fill.style.width=(total?Math.min(100,value*100/total):0)+'%';track.appendChild(fill);header.append(name,count);row.append(header,track);container.appendChild(row);
    }
    async function refresh(){
      if(!from.value || !to.value || from.value>to.value){$('dhr-date-error').textContent='Please choose a valid From and To date.';return;}
      $('dhr-date-error').textContent='';
      const token=++requestToken;
      try {
      const response=await fetch(`{{ route('delivery-hub.reports.data') }}?from=${encodeURIComponent(from.value)}&to=${encodeURIComponent(to.value)}`,{headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});
      if(!response.ok)throw new Error('Could not load delivery hub reports. Please try again.');
      const result=await response.json();if(token!==requestToken)return;
      const data=result.data||{};records=Array.isArray(data.rows)?data.rows:[];riderAssignments=Array.isArray(data.assignments)?data.assignments:[];renderAssignedRiderRoles();
      selected=records.filter(x=>x.date>=from.value && x.date<=to.value);
      currentPage=1;
      const delivered=selected.filter(x=>x.status==='delivered');
      const timely=delivered.filter(x=>x.on_time);
      const issues=selected.filter(x=>x.status==='exception');
      const distributed=delivered.filter(x=>x.distributed);
      const credited=delivered.filter(x=>x.cash_in);
      const pending=delivered.filter(x=>!x.distributed);
      $('dhr-period-caption').textContent=`${dateLabel(from.value)} – ${dateLabel(to.value)} ${to.value.slice(0,4)}`;
      $('dhr-total-parcels').textContent=num(selected.length);
      $('dhr-delivered').textContent=num(delivered.length);
      $('dhr-on-time').textContent=pct(timely.length,delivered.length);
      $('dhr-distributed').textContent=money(distributed.reduce((a,r)=>a+r.distribution_amount,0));
      const status=$('dhr-status-list');status.replaceChildren();
      for(const key of ['delivered','out_for_delivery','at_hub','exception']) meter(status,labels[key],selected.filter(r=>r.status===key).length,selected.length);
      $('dhr-status-count').textContent=num(selected.length)+' records';
      const quality=$('dhr-quality');quality.replaceChildren();
      metric(quality,'Delivery completion rate',pct(delivered.length,selected.length));
      const hours=delivered.map(x=>x.hours);
      metric(quality,'Average delivery time',hours.length?(hours.reduce((a,b)=>a+b,0)/hours.length).toFixed(1)+' hrs':'—');
      metric(quality,'Delivered after target',num(delivered.length-timely.length));
      const issueBox=$('dhr-issues');issueBox.replaceChildren();
      metric(issueBox,'Lost parcels',num(issues.filter(x=>x.issue==='lost').length));
      metric(issueBox,'Damaged parcels',num(issues.filter(x=>x.issue==='damaged').length));
      metric(issueBox,'Exception rate',pct(issues.length,selected.length));
      $('dhr-issue-count').textContent=num(issues.length)+' cases';
      const riderStats=new Map();
      for(const row of selected){const entry=riderStats.get(row.rider)||{delivered:0,timely:0};if(row.status==='delivered'){entry.delivered++;if(row.on_time)entry.timely++;}riderStats.set(row.rider,entry);}
      $('dhr-riders').innerHTML=[...riderStats.entries()].sort((a,b)=>b[1].delivered-a[1].delivered).slice(0,6).map(([name,x])=>`<tr><td>${escape(name)}</td><td class="dhr-num">${num(x.delivered)}</td><td class="dhr-num">${pct(x.timely,x.delivered)}</td></tr>`).join('')||'<tr><td colspan="3" class="dhr-empty">No rider activity</td></tr>';
      const awaitingDispatch=selected.filter(r=>r.status==='at_hub').length;
      const withRiders=selected.filter(r=>r.status==='out_for_delivery').length;
      const dispatchBox=$('dhr-dispatch');dispatchBox.replaceChildren();
      metric(dispatchBox,'Still at the hub (waiting for rider)',num(awaitingDispatch));
      metric(dispatchBox,'With riders for delivery',num(withRiders));
      metric(dispatchBox,'Successfully delivered',num(delivered.length));
      metric(dispatchBox,'Needs investigation',num(issues.length));
      const cashBox=$('dhr-cod-stats');cashBox.replaceChildren();
      metric(cashBox,'COD from delivered orders',money(delivered.reduce((a,r)=>a+r.cod,0)));
      metric(cashBox,'Cash-in recorded',money(credited.reduce((a,r)=>a+r.cod,0)));
      metric(cashBox,'Distributed to recipients',money(distributed.reduce((a,r)=>a+r.distribution_amount,0)));
      metric(cashBox,'Undistributed delivered COD',money(pending.reduce((a,r)=>a+r.cod,0)));
      drawChart();renderTable();
      } catch(error) {if(token===requestToken)$('dhr-date-error').textContent=error.message;}
    }
    function renderTable(){
      const q=search.value.trim().toLowerCase();
      filtered=selected.filter(row=>(type.value==='all'||row.status===type.value) && (!q||[row.reference,row.rider,row.destination,labels[row.status]].join(' ').toLowerCase().includes(q)))
        .sort((a,b)=>(b.date+' '+b.time).localeCompare(a.date+' '+a.time));
      const maxPage=Math.max(1,Math.ceil(filtered.length/perPage));currentPage=Math.min(currentPage,maxPage);
      const subset=filtered.slice((currentPage-1)*perPage,currentPage*perPage);
      $('dhr-history-rows').innerHTML=subset.map(r=>`<tr><td>${escape(dateLabel(r.date)+' '+r.time)}</td><td>${escape(r.reference)}</td><td>${escape(r.destination)}</td><td>${escape(r.rider)}</td><td><span class="dhr-status ${r.status==='delivered'?'delivered':r.status==='exception'?'exception':'in-progress'}">${escape(labels[r.status])}</span></td><td class="dhr-num">${r.status==='delivered'?money(r.cod):'—'}</td><td>${r.distributed?'Distributed':r.cash_in?'Cash-in recorded':'Not distributed'}</td></tr>`).join('')||'<tr><td colspan="7" class="dhr-empty">No matching parcel records.</td></tr>';
      $('dhr-history-count').textContent=num(filtered.length)+' records';
      $('dhr-page-caption').textContent=filtered.length?`Showing ${(currentPage-1)*perPage+1}–${Math.min(currentPage*perPage,filtered.length)} of ${num(filtered.length)}`:'Showing 0 records';
      const bar=$('dhr-pagination');bar.replaceChildren();
      function add(label,p,on,disabled=false){const b=document.createElement('button');b.type='button';b.textContent=label;b.disabled=disabled;if(on)b.className='current';b.addEventListener('click',()=>{currentPage=p;renderTable();});bar.appendChild(b);}
      add('‹',currentPage-1,false,currentPage===1);
      const visible=new Set([1,maxPage,currentPage-1,currentPage,currentPage+1]);let prev=0;
      [...visible].filter(n=>n>=1&&n<=maxPage).sort((a,b)=>a-b).forEach(n=>{if(prev && n-prev>1){const dots=document.createElement('span');dots.textContent='…';bar.appendChild(dots);}add(String(n),n,n===currentPage);prev=n;});
      add('›',currentPage+1,false,currentPage===maxPage);
    }
    function drawChart(){
      const target=$('dhr-volume-chart'),counts=new Map();
      selected.forEach(r=>counts.set(r.date,(counts.get(r.date)||0)+1));
      const points=[...counts.entries()].sort((a,b)=>a[0].localeCompare(b[0]));
      if(!points.length){target.innerHTML='<p class="dhr-empty">No parcel activity for this date range.</p>';return;}
      const grouped=[],size=Math.max(1,Math.ceil(points.length/14));
      for(let i=0;i<points.length;i+=size){const part=points.slice(i,i+size);grouped.push({label:dateLabel(part[0][0]),full:dateLabel(part[0][0])+(part.length>1?' – '+dateLabel(part.at(-1)[0]):''),value:part.reduce((a,p)=>a+p[1],0)});}
      const w=Math.max(350,Math.round(target.clientWidth||640)),h=Math.max(310,Math.round(target.clientHeight||340));
      const left=43,right=10,top=12,bottom=35,ph=h-top-bottom,pw=w-left-right;
      const peak=Math.max(1,...grouped.map(x=>x.value))*1.12;
      const ns='http://www.w3.org/2000/svg';const svg=document.createElementNS(ns,'svg');svg.setAttribute('viewBox',`0 0 ${w} ${h}`);
      const el=(tag,atts,text)=>{const item=document.createElementNS(ns,tag);Object.entries(atts).forEach(([k,v])=>item.setAttribute(k,v));if(text!=null)item.textContent=text;svg.appendChild(item);return item;};
      for(let n=0;n<5;n++){const y=top+ph*n/4;el('line',{x1:left,y1:y,x2:w-right,y2:y,stroke:'#ecedf1','stroke-dasharray':'4 5'});el('text',{x:left-8,y:y+4,'text-anchor':'end','font-size':10,fill:'#a2a7b1'},String(Math.round(peak*(1-n/4))));}
      const step=pw/grouped.length,barWidth=Math.min(60,step*.7),bars=[];
      const tooltip=document.createElement('div');tooltip.className='dhr-tip';tooltip.hidden=true;
      grouped.forEach((p,i)=>{
        const x=left+step*i+(step-barWidth)/2;const height=p.value/peak*ph;
        const bar=el('rect',{x,y:top+ph,width:barWidth,height:0,rx:5,fill:'#a21c41',tabindex:0});bars.push({bar,height,i});
        const show=()=>{tooltip.replaceChildren();const d=document.createElement('span'),v=document.createElement('strong');d.textContent=p.full;v.textContent=num(p.value)+' parcels';tooltip.append(d,v);tooltip.hidden=false;tooltip.style.left=Math.max(85,Math.min(w-85,x+barWidth/2))+'px';tooltip.style.top=(top+ph-height-7)+'px';};
        bar.addEventListener('mouseenter',show);bar.addEventListener('mousemove',show);bar.addEventListener('mouseleave',()=>tooltip.hidden=true);bar.addEventListener('focus',show);bar.addEventListener('blur',()=>tooltip.hidden=true);
        el('text',{x:x+barWidth/2,y:h-10,'font-size':10,fill:'#a0a5ae','text-anchor':'middle'},p.label);
      });
      target.replaceChildren(svg,tooltip);const token=++chartToken;
      if(matchMedia('(prefers-reduced-motion: reduce)').matches){bars.forEach(({bar,height})=>{bar.setAttribute('height',height);bar.setAttribute('y',top+ph-height);});return;}
      const start=performance.now();const animate=now=>{if(token!==chartToken)return;let more=false;for(const {bar,height,i} of bars){const f=Math.min(1,Math.max(0,(now-start-i*55)/650));const val=height*(1-Math.pow(1-f,3));bar.setAttribute('height',val);bar.setAttribute('y',top+ph-val);if(f<1)more=true;}if(more)requestAnimationFrame(animate);};requestAnimationFrame(animate);
    }
    [from,to].forEach(x=>x.addEventListener('change',refresh));
    search.addEventListener('input',()=>{currentPage=1;renderTable();});type.addEventListener('change',()=>{currentPage=1;renderTable();});
    reset.addEventListener('click',()=>{reset.classList.remove('spinning');void reset.offsetWidth;reset.classList.add('spinning');setMonth();});
    reset.addEventListener('animationend',()=>reset.classList.remove('spinning'));
    const chart=$('dhr-volume-chart');let width=Math.round(chart.getBoundingClientRect().width);
    if('ResizeObserver' in window){const ro=new ResizeObserver(list=>{const next=Math.round(list[0].contentRect.width);if(next>0&&Math.abs(next-width)>14){width=next;drawChart();}});ro.observe(chart);}
    setMonth();
  });
})();

    </script>
</body>
</html>
