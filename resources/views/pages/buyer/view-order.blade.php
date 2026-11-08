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
    $events = collect($read('tracking_events', []));
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

<section class="vo-card" style="padding:20px 22px"><div class="vo-section-title"><h2>Order Information</h2></div><p>Placed: {{ $placedAt->format('M d, Y · h:i A') }}</p><p>Estimated delivery: {{ $read('shipment.estimated_delivery') ?: 'Not available yet' }}</p><p>Current location: {{ $read('shipment.current_location') ?: 'Not available yet' }}</p>@if($read('shipment.rider'))<p>Rider: {{ $read('shipment.rider.name') }} · {{ $read('shipment.rider.contact_no') }}</p>@endif @if($read('buyer_received_at'))<p>Receipt confirmed: {{ \Carbon\Carbon::parse($read('buyer_received_at'))->format('M d, Y · h:i A') }}{{ $read('buyer_received_automatically') ? ' (automatically)' : '' }}</p>@endif @if($read('cancellation_request'))<p>Cancellation request: {{ \Illuminate\Support\Str::headline($read('cancellation_request.status')) }} · {{ \Illuminate\Support\Str::headline($read('cancellation_request.reason')) }}</p>@endif @if($read('complaint'))<p>Complaint: <a href="{{ $backUrl }}&complaint={{ $read('complaint.id') }}">{{ $read('complaint.reference') }}</a> · {{ $read('complaint.status') }}</p>@endif @foreach($read('returns',[]) as $return)<p>{{ ucfirst($return['return_type']) }} request #{{ $return['id'] }} · {{ $return['status'] }} · {{ $return['reason'] }}</p>@endforeach<div style="margin-top:18px"><h3>Order verification</h3><canvas id="voOrderQr" width="160" height="160" aria-label="Order verification QR code"></canvas><p>Show this QR code when your order needs to be verified.</p></div></section>
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
      @if(in_array($status, ['delivered','completed'], true))<button class="vo-outline" type="button" data-vo-action="return">Request Return / Refund</button>@endif
      <button class="vo-outline" type="button" data-vo-action="seller">Contact Seller</button><button class="vo-outline" type="button" data-vo-action="report">Report a Problem</button>
    </div>
  </section>
  <section class="vo-card"><div class="vo-section-title"><h2>Delivery & Tracking</h2><span>{{ $courier }} · {{ $tracking }}</span></div><div class="vo-trackhead"><span>Shipment progress and delivery updates</span><strong>{{ $courier }}</strong></div><div class="vo-delivery"><div class="vo-address"><h3>Delivery Address</h3><strong>{{ $buyerName }}</strong><p>{{ $buyerPhone }}</p><p>{{ $address }}</p></div><div class="vo-timeline" id="voTimeline">
    @forelse($events as $event)
    <div class="vo-event"><span class="vo-dot">{{ $loop->first ? '✓' : '·' }}</span><time>{{ filled($event['created_at'] ?? null) ? \Carbon\Carbon::parse($event['created_at'])->format('M d, Y · h:i A') : '—' }}</time><div class="vo-event-copy"><strong>{{ $event['description'] ?? $event['status'] }}</strong>@if(filled($event['location'] ?? null))<p>{{ $event['location'] }}</p>@endif</div></div>
    @empty <p>No tracking updates have been recorded yet.</p> @endforelse

  </div></div></section>
  <section class="vo-card"><div class="vo-shop-head"><strong>▣ {{ $shop }}</strong><button class="vo-outline" data-vo-action="seller" type="button">Chat with Seller</button></div>@foreach($items as $item)<div class="vo-product"><div class="vo-product-img">@if($item['image_url'])<img src="{{ $item['image_url'] }}" alt="{{ $item['product_name'] }}">@else<span>Product</span>@endif</div><div class="vo-product-copy"><h3>{{ $item['product_name'] }} @if($item['is_fragile'])<span class="se-fragile-pill">Fragile</span>@endif</h3><p>{{ collect([$item['variation'],$item['color'],$item['size']])->filter()->implode(' · ') ?: 'No variant selected' }}</p><p>Quantity: {{ $item['quantity'] }}</p><p>Unit price: ₱{{ number_format($item['unit_price'],2) }}</p></div><span class="vo-product-price">₱{{ number_format($item['unit_price'] * $item['quantity'],2) }}</span></div>@endforeach<div class="vo-breakdown"><div class="vo-money-row"><span>Merchandise Subtotal</span><strong>₱{{ number_format($subtotal,2) }}</strong></div><div class="vo-money-row"><span>Shipping Fee</span><strong>₱{{ number_format($shipping,2) }}</strong></div><div class="vo-money-row"><span>Shipping Discount</span><strong>−₱{{ number_format($shippingDiscount,2) }}</strong></div><div class="vo-money-row"><span>Voucher Discount @if($read('voucher_code'))({{ $read('voucher_code') }})@endif</span><strong>−₱{{ number_format($voucher,2) }}</strong></div><div class="vo-money-row grand"><span>Order Total</span><strong>₱{{ number_format($total,2) }}</strong></div></div><div class="vo-cod">◉ <span>{{ $read('payment_method') === 'ShopEase Wallet' ? 'Paid with ShopEase Wallet.' : 'Cash on Delivery (COD) · Pay ₱'.number_format($total,2).' when you receive your parcel.' }}</span></div><div class="vo-payment"><span>Payment Method</span><strong>{{ $read('payment_method') }}</strong></div></section>
  @if(in_array($status, ['delivered','completed'], true))
  <section class="vo-card vo-receipt-card">
    <div><h3>Confirm your delivery</h3><p>Received everything in good condition? Confirm your order after checking your items. Return/refund requests remain a separate action.</p></div>
    <div class="vo-receipt-buttons">
      @if($read('can_confirm_received'))<button type="button" class="vo-primary" id="voOrderReceived" data-vo-action="received">Order Received</button>@endif
      <button type="button" class="vo-outline vo-rate-action" id="voRateAction" data-vo-action="rate">{{ $status === 'completed' ? 'Rate / View Product Reviews' : 'Ratings available after receipt' }}</button>
    </div>
  </section>
  @endif
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
</body></html>
