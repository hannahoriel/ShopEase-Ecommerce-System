@php
    $deliveryHub = $deliveryHub ?? $hub ?? null;
    $hubName = data_get($deliveryHub, 'hub_name') ?: data_get($deliveryHub, 'name') ?: 'Delivery Hub';
    // No financial records are changed from this UI. This sample ledger is not an actual payment settlement.
    $demoCollections = [
        ['ref'=>'SE-COD-4012','time'=>'2026-11-05 09:35','buyer'=>'Maria Santos','item'=>820.00,'shipping'=>50.00,'discount'=>0.00,'state'=>'Collected'],
        ['ref'=>'SE-COD-4013','time'=>'2026-11-05 10:10','buyer'=>'Joshua Reyes','item'=>1250.00,'shipping'=>50.00,'discount'=>100.00,'state'=>'Collected'],
        ['ref'=>'SE-COD-4014','time'=>'2026-11-05 11:25','buyer'=>'Angela Cruz','item'=>490.00,'shipping'=>50.00,'discount'=>0.00,'state'=>'Collected'],
        ['ref'=>'SE-COD-4015','time'=>'2026-11-05 12:00','buyer'=>'Carlo Mendoza','item'=>670.00,'shipping'=>50.00,'discount'=>20.00,'state'=>'Collected'],
        ['ref'=>'SE-COD-4016','time'=>'2026-11-05 12:18','buyer'=>'Sofia Ramirez','item'=>950.00,'shipping'=>50.00,'discount'=>50.00,'state'=>'Collected'],
        ['ref'=>'SE-COD-4017','time'=>'2026-11-05 12:42','buyer'=>'Daniel Navarro','item'=>1140.00,'shipping'=>50.00,'discount'=>0.00,'state'=>'Collected'],
        ['ref'=>'SE-COD-4018','time'=>'2026-11-05 13:05','buyer'=>'Bea Castillo','item'=>780.00,'shipping'=>50.00,'discount'=>80.00,'state'=>'Collected'],
        ['ref'=>'SE-COD-4019','time'=>'2026-11-05 13:26','buyer'=>'Miguel Torres','item'=>1640.00,'shipping'=>50.00,'discount'=>140.00,'state'=>'Collected'],
        ['ref'=>'SE-COD-4020','time'=>'2026-11-05 14:10','buyer'=>'Alyssa Mendoza','item'=>620.00,'shipping'=>50.00,'discount'=>0.00,'state'=>'Collected'],
        ['ref'=>'SE-COD-4021','time'=>'2026-11-05 14:38','buyer'=>'Rafael Garcia','item'=>1320.00,'shipping'=>50.00,'discount'=>120.00,'state'=>'Collected'],

    ];
@endphp
<!DOCTYPE html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
<title>Delivery Hub Finance — ShopEase</title>
<!-- SHOP EASE FINANCE CLEAN VERSION 2026-11-05 : NO HEADER OR TODAY ACTIVITY -->
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
.dhf-main,.dhf-main *{box-sizing:border-box}.dhf-main{font-family:Poppins,sans-serif;color:#211b1b;padding:25px 28px 50px}.dhf-shell{max-width:1500px;margin:auto}.dhf-top{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:18px}.dhf-top h1{font-size:25px;margin:0;font-weight:650}.dhf-top p{color:#988581;font-size:13px;margin:6px 0 0}.dhf-tag{background:#f9eff0;color:#96263d;border-radius:30px;padding:8px 13px;font-size:11px;font-weight:600}.dhf-card{background:white;border:1px solid #eddfdc;border-radius:17px;box-shadow:0 2px 12px #40201a07;min-width:0}.dhf-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:15px;margin-bottom:18px}.dhf-kpi{padding:21px}.dhf-kpi small{display:block;font-size:12px;color:#948581}.dhf-kpi strong{display:block;font-size:25px;font-weight:650;margin:9px 0}.dhf-kpi p{margin:0;color:#a39490;font-size:11px;line-height:1.5}.dhf-section{margin-bottom:19px}.dhf-head{padding:21px 22px 12px;display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap}.dhf-head h2{font-size:17px;font-weight:650;margin:0}.dhf-head p{font-size:12px;color:#988b88;margin:5px 0 0}.dhf-btn{background:#85212c;border:1px solid #85212c;border-radius:9px;color:#fff;font-family:inherit;font-weight:600;font-size:12px;padding:11px 17px;cursor:pointer}.dhf-btn:hover{background:#6e1722}.dhf-btn:disabled{opacity:.48;cursor:not-allowed}.dhf-btn.ghost{background:white;color:#85212c;border-color:#ead2d0}.dhf-btn.ghost:hover{background:#fff4f2}.dhf-controls{display:flex;align-items:end;gap:11px;flex-wrap:wrap;padding:7px 22px 15px}.dhf-field{display:flex;flex-direction:column;gap:5px;font-size:11px;color:#806f6b}.dhf-field input{font-family:inherit;border:1px solid #e4d8d4;border-radius:9px;height:40px;padding:0 11px;font-size:12px;outline:none;background:white}.dhf-field input:focus{border-color:#a94b5c;box-shadow:0 0 0 3px #b43b4b12}.dhf-search{flex:1;min-width:190px}.dhf-tablewrap{overflow-x:auto;padding:0 22px}.dhf-table{width:100%;border-collapse:collapse;min-width:800px}.dhf-table th{text-align:left;color:#9a8985;font-size:11px;font-weight:600;padding:13px 10px;border-bottom:1px solid #f0e6e2}.dhf-table td{padding:15px 10px;border-bottom:1px solid #f6efec;font-size:12px;color:#3d3030}.dhf-table td small{display:block;color:#a39490;font-size:10px;margin-top:4px}.dhf-table tbody tr[data-ref]{cursor:pointer}.dhf-table tbody tr[data-ref]:hover{background:#fff7f5}.dhf-table tbody tr.selected{background:#fff1ee}.dhf-table tbody tr:focus-visible{outline:2px solid #a51f42;outline-offset:-2px}.dhf-money{text-align:right;white-space:nowrap}.dhf-badge{display:inline-flex;padding:5px 9px;border-radius:50px;font-size:10px;font-weight:600;background:#edf8f2;color:#2a8a5d}.dhf-badge.pending{background:#fff4e8;color:#b87524}.dhf-badge.distributed{background:#eff4ff;color:#4165a1}.dhf-footrow{padding:15px 22px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}.dhf-footrow small{font-size:11px;color:#988985}.dhf-pager{display:flex;gap:5px}.dhf-pager button{width:31px;height:31px;border:1px solid #eadbd8;background:white;border-radius:8px;cursor:pointer;color:#84212d}.dhf-pager button.active{background:#84212d;color:white}.dhf-split{display:grid;grid-template-columns:minmax(0,1.6fr) minmax(320px,.85fr);gap:18px;margin-bottom:20px;align-items:start}.dhf-wallet{background:linear-gradient(125deg,#641017,#a74348);color:white;border-radius:17px;padding:23px;margin-bottom:15px;position:relative;overflow:hidden}.dhf-wallet:after{content:'';position:absolute;width:160px;height:160px;border-radius:50%;background:#ffffff10;right:-55px;top:-55px}.dhf-wallet small{color:#f4d6d5;font-size:12px}.dhf-wallet strong{display:block;font-size:31px;margin:12px 0 8px}.dhf-wallet p{font-size:11px;color:#f5d9d9;margin:0 0 17px}.dhf-wallet button{position:relative;z-index:1;background:white;color:#7b1b26;border:0;border-radius:8px;padding:10px 18px;font-weight:700;cursor:pointer}.dhf-totalbox{padding:19px 21px}.dhf-totalbox .line{display:flex;justify-content:space-between;gap:10px;padding:11px 0;border-bottom:1px solid #f0e7e3;font-size:12px}.dhf-totalbox .line:last-child{border:0}.dhf-totalbox .line strong{font-weight:650}.dhf-totalbox .amount{color:#9b2342;font-size:20px}.dhf-preview{padding:0 21px 20px}.dhf-preview .line{display:flex;justify-content:space-between;gap:10px;padding:11px 0;border-bottom:1px solid #f3e9e6;font-size:12px}.dhf-preview .line strong{font-weight:650}.dhf-preview .line:last-child{border:0}.dhf-actions{display:flex;gap:9px;flex-wrap:wrap;margin-top:15px}.dhf-info{color:#998783;font-size:11px;line-height:1.6;margin-top:12px}.dhf-historybar{display:flex;gap:10px;align-items:center;flex-wrap:wrap}.dhf-modal{position:fixed;inset:0;background:#1b0809a8;z-index:9999;display:none;align-items:center;justify-content:center;padding:18px}.dhf-modal.open{display:flex}.dhf-dialog{background:white;width:min(460px,100%);padding:25px;border-radius:17px;box-shadow:0 20px 65px #0003}.dhf-dialog h3{font-size:20px;margin:0 0 6px}.dhf-dialog p{font-size:12px;color:#978783;line-height:1.6}.dhf-dialog input{width:100%;height:43px;border:1px solid #e6d6d3;border-radius:9px;padding:0 13px;font-size:14px;margin:6px 0 12px}.dhf-dialog .dhf-actions{justify-content:flex-end}.dhf-status{font-size:12px;color:#875d35;min-height:18px}.dhf-muted{color:#a58e8b;font-size:11px}.dhf-history-kpis{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:13px;margin:0 22px 14px}.dhf-history-kpis>div{background:#fbf7f6;border:1px solid #f1e6e3;border-radius:11px;padding:15px}.dhf-history-kpis small{display:block;color:#978784;font-size:11px}.dhf-history-kpis strong{display:block;margin-top:7px;font-size:20px}.dhf-notice{background:#fff8f0;border:1px solid #f2e2ce;color:#986c34;border-radius:10px;padding:10px 14px;font-size:11px;margin-bottom:18px}.dhf-reset{border:0;background:transparent;font-size:20px;cursor:pointer;border-radius:8px;padding:6px 9px}.dhf-reset:hover{background:#fff0ec}.dhf-reset.spin svg{animation:dhf-spin .55s ease}@keyframes dhf-spin{to{transform:rotate(360deg)}}@media(max-width:1250px){.dhf-split{grid-template-columns:1fr}.dhf-kpis{grid-template-columns:repeat(2,1fr)}}@media(max-width:650px){.dhf-main{padding:16px!important}.dhf-kpis,.dhf-history-kpis{grid-template-columns:1fr}.dhf-controls{align-items:stretch}.dhf-field{flex:1}.dhf-search{flex-basis:100%}.dhf-top h1{font-size:21px}}

/* Delivered table: show up to ten demo orders in the first page to align with the adjacent finance panel. */
.dhf-split .dhf-table tbody tr{min-height:50px}
.dhf-split .dhf-table tbody td{padding-top:12px;padding-bottom:12px}
/* Finance refinements: compact responsive settlement readiness */
.dhf-queue{padding:17px 22px 21px;border-top:1px solid #f0e7e4;background:linear-gradient(180deg,#fff,#fdfaf9);border-radius:0 0 17px 17px}
.dhf-queue-top{display:flex;align-items:center;justify-content:space-between;gap:12px}
.dhf-queue-top h3{font-size:13px;font-weight:600;margin:0;color:#332426}
.dhf-queue-top p{font-size:11px;color:#9c8985;margin:4px 0 0}
.dhf-queue-track{height:8px;background:#f2e8e5;border-radius:999px;overflow:hidden;margin:18px 0 14px}
.dhf-queue-track>span{display:block;height:100%;width:0;background:linear-gradient(90deg,#8c2030,#be5966);border-radius:inherit;transition:width .3s ease}
.dhf-queue-meta{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
.dhf-queue-meta small{display:block;color:#a18d88;font-size:10px;margin-bottom:6px}
.dhf-queue-meta strong{display:block;color:#34282a;font-size:14px;font-weight:600}
.dhf-pin-dialog,.dhf-cashin-dialog{width:min(480px,100%);padding:29px}
.dhf-lock-mark{width:48px;height:48px;display:grid;place-items:center;border-radius:13px;background:#f9eff0;color:#85212c;margin-bottom:17px}
.dhf-lock-mark svg{width:24px;height:24px}.dhf-step{display:block;color:#a43a4b;font-size:10px;letter-spacing:1.3px;font-weight:700;margin-bottom:7px}
.dhf-pin-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:8px;margin:24px 0 6px}
.dhf-dialog .dhf-pin-cell{margin:0!important;padding:0!important;width:100%;height:56px;text-align:center;font-size:23px;font-weight:600;border-radius:10px;letter-spacing:0}
.dhf-dialog .dhf-pin-cell:focus{outline:2px solid #a23a4b;outline-offset:1px}
.dhf-modal-footnote{border-top:1px solid #f1e9e6;margin-top:20px;padding-top:13px;color:#ad9b96;font-size:10px;line-height:1.65}
.dhf-cashin-header{display:flex;align-items:center;gap:14px;margin-bottom:22px}.dhf-cashin-symbol{display:grid;place-items:center;width:51px;height:51px;background:#faeff1;color:#8a2032;border-radius:14px;font-size:25px;font-weight:600;flex:0 0 51px}
.dhf-cashin-header h3{margin:0;font-weight:600;font-size:20px}.dhf-cashin-header p{margin:5px 0 0}
.dhf-cashin-balance{display:flex;justify-content:space-between;gap:12px;align-items:center;background:#fcf8f7;border:1px solid #f0e5e1;border-radius:12px;padding:16px;margin-bottom:21px;color:#8c7b78;font-size:11px}
.dhf-cashin-balance strong{color:#312427;font-size:19px;font-weight:600}
.dhf-amount-field{font-size:12px!important;color:#554443!important;font-weight:600}
.dhf-currency-field{position:relative;margin-top:6px}.dhf-currency-field>span{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#9c8485;font-size:19px;font-weight:600}
.dhf-dialog .dhf-currency-field input{font-size:24px;font-weight:600;height:58px;padding:0 16px 0 40px;margin:0;appearance:textfield}
.dhf-cashin-after{display:flex;justify-content:space-between;gap:15px;margin:17px 0 20px;font-size:12px;color:#968380}.dhf-cashin-after strong{color:#1e754f;font-size:16px}
@media(max-width:650px){.dhf-queue-meta{gap:8px}.dhf-queue-meta strong{font-size:12px}.dhf-pin-dialog,.dhf-cashin-dialog{padding:21px}.dhf-dialog .dhf-pin-cell{height:47px;font-size:19px}.dhf-pin-grid{gap:5px}}

/* Unified neutral-gray focus state for every Finance input, including modal PIN cells. */
.dhf-main input:focus,
.dhf-main input:focus-visible,
.dhf-main select:focus,
.dhf-main textarea:focus,
.dhf-modal input:focus,
.dhf-modal input:focus-visible,
.dhf-modal select:focus,
.dhf-modal textarea:focus,
.dhf-dialog .dhf-pin-cell:focus {
  outline: none !important;
  border-color: #9ca3af !important;
  box-shadow: 0 0 0 3px rgba(156,163,175,.17) !important;
}
.dhf-main .dhf-table tbody tr:focus-visible {
  outline: 2px solid #9ca3af;
  outline-offset: -2px;
}
.dhf-split { align-items: stretch; }
.dhf-split > .dhf-card:first-child { display:flex; flex-direction:column; }

/* All search boxes are deliberately not <input> fields: prevents Chrome from
   restoring autofilled email addresses, while keeping normal text filtering. */
.dhf-field .dhf-history-searchbox{
  display:block;width:100%;height:40px;min-height:40px;
  padding:10px 11px;border:1px solid #e4d8d4;border-radius:9px;
  background:#fff;font-family:Poppins,Arial,sans-serif;font-size:12px;
  font-weight:400;line-height:18px;color:#30282a;
  outline:none;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  cursor:text;
}
.dhf-field .dhf-history-searchbox:empty::before{
  content:attr(data-placeholder);color:#a29a99;pointer-events:none;
}
.dhf-field .dhf-history-searchbox:focus{
  border-color:#a9a9ad;box-shadow:0 0 0 3px rgba(125,125,135,.12);
}
</style></head><body class="lg-body">
@include('components.logistics.delivery-hub.sidebar')
@include('components.logistics.delivery-hub.navbar')
<!-- SEARCH_FIELDS_BLANK_V4: both Today and History search boxes have zero initial text -->
<main class="lg-main dhf-main"><div class="dhf-shell">
<section class="dhf-kpis" aria-label="Today's overview">
<div class="dhf-card dhf-kpi"><small>Delivered Today</small><strong id="today-count">0</strong><p>Eligible COD orders delivered today</p></div>
<div class="dhf-card dhf-kpi"><small>Today's Collection Due</small><strong id="today-due">₱0.00</strong><p>Total payment due for today's delivered orders</p></div>
<div class="dhf-card dhf-kpi"><small>Distributed Today</small><strong id="today-distributed">₱0.00</strong><p>Completed distributions in this preview</p></div>
<div class="dhf-card dhf-kpi"><small>Remaining to Distribute</small><strong id="today-remaining">₱0.00</strong><p>Today's eligible orders not yet distributed</p></div>
</section>
<section class="dhf-split">
<div class="dhf-card"><div class="dhf-head"><div><h2>Delivered Parcels Today</h2><p>Only today's delivered COD orders · Click any row to see its distribution preview</p></div><span class="dhf-tag" id="today-label">Today</span></div>
<div class="dhf-controls"><label class="dhf-field dhf-search">Search today's orders<span id="today-search" class="dhf-history-searchbox" role="searchbox" aria-label="Search today's orders" data-placeholder="" contenteditable="true" spellcheck="false" autocapitalize="off" autocorrect="off"></span></label><button id="distribute-all" class="dhf-btn" type="button">Distribute All Eligible</button></div>
<div class="dhf-tablewrap"><table class="dhf-table"><thead><tr><th>Delivered at</th><th>Order</th><th>Buyer</th><th>COD Amount</th><th>Status</th></tr></thead><tbody id="today-rows"></tbody></table></div>
<div class="dhf-footrow"><small id="today-page-text"></small><div class="dhf-pager" id="today-pager"></div></div>
<div class="dhf-queue"><div class="dhf-queue-top"><div><h3>Today's Settlement Readiness</h3><p>Cash required versus your available wallet funds</p></div><span class="dhf-badge pending" id="readiness-badge">Awaiting Cash In</span></div>
<div class="dhf-queue-track"><span id="readiness-fill"></span></div>
<div class="dhf-queue-meta"><div><small>Wallet coverage</small><strong id="readiness-percent">0%</strong></div><div><small>Orders awaiting distribution</small><strong id="queue-orders">0</strong></div><div><small>Remaining required</small><strong id="queue-amount">₱0.00</strong></div></div></div>
</div>
<aside><div class="dhf-wallet"><small>Available Wallet Balance</small><strong id="wallet-balance">₱0.00</strong><p>Cash-ins minus distributed settlements in this preview</p><button id="cashin-open" type="button">+ Cash In</button></div>
<div class="dhf-card dhf-totalbox"><div class="line"><span>Total to Distribute Today</span><strong class="amount" id="total-required">₱0.00</strong></div><div class="line"><span>Available wallet</span><strong id="available-inline">₱0.00</strong></div><div class="line"><span>Still needed for today's distribution</span><strong id="shortfall">₱0.00</strong></div></div>
<div class="dhf-card" style="margin-top:15px"><div class="dhf-head"><div><h2>Distribution Preview</h2><p>Click a delivered order in the table</p></div></div><div class="dhf-preview">
<div class="line"><span>Order</span><strong id="preview-ref">—</strong></div><div class="line"><span>Seller</span><strong id="preview-seller">₱0.00</strong></div><div class="line"><span>Admin (sample 10%)</span><strong id="preview-admin">₱0.00</strong></div><div class="line"><span>Logistics (₱50 shipping)</span><strong id="preview-logistics">₱0.00</strong></div><div class="line"><span>Total distribution</span><strong id="preview-total">₱0.00</strong></div><div class="dhf-actions"><button class="dhf-btn" id="distribute-one" type="button">Distribute Selected</button></div><p class="dhf-info" id="action-message" role="status">No order selected for distribution</p></div></div></aside>
</section>
<section class="dhf-card dhf-section"><div class="dhf-head"><div><h2>Distribution History</h2><p>All dated delivery and distribution records · Filter to review a specific period</p></div></div>
<div class="dhf-controls"><label class="dhf-field">From<input id="history-from" type="date"></label><label class="dhf-field">To<input id="history-to" type="date"></label><label class="dhf-field dhf-search">Search history<span id="history-search" class="dhf-history-searchbox" role="searchbox" aria-label="Search history" aria-placeholder="Order reference or buyer" data-placeholder="Order reference or buyer" contenteditable="true" spellcheck="false" autocapitalize="off" autocorrect="off"></span></label><button id="history-reset" type="button" class="dhf-reset" aria-label="Reset history filters" title="Reset filters"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11a8 8 0 1 1-2.3-5.7M20 4v6h-6"/></svg></button></div>
<div class="dhf-history-kpis"><div><small>History Orders</small><strong id="history-count">0</strong></div><div><small>Total COD Value</small><strong id="history-total">₱0.00</strong></div><div><small>Distributed Amount</small><strong id="history-distributed">₱0.00</strong></div></div>
<div class="dhf-tablewrap"><table class="dhf-table"><thead><tr><th>Delivery Date</th><th>Order</th><th>Buyer</th><th>COD Amount</th><th>Distribution Status</th></tr></thead><tbody id="history-rows"></tbody></table></div><div class="dhf-footrow"><small id="history-page-text"></small><div class="dhf-pager" id="history-pager"></div></div></section>
</div></main>
<div class="dhf-modal" id="pin-modal" role="dialog" aria-modal="true" aria-labelledby="pin-title">
  <div class="dhf-dialog dhf-pin-dialog">
    <div class="dhf-lock-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></div>
    <div class="dhf-step">WALLET ACCESS</div>
    <h3 id="pin-title">Set up Wallet PIN</h3>
    <p id="pin-instructions">Create a 6-digit PIN for this preview session. You will confirm it before cashing in.</p>
    <div class="dhf-pin-grid" id="pin-boxes" role="group" aria-label="Six-digit PIN entry">
      <input class="dhf-pin-cell" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="off" aria-label="PIN digit 1">
      <input class="dhf-pin-cell" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="off" aria-label="PIN digit 2">
      <input class="dhf-pin-cell" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="off" aria-label="PIN digit 3">
      <input class="dhf-pin-cell" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="off" aria-label="PIN digit 4">
      <input class="dhf-pin-cell" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="off" aria-label="PIN digit 5">
      <input class="dhf-pin-cell" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="off" aria-label="PIN digit 6">
    </div>
    <p class="dhf-status" id="pin-error" role="status" aria-live="polite"></p>
    <div class="dhf-actions"><button class="dhf-btn ghost" type="button" data-close>Cancel</button><button class="dhf-btn" type="button" id="pin-next">Continue</button></div>
    <div class="dhf-modal-footnote">This PIN is not stored or verified by a server; it resets when the page reloads.</div>
  </div>
</div>
<div class="dhf-modal" id="cashin-modal" role="dialog" aria-modal="true" aria-labelledby="cashin-title">
  <div class="dhf-dialog dhf-cashin-dialog">
    <div class="dhf-cashin-header"><div class="dhf-cashin-symbol">₱</div><div><span class="dhf-step">DELIVERY HUB WALLET</span><h3 id="cashin-title">Cash In</h3><p>Add collected cash to the wallet preview balance.</p></div></div>
    <div class="dhf-cashin-balance"><span>Current Wallet Balance</span><strong id="cashin-current-balance">₱0.00</strong></div>
    <label class="dhf-field dhf-amount-field" for="cashin-amount">Amount to Cash In (PHP)<div class="dhf-currency-field"><span>₱</span><input id="cashin-amount" type="number" min="0.01" step="0.01" placeholder="0.00" inputmode="decimal" autocomplete="off"></div></label>
    <div class="dhf-cashin-after"><span>Balance after Cash In</span><strong id="cashin-after-balance">₱0.00</strong></div>
    <p class="dhf-status" id="cashin-error" role="status" aria-live="polite"></p>
    <div class="dhf-actions"><button class="dhf-btn ghost" type="button" data-close>Cancel</button><button class="dhf-btn" type="button" id="cashin-confirm">Confirm Cash In</button></div>
    <div class="dhf-modal-footnote">UI simulation only. This does not record a deposit or create funds in a live wallet.</div>
  </div>
</div>
<script id="dhf-data" type="application/json">@json($demoCollections)</script>
<script>
(()=>{const run=()=>{
const $=id=>document.getElementById(id),money=n=>'₱'+Number(n||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});
const fmt=new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Manila',year:'numeric',month:'2-digit',day:'2-digit'});const parts=fmt.formatToParts(new Date());const today=[parts.find(x=>x.type==='year').value,parts.find(x=>x.type==='month').value,parts.find(x=>x.type==='day').value].join('-');
const sample=JSON.parse($('dhf-data').textContent||'[]');const records=sample.map((r,i)=>({...r,time:today+' '+r.time.slice(11),distributed:false}));
// Historical examples are displayed separately from today's orders; no actual database reads or writes.
records.push(...[
{ref:'SE-COD-3981',time:'2026-11-04 14:12',buyer:'Lea Flores',item:730,shipping:50,discount:30,state:'Delivered',distributed:true},
{ref:'SE-COD-3972',time:'2026-11-03 16:40',buyer:'Mark Dela Cruz',item:1290,shipping:50,discount:0,state:'Delivered',distributed:true},
{ref:'SE-COD-3966',time:'2026-11-02 12:05',buyer:'Rina Garcia',item:560,shipping:50,discount:0,state:'Delivered',distributed:true},
{ref:'SE-COD-3950',time:'2026-10-30 10:15',buyer:'Paolo Ramos',item:970,shipping:50,discount:70,state:'Delivered',distributed:false}
]);
const cod=r=>Math.max(0,Number(r.item)-Number(r.discount))+Number(r.shipping);const todayRecords=records.filter(r=>r.time.startsWith(today));let balance=0,selected=todayRecords[0]||null,todayPage=1,historyPage=1;const perPage=6;const todayPerPage=8;let walletPin=null, pendingPin=null, pinMode='setup';
function showModal(id){$(id).classList.add('open');const field=$(id).querySelector('input');field?.focus()}function hideModals(){document.querySelectorAll('.dhf-modal').forEach(x=>x.classList.remove('open'))}
document.querySelectorAll('[data-close]').forEach(b=>b.onclick=hideModals);document.querySelectorAll('.dhf-modal').forEach(m=>m.onclick=e=>{if(e.target===m)hideModals()});document.addEventListener('keydown',e=>{if(e.key==='Escape')hideModals()});
function renderRows(target,rows,mode,page){const tbody=$(target);tbody.replaceChildren();const size=mode==='today'?todayPerPage:perPage;const visible=rows.slice((page-1)*size,page*size);if(!visible.length){const tr=document.createElement('tr');const td=document.createElement('td');td.colSpan=5;td.textContent='No matching records';tr.appendChild(td);tbody.appendChild(tr)}
visible.forEach(r=>{const tr=document.createElement('tr');if(mode==='today'){tr.dataset.ref=r.ref;tr.tabIndex=0;if(selected?.ref===r.ref)tr.className='selected';const choose=()=>{selected=r;render()};tr.onclick=choose;tr.onkeydown=e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();choose()}}}const cells=[r.time,r.ref,r.buyer,money(cod(r)),r.distributed?'Distributed':'Ready for distribution'];cells.forEach((v,i)=>{const td=document.createElement('td');td.textContent=v;if(i===4){const b=document.createElement('span');b.className='dhf-badge '+(r.distributed?'distributed':'pending');b.textContent=v;td.replaceChildren(b)}tr.appendChild(td)});tbody.appendChild(tr)})}
function pager(id,page,count,set,size=perPage){const root=$(id);root.replaceChildren();for(let i=1;i<=Math.max(1,Math.ceil(count/size));i++){const b=document.createElement('button');b.textContent=i;b.className=i===page?'active':'';b.type='button';b.onclick=()=>{set(i);render()};root.appendChild(b)}}
function render(){const pending=todayRecords.filter(r=>!r.distributed);const due=todayRecords.reduce((a,r)=>a+cod(r),0);const distributed=todayRecords.filter(r=>r.distributed).reduce((a,r)=>a+cod(r),0);const remaining=pending.reduce((a,r)=>a+cod(r),0);
$('queue-orders').textContent=pending.length;
$('queue-amount').textContent=money(remaining);
const coverage=remaining>0?Math.min(100,Math.round(balance/remaining*100)):100;
$('readiness-percent').textContent=coverage+'%';
$('readiness-fill').style.width=coverage+'%';
$('readiness-badge').textContent=pending.length===0?'All Distributed':(balance+0.001>=remaining?'Ready to Distribute':'Cash In Required');
$('readiness-badge').className='dhf-badge '+(pending.length===0?'distributed':balance+0.001>=remaining?'':'pending');
$('today-label').textContent=today;$('today-count').textContent=todayRecords.length;$('today-due').textContent=money(due);$('today-distributed').textContent=money(distributed);$('today-remaining').textContent=money(remaining);$('total-required').textContent=money(remaining);$('wallet-balance').textContent=money(balance);$('available-inline').textContent=money(balance);$('shortfall').textContent=money(Math.max(0,remaining-balance));
const trows=todayRecords.filter(r=>(r.ref+' '+r.buyer).toLowerCase().includes($('today-search').textContent.toLowerCase()));todayPage=Math.min(todayPage,Math.max(1,Math.ceil(trows.length/todayPerPage)));renderRows('today-rows',trows,'today',todayPage);pager('today-pager',todayPage,trows.length,n=>todayPage=n,todayPerPage);$('today-page-text').textContent=trows.length+' delivered orders today';
$('preview-ref').textContent=selected?.ref||'—';const item=selected?Math.max(0,Number(selected.item)-Number(selected.discount)):0;const admin=Math.round(item*0.1*100)/100;$('preview-seller').textContent=money(item-admin);$('preview-admin').textContent=money(admin);$('preview-logistics').textContent=money(selected?.shipping||0);$('preview-total').textContent=money(selected?cod(selected):0);
$('distribute-one').disabled=!selected||selected.distributed||balance+0.001<cod(selected);$('distribute-all').disabled=!pending.length||balance+0.001<remaining;
const from=$('history-from').value,to=$('history-to').value,q=historySearch.textContent.toLowerCase();const hrows=records.filter(r=>(!from||r.time.slice(0,10)>=from)&&(!to||r.time.slice(0,10)<=to)&&(r.ref+' '+r.buyer).toLowerCase().includes(q)).sort((a,b)=>b.time.localeCompare(a.time));historyPage=Math.min(historyPage,Math.max(1,Math.ceil(hrows.length/perPage)));renderRows('history-rows',hrows,'history',historyPage);pager('history-pager',historyPage,hrows.length,n=>historyPage=n);$('history-page-text').textContent=hrows.length+' records';$('history-count').textContent=hrows.length;$('history-total').textContent=money(hrows.reduce((a,r)=>a+cod(r),0));$('history-distributed').textContent=money(hrows.filter(r=>r.distributed).reduce((a,r)=>a+cod(r),0));}
$('history-from').value=today.slice(0,7)+'-01';$('history-to').value=today;
// Search fields intentionally use contenteditable elements instead of input fields:
// Chrome password/email autofill and form-value restoration cannot populate these.
// Both start completely empty, with no initial email or default search text.
const historySearch = $('history-search');
const todaySearch = $('today-search');
const searchFields = [todaySearch, historySearch];
let hasUserTypedSearch = false;
function clearInitialSearches() {
  if (hasUserTypedSearch) return;
  searchFields.forEach(field => {
    field.replaceChildren();
    field.textContent = '';
  });
  todayPage = 1;
  historyPage = 1;
  render();
}
searchFields.forEach(field => {
  field.textContent = '';
  field.removeAttribute('data-placeholder');
  field.setAttribute('aria-placeholder', '');
  field.addEventListener('beforeinput', () => {hasUserTypedSearch = true;});
  field.addEventListener('keydown', event => {
    if (event.key === 'Enter') event.preventDefault();
    else if (event.key.length === 1 || event.key === 'Backspace' || event.key === 'Delete') hasUserTypedSearch = true;
  });
  field.addEventListener('paste', event => {
    hasUserTypedSearch = true;
    event.preventDefault();
    const plain = (event.clipboardData?.getData('text/plain') || '').replace(/[\r\n]+/g, ' ');
    document.execCommand('insertText', false, plain);
  });
  field.addEventListener('focus', () => {
    // Defensive clear if a browser extension inserted an email before user interaction.
    if (!hasUserTypedSearch && field.textContent.trim()) {
      field.textContent = '';
      render();
    }
  });
});
clearInitialSearches();
window.addEventListener('pageshow', clearInitialSearches);
// Some browser restoration/autofill executes just after DOM ready.
setTimeout(clearInitialSearches, 100);
setTimeout(clearInitialSearches, 550);
todaySearch.oninput=()=>{hasUserTypedSearch=true;todayPage=1;render()};['history-from','history-to','history-search'].forEach(id=>$(id).addEventListener(id==='history-search'?'input':'change',()=>{historyPage=1;render()}));$('history-reset').onclick=()=>{$('history-from').value=today.slice(0,7)+'-01';$('history-to').value=today;historySearch.textContent='';todaySearch.textContent='';todayPage=1;historyPage=1;$('history-reset').classList.remove('spin');void $('history-reset').offsetWidth;$('history-reset').classList.add('spin');render()};
// OTP-style PIN input is intentionally session-only and not a security boundary.
const pinCells=[...document.querySelectorAll('.dhf-pin-cell')];
function pinValue(){return pinCells.map(c=>c.value).join('')}
function clearPinFields(){pinCells.forEach(c=>c.value='');$('pin-error').textContent='';pinCells[0]?.focus()}
function pinStep(mode){
 pinMode=mode;
 const title={setup:'Set up Wallet PIN',confirm:'Confirm Wallet PIN',unlock:'Unlock Cash In'};
 const description={setup:'Create a 6-digit PIN to access Cash In.',confirm:'Re-enter the same 6-digit PIN to confirm.',unlock:'Enter your 6-digit wallet PIN to continue.'};
 $('pin-title').textContent=title[mode];$('pin-instructions').textContent=description[mode];
 $('pin-next').textContent=mode==='setup'?'Continue':mode==='confirm'?'Confirm PIN':'Unlock Wallet';
 clearPinFields();
}
pinCells.forEach((cell,index)=>{
 cell.addEventListener('input',()=>{const digits=cell.value.replace(/\D/g,'');if(digits.length>1){[...digits].slice(0,6-index).forEach((d,i)=>pinCells[index+i].value=d);pinCells[Math.min(5,index+digits.length)]?.focus()}else{cell.value=digits.slice(-1);if(cell.value&&index<5)pinCells[index+1].focus()}$('pin-error').textContent=''});
 cell.addEventListener('keydown',event=>{if(event.key==='Backspace'&&!cell.value&&index>0)pinCells[index-1].focus();if(event.key==='ArrowLeft'&&index>0)pinCells[index-1].focus();if(event.key==='ArrowRight'&&index<5)pinCells[index+1].focus();if(event.key==='Enter')$('pin-next').click()});
 cell.addEventListener('paste',event=>{const pasted=(event.clipboardData?.getData('text')||'').replace(/\D/g,'').slice(0,6);if(!pasted)return;event.preventDefault();pinCells.forEach((c,i)=>c.value=pasted[i]||'');pinCells[Math.min(pasted.length,5)].focus()});
});
function openCashinForm(){hideModals();$('cashin-amount').value='';$('cashin-error').textContent='';$('cashin-current-balance').textContent=money(balance);$('cashin-after-balance').textContent=money(balance);showModal('cashin-modal')}
$('cashin-open').onclick=()=>{pendingPin=null;pinStep(walletPin?'unlock':'setup');showModal('pin-modal')};
$('pin-next').onclick=()=>{
 const digits=pinValue();if(!/^\d{6}$/.test(digits)){$('pin-error').textContent='Enter all six digits.';return}
 if(pinMode==='setup'){pendingPin=digits;pinStep('confirm');return}
 if(pinMode==='confirm'){if(digits!==pendingPin){clearPinFields();$('pin-error').textContent='PINs do not match. Try again.';return}walletPin=digits;pendingPin=null;openCashinForm();return}
 if(digits!==walletPin){clearPinFields();$('pin-error').textContent='Incorrect PIN.';return}
 openCashinForm();
};
$('cashin-amount').addEventListener('input',()=>{const amount=Number($('cashin-amount').value);$('cashin-after-balance').textContent=money(balance+(Number.isFinite(amount)&&amount>0?amount:0));$('cashin-error').textContent=''});
$('cashin-confirm').onclick=()=>{const amount=Number($('cashin-amount').value);if(!Number.isFinite(amount)||amount<=0||Math.round(amount*100)!==amount*100){$('cashin-error').textContent='Enter a valid positive amount, up to two decimal places.';return}balance=Math.round((balance+amount)*100)/100;hideModals();$('action-message').textContent='Cash In of '+money(amount)+' reflected in the preview wallet. No live payment was processed.';render()};
function distribute(rows){const sum=rows.reduce((a,r)=>a+cod(r),0);if(!rows.length||balance+0.001<sum)return;balance=Math.round((balance-sum)*100)/100;rows.forEach(r=>r.distributed=true);$('action-message').textContent=rows.length+' order(s) marked distributed in the preview: '+money(sum)+'. No real wallet transfers occurred.';render()}
$('distribute-one').onclick=()=>selected&&distribute([selected]);$('distribute-all').onclick=()=>distribute(todayRecords.filter(r=>!r.distributed));render();
};document.readyState==='loading'?document.addEventListener('DOMContentLoaded',run):run()})();
</script></body></html>
