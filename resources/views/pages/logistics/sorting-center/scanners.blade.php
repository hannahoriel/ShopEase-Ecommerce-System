@php

    $scannerCenterId = (string) ($sortingCenterId ?? data_get(auth()->user(), 'sorting_center_id', ''));

    $scannerAccountId = (string) (auth()->id() ?? '');

    $scannerAccountEmail = (string) (auth()->user()?->email ?? '');

    $scannerCenterName = (string) ($sortingCenterName ?? data_get(auth()->user(), 'sorting_center_name') ?? auth()->user()?->name ?? 'Assigned Sorting Center');

@endphp

<!doctype html>

<html lang="en">

<head>

<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Scanners — ShopEase Sorting Center</title>

<link rel="preconnect" href="https\://fonts.googleapis.com"><link rel="preconnect" href="https\://fonts.gstatic.com" crossorigin>

<link href="https\://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>

:root{--lg-maroon:#7B1B1B;--lg-dark:#52070B;--lg-peach:#FFE6DE;--lg-bg:#FCF8F6;--lg-line:#F0E9E6;--lg-ink:#17120F;--lg-muted:#8C8784;--sc:#7b1b1b}

*{box-sizing:border-box}html,body{margin:0}body.lg-body{background:var(--lg-bg);color:var(--lg-ink);font-family:Poppins,Arial,sans-serif;overflow-x:hidden}button,input,select{font:inherit}button{cursor:pointer}a{text-decoration:none}

/* Shared sidebar/navbar geometry; actual components provide their own appearance/animations. */

.lg-main{margin-left:260px;padding:116px 25px 42px;transition:margin-left .3s ease;min-height:100vh}.lg-body.lg-collapsed .lg-main{margin-left:80px}

.sc-wrap{max-width:1580px;margin:auto}.sc-eyebrow{font-size:10px;letter-spacing:1.4px;font-weight:700;color:#9b1239;text-transform:uppercase;margin:0 0 6px}.sc-page-title{margin:0;font-size:25px;font-weight:700}.sc-sub{font-size:12px;color:#928681;line-height:1.7;margin:6px 0 0}.sc-head{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:21px}.sc-head-actions{display:flex;align-items:center;gap:9px;flex-wrap:wrap}.sc-select,.sc-input{min-height:40px;border:1px solid #e4d6d0;border-radius:9px;background:#fff;color:#433934;padding:9px 12px;font-size:12px;outline:none}.sc-input:focus,.sc-select:focus{border-color:#b48e89;box-shadow:0 0 0 3px #7b1b1b12}.sc-select{min-width:220px}.sc-btn{border:1px solid #d9c0ba;border-radius:9px;background:#fff;color:#742024;padding:10px 15px;font-size:12px;font-weight:600;transition:.2s ease;white-space:nowrap}.sc-btn:hover{background:#fff5f1}.sc-btn:focus-visible,.sc-tab:focus-visible{outline:3px solid #bd908a;outline-offset:2px}.sc-btn.primary{background:#7b1b1b;border-color:#7b1b1b;color:white}.sc-btn.primary:hover{background:#621415}.sc-btn:disabled{opacity:.45;cursor:not-allowed}.sc-btn.small{padding:7px 12px;font-size:11px}.sc-btn.danger{color:#a42c2c;border-color:#e6c9c5}.sc-notice{background:#fff8f1;border:1px solid #f0e0d5;border-radius:11px;padding:12px 15px;font-size:11px;color:#926841;line-height:1.7;margin-bottom:18px}.sc-tabs{display:flex;border-bottom:1px solid #eadfdb;gap:24px;margin-bottom:19px}.sc-tab{padding:13px 3px;border:0;border-bottom:3px solid transparent;background:none;color:#8b817d;font-weight:650;font-size:13px;display:flex;align-items:center;gap:8px}.sc-tab.active{color:#7b1b1b;border-bottom-color:#7b1b1b}.sc-chip{font-size:10px;border-radius:20px;background:#f9ece8;color:#832b2b;padding:3px 8px;font-weight:650}.sc-tab.active .sc-chip{background:#7b1b1b;color:white}.sc-view[hidden]{display:none!important}

.sc-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:13px;margin-bottom:18px}.sc-stat{border:1px solid #f0e7e2;background:#fff;border-radius:14px;padding:17px 19px;box-shadow:0 2px 12px #4a1d1806}.sc-stat span{font-size:11px;color:#968b86}.sc-stat strong{font-size:23px;display:block;color:#231b19;line-height:1.4;margin-top:4px}.sc-split{display:grid;grid-template-columns:minmax(350px,1.23fr) minmax(300px,.9fr);gap:17px;align-items:start;margin-bottom:18px}.sc-panel{border:1px solid #ede2dd;background:white;border-radius:15px;overflow:hidden;box-shadow:0 2px 10px #3a151508}.sc-panel-head{padding:19px 20px 13px;display:flex;justify-content:space-between;gap:12px;align-items:center}.sc-panel-head h2{font-size:15px;font-weight:650;margin:0}.sc-panel-head p{font-size:11px;color:#988b86;margin:5px 0 0;line-height:1.6}.sc-panel-body{padding:0 20px 20px}.sc-scan-row{display:flex;gap:9px}.sc-scan-row .sc-input{flex:1;min-width:0;height:48px;font-size:13px}.sc-scan-row .sc-btn{min-width:115px}.sc-help{font-size:10px;color:#9b908b;line-height:1.75;margin:11px 0 0}.sc-feedback{border:1px solid #e8dfdc;border-radius:11px;background:#fcfaf9;padding:18px;min-height:152px;display:flex;align-items:center}.sc-feedback strong{display:block;font-size:16px;margin:0 0 6px}.sc-feedback small{font-size:11px;color:#8a7c77;display:block;line-height:1.7}.sc-feedback.good{border-color:#c7e7d3;background:#f4fbf6}.sc-feedback.bad{border-color:#efc1c2;background:#fff5f4}.sc-feedback.warn{border-color:#efddae;background:#fffbef}.sc-feedback .sc-route{margin-top:9px;padding:8px 10px;background:white;border-radius:8px;color:#6d171a;font-size:12px;font-weight:650;display:inline-block}.sc-feedback .sc-big{font-size:12px;color:#735c56;margin-bottom:5px}.sc-table-wrap{overflow:auto;scrollbar-width:none}.sc-table-wrap::-webkit-scrollbar{display:none}.sc-table{border-collapse:collapse;width:100%;min-width:660px}.sc-table th{color:#8f807b;background:#fdfaf8;font-size:10px;text-transform:none;font-weight:600;text-align:left;padding:13px 16px;border-bottom:1px solid #eee5e0}.sc-table td{padding:15px 16px;border-bottom:1px solid #f3ebe8;font-size:11px;color:#443a36;vertical-align:middle}.sc-table tbody tr:last-child td{border-bottom:none}.sc-table tbody tr:hover{background:#fffbf9}.sc-table strong{font-weight:650;font-size:11px;color:#322523}.sc-table small{display:block;color:#9c8f89;margin-top:3px}.sc-empty{text-align:center;color:#a09590!important;padding:35px 15px!important}.sc-badge{display:inline-block;background:#f4ece9;color:#7d5753;border-radius:30px;font-size:10px;padding:5px 9px;white-space:nowrap}.sc-badge.incoming{background:#fdf0db;color:#a46b18}.sc-badge.received{background:#eaf8ee;color:#2b8048}.sc-badge.loaded{background:#eaf0fb;color:#3264aa}.sc-toolbar{display:flex;align-items:center;gap:10px;padding:0 20px 15px;flex-wrap:wrap}.sc-toolbar .sc-input{flex:1;min-width:210px}.sc-footer{padding:12px 17px;color:#9c8f89;font-size:10px;border-top:1px solid #f2ebe8}.sc-layout{display:grid;gap:18px}.sc-groups{display:grid;gap:14px}.sc-group{border:1px solid #eee1db;border-radius:13px;background:white;overflow:hidden}.sc-group-top{display:flex;align-items:start;justify-content:space-between;gap:12px;padding:17px 18px 10px}.sc-group-title{font-size:13px;font-weight:650;margin:0;color:#3c2927}.sc-group-sub{font-size:11px;color:#978984;margin-top:5px}.sc-group-actions{display:flex;align-items:center;gap:9px;flex-wrap:wrap}.sc-group-count{background:#f7eeea;padding:6px 9px;color:#7b1b1b;font-size:11px;font-weight:650;border-radius:8px}.sc-group-list{padding:0 18px 12px}.sc-group-item{font-size:11px;padding:9px 0;display:flex;justify-content:space-between;gap:9px;border-bottom:1px solid #f6efeb}.sc-group-item:last-child{border:none}.sc-group-item strong{font-weight:600}.sc-group-item small{color:#958a86}.sc-groups-empty{border:1px dashed #e2d4cf;border-radius:12px;padding:25px;text-align:center;color:#8f807b;font-size:12px;background:#fff}.sc-activity{padding:0 18px 16px}.sc-activity-row{padding:12px 0;border-bottom:1px solid #f2ebe7;display:flex;align-items:start;justify-content:space-between;gap:13px}.sc-activity-row:last-child{border:0}.sc-activity-row strong{font-size:11px}.sc-activity-row small{font-size:10px;color:#998c85;display:block;margin-top:4px}.sc-activity-row time{white-space:nowrap;font-size:10px;color:#a1938e}.sc-activity-empty{font-size:11px;color:#9d8e87;padding:18px 0}.sc-legend{display:flex;gap:12px;flex-wrap:wrap;font-size:10px;color:#988c86}.sc-legend span{display:flex;align-items:center;gap:5px}.sc-dot{width:7px;height:7px;background:#a96565;border-radius:50%}.sc-dot.green{background:#38955c}.sc-dot.blue{background:#3f83bc}.sc-toast{position:fixed;bottom:22px;right:22px;z-index:300;background:#fff;border-radius:12px;border:1px solid #e9dad4;border-left:4px solid #7b1b1b;box-shadow:0 14px 35px #3215152d;padding:14px 17px;max-width:360px;font-size:12px;line-height:1.6}.sc-toast[hidden]{display:none}.sc-modal{position:fixed;inset:0;z-index:280;background:#2b131394;display:grid;place-items:center;padding:20px}.sc-modal[hidden]{display:none}.sc-dialog{width:min(480px,100%);background:#fff;border-radius:15px;padding:23px}.sc-dialog h2{font-size:18px;margin:0 0 8px}.sc-dialog p{font-size:12px;color:#80716c;line-height:1.7}.sc-dialog-actions{display:flex;justify-content:flex-end;gap:9px;margin-top:18px}.sc-demo-warning{font-size:11px;color:#99857d;margin-top:9px}.sc-filter-bar{display:flex;gap:8px;flex-wrap:wrap}.sc-mini-note{padding:12px 15px;border:1px solid #eadfdb;background:#fff;border-radius:10px;color:#7c6d66;font-size:11px;line-height:1.7;margin-bottom:17px}

@media(max-width:1180px){.sc-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.sc-split{grid-template-columns:1fr}}

@media(max-width:900px){.lg-main,.lg-body.lg-collapsed .lg-main{margin-left:80px}}

@media(max-width:700px){.lg-main,.lg-body.lg-collapsed .lg-main{margin-left:0;padding:95px 13px 27px}.sc-head{flex-direction:column;align-items:stretch}.sc-select{width:100%}.sc-page-title{font-size:21px}.sc-scan-row{flex-direction:column}.sc-scan-row .sc-btn{width:100%}.sc-stat{padding:13px}.sc-panel-head{align-items:flex-start;flex-direction:column}.sc-group-top{flex-direction:column}.sc-group-actions{width:100%}}

@media(max-width:430px){.sc-stats{gap:8px}.sc-stat strong{font-size:20px}.sc-stat span{font-size:10px}.sc-tabs{gap:12px}.sc-tab{font-size:11px}}

.sc-assigned-name{display:inline-flex;align-items:center;border:1px solid #e5d5cd;border-radius:10px;padding:11px 16px;background:white;color:#6f2425;font-size:12px;font-weight:600;max-width:360px;overflow-wrap:anywhere}.sc-stats{grid-template-columns:repeat(3,minmax(0,1fr))}.sc-camera-dialog{width:min(600px,100%)}.sc-camera-head{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:15px}.sc-camera-head h2{margin:0}#scCameraVideo{display:block;width:100%;max-height:440px;aspect-ratio:4/3;object-fit:cover;background:#211b1b;border-radius:10px}.sc-scan-row{flex-wrap:wrap}.sc-scan-row .sc-camera-button{min-width:128px}@media(max-width:650px){.sc-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.sc-tabs{overflow-x:auto;white-space:nowrap}.sc-tab{flex-shrink:0}}

.sc-receive-stats{margin-top:4px}.sc-incoming-panel{margin-top:0;margin-bottom:18px}.sc-incoming-panel .sc-table tbody tr{transition:*background* .22s ease}

.pt-modal{position:fixed;inset:0;background:#220e13a8;z-index:300;display:flex;align-items:center;justify-content:center;padding:22px;opacity:0;visibility:hidden;transition:opacity .2s,visibility .2s}

.pt-modal.open{opacity:1;visibility:visible}

.pt-dialog{max-width:850px;width:100%;max-height:min(90vh,900px);display:flex;flex-direction:column;background:#fff;border-radius:16px;box-shadow:0 24px 70px #16080a55;overflow:hidden;transform:translateY(12px);transition:transform .2s}

.pt-modal.open .pt-dialog{transform:translateY(0)}

.pt-modal-head{padding:21px 28px;display:flex;justify-content:space-between;align-items:center;gap:20px;border-bottom:1px solid #eee3df}

.pt-modal-head h2{margin:0;font-size:19px;color:#271b1a}

.pt-x{border:0;background:#f8f0ee;color:#7b1b1b;border-radius:50%;font-size:23px;width:35px;height:35px;cursor:pointer}

.pt-modal-content{padding:23px 28px;overflow:auto}

.pt-modal-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}

.pt-detail-card{border:1px solid #eee4df;border-radius:11px;overflow:hidden;margin-bottom:16px}

.pt-detail-title{background:#fff7f5;padding:13px 16px;font-size:12px;color:#69232b;font-weight:700;border-bottom:1px solid #eee3dd}

.pt-detail-body{padding:16px}

.pt-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}

.pt-field label{font-size:10px;color:#978882;display:block;margin-bottom:5px}

.pt-field strong{display:block;font-size:12px;font-weight:600;color:#332a27;overflow-wrap:anywhere}

.pt-wide{grid-column:1/-1}

.pt-progress{display:flex;align-items:flex-start;justify-content:space-between;position:relative;gap:8px;margin:19px 0}

.pt-step{text-align:center;flex:1;position:relative;z-index:1;color:#a9a09d;font-size:10px}

.pt-step i{width:27px;height:27px;display:grid;place-items:center;border:2px solid #e0d5d1;border-radius:50%;background:#fff;margin:0 auto 7px;font-size:12px;font-style:normal}

.pt-step.done i{background:#7b1b1b;border-color:#7b1b1b;color:#fff}

.pt-step.done{color:#7b1b1b;font-weight:600}

.pt-history{list-style:none;padding:0;margin:0}

.pt-history li{padding:11px 0 11px 22px;border-left:2px solid #e8dcd7;position:relative;font-size:12px}

.pt-history li:before{content:'';position:absolute;left:-6px;top:16px;width:9px;height:9px;border-radius:50%;background:#9b2935}

.pt-history small{display:block;margin-top:4px;color:#968984;font-size:10px}

.pt-notes{border:1px solid #f2e0d9;background:#fff9f6;border-radius:9px;padding:14px;font-size:12px;color:#695d58;line-height:1.7}

.pt-modal-actions{padding:15px 27px;border-top:1px solid #efe4df;display:flex;justify-content:flex-end}

.pt-close{background:white;border:1px solid #a6a6a6;color:#777;border-radius:8px;padding:9px 21px;font-size:12px;cursor:pointer}

.pt-close:hover{background:#faf8f7}

body.pt-modal-open{overflow:hidden}

.pt-placeholder{font-size:11px;color:#a1938f;padding:8px 0}

.pt-section-heading{font-size:12px;color:#7b1b1b;margin:0 0 14px}

.pt-desc{font-size:11px;color:#a39a96;margin:0 0 4px}

.pt-count{font-size:10px;color:#ab9893}

.pt-returns-note{margin:0 22px 16px;font-size:11px;color:#927e76}

.pt-table .pt-customer{min-width:170px}

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

.pt-order-item-field label{font-size:11px;color:#9d8d87;display:block;margin-bottom:6px}

.pt-order-item-field strong{display:block;font-size:12px;color:#332a27;font-weight:600;overflow-wrap:anywhere}



/* Parcel details dialog follows the Logistics Parcel Tracking modal styles. */

.sc-clickable-row{cursor:pointer}.sc-clickable-row:hover{background:#fff7f4!important}.sc-clickable-row:focus-visible{outline:2px solid #8a3437;outline-offset:-2px}

.sc-qr-area{display:grid;grid-template-columns:138px 1fr;gap:18px;align-items:center}.sc-qr-area img{height:134px;width:134px;image-rendering:pixelated;border:1px solid #e8dbd5;padding:5px;background:white}.sc-qr-area p{font-size:11px;color:#8d7f79;line-height:1.8}.sc-demo-code{font-size:12px;color:#71232a;font-weight:700;word-break:break-word}

@media(max-width:600px){.pt-party-grid,.pt-fields,.sc-qr-area{grid-template-columns:1fr}.pt-modal-content{padding:18px}.pt-modal-head{padding:17px}.pt-info-pair{grid-template-columns:110px minmax(0,1fr)}}



/* Logistics Parcel Tracking four-stage icon tracking progress */

/* Seller Returns Shipping Status-style tracking progress inside the Parcels details modal. */

.pt-shipping-progress{display:flex;align-items:flex-start;justify-content:center;max-width:520px;width:100%;margin:15px auto 20px;gap:0}

.pt-progress-item{width:99px;flex:0 0 99px;min-width:0;display:flex;flex-direction:column;align-items:center;text-align:center}

.pt-progress-item img{width:36px;height:36px;max-width:36px;object-fit:contain;position:relative;z-index:1}

.pt-progress-item small{display:block;margin-top:8px;font-size:11px;color:#928984;line-height:1.4}

.pt-progress-line{flex:1;min-width:14px;max-width:65px;height:1px;margin-top:18px;background:#d2cdcb}

.pt-progress-line.done{background:#7db473}

@media(max-width:550px){.pt-progress-item{width:67px;flex-basis:67px}.pt-progress-item img{width:29px;height:29px}.pt-progress-item small{font-size:9px}.pt-progress-line{margin-top:15px;min-width:7px}}



.sc-qr-placeholder{min-height:165px;border:1px dashed #dfd3cd;border-radius:10px;background:#fcfaf9;display:flex;align-items:center;justify-content:center;text-align:center;padding:20px;color:#998d88;font-size:12px}


/* Demo and parcel issue reporting */
.sc-issue-button{border:1px solid #b62938;background:#fff1f1;color:#922531;border-radius:8px;padding:9px 16px;font-size:12px;font-weight:650;margin-right:9px}
.sc-issue-button:hover{background:#ffe5e5}.sc-issue-form{display:grid;gap:13px}.sc-issue-field{display:grid;gap:6px;font-size:11px;color:#6d5b55;font-weight:600}
.sc-issue-field select,.sc-issue-field textarea,.sc-issue-field input{border:1px solid #ded4cf;border-radius:9px;background:#fff;padding:10px 12px;font:inherit;font-size:12px;outline:none;color:#302723;width:100%}
.sc-issue-field textarea{min-height:100px;resize:vertical}.sc-issue-dialog{width:min(530px,100%);max-height:90vh;overflow-y:auto}.sc-issue-note{background:#fff9f4;border:1px solid #eedbd0;border-radius:8px;padding:10px 12px;font-size:11px;color:#8e6d5f;line-height:1.6}
.sc-modal#scIssueModal{z-index:420;backdrop-filter:blur(2px)}.sc-modal#scIssueModal .sc-issue-dialog{box-shadow:0 28px 85px #210d135e}.sc-issue-record{background:#fff7ed;border:1px solid #efd7b9;border-radius:9px;padding:10px;margin-top:8px;font-size:11px;line-height:1.8;color:#725239}

/* Clearly separated, high-contrast incoming transfer batch title rows. */
.sc-table tbody tr.sc-batch-row,.sc-table tbody tr.sc-batch-row:hover{background:#74303a!important}
.sc-batch-row>td{padding:0!important;border-top:12px solid #fff!important;border-bottom:0!important;background:linear-gradient(105deg,#671720 0%,#8f3540 100%)!important;color:#fff!important}
.sc-batch-header{display:flex;align-items:center;justify-content:space-between;gap:16px;min-height:86px;padding:16px 21px;flex-wrap:wrap}
.sc-batch-identity{display:grid;gap:4px;min-width:0;flex:1 1 290px}
.sc-batch-kicker{font-size:9px;letter-spacing:1.6px;text-transform:uppercase;color:#ffd9d1;font-weight:700}
.sc-batch-id{font-size:16px!important;font-weight:750!important;color:#fff!important;line-height:1.35;overflow-wrap:anywhere}
.sc-batch-meta{display:flex;flex-wrap:wrap;gap:5px 19px;color:#f5d6d1;font-size:10px;line-height:1.6}
.sc-batch-meta b{color:#fff;font-weight:650}
.sc-batch-metrics{display:flex;gap:8px;flex-wrap:wrap;align-items:center;justify-content:flex-end;margin-left:auto;flex:0 0 auto}
.sc-batch-remaining,.sc-batch-count{white-space:nowrap;border-radius:999px;font-size:10px;font-weight:700;padding:7px 11px}
.sc-batch-remaining{background:#fff1de;color:#873e20}
.sc-batch-count{background:#ffffff2b;color:#fff;border:1px solid #ffffff55}
@media(max-width:650px){.sc-batch-header{padding:14px 15px;gap:11px}.sc-batch-id{font-size:14px!important}.sc-batch-metrics{width:100%;justify-content:flex-end;margin-left:0;padding-top:8px;border-top:1px solid #ffffff38}}
.pt-destination-banner{border:1px solid #dca0a2;background:#fff1ef;border-radius:12px;padding:14px 17px;margin-bottom:17px;color:#71212a;font-size:12px;line-height:1.7}
.pt-destination-banner strong{display:block;font-size:14px;margin-top:4px;overflow-wrap:anywhere}
.pt-detail-card.pt-destination-card{border:2px solid #ae3945;box-shadow:0 0 0 3px #ae394512}
.pt-destination-card .pt-detail-title{background:#7b1b1b;color:#fff}
.pt-destination-pill{display:inline-block;border-radius:100px;background:#ffe4c8;color:#762d20;padding:4px 9px;font-size:10px;font-weight:700;margin:0 0 9px}
.sc-kind{display:inline-block;padding:4px 8px;border-radius:100px;font-size:10px;font-weight:700;background:#eaf2fb;color:#26577e}.sc-kind.return{background:#fff0df;color:#8c4b22}

/* Shared shipping indicators and departure history */
.sc-kind.return,.dh-return,.pt-destination-pill{display:inline-flex;align-items:center;border-radius:999px;background:#fff0e4!important;color:#9b4b16!important;border:1px solid #ffead5;padding:4px 10px;font-size:10px;font-weight:700;white-space:nowrap}
.sc-kind.return{font-size:10px}.sc-depart-history{margin-top:18px}.sc-history-list{display:grid;gap:10px;padding:0 19px 19px}.sc-history-row{border:1px solid #eee1dc;border-radius:11px;padding:13px;display:grid;gap:5px}.sc-history-head{display:flex;justify-content:space-between;gap:9px;flex-wrap:wrap}.sc-history-meta{color:#8f7c77;font-size:11px}.sc-history-action{display:flex;gap:8px;justify-content:flex-end}.sc-dialog-wide{width:min(690px,100%);max-height:85vh;overflow:auto}.sc-history-parcels{font-size:11px;line-height:1.9;color:#574440;max-height:40vh;overflow:auto}
.sc-assignee{display:flex;flex-direction:column;gap:3px}.sc-assignee-name{font-size:11px;font-weight:650;color:inherit}.sc-assignee-phone{display:block;font-size:10px;color:#9d8e87;font-weight:400}.sc-batch-meta .sc-assignee-phone{color:#f5d6d1}.sc-batch-meta .sc-assignee-name{color:#fff}.sc-batch-meta .sc-assignee{display:inline-flex;vertical-align:middle}.sc-vehicle-meta{white-space:normal}
</style>

</head>

<body class="lg-body">

@include('components.logistics.sorting-center.sidebar')

@include('components.logistics.sorting-center.navbar')

<main class="lg-main"><div class="sc-wrap">

<div class="sc-tabs" role="tablist" aria-label="Parcel scanner sections"><button type="button" class="sc-tab active" role="tab" aria-selected="true" aria-controls="scReceiveView" id="scReceiveTab">Received Scanner</button><button type="button" class="sc-tab" role="tab" aria-selected="false" aria-controls="scTablesView" id="scTablesTab">Received Parcels <span class="sc-chip" id="scReceiveBadge">0</span></button><button type="button" class="sc-tab" role="tab" aria-selected="false" aria-controls="scDepartView" id="scDepartTab">Departure Scanner <span class="sc-chip" id="scDepartBadge">0</span></button></div>

<section id="scReceiveView" class="sc-view" role="tabpanel" aria-labelledby="scReceiveTab">

<div class="sc-split"><section class="sc-panel"><div class="sc-panel-head"><div><h2>Receive Parcel Scanner</h2><p>Scan a tracking number from a confirmed incoming transfer manifest.</p></div><span class="sc-badge incoming">Receiving</span></div><div class="sc-panel-body"><form class="sc-scan-row" id="scReceiveForm"><input id="scReceiveCode" type="text" class="sc-input" maxlength="90" autocomplete="off" placeholder="Scan or enter tracking number" aria-label="Tracking number for receipt" required><button class="sc-btn primary" type="submit">Receive Scan</button><button class="sc-btn sc-camera-button" type="button" data-camera="receive" aria-label="Open camera to receive a parcel">▣ Open Camera</button></form><p class="sc-help">A successful scan checks the parcel against the incoming manifest, marks it received, and moves it to Received Parcels. Duplicate and unknown scans are rejected.</p></div></section><section class="sc-panel"><div class="sc-panel-head"><div><h2>Latest Receive Scan</h2><p>Verification result updates after each scan.</p></div></div><div class="sc-panel-body"><div id="scReceiveFeedback" class="sc-feedback" role="status" aria-live="polite"><div><strong>Ready to receive</strong><small>Scan a parcel to verify it against incoming transfers.</small></div></div></div></section></div>

<section class="sc-panel sc-incoming-panel"><div class="sc-panel-head"><div><h2>Incoming Transfers</h2><p>Incoming parcels assigned to this center. Scan each parcel above to remove it from this list and place it in the separate Received Parcels tab.</p></div></div><div class="sc-toolbar"><input class="sc-input" type="search" id="scIncomingSearch" placeholder="Search batch, tracking, sorting center, destination, courier, truck…"><span class="sc-badge incoming" id="scIncomingLabel">0 parcels</span></div><div class="sc-table-wrap"><table class="sc-table"><thead><tr><th>Tracking Number / Type</th><th>Origin Sorting Center / Delivery Hub</th><th>Final Destination</th><th>Assigned Courier / Truck</th><th>Loaded At</th></tr></thead><tbody id="scIncomingRows"></tbody></table></div><div class="sc-footer">Only transfers assigned to this signed-in sorting center appear here.</div></section>



</section>

<section id="scTablesView" class="sc-view" role="tabpanel" aria-labelledby="scTablesTab" hidden>



<section class="sc-panel"><div class="sc-panel-head"><div><h2>Received Parcels</h2><p>This is a separate record of successfully received parcels. Scan them in Received Scanner; they appear here automatically.</p></div></div><div class="sc-toolbar"><input class="sc-input" type="search" id="scReceivedSearch" placeholder="Search received parcel…"><span class="sc-badge received" id="scReceivedLabel">0 parcels</span></div><div class="sc-table-wrap"><table class="sc-table"><thead><tr><th>Tracking Number</th><th>Final Destination</th><th>Received From</th><th>Received At</th><th>Next Stage</th></tr></thead><tbody id="scReceivedRows"></tbody></table></div></section>

</section>

<section id="scDepartView" class="sc-view" role="tabpanel" aria-labelledby="scDepartTab" hidden>

<div class="sc-stats"><div class="sc-stat"><span>Available for Departure</span><strong id="scReadyCount">0</strong></div><div class="sc-stat"><span>Scanned for Dispatch</span><strong id="scStagedCount">0</strong></div><div class="sc-stat"><span>Route Groups</span><strong id="scRouteGroupsCount">0</strong></div><div class="sc-stat"><span>Loaded Parcels</span><strong id="scLoadedCount">0</strong></div></div>

<div class="sc-split"><section class="sc-panel"><div class="sc-panel-head"><div><h2>Departure Parcel Scanner</h2><p>Automatically assign each scanned parcel to its next sorting center or delivery hub.</p></div><span class="sc-badge">Dispatch</span></div><div class="sc-panel-body"><form class="sc-scan-row" id="scDepartForm"><input id="scDepartCode" type="text" class="sc-input" autocomplete="off" maxlength="90" placeholder="Scan or enter tracking number" required aria-label="Tracking number for departure"><button class="sc-btn primary" type="submit">Departure Scan</button><button class="sc-btn sc-camera-button" type="button" data-camera="depart" aria-label="Open camera for departure scan">▣ Open Camera</button></form><p class="sc-help">Parcels from other provinces go to the next configured sorting center in their route. Parcels already in the destination province go to the matching municipality's delivery hub. No known route? The parcel stays unassigned, not loaded.</p></div></section><section class="sc-panel"><div class="sc-panel-head"><div><h2>Last Scanned Parcel</h2><p>Shows the destination of the latest scan only; replaced on the next scan.</p></div></div><div class="sc-panel-body"><div class="sc-feedback" id="scDepartFeedback" role="status" aria-live="polite"><div><strong>Waiting for scan</strong><small>The next center or delivery hub will appear here.</small></div></div></div></section></div>

<div class="sc-split"><section class="sc-panel"><div class="sc-panel-head"><div><h2>Ready Parcels</h2><p>Already received and waiting for departure scanning.</p></div></div><div class="sc-toolbar"><input class="sc-input" id="scReadySearch" type="search" placeholder="Search ready parcels…"></div><div class="sc-table-wrap"><table class="sc-table"><thead><tr><th>Tracking No.</th><th>Destination Province</th><th>Municipality</th><th>Status</th></tr></thead><tbody id="scReadyRows"></tbody></table></div></section><section class="sc-panel"><div class="sc-panel-head"><div><h2>Recent Scanner Activity</h2><p>Local operations for the selected center.</p></div></div><div class="sc-activity" id="scActivity"></div></section></div>

<section class="sc-panel"><div class="sc-panel-head"><div><h2>Grouped Parcels for Sorting Center / Delivery Hub Transfers</h2><p>Groups are created instantly by next stop as parcels are scanned. Press Loaded to dispatch a specific group.</p></div><div class="sc-legend"><span><i class="sc-dot"></i> Staged</span><span><i class="sc-dot green"></i> Loaded</span><span><i class="sc-dot blue"></i> Next stop confirmed</span></div></div><div class="sc-panel-body"><div class="sc-groups" id="scRouteGroups"></div></div></section>

<section class="sc-panel sc-depart-history" id="scDepartHistoryPanel"><div class="sc-panel-head"><div><h2>Depart History</h2><p>Loaded outgoing batches remain here until every parcel is accounted for at the destination.</p></div><button type="button" class="sc-btn small" id="scHistoryOpen">View All Departures</button></div><div id="scHistoryPreview" class="sc-history-list"></div></section>
</section>

</div></main>

<div class="sc-modal" id="scHistoryModal" hidden><section class="sc-dialog sc-dialog-wide" role="dialog" aria-modal="true"><div class="sc-camera-head"><h2>Departure History</h2><button class="sc-btn small" id="scHistoryClose" type="button">Close</button></div><div id="scHistoryDetails"></div></section></div>
<div class="sc-toast" id="scToast" hidden role="status" aria-live="polite"></div>

<div class="sc-modal" id="scLoadModal" hidden><section class="sc-dialog" role="dialog" aria-modal="true" aria-labelledby="scLoadTitle"><h2 id="scLoadTitle">Confirm Truck Loading</h2><p id="scLoadDescription"></p><label for="scCourierSelect" style="font-size:11px;font-weight:600;display:block;margin-bottom:7px">Assigned Courier / Driver *</label><select class="sc-select" id="scCourierSelect" required style="width:100%;margin-bottom:12px"><option value="">Select available courier</option></select><label for="scTruckId" style="font-size:11px;font-weight:600">Assigned Truck / Plate Number</label><input class="sc-input" id="scTruckId" placeholder="Automatically filled from courier" readonly style="width:100%;margin-top:7px;background:#fbf6f3"><p class="sc-help" id="scCourierAvailability">Only approved, unassigned couriers may be dispatched.</p><p class="sc-demo-warning">Confirming Loaded creates incoming transfers for a sorting center destination, or a transfer manifest destined for a delivery hub. This is browser-only until backend integration.</p><div class="sc-dialog-actions"><button class="sc-btn" type="button" id="scLoadCancel">Cancel</button><button class="sc-btn primary" type="button" id="scLoadConfirm">Confirm Loaded</button></div></section></div>

<div class="sc-modal" id="scCameraModal" hidden><section class="sc-dialog sc-camera-dialog" role="dialog" aria-modal="true" aria-labelledby="scCameraTitle"><div class="sc-camera-head"><h2 id="scCameraTitle">Scan Parcel Barcode</h2><button type="button" class="sc-btn small" id="scCameraClose">Close</button></div><video id="scCameraVideo" autoplay muted playsinline aria-label="Live camera view"></video><div style="margin-top:12px;display:flex;align-items:center;gap:9px;flex-wrap:wrap"><label class="sc-btn small" for="scQrImageInput" style="cursor:pointer">Upload QR Image</label><input type="file" id="scQrImageInput" accept="image/*" style="position:absolute;width:1px;height:1px;opacity:0" aria-label="Upload QR image"></div><p id="scCameraMessage" class="sc-help" role="status">Allow camera access to scan a tracking barcode or QR code.</p><p class="sc-help">Uses native QR detection when available and a QR decoder fallback. You can also upload an image of the QR code or type the tracking number.</p></section></div>

<div class="pt-modal" id="scParcelDetails" aria-hidden="true"><div class="pt-dialog" role="dialog" aria-modal="true" aria-labelledby="scParcelDetailsTitle" tabindex="-1"><header class="pt-modal-head"><h2 id="scParcelDetailsTitle">Order Details</h2><button class="pt-x" id="scDetailsX" type="button" aria-label="Close">×</button></header><div class="pt-modal-content" id="scParcelDetailsBody"></div><div class="pt-modal-actions"><button type="button" id="scReportIssue" class="sc-issue-button">Report Issue</button><button type="button" class="pt-close" id="scDetailsClose">Close</button></div></div></div>

<div class="sc-modal" id="scIssueModal" hidden><section class="sc-dialog sc-issue-dialog" role="dialog" aria-modal="true" aria-labelledby="scIssueTitle">
<h2 id="scIssueTitle">Report Parcel Issue</h2><p>Record an issue involving the selected parcel.</p>
<form id="scIssueForm" class="sc-issue-form">
<div class="sc-issue-note">Tracking Number: <strong id="scIssueTracking">—</strong><br>Lost or damaged parcels automatically generate a full-value Logistics-to-Seller reimbursement record (demo only; no real payment).</div>
<label class="sc-issue-field">Issue Type *<select id="scIssueType" required><option value="">Select an issue</option><option value="Lost Parcel">Lost Parcel</option><option value="Damaged Parcel">Damaged Parcel</option></select></label>
<label class="sc-issue-field">Description *<textarea id="scIssueDescription" required minlength="10" maxlength="1500" placeholder="Explain what happened, when it was discovered, and what action is needed..."></textarea></label>
<label class="sc-issue-field">Photo Evidence (optional; preview only)<input type="file" id="scIssueEvidence" accept="image/png,image/jpeg,image/webp"></label>
<div class="sc-dialog-actions"><button class="sc-btn" type="button" id="scIssueCancel">Cancel</button><button class="sc-btn primary" type="submit">Submit Report</button></div></form></section></div>
<script>

document.addEventListener('DOMContentLoaded',()=>{

'use strict';

const $=id=>document.getElementById(id);const storeKey='shopease-sc-scanner-transfers-demo-v1',networkKey='shopease-logistics-network-hubs-ui-v1';

const configuredId=@json($scannerCenterId);const configuredName=@json($scannerCenterName);const accountId=@json($scannerAccountId);const accountEmail=@json($scannerAccountEmail);

const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

const norm=s=>String(s??'').trim().toLocaleLowerCase();const fmt=d=>d?new Intl.DateTimeFormat('en-PH',{timeZone:'Asia/Manila',month:'short',day:'numeric',hour:'numeric',minute:'2-digit'}).format(new Date(d)):'—';

const getLocal=k=>{try{return JSON.parse(localStorage.getItem(k)||'null')}catch{return null}};

const now=()=>new Date().toISOString();const uid=()=>('id-'+Date.now()+'-'+Math.random().toString(36).slice(2,8));

let data=getLocal(storeKey);if(!data||!Array.isArray(data.parcels)||!Array.isArray(data.manifests)){data={parcels:[],manifests:[],events:[]}};

const centers=()=>{const x=getLocal(networkKey);return Array.isArray(x)?x:[]};

// The current center is resolved from authenticated server data, never from a dropdown or query parameter.

let currentId='';let tab='receive';let pendingGroup=null;

let options=[];

function initCenter(){

 const saved=centers().filter(c=>norm(c.status)!=='inactive');

 const accountMatch=saved.find(c=>norm(c.login_email||c.manager_email)===norm(accountEmail));

 currentId=String(configuredId||accountMatch?.id||(accountId?'account-'+accountId:''));

 const assignedName=String((configuredId?saved.find(c=>String(c.id)===String(configuredId))?.branch_name:null)||accountMatch?.branch_name||configuredName||'Assigned Sorting Center');

 const assignedCenter=saved.find(c=>String(c.id)===currentId);

 options=assignedCenter?saved:[...saved,{id:currentId,branch_name:assignedName,province:'',hubs:[],status:'active'}];

 // The assigned center remains bound to the authenticated account; its header badge is hidden.

}


// Courier directory mirrors the Sorting Center Vehicles & Drivers page's preview keys.
// Live backend assignments must replace localStorage when the production API is ready.
const courierScope=()=>String(configuredId||currentId||'SC-LAGUNA-DEMO');
const courierKey=()=> 'shopease-sorting-center-courier-registrations-preview-v1:'+courierScope();
const vehicleKey=()=> 'shopease-sc-registered-vehicles-preview-v1:'+courierScope();
const courierArchiveKey=()=> 'shopease-sc-vehicles-drivers-archive-v1:'+courierScope();
const fullCourierName=r=>[r.first_name,r.middle_name,r.last_name].filter(Boolean).join(' ')||r.full_name||r.name||'Courier';
const demoCouriers=[{id:'demo-sc-courier-003',first_name:'Adrian',middle_name:'Paul',last_name:'Mendoza',plate_number:'GHI 9012',status:'approved'}];
const demoTrucks=[{id:'sc-truck-demo-03',plate_number:'GHI 9012',name:'Fuso Canter',availability:'Available',driver_name:'Adrian Paul Mendoza'}];
function courierDirectory(){
 const saved=getLocal(courierKey());const archived=getLocal(courierArchiveKey())||[];
 const excluded=new Set((Array.isArray(archived)?archived:[]).map(a=>String(a.key)));
 const items=Array.isArray(saved)?saved:demoCouriers;
 const trucks=getLocal(vehicleKey())||demoTrucks;
 return items.filter(c=>norm(c.status)==='approved'&&!excluded.has('couriers:'+String(c.id)))
   .map(c=>{const plate=String(c.plate_number||c.license_plate||'').trim();const v=(Array.isArray(trucks)?trucks:[]).find(t=>norm(t.plate_number)===norm(plate));
     return {...c,displayName:fullCourierName(c),plate,vehicle:v?.name||c.vehicle||'Truck',vehicleAvailability:v?.availability||'Available',archivedVehicle:!!(v&&excluded.has('vehicles:'+String(v.id)))};
   }).filter(c=>c.plate&&!c.archivedVehicle);
}
function activeDispatches(){return data.manifests.filter(m=>String(m.originCenterId)===String(currentId)&&m.status==='loaded'&&!m.arrivedAt&&!m.releasedAt);}
function busyCourier(c){return activeDispatches().find(m=>(m.courierId&&String(m.courierId)===String(c.id))||(!m.courierId&&norm(m.courierName)===norm(c.displayName))||norm(m.truckId)===norm(c.plate));}
function fillCourierPicker(){const all=courierDirectory();const available=all.filter(c=>!busyCourier(c)&&!['in service','unavailable','maintenance'].includes(norm(c.vehicleAvailability)));
 $('scCourierSelect').innerHTML='<option value="">Select available courier</option>'+all.map(c=>{const busy=busyCourier(c),disabled=busy||['in service','unavailable','maintenance'].includes(norm(c.vehicleAvailability));return `<option value="${esc(c.id)}" ${disabled?'disabled':''}>${esc(c.displayName)} · ${esc(c.plate)}${disabled?' · Unavailable':''}</option>`}).join('');
 $('scCourierAvailability').textContent=available.length?`${available.length} courier(s) available. Assigned truck / plate number is automatic.`:'No available approved couriers. Approve a courier with a plate number in Courier Registrations first.';
 $('scLoadConfirm').disabled=!available.length;$('scTruckId').value='';return available.length;
}
function inboundAssignment(m,p){const courier=m?.courierName||m?.driverName||m?.assignedCourierName||p?.courierName||p?.driverName||p?.assignedCourierName||'Not recorded by sender';
 const truck=m?.truckId||m?.plateNumber||m?.plate_number||p?.truckId||p?.plateNumber||p?.plate_number||'Not recorded by sender';const record=courierDirectory().find(c=>String(c.id)===String(m?.courierId||p?.courierId||'')||norm(c.displayName)===norm(courier));
 const phone=m?.courierPhone||m?.driverPhone||m?.courier_phone||p?.courierPhone||p?.driverPhone||p?.courier_phone||record?.phone_number||record?.phone||record?.mobile_number||record?.contact_number||'';
 const vehicle=m?.vehicleType||m?.vehicleName||m?.truckName||p?.vehicleType||p?.vehicleName||record?.vehicle||'Truck';
 return {courier,truck,phone,vehicle};}

const center=()=>options.find(c=>String(c.id)===String(currentId));

const centerById=id=>options.find(c=>String(c.id)===String(id));

const save=()=>{localStorage.setItem(storeKey,JSON.stringify(data));render()};

const status=p=>p.status||'ready';

const list=()=>data.parcels.filter(p=>String(p.currentCenterId||'')===String(currentId) && !isReported(p));

const findParcel=id=>data.parcels.find(p=>norm(p.tracking)===norm(id));

function say(message){$('scToast').textContent=message;$('scToast').hidden=false;clearTimeout(say.timer);say.timer=setTimeout(()=>$('scToast').hidden=true,3500)}

function feedback(id,style,title,sub,route){const el=$(id);el.className='sc-feedback '+(style||'');el.innerHTML=`<div><strong>${esc(title)}</strong><small>${esc(sub)}</small>${route?`<div class="sc-route">${esc(route)}</div>`:''}</div>`;}

function event(action,parcel,desc){data.events.unshift({id:uid(),at:now(),centerId:currentId,action,tracking:parcel?.tracking||'',desc});data.events=data.events.slice(0,150)}

function provinceCenter(province){return options.find(c=>norm(c.province)===norm(province)&&norm(c.status)!=='inactive');}

function hubOf(c,city){return (c?.hubs||[]).find(h=>norm(h.city||h.municipality)===norm(city)&&norm(h.status)!=='inactive');}

// Explicit routePlan holds known intermediate provincial legs. The system never guesses a highway or a destination.

function nextStop(parcel){const c=center();if(!c)return {error:'No sorting center is assigned to this account.'};const destKey=destinationRole(parcel).toLowerCase();const targetProvince=parcel[destKey+'_province']||parcel.destProvince,targetCity=parcel[destKey+'_city']||parcel.destCity;const prov=norm(targetProvince),here=norm(c.province);if(!prov||!norm(targetCity))return {error:'Parcel destination province/municipality is missing.'};

 if(prov===here){const hub=hubOf(c,targetCity);return hub?{type:'hub',id:hub.id,name:hub.hub_name||hub.branch_name||hub.name||'Delivery Hub',centerId:c.id}:{error:'No matching active delivery hub for '+targetCity+', '+targetProvince+'.'};}

 const plan=Array.isArray(parcel.routePlan)?parcel.routePlan:[];let nxt=plan.find(id=>String(id)!==String(c.id)&&!(parcel.visitedCenters||[]).map(String).includes(String(id))&&centerById(id));

 if(!nxt){const dc=provinceCenter(targetProvince);if(dc&&String(dc.id)!==String(c.id))nxt=dc.id;}

 if(!nxt)return {error:'No configured next sorting center for '+targetProvince+'. Set an approved route before dispatch.'};

 const target=centerById(nxt);if(!target||norm(target.status)==='inactive')return {error:'Next sorting center is unavailable.'};return {type:'sorting',id:target.id,name:target.branch_name||target.name||target.province,centerId:target.id};}

function incoming(){return data.parcels.filter(p=>p.status==='incoming'&&String(p.nextCenterId)===String(currentId)&&!isReported(p));}

function received(){return list().filter(p=>p.status==='received'||p.status==='staged'||p.status==='ready');}

function ready(){return list().filter(p=>['received','ready','staged'].includes(status(p)));}

function stagedGroups(){const groups=new Map();for(const p of list().filter(p=>status(p)==='staged'&&p.nextStop)){const n=p.nextStop;const batchId=String(p.receivedManifestId||p.inboundManifestId||p.receivedTransferId||'unassigned-'+p.id);const k=batchId+'|'+n.type+':'+String(n.id);if(!groups.has(k))groups.set(k,{key:k,batchId,dest:n,parcels:[]});groups.get(k).parcels.push(p);}return [...groups.values()].sort((a,b)=>a.dest.name.localeCompare(b.dest.name));}

// Departure safety: verify original inbound manifest is complete before dispatch.
function pendingBatchParcels(group){
 const blocked=new Map();
 for(const p of group.parcels){
  const batchId=String(p.receivedManifestId||p.inboundManifestId||p.receivedTransferId||'');
  if(!batchId)continue; // No reliable batch mapping: do not merge unrelated shipments.
  const manifest=data.manifests.find(m=>String(m.id)===batchId);
  if(!manifest)continue;
  const members=(manifest.parcelIds||[]).map(id=>data.parcels.find(x=>String(x.id)===String(id))).filter(Boolean);
  for(const member of members){
   if(isReported(member))continue; // Already documented as Lost / Damaged in Parcel Disputes.
   if(member.status==='incoming'&&String(member.nextCenterId)===String(currentId))blocked.set(String(member.id),member);
  }
 }
 return [...blocked.values()];
}
function explainPending(group){const pending=pendingBatchParcels(group);return pending.length?`Cannot load this batch: ${pending.length} parcel(s) still appear in Incoming Transfers and have not been received-scanned: ${pending.map(p=>p.tracking).join(', ')}. Receive-scan or report the missing parcels first.`:'';}

function tabs(t){tab=t;for(const [key,view,btnId] of [['receive','scReceiveView','scReceiveTab'],['tables','scTablesView','scTablesTab'],['depart','scDepartView','scDepartTab']]){const on=t===key;$(view).hidden=!on;const btn=$(btnId);btn.classList.toggle('active',on);btn.setAttribute('aria-selected',String(on));}if(t==='receive'||t==='depart')$(t==='receive'?'scReceiveCode':'scDepartCode').focus()}

function checkReceive(raw){const p=findParcel(raw);if(isReported(p)){feedback('scReceiveFeedback','warn','Parcel already reported','This parcel is now managed in Parcel Disputes.');return;}if(!p){feedback('scReceiveFeedback','bad','Tracking number not found','No matching transfer manifest or parcel record.');return;}

 if(p.status==='incoming'&&String(p.nextCenterId)===String(currentId)){

  const m=data.manifests.find(m=>m.id===p.transferId);if(!m||m.status!=='loaded'){feedback('scReceiveFeedback','bad','Unconfirmed transfer','The parcel does not belong to a confirmed Loaded manifest.');return;}

  p.status='received';p.currentCenterId=currentId;p.receivedAt=now();p.receivedManifestId=m.id;p.receivedFrom=m.originName;if((m.parcelIds||[]).every(id=>{const item=data.parcels.find(x=>String(x.id)===String(id));return !item||item===p||item.receivedManifestId===m.id||isReported(item)})){m.arrivedAt=now();}p.nextCenterId=null;p.transferId=null;p.nextStop=null;p.visitedCenters=[...new Set([...(p.visitedCenters||[]).map(String),String(currentId)])];event('Received',p,`Received from ${m.originName}`);if((m.parcelIds||[]).every(id=>{const x=data.parcels.find(z=>String(z.id)===String(id));return x&&(x.status!=='incoming'||isReported(x));})){m.arrivedAt=now();m.status='arrived';}save();feedback('scReceiveFeedback','good','Parcel received successfully',p.tracking+' was moved from Incoming Transfers to Received Parcels.','Received at '+(center()?.branch_name||'Sorting Center'));say('Parcel received: '+p.tracking);return;}

 feedback('scReceiveFeedback','warn',p.status==='received'||p.status==='staged'?'Parcel already received':'Parcel is not in your incoming manifest',p.tracking+' cannot be received at this sorting center.');}

function checkDepart(raw){const p=findParcel(raw);if(isReported(p)){feedback('scDepartFeedback','warn','Parcel already reported','This parcel is now managed in Parcel Disputes.');return;}if(!p){feedback('scDepartFeedback','bad','Parcel not found','This tracking number has no record in the scanner dataset.');return;}

 if(String(p.currentCenterId)!==String(currentId)||!['received','ready','staged'].includes(status(p))){feedback('scDepartFeedback','warn','Parcel not ready for departure',p.tracking+' must be received at this sorting center first.');return;}

 if(status(p)==='staged'){feedback('scDepartFeedback','warn','Already staged',p.tracking+' is already grouped for transportation.',p.nextStop?.name||'');return;}

 const dest=nextStop(p);if(dest.error){feedback('scDepartFeedback','bad','Route needs review',dest.error);return;}

 p.status='staged';p.nextStop=dest;p.scannedAt=now();event('Departure scan',p,`Grouped for ${dest.name}`);save();feedback('scDepartFeedback','good',dest.type==='hub'?'Next Delivery Hub':'Next Sorting Center',p.tracking+' is assigned to '+(dest.type==='hub'?'a delivery hub':'another sorting center')+' before its final destination.',dest.name);say('Grouped under '+dest.name);}



// Remove only legacy generated test records from earlier frontend versions.

// Keep any non-demo records; do not generate sample parcels or route nodes.

function purgeLegacyDemoData(){

 const isDemo = p => /^SHP-SC-DEMO-00[1-7]$/i.test(String(p?.tracking||p?.tracking_number||'')) && String(p?.id||'').startsWith('demo-');

 const oldIds = new Set(data.parcels.filter(isDemo).map(p=>String(p.id)));

 const oldCount = oldIds.size;

 if(oldCount){

  data.parcels=data.parcels.filter(p=>!oldIds.has(String(p.id)));

  data.manifests=data.manifests.filter(m=>!String(m.id||'').startsWith('DEMO-TX-') && !(Array.isArray(m.parcelIds)&&m.parcelIds.some(id=>oldIds.has(String(id)))));

  data.events=(data.events||[]).filter(e=>!/^SHP-SC-DEMO-00[1-7]$/i.test(String(e.tracking||'')));

  localStorage.setItem(storeKey,JSON.stringify(data));

 }

}

// UI PREVIEW ONLY: synthetic parcels for this signed-in sorting center.
// They are never inserted into the backend. Each account has its own demo marker.
const demoPrefix = 'SHP-SC-PREVIEW-';
function seedScannerPreview(){
  if(!currentId)return;
  const demoKey='shopease-sc-preview-seeded-'+currentId;
  const alreadySeeded=localStorage.getItem(demoKey)==='1';
  const centerLabel=center()?.branch_name||configuredName||'Current Sorting Center';
  const province=center()?.province||'Laguna';
  const hubName='Santa Cruz Delivery Hub (Demo)';
  const hubId='preview-hub-'+currentId;
  const c=center();
  if(c){c.hubs=Array.isArray(c.hubs)?c.hubs:[];if(!c.hubs.some(h=>String(h.id)===hubId))c.hubs.push({id:hubId,hub_name:hubName,city:'Santa Cruz',municipality:'Santa Cruz',status:'active'});}
  // Upgrade already-seeded PREVIEW parcels without clearing reports or scanner events.
  const demoPhones=['09171234561','09181234562','09191234563','09201234564','09211234565','09221234566'];
  let phoneUpdated=false;
  for(const p of data.parcels){
    const match=String(p.tracking||'').match(/^SHP-SC-PREVIEW-(10[1-6])$/);
    if(!match || !String(p.id||'').startsWith('preview-'))continue;
    const index=Number(match[1])-101;
    if(/x|\*/i.test(String(p.buyer_phone||'')) || !p.buyer_phone){p.buyer_phone=demoPhones[index];phoneUpdated=true;}
    if(/x|\*/i.test(String(p.seller_phone||'')) || !p.seller_phone){p.seller_phone='09175678901';phoneUpdated=true;}
  }
  if(phoneUpdated)localStorage.setItem(storeKey,JSON.stringify(data));
  if(alreadySeeded)return;
  const templates=[
    {n:'101',state:'incoming',city:'Santa Cruz',origin:'Batangas Sorting Center',name:'Laptop Stand'},
    {n:'102',state:'incoming',city:'Santa Cruz',origin:'Quezon Sorting Center',name:'Wireless Mouse',isReturn:true},
    {n:'103',state:'received',city:'Santa Cruz',origin:'Manila Sorting Center',name:'Headphones'},
    {n:'104',state:'ready',city:'Santa Cruz',origin:'Cavite Sorting Center',name:'Desk Lamp'},
    {n:'105',state:'staged',city:'Santa Cruz',origin:'Rizal Sorting Center',name:'Phone Case'},
    {n:'106',state:'received',city:'Santa Cruz',origin:'Batangas Sorting Center',name:'Portable Fan',isReturn:true},
  ];
  templates.forEach((x,i)=>{
    const tracking=demoPrefix+x.n;
    if(data.parcels.some(p=>norm(p.tracking)===norm(tracking)))return;
    const manifestId='SC-PREVIEW-MANIFEST-'+x.n+'-'+currentId;
    const when=new Date(Date.now()-(i+1)*3600000).toISOString();
    const parcel={id:'preview-'+x.n+'-'+currentId,tracking,status:x.state,isReturn:!!x.isReturn,currentCenterId:currentId,
      nextCenterId:x.state==='incoming'?currentId:null,transferId:x.state==='incoming'?manifestId:null,
      destProvince:province,destCity:x.city,loadedFromName:x.origin,receivedFrom:x.origin,loadedAt:when,receivedAt:x.state==='incoming'?null:when,
      ordered_at:when,estimated_delivery:new Date(Date.now()+86400000*(i+1)).toISOString(),order_number:'SE-PREVIEW-'+x.n,
      buyer_name:['Juan Dela Cruz','Maria Santos','Ana Reyes','Carlo Mendoza','Lea Ramos','Joshua Lim'][i],buyer_email:'buyer'+x.n+'@example.test',buyer_phone:['09171234561','09181234562','09191234563','09201234564','09211234565','09221234566'][i],
      seller_name:'ShopEase Demo Seller',seller_email:'seller@example.test',seller_phone:'09175678901',
      buyer_province:province,buyer_city:x.city,buyer_barangay:'Poblacion',buyer_street:'Rizal St.',buyer_house_no:'12',buyer_postal_code:'4009',
      seller_province:'Metro Manila',seller_city:'Quezon City',seller_barangay:'Demo Barangay',seller_street:'Sample Street',seller_house_no:'5',seller_postal_code:'1100',
      payment_method:'Cash on Delivery',total_amount:599+i*135,notes:'Demo parcel for UI testing only.',
      items:[{product_name:x.name,variation:'Standard',color:'Black',size:'One Size',quantity:1,product_price:599+i*135}],
      history:[{status:'Order Placed',date:when,location:'ShopEase Seller'},{status:'Picked Up',date:when,location:x.origin}],
      nextStop:x.state==='staged'?{type:'hub',id:hubId,name:hubName,centerId:currentId}:null};
    data.parcels.push(parcel);
    if(x.state==='incoming')data.manifests.push({id:manifestId,originCenterId:'preview-origin',originName:x.origin,nextStop:{type:'sorting',id:currentId,name:centerLabel},truckId:'TRK-PREVIEW-'+x.n,createdAt:when,status:'loaded',parcelIds:[parcel.id]});
  });
  localStorage.setItem(demoKey,'1');
  save();
}
// Separate preview batches for loading-validation testing (versioned, never deletes existing reports).
// A: one incoming unscanned and one already departure-staged; B: independent incoming batch;
// C: completely received and staged, should be loadable even while A and B remain incomplete.
function seedIndependentBatches(){
 if(!currentId)return;
 const flag='shopease-sc-group-validation-preview-v2-'+currentId;
 const c=center(), centerLabel=c?.branch_name||configuredName||'Assigned Sorting Center';
 const hubId='preview-hub-'+currentId, hubName='Santa Cruz Delivery Hub (Demo)';
 if(c){c.hubs=Array.isArray(c.hubs)?c.hubs:[];if(!c.hubs.some(h=>String(h.id)===hubId))c.hubs.push({id:hubId,hub_name:hubName,city:'Santa Cruz',municipality:'Santa Cruz',status:'active'});}
 const batches=[
  {code:'BATCH-A-501',origin:'Batangas Sorting Center',truck:'TRK-DEMO-A1',states:[['201','staged'],['202','received'],['203','incoming']]},
  {code:'BATCH-B-502',origin:'Quezon Sorting Center',truck:'TRK-DEMO-B2',states:[['204','staged'],['205','incoming'],['206','incoming']]},
  {code:'BATCH-C-503',origin:'Manila Sorting Center',truck:'TRK-DEMO-C3',states:[['207','staged'],['208','staged']]}
 ];
 const currentIssues=issuesFor; // Reported parcels must never be reseeded by a reload.
 let changed=false;
 for(const [batchIndex,b] of batches.entries()){
  const manifestId='SC-'+b.code+'-'+currentId;
  const ids=b.states.map(([num])=>'preview-'+num+'-'+currentId);
  if(!data.manifests.some(m=>String(m.id)===manifestId)){
   data.manifests.push({id:manifestId,originCenterId:'preview-source-'+batchIndex,originName:b.origin,nextStop:{type:'sorting',id:currentId,name:centerLabel},truckId:b.truck,createdAt:new Date(Date.now()-(batchIndex+1)*3600000).toISOString(),status:'loaded',parcelIds:ids});changed=true;
  }
  b.states.forEach(([num,state],pos)=>{
   const tracking='SHP-SC-PREVIEW-'+num;
   if(data.parcels.some(p=>norm(p.tracking)===norm(tracking)))return;
   const when=new Date(Date.now()-(batchIndex+1)*3600000).toISOString();
   const price=499+Number(num);
   data.parcels.push({id:'preview-'+num+'-'+currentId,tracking,status:state,currentCenterId:currentId,
    nextCenterId:state==='incoming'?currentId:null,transferId:state==='incoming'?manifestId:null,
    receivedManifestId:state==='incoming'?null:manifestId,loadedFromName:b.origin,receivedFrom:b.origin,
    loadedAt:when,receivedAt:state==='incoming'?null:when,truckId:b.truck,
    destProvince:c?.province||'Laguna',destCity:'Santa Cruz',
    order_number:'SE-PREVIEW-'+num,ordered_at:when,estimated_delivery:new Date(Date.now()+86400000*3).toISOString(),
    buyer_name:'Demo Buyer '+num,buyer_email:'buyer'+num+'@example.test',buyer_phone:'09171234'+num,
    seller_name:'ShopEase Demo Seller',seller_email:'seller@example.test',seller_phone:'09175678901',
    buyer_province:c?.province||'Laguna',buyer_city:'Santa Cruz',buyer_barangay:'Poblacion',buyer_street:'Rizal St.',buyer_house_no:'12',buyer_postal_code:'4009',
    seller_province:'Metro Manila',seller_city:'Quezon City',seller_barangay:'Demo Barangay',seller_street:'Sample Street',seller_house_no:'5',seller_postal_code:'1100',
    payment_method:'Cash on Delivery',total_amount:price,notes:'Group completeness demonstration '+b.code+'.',
    items:[{product_name:'Test Parcel Item '+num,variation:'Standard',color:'Black',size:'One Size',quantity:1,product_price:price}],
    history:[{status:'Order Placed',date:when,location:'ShopEase Seller'},{status:state==='incoming'?'In Transit':'Received at Sorting Center',date:when,location:b.origin}],
    nextStop:state==='staged'?{type:'hub',id:hubId,name:hubName,centerId:currentId}:null
   });changed=true;
  });
 }
 if(changed)save();
 localStorage.setItem(flag,'1');
}
// Dedicated return example: inbound transfer to this sorting center, with the SELLER as final destination.
// It is scoped to the signed-in center and never recreates a previously reported parcel.
function seedReturnParcelExample(){
 if(!currentId)return;
 const tracking='SHP-SC-RETURN-301';
 if(data.parcels.some(p=>norm(p.tracking)===norm(tracking)))return;
 const id='preview-return-301-'+currentId;
 const manifestId='SC-RETURN-301-'+currentId;
 const when=new Date(Date.now()-45*60000).toISOString();
 const source='Quezon Provincial Sorting Center';
 const parcel={
  id,tracking,order_number:'SE-RETURN-301',isReturn:true,kind:'return',parcelType:'Return Parcel',
  status:'incoming',currentCenterId:'',nextCenterId:currentId,transferId:manifestId,
  loadedFromName:source,receivedFrom:source,loadedAt:when,receivedAt:null,
  destProvince:'Cebu',destCity:'Cebu City',
  buyer_name:'Andrea Villanueva',buyer_email:'andrea@example.test',buyer_phone:'09171234567',
  buyer_province:'Quezon',buyer_city:'Lucena City',buyer_barangay:'Ibabang Dupay',buyer_street:'Mabini Street',buyer_house_no:'14',buyer_postal_code:'4301',
  seller_name:'Visayas Gadgets Store',seller_email:'visayas-seller@example.test',seller_phone:'09181234567',
  seller_province:'Cebu',seller_city:'Cebu City',seller_barangay:'Lahug',seller_street:'Gorordo Avenue',seller_house_no:'82',seller_postal_code:'6000',
  payment_method:'Original order paid',total_amount:1250,
  notes:'Buyer return. Final destination is the seller address in Cebu City, not the buyer pickup address in Lucena.',
  ordered_at:when,estimated_delivery:new Date(Date.now()+3*86400000).toISOString(),
  items:[{product_name:'Bluetooth Headphones (Return)',variation:'Standard',color:'Black',size:'One Size',quantity:1,product_price:1250}],
  history:[{status:'Return Requested',date:new Date(Date.now()-6*3600000).toISOString(),location:'Lucena City, Quezon'},
   {status:'Collected from Buyer',date:new Date(Date.now()-4*3600000).toISOString(),location:'Ibabang Dupay, Lucena City'},
   {status:'In Transit',date:when,location:source}]
 };
 data.parcels.push(parcel);
 data.manifests.push({id:manifestId,originCenterId:'preview-quezon-source',originName:source,
  nextStop:{type:'sorting',id:currentId,name:center()?.branch_name||configuredName||'Sorting Center'},
  courierName:'Miguel Reyes (Demo)',truckId:'NAB 4821',plateNumber:'NAB 4821',createdAt:when,status:'loaded',parcelIds:[id]});
 save();
}
// Demo issues are scoped by the current center; no backend submission occurs.
let activeIssueTracking='';
function issueStorageKey(){return 'shopease-sc-issues-preview-'+currentId;}
function issueReports(){try{return JSON.parse(localStorage.getItem(issueStorageKey())||'[]')||[]}catch{return []}}
function issuesFor(tracking){return issueReports().filter(r=>norm(r.tracking)===norm(tracking));}
function isReported(p){return !!p && (p.reportedIssue===true || issuesFor(p.tracking).length>0);}
function openIssueModal(){
  if(!activeIssueTracking)return;
  $('scIssueForm').reset();$('scIssueTracking').textContent=activeIssueTracking;
  $('scIssueModal').hidden=false;$('scIssueType').focus();
}
function closeIssueModal(){$('scIssueModal').hidden=true;if($('scParcelDetails').classList.contains('open'))$('scReportIssue').focus();}
$('scReportIssue').addEventListener('click',openIssueModal);
$('scIssueCancel').addEventListener('click',closeIssueModal);
$('scIssueModal').addEventListener('click',e=>{if(e.target===$('scIssueModal'))closeIssueModal()});
$('scIssueForm').addEventListener('submit',e=>{
  e.preventDefault();if(!$('scIssueForm').reportValidity()||!activeIssueTracking)return;
  const f=$('scIssueEvidence').files?.[0];
  if(f&&(f.size>5*1024*1024||!['image/jpeg','image/png','image/webp'].includes(f.type))){say('Evidence must be a JPG, PNG or WebP under 5 MB.');return;}
  const selectedType=$('scIssueType').value;
  if(!['Lost Parcel','Damaged Parcel'].includes(selectedType)){say('Please select Lost Parcel or Damaged Parcel.');return;}
  const parcel=findParcel(activeIssueTracking);
  const value=Number(parcel?.total_amount);
  const amount=Number.isFinite(value)&&value>0?value.toFixed(2):'';
  const recordedAt=now();
  const accountability={party:'Logistics Partner',resolution:'Full Parcel Value Reimbursement to Seller',
    parcelAmount:amount,compensation:amount,
    notes:'Automatically recorded after a '+selectedType.toLowerCase()+' report was submitted.',
    at:recordedAt,simulatedReimbursedAt:amount?recordedAt:null,status:amount?'Reimbursed · UI Simulation':'Amount Verification Required · Demo',autoVersion:3};
  const all=issueReports();const reportId='ISS-PREVIEW-'+Date.now();all.unshift({id:reportId,tracking:activeIssueTracking,type:selectedType,
    description:$('scIssueDescription').value.trim(),evidenceName:f?.name||'',status:'Open',at:recordedAt,centerId:currentId,accountability});
  localStorage.setItem(issueStorageKey(),JSON.stringify(all));
  // Retain the parcel details for Disputes; exclude it from all scanner lists and dispatch operations.
  if(parcel){parcel.reportedIssue=true;parcel.reportedAt=recordedAt;parcel.reportId=reportId;parcel.nextStop=null;if(parcel.status==='staged')parcel.status='reported';}
  save();
  const tracking=activeIssueTracking;closeIssueModal();closeParcelDetails();window.location.assign(@json(route('sorting-center.parcel-disputes'))+'?tracking='+encodeURIComponent(tracking)+'&report='+encodeURIComponent(reportId));
});
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!$('scIssueModal').hidden)closeIssueModal()});

// Parcel Tracking exact modal component classes; additional QR panel is the only new section.

const modalCard=(title,body)=>`<section class="pt-detail-card"><div class="pt-detail-title">${esc(title)}</div><div class="pt-detail-body">${body}</div></section>`;

const modalField=(label,value)=>`<div class="pt-field"><label>${esc(label)}</label><strong>${esc(value??'—')}</strong></div>`;

const modalPair=(label,value)=>`<div class="pt-info-pair"><label>${esc(label)}</label><strong>${esc(value??'—')}</strong></div>`;

function destinationRole(p){return p.isReturn===true||p.kind==='return'||String(p.parcelType||p.parcel_type||p.order_type||'').toLowerCase().includes('return')?'Seller':'Buyer'}
function destinationAddress(p){const b=destinationRole(p).toLowerCase();return [p[b+'_house_no'],p[b+'_street'],p[b+'_barangay'],p[b+'_city'],p[b+'_province'],p[b+'_postal_code']].filter(Boolean).join(', ')}
const finalDestination=p=>{const prefix=destinationRole(p).toLowerCase();return [p[prefix+'_barangay'],p[prefix+'_city']||p.destCity,p[prefix+'_province']||p.destProvince].filter(Boolean).join(', ')};
const kindBadge=p=>destinationRole(p)==='Seller'?'<span class="sc-kind return">Return Parcel</span>':'';
function modalParty(role,p){let b=role==='Buyer'?'buyer':'seller';const isDest=destinationRole(p)===role;const title=role+' Information'+(isDest?' · DELIVERY DESTINATION':'');const body=(isDest?'<span class="pt-destination-pill">DESTINATION ADDRESS</span><p class="pt-desc">'+esc(p.isReturn?'Buyer return → Seller':'Normal parcel → Buyer')+'</p>':'')+modalPair('Name',p[b+'_name'])+modalPair('Email',p[b+'_email'])+modalPair('Contact',p[b+'_phone'])+modalPair('Province',p[b+'_province'])+modalPair('Municipality',p[b+'_city'])+modalPair('Barangay',p[b+'_barangay'])+modalPair('Street',p[b+'_street'])+modalPair('House No.',p[b+'_house_no'])+modalPair('ZIP',p[b+'_postal_code']);return `<section class="pt-detail-card ${isDest?'pt-destination-card':''}"><div class="pt-detail-title">${esc(title)}</div><div class="pt-detail-body">${body}</div></section>`;}


let detailsPreviousFocus=null;

function showParcelDetails(raw){const p=findParcel(raw);if(!p)return;detailsPreviousFocus=document.activeElement;activeIssueTracking=p.tracking;

 const processState=p.status==='incoming'?'In Transit':p.status==='received'?'Arrived at Sorting Center':p.status==='staged'?'Preparing for Departure':p.status==='loaded'?'In Transit':'Processing';

 // Exact four-stage icon style used by Logistics Parcel Tracking.

 const progressStage = p.status==='loaded' ? 1 : ['incoming','received','ready','staged'].includes(p.status) ? 1 : p.status==='delivered' ? 3 : p.status==='out-for-delivery' ? 2 : 0;

 const progressLabels=['Processing','In Transit','Out for Delivery','Delivered'];

 const progressIcons=progressLabels.map((label,i)=>{

   if(i===0)return progressStage>0?'in-transit-check.png':'processing.png';

   if(i===1)return progressStage>1?'in-transit-check.png':progressStage===1?'in-transit-blue.png':'in-transit-gray.png';

   if(i===2)return progressStage>2?'delivered-check.png':progressStage===2?'out-for-delivery.png':'out-for-delivery-gray.png';

   return progressStage===3?'delivered-check.png':'delivered-gray.png';

 });

 const dots=`<div class="pt-shipping-progress" role="img" aria-label="Tracking Progress">${progressLabels.map((label,i)=>`<div class="pt-progress-item"><img src="/icons/seller/shipping-status/${progressIcons[i]}" alt=""><small>${label}</small></div>${i<progressLabels.length-1?`<span class="pt-progress-line ${i<progressStage?'done':''}"></span>`:''}`).join('')}</div>`;

 const items=(p.items||[]).map((it,i)=>`<article class="pt-order-item"><div class="pt-order-item-head"><span class="pt-order-item-index">Item ${i+1}</span><div class="pt-order-item-name">${esc(it.product_name)}</div></div><div class="pt-order-item-body"><div class="pt-order-item-fields">${modalField('Variation',it.variation)}${modalField('Color',it.color)}${modalField('Size',it.size)}${modalField('Quantity Ordered',it.quantity)}${modalField('Unit Price','₱ '+Number(it.product_price||0).toFixed(2))}</div></div></article>`).join('');

 const hist=(p.history||[]).map(x=>`<li><strong>${esc(x.status)}</strong><small>${esc(x.date)} · ${esc(x.location)}</small></li>`).join('')+`<li><strong>${esc(processState)}</strong><small>${esc(fmt(p.receivedAt||p.loadedAt||now()))}</small></li>`;

 $('scParcelDetailsBody').innerHTML=`<div class="pt-destination-banner">${esc(destinationRole(p)==='Seller'?'RETURN PARCEL · Destination: Seller':'Destination: Buyer')}<strong>${esc(destinationAddress(p))}</strong></div>`+modalCard('Order Information',`<div class="pt-fields">${modalField('Order Number',p.order_number)}${modalField('Ordered At',fmt(p.ordered_at))}${modalField('Estimated Delivery',fmt(p.estimated_delivery))}${modalField('Tracking Number',p.tracking)}${modalField('Parcel Type',destinationRole(p)==='Seller'?'Return Parcel':'Normal Parcel')}${modalField('Final Destination',destinationAddress(p))}${modalField('Current Location',p.status==='incoming'?'On the way to this sorting center':configuredName)}${modalField('Shipping Status',processState)}</div>`)+modalCard('Tracking Progress',dots)+`<div class="pt-party-grid">${modalParty('Buyer',p)}${modalParty('Seller',p)}</div>`+modalCard('Payment Information',`<div class="pt-fields">${modalField('Payment Method',p.payment_method)}${modalField('Order Total','₱ '+Number(p.total_amount||0).toFixed(2))}</div>`)+modalCard('Ordered Items',`<div class="pt-item-stack">${items||'<p>No items available.</p>'}</div>`)+modalCard('Tracking History',`<ul class="pt-history">${hist}</ul>`)+modalCard('Customer Notes',`<div class="pt-notes">${esc(p.notes)}</div>`)+modalCard('Reported Issues',issuesFor(p.tracking).map(r=>`<div class="sc-issue-record"><strong>${esc(r.type)} · ${esc(r.status)}</strong><br>${esc(r.description)}<br><small>${esc(fmt(r.at))}${r.evidenceName?' · Evidence: '+esc(r.evidenceName):''}</small></div>`).join('')||'<p class="pt-placeholder">No issues reported for this parcel.</p>')+modalCard('Parcel QR Code',`<div class="sc-qr-placeholder">QR code will be available once provided by the backend.</div>`);

 $('scParcelDetails').classList.add('open');$('scParcelDetails').setAttribute('aria-hidden','false');document.body.classList.add('pt-modal-open');$('scDetailsX').focus();

}

function closeParcelDetails(){const m=$('scParcelDetails');m.classList.remove('open');m.setAttribute('aria-hidden','true');document.body.classList.remove('pt-modal-open');detailsPreviousFocus?.focus?.()}

for(const id of ['scDetailsX','scDetailsClose'])$(id).addEventListener('click',closeParcelDetails);

$('scParcelDetails').addEventListener('click',e=>{if(e.target===$('scParcelDetails'))closeParcelDetails()});

document.addEventListener('keydown',e=>{if(e.key==='Escape'&&$('scParcelDetails').classList.contains('open'))closeParcelDetails()});

for(const id of ['scIncomingRows','scReceivedRows','scReadyRows'])$(id).addEventListener('click',e=>{let row=e.target.closest('[data-parcel]');if(row)showParcelDetails(row.dataset.parcel)});



function render(){const inc=incoming(),rec=received(),rdy=ready(),grp=stagedGroups(),loaded=data.parcels.filter(p=>p.status==='loaded'&&String(p.loadedFromId)===String(currentId)&&!isReported(p));

 $('scReceiveBadge').textContent=rec.length;

 $('scReadyCount').textContent=rdy.filter(p=>status(p)!=='staged').length;$('scStagedCount').textContent=rdy.filter(p=>status(p)==='staged').length;$('scRouteGroupsCount').textContent=grp.length;$('scLoadedCount').textContent=loaded.length;$('scDepartBadge').textContent=grp.length;

 $('scIncomingLabel').textContent=inc.length+' parcels';$('scReceivedLabel').textContent=rec.length+' parcels';

 const f=(rows,id,q,cb,cols)=>{$(id).innerHTML=rows.filter(x=>norm(JSON.stringify(x)).includes(norm($(q).value))).map(cb).join('')||`<tr><td colspan="${cols}" class="sc-empty">No parcels found.</td></tr>`};

 // Display inbound transfer manifests as separate batches; never mix independent arrivals.
 const inboundGroups=new Map();
 for(const p of inc.filter(x=>norm(JSON.stringify(x)).includes(norm($('scIncomingSearch').value)))){
  const key=String(p.transferId||'Ungrouped');if(!inboundGroups.has(key))inboundGroups.set(key,[]);inboundGroups.get(key).push(p);
 }
 $('scIncomingRows').innerHTML=[...inboundGroups].map(([batchId,rows])=>{
  const m=data.manifests.find(x=>String(x.id)===batchId);
  const total=(m?.parcelIds||[]).length||rows.length;
  const received=(m?.parcelIds||[]).map(id=>data.parcels.find(x=>String(x.id)===String(id))).filter(p=>p&&(p.receivedManifestId===batchId||isReported(p))).length;
  const batchDisplay=batchId==='Ungrouped'?'Unassigned Transfer':batchId;
  const batchComplete=rows.length===0;
  const header=`<tr class="sc-batch-row"><td colspan="5"><div class="sc-batch-header"><div class="sc-batch-identity"><span class="sc-batch-kicker">INCOMING TRANSFER BATCH</span><strong class="sc-batch-id">${esc(batchDisplay)}</strong><div class="sc-batch-meta"><span>Origin: <b>${esc(m?.originName||rows[0]?.loadedFromName||'Unknown origin')}</b></span><span class="sc-vehicle-meta">Vehicle: <b>${esc(inboundAssignment(m,rows[0]).vehicle)} · ${esc(inboundAssignment(m,rows[0]).truck)}</b></span></div></div><div class="sc-batch-metrics"><span class="sc-batch-remaining">${rows.length} Awaiting Scan</span><span class="sc-batch-count">${received} / ${total} verified</span></div></div></td></tr>`;
  return header+rows.map(p=>`<tr class="sc-clickable-row" data-parcel="${esc(p.tracking)}" title="View order details"><td><strong>${esc(p.tracking)}</strong><small>${kindBadge(p)}</small></td><td>${esc(m?.originName||p.loadedFromName||'—')}</td><td>${esc(finalDestination(p))}</td><td><span class="sc-assignee"><strong>${esc(inboundAssignment(m,p).courier)}</strong><small class="sc-assignee-phone">${esc(inboundAssignment(m,p).phone||'Phone not recorded')}</small></span></td><td>${fmt(p.loadedAt)}</td></tr>`).join('');
 }).join('')||'<tr><td colspan="5" class="sc-empty">No incoming parcels found.</td></tr>';

 f(rec,'scReceivedRows','scReceivedSearch',p=>`<tr class="sc-clickable-row" data-parcel="${esc(p.tracking)}" title="View order details"><td><strong>${esc(p.tracking)}</strong></td><td>${esc(finalDestination(p))}<small>${kindBadge(p)}</small></td><td>${esc(p.receivedFrom||'—')}</td><td>${fmt(p.receivedAt)}</td><td><span class="sc-badge ${p.status==='staged'?'incoming':'received'}">${p.status==='staged'?'Staged for dispatch':'Ready for dispatch'}</span></td></tr>`,5);

 f(rdy,'scReadyRows','scReadySearch',p=>`<tr class="sc-clickable-row" data-parcel="${esc(p.tracking)}" title="View order details"><td><strong>${esc(p.tracking)}</strong></td><td>${esc(destinationRole(p)==='Seller'?(p.seller_province||p.destProvince):(p.buyer_province||p.destProvince))}</td><td>${esc(finalDestination(p))}<small>${kindBadge(p)}</small></td><td><span class="sc-badge ${p.status==='staged'?'incoming':'received'}">${p.status==='staged'?'Staged':'Ready'}</span></td></tr>`,4);

 drawDepartHistory();$('scRouteGroups').innerHTML=grp.map(g=>`<article class="sc-group"><div class="sc-group-top"><div><h3 class="sc-group-title">${esc(g.dest.name)}</h3><p class="sc-group-sub">Inbound batch: ${esc(g.batchId)} · ${g.dest.type==='sorting'?'Next sorting center':'Destination delivery hub'} · Ready for transport</p></div><div class="sc-group-actions"><span class="sc-group-count">${g.parcels.length} parcel${g.parcels.length===1?'':'s'}</span>${explainPending(g)?`<button class="sc-btn primary small" type="button" disabled title="${esc(explainPending(g))}">Awaiting scans</button>`:`<button class="sc-btn primary small" type="button" data-load="${esc(g.key)}">Loaded</button>`}</div></div><div class="sc-group-list">${g.parcels.map(p=>`<div class="sc-group-item"><strong>${esc(p.tracking)}</strong><small>${esc(finalDestination(p))} · ${destinationRole(p)==='Seller'?'<span class="sc-kind return">Return Parcel</span>':''}</small></div>`).join('')}</div></article>`).join('')||'<div class="sc-groups-empty">No parcel groups yet. Use Departure Scanner to form groups automatically.</div>';

 const ev=data.events.filter(e=>String(e.centerId)===String(currentId)).slice(0,6);$('scActivity').innerHTML=ev.map(e=>`<div class="sc-activity-row"><div><strong>${esc(e.action)} · ${esc(e.tracking)}</strong><small>${esc(e.desc)}</small></div><time>${fmt(e.at)}</time></div>`).join('')||'<div class="sc-activity-empty">No scanner activity yet.</div>';

}

$('scReceiveTab').addEventListener('click',()=>tabs('receive'));$('scTablesTab').addEventListener('click',()=>tabs('tables'));$('scDepartTab').addEventListener('click',()=>tabs('depart'));

$('scReceiveForm').addEventListener('submit',e=>{e.preventDefault();const code=$('scReceiveCode').value.trim();if(code)checkReceive(code);$('scReceiveCode').value='';$('scReceiveCode').focus()});

$('scDepartForm').addEventListener('submit',e=>{e.preventDefault();const code=$('scDepartCode').value.trim();if(code)checkDepart(code);$('scDepartCode').value='';$('scDepartCode').focus()});

for(const field of ['scIncomingSearch','scReceivedSearch','scReadySearch'])$(field).addEventListener('input',render);

$('scRouteGroups').addEventListener('click',e=>{const btn=e.target.closest('[data-load]');if(!btn)return;pendingGroup=stagedGroups().find(g=>g.key===btn.dataset.load)||null;if(!pendingGroup)return;const blockReason=explainPending(pendingGroup);if(blockReason){say(blockReason);feedback('scDepartFeedback','warn','Loading blocked: incomplete incoming batch',blockReason);return;}fillCourierPicker();$('scLoadDescription').textContent=`Mark ${pendingGroup.parcels.length} parcel(s) as Loaded for ${pendingGroup.dest.name}? Select an available courier to assign their truck.`;$('scLoadModal').hidden=false;$('scCourierSelect').focus()});

$('scCourierSelect').addEventListener('change',()=>{const c=courierDirectory().find(x=>String(x.id)===$('scCourierSelect').value);$('scTruckId').value=c?.plate||'';$('scLoadConfirm').disabled=!c||!!busyCourier(c);$('scCourierAvailability').textContent=c?`${c.displayName} · ${c.vehicle} · Plate ${c.plate}`:'Select an available approved courier.'});

const closeModal=()=>{$('scLoadModal').hidden=true;pendingGroup=null};$('scLoadCancel').addEventListener('click',closeModal);$('scLoadModal').addEventListener('click',e=>{if(e.target===$('scLoadModal'))closeModal()});document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!$('scLoadModal').hidden)closeModal()});

$('scLoadConfirm').addEventListener('click',()=>{if(!pendingGroup)return;const g=stagedGroups().find(x=>x.key===pendingGroup.key);if(!g)return closeModal();const blockReason=explainPending(g);if(blockReason){closeModal();say(blockReason);feedback('scDepartFeedback','warn','Loading blocked: incomplete incoming batch',blockReason);return;}const selected=courierDirectory().find(x=>String(x.id)===$('scCourierSelect').value);if(!selected||busyCourier(selected)||['in service','unavailable','maintenance'].includes(norm(selected.vehicleAvailability))){say('Courier is unavailable or already assigned to another active batch.');fillCourierPicker();return;}const c=center();const m={id:'TX-'+Date.now(),originCenterId:currentId,originName:c?.branch_name||'Sorting Center',nextStop:g.dest,truckId:selected.plate,courierId:String(selected.id),courierName:selected.displayName,vehicleName:selected.vehicle,createdAt:now(),status:'loaded',parcelIds:g.parcels.map(p=>p.id)};data.manifests.push(m);

 for(const p of g.parcels){p.loadedAt=m.createdAt;p.loadedFromId=currentId;p.loadedFromName=m.originName;p.transferId=m.id;p.truckId=m.truckId;p.courierName=m.courierName;p.courierId=m.courierId;p.nextStop=null;p.status=g.dest.type==='sorting'?'incoming':'loaded';p.nextCenterId=g.dest.type==='sorting'?g.dest.id:null;p.deliveryHubId=g.dest.type==='hub'?g.dest.id:null;if(g.dest.type==='hub')p.loadedToHubName=g.dest.name;event('Loaded',p,`Sent to ${g.dest.name}`);}save();closeModal();feedback('scDepartFeedback','good','Truck loaded · transfer confirmed',g.parcels.length+' parcels dispatched. The transfer manifest is ready for the destination.',g.dest.name);say('Loaded '+g.parcels.length+' parcels for '+g.dest.name)});

// Import dispatches from Delivery Hubs in the same browser preview storage.
function importHubTransfers(){
 const prefix='shopease-dh-scanner-preview-v1:';let changed=false;
 for(let i=0;i<localStorage.length;i++){const key=localStorage.key(i);if(!key?.startsWith(prefix))continue;const hubData=getLocal(key);if(!hubData||!Array.isArray(hubData.manifests))continue;
  for(const m of hubData.manifests){if(m.sourceType!=='transfer-to-sorting'||!['loaded','arrived'].includes(m.status))continue; if(m.destinationCenterId&&String(m.destinationCenterId)!==String(currentId))continue;
   if(!m.destinationCenterId&&norm(m.destination)!==norm(center()?.branch_name||configuredName)&&norm(m.destination)!=='assigned provincial sorting center')continue;
   const ref='DH:'+m.id;const ids=[];for(const pid of m.parcelIds||[]){const p=(hubData.parcels||[]).find(x=>String(x.id)===String(pid));if(!p)continue;let existing=findParcel(p.tracking);const id=existing?.id||'dh-import-'+p.id;ids.push(id);if(existing)continue;data.parcels.push({...p,id,status:'incoming',currentCenterId:null,nextCenterId:currentId,transferId:ref,loadedFromName:m.originName,receivedFrom:m.originName,courierName:m.courierName,truckId:m.truckId,history:[...(p.history||[])]});changed=true;}
   if(ids.length&&!data.manifests.some(x=>String(x.id)===ref)){data.manifests.push({id:ref,originCenterId:'hub-'+key.slice(prefix.length),originName:m.originName,sourceType:'delivery-hub',nextStop:{type:'sorting',id:currentId},truckId:m.truckId,courierName:m.courierName,createdAt:m.createdAt,status:'loaded',parcelIds:ids});changed=true;}
  }
 }if(changed)save();}
function departHistory(){return data.manifests.filter(m=>String(m.originCenterId)===String(currentId)&&m.parcelIds?.length&&['loaded','arrived'].includes(m.status)&&m.createdAt).sort((a,b)=>String(b.createdAt).localeCompare(String(a.createdAt)));}
function drawDepartHistory(){const items=departHistory().filter(m=>!m.arrivedAt&&!m.releasedAt);const rows=items.map(m=>`<div class="sc-history-row"><div class="sc-history-head"><strong>${esc(m.id)}</strong><span class="sc-badge incoming">Awaiting Destination Receipt</span></div><div class="sc-history-meta">${esc(m.originName)} → ${esc(m.nextStop?.name||'Destination')} · ${fmt(m.createdAt)}</div><div class="sc-history-meta">Courier: ${esc(m.courierName||'—')} · Truck: ${esc(m.truckId||'—')} · ${m.parcelIds.length} parcel(s)</div><button type="button" class="sc-btn small" data-history-detail="${esc(m.id)}">View Batch</button></div>`).join('');$('scHistoryPreview').innerHTML=rows||'<p class="sc-help">No outstanding departures.</p>';$('scHistoryDetails').innerHTML=rows||'<p class="sc-help">No outstanding departures.</p>'; }
$('scHistoryOpen').onclick=()=>{drawDepartHistory();$('scHistoryModal').hidden=false};$('scHistoryClose').onclick=()=>$('scHistoryModal').hidden=true;
for(const id of ['scHistoryPreview','scHistoryDetails'])$(id).addEventListener('click',e=>{const btn=e.target.closest('[data-history-detail]');if(!btn)return;const m=data.manifests.find(x=>String(x.id)===btn.dataset.historyDetail);if(!m)return;$('scHistoryDetails').innerHTML=`<h3>${esc(m.id)}</h3><p class="sc-history-meta">${esc(m.originName)} → ${esc(m.nextStop?.name||'Destination')} · ${fmt(m.createdAt)}</p><p class="sc-history-meta">${esc(m.courierName||'—')} · ${esc(m.truckId||'—')}</p><div class="sc-history-parcels">${m.parcelIds.map(pid=>{const p=data.parcels.find(x=>String(x.id)===String(pid));return `<div>${esc(p?.tracking||pid)} ${p&&destinationRole(p)==='Seller'?'<span class="sc-kind return">Return Parcel</span>':''}</div>`}).join('')}</div>`;$('scHistoryModal').hidden=false;});

// QR camera scanning: native BarcodeDetector first, jsQR fallback for browsers without it.

// jsQR is loaded on demand from CDN (no backend necessary for this frontend preview).

let cameraStream=null,cameraAnimation=null,cameraMode='receive',cameraWorking=false;

let qrLibraryPromise=null;

const qrCanvas=document.createElement('canvas');

const qrContext=qrCanvas.getContext('2d',{willReadFrequently:true});

function loadQrLibrary(){

 if(typeof window.jsQR==='function')return Promise.resolve(window.jsQR);

 if(qrLibraryPromise)return qrLibraryPromise;

 qrLibraryPromise=new Promise((resolve,reject)=>{

  const el=document.createElement('script');

  el.src='https\://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js';

  el.onload=()=>typeof window.jsQR==='function'?resolve(window.jsQR):reject(new Error('QR library could not initialize.'));

  el.onerror=()=>reject(new Error('QR decoder could not load. Check internet access or content-blocking settings.'));

  document.head.append(el);

 }).catch(error=>{qrLibraryPromise=null;throw error});

 return qrLibraryPromise;

}

function decodeQrFrame(source,width,height){

 if(!qrContext || !width || !height || typeof window.jsQR!=='function')return null;

 const scale=Math.min(1,900/Math.max(width,height));

 qrCanvas.width=Math.max(1,Math.round(width*scale));qrCanvas.height=Math.max(1,Math.round(height*scale));

 qrContext.drawImage(source,0,0,qrCanvas.width,qrCanvas.height);

 const image=qrContext.getImageData(0,0,qrCanvas.width,qrCanvas.height);

 return window.jsQR(image.data,image.width,image.height,{inversionAttempts:'attemptBoth'})?.data?.trim()||null;

}

async function stopCamera(){

 cameraWorking=false;

 if(cameraAnimation)cancelAnimationFrame(cameraAnimation);cameraAnimation=null;

 if(cameraStream){cameraStream.getTracks().forEach(t=>t.stop());cameraStream=null;}

 const video=$('scCameraVideo');video.pause();video.srcObject=null;$('scCameraModal').hidden=true;

}

async function handleQrResult(value,mode){

 const code=String(value||'').trim();if(!code)return;

 await stopCamera();

 const input=$(mode==='receive'?'scReceiveCode':'scDepartCode');input.value=code;

 if(mode==='receive')checkReceive(code);else checkDepart(code);

 input.value='';say('QR detected: '+code);

}

async function startCamera(mode){

 await stopCamera();cameraMode=mode;

 $('scCameraModal').hidden=false;

 $('scCameraTitle').textContent=mode==='receive'?'Receive Parcel · QR Scanner':'Departure Parcel · QR Scanner';

 const msg=$('scCameraMessage');msg.textContent='Starting camera…';

 try{

  if(!navigator.mediaDevices?.getUserMedia)throw new Error('Camera access requires localhost or HTTPS, browser permission, and a connected camera.');

  cameraStream=await navigator.mediaDevices.getUserMedia({video:{facingMode:{ideal:'environment'},width:{ideal:1280},height:{ideal:720}},audio:false});

  if($('scCameraModal').hidden){cameraStream.getTracks().forEach(t=>t.stop());cameraStream=null;return;}

  const video=$('scCameraVideo');video.srcObject=cameraStream;await video.play();

  let nativeDetector=null;

  if(typeof window.BarcodeDetector==='function'){

   try{const formats=await window.BarcodeDetector.getSupportedFormats();if(formats.includes('qr_code'))nativeDetector=new window.BarcodeDetector({formats:['qr_code']});}catch(_){}

  }

  let fallback=null;

  try{fallback=await loadQrLibrary();}catch(err){if(!nativeDetector)throw err;}

  cameraWorking=true;let busy=false;let previous=0;

  msg.textContent='Hold the QR code steady within the camera frame. The scan is automatic.';

  const tick=async(time)=>{

   if(!cameraWorking)return;

   if(!busy&&video.readyState>=2&&time-previous>=150){

    busy=true;previous=time;

    try{

     let code=null;

     if(nativeDetector){const results=await nativeDetector.detect(video);code=results.find(x=>x.rawValue?.trim())?.rawValue?.trim()||null;}

     if(!code&&fallback)code=decodeQrFrame(video,video.videoWidth,video.videoHeight);

     if(code){await handleQrResult(code,mode);return;}

    }catch(err){msg.textContent='Trying to read QR: '+err.message;}

    finally{busy=false;}

   }

   if(cameraWorking)cameraAnimation=requestAnimationFrame(tick);

  };

  cameraAnimation=requestAnimationFrame(tick);

 }catch(err){msg.textContent=(err?.message||'Could not start the QR scanner.')+' You may still upload a QR image below.';

  if(cameraStream){cameraStream.getTracks().forEach(t=>t.stop());cameraStream=null;}

 }

}

$('scQrImageInput').addEventListener('change',async event=>{

 const file=event.target.files?.[0];if(!file)return;

 const msg=$('scCameraMessage');msg.textContent='Reading QR image…';

 try{

  await loadQrLibrary();

  const bitmap=await createImageBitmap(file);

  const result=decodeQrFrame(bitmap,bitmap.width,bitmap.height);

  bitmap.close();

  if(!result){msg.textContent='No readable QR found. Try a sharper and larger image.';return;}

  await handleQrResult(result,cameraMode);

 }catch(err){msg.textContent=err.message||'Could not decode the QR image.';}

 finally{event.target.value='';}

});

document.querySelectorAll('[data-camera]').forEach(b=>b.addEventListener('click',()=>startCamera(b.dataset.camera)));

$('scCameraClose').addEventListener('click',stopCamera);

$('scCameraModal').addEventListener('click',e=>{if(e.target===$('scCameraModal'))stopCamera()});

document.addEventListener('visibilitychange',()=>{if(document.hidden&&cameraStream)stopCamera()});

window.addEventListener('storage',e=>{if(e.key===storeKey){const r=getLocal(storeKey);if(r&&Array.isArray(r.parcels)){data=r;render()}}if(e.key===networkKey){initCenter();render()}if(e.key?.startsWith('shopease-dh-scanner-preview-v1:'))importHubTransfers()});

initCenter();purgeLegacyDemoData();seedScannerPreview();seedIndependentBatches();importHubTransfers();seedReturnParcelExample();render();tabs('receive');

});

</script>

</body></html>