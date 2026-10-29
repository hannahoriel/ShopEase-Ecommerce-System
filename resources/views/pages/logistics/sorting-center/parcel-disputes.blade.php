@php
    $centerId = (string) ($sortingCenterId ?? data_get(auth()->user(), 'sorting_center_id', ''));
    $accountId = (string) (auth()->id() ?? '');
    $accountEmail = (string) (auth()->user()?->email ?? '');
    $centerName = (string) ($sortingCenterName ?? data_get(auth()->user(), 'sorting_center_name') ?? auth()->user()?->name ?? 'Assigned Sorting Center');
@endphp
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><title>Parcel Disputes — ShopEase Sorting Center</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--brand:#7b1b1b;--line:#eee4df;--muted:#8b807c}*{box-sizing:border-box}html,body{margin:0}body{font-family:Poppins,sans-serif;background:#fcf8f6;color:#281d1b}button,input,select{font:inherit}button{cursor:pointer}.lg-main{margin-left:260px;padding:114px 25px 40px;transition:margin-left .3s}.lg-body.lg-collapsed .lg-main{margin-left:80px}.pd-panel{background:#fff;border:1px solid var(--line);border-radius:16px;overflow:hidden;box-shadow:0 2px 13px #421c1709;max-width:1500px;margin:auto}.pd-head{padding:22px 23px 10px;display:flex;justify-content:space-between;align-items:center;gap:12px}.pd-head h1{font-size:18px;margin:0}.pd-head p{font-size:11px;color:var(--muted);margin:7px 0 0}.pd-controls{padding:14px 23px 18px;display:flex;gap:10px;flex-wrap:wrap}.pd-controls input,.pd-controls select{border:1px solid #ddd2ce;border-radius:9px;background:white;padding:10px 12px;font-size:12px;color:#62524d;min-height:39px}.pd-controls input{width:330px;max-width:100%}.pd-scroll{overflow:auto}.pd-table{width:100%;border-collapse:collapse;min-width:850px}.pd-table th{font-size:11px;color:#897c77;background:#fff9f7;padding:14px 17px;text-align:left;font-weight:600;border-block:1px solid #f1e9e5}.pd-table td{font-size:11px;border-bottom:1px solid #f3edeb;padding:16px 17px;vertical-align:middle}.pd-table tr[data-id]{cursor:pointer}.pd-table tr[data-id]:hover{background:#fff8f5}.pd-tag{display:inline-block;padding:5px 10px;border-radius:999px;background:#fff0e0;color:#95571d;font-size:10px;font-weight:600}.pd-tag.urgent{background:#fce4e5;color:#a02f3b}.pd-tag.high{background:#fff0de;color:#9d6028}.pd-tag.normal{background:#e8f2fc;color:#34759e}.pd-foot{padding:14px 23px;color:#988984;font-size:11px}.pd-empty{text-align:center;color:#9b908b!important;padding:45px!important}.pd-modal{position:fixed;inset:0;background:#241217a8;z-index:500;display:grid;place-items:center;padding:20px}.pd-modal[hidden]{display:none}.pd-dialog{background:#fff;border-radius:15px;width:min(940px,100%);max-height:90vh;overflow:auto;box-shadow:0 20px 70px #0003}.pd-modal-head{display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--line);padding:17px 21px}.pd-modal-head h2{font-size:17px;margin:0}.pd-close{border:0;border-radius:50%;background:#faf0ed;color:var(--brand);font-size:22px;height:33px;width:33px}.pd-modal-body{padding:20px 22px}.pd-field{margin:0 0 15px}.pd-field label{display:block;color:#a0918b;font-size:10px;margin-bottom:4px}.pd-field strong,.pd-field p{font-size:12px;white-space:pre-wrap;overflow-wrap:anywhere;margin:0;line-height:1.7}.pd-actions{padding:14px 22px;border-top:1px solid var(--line);display:flex;justify-content:flex-end}.pd-btn{padding:9px 18px;background:#fff;color:#655957;border:1px solid #bfb8b4;border-radius:8px;font-size:12px}.pd-note{margin:0 23px 18px;color:#998b83;font-size:10px;line-height:1.7}
.pd-modal-body{padding:22px 26px;overflow-y:auto}.pd-dialog{display:flex;flex-direction:column;overflow:hidden}.pd-modal-head,.pd-actions{flex-shrink:0}.pd-actions{gap:10px;flex-wrap:wrap}.pd-primary{background:#7b1b1b;color:white;border-color:#7b1b1b;font-weight:600}.pd-primary:hover{background:#651318}.pd-section{border:1px solid #eee4df;border-radius:12px;overflow:hidden;margin-bottom:16px}.pd-section-title{font-size:12px;font-weight:700;background:#fff7f5;color:#77242c;padding:13px 16px;border-bottom:1px solid #eee4df}.pd-section-content{padding:16px}.pd-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px 22px}.pd-field{margin:0;min-width:0}.pd-field label{font-size:10px;color:#9b8e88;margin-bottom:6px}.pd-field strong,.pd-field p{font-size:12px;color:#302725;font-weight:600;line-height:1.7}.pd-full{grid-column:1/-1}.pd-item{border:1px solid #eaddd8;border-radius:10px;padding:14px;margin-bottom:10px}.pd-item:last-child{margin-bottom:0}.pd-item h4{font-size:12px;margin:0 0 13px;color:#5c2629}.pd-history{margin:0;padding-left:18px}.pd-history li{font-size:11px;color:#433634;padding:7px 0}.pd-history small{display:block;color:#9b8e89;font-size:10px}.pd-caution{font-size:11px;line-height:1.7;color:#856047;background:#fff8ef;border:1px solid #f2e2d1;border-radius:9px;padding:11px 14px;margin:0 0 17px}.pd-input-field{display:grid;gap:7px;font-size:11px;color:#655450;font-weight:600}.pd-input-field span{color:#a62c35}.pd-input-field input,.pd-input-field select,.pd-input-field textarea{border:1px solid #e1d4cf;border-radius:9px;padding:11px 12px;background:white;font-size:12px;outline:0;width:100%}.pd-input-field textarea{min-height:100px;resize:vertical}.pd-account-form{display:grid;gap:16px}.pd-dialog-actions{display:flex;justify-content:flex-end;gap:10px}.pd-account-dialog{max-width:670px}.pd-tag.confirmed{background:#edf7ee;color:#267543}.pd-note{margin-top:15px}.pd-details-note{font-size:10px;color:#9f8e88;margin-top:8px}.pd-accountability-summary{border:1px solid #dfebdd;background:#f6fbf5;border-radius:9px;padding:13px;line-height:1.8;font-size:11px;color:#486948}.pd-modal[hidden]{display:none!important}body.pd-modal-open{overflow:hidden}
@media(max-width:640px){.pd-grid{grid-template-columns:1fr}.pd-modal{padding:9px}.pd-modal-body{padding:16px}.pd-actions{padding:12px}.pd-dialog{max-height:95vh}}
@media(max-width:900px){.lg-main,.lg-body.lg-collapsed .lg-main{margin-left:80px}}@media(max-width:700px){.lg-main,.lg-body.lg-collapsed .lg-main{margin-left:0;padding:96px 13px 24px}.pd-head{padding:18px}.pd-controls{padding:12px 18px}.pd-controls input,.pd-controls select{width:100%}}

/* Match the Sorting Center Scanner's Order Details modal boxes exactly. */
.pd-modal-body{padding:23px 28px}
.pt-detail-card{border:1px solid #eee4df;border-radius:11px;overflow:hidden;margin-bottom:16px;background:white}
.pt-detail-title{background:#fff7f5;padding:13px 16px;font-size:12px;color:#69232b;font-weight:700;border-bottom:1px solid #eee3dd}
.pt-detail-body{padding:16px}
.pt-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
.pt-field label{font-size:10px;color:#978882;display:block;margin-bottom:5px}
.pt-field strong{display:block;font-size:12px;font-weight:600;color:#332a27;overflow-wrap:anywhere}
.pt-wide{grid-column:1/-1}
.pt-party-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.pt-info-pair{display:grid;grid-template-columns:130px minmax(0,1fr);gap:10px 14px;align-items:start}
.pt-info-pair+.pt-info-pair{margin-top:10px;padding-top:10px;border-top:1px solid #f2e7e2}
.pt-info-pair label{font-size:11px;color:#9d8d87}
.pt-info-pair strong{font-size:12px;color:#332a27;font-weight:600;overflow-wrap:anywhere}
.pt-item-stack{display:flex;flex-direction:column;gap:14px}
.pt-order-item{border:1px solid #ead7d1;border-radius:14px;overflow:hidden;background:#fff}
.pt-order-item-head{background:#f7efed;padding:14px 16px;border-bottom:1px solid #eadfdb}
.pt-order-item-index{font-size:10px;letter-spacing:1.4px;text-transform:uppercase;color:#a2574c;font-weight:700;display:block;margin-bottom:8px}
.pt-order-item-name{font-size:13px;font-weight:700;color:#2e2321}
.pt-order-item-body{padding:16px}
.pt-order-item-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px 24px}
.pt-history{list-style:none;padding:0;margin:0}
.pt-history li{padding:11px 0 11px 22px;border-left:2px solid #e8dcd7;position:relative;font-size:12px}
.pt-history li:before{content:'';position:absolute;left:-6px;top:16px;width:9px;height:9px;border-radius:50%;background:#9b2935}
.pt-history small{display:block;margin-top:4px;color:#968984;font-size:10px}
.pt-notes{border:1px solid #f2e0d9;background:#fff9f6;border-radius:9px;padding:14px;font-size:12px;color:#695d58;line-height:1.7;white-space:pre-wrap}
.pt-shipping-progress{display:flex;align-items:flex-start;justify-content:center;max-width:520px;width:100%;margin:15px auto 20px;gap:0}
.pt-progress-item{width:99px;flex:0 0 99px;min-width:0;display:flex;flex-direction:column;align-items:center;text-align:center}
.pt-progress-item img{width:36px;height:36px;max-width:36px;object-fit:contain;position:relative;z-index:1}
.pt-progress-item small{display:block;margin-top:8px;font-size:11px;color:#928984;line-height:1.4}
.pt-progress-line{flex:1;min-width:14px;max-width:65px;height:1px;margin-top:18px;background:#d2cdcb}
.pt-progress-line.done{background:#7db473}
.pd-accountability-summary{border:0;background:transparent;padding:0}
@media(max-width:600px){.pt-party-grid,.pt-fields,.pt-order-item-fields{grid-template-columns:1fr}.pt-info-pair{grid-template-columns:110px minmax(0,1fr)}.pd-modal-body{padding:18px}}
@media(max-width:550px){.pt-progress-item{width:67px;flex-basis:67px}.pt-progress-item img{width:29px;height:29px}.pt-progress-item small{font-size:9px}.pt-progress-line{margin-top:15px;min-width:7px}}

/* Keep required asterisks beside their field titles instead of on separate grid rows. */
.pd-account-form .pd-label-line{display:inline-flex;align-items:baseline;gap:4px;white-space:normal;color:#655450;font-size:11px;font-weight:600}
.pd-account-form .pd-label-line .pd-required{display:inline;color:#a62c35;font-weight:700;line-height:1}
</style></head><body class="lg-body">
@include('components.logistics.sorting-center.sidebar')
@include('components.logistics.sorting-center.navbar')
<main class="lg-main"><section class="pd-panel"><div class="pd-controls"><input id="pdSearch" type="search" placeholder="Search tracking number, issue, description…" aria-label="Search parcel disputes"></div><div class="pd-scroll"><table class="pd-table"><thead><tr><th>Report ID</th><th>Tracking Number</th><th>Issue Type</th><th>Reported At</th></tr></thead><tbody id="pdRows"><tr><td colspan="4" class="pd-empty">No parcel reports yet.</td></tr></tbody></table></div><footer class="pd-foot" id="pdCount">0 records</footer><p class="pd-note">Lost and damaged parcel reports generate a simulated full-value reimbursement record with a demo date and time. Actual money transfer requires a verified backend payment transaction.</p></section></main>
<div id="pdModal" class="pd-modal" hidden><section class="pd-dialog" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="pdTitle"><header class="pd-modal-head"><h2 id="pdTitle">Parcel Issue Details</h2><button id="pdModalX" class="pd-close" type="button" aria-label="Close">×</button></header><div class="pd-modal-body" id="pdDetails"></div><footer class="pd-actions"><button id="pdModalClose" class="pd-btn" type="button">Close</button></footer></section></div>

<script>
document.addEventListener('DOMContentLoaded',()=>{'use strict';
const $=id=>document.getElementById(id), norm=v=>String(v??'').trim().toLowerCase(), esc=v=>String(v??'—').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const networkKey='shopease-logistics-network-hubs-ui-v1';const configuredId=@json($centerId),accountId=@json($accountId),accountEmail=@json($accountEmail);
let saved=[];try{const data=JSON.parse(localStorage.getItem(networkKey)||'[]');if(Array.isArray(data))saved=data;}catch{}
const match=saved.find(c=>norm(c.login_email||c.manager_email)===norm(accountEmail));
const centerId=String(configuredId||match?.id||(accountId?'account-'+accountId:''));
const key='shopease-sc-issues-preview-'+centerId;const scannerKey='shopease-sc-scanner-transfers-demo-v1';
let reports=[];try{const data=JSON.parse(localStorage.getItem(key)||'[]');if(Array.isArray(data))reports=data.filter(x=>String(x.centerId)===centerId);}catch{}
const fmt=v=>{if(!v)return '—';const date=new Date(v);return isNaN(date)?String(v):date.toLocaleString('en-PH',{timeZone:'Asia/Manila',dateStyle:'medium',timeStyle:'short'});};
const initial=new URLSearchParams(location.search).get('tracking');if(initial)$('pdSearch').value=initial;
// Restore the missing table renderer (removed by an earlier modal/layout edit).
function render(){
  const q=norm($('pdSearch').value);
  const matching=reports.filter(r=>!q||norm([r.tracking,r.type,r.description,r.id].join(' ')).includes(q));
  $('pdCount').textContent=matching.length+' of '+reports.length+' reports';
  $('pdRows').innerHTML=matching.map(r=>`<tr tabindex="0" role="button" data-id="${esc(r.id)}" aria-label="View issue ${esc(r.id)}"><td><strong>${esc(r.id)}</strong></td><td>${esc(r.tracking)}</td><td>${esc(r.type)}</td><td>${esc(fmt(r.at))}</td></tr>`).join('')||'<tr><td colspan="4" class="pd-empty">No parcel issues match your filters.</td></tr>';
}

const field=(label,value,wide=false)=>`<div class="pt-field ${wide?'pt-wide':''}"><label>${esc(label)}</label><strong>${esc(value??'—')}</strong></div>`;
const section=(title,body)=>`<section class="pt-detail-card"><div class="pt-detail-title">${esc(title)}</div><div class="pt-detail-body">${body}</div></section>`;
const grid=body=>`<div class="pt-fields">${body}</div>`;
const pair=(label,value)=>`<div class="pt-info-pair"><label>${esc(label)}</label><strong>${esc(value??'—')}</strong></div>`;
const formatMoney=v=>{if(v===null||v===undefined||v==='')return '—';const n=Number(String(v).replace(/[^0-9.-]/g,''));return Number.isFinite(n)?'₱ '+n.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}):'—'};
function scannerParcel(tracking){
 try{
  const state=JSON.parse(localStorage.getItem(scannerKey)||'{}');
  const p=(state.parcels||[]).find(x=>norm(x.tracking||x.tracking_number)===norm(tracking))||null;
  if(!p)return null;
  // Demo-only upgrade: the previous scanner seed stored literal '09XX XXX XXXX'.
  const match=String(p.tracking||'').match(/^SHP-SC-PREVIEW-(10[1-6])$/);
  if(match && String(p.id||'').startsWith('preview-')){
   const phones=['09171234561','09181234562','09191234563','09201234564','09211234565','09221234566'];
   let changed=false;
   if(/x|\*/i.test(String(p.buyer_phone||''))||!p.buyer_phone){p.buyer_phone=phones[Number(match[1])-101];changed=true;}
   if(/x|\*/i.test(String(p.seller_phone||''))||!p.seller_phone){p.seller_phone='09175678901';changed=true;}
   if(changed)localStorage.setItem(scannerKey,JSON.stringify(state));
  }
  return p;
 }catch{return null;}
}
function partyInfo(p,label){
 const b=label.toLowerCase();
 const contact=[p[b+'_phone'],p[b+'_contact_number'],p[b+'_contact_no'],p[b+'_mobile_number'],p[b+'_mobile'],p[b+'_phone_number'],p[b+'_contact'],p[b+'Phone'],p[b+'ContactNumber'],p[b]?.phone,p[b]?.contact_number,p[b]?.mobile_number].find(v=>v!==null&&v!==undefined&&String(v).trim()!=='');
 return section(label+' Information',pair('Name',p[b+'_name'])+pair('Email',p[b+'_email'])+pair('Contact Number',contact??'Not provided')+pair('Province',p[b+'_province'])+pair('Municipality',p[b+'_city'])+pair('Barangay',p[b+'_barangay'])+pair('Street',p[b+'_street'])+pair('House No.',p[b+'_house_no'])+pair('ZIP',p[b+'_postal_code']));
}
function reportAccountability(r){
 if(!r.accountability)return section('Accountability & Seller Compensation','<p class="pd-details-note">Automatic reimbursement applies to lost and damaged parcel reports.</p>');
 const a=r.accountability;
 return section('Accountability & Seller Compensation',`<div class="pd-accountability-summary">${grid(field('Responsible Party','Logistics Partner')+field('Resolution','Full Parcel Value Reimbursement to Seller')+field('Parcel Value',formatMoney(a.parcelAmount))+field('Amount Owed to Seller',formatMoney(a.compensation))+field('Settlement Status',a.status||'Reimbursed · UI Simulation')+field('Reimbursed At (Demo)',fmt(a.simulatedReimbursedAt))+field('Accountability Recorded At',fmt(a.at))+field('Notes',a.notes||'Automatically assigned after the parcel issue was reported.',true))}</div><p class="pd-details-note">The reimbursement date/time is simulated for UI preview only. No real money has been transferred or credited to the seller.</p>`);
}
function applyAutomaticAccountability(){
 let changed=false;
 for(const r of reports){
  const issue=norm(r.type||r.issueType||r.issue_type).replace(/[-_]/g,' ');
  if(!(issue.includes('lost')||issue.includes('damaged')) || r.accountability?.autoVersion===3)continue;
  const p=scannerParcel(r.tracking);
  const value=Number(p?.total_amount ?? r.parcelAmount ?? r.total_amount ?? NaN);
  const amount=Number.isFinite(value)&&value>0?value.toFixed(2):'';
  r.accountability={party:'Logistics Partner',resolution:'Full Parcel Value Reimbursement to Seller',parcelAmount:amount,compensation:amount,notes:'Automatically assessed when a '+issue+' report was recorded.',at:r.at||new Date().toISOString(),simulatedReimbursedAt:amount?(r.accountability?.simulatedReimbursedAt||r.at||new Date().toISOString()):null,status:amount?'Reimbursed · UI Simulation':'Amount Verification Required · Demo',autoVersion:3};
  changed=true;
 }
 if(changed){try{localStorage.setItem(key,JSON.stringify(reports));}catch(error){console.warn('Unable to save demo accountability:',error);}}
}

function trackingProgress(p){
 const stage=p.status==='delivered'?3:p.status==='out-for-delivery'?2:['incoming','received','ready','staged','loaded'].includes(p.status)?1:0;
 const labels=['Processing','In Transit','Out for Delivery','Delivered'];
 const icons=labels.map((_,i)=>i===0?(stage>0?'in-transit-check.png':'processing.png'):i===1?(stage>1?'in-transit-check.png':stage===1?'in-transit-blue.png':'in-transit-gray.png'):i===2?(stage>2?'delivered-check.png':stage===2?'out-for-delivery.png':'out-for-delivery-gray.png'):(stage===3?'delivered-check.png':'delivered-gray.png'));
 return `<div class="pt-shipping-progress" role="img" aria-label="Tracking Progress">${labels.map((label,i)=>`<div class="pt-progress-item"><img src="/icons/seller/shipping-status/${icons[i]}" alt=""><small>${label}</small></div>${i<labels.length-1?`<span class="pt-progress-line ${i<stage?'done':''}"></span>`:''}`).join('')}</div>`;
}
let activeReportId=null;
function openReport(id){
 const r=reports.find(x=>String(x.id)===String(id));if(!r)return;
 applyAutomaticAccountability();activeReportId=r.id;const p=scannerParcel(r.tracking);
 // Same detail-card order, field grid, paired buyer/seller columns and progress icons as Scanner.
 let html=section('Report Information',grid(field('Report ID',r.id)+field('Tracking Number',r.tracking)+field('Issue Type',r.type)+field('Report Status',r.status)+field('Reported At',fmt(r.at))+field('Description',r.description,true)+field('Evidence Filename',r.evidenceName||'No evidence attached',true)));
 if(p){
  const currentLocation=p.status==='incoming'?'On the way to this sorting center':p.current_location||p.loadedToHubName||p.receivedFrom||'Assigned Sorting Center';
  const currentStatus=p.status==='incoming'?'In Transit':p.status==='received'?'Arrived at Sorting Center':p.status==='staged'?'Preparing for Departure':p.status==='loaded'?'In Transit':p.status==='out-for-delivery'?'Out for Delivery':p.status==='delivered'?'Delivered':'Processing';
  html+=section('Order Information',grid(field('Order Number',p.order_number)+field('Ordered At',fmt(p.ordered_at))+field('Estimated Delivery',fmt(p.estimated_delivery))+field('Tracking Number',p.tracking)+field('Current Location',currentLocation)+field('Shipping Status',currentStatus)));
  html+=section('Tracking Progress',trackingProgress(p));
  html+=`<div class="pt-party-grid">${partyInfo(p,'Buyer')}${partyInfo(p,'Seller')}</div>`;
  html+=section('Payment Information',grid(field('Payment Method',p.payment_method)+field('Order Total',formatMoney(p.total_amount))));
  const items=Array.isArray(p.items)?p.items:[];
  html+=section('Ordered Items',`<div class="pt-item-stack">${items.length?items.map((it,i)=>`<article class="pt-order-item"><div class="pt-order-item-head"><span class="pt-order-item-index">Item ${i+1}</span><div class="pt-order-item-name">${esc(it.product_name||it.name)}</div></div><div class="pt-order-item-body"><div class="pt-order-item-fields">${field('Variation',it.variation)}${field('Color',it.color)}${field('Size',it.size)}${field('Quantity Ordered',it.quantity)}${field('Unit Price',formatMoney(it.product_price))}</div></div></article>`).join(''):'<p class="pd-details-note">No items available.</p>'}</div>`);
  const history=Array.isArray(p.history)?p.history:[];
  const processAt=p.receivedAt||p.loadedAt||null;
  const entries=history.map(h=>`<li><strong>${esc(h.status)}</strong><small>${esc(h.date||'—')} · ${esc(h.location||'—')}</small></li>`).join('')+`<li><strong>${esc(currentStatus)}</strong><small>${esc(fmt(processAt))} · ${esc(currentLocation)}</small></li>`;
  html+=section('Tracking History',`<ul class="pt-history">${entries}</ul>`);
  html+=section('Customer Notes',`<div class="pt-notes">${esc(p.notes||'—')}</div>`);
  html+=section('Parcel QR Code','<div class="sc-qr-placeholder" style="min-height:125px;border:1px dashed #dfd3cd;border-radius:10px;background:#fcfaf9;display:flex;align-items:center;justify-content:center;text-align:center;padding:20px;color:#998d88;font-size:12px">QR code will be available once provided by the backend.</div>');
 }else{html+=section('Parcel Details','<p class="pd-details-note">Matching Scanner parcel data is unavailable in this browser. The report remains visible.</p>');}
 html+=reportAccountability(r);
 $('pdDetails').innerHTML=html;$('pdModal').hidden=false;document.body.classList.add('pd-modal-open');$('pdModalX').focus();
}
function close(){$('pdModal').hidden=true;document.body.classList.remove('pd-modal-open');}
$('pdRows').addEventListener('click',e=>{const row=e.target.closest('[data-id]');if(row)openReport(row.dataset.id)});$('pdRows').addEventListener('keydown',e=>{if(!['Enter',' '].includes(e.key))return;const row=e.target.closest('[data-id]');if(row){e.preventDefault();openReport(row.dataset.id)}});
$('pdSearch').addEventListener('input',render);
$('pdModalX').addEventListener('click',close);$('pdModalClose').addEventListener('click',close);$('pdModal').addEventListener('click',e=>{if(e.target===$('pdModal'))close()});document.addEventListener('keydown',e=>{if(e.key==='Escape')close();});window.addEventListener('storage',e=>{if(e.key===key){try{reports=JSON.parse(e.newValue||'[]').filter(x=>String(x.centerId)===centerId)}catch{reports=[]}applyAutomaticAccountability();render()}});applyAutomaticAccountability();render();const openId=new URLSearchParams(location.search).get('report');if(openId&&reports.some(r=>String(r.id)===openId))openReport(openId);
});
</script></body></html>