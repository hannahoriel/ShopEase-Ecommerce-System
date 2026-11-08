{{-- Buyer order details, backed by the authenticated buyer's database order. --}}
@php
    $provided = $order;
    $read = fn($key, $fallback = null) => data_get($provided, $key, $fallback);
    $number = $read('order_number');
    $status = str_replace('_', '-', $read('status', 'pending'));
    $status = ['pending'=>'processing','new'=>'processing','preparing'=>'processing'][$status] ?? $status;
    $statusNames = ['processing'=>'Processing','to-ship'=>'To Ship','in-transit'=>'In Transit','out-for-delivery'=>'Out for Delivery','delivered'=>'Delivered','completed'=>'Completed','cancelled'=>'Cancelled','refunded'=>'Refunded','returned'=>'Returned','return-refund'=>'Return / Refund'];
    $statusNames[$status] ??= \Illuminate\Support\Str::headline($status);
    $shop = $read('shop', 'Shop');
    $items = collect($read('items', []));
    $name = $items->first()['product_name'] ?? 'Product';
    $subtotal = $items->sum(fn($item) => $item['unit_price'] * $item['quantity']);
    $shipping = (float)$read('shipping_fee', 0);
    $shippingDiscount = 0;
    $voucher = (float)$read('voucher_discount', 0);
    $total = (float)$read('total', 0);
    $placedAt = \Carbon\Carbon::parse($read('created_at'));
    $currentStep = match($status) {'processing'=>1,'to-ship'=>2,'in-transit'=>3,'out-for-delivery'=>4,'delivered','completed'=>5,default=>0};
    $buyerName = $read('delivery_name', 'Not provided');
    $buyerPhone = $read('delivery_phone', 'Not provided');
    $address = $read('delivery_address', 'Not provided');
    $courier = $read('shipment.courier') ?: 'Not assigned yet';
    $tracking = $read('shipment.tracking_number') ?: 'Not assigned yet';
    $steps = [['Processing','processing.png'],['To Ship','to-ship.png'],['In Transit','in-transit.png'],['Out for Delivery','out-for-delivery.png'],['Delivered','delivered.png']];
    $backUrl = route('buyer.my-purchases', ['section'=>'purchases']);
    $eventRank = fn($event) => match($event['status'] ?? '') {'order_placed'=>0, 'processing'=>1, default=>2};
    $events = collect($read('tracking_events', []))->sort(fn($first, $second) =>
        ($eventRank($first) <=> $eventRank($second))
        ?: strcmp($first['sort_at'] ?? $first['created_at'] ?? '', $second['sort_at'] ?? $second['created_at'] ?? '')
    )->reverse()->values();
@endphp
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="csrf-token" content="{{ csrf_token() }}"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Order {{ $number }} · ShopEase</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@vite(['resources/css/app.css','resources/js/buyer/pages/view-order.js'])
<style>
:root{--vo-maroon:#74232d;--vo-deep:#52070b;--vo-peach:#fff1ed;--vo-bg:#fcf6f4;--vo-line:#ece1dd;--vo-muted:#8c807c;--vo-good:#27ab69;--vo-text:#2a2221}
*{box-sizing:border-box}html{scroll-behavior:smooth}body.vo-page{margin:0;background:var(--vo-bg);font-family:'Plus Jakarta Sans',sans-serif;color:var(--vo-text);font-size:13px}button{font:inherit;cursor:pointer}button:disabled{cursor:not-allowed;opacity:.48}[hidden]{display:none!important}
.vo-main{padding:112px 22px 55px}.vo-container{max-width:1250px;margin:0 auto;display:grid;gap:17px}.vo-card{background:#fff;border:1px solid #eee3e0;border-radius:14px;box-shadow:0 3px 16px rgba(91,42,33,.035);overflow:hidden}.vo-topbar{padding:17px 21px;display:flex;justify-content:space-between;gap:16px;align-items:center}.vo-back{border:0;background:none;display:flex;align-items:center;gap:7px;color:#7b6463;font-weight:700;text-decoration:none}.vo-back:hover{color:var(--vo-maroon)}.vo-back svg{width:17px;height:17px}.vo-order-head{display:flex;align-items:center;flex-wrap:wrap;justify-content:flex-end;gap:11px;font-size:12px;color:#514441}.vo-order-head strong{color:var(--vo-maroon)}.vo-head-divider{height:18px;width:1px;background:#d9c9c3}.vo-status-head{color:var(--vo-maroon);font-weight:800;text-transform:uppercase;letter-spacing:.3px}.vo-demo{margin-top:6px;color:#aa8c80;font-size:10px}
.vo-progress{padding:30px 38px 34px}.vo-progress-track{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));position:relative;gap:0}.vo-progress-track:before{content:'';height:3px;position:absolute;left:10%;right:10%;top:30px;background:#e4e0de}.vo-progress-fill{position:absolute;height:3px;top:30px;left:10%;width:var(--progress-width);background:var(--vo-good);transition:width .3s}.vo-step{position:relative;z-index:1;text-align:center;min-width:0}.vo-step-icon{height:62px;width:62px;display:grid;place-items:center;margin:0 auto 13px;border-radius:50%;background:#fff;border:3px solid #dedbd9}.vo-step-icon img{height:37px;width:37px;object-fit:contain;filter:grayscale(1);opacity:.45}.vo-step.passed .vo-step-icon{border-color:var(--vo-good)}.vo-step.passed .vo-step-icon img{filter:none;opacity:1}.vo-step.current .vo-step-icon{background:#eaf9f0;border-color:var(--vo-good);box-shadow:0 0 0 5px #ecf8f0}.vo-step-title{display:block;font-size:12px;color:#817875;font-weight:600}.vo-step.passed .vo-step-title{color:#3b322e}.vo-step.current .vo-step-title{color:#13894e;font-weight:800}.vo-step-time{display:block;margin:7px auto 0;color:#aca4a0;font-size:10px;max-width:160px}.vo-step.current .vo-step-time{color:#529a74}.vo-step-desc{margin:0;font-size:11px}
.vo-actions-card{padding:0}.vo-attention{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:18px 22px;background:#fffdfb;border-bottom:1px solid var(--vo-line)}.vo-attention h3{margin:0 0 5px;font-size:13px}.vo-attention p{margin:0;color:#8c807b;line-height:1.7;font-size:11px}.vo-primary{border:1px solid var(--vo-maroon);background:var(--vo-maroon);color:#fff;font-size:12px;font-weight:700;border-radius:8px;padding:12px 18px;min-width:160px}.vo-primary:hover{background:#5c1721}.vo-action-row{display:flex;gap:10px;justify-content:flex-end;padding:12px 22px}.vo-outline{border:1px solid #e1d6d2;background:#fff;border-radius:8px;padding:10px 17px;font-weight:600;font-size:11px;color:#6c4545}.vo-outline:hover{border-color:#bd9193;color:var(--vo-maroon);background:#fffafa}.vo-warning{font-size:11px;color:#a97922;background:#fff8e9;border:1px solid #f5dfb5;padding:12px 16px;border-radius:9px}
.vo-section-title{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:19px 22px 13px}.vo-section-title h2{margin:0;font-size:16px;color:#331f22}.vo-section-title span{font-size:11px;color:#948b87}.vo-delivery{padding:0 22px 24px;display:grid;grid-template-columns:minmax(230px,.75fr) minmax(0,1.5fr);gap:30px}.vo-address{border-right:1px solid var(--vo-line);padding-right:25px}.vo-address h3{font-size:12px;margin:0 0 12px;color:#5c383b}.vo-address strong{font-size:12px}.vo-address p{color:#807571;margin:7px 0 0;font-size:11px;line-height:1.8;overflow-wrap:anywhere}.vo-timeline{padding-left:0;display:grid;gap:0}.vo-event{display:grid;grid-template-columns:24px 116px minmax(0,1fr);gap:12px;position:relative;min-height:52px}.vo-event:not(:last-child):before{content:'';position:absolute;left:11px;top:22px;bottom:-3px;background:#e1dedb;width:2px}.vo-dot{display:grid;place-items:center;height:23px;width:23px;background:#fff;border:2px solid #dedbd8;border-radius:50%;z-index:1;font-size:10px;color:#a9a2a0}.vo-event:first-child .vo-dot{background:#21b47b;color:#fff;border-color:#21b47b}.vo-event time{font-size:11px;color:#69605d;padding-top:5px;white-space:nowrap}.vo-event-copy{padding:3px 0 15px;min-width:0}.vo-event-copy strong{font-size:12px;color:#79615a}.vo-event:first-child .vo-event-copy strong{color:#12a46e}.vo-event-copy p{font-size:11px;margin:5px 0;color:#988b87;line-height:1.6}.vo-trackhead{display:flex;justify-content:space-between;gap:12px;padding:13px 22px;margin-bottom:18px;border-top:4px solid transparent;border-image:repeating-linear-gradient(90deg,#f3a4b3 0 23px,#fff 23px 30px,#83b8dc 30px 53px,#fff 53px 60px) 4;color:#826e6a;font-size:11px}.vo-trackhead strong{color:#4a3035}
.vo-shop-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:15px 22px;border-bottom:1px solid var(--vo-line)}.vo-shop-head strong{font-size:13px}.vo-product{display:flex;align-items:center;gap:16px;padding:20px 22px}.vo-product-img{height:90px;width:90px;border-radius:12px;border:1px solid #eaded8;background:#f8f0ec;flex-shrink:0;display:grid;place-items:center;overflow:hidden}.vo-product-img img{width:100%;height:100%;object-fit:cover}.vo-product-img svg{height:50px;width:50px;color:#b89591}.vo-product-copy{flex:1;min-width:0}.vo-product-copy h3{font-size:13px;margin:0 0 7px;line-height:1.5}.vo-product-copy p{font-size:11px;color:var(--vo-muted);margin:4px 0}.vo-product-price{text-align:right;color:var(--vo-maroon);font-size:15px;font-weight:800;white-space:nowrap}.vo-breakdown{background:#fffdfc;border-top:1px solid #eee6e2}.vo-money-row{display:flex;justify-content:space-between;gap:20px;padding:12px 22px;border-bottom:1px solid #f3ebe8;font-size:12px}.vo-money-row span:first-child{color:#958682}.vo-money-row strong{color:#382625}.vo-money-row.grand{background:#fff6f4;align-items:center}.vo-money-row.grand strong{font-size:22px;color:var(--vo-maroon)}.vo-cod{display:flex;gap:9px;align-items:center;padding:13px 22px;color:#83632c;background:#fff9ed;font-size:11px;border-bottom:1px solid #f2dfc0}.vo-payment{display:flex;align-items:center;justify-content:space-between;padding:15px 22px;color:#81736e;font-size:12px}.vo-payment strong{color:#42272a}.vo-success-note{color:#1b9460!important}.vo-footer-help{padding:3px 4px;color:#a29a96;font-size:11px}
.vo-modal-overlay{position:fixed;inset:0;z-index:12000;display:grid;place-items:center;padding:20px;background:rgba(34,20,23,.58);backdrop-filter:blur(2px)}.vo-modal{width:min(450px,100%);border-radius:19px;background:#fff;padding:28px;box-shadow:0 22px 66px #21070937;text-align:center}.vo-modal-icon{width:70px;height:70px;margin:0 auto 13px;display:grid;place-items:center;border-radius:50%;background:#ffe4e3;color:var(--vo-deep)}.vo-modal-icon svg{width:32px;height:32px}.vo-modal h2{margin:0 0 10px;font-size:19px}.vo-modal p{font-size:12px;color:#8b7b77;line-height:1.8}.vo-modal-buttons{display:flex;gap:10px;margin-top:25px}.vo-modal-buttons button{flex:1}.vo-toast{position:fixed;bottom:22px;left:50%;transform:translateX(-50%);z-index:12010;background:#49212a;color:#fff;padding:12px 20px;border-radius:12px;font-size:12px;box-shadow:0 9px 25px #32101525}
@media(max-width:760px){.vo-main{padding:93px 12px 35px}.vo-topbar{align-items:flex-start;flex-direction:column}.vo-order-head{justify-content:flex-start}.vo-progress{padding:22px 10px}.vo-step-icon{width:46px;height:46px}.vo-step-icon img{width:27px;height:27px}.vo-progress-track:before,.vo-progress-fill{top:23px}.vo-step-title{font-size:10px}.vo-step-time{font-size:9px}.vo-delivery{grid-template-columns:1fr;gap:19px}.vo-address{border-right:0;border-bottom:1px solid var(--vo-line);padding-right:0;padding-bottom:14px}.vo-attention{align-items:stretch;flex-direction:column}.vo-action-row{flex-wrap:wrap}.vo-event{grid-template-columns:22px 101px minmax(0,1fr);gap:9px}.vo-event time{font-size:10px}}@media(max-width:420px){.vo-progress{overflow-x:auto}.vo-progress-track{min-width:495px}.vo-product{flex-wrap:wrap}.vo-product-price{text-align:left;margin-left:105px}}

/* Five-stage actions and rate-product modal */
.vo-receipt-buttons{display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-wrap:wrap}
.vo-receipt-buttons .vo-outline,.vo-receipt-buttons .vo-primary{min-height:42px}
.vo-cancel-modal{text-align:left}
.vo-cancel-modal .vo-modal-icon{margin:0 auto 12px}
.vo-cancel-modal h2,.vo-cancel-modal>p{text-align:center}
.vo-cancel-modal select,.vo-cancel-modal textarea{display:block;width:100%;border:1px solid #ded4d0;border-radius:9px;background:white;padding:11px 12px;font:inherit;font-size:12px;outline:none;margin:7px 0 14px}
.vo-cancel-modal select:focus,.vo-cancel-modal textarea:focus{border-color:#a57d85;box-shadow:0 0 0 3px #fcebef}
.vo-cancel-modal textarea{resize:vertical}
.vo-cancel-modal [hidden]{display:none!important}
.vo-rating-stars button:disabled{cursor:default;transform:none!important}
.vo-rating-modal textarea[readonly]{background:#fcf9f8;color:#524845}

.vo-stage-pill{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;padding:8px 14px;background:#fff0ec;color:#7a2630;font-weight:700;font-size:11px;white-space:nowrap}
.vo-receipt-card{padding:20px 22px;display:flex;align-items:center;justify-content:space-between;gap:20px;background:#fffdfb}
.vo-receipt-card h3{margin:0 0 5px;font-size:14px;color:#3b2428}.vo-receipt-card p{margin:0;color:#8c807b;font-size:11px;line-height:1.7;max-width:740px}
.vo-rate-action{border-color:#cfa3aa;color:#832939;background:#fff7f7}
.vo-rating-modal{position:relative;text-align:left;width:min(485px,100%)}.vo-rating-modal>h2,.vo-rating-modal>.vo-rating-product,.vo-rating-modal>.vo-rating-icon{text-align:center}
.vo-rating-close{position:absolute;right:16px;top:12px;border:0;background:#f7f3f2;color:#7c6967;width:30px;height:30px;border-radius:50%;font-size:23px;line-height:1}
.vo-rating-icon{display:block;width:70px;height:70px;line-height:70px;margin:0 auto 12px;background:#ffe8e6;color:#842633;border-radius:50%;font-size:38px}
.vo-rating-product{line-height:1.55}.vo-rating-product span{color:#a29692}.vo-rating-stars{display:flex;justify-content:center;gap:5px;margin:15px 0 0}
.vo-rating-stars button{padding:0 3px;border:0;background:transparent;font-size:38px;color:#d8cfcb;line-height:1.3;transition:transform .12s;color .12s}.vo-rating-stars button:hover{transform:scale(1.13)}.vo-rating-stars button.selected{color:#e5a53c}
#voRatingHint{text-align:center;margin:5px 0 19px}.vo-rating-label{display:block;font-size:12px;font-weight:700;margin-bottom:7px}.vo-rating-label span{font-weight:400;color:#9a8d88}
.vo-rating-modal textarea{display:block;width:100%;padding:12px;border:1px solid #e1d6d2;outline:0;border-radius:9px;resize:vertical;font:inherit;font-size:12px;line-height:1.6}.vo-rating-modal textarea:focus{border-color:#a57d85;box-shadow:0 0 0 3px #fcebef}
/* Product review photo uploads */
.vo-rating-photo-block{margin-top:15px}
.vo-rating-upload{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;min-height:99px;border:1.5px dashed #dcc3c8;background:#fff9f9;border-radius:11px;color:#812b3d;cursor:pointer;text-align:center;padding:10px}
.vo-rating-upload:hover{border-color:#a33e53;background:#fff1f3}
.vo-rating-upload>span{font-size:25px;line-height:1}.vo-rating-upload strong{font-size:12px}.vo-rating-upload small{font-size:10px;color:#968487}
.vo-rating-photo-previews{display:flex;flex-wrap:wrap;gap:9px;margin-top:10px}
.vo-rating-photo-item{width:82px;height:82px;border:1px solid #eddee1;border-radius:10px;position:relative;overflow:hidden;background:#fff5f5}
.vo-rating-photo-item img{height:100%;width:100%;object-fit:cover}
.vo-rating-photo-remove{position:absolute;right:3px;top:3px;width:21px;height:21px;border:0;border-radius:50%;background:rgba(47,15,22,.85);color:#fff;font-size:16px;line-height:1;cursor:pointer}
.vo-rating-photo-view{display:block;width:100%;height:100%;padding:0;border:0;background:transparent;cursor:zoom-in}
.vo-rating-error[hidden]{display:none!important}
.vo-rating-check{display:flex;align-items:center;gap:7px;font-size:11px;color:#6c6160;margin-top:14px}.vo-rating-check input{accent-color:#7a2630}.vo-rating-error{color:#b42336!important}.vo-rating-demo{text-align:center;font-size:10px!important;margin:12px 0 0!important}
@media(max-width:650px){.vo-receipt-card{align-items:stretch;flex-direction:column}.vo-receipt-card button{width:100%}.vo-stage-pill{align-self:flex-start}.vo-modal{padding:22px}}

/* Order metadata and tracking share a readable, responsive hierarchy. */
.vo-section-title h2{font-size:18px;font-weight:600}
.vo-info-layout{display:grid;grid-template-columns:minmax(0,1fr) 220px;gap:28px;padding:4px 24px 24px;align-items:start}
.vo-info-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px 28px;margin:0;padding:8px 0}
.vo-info-fields>div{min-width:0}.vo-info-fields dt{font-size:13px;color:#8c807c;margin-bottom:7px}
.vo-info-fields dd{margin:0;font-size:15px;font-weight:500;line-height:1.65;overflow-wrap:anywhere;color:#382b2d}
.vo-info-fields dd span{display:block;font-size:14px;font-weight:400;color:#81736f;margin-top:3px}
.vo-verification{border-left:1px solid var(--vo-line);padding-left:24px;text-align:center}
.vo-verification h3{font-size:15px;font-weight:600;margin:5px 0 14px}.vo-verification canvas{display:block;max-width:100%;height:auto;margin:auto}
.vo-verification p{font-size:12px;line-height:1.7;color:#8c807c;margin:12px 0 0}
.vo-carrier-identity{display:flex;flex-direction:column;gap:5px;align-items:flex-end;min-width:0;overflow-wrap:anywhere}
.vo-carrier-identity strong{font-size:15px;font-weight:600;color:#4a3035}.vo-carrier-identity span{font-size:13px;color:#8c807c}
.vo-trackhead{font-size:13px;padding-top:15px;padding-bottom:15px}
.vo-address h3{font-size:15px;font-weight:600}.vo-address strong{font-size:15px;font-weight:600}.vo-address p{font-size:14px;line-height:1.8}
.vo-event{grid-template-columns:24px minmax(120px,145px) minmax(0,1fr);gap:14px;min-height:70px}
.vo-event time{font-size:12px;line-height:1.6;white-space:normal;padding-top:3px}
.vo-event-copy strong{font-size:15px;font-weight:600}.vo-event-copy p{font-size:14px;line-height:1.7}
.vo-attention h3{font-size:16px;font-weight:600}.vo-attention p{font-size:14px;line-height:1.75}
.vo-action-row button,.vo-stage-pill{font-size:13px}.vo-step-title{font-size:13px}.vo-step-time{font-size:11px}
.vo-product-copy h3{font-size:15px}.vo-product-copy p,.vo-money-row,.vo-payment{font-size:14px}
@media(max-width:760px){
 .vo-info-layout{grid-template-columns:1fr;gap:20px;padding:0 18px 20px}
 .vo-info-fields{gap:20px}.vo-verification{border-left:0;border-top:1px solid var(--vo-line);padding:18px 0 0}
 .vo-section-title{align-items:flex-start;flex-wrap:wrap}.vo-carrier-identity{align-items:flex-start}
 .vo-event{grid-template-columns:24px minmax(0,1fr);gap:8px 12px}.vo-event time{grid-column:2;padding-top:0}.vo-event-copy{grid-column:2;padding-top:0}
 .vo-dot{grid-column:1;grid-row:1/3}.vo-step-title{font-size:11px}.vo-step-time{font-size:10px}
}
@media(max-width:480px){.vo-info-fields{grid-template-columns:1fr}}

.vo-solution-options{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:12px}.vo-solution-options label{display:flex;align-items:center;gap:8px;border:1px solid #ddd;border-radius:9px;padding:12px;font-weight:500;cursor:pointer}.vo-solution-options label:has(input:checked){border-color:#7b1b1b;background:#fff1ed;color:#7b1b1b}.vo-return-lock{font-size:12px;color:#8c807c;align-self:center}
.vo-return-products{border:0;padding:0;margin:0 0 22px;min-width:0}
.vo-return-products legend{font-size:15px;font-weight:500;margin:0 0 14px;padding:0}
.vo-return-product{display:grid;grid-template-columns:18px 72px minmax(0,1fr) auto;align-items:center;gap:14px;padding:16px;margin-bottom:10px;border:1px solid #e8ded9;border-radius:12px;background:#fff;cursor:pointer;text-align:left}
.vo-return-product:has(input:checked){border-color:#7b1b1b;background:#fff7f3;box-shadow:0 0 0 1px #7b1b1b}
.vo-return-product:has(input:focus-visible){outline:2px solid #9ca3af;outline-offset:3px}
.vo-return-product>input{margin:0;accent-color:#7b1b1b;width:17px;height:17px}
.vo-return-product-photo{width:72px;height:72px;border-radius:10px;background:#f8f0ec;border:1px solid #eaded8;display:grid;place-items:center;overflow:hidden;font-size:12px;color:#8c807c}
.vo-return-product-photo img{display:block;width:100%;height:100%;object-fit:cover}
.vo-return-product-copy{display:flex;flex-direction:column;gap:4px;min-width:0;overflow-wrap:anywhere}
.vo-return-product-copy>strong{font-size:14px;font-weight:500;color:#382b2d;line-height:1.5}
.vo-return-product-copy>span{font-size:12px;color:#8c807c;line-height:1.5}
.vo-return-product-copy .se-fragile-pill{align-self:flex-start;color:#fff;margin:0}
.vo-return-product-price{font-size:14px;font-weight:600;color:#7b1b1b;white-space:nowrap}
@media(max-width:520px){.vo-return-product{grid-template-columns:17px 56px minmax(0,1fr);gap:10px;padding:12px}.vo-return-product-photo{width:56px;height:56px}.vo-return-product-price{grid-column:3;justify-self:start}.vo-return-product-copy>strong{font-size:13px}}
.vo-shop-name{display:flex;align-items:center;gap:14px}.vo-shop-name svg{width:22px;height:22px;flex-shrink:0;color:#7b1b1b}
.vo-return-request{max-width:920px!important;width:min(920px,100%)!important;position:relative}
.vo-event[hidden]{display:none!important}.vo-history-toggle{justify-self:start;margin-top:12px}.vo-timeline:not(.is-expanded) .vo-event:nth-child(5):before{display:none}
.vo-modal-overlay.vo-return-overlay{display:flex!important;align-items:center;justify-content:center}.vo-modal-overlay.vo-return-overlay[hidden]{display:none!important}
.vo-return-overlay .vo-return-request{flex:0 1 920px!important;min-width:0;max-width:calc(100vw - 40px)!important;box-sizing:border-box}
.vo-evidence-label{margin:0 0 10px}.vo-evidence-label small{font-size:12px;color:#8c807c}.vo-evidence-row,.vo-evidence-previews{display:flex;align-items:center;gap:12px;flex-wrap:wrap}.vo-evidence-row{margin-bottom:20px}.vo-evidence-previews{display:contents}
.vo-attach-tile{position:relative;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;width:76px;height:76px;border:1px solid #dfceca;border-radius:12px;cursor:pointer;flex-shrink:0;overflow:hidden}.vo-attach-tile input{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer}.vo-attach-tile>span{display:grid;place-items:center;width:34px;height:34px;border-radius:9px;background:#fae9e6;color:#9d2525;font-size:27px;font-weight:700}.vo-attach-tile strong{font-size:12px;font-weight:500;color:#7b1b1b}.vo-attach-tile:focus-within{outline:2px solid #9ca3af;outline-offset:2px}
.vo-evidence-photo{position:relative;width:76px;height:76px;flex-shrink:0}.vo-evidence-photo img{width:100%;height:100%;object-fit:cover;border-radius:10px;border:1px solid #dfceca}.vo-evidence-photo button{position:absolute;right:-5px;top:-5px;border:1px solid #dfceca;border-radius:50%;background:#fff;color:#7b1b1b;width:23px;height:23px;cursor:pointer}

.vo-return-request{padding:30px!important;border:1px solid #eee2dd;border-radius:20px;scrollbar-width:thin;scrollbar-color:#d6c7c2 transparent}
.vo-return-request #voRequestTitle{font-size:23px;font-weight:600;color:#38272b;padding:0 42px 20px 0;margin-bottom:22px;border-bottom:1px solid #eee4e0}
.vo-return-request .vo-return-products legend,.vo-return-request [data-product-complaint-fields] label,.vo-return-request .vo-evidence-label,.vo-return-request .vo-field-heading{font-size:14px;font-weight:600;color:#463236;line-height:1.6}
.vo-return-request [data-product-complaint-fields]>label,.vo-complaint-basics>label{display:block}
.vo-complaint-basics{display:grid;grid-template-columns:minmax(110px,.45fr) minmax(0,1fr);gap:18px}
.vo-return-request [data-product-complaint-fields]{padding-top:22px!important;border-top:1px solid #eee4e0!important}
.vo-return-request [data-product-complaint-fields][hidden]{display:none!important}
.vo-return-request input:not([type=radio]):not([type=file]),.vo-return-request select,.vo-return-request textarea{box-sizing:border-box;background:#fffdfc;color:#382b2d;transition:border-color .15s,box-shadow .15s}
.vo-return-request input:focus-visible,.vo-return-request select:focus-visible,.vo-return-request textarea:focus-visible{outline:none;border-color:#a9a2a0!important;box-shadow:0 0 0 3px #e9e7e5}
.vo-return-request textarea{resize:vertical;min-height:110px}
.vo-return-request .vo-evidence-label small{font-weight:400}.vo-return-request .vo-field-heading{margin:6px 0 10px}
.vo-return-request .vo-solution-options{margin-top:0}.vo-return-request .vo-solution-options label{padding:15px;border-radius:11px;transition:background .15s,border-color .15s}.vo-return-request input[type=radio]{accent-color:#7b1b1b;flex-shrink:0}
.vo-return-request .vo-review-note{background:#faf6f3;border:1px solid #eee4de;border-radius:10px;padding:12px 15px;color:#80706c;line-height:1.7;margin:18px 0 0}
.vo-return-request .vo-modal-buttons{border-top:1px solid #eee4e0;padding-top:20px;margin-top:24px;justify-content:flex-end}.vo-return-request .vo-modal-buttons button{flex:0 1 190px;min-height:46px;border-radius:10px}
@media(max-width:600px){.vo-return-request{padding:22px!important}.vo-return-request #voRequestTitle{font-size:20px}.vo-complaint-basics{grid-template-columns:1fr;gap:0}.vo-return-request .vo-solution-options{grid-template-columns:1fr}.vo-return-request .vo-modal-buttons button{flex:1}}
.vo-return-request [data-product-complaint-fields] label.vo-attach-tile{display:flex;align-items:center;justify-content:center;flex-direction:column;padding:0;gap:4px;box-sizing:border-box;text-align:center;line-height:1;margin:0}
.vo-return-request .vo-attach-tile>span{display:grid;place-items:center;line-height:1;padding:0;margin:0}.vo-return-request .vo-attach-tile>strong{display:block;line-height:1.3;margin:0;padding:0;font-size:12px}
.vo-required{color:#8a2027;font-weight:600}
.vo-return-case .vo-section-title{padding:22px 24px;border-bottom:1px solid #eee4e0}.vo-return-case .vo-section-title h2{font-size:18px;font-weight:600}.vo-return-case .vo-case-investigation{background:#f0f1f3;color:#626773;border:1px solid #e1e3e7;font-size:12px;font-weight:500}
.vo-case-body{padding:24px;font-size:14px;line-height:1.75}.vo-case-overview{display:grid;grid-template-columns:minmax(0,1fr) 240px;gap:28px}.vo-case-details p{margin:0 0 12px;overflow-wrap:anywhere}.vo-case-details strong{font-weight:600}.vo-case-description{padding:14px 16px;background:#faf7f5;border:1px solid #eee4e0;border-radius:10px;margin:14px 0}.vo-case-description p{margin:5px 0 0;white-space:pre-wrap}.vo-case-evidence{border-left:1px solid #eee4e0;padding-left:24px}.vo-case-body h3{font-size:14px;font-weight:600;margin:0 0 14px;color:#463236}.vo-case-photos{display:flex;gap:10px;flex-wrap:wrap}.vo-case-photos img{width:84px!important;height:84px!important;border:1px solid #eee4e0}.vo-case-history{border-top:1px solid #eee4e0;margin-top:22px;padding-top:20px}.vo-case-history p{position:relative;padding:0 0 14px 20px;margin:0;border-left:2px solid #e9e4e1;color:#877975;font-size:13px}.vo-case-history p:before{content:'';position:absolute;left:-5px;top:7px;width:8px;height:8px;border-radius:50%;background:#aba3a0}.vo-case-history strong{color:#514346;font-weight:600}.vo-case-footer{border-top:1px solid #eee4e0;padding-top:18px;display:flex;justify-content:flex-end}.vo-case-footer a{text-decoration:none;display:inline-flex;align-items:center}.vo-outline:disabled{opacity:.5;cursor:not-allowed}
@media(max-width:700px){.vo-case-overview{grid-template-columns:1fr;gap:18px}.vo-case-evidence{border-left:0;border-top:1px solid #eee4e0;padding:18px 0 0}.vo-case-body{padding:20px}.vo-return-case .vo-section-title{flex-wrap:wrap}.vo-case-footer a{width:100%;justify-content:center}}
</style></head>
<body class="vo-page">
{{-- Exactly the same navbar include used by Buyer My Purchases --}}
@include('components.buyer.navbar', ['hideCategories' => true])
<main class="vo-main"><div class="vo-container">
  <section class="vo-card vo-topbar">
    <a class="vo-back" href="{{ $backUrl }}" id="voBack"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"/></svg>Back to My Purchases</a>
    <div class="vo-order-head"><span>ORDER ID: <strong>{{ $number }}</strong></span><span class="vo-head-divider"></span><span class="vo-status-head">{{ in_array($status, ['delivered','completed'], true) ? ($status === 'completed' ? 'Your order is completed' : 'Your order has been delivered') : 'Order '.$statusNames[$status] }}</span></div>
  </section>
  <section class="vo-card vo-progress" aria-label="Order progress">
    <div class="vo-progress-track" style="--progress-width:{{ max(0, ($currentStep - 1) * 20) }}%"><div class="vo-progress-fill"></div>
      @foreach($steps as $i => $step)
      @php
        $passed = ($i + 1) <= $currentStep;
        $stageKeys = [['pending','new','preparing'],['to_ship','to-ship'],['in_transit','in-transit'],['out_for_delivery','out-for-delivery'],['delivered','completed']][$i];
        $stageDate = $i === 0 ? $read('created_at') : data_get($events->filter(fn($event) => in_array($event['status'], $stageKeys, true))->sortBy('created_at')->first(), 'created_at');
      @endphp
      <div class="vo-step {{ $passed ? 'passed' : '' }} {{ $i + 1 === $currentStep ? 'current' : '' }}"><div class="vo-step-icon"><img src="{{ asset('icons/buyer/'.$step[1]) }}" alt="" loading="lazy"></div><span class="vo-step-title">{{ $step[0] }}</span><span class="vo-step-time">{{ $stageDate ? \Carbon\Carbon::parse($stageDate)->format('M d, Y · h:i A') : ($passed ? 'No timestamp available' : 'Awaiting update') }}</span></div>
      @endforeach
    </div>
  </section>

<section class="vo-card vo-order-info">
  <div class="vo-section-title"><h2>Order Information</h2></div>
  <div class="vo-info-layout">
    <dl class="vo-info-fields">
      <div><dt>Order placed</dt><dd>{{ $placedAt->format('M d, Y · h:i A') }}</dd></div>
      <div><dt>Estimated delivery</dt><dd>{{ $read('shipment.estimated_delivery') ? \Carbon\Carbon::parse($read('shipment.estimated_delivery'))->format('M d, Y') : 'Not available yet' }}</dd></div>
      <div><dt>Current location</dt><dd>{{ $read('shipment.current_location') ?: 'Not available yet' }}</dd></div>
      <div><dt>Assigned rider</dt><dd>{{ $read('shipment.rider.name') ?: 'Not assigned yet' }}@if($read('shipment.rider.contact_no'))<span>{{ $read('shipment.rider.contact_no') }}</span>@endif</dd></div>
      @if($read('buyer_received_at'))<div><dt>Receipt confirmed</dt><dd>{{ \Carbon\Carbon::parse($read('buyer_received_at'))->format('M d, Y · h:i A') }}{{ $read('buyer_received_automatically') ? ' (automatically)' : '' }}</dd></div>@endif
      @if($read('cancellation_request'))<div><dt>Cancellation request</dt><dd>{{ \Illuminate\Support\Str::headline($read('cancellation_request.status')) }}<span>{{ \Illuminate\Support\Str::headline($read('cancellation_request.reason')) }}</span></dd></div>@endif
      @if($read('complaint'))<div><dt>Complaint</dt><dd><a href="{{ $backUrl }}&complaint={{ $read('complaint.id') }}">{{ $read('complaint.reference') }}</a><span>{{ $read('complaint.status') }}</span></dd></div>@endif
      @foreach($read('returns',[]) as $return)<div><dt>{{ ucfirst($return['return_type']) }} request #{{ $return['id'] }}</dt><dd>{{ $return['status'] }}<span>{{ $return['reason'] }}</span></dd></div>@endforeach
    </dl>
    <aside class="vo-verification"><h3>Order verification</h3><canvas id="voOrderQr" width="160" height="160" aria-label="Order verification QR code"></canvas><p>Show this QR code when your order needs to be verified.</p></aside>
  </div>
</section>
  <section class="vo-card vo-actions-card">
    <div class="vo-attention">
      <div>
        <h3>{{ match($status) {'processing'=>'Your order is being processed','to-ship'=>'Your parcel is being prepared','in-transit'=>'Your parcel is on the way','out-for-delivery'=>'Your parcel is out for delivery','delivered'=>'Your order has arrived!','completed'=>'Order completed',default=>'Order update'} }}</h3>
        <p>{{ match($status) {'processing'=>'We are confirming your order details. You can still cancel before the parcel enters transit.','to-ship'=>'Your seller is preparing your parcel. You can still request cancellation at this stage.','in-transit'=>'Your parcel has been shipped. Order cancellation is no longer available.','out-for-delivery'=>'Your courier is delivering your parcel. Please prepare your COD payment.','delivered'=>'Check your parcel. You can confirm receipt, request a return/refund, or rate your purchase.','completed'=>'Your order is complete. You can rate eligible products or view your submitted reviews below.',''=>'Check your order status.',default=>'Track the progress of your order.'} }}</p>
      </div>
      <span class="vo-stage-pill">{{ $statusNames[$status] }}</span>
    </div>
    <div class="vo-action-row">
      @if($read('can_cancel'))<button class="vo-outline" type="button" data-vo-action="cancel">Cancel Order</button>@endif
      @if(in_array($status, ['delivered','completed'], true))<button class="vo-outline" type="button" data-vo-action="return" @disabled(!$read('can_request_return'))>Request Return / Refund</button>@endif
      <button class="vo-outline" type="button" data-vo-action="seller">Contact Seller</button><button class="vo-outline" type="button" data-vo-action="report">Report a Problem</button>
    </div>
  </section>
  <section class="vo-card"><div class="vo-section-title"><h2>Delivery & Tracking</h2><div class="vo-carrier-identity"><strong>{{ $courier }}</strong><span>Tracking number: {{ $tracking }}</span></div></div><div class="vo-trackhead"><span>Shipment progress and delivery updates</span></div><div class="vo-delivery"><div class="vo-address"><h3>Delivery Address</h3><strong>{{ $buyerName }}</strong><p>{{ $buyerPhone }}</p><p>{{ $address }}</p></div><div class="vo-timeline" id="voTimeline">
    @forelse($events as $event)
    <div class="vo-event" @if($loop->index >= 5) data-vo-older-event hidden @endif><span class="vo-dot">{{ $loop->first ? '✓' : '·' }}</span><time>{{ filled($event['created_at'] ?? null) ? \Carbon\Carbon::parse($event['created_at'])->format('M d, Y · h:i A') : 'Time not recorded' }}</time><div class="vo-event-copy"><strong>{{ $event['description'] ?? $event['status'] }}</strong>@if(filled($event['actor'] ?? null))<p>{{ $event['actor'] }} update</p>@endif @if(filled($event['notes'] ?? null))<p>{{ $event['notes'] }}</p>@endif @if(filled($event['location'] ?? null))<p>{{ $event['location'] }}</p>@endif</div></div>
    @empty <p>No tracking updates have been recorded yet.</p> @endforelse
    @if($events->count() > 5)<button type="button" class="vo-outline vo-history-toggle" id="voHistoryToggle" aria-expanded="false" aria-controls="voTimeline">Show all {{ $events->count() }} updates</button>@endif

  </div></div></section>
  <section class="vo-card"><div class="vo-shop-head"><strong class="vo-shop-name"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 9h16l-1.2-5H5.2L4 9Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M6 9v10h12V9M9 19v-5h6v5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>{{ $shop }}</strong><button class="vo-outline" data-vo-action="seller" type="button">Chat with Seller</button></div>@foreach($items as $item)<div class="vo-product"><div class="vo-product-img">@if($item['image_url'])<img src="{{ $item['image_url'] }}" alt="{{ $item['product_name'] }}">@else<span>Product</span>@endif</div><div class="vo-product-copy"><h3>{{ $item['product_name'] }} @if($item['is_fragile'])<span class="se-fragile-pill">Fragile</span>@endif</h3><p>{{ collect([$item['variation'],$item['color'],$item['size']])->filter()->implode(' · ') ?: 'No variant selected' }}</p><p>Quantity: {{ $item['quantity'] }}</p><p>Unit price: ₱{{ number_format($item['unit_price'],2) }}</p></div><span class="vo-product-price">₱{{ number_format($item['unit_price'] * $item['quantity'],2) }}</span></div>@endforeach<div class="vo-breakdown"><div class="vo-money-row"><span>Merchandise Subtotal</span><strong>₱{{ number_format($subtotal,2) }}</strong></div><div class="vo-money-row"><span>Shipping Fee</span><strong>₱{{ number_format($shipping,2) }}</strong></div><div class="vo-money-row"><span>Shipping Discount</span><strong>−₱{{ number_format($shippingDiscount,2) }}</strong></div><div class="vo-money-row"><span>Voucher Discount @if($read('voucher_code'))({{ $read('voucher_code') }})@endif</span><strong>−₱{{ number_format($voucher,2) }}</strong></div><div class="vo-money-row grand"><span>Order Total</span><strong>₱{{ number_format($total,2) }}</strong></div></div><div class="vo-cod">◉ <span>{{ $read('payment_method') === 'ShopEase Wallet' ? 'Paid with ShopEase Wallet.' : 'Cash on Delivery (COD) · Pay ₱'.number_format($total,2).' when you receive your parcel.' }}</span></div><div class="vo-payment"><span>Payment Method</span><strong>{{ $read('payment_method') }}</strong></div></section>
  @if(in_array($status, ['delivered','completed'], true))
  <section class="vo-card vo-receipt-card">
    <div><h3>Confirm your delivery</h3><p>Received everything in good condition? Confirm your order after checking your items. Return/refund requests remain a separate action.</p></div>
    <div class="vo-receipt-buttons">
      @if($read('can_confirm_received'))<button type="button" class="vo-primary" id="voOrderReceived" data-vo-action="received">Order Received</button>@endif
      <button type="button" class="vo-outline vo-rate-action" id="voRateAction" data-vo-action="rate">{{ $status === 'completed' ? 'Rate / View Product Reviews' : 'Ratings available after receipt' }}</button>
    </div>
  </section>
  @endif
  @include('components.buyer.order-return-progress')
  <div class="vo-footer-help">For help with this order, contact the seller or report a problem.</div>
</div></main>
@include('components.buyer.floating-chat')
<div class="vo-modal-overlay" id="voRateModal" hidden>
  <form class="vo-modal vo-rating-modal" id="voRatingForm" role="dialog" aria-modal="true" aria-labelledby="voRatingHeading">
    <button type="button" class="vo-rating-close" id="voRatingClose" aria-label="Close rating modal">×</button>
    <span class="vo-rating-icon">☆</span>
    <h2 id="voRatingHeading">Rate Your Purchase</h2>
    <label class="vo-rating-label" for="voRatingProduct">Product</label><select id="voRatingProduct" class="vo-rating-product-select">@foreach($items->unique('product_id') as $item)@if($item['product_id'])<option value="{{ $item['product_id'] }}">{{ $item['product_name'] }}</option>@endif @endforeach</select><p class="vo-rating-product">{{ $name }} <span>· {{ $shop }}</span></p>
    <div class="vo-rating-stars" id="voRatingStars" role="group" aria-label="Product rating">
      @for($star=1;$star<=5;$star++)<button type="button" data-vo-star="{{ $star }}" aria-label="Rate {{ $star }} {{ $star===1?'star':'stars' }}" aria-pressed="false">☆</button>@endfor
    </div>
    <p id="voRatingHint">Tap a star to rate your product</p>
    <label class="vo-rating-label" for="voRatingReview">Your review <span>(required)</span></label>
    <textarea id="voRatingReview" maxlength="2000" minlength="2" required rows="4" placeholder="Share your experience with this product..."></textarea>
    <div class="vo-rating-photo-block">
      <label class="vo-rating-label" for="voRatingPhotos">Product photos <span>(optional · one photo)</span></label>
      <label class="vo-rating-upload" id="voRatingUploadLabel" for="voRatingPhotos"><span aria-hidden="true">＋</span><strong>Add product photos</strong><small>JPG, PNG, WEBP · max 5 MB</small></label>
      <input id="voRatingPhotos" type="file" accept="image/jpeg,image/png,image/webp" hidden>
      <div id="voRatingPhotoPreviews" class="vo-rating-photo-previews" aria-live="polite"></div>
    </div>
    <label class="vo-rating-check"><input type="checkbox" id="voRatingAnonymous"> Post anonymously</label>
    <p class="vo-rating-error" id="voRatingError" role="alert" hidden>Please choose a star rating first.</p>
    <div class="vo-modal-buttons"><button type="button" class="vo-outline" id="voRatingCancel">Cancel</button><button type="submit" class="vo-primary" id="voRatingSubmit" disabled>Submit Rating</button></div>
  </form>
</div>
<div class="vo-modal-overlay" id="voCancelModal" hidden role="presentation">
  <form class="vo-modal vo-cancel-modal" id="voCancelForm" role="dialog" aria-modal="true" aria-labelledby="voCancelTitle">
    <button type="button" class="vo-rating-close" id="voCancelClose" aria-label="Close cancel order modal">×</button>
    <div class="vo-modal-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="m9 9 6 6m0-6-6 6"/></svg></div>
    <h2 id="voCancelTitle">Cancel Order</h2>
    <p>Please tell us why you want to cancel order <strong>{{ $number }}</strong>.</p>
    <label class="vo-rating-label" for="voCancelReason">Cancellation reason <span>*</span></label>
    <select id="voCancelReason" required>
      <option value="">Select a reason</option>
      @foreach(['changed_mind'=>'I changed my mind','ordered_by_mistake'=>'Ordered by mistake','wrong_address'=>'Incorrect delivery address','delivery_too_slow'=>'Delivery is taking too long','payment_issue'=>'Payment issue','other'=>'Other reason'] as $value=>$label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
    </select>
    <div id="voCancelOtherWrap" hidden><label class="vo-rating-label" for="voCancelOther">Please specify <span>*</span></label><textarea id="voCancelOther" rows="3" maxlength="300" placeholder="Enter your reason"></textarea></div>
    <p id="voCancelError" class="vo-rating-error" role="alert" hidden></p>
    <div class="vo-modal-buttons"><button type="button" class="vo-outline" id="voCancelKeep">Keep Order</button><button type="submit" class="vo-primary">Submit Cancellation</button></div>
  </form>
</div>
<div class="vo-modal-overlay" id="voModal" hidden role="presentation"><div class="vo-modal" role="dialog" aria-modal="true" aria-labelledby="voModalTitle"><div class="vo-modal-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5m0 4h.01"/></svg></div><h2 id="voModalTitle">Action unavailable</h2><p id="voModalText">This action requires backend support.</p><div class="vo-modal-buttons"><button class="vo-primary" id="voModalOkay" type="button">Okay, Got it</button></div></div></div>
<script type="application/json" id="voOrderData">{!! json_encode(['order'=>$order,'apiToken'=>$apiToken], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@include('components.buyer.floating-chat')
@include('components.buyer.footer')
</body></html>
