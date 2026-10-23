{{-- Seller Returns & Refunds: same performance table, preview and details modal hooks; frontend demo only. --}}
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><title>ShopEase — Returns & Refunds</title><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css','resources/css/seller/dashboard.css','resources/css/seller/seller-performance.css'])
@php
    // Always resolve the recipient shop from the currently authenticated seller.
    $rrSellerAccount = auth()->user();
    $rrSellerProfile = $seller ?? $rrSellerAccount;
    $rrSellerRegistration = $registration ?? null;
    $rrSellerField = function (array $fields) use ($rrSellerProfile, $rrSellerAccount, $rrSellerRegistration) {
        foreach ([$rrSellerProfile, $rrSellerAccount, $rrSellerRegistration] as $source) {
            foreach ($fields as $key) {
                $value = data_get($source, $key);
                if (is_scalar($value) && trim((string) $value) !== '') return trim((string) $value);
            }
        }
        return '';
    };
    $rrSellerAddress = $rrSellerField(['return_address', 'business_address', 'store_address', 'shop_address', 'address', 'full_address']);
    if (!$rrSellerAddress) {
        $rrSellerAddressParts = array_filter([
            $rrSellerField(['house_no', 'house_number', 'street_address', 'street']),
            $rrSellerField(['barangay']),
            $rrSellerField(['municipality', 'city']),
            $rrSellerField(['province']),
            $rrSellerField(['postal_code', 'zip_code']),
        ]);
        $rrSellerAddress = implode(', ', $rrSellerAddressParts);
    }
    $rrCurrentSeller = [
        'name' => $rrSellerField(['store_name', 'shop_name', 'business_name', 'name']),
        'phone' => $rrSellerField(['store_phone', 'business_phone', 'contact_no', 'contact_number', 'phone', 'mobile_number']),
        'address' => $rrSellerAddress,
    ];
@endphp
<script id="rrAuthenticatedSeller" type="application/json">@json($rrCurrentSeller)</script>
<style>
.rr-page{min-height:100vh;background:#fbf8f6;padding:118px 30px 50px;margin-left:256px;font-family:Poppins,sans-serif;color:#241b19;transition:margin-left .25s}.rr-page *{box-sizing:border-box}.rr-container{max-width:1510px;margin:auto}.rr-heading{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px}.rr-heading h1{margin:0;font-size:25px;font-weight:650}.rr-heading p{margin:4px 0;color:#938985;font-size:12px}.rr-back{border:1px solid #e9ddda;border-radius:8px;padding:10px 15px;background:#fff;text-decoration:none;color:#72191f;font-size:12px}.rr-page .sp-complaints{width:100%}.rr-switch{display:flex;gap:8px;padding:6px;background:#f1e9e6;border-radius:12px;margin-bottom:18px;width:max-content;max-width:100%}.rr-switch button{border:0;border-radius:8px;padding:11px 23px;background:transparent;color:#847773;font-size:12px;cursor:pointer;font-family:inherit}.rr-switch button.active{background:white;color:#791e2a;box-shadow:0 2px 9px #00000012;font-weight:600}.rr-panel[hidden]{display:none!important}.rr-help{margin:0 0 14px;color:#918581;font-size:12px}.rr-page .complaints-filter-card{background:white}.rr-solution-box{padding:17px 19px;margin:15px 0;border:1px solid #ead5d7;border-radius:11px;background:#fff9f9}.rr-solution-box h4{color:#68242c;font-size:13px;margin:0 0 8px}.rr-solution-box p{margin:0;color:#3a3435;font-size:12px;line-height:1.8}.rr-solution-meta{display:flex;justify-content:space-between;gap:15px;margin-top:10px;font-size:12px}.rr-solution-meta strong{color:#79202a}.rr-page .complaints-detail-card{max-height:720px;overflow:auto}.rr-page .complaints-workspace.has-selection{align-items:start}.rr-ship-table{width:100%;border-collapse:collapse;background:white;font-size:12px}.rr-ship-table th{text-align:left;background:#fbf5f3;color:#756e6b;font-size:11px;padding:16px;border-bottom:1px solid #f0e7e3}.rr-ship-table td{padding:17px 16px;border-bottom:1px solid #f0e7e3}.rr-ship-row{cursor:pointer}.rr-ship-row:hover{background:#fff8f6}.rr-ship-wrap{overflow:auto;border:1px solid #ece4e0;border-radius:12px;background:#fff}.rr-chip{border-radius:25px;padding:5px 11px;background:#eaf2fb;color:#2f628e;font-size:11px;font-weight:600}.rr-chip.delivered{background:#e4f4e7;color:#27733b}.rr-shipping-modal{position:fixed;inset:0;z-index:300;background:#17121899;display:flex;align-items:center;justify-content:center;padding:18px;backdrop-filter:blur(3px)}.rr-shipping-modal[hidden]{display:none!important}.rr-shipping-dialog{background:white;max-width:780px;width:100%;border-radius:17px;padding:25px;max-height:92vh;overflow:auto;box-shadow:0 18px 70px #0003}.rr-modal-head{display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #eee3e1;padding-bottom:12px}.rr-modal-head h2{margin:0;font-size:18px}.rr-close{border:0;background:#f8eeee;color:#681f29;border-radius:50%;width:32px;height:32px;cursor:pointer;font-size:22px}.rr-ship-summary{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:17px 0}.rr-ship-summary div{padding:14px;background:#faf7f5;border:1px solid #eee8e5;border-radius:9px}.rr-ship-summary small{display:block;color:#938784;font-size:10px;margin-bottom:5px}.rr-ship-summary strong{font-size:12px}.rr-track{display:flex;gap:0;margin:27px 0 30px}.rr-track-step{flex:1;text-align:center;position:relative;font-size:10px;color:#aaa}.rr-track-step:before{content:'';height:3px;position:absolute;top:18px;left:0;right:0;background:#eee0dd;z-index:0}.rr-track-step:first-child:before{left:50%}.rr-track-step:last-child:before{right:50%}.rr-track-step.done:before{background:#8e3540}.rr-track-step img{position:relative;z-index:1;width:37px;height:37px;object-fit:contain;background:white;filter:grayscale(1);opacity:.5}.rr-track-step.done img{filter:none;opacity:1}.rr-track-step span{display:block;margin-top:8px}.rr-history{border-left:2px solid #ead5d3;margin-left:9px;padding-left:20px}.rr-history-item{padding:0 0 19px;position:relative;font-size:12px}.rr-history-item:before{content:'';position:absolute;left:-27px;top:3px;width:11px;height:11px;background:#852a35;border:3px solid #f7e6e7;border-radius:50%}.rr-history-item p{margin:5px 0;color:#9c8e89;font-size:11px}.rr-empty{padding:28px;color:#8d8683;text-align:center;font-size:12px}.rr-ship-head{padding:20px 22px;background:white;border:1px solid #eee1db;border-bottom:0;border-radius:13px 13px 0 0;display:flex;gap:15px;justify-content:space-between;align-items:center}.rr-ship-head input{border:1px solid #e1d6d2;border-radius:8px;padding:9px 12px;font:inherit;font-size:12px}.rr-ship-foot{display:flex;align-items:center;justify-content:space-between;padding:15px 19px;background:white;border:1px solid #eee1db;border-top:0;border-radius:0 0 13px 13px;color:#9a8d89;font-size:11px}.rr-ship-foot button{padding:6px 12px;background:white;border:1px solid #e8dbd8;border-radius:7px;cursor:pointer}.rr-tabs-note{color:#948984;font-size:11px;margin:8px 0 18px}@media(max-width:760px){.rr-page{margin-left:0!important;padding:100px 12px 35px}.rr-heading h1{font-size:20px}.rr-switch button{padding:9px 11px}.rr-ship-summary{grid-template-columns:1fr}.rr-track-step img{width:27px;height:27px}}

/* Returns navigation — matching the screenshot pill / underline tabs */
.rr-page{padding-top:115px}.rr-switch{display:flex;gap:0;width:max-content;max-width:100%;margin:0 0 22px;background:#fff;border:1px solid #f1e8e4;border-radius:19px;padding:0 16px;box-shadow:0 2px 14px rgba(60,18,12,.045);overflow-x:auto}.rr-switch button{position:relative;min-height:68px;padding:0 25px;border-radius:0;background:transparent!important;box-shadow:none!important;color:#968b87;font-size:13px;font-weight:500;white-space:nowrap}.rr-switch button.active{color:#211c1a;font-weight:600}.rr-switch button:after{content:'';position:absolute;bottom:0;left:15px;right:15px;height:2px;background:transparent}.rr-switch button.active:after{background:#9d2034}.rr-page .complaints-date-wrap{min-width:155px}.rr-page .complaints-date-input{padding-right:8px!important}.rr-page .complaints-date-input::-webkit-calendar-picker-indicator{opacity:1;cursor:pointer}.rr-page .complaints-table .complaint-date-cell{white-space:normal}.rr-page .complaints-detail-card .rr-preview-solution{padding:0 20px 17px}.rr-page .complaints-detail-card .rr-preview-solution p{white-space:normal;line-height:1.7}.rr-page .complaints-modal-dialog{max-height:92vh;overflow-y:auto;max-width:min(980px,calc(100vw - 28px))}.rr-page .complaints-table-card{min-width:0}.rr-page .complaints-workspace{align-items:start}
.rr-shipping-shell{background:#fff;border:1px solid #efe7e4;border-radius:14px;overflow:hidden}.rr-ship-head{border:0;border-bottom:1px solid #eee4df;border-radius:0;min-height:64px}.rr-ship-status-tabs{display:flex;align-items:stretch;gap:27px;height:55px}.rr-ship-status-tabs button{position:relative;border:0;background:none;color:#918884;font:500 13px Poppins;cursor:pointer;padding:0 8px}.rr-ship-status-tabs button.active{color:#8d202e;font-weight:600}.rr-ship-status-tabs button.active:after{content:'';position:absolute;bottom:0;height:3px;background:#8d202e;left:0;right:0}.rr-shipping-search{position:relative}.rr-shipping-search input{width:300px;max-width:100%;padding-right:36px}.rr-shipping-search svg{position:absolute;right:11px;top:50%;transform:translateY(-50%);width:17px;height:17px;color:#aaa;pointer-events:none}.rr-ship-wrap{border:0;border-radius:0}.rr-ship-table{min-width:980px}.rr-ship-table thead{display:none}.rr-ship-table td{padding:22px 17px;font-size:12px;vertical-align:middle}.rr-ship-table td:nth-child(1){width:32%}.rr-ship-table td:nth-child(2){width:17%}.rr-ship-table td:nth-child(3){width:34%}.rr-ship-table td:nth-child(4){width:17%}.rr-order-cell{display:flex;gap:17px;align-items:center}.rr-product-thumb{width:65px;height:65px;display:grid;place-items:center;background:transparent!important;border-radius:0;flex-shrink:0}.rr-product-thumb img{width:55px;height:55px;object-fit:contain}.rr-order-cell strong{font-size:15px;color:#201915}.rr-order-cell p{margin:5px 0 0}.rr-order-cell small,.rr-order-cell .rr-date{font-size:11px;color:#958983}.rr-delivery-label{display:block;margin:8px 0 2px;color:#97908c}.rr-delivery-date{color:#1b648c;font-size:12px}.rr-shipping-progress{display:flex;align-items:flex-start;justify-content:center;width:100%}.rr-progress-item{display:flex;flex-direction:column;align-items:center;flex-shrink:0;width:72px}.rr-progress-item img{width:36px;height:36px;object-fit:contain}.rr-progress-item small{margin-top:6px;font-size:9px;color:#8e8682;white-space:nowrap}.rr-progress-line{flex:1;height:1px;background:#d2cdcb;margin-top:18px}.rr-progress-line.done{background:#7db473}.rr-tracking{display:block;text-align:center;margin-top:8px;color:#958c88;font-size:10px}.rr-courier{font-size:14px;color:#1a618e}.rr-shipping-price{font-weight:600;color:#241a16;font-size:16px}.rr-ship-table td:last-child p{margin:8px 0 0}.rr-ship-table td:last-child b{color:#e58b38}.rr-ship-table td:last-child small{color:#aaa}.rr-ship-foot{border:0;border-top:1px solid #eee4df}.rr-paging{display:flex;align-items:center;gap:12px}
.rr-shipping-modal{background:rgba(0,0,0,.25);backdrop-filter:blur(2px)}.rr-shipping-dialog{width:916px;max-width:calc(100vw - 20px);max-height:calc(100vh - 20px);padding:0;border-radius:27px;overflow-y:auto}.rr-modal-head{height:72px;padding:0 35px;position:relative}.rr-modal-head h2{font-size:19px;font-weight:500}.rr-courier-brand{margin-left:auto;margin-right:38px;font-size:20px;font-weight:700;color:#831e28}.rr-close{position:absolute;right:18px;top:18px}.rr-shipping-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px;padding:25px 35px;border-bottom:1px solid #e9e1dc}.rr-shipping-summary div{min-width:0}.rr-shipping-summary small{display:block;color:#928984;font-size:11px}.rr-shipping-summary strong{display:block;margin-top:7px;font-size:13px;overflow-wrap:anywhere}.rr-detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:30px;padding:17px 35px}.rr-modal-section{padding:16px 35px}.rr-detail-grid .rr-modal-section{padding:0}.rr-modal-section h3{font-size:15px;font-weight:600;margin:0 0 13px}.rr-modal-section p{font-size:12px;margin:8px 0}.rr-modal-section small{color:#948782;font-size:11px}.rr-modal-items{padding-top:6px}.rr-return-item{display:grid;grid-template-columns:2fr 1fr 70px;gap:12px;background:#fff2f1;border-radius:9px;padding:18px;font-size:12px}.rr-track{margin:16px 0 24px}.rr-track .rr-progress-item{width:110px}.rr-track .rr-progress-item img{height:55px;width:55px}.rr-track .rr-progress-line{margin-top:26px}.rr-track .rr-progress-item small{font-size:11px}.rr-history{margin:12px 0 20px 10px}.rr-history-item p{line-height:1.7}
@media(max-width:850px){.rr-ship-head{flex-wrap:wrap}.rr-shipping-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.rr-track .rr-progress-item{width:78px}}@media(max-width:550px){.rr-switch{width:100%}.rr-switch button{padding:0 12px;font-size:11px}.rr-shipping-summary,.rr-detail-grid{grid-template-columns:1fr}.rr-shipping-dialog{border-radius:17px}.rr-shipping-summary,.rr-modal-section,.rr-detail-grid{padding-left:17px;padding-right:17px}.rr-modal-head{padding:0 16px}.rr-courier-brand{display:none}}

/* Compact tracking visuals — matched to seller shipping status page (36px icons). */
.rr-page .rr-shipping-progress{display:flex;align-items:flex-start;justify-content:center;gap:0;max-width:360px;margin:0 auto}
.rr-page .rr-progress-item{width:73px;min-width:0;flex:0 0 73px;display:flex;flex-direction:column;align-items:center;justify-content:flex-start}
.rr-page .rr-progress-item img{width:32px!important;height:32px!important;max-width:32px;max-height:32px;object-fit:contain}
.rr-page .rr-progress-item small{font-size:9px!important;line-height:1.25;text-align:center;margin-top:6px;white-space:normal}
.rr-page .rr-progress-line{flex:1;min-width:12px;max-width:50px;margin-top:16px;height:1px}
.rr-shipping-dialog .rr-shipping-progress{max-width:440px;margin:0 auto}
.rr-shipping-dialog .rr-progress-item{width:96px!important;flex:0 0 96px}
.rr-shipping-dialog .rr-progress-item img{width:36px!important;height:36px!important;max-width:36px;max-height:36px}
.rr-shipping-dialog .rr-progress-line{margin-top:18px!important;max-width:70px}
.rr-chip.processing{background:#fff0d9;color:#99621c}.rr-chip.out-for-delivery{background:#fff0e5;color:#a45924}
.rr-schedule{display:block;margin-top:7px!important;color:#a2621d!important;font-size:10px!important;line-height:1.5}
.rr-ship-status-tabs{gap:12px;flex-wrap:wrap;height:auto;min-height:55px}.rr-ship-status-tabs button{min-height:55px;white-space:nowrap;font-size:12px}
.rr-refunds-shell{background:#fff;border:1px solid #efe7e4;border-radius:14px;overflow:hidden;box-shadow:0 3px 12px rgba(40,10,10,.025)}
.rr-refunds-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:0 18px;border-bottom:1px solid #ede4e1;min-height:64px}
.rr-refund-tabs{display:flex;gap:18px;align-self:stretch}.rr-refund-tabs button{position:relative;border:0;background:transparent;padding:0 8px;color:#958b87;font:500 12px Poppins,sans-serif;white-space:nowrap;cursor:pointer}
.rr-refund-tabs button.active{color:#7f1a2b;font-weight:600}.rr-refund-tabs button.active:after{content:'';position:absolute;height:3px;background:#8d202e;bottom:0;left:0;right:0}
#rrRefundSearch{height:36px;padding:0 12px;min-width:230px;max-width:100%;border:1px solid #ddd2ce;border-radius:8px;font:400 11px Poppins,sans-serif;outline:none}
.rr-refunds-table-scroll{overflow-x:auto}.rr-refunds-table{width:100%;min-width:900px;border-collapse:collapse;text-align:left}
.rr-refunds-table th{background:#fff9f7;color:#7b6f69;font-size:11px;font-weight:600;padding:15px 17px;border-bottom:1px solid #eae1dd}
.rr-refunds-table td{padding:18px 17px;border-bottom:1px solid #f0e9e5;font-size:12px;vertical-align:middle;color:#392d2a}
.rr-refunds-table tbody tr{cursor:pointer;transition:background .2s}.rr-refunds-table tbody tr:hover{background:#fff7f5}
.rr-refunds-table td strong{font-weight:600}.rr-refunds-table td small{display:block;margin-top:4px;color:#9a8f8b;font-size:10px}
.rr-refund-money{color:#8b202f!important;font-size:14px!important;font-weight:650}.rr-refund-status{display:inline-block;border-radius:999px;padding:6px 12px;font-size:10px;font-weight:600;background:#fff0d5;color:#a16c1c}
.rr-refund-status.refunded{background:#e0f3e5;color:#257141}.rr-refunds-footer{display:flex;justify-content:space-between;align-items:center;padding:15px 19px;color:#8d827d;font-size:11px}
.rr-refund-dialog{width:740px}.rr-refund-detail-body{padding:20px 30px 24px}.rr-refund-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}
.rr-refund-field{border:1px solid #eee4df;background:#fffdfc;border-radius:10px;padding:12px 15px;min-width:0}.rr-refund-field small{display:block;font-size:10px;color:#9a8b84;margin-bottom:6px}.rr-refund-field strong{font-size:12px;overflow-wrap:anywhere}
.rr-refund-notes{margin-top:15px;background:#fff8f5;border:1px solid #f0ded8;border-radius:10px;padding:15px}.rr-refund-notes strong{font-size:12px}.rr-refund-notes p{font-size:11px;line-height:1.7;color:#786c68;margin:8px 0 0}
.rr-refund-modal-footer{border-top:1px solid #eee4df;padding:15px 30px;display:flex;justify-content:flex-end}.rr-refund-modal-footer button{border:0;border-radius:8px;background:#7b1b2b;color:#fff;padding:10px 25px;cursor:pointer;font:500 11px Poppins,sans-serif}
@media(max-width:900px){.rr-refunds-toolbar{flex-wrap:wrap;padding:10px 15px}.rr-refund-tabs{min-height:48px}.rr-ship-status-tabs{gap:4px}}
@media(max-width:550px){.rr-refund-grid{grid-template-columns:1fr}.rr-refund-detail-body{padding:15px}.rr-refund-tabs{gap:1px}.rr-refund-tabs button{font-size:10px;padding:0 6px}#rrRefundSearch{width:100%}.rr-page .rr-progress-item{width:61px;flex-basis:61px}.rr-page .rr-progress-item img{width:28px!important;height:28px!important}}

/* Request decision pills — pending orange, declined red, accepted green. */
.rr-page .complaint-status.rr-pending,
.rr-page .complaints-modal-status.rr-pending,
.rr-page .complaint-status.open,
.rr-page .complaints-modal-status.open {background:#FFF0DA!important;color:#B86611!important;border-color:#F5D6A9!important;}
.rr-page .complaint-status.rr-declined,
.rr-page .complaints-modal-status.rr-declined {background:#FCE4E5!important;color:#B52639!important;border-color:#F2BDC5!important;}
/* Use the original transparent PNG without a colored square on the left of each shipping row. */
.rr-page .rr-product-thumb {background:transparent!important;border:0!important;box-shadow:none!important;}
.rr-page .rr-product-thumb img {background:transparent!important;border:0!important;box-shadow:none!important;}

/* Matching Requests and Refunds table typography, ID, and amount column. */
.rr-page .complaints-table th,
.rr-page .rr-refunds-table th{font-weight:600}
.rr-page .complaints-table td,
.rr-page .rr-refunds-table td{font-weight:500}
.rr-page .complaints-table .complaint-id,
.rr-page .rr-refunds-table .rr-order-id{color:#8b202f!important;font-weight:600!important;font-size:12px;letter-spacing:0}
.rr-page .complaints-table .rr-refund-money,
.rr-page .rr-refunds-table .rr-refund-money{color:#8b202f!important;font-weight:600!important;font-size:14px!important;white-space:nowrap}
.rr-page .complaints-table td small,
.rr-page .rr-refunds-table td small{font-weight:400}
.rr-page .complaints-table{min-width:850px}


/* Shipping details modal: same visible close affordance as complaint details modal. */
#rrShippingModal .rr-modal-head{position:relative;padding-right:88px}
#rrShippingModal .rr-ship-close-x{position:absolute!important;right:25px!important;top:50%!important;transform:translateY(-50%)!important;display:flex!important;align-items:center;justify-content:center;width:35px!important;height:35px!important;min-width:35px;border:0!important;border-radius:50%!important;background:#f7f1f0!important;color:#72212d!important;font:400 24px/1 Poppins,sans-serif!important;cursor:pointer;z-index:3;transition:background .2s}
#rrShippingModal .rr-ship-close-x:hover{background:#f1dfe0!important}
#rrShippingModal .rr-ship-close-x:focus-visible{outline:2px solid #8c2533;outline-offset:2px}

/* Restore compact request rows and give all six columns explicit widths. */
#rrRequestsPanel .complaints-table{width:100%;min-width:860px;table-layout:fixed;border-collapse:collapse}
#rrRequestsPanel .complaints-table th:nth-child(1){width:18%}
#rrRequestsPanel .complaints-table th:nth-child(2){width:20%}
#rrRequestsPanel .complaints-table th:nth-child(3){width:19%}
#rrRequestsPanel .complaints-table th:nth-child(4){width:14%}
#rrRequestsPanel .complaints-table th:nth-child(5){width:12%}
#rrRequestsPanel .complaints-table th:nth-child(6){width:17%}
#rrRequestsPanel .complaint-row td{padding:10px 12px!important;line-height:1.25!important;height:auto!important;vertical-align:middle}
#rrRequestsPanel .complaints-table th{padding:12px!important;white-space:nowrap}
#rrRequestsPanel .complaint-parties{line-height:1.3;gap:2px}
#rrRequestsPanel .complaint-parties small{font-size:10px}
#rrRequestsPanel .rr-filed-cell{white-space:nowrap}
#rrRequestsPanel .rr-filed-date,#rrRequestsPanel .rr-filed-time{display:block;font-size:11px;line-height:1.5}
#rrRequestsPanel .rr-filed-time{color:#948a85;font-size:10px}
#rrRequestsPanel .complaint-type{white-space:normal;overflow-wrap:break-word}

/* Always-visible shipping modal close button, independent of modal header CSS. */
#rrShippingModal .rr-shipping-dialog{position:relative!important;overflow-x:hidden}
#rrShippingModal .rr-shipping-fixed-close{position:sticky!important;top:15px!important;float:right!important;margin:15px 17px -54px 0!important;z-index:999!important;display:flex!important;align-items:center!important;justify-content:center!important;width:39px!important;height:39px!important;min-width:39px!important;border:1px solid #ead9d9!important;border-radius:50%!important;background:#fff!important;color:#782432!important;box-shadow:0 3px 12px rgba(62,18,27,.13)!important;cursor:pointer!important;opacity:1!important;visibility:visible!important;transition:background .15s,transform .15s!important}
#rrShippingModal .rr-shipping-fixed-close:hover{background:#fcebee!important;transform:scale(1.05)}
#rrShippingModal .rr-shipping-fixed-close:focus-visible{outline:3px solid #bd6470;outline-offset:2px}
#rrShippingModal .rr-modal-head{padding-right:80px!important}

/* Shipping modal footer: match the Requests details modal Close button exactly. */
#rrShippingModal .rr-shipping-bottom-actions{display:flex;justify-content:flex-end;align-items:center;padding:17px 35px 23px;border-top:1px solid #eee3e0;background:#fff}
#rrShippingModal .rr-shipping-bottom-close{height:34px;min-width:0;padding:0 18px;border:1px solid #A6A6A6!important;border-radius:8px;background:transparent!important;background-image:none!important;color:#777!important;font-family:Poppins,sans-serif;font-size:12px;font-weight:500;line-height:normal;cursor:pointer;filter:none!important;box-shadow:none!important;transition:background .2s,border-color .2s,color .2s}
#rrShippingModal .rr-shipping-bottom-close:hover{background:#F7F7F7!important;border-color:#888!important;color:#555!important;filter:none!important;box-shadow:none!important}
#rrShippingModal .rr-shipping-bottom-close:focus-visible{outline:2px solid #A6A6A6;outline-offset:2px}
@media(max-width:550px){#rrShippingModal .rr-shipping-bottom-actions{padding:14px 17px 20px}}

/* Align the Requests buyer/product cell with the Refunds table. */
#rrRequestsPanel .rr-buyer-product{display:flex;flex-direction:column;gap:4px;min-width:0;line-height:1.45}
#rrRequestsPanel .rr-buyer-product strong{display:block;font-size:12px;font-weight:600;color:#392d2a;overflow-wrap:anywhere}
#rrRequestsPanel .rr-buyer-product small{display:block;margin:0;font-size:10px;font-weight:400;color:#9a8f8b;overflow-wrap:anywhere}
/* Refund modal Close button exactly matches Requests modal's neutral action. */
#rrRefundModal .rr-refund-modal-footer{padding:17px 30px 23px}
#rrRefundModal .rr-refund-modal-footer button.sp-modal-done{height:34px;min-width:0;padding:0 18px;border:1px solid #A6A6A6!important;border-radius:8px;background:transparent!important;background-image:none!important;color:#777!important;font-family:Poppins,sans-serif;font-size:12px;font-weight:500;line-height:normal;box-shadow:none!important;filter:none!important;cursor:pointer;transition:background .2s,border-color .2s,color .2s}
#rrRefundModal .rr-refund-modal-footer button.sp-modal-done:hover{background:#F7F7F7!important;border-color:#888!important;color:#555!important}

/* Buyer information in Requests preview and details modal */
#rrRequestsPanel .rr-buyer-info-list{display:grid;gap:9px;min-width:0}
#rrRequestsPanel .rr-buyer-info-list>div{display:grid;grid-template-columns:110px minmax(0,1fr);gap:9px;align-items:start;font-size:12px;line-height:1.6}
#rrRequestsPanel .rr-buyer-info-list span{color:#9a8e8c;font-weight:400}
#rrRequestsPanel .rr-buyer-info-list strong{font-weight:500;color:#3d302e;overflow-wrap:anywhere}
#rrRequestsPanel .rr-ordered-items{display:grid;gap:11px}
#rrRequestsPanel .rr-ordered-product{border:1px solid #eee0df;border-radius:10px;overflow:hidden;background:white}
#rrRequestsPanel .rr-ordered-product-head{display:flex;flex-direction:column;gap:5px;padding:11px 13px;background:#fff7f6}
#rrRequestsPanel .rr-ordered-product-head span{font-size:10px;color:#a06770}
#rrRequestsPanel .rr-ordered-product-head strong{font-size:12px;color:#392d2a;font-weight:600}
#rrRequestsPanel .rr-ordered-product-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;padding:12px 13px}
#rrRequestsPanel .rr-ordered-product-meta>div{display:flex;flex-direction:column;gap:3px;min-width:0}
#rrRequestsPanel .rr-ordered-product-meta span{font-size:10px;color:#998d89}
#rrRequestsPanel .rr-ordered-product-meta strong{font-size:12px;font-weight:500;color:#362b2a;overflow-wrap:anywhere}
@media(max-width:560px){#rrRequestsPanel .rr-buyer-info-list>div{grid-template-columns:1fr;gap:0}}

/* MODAL CONTENT LAYOUT FIX — modal lives outside #rrRequestsPanel. */
#complaints-details-modal .complaints-modal-parties-card,
#complaints-details-modal .complaints-modal-item-card { min-width:0; }
#complaints-details-modal .rr-buyer-info-list { display:grid;grid-template-columns:minmax(0,1fr);gap:0;width:100%;margin-top:5px; }
#complaints-details-modal .rr-buyer-info-list > div { display:grid;grid-template-columns:112px minmax(0,1fr);column-gap:16px;align-items:start;padding:10px 0;border-bottom:1px solid #f2eae7;font-size:12px;line-height:1.5;min-width:0; }
#complaints-details-modal .rr-buyer-info-list > div:last-child { border-bottom:0;padding-bottom:0; }
#complaints-details-modal .rr-buyer-info-list > div:first-child { padding-top:2px; }
#complaints-details-modal .rr-buyer-info-list span { display:block;color:#938985;font-size:11px;font-weight:400;letter-spacing:0; }
#complaints-details-modal .rr-buyer-info-list strong { display:block;color:#302624;font-size:12px;font-weight:500;line-height:1.5;overflow-wrap:anywhere;min-width:0; }
#complaints-details-modal .rr-ordered-items { display:grid;grid-template-columns:minmax(0,1fr);gap:12px;margin-top:8px;width:100%; }
#complaints-details-modal .rr-ordered-product { display:block;min-width:0;border:1px solid #eadedb;border-radius:10px;overflow:hidden;background:#fff;box-shadow:0 1px 2px rgba(50,20,15,.025); }
#complaints-details-modal .rr-ordered-product-head { display:flex;flex-direction:column;align-items:flex-start;gap:5px;padding:12px 14px;background:#fff6f4;border-bottom:1px solid #f0e3e0; }
#complaints-details-modal .rr-ordered-product-head span { display:inline-block;color:#9e5863;font-size:10px;font-weight:600;letter-spacing:.02em; }
#complaints-details-modal .rr-ordered-product-head strong { display:block;color:#302624;font-size:12px;font-weight:600;line-height:1.5;overflow-wrap:anywhere; }
#complaints-details-modal .rr-ordered-product-meta { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 16px;padding:14px;min-width:0; }
#complaints-details-modal .rr-ordered-product-meta > div { display:flex;flex-direction:column;align-items:flex-start;gap:5px;min-width:0; }
#complaints-details-modal .rr-ordered-product-meta span { display:block;color:#958b87;font-size:10px;font-weight:400;line-height:1.4; }
#complaints-details-modal .rr-ordered-product-meta strong { display:block;color:#332824;font-size:12px;font-weight:500;line-height:1.45;overflow-wrap:anywhere; }
@media(max-width:650px) {
 #complaints-details-modal .rr-buyer-info-list > div { grid-template-columns:95px minmax(0,1fr);column-gap:10px; }
 #complaints-details-modal .rr-ordered-product-meta {gap:13px 10px;padding:12px;}
}
@media(max-width:420px) {
 #complaints-details-modal .rr-buyer-info-list > div {grid-template-columns:minmax(0,1fr);gap:4px;}
 #complaints-details-modal .rr-ordered-product-meta {grid-template-columns:minmax(0,1fr);}
}


/* Request details dialog is outside .rr-page: explicit pill rules prevent inherited oversized/mismatched styles. */
#complaints-details-modal .complaints-modal-status{display:inline-flex!important;align-items:center;justify-content:center;width:max-content;min-width:80px;max-width:100%;min-height:27px;padding:5px 13px!important;border:1px solid transparent!important;border-radius:999px!important;font:600 11px/1.4 Poppins,sans-serif!important;white-space:nowrap;box-shadow:none!important;text-transform:none!important;letter-spacing:0!important}
#complaints-details-modal .complaints-modal-status.rr-pending{background:#FFF0DA!important;color:#B86611!important;border-color:#F5D6A9!important}
#complaints-details-modal .complaints-modal-status.resolved{background:#E4F4E7!important;color:#27733B!important;border-color:#BBDDC5!important}
#complaints-details-modal .complaints-modal-status.rr-declined{background:#FCE4E5!important;color:#B52639!important;border-color:#F2BDC5!important}
/* Refund timestamps follow the same stacked date/time pattern as the Requests table. */
#rrRefundsPanel .rr-refund-date-cell{white-space:nowrap}
#rrRefundsPanel .rr-refund-filed-date,#rrRefundsPanel .rr-refund-filed-time{display:block;font-size:11px;line-height:1.5;font-weight:500}
#rrRefundsPanel .rr-refund-filed-time{color:#948a85;font-size:10px;font-weight:400}
#rrRefundsPanel .rr-refund-status{white-space:nowrap;min-width:85px;text-align:center}
#rrRefundsPanel .rr-refund-status.returned-refunded{background:#e0f3e5;color:#257141}

/* Refund filters mirror the Requests toolbar without adding a second search input. */
#rrRefundsPanel .rr-refund-filter-card{margin-bottom:22px;padding:21px 20px;border-radius:18px}
#rrRefundsPanel .rr-refund-filter-card .complaints-filter-row{display:flex;align-items:center;gap:13px;flex-wrap:wrap}
#rrRefundsPanel .rr-refund-filter-card .complaints-search-wrap{width:412px;max-width:100%;position:relative}
#rrRefundsPanel .rr-refund-filter-card .complaints-date-wrap{width:243px;max-width:100%}
#rrRefundsPanel .rr-refund-filter-card .complaints-search,#rrRefundsPanel .rr-refund-filter-card .complaints-date-input{width:100%;height:46px}
#rrRefundsPanel .rr-refund-filter-card .complaints-reload{display:grid;place-items:center;min-width:42px;height:42px}
#rrRefundsPanel .rr-refund-filter-card .complaints-reload svg{width:22px;height:22px}
@media(max-width:650px){#rrRefundsPanel .rr-refund-filter-card .complaints-search-wrap,#rrRefundsPanel .rr-refund-filter-card .complaints-date-wrap{width:100%}}

/* Scoped filter control geometry: Refunds lives outside .sp-complaints, so
   seller-performance's icon sizing rules do not reach it. */
#rrRefundsPanel .rr-refund-filter-card,
#rrRequestsPanel > .complaints-filter-card {padding:21px 20px!important; margin-bottom:22px; min-height:90px; border:1px solid #f0e7e3; border-radius:18px; background:#fff; box-shadow:0 2px 9px rgba(65,28,19,.035)}
#rrRefundsPanel .rr-refund-filter-card .complaints-filter-row,
#rrRequestsPanel > .complaints-filter-card .complaints-filter-row{display:flex!important; flex-flow:row wrap!important; align-items:center!important; justify-content:flex-start!important; gap:13px!important; padding:0!important}
#rrRefundsPanel .rr-refund-filter-card .complaints-search-wrap,
#rrRequestsPanel > .complaints-filter-card .complaints-search-wrap{position:relative!important; display:block!important; flex:0 1 412px!important; width:412px!important; max-width:100%!important; height:46px!important}
#rrRefundsPanel .rr-refund-filter-card input.complaints-search,
#rrRequestsPanel > .complaints-filter-card input.complaints-search{display:block!important; width:100%!important; height:46px!important; padding:0 47px 0 18px!important; margin:0!important; border:1px solid #dcd5d2!important; border-radius:9px!important; background:#fff!important; font:400 13px Poppins,sans-serif!important; outline:none!important}
#rrRefundsPanel .rr-refund-filter-card svg.complaints-search-icon,
#rrRequestsPanel > .complaints-filter-card svg.complaints-search-icon{position:absolute!important; right:16px!important; top:50%!important; left:auto!important; width:19px!important; height:19px!important; min-width:19px!important; min-height:19px!important; max-width:19px!important; max-height:19px!important; transform:translateY(-50%)!important; pointer-events:none!important; color:#88827e!important; stroke:currentColor!important}
#rrRefundsPanel .rr-refund-filter-card .complaints-date-wrap,
#rrRequestsPanel > .complaints-filter-card .complaints-date-wrap{position:relative!important; flex:0 0 243px!important; width:243px!important; height:46px!important; max-width:100%!important}
#rrRefundsPanel .rr-refund-filter-card input.complaints-date-input,
#rrRequestsPanel > .complaints-filter-card input.complaints-date-input{display:block!important; width:100%!important; height:46px!important; padding:0 12px!important; margin:0!important; border:1px solid #dcd5d2!important; border-radius:9px!important; background:#fff!important; font:400 13px Poppins,sans-serif!important; color:#473e3b!important}
#rrRefundsPanel .rr-refund-filter-card button.complaints-reload,
#rrRequestsPanel > .complaints-filter-card button.complaints-reload{position:relative!important; flex:0 0 42px!important; display:grid!important; place-items:center!important; width:42px!important; height:42px!important; min-width:42px!important; min-height:42px!important; margin:0!important; padding:0!important; border:0!important; background:transparent!important; cursor:pointer!important}
#rrRefundsPanel .rr-refund-filter-card button.complaints-reload svg,
#rrRequestsPanel > .complaints-filter-card button.complaints-reload svg{display:block!important; position:static!important; width:21px!important; height:21px!important; min-width:21px!important; min-height:21px!important; max-width:21px!important; max-height:21px!important; transform:none!important; stroke:#312c2a!important; fill:none!important}
@media(max-width:680px){#rrRefundsPanel .rr-refund-filter-card .complaints-search-wrap,#rrRequestsPanel > .complaints-filter-card .complaints-search-wrap{flex:1 1 100%!important;width:100%!important}#rrRefundsPanel .rr-refund-filter-card .complaints-date-wrap,#rrRequestsPanel > .complaints-filter-card .complaints-date-wrap{flex:1 1 190px!important;width:auto!important}}

/* Refund detail modal shares the complete Requests details modal design. */
#rrRefundModal.sp-admin-modal-overlay[hidden]{display:none!important}
#rrRefundModal .rr-buyer-info-list{display:grid;grid-template-columns:minmax(0,1fr);gap:0;width:100%;margin-top:5px}
#rrRefundModal .rr-buyer-info-list>div{display:grid;grid-template-columns:112px minmax(0,1fr);gap:16px;padding:10px 0;border-bottom:1px solid #f2eae7;align-items:start;min-width:0}
#rrRefundModal .rr-buyer-info-list>div:last-child{border:0}
#rrRefundModal .rr-buyer-info-list span{font-size:11px;color:#938985}
#rrRefundModal .rr-buyer-info-list strong{font-size:12px;color:#302624;font-weight:500;overflow-wrap:anywhere}
#rrRefundModal .rr-ordered-items{display:grid;gap:12px;margin-top:8px}
#rrRefundModal .rr-ordered-product{min-width:0;border:1px solid #eadedb;border-radius:10px;overflow:hidden;background:#fff}
#rrRefundModal .rr-ordered-product-head{display:flex;flex-direction:column;gap:5px;padding:12px 14px;background:#fff6f4;border-bottom:1px solid #f0e3e0}
#rrRefundModal .rr-ordered-product-head span{font-size:10px;color:#9e5863}
#rrRefundModal .rr-ordered-product-head strong{font-size:12px;font-weight:600;overflow-wrap:anywhere}
#rrRefundModal .rr-ordered-product-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 16px;padding:14px}
#rrRefundModal .rr-ordered-product-meta>div{display:flex;flex-direction:column;gap:5px;min-width:0}
#rrRefundModal .rr-ordered-product-meta span{font-size:10px;color:#958b87}
#rrRefundModal .rr-ordered-product-meta strong{font-size:12px;font-weight:500;overflow-wrap:anywhere}
#rrRefundModal .complaints-modal-status{display:inline-flex!important;align-items:center;justify-content:center;width:max-content;max-width:100%;min-height:27px;padding:5px 13px!important;border-radius:999px!important;background:#E4F4E7!important;color:#27733B!important;border:1px solid #BBDDC5!important;font:600 11px/1.4 Poppins,sans-serif!important;white-space:nowrap}
#rrRefundModal .rr-refund-footer-only{display:flex;justify-content:flex-end;margin-top:16px}
#rrRefundModal .rr-refund-footer-only .sp-modal-bottom-actions{margin-left:auto}
@media(max-width:560px){#rrRefundModal .rr-buyer-info-list>div{grid-template-columns:1fr;gap:4px}#rrRefundModal .rr-ordered-product-meta{grid-template-columns:1fr}}

/* Refund modal: match the Requests ordered-items cards and retain clear product specifications. */
#rrRefundModal .complaints-modal-item-card{min-width:0;overflow:hidden}
#rrRefundModal #rrRefundItems{width:100%;min-width:0}
#rrRefundModal #rrRefundItems .rr-ordered-items{display:grid;grid-template-columns:minmax(0,1fr);gap:12px;width:100%;margin-top:10px}
#rrRefundModal #rrRefundItems .rr-ordered-product{display:block;min-width:0;border:1px solid #eadedb;border-radius:10px;overflow:hidden;background:#fff}
#rrRefundModal #rrRefundItems .rr-ordered-product-head{display:flex;flex-direction:column;align-items:flex-start;gap:5px;padding:12px 14px;background:#fff6f4;border-bottom:1px solid #f0e3e0}
#rrRefundModal #rrRefundItems .rr-ordered-product-head span{font-size:10px;font-weight:600;letter-spacing:.03em;color:#9e5863}
#rrRefundModal #rrRefundItems .rr-ordered-product-head strong{color:#302624;font-size:12px;font-weight:600;line-height:1.5}
#rrRefundModal #rrRefundItems .rr-ordered-product-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px 18px;padding:14px;min-width:0}
#rrRefundModal #rrRefundItems .rr-ordered-product-meta>div{display:flex;flex-direction:column;align-items:flex-start;gap:5px;min-width:0}
#rrRefundModal #rrRefundItems .rr-ordered-product-meta span{color:#958b87;font-size:10px;font-weight:400;line-height:1.4}
#rrRefundModal #rrRefundItems .rr-ordered-product-meta strong{color:#332824;font-size:12px;font-weight:500;line-height:1.45;overflow-wrap:anywhere}
@media(max-width:550px){#rrRefundModal #rrRefundItems .rr-ordered-product-meta{grid-template-columns:1fr}}

/* Refined Returns Shipping modal information and multi-item display */
#rrShippingModal .rr-shipping-summary strong{font-weight:500;font-size:12px;line-height:1.6}
#rrShippingModal .rr-modal-section h3{font-size:14px;font-weight:600;letter-spacing:0}
#rrShippingModal .rr-courier-brand{font-weight:600}
#rrShippingModal .rr-shipping-info-grid{gap:14px;padding-top:19px;padding-bottom:14px;align-items:stretch}
#rrShippingModal .rr-ship-info-card{padding:16px!important;border:1px solid #e9e1dd;border-radius:11px;background:#fff;min-width:0}
#rrShippingModal .rr-shipping-info-fields{display:grid;gap:11px}
#rrShippingModal .rr-ship-info-row{display:grid;grid-template-columns:minmax(90px,32%) minmax(0,1fr);gap:13px;align-items:start;font-size:12px;line-height:1.55}
#rrShippingModal .rr-ship-info-row>span{color:#8d8582;font-weight:400}
#rrShippingModal .rr-ship-info-row>strong{color:#312827;font-size:12px;font-weight:500;overflow-wrap:anywhere}
#rrShippingModal .rr-shipping-items-list{display:grid;gap:11px}
#rrShippingModal .rr-ship-item-card{border:1px solid #ebe1dd;border-radius:10px;overflow:hidden;background:#fff}
#rrShippingModal .rr-ship-item-card>header{display:flex;gap:12px;align-items:center;padding:13px 16px;background:#fff6f3;border-bottom:1px solid #f2e7e3}
#rrShippingModal .rr-ship-item-card>header small{color:#935462;font-size:10px;font-weight:600;white-space:nowrap}
#rrShippingModal .rr-ship-item-card>header strong{color:#352a29;font-size:12px;font-weight:600;overflow-wrap:anywhere}
#rrShippingModal .rr-ship-item-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px 22px;padding:14px 16px}
#rrShippingModal .rr-ship-item-fields .rr-ship-info-row{display:flex;flex-direction:column;gap:2px}
#rrShippingModal .rr-history-item strong{font-weight:600}
@media(max-width:630px){#rrShippingModal .rr-shipping-info-grid{grid-template-columns:1fr}#rrShippingModal .rr-ship-item-fields{grid-template-columns:1fr 1fr;gap:12px}#rrShippingModal .rr-ship-info-row{grid-template-columns:100px minmax(0,1fr)}}
/* Shipping status pill consistency */
.rr-page .rr-chip,.rr-shipping-modal .rr-chip{display:inline-flex;align-items:center;justify-content:center;width:max-content;max-width:100%;min-height:26px;border-radius:999px;padding:5px 12px;font:600 11px/1.3 Poppins,sans-serif;white-space:nowrap;background:#eaf2fb;color:#2f628e}
.rr-page .rr-chip.processing,.rr-shipping-modal .rr-chip.processing{background:#fff2c9;color:#946900}
.rr-page .rr-chip.in-transit,.rr-shipping-modal .rr-chip.in-transit{background:#deedff;color:#1a6096}
.rr-page .rr-chip.out-for-delivery,.rr-shipping-modal .rr-chip.out-for-delivery{background:#fff0e5;color:#a45924}
.rr-page .rr-chip.delivered,.rr-shipping-modal .rr-chip.delivered{background:#e4f4e7;color:#27733b}
.rr-shipping-modal .rr-ship-summary .rr-chip{margin-top:3px}
</style></head><body>
@section('page-title','Returns & Refunds')
<x-seller.sidebar /><x-seller.navbar />
<main class="rr-page" id="sellerReturnRefundPage"><div class="rr-container">
<div class="rr-switch" role="tablist" aria-label="Returns management"><button type="button" class="active" data-rr-main="requests" role="tab" aria-selected="true">Return / Refund Requests</button><button type="button" data-rr-main="shipping" role="tab" aria-selected="false">Returns Shipping Status</button><button type="button" data-rr-main="refunds" role="tab" aria-selected="false">Refunds</button></div>
<section id="rrRequestsPanel" class="rr-panel sp-complaints">
<div class="complaints-filter-card"><div class="complaints-filter-row">
<div class="complaints-search-wrap"><input id="complaints-search" class="complaints-search" type="search" placeholder="Search Order ID, buyer, product" aria-label="Search requests"><svg class="complaints-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg></div>
<div class="complaints-date-wrap"><input id="complaints-date-filter" class="complaints-date-input" type="date" aria-label="Filter by request date"></div>
<button id="complaints-reload" type="button" class="complaints-reload" title="Reset filters" aria-label="Reset filters"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11a8.1 8.1 0 0 0-15.5-2M4 5v4h4M4 13a8.1 8.1 0 0 0 15.5 2M20 19v-4h-4"/></svg></button>
</div></div>
              <div id="complaints-workspace" class="complaints-workspace">
                <section class="complaints-table-card"><div class="complaints-tabs" role="tablist" aria-label="Complaint status">
                    <button class="complaints-tab active" type="button" data-tab="all" role="tab" aria-selected="true">All Requests</button>
                    <button class="complaints-tab" type="button" data-tab="pending" role="tab" aria-selected="false">Pending</button>
                    <button class="complaints-tab" type="button" data-tab="accepted" role="tab" aria-selected="false">Accepted</button>
                    <button class="complaints-tab" type="button" data-tab="declined" role="tab" aria-selected="false">Declined</button>
                </div><div class="complaints-table-scroll"><table class="complaints-table"><thead><tr><th>Order ID</th><th>Buyer / Product</th><th>Type</th><th>Amount</th><th>Status</th><th>Date &amp; Time Filed</th></tr></thead><tbody id="complaints-table-body"></tbody></table><div id="complaints-empty" class="complaints-empty">No return / refund requests match the selected filters.</div></div>
                <div class="complaints-table-footer"><p id="complaints-count" class="complaints-count">Showing 0 out of 0 entries</p><div id="complaints-pagination" class="complaints-pagination"><button type="button" id="complaints-prev" class="complaints-page-button" aria-label="Previous page">‹</button><div id="complaints-page-numbers" class="complaints-page-numbers"></div><button type="button" id="complaints-next" class="complaints-page-button" aria-label="Next page">›</button><select id="complaints-items" class="complaints-items" aria-label="Items per page"><option value="7">Items per page: 7</option><option value="10">Items per page: 10</option><option value="20">Items per page: 20</option></select></div></div></section>
                <aside id="complaints-detail-card" class="complaints-detail-card" aria-hidden="true">
                    <div class="complaints-detail-header"><div><div class="complaints-detail-eyebrow">Order ID</div><div id="detail-id" class="complaints-detail-id">—</div></div><div class="complaints-detail-header-right"><span id="detail-status" class="complaint-status open">Pending</span><button id="complaints-detail-close" class="complaints-detail-close" aria-label="Close preview" type="button">×</button></div></div>
                    <div class="complaints-detail-body"><div class="complaints-detail-section"><h4 class="complaints-detail-label">Date &amp; Time Filed</h4><p id="detail-date" class="complaints-detail-text">—</p></div><div class="complaints-detail-section"><h4 class="complaints-detail-label">Complaint Type</h4><p id="detail-type" class="complaints-detail-text">—</p></div><div class="complaints-detail-section"><h4 class="complaints-detail-label">Description</h4><p id="detail-description" class="complaints-detail-text">—</p></div><div class="complaints-detail-section"><h4 class="complaints-detail-label">Buyer Information</h4><div id="detail-parties" class="complaints-parties-list"></div></div><div class="complaints-detail-section"><h4 class="complaints-detail-label">Supporting Evidence</h4><div id="detail-evidence" class="complaints-evidence"></div></div></div>
                    <div class="complaints-detail-section rr-preview-solution"><h4 class="complaints-detail-label">Requested Solution</h4><p id="rr-preview-solution" class="complaints-detail-text">—</p></div><div class="complaints-detail-actions"><button id="complaints-view-details" type="button" class="complaints-view-details">View Details</button></div>
                </aside>
              </div>
</section>
<section id="rrShippingPanel" class="rr-panel" hidden>
<div class="rr-shipping-shell"><div class="rr-ship-head"><div class="rr-ship-status-tabs" role="tablist" aria-label="Return shipping status"><button type="button" data-rr-ship-tab="all" class="active">All</button><button type="button" data-rr-ship-tab="processing">Processing</button><button type="button" data-rr-ship-tab="in-transit">In Transit</button><button type="button" data-rr-ship-tab="out-for-delivery">Out for Delivery</button><button type="button" data-rr-ship-tab="delivered">Delivered</button></div><label class="rr-shipping-search"><input type="search" id="rrShippingSearch" placeholder="Search Order ID, buyer, or tracking" aria-label="Search return shipping orders"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg></label></div>
<div class="rr-ship-wrap"><table class="rr-ship-table"><thead><tr><th>Order / Customer</th><th>Return Delivery</th><th>Tracking Progress</th><th>Courier / Payment</th></tr></thead><tbody id="rrShippingRows"></tbody></table></div>
<div class="rr-ship-foot"><span id="rrShippingCount">0 orders</span><span class="rr-paging"><button id="rrShipPrev" type="button" aria-label="Previous">‹</button> <span id="rrShipPage">1</span> <button id="rrShipNext" type="button" aria-label="Next">›</button></span></div></div></section>
<section id="rrRefundsPanel" class="rr-panel" hidden aria-label="Refund records">
<div class="rr-refund-filter-card complaints-filter-card"><div class="complaints-filter-row">
<div class="complaints-search-wrap"><input id="rrRefundSearch" class="complaints-search" type="search" placeholder="Search Order ID, buyer, product" aria-label="Search refunds"><svg class="complaints-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg></div>
<div class="complaints-date-wrap"><input id="rrRefundDate" class="complaints-date-input" type="date" aria-label="Filter by refund date"></div>
<button id="rrRefundReset" type="button" class="complaints-reload" title="Reset refund filters" aria-label="Reset refund filters"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11a8.1 8.1 0 0 0-15.5-2M4 5v4h4M4 13a8.1 8.1 0 0 0 15.5 2M20 19v-4h-4"/></svg></button>
</div></div>
<div class="rr-refunds-shell">
<header class="rr-refunds-toolbar"><div class="rr-refund-tabs" role="tablist" aria-label="Refund status"><button type="button" data-refund-filter="all" class="active">All Refunded</button><button type="button" data-refund-filter="return-refund">Returned &amp; Refunded</button><button type="button" data-refund-filter="refund-only">Refund Only</button></div></header>
<div class="rr-refunds-table-scroll"><table class="rr-refunds-table"><thead><tr><th>Order ID</th><th>Buyer / Product</th><th>Refund Type</th><th>Amount</th><th>Status</th><th>Date &amp; Time Refunded</th></tr></thead><tbody id="rrRefundRows"></tbody></table></div>
<footer class="rr-refunds-footer"><span id="rrRefundCount">0 records</span><div class="rr-paging"><button type="button" id="rrRefundPrev" aria-label="Previous page">‹</button><span id="rrRefundPage">1 / 1</span><button type="button" id="rrRefundNext" aria-label="Next page">›</button></div></footer>
</div></section>
</div></main>    <div id="complaints-details-modal" class="sp-admin-modal-overlay" aria-hidden="true" hidden>
      <div class="complaints-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="complaints-modal-title" tabindex="-1">
        <div class="sp-modal-title-row"><h3 id="complaints-modal-title" class="complaints-modal-title">Return / Refund Details</h3><button id="complaints-modal-close" type="button" aria-label="Close complaint details" class="sp-modal-x">×</button></div>
        <div class="complaints-modal-grid complaints-modal-top-grid"><section class="complaints-modal-card complaints-modal-summary"><div class="complaints-modal-summary-main"><div class="complaints-modal-id-block"><span class="complaints-modal-label">Order ID</span><strong id="modal-complaint-id" class="complaints-modal-id">—</strong></div><span id="modal-complaint-status" class="complaints-modal-status open">—</span></div><div class="complaints-modal-meta"><div class="complaints-modal-meta-item"><span>Date &amp; Time Filed</span><strong id="modal-date-filed">—</strong></div><div class="complaints-modal-meta-item"><span>Last Updated (Date &amp; Time)</span><strong id="modal-last-updated">—</strong></div></div></section><section class="complaints-modal-card complaints-modal-type-order"><div><span class="complaints-modal-label">Request Type</span><div class="complaints-modal-type-value"><span id="modal-complaint-type">—</span></div></div><div><span class="complaints-modal-label">Order ID</span><strong id="modal-order-id-top" class="complaints-modal-order-id">—</strong></div></section></div>
        <div class="complaints-modal-grid"><section class="complaints-modal-card complaints-modal-description-card"><h4 class="complaints-modal-card-title">Description</h4><p id="modal-description-copy" class="complaints-modal-description">—</p></section><section class="complaints-modal-card complaints-modal-parties-card"><h4 class="complaints-modal-card-title">Buyer Information</h4><div id="modal-parties-list" class="complaints-modal-party-list"></div></section></div>
        <div class="rr-solution-box"><h4>Buyer Requested Solution</h4><p id="rr-modal-solution">—</p><div class="rr-solution-meta"><span>Amount Requested</span><strong id="rr-modal-amount">—</strong></div></div><div class="complaints-modal-grid complaints-modal-middle-grid"><section class="complaints-modal-card complaints-modal-order-card"><h4 class="complaints-modal-card-title">Order Information</h4><div id="modal-order-information" class="complaints-modal-order-list"></div></section><section class="complaints-modal-card complaints-modal-item-card"><h4 class="complaints-modal-card-title">Ordered Items for Return / Refund</h4><div id="modal-product-info"></div></section></div>
        <div class="complaints-modal-grid complaints-modal-bottom-grid"><section class="complaints-modal-card complaints-modal-evidence-card"><h4 class="complaints-modal-card-title">Supporting Evidence</h4><div id="modal-evidence-list" class="complaints-modal-evidence-list"></div></section><section class="complaints-modal-card sp-contact-admin-card" aria-labelledby="sp-contact-admin-heading">
                    <div class="sp-contact-admin-copy">
                        <h4 class="complaints-modal-card-title" id="sp-contact-admin-heading">Need assistance with this return / refund?</h4>
                        <p>Contact ShopEase administration regarding this return / refund.</p>
                    </div>
                    <button id="sp-contact-admin" class="sp-contact-admin-button" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7A8.4 8.4 0 0 1 4 11.5 8.5 8.5 0 0 1 8.7 3.9 8.4 8.4 0 0 1 12.5 3h.5a8.5 8.5 0 0 1 8 8v.5Z"/></svg>
                        Message Admin
                    </button>
                </section></div>
        <div class="complaints-modal-footer-grid"><section class="complaints-modal-card complaints-modal-notes-card"><h4 class="complaints-modal-card-title">Status Updates</h4><div id="complaints-modal-notes-list" class="complaints-modal-notes-list"></div></section><div class="sp-modal-bottom-actions"><p>Decisions and enforcement are managed by ShopEase administration.</p><button id="complaints-modal-done" type="button" class="sp-modal-done">Close</button></div></div>
      </div>
    </div>


<div id="rrShippingModal" class="rr-shipping-modal" hidden><div class="rr-shipping-dialog" role="dialog" aria-modal="true" aria-labelledby="rrShipTitle">
<div class="rr-modal-head"><h2 id="rrShipTitle">Return Shipping Details</h2><div class="rr-courier-brand">⌁ <span>Ease Express</span></div></div>
<div class="rr-shipping-summary" id="rrShipSummary"></div>
<div class="rr-detail-grid rr-shipping-info-grid"><section class="rr-modal-section rr-ship-info-card"><h3>Customer Information</h3><div id="rrShipCustomer" class="rr-shipping-info-fields"></div></section><section class="rr-modal-section rr-ship-info-card"><h3>Seller Return Address</h3><div id="rrShipSeller" class="rr-shipping-info-fields"></div></section></div>
<div class="rr-modal-section rr-modal-items"><h3>Returned Items</h3><div id="rrShipItem" class="rr-shipping-items-list"></div></div>
<div class="rr-modal-section"><h3>Tracking Progress</h3><div class="rr-track" id="rrShipTrack"></div></div>
<div class="rr-modal-section"><h3>Tracking History</h3><div id="rrShipHistory" class="rr-history"></div></div>
<div class="rr-shipping-bottom-actions"><button type="button" id="rrShipClose" class="rr-shipping-bottom-close sp-modal-done">Close</button></div>
</div></div>
<div id="rrRefundModal" class="sp-admin-modal-overlay rr-refund-overlay" hidden aria-hidden="true">
  <div class="complaints-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="rrRefundTitle" tabindex="-1">
    <div class="sp-modal-title-row"><h3 id="rrRefundTitle" class="complaints-modal-title">Refund Details</h3><button type="button" class="sp-modal-x" id="rrRefundClose" aria-label="Close refund details">×</button></div>
    <div class="complaints-modal-grid complaints-modal-top-grid">
      <section class="complaints-modal-card complaints-modal-summary">
        <div class="complaints-modal-summary-main"><div class="complaints-modal-id-block"><span class="complaints-modal-label">Order ID</span><strong id="rrRefundOrderId" class="complaints-modal-id">—</strong></div><span id="rrRefundPill" class="complaints-modal-status resolved">Refunded</span></div>
        <div class="complaints-modal-meta"><div class="complaints-modal-meta-item"><span>Date &amp; Time Refunded</span><strong id="rrRefundDateValue">—</strong></div><div class="complaints-modal-meta-item"><span>Approved At</span><strong id="rrRefundApprovedValue">—</strong></div></div>
      </section>
      <section class="complaints-modal-card complaints-modal-type-order"><div><span class="complaints-modal-label">Refund Type</span><div class="complaints-modal-type-value"><span id="rrRefundTypeValue">—</span></div></div><div><span class="complaints-modal-label">Order ID</span><strong id="rrRefundOrderIdTop" class="complaints-modal-order-id">—</strong></div></section>
    </div>
    <div class="complaints-modal-grid"><section class="complaints-modal-card complaints-modal-description-card"><h4 class="complaints-modal-card-title">Refund Resolution</h4><p id="rrRefundDescription" class="complaints-modal-description">—</p></section><section class="complaints-modal-card complaints-modal-parties-card"><h4 class="complaints-modal-card-title">Buyer Information</h4><div id="rrRefundBuyer" class="complaints-modal-party-list"></div></section></div>
    <div class="rr-solution-box"><h4>Refund Resolution</h4><p id="rrRefundSolution">—</p><div class="rr-solution-meta"><span>Amount Refunded</span><strong id="rrRefundAmount">—</strong></div></div>
    <div class="complaints-modal-grid complaints-modal-middle-grid"><section class="complaints-modal-card complaints-modal-order-card"><h4 class="complaints-modal-card-title">Order Information</h4><div id="rrRefundOrderInfo" class="complaints-modal-order-list"></div></section><section class="complaints-modal-card complaints-modal-item-card"><h4 class="complaints-modal-card-title">Ordered Items for Return / Refund</h4><div id="rrRefundItems"></div></section></div>
    <div class="complaints-modal-grid complaints-modal-bottom-grid"><section class="complaints-modal-card complaints-modal-evidence-card"><h4 class="complaints-modal-card-title">Supporting Evidence</h4><div id="rrRefundEvidence" class="complaints-modal-evidence-list"></div></section><section class="complaints-modal-card"><h4 class="complaints-modal-card-title">Refund Confirmation</h4><p id="rrRefundConfirmation" class="complaints-modal-description">—</p></section></div>
    <div class="complaints-modal-footer-grid rr-refund-footer-only"><div class="sp-modal-bottom-actions"><button id="rrRefundDone" type="button" class="sp-modal-done">Close</button></div></div>
  </div>
</div>
<script id="rrData" type="application/json">{!! json_encode($returnRefunds ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<script id="rrRefundData" type="application/json">{!! json_encode($approvedRefunds ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<script id="rrShippingData" type="application/json">{!! json_encode($shippingOrders ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<script>
document.addEventListener('DOMContentLoaded',()=>{
const $=id=>document.getElementById(id), esc=v=>String(v??'—').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const norm=v=>String(v??'').toLowerCase().trim().replace(/_/g,'-');
const date=v=>{if(!v)return '—';const d=new Date(v);return isNaN(+d)?String(v):d.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});};
const dateTime=v=>{if(!v)return '—';const d=new Date(v);return isNaN(+d)?String(v):d.toLocaleString('en-US',{month:'short',day:'numeric',year:'numeric',hour:'numeric',minute:'2-digit'});};
const dateOnly=v=>{if(!v)return '—';const d=new Date(v);return isNaN(+d)?String(v):d.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});};
const timeOnly=v=>{if(!v)return '—';const d=new Date(v);return isNaN(+d)?'—':d.toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit'});};
const fmt=v=>v==null||v===''?'—':String(v);
const requestType=v=>/refund.only|partial.refund/i.test(String(v||''))?'Refund Only':'Return & Refund';
const demo=[
{id:'RR-2026-001',order_id:'SH-2026-1005',buyer_name:'Alex Rivera',buyer_email:'alex@example.com',buyer_phone:'0917 123 4567',delivery_address:'Santa Cruz, Laguna',return_items:[{product_name:'Minimalist Smart Watch',variation:'Bluetooth Edition',color:'Midnight Black',size:'42 mm',quantity:1,product_price:'₱2,199.00'},{product_name:'Silicone Watch Strap',variation:'Sport Band',color:'Black',size:'Medium',quantity:1,product_price:'₱199.00'}],seller_name:'Tech Haven',type:'Return & Refund',status:'pending',date:'2026-10-19T09:30:00',description:'Smart watch screen arrived damaged.',solution:'Return item for a full refund after courier pickup.',amount:'₱2,199.00',product_name:'Minimalist Smart Watch',product_price:'₱2,199',quantity:1,reason:'Damaged product',shipping_status:'delivered',tracking_number:'SPX-RR-25901',courier:'ShopEase Express'},
{id:'RR-2026-002',order_id:'SH-2026-1003',buyer_name:'Jamie Santos',buyer_email:'jamie@example.com',buyer_phone:'0918 234 5678',return_items:[{product_name:'Hydrating Skincare Essentials Set',variation:'3-piece set',color:'Original',size:'100 ml',quantity:1,product_price:'₱899.00'}],seller_name:'Glow Essentials',type:'Refund Only',status:'accepted',date:'2026-10-16T14:10:00',description:'One skincare bottle leaked in transit.',solution:'Partial refund of ₱300, no return required.',amount:'₱300.00',product_name:'Hydrating Skincare Essentials Set',product_price:'₱899',quantity:1,shipping_status:'delivered',tracking_number:'SPX-RR-25902',courier:'ShopEase Express'},
{id:'RR-2026-003',order_id:'SH-2026-1004',buyer_name:'Morgan Lee',buyer_phone:'0919 345 6789',return_items:[{product_name:'Everyday Running Sneakers',variation:'Standard',color:'White / Gray',size:'EU 40',quantity:1,product_price:'₱1,799.00'}],seller_name:'StreetFit',type:'Return & Refund',status:'declined',date:'2026-10-13T10:05:00',description:'Requested cancellation after delivery due to fit preference.',solution:'Return footwear and receive a full refund.',amount:'₱1,799.00',product_name:'Everyday Running Sneakers',product_price:'₱1,799',quantity:1,shipping_status:'delivered',tracking_number:'SPX-RR-25903',courier:'ShopEase Express'},
{id:'RR-2026-004',order_id:'SH-2026-1006',buyer_name:'Taylor Cruz',buyer_phone:'0920 456 7890',return_items:[{product_name:'Ceramic Coffee Mug Set',variation:'Set of 2',color:'Cream',size:'350 ml',quantity:1,product_price:'₱349.00'}],seller_name:'Home Finds',type:'Return & Refund',status:'pending',date:'2026-10-21T12:45:00',description:'A ceramic mug arrived with a chipped handle.',solution:'Return the chipped mug for a refund after inspection.',amount:'₱349.00',product_name:'Ceramic Coffee Mug Set',product_price:'₱698',quantity:1,shipping_status:'delivered',tracking_number:'SPX-RR-25904',courier:'Flash Express'},
{id:'RR-2026-005',order_id:'SH-2026-1007',buyer_name:'Casey Ramos',buyer_phone:'0921 567 8901',return_items:[{product_name:'Classic Crossbody Bag',variation:'Everyday',color:'Brown',size:'One Size',quantity:1,product_price:'₱799.00'}],seller_name:'Mia Boutique',type:'Refund Only',status:'accepted',date:'2026-10-18T08:15:00',description:'Wrong color received.',solution:'Refund ₱799 to buyer after review.',amount:'₱799.00',product_name:'Classic Crossbody Bag',product_price:'₱799',quantity:1,shipping_status:'delivered',tracking_number:'SPX-RR-25905',courier:'J&T Express'}];
let server=[];try{server=JSON.parse($('rrData')?.textContent||'[]')}catch(e){};
let shippingServer=[];try{shippingServer=JSON.parse($('rrShippingData')?.textContent||'[]')}catch(e){};
const records=(Array.isArray(server)&&server.length?server:demo).map(x=>({...x,id:String(x.id??x.request_id??''),status:norm(x.status),date:x.date||x.filed_at||x.created_at,solution:x.solution||x.requested_solution||x.buyer_solution||'Not specified',buyer_name:x.buyer_name||x.buyer?.name||'Buyer',product_name:x.product_name||x.product?.name||x.item_name||'Product',order_id:x.order_id||x.order_number||'—'}));
const statusMap={pending:'Pending',accepted:'Accepted',declined:'Declined'};let state={status:'all',search:'',date:'',page:1,size:7,selected:null};
const rows=()=>records.filter(c=>(state.status==='all'||norm(c.status)===state.status)&&(!state.date||String(c.date).slice(0,10)===state.date)&&(!state.search||[c.id,c.order_id,c.buyer_name,c.product_name,c.type].join(' ').toLowerCase().includes(state.search)));
function pill(c){let col=c.status==='accepted'?'resolved':c.status==='declined'?'rr-declined':'rr-pending';return `<span class="complaint-status ${col}">${esc(statusMap[c.status]||c.status)}</span>`}
function renderTable(){let all=rows(),pages=Math.max(1,Math.ceil(all.length/state.size));state.page=Math.min(state.page,pages);const slice=all.slice((state.page-1)*state.size,state.page*state.size);$('complaints-table-body').innerHTML=slice.map(c=>`<tr class="complaint-row ${state.selected?.id===c.id?'active':''}" data-id="${esc(c.id)}" role="button" tabindex="0"><td><span class="complaint-id">${esc(c.order_id)}</span></td><td><div class="rr-buyer-product"><strong>${esc(c.buyer_name)}</strong><small>${esc(c.product_name||'Product')}</small></div></td><td><span class="complaint-type">${esc(c.type||'Return / Refund')}</span></td><td class="rr-refund-money">${esc(c.amount && String(c.amount).includes('₱') ? c.amount : refundMoney(c.amount))}</td><td>${pill(c)}</td><td class="rr-filed-cell"><span class="rr-filed-date">${esc(dateOnly(c.date))}</span><span class="rr-filed-time">${esc(timeOnly(c.date))}</span></td></tr>`).join('');$('complaints-empty').style.display=all.length?'none':'block';$('complaints-count').textContent=`Showing ${slice.length} out of ${all.length} entries`;$('complaints-prev').disabled=state.page===1;$('complaints-next').disabled=state.page===pages;$('complaints-page-numbers').innerHTML=Array.from({length:pages},(_,i)=>`<button type="button" class="complaints-page-button ${i+1===state.page?'active':''}" data-page="${i+1}">${i+1}</button>`).join('');if(state.selected)preview(state.selected)}
function preview(c){$('complaints-workspace').classList.add('has-selection');$('complaints-detail-card').setAttribute('aria-hidden','false');const values={'detail-id':c.order_id,'detail-date':dateTime(c.date),'detail-type':c.type,'detail-description':c.description,'detail-status':statusMap[c.status]||c.status,'rr-preview-solution':c.solution};Object.entries(values).forEach(([id,value])=>{const el=$(id);if(el)el.textContent=fmt(value)});$('detail-parties').innerHTML=buyerInfoHtml(c);$('detail-evidence').textContent=c.evidence?.length?'Evidence provided (see details)':'No evidence attached.';$('detail-status').className=`complaint-status ${c.status==='accepted'?'resolved':c.status==='declined'?'rr-declined':'rr-pending'}`}
function pick(id){const c=records.find(r=>r.id===id);if(!c)return;state.selected=c;renderTable()}
function closePreview(){state.selected=null;$('complaints-workspace').classList.remove('has-selection');$('complaints-detail-card').setAttribute('aria-hidden','true');renderTable()}
function evidenceHtml(c){const arr=Array.isArray(c.evidence)?c.evidence:[];return arr.length?arr.map(x=>{const src=typeof x==='string'?x:x.url;return /^https?:\/\/|^\/storage\//i.test(src||'')?`<a href="${esc(src)}" target="_blank" rel="noopener">View attached evidence</a>`:''}).join(''):'No supporting evidence uploaded.'}
function buyerInfoHtml(c){
 const b=c.buyer&&typeof c.buyer==='object'?c.buyer:{};
 const rows=[['Name',c.buyer_name||b.name],['Email',c.buyer_email||b.email],['Contact',c.buyer_phone||c.contact_no||b.contact_no||b.phone],['Delivery Address',c.delivery_address||c.buyer_address||c.address||b.address]];
 return `<div class="rr-buyer-info-list">${rows.filter(([k,v])=>v!=null&&String(v).trim()).map(([k,v])=>`<div><span>${esc(k)}</span><strong>${esc(v)}</strong></div>`).join('')}</div>`;
}
function orderedItemsHtml(c){
 const source=Array.isArray(c.return_items)&&c.return_items.length?c.return_items:Array.isArray(c.items)&&c.items.length?c.items:Array.isArray(c.ordered_items)&&c.ordered_items.length?c.ordered_items:[c];
 const val=(x,...keys)=>keys.map(k=>x[k]).find(v=>v!==null&&v!==undefined&&String(v)!=='');
 return `<div class="rr-ordered-items">${source.map((item,index)=>{
  const name=val(item,'product_name','name','item_name')||item.product?.name||c.product_name||'Product';
  const qty=val(item,'return_quantity','quantity','qty')||1;
  const variation=val(item,'variation','variant','variation_name')||item.variation?.name||'—';
  const color=val(item,'color','colour')||item.attributes?.color||'—';
  const size=val(item,'size')||item.attributes?.size||'—';
  const price=val(item,'product_price','unit_price','price')||c.product_price||'—';
  return `<article class="rr-ordered-product"><div class="rr-ordered-product-head"><span>Item ${index+1}</span><strong>${esc(name)}</strong></div><div class="rr-ordered-product-meta"><div><span>Variation</span><strong>${esc(variation)}</strong></div><div><span>Color</span><strong>${esc(color)}</strong></div><div><span>Size</span><strong>${esc(size)}</strong></div><div><span>Quantity to Return</span><strong>${esc(qty)}</strong></div><div><span>Unit Price</span><strong>${esc(price)}</strong></div></div></article>`;
 }).join('')}</div>`;
}
function openDetails(){const c=state.selected;if(!c)return;const set=(id,v)=>{if($(id))$(id).textContent=fmt(v)};[['modal-complaint-id',c.order_id],['modal-date-filed',dateTime(c.date)],['modal-last-updated',dateTime(c.updated_at||c.date)],['modal-complaint-type',requestType(c.type)],['modal-order-id-top',c.order_id],['modal-description-copy',c.description],['rr-modal-solution',c.solution],['rr-modal-amount',c.amount],['modal-complaint-status',statusMap[c.status]||c.status]].forEach(([k,v])=>set(k,v));$('modal-complaint-status').className=`complaints-modal-status ${c.status==='accepted'?'resolved':c.status==='declined'?'rr-declined':'rr-pending'}`;$('modal-parties-list').innerHTML=buyerInfoHtml(c);$('modal-order-information').innerHTML=`<div class="complaints-modal-order-row"><span>Order ID</span><strong>${esc(c.order_id)}</strong></div><div class="complaints-modal-order-row"><span>Payment Method</span><strong>Cash on Delivery (COD)</strong></div><div class="complaints-modal-order-row"><span>Shipping Status</span><strong>Delivered</strong></div>`;$('modal-product-info').innerHTML=orderedItemsHtml(c);$('modal-evidence-list').innerHTML=evidenceHtml(c);$('complaints-modal-notes-list').innerHTML=`<p>${esc(c.admin_note||'Awaiting an official administrative update.')}</p>`;$('complaints-details-modal').hidden=false;document.body.classList.add('sp-modal-open')}
function closeDetails(){$('complaints-details-modal').hidden=true;document.body.classList.remove('sp-modal-open')}
$('complaints-table-body').addEventListener('click',e=>{const row=e.target.closest('[data-id]');if(row)pick(row.dataset.id)});$('complaints-table-body').addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){const row=e.target.closest('[data-id]');if(row){e.preventDefault();pick(row.dataset.id)}}});$('complaints-detail-close')?.addEventListener('click',closePreview);$('complaints-view-details')?.addEventListener('click',openDetails);['complaints-modal-close','complaints-modal-done'].forEach(id=>$(id)?.addEventListener('click',closeDetails));$('complaints-details-modal')?.addEventListener('click',e=>{if(e.target.id==='complaints-details-modal')closeDetails()});$('sp-contact-admin')?.addEventListener('click',()=>{const id=state.selected?.id||'';window.location.assign('/seller/messages?tab=complaints&complaint='+encodeURIComponent(id))});
document.querySelectorAll('.complaints-tab').forEach(b=>b.addEventListener('click',()=>{state.status=b.dataset.tab;state.page=1;document.querySelectorAll('.complaints-tab').forEach(x=>{const on=x===b;x.classList.toggle('active',on);x.setAttribute('aria-selected',String(on))});closePreview()}));$('complaints-search').addEventListener('input',e=>{state.search=e.target.value.toLowerCase().trim();state.page=1;renderTable()});$('complaints-date-filter').addEventListener('change',e=>{state.date=e.target.value;state.page=1;renderTable()});$('complaints-reload').addEventListener('click',()=>{state.search='';state.date='';state.status='all';state.page=1;$('complaints-search').value='';$('complaints-date-filter').value='';document.querySelector('.complaints-tab[data-tab="all"]')?.click();renderTable()});$('complaints-prev').addEventListener('click',()=>{state.page=Math.max(1,state.page-1);renderTable()});$('complaints-next').addEventListener('click',()=>{state.page++;renderTable()});$('complaints-page-numbers').addEventListener('click',e=>{const b=e.target.closest('[data-page]');if(b){state.page=+b.dataset.page;renderTable()}});$('complaints-items').addEventListener('change',e=>{state.size=+e.target.value;state.page=1;renderTable()});
const shippingDemo=[
{order_id:'SH-2026-1008',buyer_name:'Riley Mendoza',buyer_phone:'0918 221 4490',address:'Calamba, Laguna',product_name:'Portable Blender',price:1290,shipping_status:'processing',date:'2026-10-22T09:15:00',updated_at:'2026-10-23T08:30:00',estimated_delivery:'2026-10-29',scheduled_return_date:'2026-10-25T10:00:00',tracking_number:'PICKUP-SCHEDULED',courier:'SPX Express',location:'Awaiting scheduled buyer pickup',events:[['Return Request Accepted','Oct 23, 2026 · 8:30 AM','Seller accepted buyer request'],['Return Pickup Scheduled','Oct 25, 2026 · 10:00 AM','Courier scheduled to collect item']]},
{order_id:'SH-2026-1005',buyer_name:'Alex Rivera',buyer_phone:'0917 815 2234',address:'Pila, Laguna',product_name:'Minimalist Smart Watch',price:2199,shipping_status:'in-transit',date:'2026-10-20T09:30:00',updated_at:'2026-10-23T10:25:00',estimated_delivery:'2026-10-25',tracking_number:'RR-SPX-100501',courier:'SPX Express',location:'Santa Rosa Sorting Center',events:[['Return Pickup Scheduled','Oct 21, 2026 · 9:30 AM','Buyer pickup confirmed'],['Parcel Picked Up','Oct 22, 2026 · 3:10 PM','Pila Hub'],['In Transit','Oct 23, 2026 · 10:25 AM','Santa Rosa Sorting Center']]},
{order_id:'SH-2026-1003',buyer_name:'Jamie Santos',buyer_phone:'0926 330 4102',address:'Santa Cruz, Laguna',product_name:'Hydrating Skincare Essentials Set',price:899,shipping_status:'delivered',date:'2026-10-16T14:10:00',updated_at:'2026-10-21T13:20:00',estimated_delivery:'2026-10-21',tracking_number:'RR-SPX-100302',courier:'J&T Express',location:'Seller Return Warehouse',events:[['Parcel Picked Up','Oct 18, 2026 · 2:20 PM','Santa Cruz Hub'],['In Transit','Oct 19, 2026 · 8:30 AM','Calamba Hub'],['Out for Delivery','Oct 21, 2026 · 8:15 AM','Seller return facility'],['Delivered','Oct 21, 2026 · 1:20 PM','Seller Return Warehouse']]},
{order_id:'SH-2026-1006',buyer_name:'Taylor Cruz',buyer_phone:'0998 351 7718',address:'Los Baños, Laguna',product_name:'Ceramic Coffee Mug Set',price:698,shipping_status:'out-for-delivery',date:'2026-10-21T12:45:00',updated_at:'2026-10-23T08:45:00',estimated_delivery:'2026-10-23',tracking_number:'RR-FLX-100603',courier:'Flash Express',location:'Seller delivery zone',events:[['Parcel Picked Up','Oct 21, 2026 · 4:00 PM','Los Baños Hub'],['In Transit','Oct 22, 2026 · 11:15 AM','Calamba Hub'],['Out for Delivery','Oct 23, 2026 · 8:45 AM','Assigned to return courier']]},
{order_id:'SH-2026-1007',buyer_name:'Casey Ramos',buyer_phone:'0915 808 7601',address:'San Pablo, Laguna',product_name:'Classic Crossbody Bag',price:799,shipping_status:'delivered',date:'2026-10-18T08:15:00',updated_at:'2026-10-22T15:35:00',estimated_delivery:'2026-10-22',tracking_number:'RR-JNT-100704',courier:'J&T Express',location:'Seller Return Warehouse',events:[['Parcel Picked Up','Oct 19, 2026 · 1:05 PM','San Pablo Hub'],['In Transit','Oct 20, 2026 · 9:50 AM','Sorting Center'],['Delivered','Oct 22, 2026 · 3:35 PM','Seller Return Warehouse']]}
];
// Demo addresses/items are UI samples only. Real shipments use their supplied values.
shippingDemo.forEach((x,index)=>{
 x.buyer_email=['riley@example.com','alex@example.com','jamie@example.com','taylor@example.com','casey@example.com'][index];
 const variation=[['Portable','White','500 ml'],['Bluetooth Edition','Midnight Black','42 mm'],['3-piece set','Original','100 ml'],['Set of 2','Cream','350 ml'],['Everyday','Brown','One Size']][index];
 x.return_items=[{product_name:x.product_name,variation:variation[0],color:variation[1],size:variation[2],quantity:1,unit_price:x.price}];
});
shippingDemo[1].return_items.push({product_name:'Silicone Watch Strap',variation:'Sport Band',color:'Black',size:'Medium',quantity:2,unit_price:199});
const shipment=Array.isArray(shippingServer)&&shippingServer.length?shippingServer.map(x=>({...x,order_id:x.order_id||x.order_number||'',shipping_status:norm(x.shipping_status||x.status)})):shippingDemo;
let shipPage=1,shipFilter='all';
const shippingStatus=x=>({processing:'Processing','in-transit':'In Transit','out-for-delivery':'Out for Delivery',delivered:'Delivered'})[norm(x)]||x;
const shippingIcon=(name)=>'/icons/seller/shipping-status/'+name;
function shipRows(){const q=$('rrShippingSearch').value.trim().toLowerCase();return shipment.filter(x=>(shipFilter==='all'||x.shipping_status===shipFilter)&&(!q||[x.order_id,x.buyer_name,x.tracking_number].join(' ').toLowerCase().includes(q)))}
function progressHtml(c){
 const stages=['processing','in-transit','out-for-delivery','delivered'];
  const pos=Math.max(0,stages.indexOf(norm(c.shipping_status)));
 return `<div class="rr-shipping-progress">${stages.map((stage,i)=>{
   const icon=i===0?(pos>0?'in-transit-check.png':'processing.png'):i===1?(pos>1?'in-transit-check.png':pos===1?'in-transit-blue.png':'in-transit-gray.png'):i===2?(pos>2?'delivered-check.png':pos===2?'out-for-delivery.png':'out-for-delivery-gray.png'):(pos===3?'delivered-check.png':'delivered-gray.png');
   const stateClass=i<=pos?'is-current':'';
   return `<div class="rr-progress-item ${stateClass}"><img src="${shippingIcon(icon)}" alt=""><small>${esc(shippingStatus(stage))}</small></div>${i<3?`<span class="rr-progress-line ${i<pos?'done':''}"></span>`:''}`;
 }).join('')}</div>`;
}
function renderShip(){const all=shipRows(),max=Math.max(1,Math.ceil(all.length/6));shipPage=Math.max(1,Math.min(max,shipPage));const visible=all.slice((shipPage-1)*6,shipPage*6);$('rrShippingRows').innerHTML=visible.map(x=>`<tr class="rr-ship-row" data-order="${esc(x.order_id)}" role="button" tabindex="0"><td><div class="rr-order-cell"><div class="rr-product-thumb"><img src="${shippingIcon(x.shipping_status==='delivered'?'delivered.png':'in-transit.png')}" alt=""></div><div><strong>#${esc(x.order_id)}</strong><p>${esc(x.buyer_name)}</p><small>${esc(x.buyer_phone||'—')}</small><p class="rr-date">${esc(dateTime(x.date))}</p></div></div></td><td><span class="rr-chip ${esc(norm(x.shipping_status))}">${esc(shippingStatus(x.shipping_status))}</span><small class="rr-delivery-label">${x.shipping_status==='processing'?'Scheduled Return Pickup':'Estimated Delivery'}</small><strong class="rr-delivery-date">${esc(x.shipping_status==='processing'?dateTime(x.scheduled_return_date):date(x.estimated_delivery))}</strong></td><td>${progressHtml(x)}<small class="rr-tracking">Tracking Number: ${esc(x.tracking_number)}</small></td><td><strong class="rr-courier">${esc(x.courier)}</strong><p class="rr-shipping-price">₱${Number(x.price||0).toLocaleString('en-PH',{minimumFractionDigits:2})}</p><p>Transaction: <b>Refund</b></p><small>Return / Refund</small></td></tr>`).join('')||'<tr><td colspan="4" class="rr-empty">No return shipping records match the filters.</td></tr>';$('rrShippingCount').textContent=`Showing ${visible.length} of ${all.length} orders`;$('rrShipPage').textContent=`${shipPage} / ${max}`;$('rrShipPrev').disabled=shipPage<=1;$('rrShipNext').disabled=shipPage>=max}
function shippingDetails(order){const c=shipment.find(x=>x.order_id===order);if(!c)return;const status=c.shipping_status;const summary=[['Order Number','#'+c.order_id],['Ordered At',dateTime(c.date)],[status==='processing'?'Scheduled Return Pickup':'Estimated Delivery',status==='processing'?dateTime(c.scheduled_return_date):date(c.estimated_delivery)],['Tracking Number',c.tracking_number],['Current Location',c.location||'Not scanned yet'],['Shipping Status',shippingStatus(status)]];$('rrShipSummary').innerHTML=summary.map(([a,b])=>`<div><small>${esc(a)}</small>${a==='Shipping Status'?`<span class="rr-chip ${esc(norm(status))}">${esc(b)}</span>`:`<strong>${esc(b)}</strong>`}</div>`).join('');const field=(name,value)=>`<div class="rr-ship-info-row"><span>${esc(name)}</span><strong>${esc(value||'—')}</strong></div>`;
$('rrShipCustomer').innerHTML=[field('Name',c.buyer_name||c.buyer?.name),field('Email',c.buyer_email||c.buyer?.email),field('Contact',c.buyer_phone||c.buyer?.phone),field('Pickup Address',c.address||c.buyer_address||c.pickup_address)].join('');
// Never use shipment/demo seller names or addresses; this page belongs to the logged-in seller.
const sellerInfo=JSON.parse(document.getElementById('rrAuthenticatedSeller')?.textContent||'{}');
$('rrShipSeller').innerHTML=[field('Seller / Shop',sellerInfo.name||'Your Shop'),field('Contact',sellerInfo.phone||'Not provided'),field('Return Address',sellerInfo.address||'No seller return address on file')].join('');
const matchingRequest=records.find(r=>String(r.order_id)===String(c.order_id));
const items=Array.isArray(c.return_items)&&c.return_items.length?c.return_items:Array.isArray(c.items)&&c.items.length?c.items:Array.isArray(matchingRequest?.return_items)&&matchingRequest.return_items.length?matchingRequest.return_items:[c];
const itemValue=(item,...keys)=>keys.map(k=>item?.[k]).find(v=>v!==null&&v!==undefined&&String(v)!=='');
const money=value=>{const numeric=Number(String(value??0).replace(/[^0-9.-]/g,''));return Number.isFinite(numeric)?`₱${numeric.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})}`:'—'};
$('rrShipItem').innerHTML=items.map((item,index)=>{const name=itemValue(item,'product_name','name','item_name')||item.product?.name||c.product_name||'Returned product';const variation=itemValue(item,'variation','variant','variation_name')||item.variation?.name||'—';const color=itemValue(item,'color','colour')||item.attributes?.color||'—';const size=itemValue(item,'size')||item.attributes?.size||'—';const qty=itemValue(item,'return_quantity','quantity','qty')||1;const price=itemValue(item,'unit_price','product_price','price')??c.price;return `<article class="rr-ship-item-card"><header><small>ITEM ${index+1}</small><strong>${esc(name)}</strong></header><div class="rr-ship-item-fields">${field('Variation',variation)}${field('Color',color)}${field('Size',size)}${field('Quantity',qty)}${field('Unit Price',money(price))}</div></article>`}).join('');$('rrShipTrack').innerHTML=progressHtml(c);const events=Array.isArray(c.events)?c.events:(Array.isArray(c.status_history)?c.status_history.map(e=>[shippingStatus(norm(e.to_status||e.status)),dateTime(e.created_at),e.notes||'Logistics update']):[]);$('rrShipHistory').innerHTML=events.slice().reverse().map(e=>{const a=Array.isArray(e)?e:[e.status||e.title,e.time||e.created_at,e.location||e.notes];return `<div class="rr-history-item"><strong>${esc(a[0])}</strong><p>${esc(a[1]||'—')} · ${esc(a[2]||'—')}</p></div>`}).join('')||'<p>No tracking history recorded.</p>';$('rrShippingModal').hidden=false;document.body.style.overflow='hidden'}
$('rrShippingRows').addEventListener('click',e=>{const row=e.target.closest('[data-order]');if(row)shippingDetails(row.dataset.order)});$('rrShippingRows').addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){const row=e.target.closest('[data-order]');if(row){e.preventDefault();shippingDetails(row.dataset.order)}}});$('rrShippingSearch').addEventListener('input',()=>{shipPage=1;renderShip()});$('rrShipPrev').addEventListener('click',()=>{shipPage=Math.max(1,shipPage-1);renderShip()});$('rrShipNext').addEventListener('click',()=>{shipPage++;renderShip()});document.querySelectorAll('[data-rr-ship-tab]').forEach(b=>b.addEventListener('click',()=>{shipFilter=b.dataset.rrShipTab;shipPage=1;document.querySelectorAll('[data-rr-ship-tab]').forEach(x=>x.classList.toggle('active',x===b));renderShip()}));function closeShip(){$('rrShippingModal').hidden=true;document.body.style.overflow=''}$('rrShipClose').addEventListener('click',closeShip);$('rrShippingModal').addEventListener('click',e=>{if(e.target===$('rrShippingModal'))closeShip()});document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeShip();closeDetails()}});

const refundDemo=[
 {order_id:'SH-2026-1007',buyer_name:'Casey Ramos',product_name:'Classic Crossbody Bag',return_items:[{product_name:'Classic Crossbody Bag',variation:'Adjustable Strap',color:'Coffee Brown',size:'Medium',return_quantity:1,unit_price:799}],refund_type:'Return & Refund',amount:799,status:'refunded',approved_at:'2026-10-21T09:30:00',refunded_at:'2026-10-23T13:15:00',solution:'Full refund after returned parcel reached the seller.',note:'Return received and refund marked completed in this demonstration.'},
 {order_id:'SH-2026-1011',buyer_name:'Robin Reyes',product_name:'Running Sneakers',return_items:[{product_name:'Running Sneakers',variation:'Everyday Running',color:'White / Gray',size:'EU 39',return_quantity:1,unit_price:1599},{product_name:'Performance Socks',variation:'Cushioned',color:'White',size:'Medium',return_quantity:2,unit_price:100}],refund_type:'Return & Refund',amount:1799,status:'refunded',approved_at:'2026-10-18T14:20:00',refunded_at:'2026-10-22T16:40:00',solution:'Returned footwear inspected and full refund completed.',note:'Sample return was received at seller warehouse.'},
 {order_id:'SH-2026-1012',buyer_name:'Mika Flores',product_name:'Ceramic Mug Set',return_items:[{product_name:'Ceramic Mug Set',variation:'Set of 2',color:'Ivory',size:'350 mL',return_quantity:1,unit_price:250}],refund_type:'Refund Only',amount:250,status:'refunded',approved_at:'2026-10-20T10:30:00',refunded_at:'2026-10-22T12:45:00',solution:'Refund without return for broken item.',note:'Refund payout completed in this demo.'}
];
let refundServer=[];try{refundServer=JSON.parse(document.getElementById('rrRefundData')?.textContent||'[]')}catch(e){}
const refunds=(Array.isArray(refundServer)&&refundServer.length?refundServer:refundDemo).filter(x=>['refunded','completed','returned-and-refunded','returned-refunded'].includes(norm(x.status||x.refund_status)) && Boolean(x.refunded_at||x.refundedAt)).map(x=>({...x,order_id:String(x.order_id||x.order_number||''),status:'refunded'}));
let refundFilter='all',refundSearch='',refundDate='',refundPage=1;
const isRefundOnly=x=>/^(refund[-_ ]?only(?:[-_ ]?request)?|partial[-_ ]?refund)$/i.test(String(x.refund_type||x.type||'').trim());
const refundMoney=v=>'₱'+Number(v||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});
function filteredRefunds(){return refunds.filter(x=>(refundFilter==='all'||(refundFilter==='refund-only'?norm(x.refund_type)==='refund-only':norm(x.refund_type)!=='refund-only'))&&(!refundSearch||[x.order_id,x.buyer_name,x.product_name,x.refund_type].join(' ').toLowerCase().includes(refundSearch))&&(!refundDate||String(x.refunded_at||x.refundedAt||'').slice(0,10)===refundDate))}
function renderRefunds(){const filtered=filteredRefunds(),totalPages=Math.max(1,Math.ceil(filtered.length/7));refundPage=Math.max(1,Math.min(totalPages,refundPage));const shown=filtered.slice((refundPage-1)*7,refundPage*7);
 $('rrRefundRows').innerHTML=shown.map((x,i)=>`<tr data-refund-index="${refunds.indexOf(x)}" tabindex="0" role="button" aria-label="View refund for ${esc(x.order_id)}"><td><span class="complaint-id rr-order-id">${esc(x.order_id)}</span></td><td><strong>${esc(x.buyer_name)}</strong><small>${esc(x.product_name)}</small></td><td>${esc(x.refund_type||'Refund')}</td><td class="rr-refund-money">${refundMoney(x.amount)}</td><td><span class="rr-refund-status ${isRefundOnly(x)?'refunded':'returned-refunded'}">${isRefundOnly(x)?'Refunded':'Returned &amp; Refunded'}</span></td><td class="rr-refund-date-cell"><span class="rr-refund-filed-date">${esc(dateOnly(x.refunded_at||x.refundedAt))}</span><span class="rr-refund-filed-time">${esc(timeOnly(x.refunded_at||x.refundedAt))}</span></td></tr>`).join('')||'<tr><td colspan="6" class="rr-empty">No completed refunds match the filters.</td></tr>';
 $('rrRefundCount').textContent=`Showing ${shown.length} of ${filtered.length} refund records`;$('rrRefundPage').textContent=`${refundPage} / ${totalPages}`;$('rrRefundPrev').disabled=refundPage===1;$('rrRefundNext').disabled=refundPage===totalPages;
}
function refundItemsHtml(record){
 const matching=records.find(r=>String(r.order_id)===String(record.order_id));
 const items=Array.isArray(record.return_items)&&record.return_items.length?record.return_items
   :Array.isArray(record.items)&&record.items.length?record.items
   :Array.isArray(record.ordered_items)&&record.ordered_items.length?record.ordered_items
   :Array.isArray(matching?.return_items)&&matching.return_items.length?matching.return_items
   :[record];
 const value=(item,keys)=>{for(const key of keys){const found=item?.[key];if(found!==null&&found!==undefined&&String(found).trim()!=='')return found;}return null;};
 const amount=raw=>{if(raw===null||raw===undefined||String(raw).trim()==='')return '—';const numeric=Number(String(raw).replace(/[^0-9.-]/g,''));return Number.isFinite(numeric)?'₱'+numeric.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}):String(raw);};
 return `<div class="rr-ordered-items">${items.map((item,index)=>{
   const name=value(item,['product_name','name','item_name'])||item.product?.name||record.product_name||'Product';
   const variation=value(item,['variation','variant','variation_name'])||item.variation?.name||'—';
   const color=value(item,['color','colour'])||item.attributes?.color||'—';
   const size=value(item,['size'])||item.attributes?.size||'—';
   const qty=value(item,['return_quantity','quantity','qty'])??1;
   const unit=value(item,['unit_price','product_price','price'])??record.product_price??(items.length===1?record.amount:null);
   return `<article class="rr-ordered-product"><div class="rr-ordered-product-head"><span>ITEM ${index+1}</span><strong>${esc(name)}</strong></div><div class="rr-ordered-product-meta"><div><span>Variation</span><strong>${esc(variation)}</strong></div><div><span>Color</span><strong>${esc(color)}</strong></div><div><span>Size</span><strong>${esc(size)}</strong></div><div><span>Quantity to Return / Refund</span><strong>${esc(qty)}</strong></div><div><span>Unit Price</span><strong>${esc(amount(unit))}</strong></div></div></article>`;
 }).join('')}</div>`;
}
function openRefund(index){const x=refunds[index];if(!x)return;
 const isOnly=isRefundOnly(x);
 const status=isOnly?'Refunded':'Returned & Refunded';
 const put=(id,value)=>{const el=$(id);if(el)el.textContent=value??'—'};
 put('rrRefundOrderId',x.order_id);put('rrRefundOrderIdTop',x.order_id);put('rrRefundPill',status);
 put('rrRefundTypeValue',isOnly?'Refund Only':'Return & Refund');
 put('rrRefundDateValue',dateTime(x.refunded_at||x.refundedAt));put('rrRefundApprovedValue',dateTime(x.approved_at||x.approvedAt));
 put('rrRefundDescription',x.description||x.note||'Refund completed.');put('rrRefundSolution',x.solution||'Refund has been completed.');
 put('rrRefundAmount',refundMoney(x.amount));put('rrRefundConfirmation',x.note||'Refund transaction completed successfully.');
 $('rrRefundBuyer').innerHTML=buyerInfoHtml(x);
 $('rrRefundOrderInfo').innerHTML=`<div class="complaints-modal-order-row"><span>Order ID</span><strong>${esc(x.order_id)}</strong></div><div class="complaints-modal-order-row"><span>Payment Method</span><strong>Cash on Delivery (COD)</strong></div><div class="complaints-modal-order-row"><span>Original Shipping Status</span><strong>Delivered</strong></div><div class="complaints-modal-order-row"><span>Refund Status</span><strong>${esc(status)}</strong></div>`;
 $('rrRefundItems').innerHTML=refundItemsHtml(x);
 $('rrRefundEvidence').innerHTML=evidenceHtml(x);
 $('rrRefundModal').hidden=false;$('rrRefundModal').setAttribute('aria-hidden','false');document.body.classList.add('sp-modal-open');
}
function closeRefund(){$('rrRefundModal').hidden=true;$('rrRefundModal').setAttribute('aria-hidden','true');document.body.classList.remove('sp-modal-open')}
$('rrRefundRows').addEventListener('click',e=>{const tr=e.target.closest('[data-refund-index]');if(tr)openRefund(Number(tr.dataset.refundIndex))});
$('rrRefundRows').addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){const tr=e.target.closest('[data-refund-index]');if(tr){e.preventDefault();openRefund(Number(tr.dataset.refundIndex))}}});
document.querySelectorAll('[data-refund-filter]').forEach(button=>button.addEventListener('click',()=>{refundFilter=button.dataset.refundFilter;refundPage=1;document.querySelectorAll('[data-refund-filter]').forEach(b=>b.classList.toggle('active',b===button));renderRefunds()}));
$('rrRefundSearch').addEventListener('input',e=>{refundSearch=e.target.value.trim().toLowerCase();refundPage=1;renderRefunds()});
$('rrRefundDate').addEventListener('change',e=>{refundDate=e.target.value;refundPage=1;renderRefunds()});
$('rrRefundReset').addEventListener('click',()=>{refundSearch='';refundDate='';refundFilter='all';refundPage=1;$('rrRefundSearch').value='';$('rrRefundDate').value='';document.querySelectorAll('[data-refund-filter]').forEach(b=>b.classList.toggle('active',b.dataset.refundFilter==='all'));renderRefunds()});
$('rrRefundPrev').addEventListener('click',()=>{refundPage--;renderRefunds()});$('rrRefundNext').addEventListener('click',()=>{refundPage++;renderRefunds()});
['rrRefundClose','rrRefundDone'].forEach(id=>$(id).addEventListener('click',closeRefund));$('rrRefundModal').addEventListener('click',e=>{if(e.target===$('rrRefundModal'))closeRefund()});
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeRefund()});

document.querySelectorAll('[data-rr-main]').forEach(b=>b.addEventListener('click',()=>{document.querySelectorAll('[data-rr-main]').forEach(x=>{const on=x===b;x.classList.toggle('active',on);x.setAttribute('aria-selected',String(on))});$('rrRequestsPanel').hidden=b.dataset.rrMain!=='requests';$('rrShippingPanel').hidden=b.dataset.rrMain!=='shipping';$('rrRefundsPanel').hidden=b.dataset.rrMain!=='refunds';history.replaceState(null,'','#'+b.dataset.rrMain)}));if(['#shipping','#refunds'].includes(location.hash))document.querySelector(`[data-rr-main="${location.hash.slice(1)}"]`)?.click();const linked=new URLSearchParams(location.search).get('request');if(linked)pick(linked);renderTable();renderShip();renderRefunds();const sidebar=$('sellerSidebar'),page=$('sellerReturnRefundPage');function offset(){if(!page)return;page.style.marginLeft=innerWidth<=760?'0px':(sidebar?.getBoundingClientRect().width||256)+'px'};offset();window.addEventListener('resize',offset);if(sidebar&&window.ResizeObserver)new ResizeObserver(offset).observe(sidebar);
});
</script></body></html>