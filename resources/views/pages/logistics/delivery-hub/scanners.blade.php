@php
    $deliveryHub = $deliveryHub ?? $hub ?? null;
    $hubAccountId = (string) (auth()->id() ?? '');
    $hubId = (string) ($deliveryHubId ?? data_get($deliveryHub, 'id') ?? 'account-'.(auth()->id() ?? 'preview'));
    $hubName = (string) (data_get($deliveryHub,'hub_name') ?? auth()->user()?->name ?? 'Delivery Hub');
    $hubMunicipality = (string) (data_get($deliveryHub,'municipality') ?? 'Pila');
    $hubProvince = (string) (data_get($deliveryHub,'province') ?? 'Laguna');
    $hubCenterId = (string) ($sortingCenterId ?? data_get($deliveryHub,'logistics_branch_id') ?? '');
    if (!$deliveryHub && auth()->check()) {
        $deliveryHub = \App\Models\Logistics\LogisticsDeliveryHub::query()
            ->with('logisticsBranch:id,name')
            ->where('user_id', auth()->id())->first();
    }
    $hubCenterId = (string) ($sortingCenterId ?? data_get($deliveryHub, 'logistics_branch_id') ?? $hubCenterId);
    $assignedCenterName = (string) ($sortingCenterName ?? data_get($deliveryHub, 'logisticsBranch.name') ?? data_get($deliveryHub, 'logistics_branch.name') ?? '');
@endphp
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><title>Delivery Hub Scanners — ShopEase</title><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet"><style>

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

/* Delivery Hub specific grouped manifests */
.dh-source-strip{display:flex;gap:8px;flex-wrap:wrap;align-items:center}.dh-source{padding:7px 12px;background:#fff5f0;border:1px solid #f0dcd5;border-radius:22px;font-size:11px;color:#7a2828;font-weight:600}.dh-hub-name{font-size:11px;color:#a18a81;margin-top:4px}.dh-pickup{background:#eef6ff;color:#295e8e}.dh-return{background:#fff0e7;color:#9c582e}.dh-delivery{background:#e9f7ed;color:#286d45}.dh-group-caption{padding:9px 18px 15px;color:#8d7c75;font-size:11px}.dh-group-secondary{font-size:10px;color:#99867e;margin-top:6px}.dh-receive-note{padding:12px 16px;border:1px solid #e9d9d1;background:#fffaf7;border-radius:10px;font-size:11px;color:#795b51;margin:0 0 18px}.dh-layout-2{display:grid;grid-template-columns:minmax(0,1.6fr) minmax(300px,1fr);gap:17px}.dh-assign{display:flex;gap:10px;flex-wrap:wrap;align-items:center}.dh-error{color:#b02c32;font-size:11px;min-height:16px;margin:7px 0 0}@media(max-width:1050px){.dh-layout-2{grid-template-columns:1fr}}
/* Explicit shipping destination inside order details */
.pt-destination-banner{border:1px solid #dca0a2;background:#fff1ef;border-radius:12px;padding:14px 17px;margin-bottom:17px;color:#71212a;font-size:12px;line-height:1.7}
.pt-destination-banner strong{display:block;font-size:14px;margin-top:4px;overflow-wrap:anywhere}
.pt-detail-card.pt-destination-card{border:2px solid #ae3945;box-shadow:0 0 0 3px #ae394512}
.pt-destination-card .pt-detail-title{background:#7b1b1b;color:#fff}
.pt-destination-pill{display:inline-block;border-radius:100px;background:#ffe4c8;color:#762d20;padding:4px 9px;font-size:10px;font-weight:700;margin-left:9px}

/* Shared shipping indicators and departure history */
.sc-kind.return,.dh-return,.pt-destination-pill{display:inline-flex;align-items:center;border-radius:999px;background:#fff0e4!important;color:#9b4b16!important;border:1px solid #ffead5;padding:4px 10px;font-size:10px;font-weight:700;white-space:nowrap}
.sc-kind.return{font-size:10px}.sc-kind.pickup{display:inline-flex;align-items:center;border-radius:999px;background:#eef6ff;color:#295e8e;border:1px solid #d9ebff;padding:4px 10px;font-size:10px;font-weight:700;white-space:nowrap}.sc-kind.plain{font-weight:600;color:#9b8a84;font-size:12px}.sc-depart-history{margin-top:18px}.sc-history-list{display:grid;gap:10px;padding:0 19px 19px}.sc-history-row{border:1px solid #eee1dc;border-radius:11px;padding:13px;display:grid;gap:5px}.sc-history-head{display:flex;justify-content:space-between;gap:9px;flex-wrap:wrap}.sc-history-meta{color:#8f7c77;font-size:11px}.sc-history-action{display:flex;gap:8px;justify-content:flex-end}.sc-dialog-wide{width:min(690px,100%);max-height:85vh;overflow:auto}.sc-history-parcels{font-size:11px;line-height:1.9;color:#574440;max-height:40vh;overflow:auto}
.sc-assignee{display:flex;flex-direction:column;gap:3px}.sc-assignee-name{font-size:11px;font-weight:650;color:inherit}.sc-assignee-phone{display:block;font-size:10px;color:#9d8e87;font-weight:400}.sc-batch-meta .sc-assignee-phone{color:#f5d6d1}.sc-batch-meta .sc-assignee-name{color:#fff}.sc-batch-meta .sc-assignee{display:inline-flex;vertical-align:middle}.sc-vehicle-meta{white-space:normal}
</style></head><body class="lg-body">
@include('components.logistics.delivery-hub.sidebar')
@include('components.logistics.delivery-hub.navbar')
<main class="lg-main"><div class="sc-wrap">
<div class="sc-tabs" role="tablist"><button class="sc-tab active" id="scReceiveTab" role="tab" aria-selected="true">Received Scanner</button><button class="sc-tab" id="scTablesTab" role="tab" aria-selected="false">Received Parcels <span class="sc-chip" id="scReceiveBadge">0</span></button><button class="sc-tab" id="scDepartTab" role="tab" aria-selected="false">Departure Scanner <span class="sc-chip" id="scDepartBadge">0</span></button></div>
<section class="sc-view" id="scReceiveView"><div class="sc-split"><section class="sc-panel"><div class="sc-panel-head"><div><h2>Receive Parcel Scanner</h2><p>Verify incoming batches sent by Sorting Center, or collected by a barangay pickup/returns rider.</p></div><span class="sc-badge incoming">Receiving</span></div><div class="sc-panel-body"><form id="scReceiveForm" class="sc-scan-row"><input class="sc-input" id="scReceiveCode" placeholder="Scan or enter tracking number" required maxlength="90"><button class="sc-btn primary" type="submit">Receive Scan</button><button class="sc-btn sc-camera-button" type="button" data-camera="receive">▣ Open Camera</button></form><p class="sc-help">Only parcels in this hub's incoming manifest are accepted. Scanned parcels move into Received Parcels.</p></div></section><section class="sc-panel"><div class="sc-panel-head"><div><h2>Latest Receive Scan</h2><p>Live scanner verification result.</p></div></div><div class="sc-panel-body"><div id="scReceiveFeedback" class="sc-feedback"><div><strong>Ready to receive</strong><small>Scan a parcel from the incoming batch list.</small></div></div></div></section></div>
<div class="dh-receive-note">Every Sorting Center transfer is a separate manifest. Seller pickups and buyer returns are grouped by <strong>scheduled date + barangay + pickup rider (seller and return parcels can share a batch)</strong>, so independent batches never mix.</div>
<section class="sc-panel sc-incoming-panel"><div class="sc-panel-head"><div><h2>Incoming Transfers & Barangay Collections</h2><p>Sorting Center deliveries plus pickups from sellers and buyer returns; Sorting transfers must be accounted for; barangay collections allow unsuccessful pickups.</p></div></div><div class="sc-toolbar"><input class="sc-input" id="scIncomingSearch" type="search" placeholder="Search batch, tracking, barangay, courier, truck…"><select class="sc-select" id="dhIncomingType"><option value="all">All Sources</option><option value="sorting">Sorting Center Transfers</option><option value="pickup">Seller Pickups</option><option value="return">Buyer Returns</option><option value="voided">Voided / Rescheduling</option></select><span class="sc-badge incoming" id="scIncomingLabel">0 parcels</span></div><div class="sc-table-wrap"><table class="sc-table"><thead><tr><th>Tracking Number</th><th>Source / Barangay</th><th>From</th><th>Assigned Courier / Pickup Rider</th><th>Truck / Vehicle</th></tr></thead><tbody id="scIncomingRows"></tbody></table></div></section></section>
<section class="sc-view" id="scTablesView" hidden><section class="sc-panel"><div class="sc-panel-head"><div><h2>Received Parcels at This Hub</h2><p>All successfully scanned parcels currently at this Delivery Hub, including seller pickups and buyer returns.</p></div></div><div class="sc-toolbar"><input class="sc-input" id="scReceivedSearch" type="search" placeholder="Search tracking, source, barangay…"><span class="sc-badge received" id="scReceivedLabel">0 parcels</span></div><div class="sc-table-wrap"><table class="sc-table"><thead><tr><th>Tracking Number</th><th>Source</th><th>Barangay / Destination</th><th>Received At</th><th>Next Processing</th></tr></thead><tbody id="scReceivedRows"></tbody></table></div></section></section>
<section class="sc-view" id="scDepartView" hidden><div class="sc-stats"><div class="sc-stat"><span>Ready to Process</span><strong id="scReadyCount">0</strong></div><div class="sc-stat"><span>Staged for Dispatch</span><strong id="scStagedCount">0</strong></div><div class="sc-stat"><span>Grouped Assignments</span><strong id="scRouteGroupsCount">0</strong></div><div class="sc-stat"><span>Out for Delivery / Sent</span><strong id="scLoadedCount">0</strong></div></div>
<div class="sc-split"><section class="sc-panel"><div class="sc-panel-head"><div><h2>Departure Parcel Scanner</h2><p>Delivery parcels go to the designated <strong>delivery rider per barangay</strong>. Seller pickups and buyer returns go to <strong id="dhAssignedCenterHeader">the assigned Sorting Center</strong>.</p></div></div><div class="sc-panel-body"><form id="scDepartForm" class="sc-scan-row"><input class="sc-input" id="scDepartCode" required maxlength="90" placeholder="Scan received tracking number"><button type="submit" class="sc-btn primary">Departure Scan</button><button class="sc-btn sc-camera-button" type="button" data-camera="depart">▣ Open Camera</button></form><p class="sc-help">Sorting Center batches cannot dispatch until every manifest member is received or reported Lost/Damaged. Barangay collections may dispatch partially if pickups are unsuccessful.</p></div></section><section class="sc-panel"><div class="sc-panel-head"><div><h2>Last Scanned Parcel</h2><p>Assigned destination, rider, and transportation.</p></div></div><div class="sc-panel-body"><div class="sc-feedback" id="scDepartFeedback"><div><strong>Waiting for scan</strong><small>Scan a received parcel to assign its route.</small></div></div></div></section></div>
<div class="sc-split"><section class="sc-panel"><div class="sc-panel-head"><div><h2>Ready Parcels</h2><p>Received parcels awaiting route assignment.</p></div></div><div class="sc-toolbar"><input class="sc-input" id="scReadySearch" placeholder="Search ready parcel…"></div><div class="sc-table-wrap"><table class="sc-table"><thead><tr><th>Tracking No.</th><th>Type</th><th>Barangay</th><th>Next Stage</th></tr></thead><tbody id="scReadyRows"></tbody></table></div></section><section class="sc-panel"><div class="sc-panel-head"><div><h2>Recent Scanner Activity</h2><p>Activities for this hub account.</p></div></div><div class="sc-activity" id="scActivity"></div></section></div>
<section class="sc-panel"><div class="sc-panel-head"><div><h2>Grouped Parcels for Dispatch</h2><p>By barangay + designated delivery rider, or by transfers to the assigned Sorting Center.</p></div></div><div class="sc-panel-body"><div class="sc-groups" id="scRouteGroups"></div></div></section><section class="sc-panel sc-depart-history" style="margin-top:18px"><div class="sc-panel-head"><div><h2>Depart History</h2><p>Outgoing Sorting Center transfers remain until the destination receives every parcel.</p></div><button class="sc-btn small" type="button" id="scHistoryOpen">View Depart History</button></div><div class="sc-history-list" id="scHistoryPreview"></div></section>
</section>
</div></main>
<div class="sc-modal" id="scHistoryModal" hidden><section class="sc-dialog sc-dialog-wide" role="dialog" aria-modal="true"><div class="sc-camera-head"><h2>Depart History</h2><button type="button" class="sc-btn small" id="scHistoryClose">Close</button></div><div id="scHistoryDetails"></div></section></div>
<div class="sc-toast" id="scToast" hidden role="status"></div>
<div class="sc-modal" id="scLoadModal" hidden><section class="sc-dialog" role="dialog" aria-modal="true"><h2 id="scLoadTitle">Confirm Dispatch</h2><p id="scLoadDescription"></p><div id="dhDispatchAssignment" class="dh-receive-note"></div><label class="sc-issue-field" id="dhTruckPickerRow" hidden>Available Returning Courier / Driver (from arrived provincial trucks) *<select id="dhAvailableTruck" class="sc-select" style="width:100%"><option value="">Select available courier / driver</option></select></label><label class="sc-issue-field">Vehicle / Truck Reference <input id="scTruckId" class="sc-input" maxlength="60" placeholder="Assigned vehicle or plate number" readonly></label><p class="dh-error" id="dhDispatchError"></p><p class="sc-demo-warning">Preview dispatch only. Backend trip assignment and Rider mobile updates are not connected.</p><div class="sc-dialog-actions"><button class="sc-btn" id="scLoadCancel" type="button">Cancel</button><button class="sc-btn primary" id="scLoadConfirm" type="button">Confirm Dispatch</button></div></section></div>
<div class="sc-modal" id="scCameraModal" hidden><section class="sc-dialog sc-camera-dialog" role="dialog" aria-modal="true"><div class="sc-camera-head"><h2 id="scCameraTitle">Scan Parcel Barcode</h2><button class="sc-btn small" type="button" id="scCameraClose">Close</button></div><video id="scCameraVideo" autoplay muted playsinline></video><div style="margin-top:12px"><label class="sc-btn small" for="scQrImageInput">Upload QR Image</label><input type="file" id="scQrImageInput" accept="image/*" style="position:absolute;width:1px;height:1px;opacity:0"></div><p class="sc-help" id="scCameraMessage">Allow camera access.</p></section></div>
<div class="pt-modal" id="scParcelDetails" aria-hidden="true"><div class="pt-dialog" role="dialog" aria-modal="true" aria-labelledby="scParcelDetailsTitle" tabindex="-1"><header class="pt-modal-head"><h2 id="scParcelDetailsTitle">Order Details</h2><button class="pt-x" id="scDetailsX" type="button">×</button></header><div id="scParcelDetailsBody" class="pt-modal-content"></div><div class="pt-modal-actions"><button class="sc-issue-button" id="scReportIssue" type="button">Report Issue</button><button class="pt-close" id="scDetailsClose" type="button">Close</button></div></div></div>
<div class="sc-modal" id="scIssueModal" hidden><section class="sc-dialog sc-issue-dialog" role="dialog" aria-modal="true"><h2>Report Parcel Issue</h2><p>Record missing or damaged parcel.</p><form id="scIssueForm" class="sc-issue-form"><div class="sc-issue-note">Tracking: <strong id="scIssueTracking">—</strong><br>Creates a demo full-value reimbursement record for the seller; no actual payment.</div><label class="sc-issue-field">Issue Type *<select id="scIssueType" required><option value="">Select issue</option><option value="Lost Parcel">Lost Parcel</option><option value="Damaged Parcel">Damaged Parcel</option><option value="Pickup Unsuccessful — Sender Not Available">Pickup Unsuccessful — Sender Not Available</option><option value="Pickup Unsuccessful — Parcel Not Ready">Pickup Unsuccessful — Parcel Not Ready</option><option value="Pickup Unsuccessful — Incorrect Address">Pickup Unsuccessful — Incorrect Address</option><option value="Pickup Unsuccessful — Customer Cancelled">Pickup Unsuccessful — Customer Cancelled</option></select></label><label class="sc-issue-field">Description *<textarea id="scIssueDescription" minlength="10" maxlength="1500" required placeholder="Describe what happened"></textarea></label><label class="sc-issue-field">Photo evidence (optional)<input type="file" id="scIssueEvidence" accept="image/png,image/jpeg,image/webp"></label><div class="sc-dialog-actions"><button class="sc-btn" id="scIssueCancel" type="button">Cancel</button><button class="sc-btn primary" type="submit">Submit Report</button></div></form></section></div>
<div class="sc-modal" id="dhRescheduleModal" hidden><section class="sc-dialog" role="dialog" aria-modal="true" aria-labelledby="dhRescheduleTitle"><h2 id="dhRescheduleTitle">Reschedule Barangay Pickup</h2><p id="dhRescheduleText">A buyer or seller must confirm the next collection date. This is a browser preview, not a customer booking.</p><label class="sc-issue-field">New collection date *<input id="dhRescheduleDate" type="date" required></label><p class="dh-error" id="dhRescheduleError"></p><div class="sc-dialog-actions"><button class="sc-btn" id="dhRescheduleCancel" type="button">Cancel</button><button class="sc-btn primary" id="dhRescheduleConfirm" type="button">Save Scheduled Date</button></div></section></div>
<script>document.addEventListener('DOMContentLoaded',()=>{
'use strict';
const $=id=>document.getElementById(id);
const hubId=@json($hubId),hubName=@json($hubName),municipality=@json($hubMunicipality),province=@json($hubProvince),centerId=@json($hubCenterId),accountId=@json($hubAccountId),serverCenterName=@json($assignedCenterName);
const networkKey='shopease-logistics-network-hubs-ui-v1';
function assignedSortingCenter(){
 if(serverCenterName.trim())return serverCenterName.trim();
 const nodes=read(networkKey);if(Array.isArray(nodes)){
  const match=nodes.find(c=>String(c.id)===String(centerId)||(Array.isArray(c.hubs)&&c.hubs.some(h=>String(h.id)===String(hubId))));
  if(match)return String(match.branch_name||match.name||'').trim();
 }
 const sc=read(scStoreKey);const manifest=sc?.manifests?.find(m=>String(m.originCenterId)===String(centerId)&&m.nextStop?.type==='hub'&&(String(m.nextStop.id)===String(hubId)||norm(m.nextStop.name)===norm(hubName)));
 return manifest?.originName||'Sorting Center name unavailable';
}
const storeKey='shopease-dh-scanner-preview-v1:'+accountId, scStoreKey='shopease-sc-scanner-transfers-demo-v1';
const esc=s=>String(s??'—').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const norm=s=>String(s??'').trim().toLowerCase(),now=()=>new Date().toISOString(),fmt=s=>{if(!s)return '—';const d=new Date(s);return isNaN(+d)?String(s):new Intl.DateTimeFormat('en-PH',{timeZone:'Asia/Manila',month:'short',day:'numeric',hour:'numeric',minute:'2-digit'}).format(d)};
const read=k=>{try{return JSON.parse(localStorage.getItem(k)||'null')}catch{return null}};
const phDay=(date=new Date())=>new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Manila',year:'numeric',month:'2-digit',day:'2-digit'}).format(date);
const scheduledDay=p=>String(p.scheduledDate||p.pickupDate||p.loadedAt||'').slice(0,10);
const isCommunity=p=>p?.kind==='pickup'||p?.kind==='return';
function addHistory(p,status,location,at=now(),details=''){p.history=Array.isArray(p.history)?p.history:[];if(!p.history.some(h=>h.status===status&&h.date===at))p.history.push({status,date:at,location,details});}

let data=read(storeKey);if(!data||!Array.isArray(data.parcels)||!Array.isArray(data.manifests))data={parcels:[],manifests:[],events:[]};
const riders=[
{id:'dh-delivery-poblacion',name:'Angela Reyes',barangay:'Poblacion',role:'delivery',vehicle:'Motorcycle · MC-701'},
{id:'dh-pickup-poblacion',name:'Rico Santos',barangay:'Poblacion',role:'pickup',vehicle:'Tricycle · TR-203'},
{id:'dh-delivery-pansol',name:'Bryan Mendoza',barangay:'Pansol',role:'delivery',vehicle:'Tricycle · TR-204'},
{id:'dh-pickup-pansol',name:'Cathy Cruz',barangay:'Pansol',role:'pickup',vehicle:'Motorcycle · MC-702'},
{id:'dh-delivery-santa',name:'Jessa Bautista',barangay:'Santa Clara Norte',role:'delivery',vehicle:'Motorcycle · MC-703'},
{id:'dh-pickup-santa',name:'Darren Lim',barangay:'Santa Clara Norte',role:'pickup',vehicle:'Tricycle · TR-205'}
]; // Fictional preview assignments, replace with approved riders from the backend.
const riderFor=(barangay,role)=>riders.find(r=>norm(r.barangay)===norm(barangay)&&r.role===role);
const displayAssignee=(name,phone)=>`<span class="sc-assignee"><strong>${esc(name||'Not recorded')}</strong><small class="sc-assignee-phone">${esc(phone||'Phone not recorded')}</small></span>`;
const assignedPhone=(m,p)=>{const matched=riders.find(r=>norm(r.name)===norm(m?.pickupRider||p?.courierName));return m?.courierPhone||m?.riderPhone||m?.driverPhone||p?.courierPhone||p?.riderPhone||p?.driverPhone||matched?.phone||matched?.phone_number||'';};
const assignedVehicle=(m,p)=>{const matched=riders.find(r=>norm(r.name)===norm(m?.pickupRider||p?.courierName));return (m?.sourceType==='collection'||p?.kind==='pickup'||p?.kind==='return')?(matched?.vehicle||p?.riderVehicle||m?.vehicleType||m?.truckId||p?.truckId||'Vehicle not recorded'):(m?.vehicleType||m?.truckId||p?.truckId||'Vehicle not recorded');};
const issueKey='shopease-dh-issues-preview-'+accountId;
const issueReports=()=>read(issueKey)||[];
const issuesFor=tracking=>issueReports().filter(r=>norm(r.tracking)===norm(tracking));
const isReported=p=>!!p&&(p.reportedIssue===true||issuesFor(p.tracking).length>0);
const findParcel=tracking=>data.parcels.find(p=>norm(p.tracking)===norm(tracking));
const event=(action,p,desc)=>{data.events.unshift({at:now(),action,tracking:p?.tracking||'',desc});data.events=data.events.slice(0,100)};
const persist=()=>{localStorage.setItem(storeKey,JSON.stringify(data));render()};
function say(message){$('scToast').textContent=message;$('scToast').hidden=false;clearTimeout(say.timer);say.timer=setTimeout(()=>$('scToast').hidden=true,4400)}
function feedback(id,style,title,sub,route){const el=$(id);el.className='sc-feedback '+(style||'');el.innerHTML=`<div><strong>${esc(title)}</strong><small>${esc(sub)}</small>${route?`<div class="sc-route">${esc(route)}</div>`:''}</div>`}
function seed(){const flag='shopease-dh-scanner-demo-seeded-v2-'+accountId;if(localStorage.getItem(flag)==='1')return;
 const batchTemplates=[
 {id:'DH-SC-301',kind:'sorting',origin:assignedSortingCenter(),barangay:'Poblacion',courier:'Mark Angelo Reyes',truck:'Isuzu Forward · ABC 1234',prefix:'SHP-DH-SC',states:['incoming','incoming','received'],count:3},
 {id:'DH-SC-302',kind:'sorting',origin:assignedSortingCenter(),barangay:'Pansol',courier:'Joshua Miguel Santos',truck:'Hino 500 · DEF 5678',prefix:'SHP-DH-SC2',states:['incoming','incoming'],count:2},
 {id:'DH-COLLECTION-303',kind:'pickup',origin:'Barangay Poblacion Collections',barangay:'Poblacion',courier:'Rico Santos',truck:'Tricycle · TR-203',prefix:'SHP-DH-PU',states:['incoming','incoming'],count:2},
 {id:'DH-COLLECTION-304',kind:'return',origin:'Barangay Pansol Collections',barangay:'Pansol',courier:'Cathy Cruz',truck:'Motorcycle · MC-702',prefix:'SHP-DH-RT',states:['incoming','incoming'],count:2},
 {id:'DH-COLLECTION-303',kind:'return',origin:'Barangay Poblacion Collections',barangay:'Poblacion',courier:'Rico Santos',truck:'Tricycle · TR-203',prefix:'SHP-DH-RT-MIX',states:['incoming'],count:1},
 {id:'DH-PICKUP-305',kind:'pickup',origin:'Barangay Santa Clara Norte Sellers',barangay:'Santa Clara Norte',courier:'Darren Lim',truck:'Tricycle · TR-205',prefix:'SHP-DH-PU2',states:['incoming'],count:1},
 {id:'DH-READY-306',kind:'sorting',origin:assignedSortingCenter(),barangay:'Pansol',courier:'Joshua Miguel Santos',truck:'Hino 500 · DEF 5678',prefix:'SHP-DH-READY',states:['received','received'],count:2}
 ];
 for(const b of batchTemplates){const ids=[];b.states.forEach((state,i)=>{const tracking=b.prefix+'-'+String(i+1).padStart(3,'0');const id=tracking+'-'+accountId;ids.push(id);if(findParcel(tracking))return;const at=new Date(Date.now()-(i+1)*3600000).toISOString();const value=450+i*175;
 const p={id,tracking,status:state,kind:b.kind,barangay:b.barangay,destProvince:b.kind==='sorting'?province:'Cebu',destCity:b.kind==='sorting'?municipality:'Cebu City',destBarangay:b.kind==='sorting'?b.barangay:'Lahug',receivedManifestId:state==='received'?b.id:null,transferId:b.id,receivedAt:state==='received'?at:null,loadedAt:at,scheduledDate:phDay(),loadedFromName:b.origin,receivedFrom:b.origin,courierName:b.courier,truckId:b.truck,
 buyer_name:'Sample Buyer '+(i+1),buyer_email:'buyer'+(i+1)+'@example.test',buyer_phone:'0917123456'+i,buyer_province:b.kind==='pickup'?'Cebu':(b.kind==='return'?'Cavite':province),buyer_city:b.kind==='pickup'?'Cebu City':(b.kind==='return'?'Dasmariñas':municipality),buyer_barangay:b.kind==='pickup'?'Lahug':(b.kind==='return'?'Salitran':b.barangay),buyer_street:'Sample Street',buyer_house_no:'12',buyer_postal_code:b.kind==='pickup'?'6000':'4010',seller_name:'ShopEase Demo Seller',seller_email:'seller@example.test',seller_phone:'09181234567',seller_province:b.kind==='return'?'Batangas':(b.kind==='sorting'?'Metro Manila':province),seller_city:b.kind==='return'?'Lipa City':(b.kind==='sorting'?'Quezon City':municipality),seller_barangay:b.kind==='return'?'Sabang':(b.kind==='sorting'?'Bagumbayan':b.barangay),seller_street:'Merchant Street',seller_house_no:'5',seller_postal_code:b.kind==='return'?'4217':'4010',order_number:'DH-ORDER-'+(300+i),ordered_at:at,estimated_delivery:new Date(Date.now()+86400000).toISOString(),payment_method:'Cash on Delivery',total_amount:value,notes:'Frontend demonstration record only.',items:[{product_name:b.kind==='return'?'Returned Item':b.kind==='pickup'?'Seller Pickup Package':'ShopEase Parcel',variation:'Standard',color:'N/A',size:'N/A',quantity:1,product_price:value}],history:b.kind==='sorting'?[{status:'In Transit',date:at,location:b.origin,details:'Sorting Center dispatched to '+hubName}]:[{status:'Processing — Awaiting Pickup',date:at,location:b.barangay+', '+municipality,details:'Pickup scheduled with barangay collection rider'}]};data.parcels.push(p)});
 const existing=data.manifests.find(m=>m.id===b.id);if(existing){existing.parcelIds=[...new Set([...(existing.parcelIds||[]),...ids])];if(b.kind!=='sorting')existing.sourceType='collection';}else data.manifests.push({id:b.id,sourceType:b.kind==='sorting'?'sorting':'collection',originName:b.origin,barangay:b.barangay,receivedDate:phDay(),pickupRider:b.courier,truckId:b.truck,status:'loaded',parcelIds:ids});}
 localStorage.setItem(flag,'1');persist();}
// All direct inbound Sorting Center batches come exclusively from this hub's assigned provincial Sorting Center.
// Previous upstream Sorting Centers may appear in parcel tracking history, never as the sender to this hub.
function seedCrossCenterAndReturnExamples(){
 const flag='shopease-dh-multi-origin-destination-preview-v3-'+accountId;
 if(localStorage.getItem(flag)==='1')return;
 const batchSpecs=[
  {id:'DH-SC-PROV-401',origin:assignedSortingCenter(),courier:'Mark Angelo Reyes',truck:'Truck · LAG 4101',barangay:'Poblacion',returnIndexes:[1],paths:['Provincial Sorting Center → '+hubName]},
  {id:'DH-SC-INTER-402',origin:assignedSortingCenter(),courier:'Joshua Santos',truck:'Truck · LAG 4202',barangay:'Pansol',returnIndexes:[0],paths:['Batangas Sorting Center',''+(province||'Laguna')+' Provincial Sorting Center']}
 ];
 let changed=false;
 for(const batch of batchSpecs){
  const ids=[];
  for(let i=0;i<3;i++){
   const tracking='SHP-DH-'+(batch.id.includes('PROV')?'PROV':'INTER')+'-'+String(i+1).padStart(3,'0');
   const id='multi-origin-'+tracking+'-'+accountId;ids.push(id);
   if(findParcel(tracking))continue;
   const isReturn=batch.returnIndexes.includes(i), at=new Date(Date.now()-(i+1)*3600000).toISOString();
   const originIsRemote=batch.id.includes('INTER');
   const originCity=originIsRemote?'Lipa City':(municipality||'Pila');
   const destinationBarangay=i===2?'Santa Clara Norte':batch.barangay;
   const sellerLocation=isReturn?{province:province||'Laguna',city:municipality||'Pila',barangay:destinationBarangay}:{province:'Batangas',city:'Lipa City',barangay:'Sabang'};
   const buyerLocation=isReturn?{province:i%2?'Cebu':'Batangas',city:i%2?'Cebu City':'Lipa City',barangay:i%2?'Lahug':'Sabang'}:{province:province||'Laguna',city:municipality||'Pila',barangay:destinationBarangay};
   const history=[{status:'Processing',date:new Date(Date.now()-7200000).toISOString(),location:isReturn?'Buyer return requested':'Seller prepared order',details:isReturn?'Return approved; destination is the seller':'Normal shipment; destination is the buyer'},...batch.paths.map((location,j)=>({status:'In Transit — Sorting Center',date:new Date(Date.now()-(j+2)*3600000).toISOString(),location,details:j===batch.paths.length-1?'Transferred toward '+hubName:'Scanned at transfer Sorting Center'})),{status:'In Transit',date:at,location:batch.origin,details:'Incoming batch '+batch.id+' dispatched to '+hubName}];
   data.parcels.push({id,tracking,kind:'sorting',isReturn,parcelType:isReturn?'return':'normal',status:i===2?'received':'incoming',barangay:destinationBarangay,destProvince:isReturn?sellerLocation.province:buyerLocation.province,destCity:isReturn?sellerLocation.city:buyerLocation.city,destBarangay:destinationBarangay,transferId:batch.id,receivedManifestId:i===2?batch.id:null,receivedAt:i===2?at:null,loadedAt:at,loadedFromName:batch.origin,receivedFrom:batch.origin,courierName:batch.courier,truckId:batch.truck,order_number:'DH-EXAMPLE-'+tracking,ordered_at:at,estimated_delivery:new Date(Date.now()+86400000).toISOString(),buyer_name:'Sample Buyer '+(i+1),buyer_email:'buyer.'+i+'@example.test',buyer_phone:'09171234560',buyer_province:buyerLocation.province,buyer_city:buyerLocation.city,buyer_barangay:buyerLocation.barangay,buyer_street:'Buyer Street',buyer_house_no:'12',buyer_postal_code:'4009',seller_name:'Example Seller '+(i+1),seller_email:'seller.'+i+'@example.test',seller_phone:'09181234560',seller_province:sellerLocation.province,seller_city:sellerLocation.city,seller_barangay:sellerLocation.barangay,seller_street:'Seller Street',seller_house_no:'25',seller_postal_code:'4217',payment_method:'Cash on Delivery',total_amount:590+i*150,notes:isReturn?'Buyer return, deliver to seller address':'Normal delivery, deliver to buyer address',items:[{product_name:isReturn?'Returned Merchandise':'Demo ShopEase Order',variation:'Standard',color:'N/A',size:'N/A',quantity:1,product_price:590+i*150}],history});changed=true;
  }
  if(!data.manifests.some(m=>String(m.id)===batch.id)){
   data.manifests.push({id:batch.id,sourceType:'sorting',originName:batch.origin,barangay:batch.barangay,receivedDate:now(),pickupRider:batch.courier,truckId:batch.truck,status:'loaded',parcelIds:ids});changed=true;
  }
 }
 if(changed)persist();localStorage.setItem(flag,'1');
}
// Correct previews previously seeded with an out-of-province direct sender.
// Keep upstream route events in history, and preserve all scan / report / dispatch statuses.
function normalizeSortingCenterOrigins(){
 const provincial=assignedSortingCenter();
 let changed=false;
 const demoIds=new Set(['DH-SC-INTER-402','DH-SC-PROV-401']);
 for(const m of data.manifests){
  if(!demoIds.has(String(m.id)))continue;
  if(m.originName!==provincial){m.originName=provincial;changed=true;}
 }
 for(const p of data.parcels){
  if(!/^SHP-DH-(INTER|PROV)-/i.test(String(p.tracking||'')))continue;
  if(p.loadedFromName!==provincial){p.loadedFromName=provincial;changed=true;}
  if(p.receivedFrom&&p.receivedFrom!==provincial){p.receivedFrom=provincial;changed=true;}
  // Older final 'In Transit' entries represented a remote center as the direct shipper.
  for(const h of (p.history||[])){
   if(h.details?.includes('Incoming batch DH-SC-INTER-402 dispatched to')&&h.location!==provincial){h.location=provincial;changed=true;}
  }
 }
 if(changed)localStorage.setItem(storeKey,JSON.stringify(data));
}
// Normalize original examples without discarding the user's already-scanned state.
function normalizeDemoDestinations(){let changed=false;
 for(const p of data.parcels){
  if(!String(p.tracking||'').startsWith('SHP-DH-'))continue;
  if(p.kind==='return' && p.isReturn!==true){p.isReturn=true;changed=true;}
  if(p.kind==='pickup' && p.isReturn!==false){p.isReturn=false;changed=true;}
  if(p.kind==='sorting' && p.isReturn==null){p.isReturn=/RETURN|\bRT\b/i.test(String(p.parcelType||p.tracking||''));changed=true;}
  if(isCommunity(p)){
   // Collection location is this hub's barangay, but the receiving party may be in a different province.
   const outside=p.isReturn?{province:'Batangas',city:'Lipa City',barangay:'Sabang'}:{province:'Cebu',city:'Cebu City',barangay:'Lahug'};
   const fields=p.isReturn?['seller_province','seller_city','seller_barangay']:['buyer_province','buyer_city','buyer_barangay'];
   if(norm(p[fields[0]])===norm(province)&&norm(p[fields[1]])===norm(municipality)){
    [p[fields[0]],p[fields[1]],p[fields[2]]]=[outside.province,outside.city,outside.barangay];changed=true;
   }
  }
 }
 if(changed)localStorage.setItem(storeKey,JSON.stringify(data));
}
// Optionally import actual SC-to-hub preview manifests; don't reinterpret unrelated manifests.
function importSortingCenterTransfers(){const sc=read(scStoreKey);if(!sc||!Array.isArray(sc.manifests)||!Array.isArray(sc.parcels))return;
 let changed=false;for(const m of sc.manifests){if(m.status!=='loaded'||m.nextStop?.type!=='hub')continue;
 if(String(m.nextStop.id)!==String(hubId)&&norm(m.nextStop.name)!==norm(hubName))continue;
 // Only the sorting center associated with this delivery hub may dispatch directly to it.
 if(!centerId||!m.originCenterId||String(m.originCenterId)!==String(centerId))continue;
 const mid=String(m.id);const ids=[];for(const pid of (m.parcelIds||[])){const source=sc.parcels.find(x=>String(x.id)===String(pid));if(!source||!source.tracking)continue;const localId='sc-import-'+pid;ids.push(localId);if(findParcel(source.tracking))continue;
 data.parcels.push({...source,id:localId,kind:'sorting',isReturn:Boolean(source.isReturn||source.is_return||String(source.parcelType||source.parcel_type||source.order_type||'').toLowerCase().includes('return')),status:'incoming',transferId:mid,barangay:(source.isReturn?source.seller_barangay:source.buyer_barangay)||source.destBarangay||'',destBarangay:(source.isReturn?source.seller_barangay:source.buyer_barangay)||source.destBarangay||'',loadedFromName:m.originName||'Sorting Center',courierName:m.courierName||'Unassigned courier',truckId:m.truckId||'',reportedIssue:false,receivedAt:null,history:[...(Array.isArray(source.history)?source.history:[]),{status:'In Transit',date:m.createdAt||now(),location:m.originName||'Origin Sorting Center',details:'Dispatched to '+hubName}]});changed=true;}
 if(ids.length&&!data.manifests.some(x=>String(x.id)===mid)){data.manifests.push({id:mid,sourceType:'sorting',originName:m.originName||'Sorting Center',truckId:m.truckId||'',pickupRider:m.courierName||'Unassigned courier',receivedDate:m.createdAt,status:'loaded',parcelIds:ids});changed=true;}}
 if(changed)persist();}
function expireMissedCollections(){const today=phDay();let changed=false;for(const p of data.parcels){if(isCommunity(p)&&!p.scheduledDate&&!p.pickupDate){p.scheduledDate=today;changed=true;if(Array.isArray(p.history)&&p.history.length===1&&p.history[0].status==='In Transit to Delivery Hub'){p.history=[{status:'Processing — Awaiting Pickup',date:p.history[0].date,location:p.barangay+', '+municipality,details:'Original preview pickup; awaiting collection'}];}}if(!isCommunity(p)||p.status!=='incoming'||isReported(p))continue;const day=scheduledDay(p);if(day&&day<today){p.status='voided';p.voidedAt=now();p.rescheduleRequired=true;addHistory(p,'Pickup Voided — Reschedule Required',p.barangay+', '+municipality,p.voidedAt,'Pickup not received on scheduled date '+day);event('Pickup voided',p,'Buyer/seller must arrange another pickup date');changed=true;}}if(changed)localStorage.setItem(storeKey,JSON.stringify(data));return changed;}
const incoming=()=>data.parcels.filter(p=>p.status==='incoming'&&!isReported(p)&&(p.kind==='sorting'||scheduledDay(p)===phDay()));
const voided=()=>data.parcels.filter(p=>p.status==='voided'&&isCommunity(p)&&!isReported(p));
const received=()=>data.parcels.filter(p=>['received','staged'].includes(p.status)&&!isReported(p));
const ready=()=>data.parcels.filter(p=>p.status==='received'&&!isReported(p));
function checkReceive(tracking){const p=findParcel(tracking);if(!p){feedback('scReceiveFeedback','bad','Not in incoming list','The tracking number does not belong to this delivery hub.');return}if(isReported(p)){feedback('scReceiveFeedback','warn','Already reported','This parcel is in Delivery Hub Parcel Disputes.');return}if(p.status!=='incoming'){feedback('scReceiveFeedback','warn','Already processed',p.tracking+' is not awaiting a receive scan.');return}
 const manifest=data.manifests.find(m=>String(m.id)===String(p.transferId));if(!manifest||manifest.status!=='loaded'){feedback('scReceiveFeedback','bad','Invalid manifest','No confirmed incoming batch for this tracking number.');return}
 if(isCommunity(p)&&scheduledDay(p)!==phDay()){feedback('scReceiveFeedback','bad','Pickup schedule expired','Buyer/seller must reschedule the barangay pickup before receiving.');return;}p.status='received';p.receivedAt=now();p.receivedManifestId=manifest.id;p.receivedFrom=manifest.originName;addHistory(p,isCommunity(p)?'In Transit — Received at Delivery Hub':'Received at Delivery Hub',hubName,p.receivedAt,isCommunity(p)?'Collected from '+p.barangay+'; forwarding to provincial Sorting Center':'Arrived from '+manifest.originName);event('Received',p,'Received '+p.kind+' parcel from '+manifest.originName);if(manifest.sourceType==='sorting'&&(manifest.parcelIds||[]).every(id=>{const member=data.parcels.find(y=>String(y.id)===String(id));return member&&(member.status!=='incoming'||isReported(member));})){manifest.arrivedAt=now();const originStore=read(scStoreKey);if(originStore?.manifests){const original=originStore.manifests.find(m=>String(m.id)===String(manifest.id));if(original&&original.status==='loaded'){original.arrivedAt=manifest.arrivedAt;original.status='arrived';localStorage.setItem(scStoreKey,JSON.stringify(originStore));}}}persist();feedback('scReceiveFeedback','good','Parcel received',p.tracking+' moved to Received Parcels.',manifest.originName);say('Received '+p.tracking)}
function arrivingTrucks(){return data.manifests.filter(m=>m.sourceType==='sorting'&&m.status==='loaded'&&m.truckId&&m.parcelIds?.length&&(m.parcelIds||[]).every(id=>{const p=data.parcels.find(x=>String(x.id)===String(id));return p&&(p.status!=='incoming'||isReported(p));})).map(m=>({id:String(m.id),truck:m.truckId,courier:m.pickupRider||m.courierName||'Courier not recorded',source:m.originName}));}
function truckBusy(t){return data.manifests.some(m=>m.sourceType==='transfer-to-sorting'&&m.status==='loaded'&&!m.arrivedAt&&String(m.returningTruckManifestId)===String(t.id));}
function refreshTruckPicker(){const trucks=arrivingTrucks();$('dhAvailableTruck').innerHTML='<option value="">Select available returning courier / driver</option>'+trucks.map(t=>`<option value="${esc(t.id)}" ${truckBusy(t)?'disabled':''}>${esc(t.courier)} · ${esc(t.truck)}${truckBusy(t)?' · Unavailable':''}</option>`).join('');$('scTruckId').value='';}
$('dhAvailableTruck').addEventListener('change',()=>{const t=arrivingTrucks().find(x=>x.id===$('dhAvailableTruck').value);$('scTruckId').value=t?.truck||'';});
$('dhAssignedCenterHeader') && ($('dhAssignedCenterHeader').textContent=assignedSortingCenter());
function routeFor(p){if(p.kind==='sorting'){if(norm(p.isReturn?p.seller_city:(p.buyer_city||p.destCity)||municipality)!==norm(municipality))return {error:'Destination is outside this delivery hub municipality.'};const barangay=(p.isReturn?p.seller_barangay:p.buyer_barangay)||p.destBarangay||p.barangay;const rider=riderFor(barangay,'delivery');if(!rider)return {error:'No designated delivery rider for barangay '+(barangay||'(missing)')+'. Assign a delivery rider before dispatch.'};return {type:'delivery',destination:barangay,rider,groupKey:'delivery|'+norm(barangay)+'|'+rider.id}};
 const destination=assignedSortingCenter();if(!centerId||!destination||destination==='Sorting Center name unavailable')return {error:'Assigned Sorting Center was not found for this Delivery Hub account.'};return {type:'sorting',destination,destinationCenterId:centerId,rider:null,groupKey:'sorting|'+String(centerId)};}
function checkDepart(tracking){const p=findParcel(tracking);if(!p){feedback('scDepartFeedback','bad','Parcel not found','No matching parcel at this delivery hub.');return}if(isReported(p)){feedback('scDepartFeedback','warn','Reported parcel','Cannot dispatch a reported parcel.');return}if(p.status!=='received'){feedback('scDepartFeedback','warn','Parcel not ready',p.status==='staged'?'Already staged for dispatch.':'Receive-scan this parcel first.');return}const route=routeFor(p);if(route.error){feedback('scDepartFeedback','bad','Rider assignment needed',route.error);return}p.status='staged';p.nextStop=route;p.stagedAt=now();event('Departure scan',p,(route.type==='sorting'?'Staged for transportation':'Assigned to '+route.rider.name)+' · '+route.destination);persist();feedback('scDepartFeedback','good',route.type==='sorting'?'Ready for Sorting Center loading':'Assigned to '+route.rider.name,route.type==='sorting'?'Select the available returning courier and truck when clicking Loaded.':'Barangay: '+p.barangay+' · '+route.rider.role+' rider',route.destination);say('Grouped for '+route.destination)}
function stagedGroups(){const map=new Map();for(const p of data.parcels.filter(x=>x.status==='staged'&&!isReported(x))){const r=p.nextStop;if(!r)continue;const batchId=String(p.receivedManifestId||p.transferId||'unassigned');const key=r.type==='sorting'?'sorting|'+String(centerId):(batchId+'|'+r.groupKey);if(!map.has(key))map.set(key,{key,batchId,route:r,parcels:[]});map.get(key).parcels.push(p)}return [...map.values()];}
function blockedMembers(g){if(g.route.type==='sorting')return [];const m=data.manifests.find(x=>String(x.id)===g.batchId);if(!m)return [];return (m.parcelIds||[]).map(id=>data.parcels.find(p=>String(p.id)===String(id))).filter(p=>p&&p.status==='incoming'&&!isReported(p));}
let activeIssueTracking='',pendingGroup=null;
function openIssueModal(){if(!activeIssueTracking)return;const parcel=findParcel(activeIssueTracking);if(parcel?.status==='voided'){say('Pickup already voided; reschedule instead.');return;}$('scIssueForm').reset();for(const opt of $('scIssueType').options){if(opt.value.startsWith('Pickup Unsuccessful'))opt.hidden=!isCommunity(parcel);else if(['Lost Parcel','Damaged Parcel'].includes(opt.value))opt.hidden=isCommunity(parcel)&&parcel.status==='incoming';}$('scIssueTracking').textContent=activeIssueTracking;$('scIssueModal').hidden=false}
function closeIssueModal(){$('scIssueModal').hidden=true}
$('scReportIssue').onclick=openIssueModal;$('scIssueCancel').onclick=closeIssueModal;
$('scIssueModal').addEventListener('click',e=>{if(e.target===$('scIssueModal'))closeIssueModal()});
$('scIssueForm').addEventListener('submit',e=>{e.preventDefault();if(!$('scIssueForm').reportValidity())return;const p=findParcel(activeIssueTracking);if(!p)return;const f=$('scIssueEvidence').files[0];if(f&&(f.size>5*1024*1024||!['image/png','image/jpeg','image/webp'].includes(f.type))){say('Evidence must be JPG, PNG or WebP under 5 MB');return}
if(isCommunity(p)&&p.status==='incoming'&&$('scIssueType').value.startsWith('Pickup Unsuccessful')){const at=now(),type=$('scIssueType').value;const id='DH-VOID-'+Date.now();p.status='voided';p.voidedAt=at;p.rescheduleRequired=true;p.pickupFailureReason=type;addHistory(p,'Pickup Voided — Reschedule Required',p.barangay+', '+municipality,at,type+' — '+$('scIssueDescription').value.trim());event('Pickup voided',p,type);persist();closeIssueModal();closeParcelDetails();say('Pickup voided. Buyer/seller may reschedule.');return;}
const at=now(),value=Number(p.total_amount),amount=Number.isFinite(value)&&value>0?value.toFixed(2):'';const id='DH-ISS-'+Date.now();const entry={id,tracking:p.tracking,type:$('scIssueType').value,description:$('scIssueDescription').value.trim(),evidenceName:f?.name||'',at,hubId,status:'Open',accountability:{party:'Logistics Partner',resolution:'Full Parcel Value Reimbursement to Seller',parcelAmount:amount,compensation:amount,at,simulatedReimbursedAt:amount?at:null,status:amount?'Reimbursed · UI Simulation':'Amount Verification Required · Demo'}};
localStorage.setItem(issueKey,JSON.stringify([entry,...issueReports()]));p.reportedIssue=true;p.reportedAt=at;p.reportId=id;p.status='reported';p.nextStop=null;event('Issue reported',p,entry.type);if(p.kind==='sorting'){const m=data.manifests.find(m=>String(m.id)===String(p.transferId));if(m&&(m.parcelIds||[]).every(id=>{const member=data.parcels.find(y=>String(y.id)===String(id));return member&&(member.status!=='incoming'||isReported(member));})){m.arrivedAt=now();const originStore=read(scStoreKey);const original=originStore?.manifests?.find(x=>String(x.id)===String(m.id));if(original){original.arrivedAt=m.arrivedAt;original.status='arrived';localStorage.setItem(scStoreKey,JSON.stringify(originStore));}}}persist();closeIssueModal();closeParcelDetails();say('Parcel reported and removed from scanner lists. View Delivery Hub Parcel Disputes.');});
const modalCard=(title,body)=>`<section class="pt-detail-card"><div class="pt-detail-title">${esc(title)}</div><div class="pt-detail-body">${body}</div></section>`;

const modalField=(label,value)=>`<div class="pt-field"><label>${esc(label)}</label><strong>${esc(value??'—')}</strong></div>`;

const modalPair=(label,value)=>`<div class="pt-info-pair"><label>${esc(label)}</label><strong>${esc(value??'—')}</strong></div>`;

function destinationRole(p){return p.isReturn===true||p.kind==='return'||String(p.parcelType||p.parcel_type||p.order_type||'').toLowerCase().includes('return')?'Seller':'Buyer'}
function destinationAddress(p){const b=destinationRole(p).toLowerCase();return [p[b+'_house_no'],p[b+'_street'],p[b+'_barangay'],p[b+'_city'],p[b+'_province'],p[b+'_postal_code']].filter(Boolean).join(', ')}
function modalParty(role,p){let b=role==='Buyer'?'buyer':'seller';const isDest=destinationRole(p)===role;const title=role+' Information'+(isDest?' · DELIVERY DESTINATION':'');const body=(isDest?'<span class="pt-destination-pill">DESTINATION ADDRESS</span><p class="pt-desc">'+esc(p.isReturn?'Buyer return → Seller':'Normal parcel → Buyer')+'</p>':'')+modalPair('Name',p[b+'_name'])+modalPair('Email',p[b+'_email'])+modalPair('Contact',p[b+'_phone'])+modalPair('Province',p[b+'_province'])+modalPair('Municipality',p[b+'_city'])+modalPair('Barangay',p[b+'_barangay'])+modalPair('Street',p[b+'_street'])+modalPair('House No.',p[b+'_house_no'])+modalPair('ZIP',p[b+'_postal_code']);return `<section class="pt-detail-card ${isDest?'pt-destination-card':''}"><div class="pt-detail-title">${esc(title)}</div><div class="pt-detail-body">${body}</div></section>`;}

let detailsPreviousFocus=null;

function showParcelDetails(raw){const p=findParcel(raw);if(!p)return;detailsPreviousFocus=document.activeElement;activeIssueTracking=p.tracking;

 const processState=p.status==='voided'?'Pickup Voided — Awaiting Reschedule':p.status==='incoming'?(isCommunity(p)?'Processing':'In Transit'):p.status==='received'?'Arrived at Delivery Hub':p.status==='staged'?'Preparing for Departure':p.status==='loaded'?'In Transit':'Processing';

 // Exact four-stage icon style used by Logistics Parcel Tracking.

 const progressStage = p.status==='loaded' ? 1 : (['received','ready','staged'].includes(p.status)||(p.status==='incoming'&&!isCommunity(p))) ? 1 : p.status==='delivered' ? 3 : p.status==='out-for-delivery' ? 2 : 0;

 const progressLabels=['Processing','In Transit','Out for Delivery','Delivered'];

 const progressIcons=progressLabels.map((label,i)=>{

   if(i===0)return progressStage>0?'in-transit-check.png':'processing.png';

   if(i===1)return progressStage>1?'in-transit-check.png':progressStage===1?'in-transit-blue.png':'in-transit-gray.png';

   if(i===2)return progressStage>2?'delivered-check.png':progressStage===2?'out-for-delivery.png':'out-for-delivery-gray.png';

   return progressStage===3?'delivered-check.png':'delivered-gray.png';

 });

 const dots=`<div class="pt-shipping-progress" role="img" aria-label="Tracking Progress">${progressLabels.map((label,i)=>`<div class="pt-progress-item"><img src="/icons/seller/shipping-status/${progressIcons[i]}" alt=""><small>${label}</small></div>${i<progressLabels.length-1?`<span class="pt-progress-line ${i<progressStage?'done':''}"></span>`:''}`).join('')}</div>`;

 const items=(p.items||[]).map((it,i)=>`<article class="pt-order-item"><div class="pt-order-item-head"><span class="pt-order-item-index">Item ${i+1}</span><div class="pt-order-item-name">${esc(it.product_name)}</div></div><div class="pt-order-item-body"><div class="pt-order-item-fields">${modalField('Variation',it.variation)}${modalField('Color',it.color)}${modalField('Size',it.size)}${modalField('Quantity Ordered',it.quantity)}${modalField('Unit Price','₱ '+Number(it.product_price||0).toFixed(2))}</div></div></article>`).join('');

 const hist=(p.history||[]).map(x=>`<li><strong>${esc(x.status)}</strong><small>${esc(fmt(x.date))} · ${esc(x.location)}${x.details?' · '+esc(x.details):''}</small></li>`).join('');

 $('scParcelDetailsBody').innerHTML=`<div class="pt-destination-banner">${esc(destinationRole(p)==='Seller'?'BUYER RETURN · Destination: Seller':'Destination: Buyer')}<strong>${esc(destinationAddress(p))}</strong></div>`+modalCard('Order Information',`<div class="pt-fields">${modalField('Order Number',p.order_number)}${modalField('Ordered At',fmt(p.ordered_at))}${modalField('Estimated Delivery',fmt(p.estimated_delivery))}${modalField('Tracking Number',p.tracking)}${modalField('Current Location',p.status==='incoming'?'On the way to this delivery hub':hubName)}${modalField('Shipping Status',processState)}</div>`)+modalCard('Tracking Progress',dots)+`<div class="pt-party-grid">${modalParty('Buyer',p)}${modalParty('Seller',p)}</div>`+modalCard('Payment Information',`<div class="pt-fields">${modalField('Payment Method',p.payment_method)}${modalField('Order Total','₱ '+Number(p.total_amount||0).toFixed(2))}</div>`)+modalCard('Ordered Items',`<div class="pt-item-stack">${items||'<p>No items available.</p>'}</div>`)+modalCard('Tracking History',`<ul class="pt-history">${hist}</ul>`)+modalCard('Customer Notes',`<div class="pt-notes">${esc(p.notes)}</div>`)+modalCard('Reported Issues',issuesFor(p.tracking).map(r=>`<div class="sc-issue-record"><strong>${esc(r.type)} · ${esc(r.status)}</strong><br>${esc(r.description)}<br><small>${esc(fmt(r.at))}${r.evidenceName?' · Evidence: '+esc(r.evidenceName):''}</small></div>`).join('')||'<p class="pt-placeholder">No issues reported for this parcel.</p>')+modalCard('Parcel QR Code',`<div class="sc-qr-placeholder">QR code will be available once provided by the backend.</div>`);

 $('scParcelDetails').classList.add('open');$('scParcelDetails').setAttribute('aria-hidden','false');document.body.classList.add('pt-modal-open');$('scDetailsX').focus();

}

function closeParcelDetails(){const m=$('scParcelDetails');m.classList.remove('open');m.setAttribute('aria-hidden','true');document.body.classList.remove('pt-modal-open');detailsPreviousFocus?.focus?.()}

for(const id of ['scDetailsX','scDetailsClose'])$(id).addEventListener('click',closeParcelDetails);

$('scParcelDetails').addEventListener('click',e=>{if(e.target===$('scParcelDetails'))closeParcelDetails()});

document.addEventListener('keydown',e=>{if(e.key==='Escape'&&$('scParcelDetails').classList.contains('open'))closeParcelDetails()});

for(const id of ['scIncomingRows','scReceivedRows','scReadyRows'])$(id).addEventListener('click',e=>{let row=e.target.closest('[data-parcel]');if(row)showParcelDetails(row.dataset.parcel)});



function render(){syncArrivalFromSorting();drawDepartHistory();const inc=incoming(),rec=received(),rdy=ready(),grp=stagedGroups(),sent=data.parcels.filter(p=>['out-for-delivery','sent-to-sorting'].includes(p.status));
$('scReceiveBadge').textContent=rec.length;$('scDepartBadge').textContent=grp.length;$('scIncomingLabel').textContent=inc.length+' parcels';$('scReceivedLabel').textContent=rec.length+' parcels';$('scReadyCount').textContent=rdy.length;$('scStagedCount').textContent=grp.reduce((n,g)=>n+g.parcels.length,0);$('scRouteGroupsCount').textContent=grp.length;$('scLoadedCount').textContent=sent.length;
const q=norm($('scIncomingSearch').value),type=$('dhIncomingType').value;const grouped=new Map();for(const p of inc){if(type!=='all'&&p.kind!==type)continue;const m=data.manifests.find(x=>String(x.id)===String(p.transferId));if(q&&!norm([p.tracking,p.barangay,p.courierName,p.truckId,p.loadedFromName,p.transferId,m?.id].join(' ')).includes(q))continue;const id=p.kind==='sorting'?(p.transferId||'Unassigned'):'COLLECTION · '+scheduledDay(p)+' · '+p.barangay+' · '+(p.courierName||'Unassigned');if(!grouped.has(id))grouped.set(id,{manifest:m,parcels:[]});grouped.get(id).parcels.push(p)}
$('scIncomingRows').innerHTML=[...grouped].map(([id,g])=>{const m=g.manifest,b=g.parcels,source=m?.sourceType||b[0].kind;const total=source==='sorting'?(m?.parcelIds?.length||b.length):b.length;const verified=(m?.parcelIds||[]).map(id=>data.parcels.find(x=>String(x.id)===String(id))).filter(x=>x&&(x.status!=='incoming'||isReported(x))).length;const caption=source==='sorting'?'SORTING CENTER TRANSFER':'MIXED BARANGAY COLLECTION · SELLER PARCELS / BUYER RETURNS';const date=m?.receivedDate||b[0].scheduledDate||b[0].loadedAt;
return `<tr class="sc-batch-row"><td colspan="5"><div class="sc-batch-header"><div class="sc-batch-identity"><span class="sc-batch-kicker">${caption}</span><strong class="sc-batch-id">${esc(id)}</strong><div class="sc-batch-meta"><span>Origin: <b>${esc(m?.originName||b[0].loadedFromName)}</b></span><span>Barangay: <b>${esc(m?.barangay||b[0].barangay)}</b></span><span>Date: <b>${esc(fmt(date))}</b></span></div></div><div class="sc-batch-metrics"><span class="sc-batch-remaining">${b.length} Awaiting Scan</span><span class="sc-batch-count">${verified} / ${total} verified</span></div></div></td></tr>`+b.map(p=>`<tr class="sc-clickable-row" data-parcel="${esc(p.tracking)}" tabindex="0"><td><strong>${esc(p.tracking)}</strong></td><td>${p.kind==='return'||p.isReturn?'<span class="sc-kind return">Return Parcel</span>':p.kind==='pickup'?'<span class="sc-kind pickup">Seller Pickup</span>':'<span class="sc-kind plain">—</span>'}<small>${esc(p.barangay)} · To ${esc(destinationRole(p))}: ${esc(destinationAddress(p))}</small></td><td>${esc(p.loadedFromName)}</td><td>${displayAssignee(p.courierName,assignedPhone(m,p))}</td><td>${esc(assignedVehicle(m,p))}</td></tr>`).join('')}).join('')+(type==='sorting'?'':voided().filter(p=>type==='all'||type==='voided'||type===p.kind).map(p=>`<tr><td><strong>${esc(p.tracking)}</strong><small>Pickup voided · ${esc(scheduledDay(p))}</small></td><td>${esc(p.kind==='return'?'Buyer Return':'Seller Pickup')}<small>${esc(p.barangay)} · To ${esc(destinationRole(p))}: ${esc(destinationAddress(p))}</small></td><td>${esc(p.loadedFromName)}</td><td>${displayAssignee(p.courierName,assignedPhone(null,p))}</td><td>${esc(assignedVehicle(null,p))}<small><button class="sc-btn small" type="button" data-reschedule="${esc(p.tracking)}">Reschedule</button></small></td></tr>`).join(''))||'<tr><td class="sc-empty" colspan="5">No active or voided incoming parcels.</td></tr>';
const filtered=(arr,qid)=>arr.filter(p=>norm([p.tracking,p.barangay,p.kind,p.receivedFrom].join(' ')).includes(norm($(qid).value)));
$('scReceivedRows').innerHTML=filtered(rec,'scReceivedSearch').map(p=>`<tr data-parcel="${esc(p.tracking)}" class="sc-clickable-row" tabindex="0"><td><strong>${esc(p.tracking)}</strong></td><td>${p.kind==='return'||p.isReturn?'<span class="sc-kind return">Return Parcel</span>':p.kind==='pickup'?'<span class="sc-kind pickup">Seller Pickup</span>':'<span class="sc-kind plain">—</span>'}</td><td>${esc(p.barangay)}</td><td>${fmt(p.receivedAt)}</td><td><span class="sc-badge ${p.status==='staged'?'incoming':'received'}">${p.status==='staged'?'Staged':'Ready for scan'}</span></td></tr>`).join('')||'<tr><td colspan="5" class="sc-empty">No received parcels yet.</td></tr>';
$('scReadyRows').innerHTML=filtered(rdy,'scReadySearch').map(p=>`<tr class="sc-clickable-row" data-parcel="${esc(p.tracking)}" tabindex="0"><td><strong>${esc(p.tracking)}</strong></td><td>${p.kind==='return'||p.isReturn?'<span class="sc-kind return">Return Parcel</span>':p.kind==='pickup'?'<span class="sc-kind pickup">Seller Pickup</span>':'<span class="sc-kind plain">—</span>'}</td><td>${esc(p.barangay)}</td><td>${esc(p.kind==='sorting'?'Barangay delivery rider':'Sorting Center transfer')}</td></tr>`).join('')||'<tr><td colspan="4" class="sc-empty">No parcels ready for dispatch.</td></tr>';
$('scRouteGroups').innerHTML=grp.map(g=>{const pending=blockedMembers(g);const route=g.route;return `<article class="sc-group"><div class="sc-group-top"><div><h3 class="sc-group-title">${esc(route.type==='delivery'?'OUT FOR DELIVERY · '+route.destination:'TO SORTING CENTER · '+assignedSortingCenter())}</h3><p class="sc-group-sub">${route.type==='sorting'?`All received seller pickups and buyer returns for ${esc(assignedSortingCenter())}. Courier and truck are selected on Loaded.`:`Incoming batch: ${esc(g.batchId)} · Delivery Rider: <strong>${esc(route.rider.name)}</strong>`}</p>${route.type==='delivery'?`<p class="dh-group-secondary">Designated vehicle: ${esc(route.rider.vehicle)}</p>`:''}</div><div class="sc-group-actions"><span class="sc-group-count">${g.parcels.length} parcel(s)</span><button class="sc-btn primary small" type="button" data-load="${esc(g.key)}" ${pending.length?'disabled':''}>${pending.length?'Awaiting '+pending.length+' scan(s)':route.type==='delivery'?'Out for Delivery':'Loaded'}</button></div></div><div class="sc-group-list">${g.parcels.map(p=>`<div class="sc-group-item"><strong>${esc(p.tracking)}</strong><small>${esc(p.barangay)}</small></div>`).join('')}</div>${pending.length?`<div class="dh-group-caption">This original inbound manifest still has ${pending.length} unscanned parcel(s): ${esc(pending.map(p=>p.tracking).join(', '))}.</div>`:''}</article>`}).join('')||'<div class="sc-groups-empty">No staged groups. Departure-scan a received parcel to assign a delivery rider or Sorting Center transfer.</div>';
$('scActivity').innerHTML=(data.events||[]).slice(0,7).map(e=>`<div class="sc-activity-row"><div><strong>${esc(e.action)} · ${esc(e.tracking)}</strong><small>${esc(e.desc)}</small></div><time>${fmt(e.at)}</time></div>`).join('')||'<div class="sc-activity-empty">No scanner activity recorded yet.</div>';
}
function tabs(which){for(const [tab,view,name] of [['receive','scReceiveView','scReceiveTab'],['tables','scTablesView','scTablesTab'],['depart','scDepartView','scDepartTab']]){$(view).hidden=which!==tab;$(name).classList.toggle('active',which===tab);$(name).setAttribute('aria-selected',String(which===tab))}}
$('scReceiveTab').onclick=()=>tabs('receive');$('scTablesTab').onclick=()=>tabs('tables');$('scDepartTab').onclick=()=>tabs('depart');
$('scReceiveForm').onsubmit=e=>{e.preventDefault();checkReceive($('scReceiveCode').value.trim());$('scReceiveCode').value=''};
$('scDepartForm').onsubmit=e=>{e.preventDefault();checkDepart($('scDepartCode').value.trim());$('scDepartCode').value=''};
for(const id of ['scIncomingSearch','scReceivedSearch','scReadySearch','dhIncomingType'])$(id).addEventListener('input',render);
for(const id of ['scIncomingRows','scReceivedRows','scReadyRows'])$(id).addEventListener('click',e=>{const row=e.target.closest('[data-parcel]');if(row)showParcelDetails(row.dataset.parcel)});
let rescheduleTracking='';$('scIncomingRows').addEventListener('click',e=>{const btn=e.target.closest('[data-reschedule]');if(!btn)return;e.stopPropagation();rescheduleTracking=btn.dataset.reschedule;const p=findParcel(rescheduleTracking);if(!p||p.status!=='voided')return;$('dhRescheduleText').textContent='Preview: confirm a new pickup date with the buyer/seller for '+p.tracking+'.';$('dhRescheduleDate').value=phDay();$('dhRescheduleDate').min=phDay();$('dhRescheduleError').textContent='';$('dhRescheduleModal').hidden=false;});
$('dhRescheduleCancel').onclick=()=>{$('dhRescheduleModal').hidden=true;rescheduleTracking=''};
$('dhRescheduleConfirm').onclick=()=>{const p=findParcel(rescheduleTracking),date=$('dhRescheduleDate').value;if(!p||p.status!=='voided'||!date||date<phDay()){$('dhRescheduleError').textContent='Choose today or a future date.';return;}p.scheduledDate=date;p.status='incoming';p.rescheduleRequired=false;p.voidedAt=null;addHistory(p,'Pickup Rescheduled',p.barangay+', '+municipality,now(),'New scheduled date: '+date);event('Pickup rescheduled',p,date);localStorage.setItem(storeKey,JSON.stringify(data));$('dhRescheduleModal').hidden=true;rescheduleTracking='';render();say('Pickup rescheduled for '+date+'. Customer confirmation is simulated.');};
$('scRouteGroups').addEventListener('click',e=>{const button=e.target.closest('[data-load]');if(!button)return;const g=stagedGroups().find(g=>g.key===button.dataset.load);if(!g)return;const blocked=blockedMembers(g);if(blocked.length){say('Cannot dispatch; inbound batch contains unscanned parcels.');return}pendingGroup=g;$('scLoadDescription').textContent='Load '+g.parcels.length+' parcel(s) for '+(g.route.type==='sorting'?assignedSortingCenter():g.route.destination)+'?';$('dhDispatchAssignment').textContent=g.route.type==='delivery'?'Designated delivery rider: '+g.route.rider.name+' · '+g.route.rider.barangay:'Select the courier / driver from the truck that delivered parcels from '+assignedSortingCenter()+'. The returning truck and plate will populate automatically.';$('dhTruckPickerRow').hidden=g.route.type!=='sorting';if(g.route.type==='sorting'){refreshTruckPicker();}else{$('scTruckId').value=g.route.rider.vehicle;}$('dhDispatchError').textContent='';$('scLoadModal').hidden=false});
const closeLoad=()=>{$('scLoadModal').hidden=true;pendingGroup=null};$('scLoadCancel').onclick=closeLoad;$('scLoadModal').addEventListener('click',e=>{if(e.target===$('scLoadModal'))closeLoad()});
$('scLoadConfirm').onclick=()=>{if(!pendingGroup)return;const g=stagedGroups().find(x=>x.key===pendingGroup.key);if(!g)return closeLoad();if(blockedMembers(g).length){$('dhDispatchError').textContent='Receive-scan or report every missing Sorting Center transfer parcel before dispatch.';return}const returning=arrivingTrucks().find(t=>t.id===$('dhAvailableTruck').value);if(g.route.type==='sorting'&&(!returning||truckBusy(returning))){$('dhDispatchError').textContent='Choose an available truck that arrived from the designated provincial Sorting Center.';return;}const vehicle=$('scTruckId').value.trim();if(!vehicle){$('dhDispatchError').textContent='A vehicle / plate reference is required.';return}const sentAt=now(),manifestId='DH-OUT-'+Date.now();data.manifests.push({id:manifestId,sourceType:g.route.type==='delivery'?'out-for-delivery':'transfer-to-sorting',originName:hubName,destination:g.route.destination,barangay:g.parcels[0].barangay,courierName:g.route.type==='sorting'?returning.courier:g.route.rider.name,truckId:vehicle,returningTruckManifestId:g.route.type==='sorting'?returning.id:null,destinationCenterId:g.route.type==='sorting'?centerId:null,createdAt:sentAt,status:'loaded',parcelIds:g.parcels.map(p=>p.id)});for(const p of g.parcels){p.status=g.route.type==='delivery'?'out-for-delivery':'sent-to-sorting';p.dispatchedAt=sentAt;p.dispatchedManifestId=manifestId;p.assignedRider=g.route.type==='sorting'?returning.courier:g.route.rider.name;p.assignedVehicle=vehicle;p.departedTo=g.route.destination;p.nextStop=null;addHistory(p,g.route.type==='delivery'?'Out for Delivery':'In Transit — Departed Delivery Hub',g.route.type==='delivery'?g.route.destination:g.route.destination,sentAt,'Courier: '+p.assignedRider+' · Vehicle: '+vehicle);event(g.route.type==='delivery'?'Out for delivery':'Departed to Sorting Center',p,p.assignedRider+' · '+vehicle)}persist();closeLoad();say(g.parcels.length+' parcels dispatched.');feedback('scDepartFeedback','good','Dispatch confirmed','Assigned courier: '+(g.route.type==='sorting'?returning.courier:g.route.rider.name),g.route.destination)};
document.addEventListener('keydown',e=>{if(e.key==='Escape'){if(!$('scIssueModal').hidden)closeIssueModal();else if(!$('scLoadModal').hidden)closeLoad()}});
function syncArrivalFromSorting(){const sc=read(scStoreKey);if(!sc?.manifests)return;let dirty=false;for(const m of data.manifests){if(m.sourceType!=='transfer-to-sorting'||m.arrivedAt)continue;const sm=sc.manifests.find(x=>String(x.id)==='DH:'+m.id);if(sm?.arrivedAt){m.arrivedAt=sm.arrivedAt;m.status='arrived';dirty=true;}}if(dirty)localStorage.setItem(storeKey,JSON.stringify(data));}
function departHistory(){return data.manifests.filter(m=>['transfer-to-sorting','out-for-delivery'].includes(m.sourceType)&&m.createdAt&&!m.arrivedAt).sort((a,b)=>String(b.createdAt).localeCompare(String(a.createdAt)));}
function drawDepartHistory(){const rows=departHistory().map(m=>`<div class="sc-history-row"><div class="sc-history-head"><strong>${esc(m.id)}</strong><span class="sc-badge incoming">${m.sourceType==='out-for-delivery'?'Out for Delivery':'Awaiting Sorting Center Receipt'}</span></div><div class="sc-history-meta">${esc(m.originName)} → ${esc(m.destination)} · ${fmt(m.createdAt)}</div><div class="sc-history-meta">Courier: ${esc(m.courierName||'—')} · Truck: ${esc(m.truckId||'—')} · ${m.parcelIds.length} parcels</div><button class="sc-btn small" type="button" data-history-detail="${esc(m.id)}">View Batch</button></div>`).join('');$('scHistoryPreview').innerHTML=rows||'<p class="sc-help">No outstanding departures.</p>';$('scHistoryDetails').innerHTML=rows||'<p class="sc-help">No outstanding departures.</p>'; }
$('scHistoryOpen').onclick=()=>{syncArrivalFromSorting();drawDepartHistory();$('scHistoryModal').hidden=false};$('scHistoryClose').onclick=()=>$('scHistoryModal').hidden=true;for(const id of ['scHistoryPreview','scHistoryDetails'])$(id).addEventListener('click',e=>{const b=e.target.closest('[data-history-detail]');if(!b)return;const m=data.manifests.find(x=>String(x.id)===b.dataset.historyDetail);if(!m)return;$('scHistoryDetails').innerHTML=`<h3>${esc(m.id)}</h3><p class="sc-history-meta">${esc(m.originName)} → ${esc(m.destination)} · ${esc(m.courierName)} · ${esc(m.truckId)}</p><div class="sc-history-parcels">${m.parcelIds.map(id=>{const p=data.parcels.find(x=>String(x.id)===String(id));return `<div>${esc(p?.tracking||id)} ${p&&destinationRole(p)==='Seller'?'<span class="sc-kind return">Return Parcel</span>':''}</div>`}).join('')}</div>`;$('scHistoryModal').hidden=false;});
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


window.addEventListener('pageshow',()=>{expireMissedCollections();render()});window.addEventListener('focus',()=>{expireMissedCollections();render()});window.addEventListener('storage',e=>{if(e.key===storeKey){const fresh=read(storeKey);if(fresh&&Array.isArray(fresh.parcels)){data=fresh;render()}}if(e.key===scStoreKey)importSortingCenterTransfers()});
seed();seedCrossCenterAndReturnExamples();importSortingCenterTransfers();normalizeSortingCenterOrigins();normalizeDemoDestinations();expireMissedCollections();render();tabs('receive');
});</script></body></html>